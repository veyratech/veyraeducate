<footer class="border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 pb-20 lg:pb-0">
    <div class="container-site py-14 sm:py-16">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <x-brand-logo size="lg" :loading="'lazy'" :fetchPriority="'low'" />
                <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    Wadah aplikasi sekolah untuk Indonesia. Satu akun untuk dashboard, langganan, dan aplikasi Anda.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a
                        href="https://wa.me/6285707074778?text=Halo%20Veyra%20Educate"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold shadow-sm transition-all hover:border-brand-200 hover:bg-brand-50/50 dark:border-emerald-600 dark:bg-emerald-700 dark:text-white dark:hover:bg-emerald-600"
                    >
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.3-1.65a11.86 11.86 0 0 0 5.76 1.47h.01c6.55 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.44ZM12.07 21.7h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.74.98 1-3.64-.24-.37a9.8 9.8 0 0 1-1.5-5.23c0-5.42 4.41-9.83 9.84-9.83 2.63 0 5.1 1.03 6.96 2.89a9.78 9.78 0 0 1 2.88 6.95c0 5.42-4.41 9.83-9.83 9.83Zm5.4-7.37c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-50h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.12-.27-.2-.57-.35Z"/>
                        </svg>
                        Hubungi Kami
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-brand-700 hover:shadow-md">
                        Daftar sekolah
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">Jelajahi</h3>
                <ul class="mt-4 space-y-2.5">
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/fitur">Fitur</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/harga">Harga</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/demo">Demo</a></li>
                    <li><a class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:underline transition-colors" href="/brosur">Brosur Digital</a></li>
                    <li><a class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:underline transition-colors" href="/proposal">Proposal Resmi</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/tentang">Tentang</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/faq">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">Paket</h3>
                <ul class="mt-4 space-y-2.5">
                    @php
                        try {
                            $footerPlans = \App\Models\Plan::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->get(['code', 'label', 'price_idr']);
                        } catch (\Throwable) {
                            $footerPlans = collect();
                        }
                    @endphp
                    @forelse ($footerPlans as $plan)
                        <li>
                            <a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/harga">
                                {{ $plan->label }} — Rp {{ number_format($plan->price_idr, 0, ',', '.') }}
                            </a>
                        </li>
                    @empty
                        <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/harga">Lihat paket</a></li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">Akun</h3>
                <ul class="mt-4 space-y-2.5">
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="{{ route('register') }}">Daftar sekolah</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="{{ route('login') }}">Masuk panel</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="{{ route('password.request') }}">Lupa password</a></li>
                    <li><a class="text-sm text-slate-600 dark:text-slate-400 hover:text-brand-700 dark:hover:text-brand-300 transition-colors" href="/kontak">Bantuan WhatsApp</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-slate-200 dark:border-slate-800 pt-6 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500 dark:text-slate-500">
                &copy; {{ date('Y') }} PT. VEYRA CREATIVE TECH ({{ config('app.name') }}). Seluruh hak cipta dilindungi.
            </p>
            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                <a href="/kebijakan-privasi" class="hover:text-brand-600 dark:hover:text-brand-400 transition">Kebijakan Privasi</a>
                <span>&bull;</span>
                <a href="/syarat-ketentuan" class="hover:text-brand-600 dark:hover:text-brand-400 transition">Syarat &amp; Ketentuan</a>
                <span>&bull;</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">100% Siap SPJ BOS</span>
            </div>
        </div>
    </div>
</footer>