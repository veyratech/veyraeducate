<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppGuide;
use App\Models\WebApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppGuideController extends Controller
{
    /**
     * Quick health check — tanpa query berat.
     */
    public function healthCheck(): View
    {
        return view('admin.guides.index', [
            'guides' => null,
            'webApps' => [],
            'stats' => ['total_guides' => 0, 'total_downloads' => 0, 'total_apps_with_guide' => 0],
        ]);
    }

    /**
     * Display a listing of application guides for admin.
     */
    public function index(Request $request): View
    {
        try {
            $query = AppGuide::with(['webApp', 'user', 'school'])->latest();

            $search = trim((string) $request->input('search', ''));
            if ($search !== '') {
                $escaped = str_replace(['%', '_'], '\\\\$0', $search);
                $query->where(function ($q) use ($escaped) {
                    $q->where('title', 'like', "%{$escaped}%")
                        ->orWhere('description', 'like', "%{$escaped}%")
                        ->orWhereHas('webApp', function ($appQ) use ($escaped) {
                            $appQ->where('name', 'like', "%{$escaped}%");
                        });
                });
            }

            if ($appId = $request->input('app')) {
                $query->where('web_app_id', $appId);
            }

            if ($category = $request->input('category')) {
                $query->where('category', $category);
            }

            $guides = $query->paginate(15)->withQueryString();
            $webApps = WebApp::enabled()->orderBy('name')->get(['id', 'name', 'slug']);
            $stats = [
                'total_guides' => AppGuide::count(),
                'total_downloads' => AppGuide::sum('download_count'),
                'total_apps_with_guide' => AppGuide::distinct('web_app_id')->count('web_app_id'),
            ];

            return view('admin.guides.index', compact('guides', 'webApps', 'stats'));
        } catch (\Throwable $e) {
            abort(500, 'Terjadi kesalahan pada halaman panduan. Silakan hubungi administrator.');
        }
    }

    /**
     * Store a newly uploaded application guide by admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt'];

        // Validate non-file fields
        $title = trim($request->input('title', ''));
        $webAppId = $request->input('web_app_id');
        $version = trim($request->input('version', ''));
        $category = trim($request->input('category', 'umum'));
        $description = trim($request->input('description', ''));

        if (empty($title)) {
            return redirect()->back()->withInput()->withErrors(['title' => 'Judul buku panduan wajib diisi.']);
        }

        // Check web_app_id if provided
        if (!empty($webAppId)) {
            $exists = \DB::table('web_apps')->where('id', $webAppId)->whereNull('deleted_at')->exists();
            if (!$exists) {
                return redirect()->back()->withInput()->withErrors(['web_app_id' => 'Aplikasi terkait tidak ditemukan.']);
            }
        }

        // Validate file upload
        if (!$request->hasFile('file')) {
            return redirect()->back()->withInput()->withErrors(['file' => 'File buku panduan wajib diunggah.']);
        }

        $uploadedFile = $request->file('file');

        // Check file size FIRST before anything else
        $fileSize = $uploadedFile->getSize();
        if ($fileSize === 0) {
            return redirect()->back()->withInput()->withErrors([
                'file' => 'File kosong atau gagal diunggah. Pastikan folder temporary PHP dapat ditulis dan ukuran file maksimal 25MB.',
            ]);
        }
        if ($fileSize > 25 * 1024 * 1024) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Ukuran file maksimal adalah 25MB.']);
        }

        // Get extension safely using pathinfo (no finfo dependency)
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // If no extension found, try guessing from content if finfo available
        if (empty($extension) && function_exists('finfo_open')) {
            try {
                $resource = fopen($uploadedFile->getPathname(), 'r');
                if ($resource) {
                    $f = finfo_open(FILEINFO_EXTENSION);
                    $extension = strtolower(finfo_file($f, $resource));
                    finfo_close($f);
                    fclose($resource);
                }
            } catch (\Throwable $e) {
                // Ignore finfo errors, fall back to original name extension
            }
        }

        if (empty($extension)) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Format file harus berupa PDF, Word (doc/docx), PPT, Excel, TXT, atau ZIP.']);
        }

        // Validate extension
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return redirect()->back()->withInput()->withErrors([
                'file' => 'Format file harus berupa PDF, Word (doc/docx), PPT, Excel, TXT, atau ZIP. (' . $extension . ' tidak didukung)',
            ]);
        }

        // Check file was moved properly by PHP
        if (!$uploadedFile->isValid()) {
            $errorCode = $uploadedFile->getError();
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi batas yang diizinkan oleh server (php.ini).',
                UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas yang diizinkan pada form.',
                UPLOAD_ERR_PARTIAL    => 'File hanya sebagian yang terunggah. Coba lagi.',
                UPLOAD_ERR_NO_FILE    => 'Tidak ada file yang diunggah.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary PHP tidak tersedia. Hubungi administrator sistem.',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk. Hubungi administrator sistem.',
                UPLOAD_ERR_EXTENSION  => 'Ekstensi PHP memblokir unggahan ini. Hubungi administrator sistem.',
            ];
            return redirect()->back()->withInput()->withErrors([
                'file' => $errorMessages[$errorCode] ?? 'Gagal memproses file. Kode error: ' . $errorCode,
            ]);
        }

        try {
            $directory = 'guides';
            $disk = Storage::disk('public');

            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory, 0755, true);
            }

            // Try to store the file
            $filePath = $uploadedFile->store($directory, 'public');

            if (empty($filePath) || !$disk->exists($filePath)) {
                return redirect()->back()->withInput()->withErrors(['file' => 'Gagal menyimpan file ke server. Pastikan folder storage/app/public dapat ditulis.']);
            }

            // Create the record
            AppGuide::create([
                'user_id' => $request->user() ? $request->user()->id : null,
                'school_id' => null,
                'web_app_id' => $webAppId ?: null,
                'title' => $title,
                'description' => $description ?: null,
                'version' => $version ?: null,
                'category' => $category,
                'file_path' => $filePath,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'file_type' => $extension,
                'download_count' => 0,
            ]);

            return redirect()->route('admin.guides.index')
                ->with('status', 'Buku panduan aplikasi berhasil diunggah dan dirilis untuk sekolah!');

        } catch (TokenMismatchException $e) {
            return redirect()->back()->withInput()->withErrors(['_token' => 'Sesi berakhir. Silakan coba unggah ulang.']);
        } catch (\Throwable $e) {
            \Log::error('AppGuide store failed: ' . $e->getMessage(), [
                'exception_class' => get_class($e),
                'trace' => substr($e->getTraceAsString(), 0, 500),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'file' => 'Error: ' . substr($e->getMessage(), 0, 150) . '. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Update an existing application guide metadata (and optionally replace the file).
     */
    public function update(Request $request, AppGuide $guide): RedirectResponse
    {
        $title = trim($request->input('title', ''));
        $webAppId = $request->input('web_app_id');
        $version = trim($request->input('version', ''));
        $category = trim($request->input('category', 'umum'));
        $description = trim($request->input('description', ''));

        if (empty($title)) {
            return redirect()->back()->withErrors(['title' => 'Judul buku panduan wajib diisi.']);
        }

        if (!empty($webAppId)) {
            $exists = \DB::table('web_apps')->where('id', $webAppId)->whereNull('deleted_at')->exists();
            if (!$exists) {
                return redirect()->back()->withErrors(['web_app_id' => 'Aplikasi terkait tidak ditemukan.']);
            }
        }

        $payload = [
            'title' => $title,
            'web_app_id' => $webAppId ?: null,
            'version' => $version ?: null,
            'category' => $category,
            'description' => $description ?: null,
        ];

        if ($request->hasFile('file')) {
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt'];
            $uploadedFile = $request->file('file');
            $fileSize = $uploadedFile->getSize();

            if ($fileSize > 25 * 1024 * 1024) {
                return redirect()->back()->withErrors(['file' => 'Ukuran file maksimal adalah 25MB.']);
            }

            $extension = strtolower(pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION));
            if (empty($extension) && function_exists('finfo_open')) {
                try {
                    $resource = fopen($uploadedFile->getPathname(), 'r');
                    if ($resource) {
                        $f = finfo_open(FILEINFO_EXTENSION);
                        $extension = strtolower(finfo_file($f, $resource));
                        finfo_close($f);
                        fclose($resource);
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }

            if (!in_array($extension, $allowedExtensions, true)) {
                return redirect()->back()->withErrors(['file' => 'Format file harus berupa PDF, Word (doc/docx), PPT, Excel, TXT, atau ZIP.']);
            }

            if ($guide->file_path && Storage::disk('public')->exists($guide->file_path)) {
                Storage::disk('public')->delete($guide->file_path);
            }

            $filePath = $uploadedFile->store('guides', 'public');
            if (!empty($filePath)) {
                $payload['file_path'] = $filePath;
                $payload['file_name'] = $uploadedFile->getClientOriginalName();
                $payload['file_size'] = $fileSize;
                $payload['file_type'] = $extension;
            }
        }

        $guide->update($payload);

        return redirect()->route('admin.guides.index')
            ->with('status', 'Buku panduan "' . $guide->title . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified guide from storage.
     */
    public function destroy(AppGuide $guide): RedirectResponse
    {
        if (Storage::disk('public')->exists($guide->file_path)) {
            Storage::disk('public')->delete($guide->file_path);
        }

        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('status', 'Buku panduan berhasil dihapus.');
    }
}
