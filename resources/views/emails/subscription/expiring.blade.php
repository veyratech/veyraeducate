<x-mail::message>
# Peringatan Masa Langganan

Halo {{ $subscription->school->name ?? 'Sekolah' }},

Langganan Veyra Educate untuk sekolah Anda akan berakhir dalam {{ $daysUntilExpiry }} hari.

**Detail Langganan:**

- **Nama Sekolah:** {{ $subscription->school->name ?? '—' }}
- **Paket:** {{ $subscription->plan?->label ?? '—' }}
- **Berakhir pada:** {{ $subscription->ends_at->locale('id')->isoFormat('dddd, DD MMMM Y') }}

Klik tombol di bawah untuk segera memperpanjang langganan Anda dan menghindari gangguan layanan.

<x-mail::button :url="route('dashboard')" color="primary">
Perpanjang Sekarang
</x-mail::button>

Jika Anda sudah memperpanjang, abaikan pesan ini.

Terima kasih,
Tim Veyra Educate
</x-mail::message>
