<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchoolSettingController extends Controller
{
    /**
     * Display the school settings dashboard page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $school = $user->school;

        if (! $school && $user->school_id) {
            $school = School::find($user->school_id);
        }

        if (! $school) {
            $school = School::where('owner_user_id', $user->id)->first();
        }

        if (! $school) {
            $school = School::create([
                'owner_user_id' => $user->id,
                'name' => 'Sekolah '.$user->name,
                'email' => $user->email,
                'meta' => [
                    'level' => 'SMA/SMK',
                    'status' => 'Swasta',
                    'academic_year' => '2026/2027',
                    'semester' => 'Ganjil',
                ],
            ]);
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $activeSubscription = $school->activeSubscription;
        $webApps = $school->webApps()->orderBy('name')->get();
        $teamMembers = $school->users()->orderBy('role')->orderBy('name')->get();

        return view('school.settings.index', compact('user', 'school', 'activeSubscription', 'webApps', 'teamMembers'));
    }

    /**
     * Update the school identity and educational metadata.
     */
    public function updateSchool(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();
        $this->ensureCanManageSchool($user, $school);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:30', Rule::unique('schools', 'npsn')->ignore($school->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'level' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'headmaster' => ['nullable', 'string', 'max:255'],
            'headmaster_nip' => ['nullable', 'string', 'max:50'],
            'academic_year' => ['nullable', 'string', 'max:50'],
            'semester' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama resmi sekolah wajib diisi.',
            'npsn.unique' => 'NPSN ini sudah terdaftar untuk sekolah lain.',
            'email.email' => 'Format email sekolah tidak valid.',
        ]);

        $meta = $school->meta ?? [];
        $meta['address'] = $validated['address'] ?? null;
        $meta['level'] = $validated['level'] ?? ($meta['level'] ?? 'SMA/SMK');
        $meta['status'] = $validated['status'] ?? ($meta['status'] ?? 'Swasta');
        $meta['headmaster'] = $validated['headmaster'] ?? null;
        $meta['headmaster_name'] = $validated['headmaster'] ?? ($meta['headmaster_name'] ?? null);
        $meta['headmaster_nip'] = $validated['headmaster_nip'] ?? ($meta['headmaster_nip'] ?? null);
        $meta['academic_year'] = $validated['academic_year'] ?? '2026/2027';
        $meta['semester'] = $validated['semester'] ?? 'Ganjil';

        $school->update([
            'name' => $validated['name'],
            'npsn' => $validated['npsn'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'meta' => $meta,
        ]);

        return redirect()->route('school.settings', ['tab' => 'profil'])
            ->with('status', 'Identitas dan profil sekolah berhasil diperbarui!');
    }

    /**
     * Upload or update school official logo.
     */
    public function updateLogo(Request $request): RedirectResponse
    {
        if (!$request->hasFile('logo')) {
            return redirect()->back()->withErrors(['logo' => 'Silakan pilih berkas gambar logo sekolah.']);
        }
        $file = $request->file('logo');
        $sizeMB = $file->getSize() / 1024 / 1024;
        if ($sizeMB > 2) {
            return redirect()->back()->withErrors(['logo' => 'Ukuran logo maksimal 2MB.']);
        }
        $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        if (empty($ext)) {
            $ext = '';
        }
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return redirect()->back()->withErrors(['logo' => 'Format logo harus berupa PNG, JPG, JPEG, atau WebP.']);
        }
        if (!$file->isValid()) {
            return redirect()->back()->withErrors(['logo' => 'Gagal memproses gambar. Coba gunakan file yang lebih kecil.']);
        }

        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();
        $this->ensureCanManageSchool($user, $school);

        if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
            Storage::disk('public')->delete($school->logo_path);
        }

        $path = $file->store('schools/logos', 'public');
        $school->update(['logo_path' => $path]);

        return redirect()->route('school.settings', ['tab' => 'profil'])
            ->with('status', 'Logo resmi sekolah berhasil diunggah!');
    }

    /**
     * Update school administrator personal account profile.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama administrator wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('school.settings', ['tab' => 'akun'])
            ->with('status', 'Informasi akun administrator berhasil diperbarui!');
    }

    /**
     * Update school administrator password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('school.settings', ['tab' => 'akun'])
            ->with('status', 'Kata sandi akun Anda berhasil diperbarui!');
    }

    /**
     * Tambah akun pengajar / staf baru di sekolah ini.
     */
    public function storeMember(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();
        $this->ensureCanManageSchool($user, $school);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'in:school,bendahara,operator,teacher,staff'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', Password::defaults()],
        ], [
            'name.required' => 'Nama anggota tim wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role.required' => 'Pilih peran/jabatan anggota tim.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        User::create([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('school.settings', ['tab' => 'tim'])
            ->with('status', 'Akun tim pengajar/staf berhasil ditambahkan!');
    }

    /**
     * Hapus akun staf / pengajar dari sekolah.
     */
    public function deleteMember(Request $request, User $member): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();
        $this->ensureCanManageSchool($user, $school);

        if ((int) $member->school_id !== (int) $school->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ((int) $member->id === (int) $school->owner_user_id || (int) $member->id === (int) $user->id) {
            return redirect()->route('school.settings', ['tab' => 'tim'])
                ->with('error', 'Akun pemilik sekolah atau akun Anda sendiri tidak dapat dihapus.');
        }

        $member->delete();

        return redirect()->route('school.settings', ['tab' => 'tim'])
            ->with('status', 'Akun anggota tim berhasil dihapus.');
    }

    /**
     * Unduh berkas format template CSV / Dokumen Bantuan (Siswa, Guru, Soal, Tata Tertib).
     */
    public function downloadTemplate(string $type): StreamedResponse
    {
        $filenames = [
            'siswa' => 'template_dapodik_siswa.csv',
            'guru' => 'template_dapodik_guru.csv',
            'soal' => 'template_bank_soal_cbt_veyra.csv',
            'tatatertib' => 'panduan_tata_tertib_ujian_cbt.txt',
        ];

        $filename = $filenames[$type] ?? "template_{$type}.csv";
        $contentType = $type === 'tatatertib' ? 'text/plain; charset=UTF-8' : 'text/csv; charset=UTF-8';

        return response()->streamDownload(function () use ($type) {
            // Output UTF-8 BOM so Microsoft Excel renders accented and special characters correctly
            echo "\xEF\xBB\xBF";

            if ($type === 'siswa') {
                echo "NISN,NIK,Nama Siswa,Jenis Kelamin (L/P),Tingkat Kelas,Rombongan Belajar,Tempat Lahir,Tanggal Lahir (YYYY-MM-DD),Nama Ibu Kandung,No WhatsApp\n";
                echo "0081234567,3201010101080001,Ahmad Pratama,L,10,10 IPA 1,Jakarta,2008-05-14,Siti Aminah,081234567890\n";
                echo "0081234568,3201010101080002,Nurul Hidayah,P,10,10 IPA 1,Bandung,2008-08-22,Fatimah Zahra,081234567891\n";
            } elseif ($type === 'guru') {
                echo "NUPTK,NIP,Nama Lengkap,Gelar,Jenis Kelamin (L/P),Mata Pelajaran,No WhatsApp,Email Akun\n";
                echo "1234567890123456,198501012010011001,Budi Santoso,S.Pd.,L,Matematika,081234567890,budi.santoso@sekolah.sch.id\n";
                echo "1234567890123457,-,Dewi Lestari,M.Pd.,P,Bahasa Indonesia,081234567892,dewi.lestari@sekolah.sch.id\n";
            } elseif ($type === 'soal') {
                echo "No,Tipe Soal (PG/Esai),Pertanyaan,Pilihan A,Pilihan B,Pilihan C,Pilihan D,Pilihan E,Kunci Jawaban,Bobot Nilai,Limit Audio (0=Unlimited)\n";
                echo "1,PG,\"Hasil dari 25 x 4 + 50 adalah ...\",100,125,150,175,200,C,1,0\n";
                echo "2,PG,\"Ibu kota Negara Kesatuan Republik Indonesia adalah ...\",Surabaya,Jakarta,Bandung,Nusantara,Medan,D,1,0\n";
                echo "3,PG,\"Listen to the audio conversation: What is the speaker's main topic?\",Weather,Vacation plan,Favorite food,School exam,Music concert,B,1,2\n";
                echo "4,Esai,\"Jelaskan secara singkat siklus terjadinya hujan (daur air)!\",-,-,-,-,-,\"Evaporasi - Kondensasi - Presipitasi\",5,0\n";
            } elseif ($type === 'tatatertib') {
                echo "========================================================================\n";
                echo "PANDUAN & TATA TERTIB PESERTA UJIAN ASESMEN DIGITAL (CBT ONLINE)\n";
                echo "PLATFORM CLOUD: VEYRA EDUCATE & CBT ONLINE\n";
                echo "========================================================================\n\n";
                echo "A. PERSIAPAN SEBELUM UJIAN:\n";
                echo "1. Pastikan baterai ponsel/laptop terisi penuh (minimal 70%) atau bawa charger.\n";
                echo "2. Pastikan telah menerima KARTU PESERTA UJIAN resmi yang memuat:\n";
                echo "   - Nama Lengkap & Kelas\n";
                echo "   - NIS / Nomor Peserta\n";
                echo "   - Sandi Ujian (Password)\n";
                echo "   - Ruang & Sesi Ujian\n";
                echo "3. Terhubung ke jaringan Wi-Fi sekolah atau paket data internet yang stabil.\n\n";
                echo "B. SAAT MENGERJAKAN UJIAN:\n";
                echo "1. Buka browser (Google Chrome disarankan) lalu masukkan URL ujian sekolah Anda.\n";
                echo "2. Masukkan Kode Sekolah, NIS, dan Sandi Ujian persis seperti di kartu.\n";
                echo "3. Periksa kesesuaian Nama Mata Pelajaran dan Alokasi Waktu Ujian.\n";
                echo "4. Masukkan TOKEN UJIAN yang diberikan oleh Pengawas Ruang.\n";
                echo "5. Pilihlah jawaban dengan mengklik opsi yang benar (jawaban otomatis tersimpan).\n";
                echo "6. Untuk soal listening/audio, perhatikan batas putar yang tertera pada layar.\n";
                echo "7. Dilarang berpindah aplikasi (split screen / membuka WhatsApp / browser lain),\n";
                echo "   karena sistem CBT Veyra dapat mencatat riwayat perpindahan layar.\n\n";
                echo "C. KETIKA SELESAI UJIAN:\n";
                echo "1. Klik tombol 'Selesai Ujian' dan lakukan konfirmasi akhir.\n";
                echo "2. Pastikan muncul notifikasi bahwa jawaban telah berhasil dikirim ke server.\n";
                echo "3. Lakukan Logout dan tinggalkan ruang ujian dengan tertib.\n\n";
                echo "Semoga sukses dan raih hasil terbaik dengan jujur & mandiri!\n";
            }
        }, $filename, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Update school persistent brand theme.
     */
    public function updateTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'brand_theme' => ['required', 'string', 'in:teal,indigo,rose,emerald,violet,slate'],
        ]);

        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();
        if ($school) {
            $meta = $school->meta ?? [];
            $meta['brand_theme'] = $validated['brand_theme'];
            $school->update(['meta' => $meta]);
        }

        return response()->json([
            'success' => true,
            'brand_theme' => $validated['brand_theme'],
        ]);
    }

    /**
     * Store a custom agenda item in the school metadata.
     */
    public function storeAgenda(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'in:CBT & Ujian,Akademik,Rapat & Dinas,Pemeliharaan'],
        ], [
            'title.required' => 'Judul agenda wajib diisi.',
            'date.required' => 'Tanggal pelaksanaan wajib dipilih.',
            'category.required' => 'Kategori agenda wajib dipilih.',
        ]);

        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();

        $meta = $school->meta ?? [];
        $agendas = is_array($meta['agendas'] ?? null) ? $meta['agendas'] : [];

        $agendas[] = [
            'id' => uniqid('ag_', false),
            'title' => $validated['title'],
            'date' => $validated['date'],
            'category' => $validated['category'],
        ];

        $meta['agendas'] = array_values($agendas);
        $school->update(['meta' => $meta]);

        return back()->with('status', 'Agenda kegiatan sekolah berhasil ditambahkan!');
    }

    /**
     * Delete a custom agenda item from the school metadata.
     */
    public function destroyAgenda(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();

        $meta = $school->meta ?? [];
        $agendas = is_array($meta['agendas'] ?? null) ? $meta['agendas'] : [];

        $meta['agendas'] = array_values(array_filter(
            $agendas,
            fn (array $item): bool => ($item['id'] ?? '') !== $id
        ));

        $school->update(['meta' => $meta]);

        return back()->with('status', 'Agenda kegiatan sekolah berhasil dihapus.');
    }

    /**
     * Ensure only the school owner or authorized operator can manage school identity & team members.
     */
    private function ensureCanManageSchool(User $user, School $school): void
    {
        if ($user->isAdmin() || (int) $user->id === (int) $school->owner_user_id) {
            return;
        }

        if (! in_array($user->role, ['school', 'operator', null, ''], true)) {
            abort(403, 'Hanya pemilik sekolah atau operator utama yang diizinkan mengelola pengaturan ini.');
        }
    }
}
