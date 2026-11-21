<?php

namespace App\Http\Controllers;

use App\Models\AppGuide;
use App\Models\WebApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppGuideController extends Controller
{
    /**
     * Display a listing of application guides.
     */
    public function index(Request $request): View
    {
        $query = AppGuide::with(['webApp', 'user', 'school'])
            ->latest();

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

        $guides = $query->paginate(12)->withQueryString();
        $webApps = WebApp::enabled()->orderBy('name')->get(['id', 'name', 'slug', 'category']);

        $stats = [
            'total_guides' => AppGuide::count(),
            'total_downloads' => AppGuide::sum('download_count'),
            'total_apps_with_guide' => AppGuide::distinct('web_app_id')->count('web_app_id'),
        ];

        return view('guides.index', compact('guides', 'webApps', 'stats'));
    }

    /**
     * Store a newly uploaded application guide.
     * Only Admin Veyra is allowed to upload application guides.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Hanya Admin Veyra yang memiliki akses untuk mengunggah buku panduan aplikasi.');
        }

        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'txt'];
        $maxSizeBytes = 25 * 1024 * 1024;

        // Validate text fields
        $title = trim($request->input('title', ''));
        $webAppId = $request->input('web_app_id');
        $version = trim($request->input('version', ''));
        $category = trim($request->input('category', 'umum'));
        $description = trim($request->input('description', ''));

        if (empty($title)) {
            return redirect()->back()->withInput()->withErrors(['title' => 'Judul buku panduan wajib diisi.']);
        }

        if (!empty($webAppId)) {
            $exists = \DB::table('web_apps')->where('id', $webAppId)->whereNull('deleted_at')->exists();
            if (!$exists) {
                return redirect()->back()->withInput()->withErrors(['web_app_id' => 'Aplikasi terkait tidak ditemukan.']);
            }
        }

        // Handle file
        if (!$request->hasFile('file')) {
            return redirect()->back()->withInput()->withErrors(['file' => 'File buku panduan wajib diunggah.']);
        }

        $uploadedFile = $request->file('file');
        $fileSize = $uploadedFile->getSize();

        if ($fileSize === 0) {
            return redirect()->back()->withInput()->withErrors(['file' => 'File kosong atau gagal diunggah. Pastikan ukuran file maksimal 25MB.']);
        }

        if ($fileSize > $maxSizeBytes) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Ukuran file maksimal adalah 25MB.']);
        }

        $originalName = $uploadedFile->getClientOriginalName();
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

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

        if (empty($extension) || !in_array($extension, $allowedExtensions, true)) {
            return redirect()->back()->withInput()->withErrors([
                'file' => 'Format file harus berupa PDF, Word (doc/docx), PPT, Excel, TXT, atau ZIP.',
            ]);
        }

        if (!$uploadedFile->isValid()) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Gagal memproses file. Coba gunakan file yang lebih kecil.']);
        }

        try {
            $filePath = $uploadedFile->store('guides', 'public');

            if (empty($filePath) || !Storage::disk('public')->exists($filePath)) {
                return redirect()->back()->withInput()->withErrors(['file' => 'Gagal menyimpan file ke server.']);
            }

            AppGuide::create([
                'user_id' => $request->user()->id,
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

            return redirect()->route('guides.index')
                ->with('status', 'Buku panduan aplikasi berhasil diunggah!');

        } catch (\Throwable $e) {
            \Log::error('Public guide store failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['file' => 'Gagal menyimpan: ' . substr($e->getMessage(), 0, 100)]);
        }
    }

    /**
     * Preview the specified application guide inline in browser (for PDF/TXT).
     */
    public function preview(AppGuide $guide): void
    {
        if (!Storage::disk('public')->exists($guide->file_path)) {
            abort(404, 'File buku panduan tidak ditemukan.');
        }

        $guide->increment('download_count');

        $fullPath = Storage::disk('public')->path($guide->file_path);

        // Detect MIME type safely without depending on finfo extension
        $mimeType = $this->guessMimeType($fullPath, $guide->file_type);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . addslashes($guide->file_name) . '"');
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($fullPath);
        exit;
    }

    /**
     * Download the specified application guide file.
     */
    public function download(AppGuide $guide): void
    {
        if (!Storage::disk('public')->exists($guide->file_path)) {
            abort(404, 'File buku panduan tidak ditemukan.');
        }

        $guide->increment('download_count');

        $fullPath = Storage::disk('public')->path($guide->file_path);

        // Detect MIME type safely without depending on finfo extension
        $mimeType = $this->guessMimeType($fullPath, $guide->file_type);

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . addslashes($guide->file_name) . '"');
        header('Cache-Control: public, max-age=31536000, immutable');
        readfile($fullPath);
        exit;
    }

    /**
     * Safely guess MIME type — falls back to stored file_type when finfo unavailable.
     */
    protected function guessMimeType(string $path, string $storedType = ''): string
    {
        $defaults = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip'  => 'application/zip',
            'txt'  => 'text/plain',
        ];

        // Use stored type as primary fallback
        if ($storedType && isset($defaults[strtolower($storedType)])) {
            return $defaults[strtolower($storedType)];
        }

        // Try finfo only if available and functional
        if (class_exists('finfo') && function_exists('finfo_open')) {
            try {
                $f = finfo_open(FILEINFO_MIME_TYPE);
                if ($f) {
                    $result = @finfo_file($f, $path);
                    finfo_close($f);
                    if ($result !== false && $result !== '') {
                        return $result;
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        return 'application/octet-stream';
    }

    /**
     * Remove the specified application guide from storage.
     * Only Admin Veyra is allowed to delete application guides.
     */
    public function destroy(Request $request, AppGuide $guide)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Hanya Admin Veyra yang memiliki akses untuk menghapus buku panduan aplikasi.');
        }

        if (Storage::disk('public')->exists($guide->file_path)) {
            Storage::disk('public')->delete($guide->file_path);
        }

        $guide->delete();

        return redirect()->route('guides.index')
            ->with('status', 'Buku panduan berhasil dihapus.');
    }
}
