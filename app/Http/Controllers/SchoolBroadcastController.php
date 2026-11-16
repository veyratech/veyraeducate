<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Services\WhatsAppService;
use App\Support\WhatsAppConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolBroadcastController extends Controller
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Display the school WhatsApp broadcast center.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->first();

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

        $waConfig = WhatsAppConfig::get();
        $isGatewayActive = $waConfig['enabled'] && ! empty($waConfig['api_token']) && $waConfig['gateway'] !== 'log';

        $recentBroadcasts = $school->meta['broadcast_logs'] ?? [];
        $teacherContacts = $school->users()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'phone']);

        // Sample ready-to-use school message templates
        $templates = [
            [
                'id' => 'exam_schedule',
                'name' => 'Jadwal & Tata Tertib Ujian CBT',
                'category' => 'Ujian',
                'title' => 'Pengumuman Jadwal STS/SAS CBT Online',
                'text' => "📢 *PENGUMUMAN ASESMEN SUMATIF (CBT ONLINE)*\n*{$school->name}*\n\n"
                    ."Yth. Bapak/Ibu Guru & Wali Murid,\n\n"
                    ."Diberitahukan bahwa pelaksanaan Asesmen Sumatif Tengah Semester (STS) berbasis komputer akan diselenggarakan pada:\n"
                    ."📅 *Tanggal:* Senin - Jumat mendatang\n"
                    ."⏰ *Waktu:* Sesuai sesi di Kartu Peserta Ujian\n"
                    .'🌐 *Portal Ujian:* '.url('/')."\n\n"
                    ."*Petunjuk Penting Siswa:*\n"
                    ."1. Bawa smartphone/laptop dengan baterai terisi penuh.\n"
                    ."2. Wajib membawa Kartu Peserta Ujian resmi.\n"
                    ."3. Token ujian akan dibagikan oleh pengawas ruang.\n\n"
                    ."Terima kasih atas kerjasama dan dukungan Bapak/Ibu sekalian.\n\n"
                    ."_Humas {$school->name}_",
            ],
            [
                'id' => 'gladi_bersih',
                'name' => 'Undangan Gladi Bersih / Simulasi',
                'category' => 'Simulasi',
                'title' => 'Simulasi & Gladi Bersih Ujian Sekolah',
                'text' => "⚡ *GLADI BERSIH SIMULASI ASESMEN DIGITAL*\n*{$school->name}*\n\n"
                    ."Kepada seluruh siswa-siswi peserta ujian,\n\n"
                    ."Guna memastikan kelancaran perangkat dan koneksi internet, sekolah akan mengadakan gladi bersih simulasi CBT pada:\n"
                    ."📅 *Hari/Tanggal:* Besok Pukul 08.00 WIB\n"
                    ."🔑 *Token Simulasi:* `SIMULASI2026`\n\n"
                    ."Harap seluruh siswa mencoba login dan menyelesaikan 10 soal uji coba untuk memastikan akun Anda aktif.\n\n"
                    ."_Tim IT / Operator {$school->name}_",
            ],
            [
                'id' => 'spp_reminder',
                'name' => 'Pemberitahuan Administrasi / SPP',
                'category' => 'Keuangan',
                'title' => 'Informasi Administrasi Pendidikan & SPP',
                'text' => "💳 *INFORMASI ADMINISTRASI PENDIDIKAN*\n*{$school->name}*\n\n"
                    ."Yth. Orang Tua / Wali Murid dari Siswa:\n"
                    ."Nama: [Nama Siswa]\n"
                    ."Kelas: [Kelas]\n\n"
                    ."Kami menginformasikan kewajiban administrasi SPP bulan berjalan untuk kelancaran kegiatan belajar mengajar.\n"
                    ."Pembayaran dapat dilakukan melalui loket tata usaha sekolah atau transfer resmi rekening sekolah.\n\n"
                    ."Abaikan pesan ini apabila Bapak/Ibu telah menyelesaikan pembayaran. Terima kasih atas perhatiannya.\n\n"
                    ."_Bendahara {$school->name}_",
            ],
            [
                'id' => 'report_card',
                'name' => 'Undangan Pembagian Rapor',
                'category' => 'Akademik',
                'title' => 'Pengambilan Hasil Belajar (Rapor Semester)',
                'text' => "📄 *UNDANGAN PEMBAGIAN RAPOR SISWA*\n*{$school->name}*\n\n"
                    ."Yth. Bapak/Ibu Wali Murid,\n\n"
                    ."Kami mengundang kehadiran Bapak/Ibu untuk menerima laporan hasil belajar (Rapor) putra/putri tercinta pada:\n"
                    ."📅 *Hari/Tanggal:* Sabtu, Akhir Semester\n"
                    ."⏰ *Pukul:* 08.00 - 11.30 WIB\n"
                    ."📍 *Tempat:* Ruang Kelas Masing-masing\n\n"
                    ."Kehadiran Bapak/Ibu sangat diharapkan demi evaluasi kemajuan belajar siswa.\n\n"
                    ."_Kepala Sekolah {$school->name}_",
            ],
        ];

        return view('school.broadcast.index', compact(
            'school',
            'isGatewayActive',
            'waConfig',
            'templates',
            'recentBroadcasts',
            'teacherContacts'
        ));
    }

    /**
     * Send or dispatch WhatsApp announcement.
     */
    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'recipient_type' => ['required', 'string', 'in:single,parents,teachers,group_link'],
            'phone' => ['nullable', 'string', 'max:500'],
            'send_method' => ['required', 'string', 'in:gateway,wa_link'],
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'message.required' => 'Isi pesan WhatsApp wajib diisi.',
            'recipient_type.required' => 'Pilih target penerima pesan.',
        ]);

        $logs = $school->meta['broadcast_logs'] ?? [];

        if ($validated['send_method'] === 'gateway') {
            $recipients = [];

            if (! empty($validated['phone'])) {
                $recipients = array_values(array_filter(array_map('trim', explode(',', $validated['phone']))));
            } elseif ($validated['recipient_type'] === 'teachers') {
                $recipients = $school->users()
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->pluck('phone')
                    ->unique()
                    ->values()
                    ->all();
            }

            if (empty($recipients)) {
                $fallbackPhone = $school->phone ?: $user->phone;
                if (! empty($fallbackPhone)) {
                    $recipients = [$fallbackPhone];
                }
            }

            if (empty($recipients)) {
                return back()->with('error', 'Nomor telepon tujuan belum ditentukan.');
            }

            $sentCount = 0;
            foreach ($recipients as $targetPhone) {
                if ($this->whatsAppService->sendMessage((string) $targetPhone, $validated['message'])) {
                    $sentCount++;
                }
            }

            $success = $sentCount > 0;
            $recipientLabel = count($recipients) > 1
                ? count($recipients).' Nomor ('.implode(', ', array_slice($recipients, 0, 2)).(count($recipients) > 2 ? ', ...' : '').')'
                : $recipients[0];

            $logs[] = [
                'id' => 'BC-'.strtoupper(uniqid()),
                'date' => now()->translatedFormat('d M Y H:i'),
                'title' => $validated['title'],
                'message' => $validated['message'],
                'recipient' => $recipientLabel,
                'type' => $validated['recipient_type'],
                'method' => 'Gateway API',
                'status' => $success ? 'Terkirim ('.$sentCount.'/'.count($recipients).')' : 'Tersimpan ke Log Simulasi',
            ];

            $meta = $school->meta ?? [];
            $meta['broadcast_logs'] = array_slice($logs, -20);
            $school->update(['meta' => $meta]);

            if ($success) {
                return back()->with('success', 'Pesan pengumuman berhasil dikirim melalui WhatsApp Gateway ke '.$recipientLabel);
            }

            return back()->with('info', 'Pesan tersimpan ke log sistem simulasi WhatsApp karena gateway live belum diaktifkan oleh admin.');
        }

        // Mode WhatsApp Link / Web direct
        $encodedMessage = urlencode($validated['message']);
        $phoneParam = ! empty($validated['phone']) ? preg_replace('/[^0-9]/', '', $validated['phone']) : '';
        if (str_starts_with($phoneParam, '0')) {
            $phoneParam = '62'.substr($phoneParam, 1);
        }
        $waUrl = 'https://wa.me/'.($phoneParam ?: '').'?text='.$encodedMessage;

        $logs[] = [
            'id' => 'BC-'.strtoupper(uniqid()),
            'date' => now()->translatedFormat('d M Y H:i'),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'recipient' => $phoneParam ?: 'Grup WhatsApp / Kontak Pilihan',
            'type' => $validated['recipient_type'],
            'method' => 'WhatsApp Web Link',
            'status' => 'Dibuka via WhatsApp',
        ];

        $meta = $school->meta ?? [];
        $meta['broadcast_logs'] = array_slice($logs, -20);
        $school->update(['meta' => $meta]);

        return redirect()->away($waUrl);
    }

    /**
     * Clear school broadcast logs history.
     */
    public function clearHistory(Request $request): RedirectResponse
    {
        $user = $request->user();
        $school = $user->school ?? School::where('owner_user_id', $user->id)->firstOrFail();

        $meta = $school->meta ?? [];
        $meta['broadcast_logs'] = [];
        $school->update(['meta' => $meta]);

        return redirect()
            ->route('school.broadcast.index')
            ->with('success', 'Riwayat log broadcast berhasil dibersihkan.');
    }
}
