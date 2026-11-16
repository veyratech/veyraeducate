<x-guest-layout>
    <div class="mb-4 text-sm text-muted">
        Terima kasih telah mendaftar. Sebelum mulai, silakan verifikasi email dengan mengklik tautan yang baru saja kami kirim. Belum menerima email? Kirim ulang di bawah.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-brand-700">
            Tautan verifikasi baru telah dikirim ke email yang Anda daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim ulang email verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm text-muted underline hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
