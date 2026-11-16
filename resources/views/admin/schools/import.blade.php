<x-admin-layout title="Import Sekolah — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Tenant</p>
                <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">Import CSV Sekolah</h2>
                <p class="mt-1 text-sm text-muted dark:text-slate-400">
                    <a href="{{ route('admin.schools.index') }}" class="link-muted">← Kembali ke daftar sekolah</a>
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6">

            @if (!$successes && !$failures)
                {{-- Upload Form --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 stagger-in">
                    <div class="card-minimal p-6 lg:col-span-2">
                        <h3 class="text-base font-bold text-ink dark:text-slate-100 mb-1">Upload File CSV</h3>
                        <p class="text-xs text-muted dark:text-slate-400 mb-5">
                            Maksimum 5 MB. Baris pertama adalah header kolom.
                            Kolom NPSN (Dapodik) atau NSM (EMIS Kemenag) akan otomatis mengisi data sekolah.
                        </p>

                        <form method="POST" action="{{ route('admin.schools.process-import') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="csv_file" value="File CSV (.csv)" />
                                <x-text-input
                                    id="csv_file"
                                    type="file"
                                    name="csv_file"
                                    accept=".csv"
                                    class="mt-1 block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-700"
                                    required
                                />
                                <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                            </div>

                            <div class="rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                                <table class="table-minimal text-xs w-full">
                                    <thead>
                                        <tr class="bg-slate-50 dark:bg-slate-900">
                                            <th class="px-3 py-2 text-left font-semibold text-ink dark:text-slate-200">Kolom</th>
                                            <th class="px-3 py-2 text-left font-semibold text-ink dark:text-slate-200">Keterangan</th>
                                            <th class="px-3 py-2 text-left font-semibold text-ink dark:text-slate-200">Wajib?</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">nama_sekolah</td>
                                            <td class="px-3 py-2 text-muted">Nama lengkap sekolah</td>
                                            <td class="px-3 py-2 text-muted">Ya (atau email)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">npsn</td>
                                            <td class="px-3 py-2 text-muted">NPSN 8 digit → auto-fill Dapodik (nama, kota, negeri/swasta)</td>
                                            <td class="px-3 py-2 text-muted">Tidak</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">nsm</td>
                                            <td class="px-3 py-2 text-muted">NSM 10-13 digit madrasah → auto-fill EMIS Kemenag</td>
                                            <td class="px-3 py-2 text-muted">Tidak</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">email</td>
                                            <td class="px-3 py-2 text-muted">Email sekolah (login operator)</td>
                                            <td class="px-3 py-2 text-muted">Ya</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">owner_name</td>
                                            <td class="px-3 py-2 text-muted">Nama lengkap operator / kepala sekolah</td>
                                            <td class="px-3 py-2 text-muted">Ya</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">owner_email</td>
                                            <td class="px-3 py-2 text-muted">Email login operator</td>
                                            <td class="px-3 py-2 text-muted">Ya</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">owner_password</td>
                                            <td class="px-3 py-2 text-muted">Password login operator (min. 8 karakter)</td>
                                            <td class="px-3 py-2 text-muted">Ya</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">kota</td>
                                            <td class="px-3 py-2 text-muted">Kota / kabupaten (opsional, diisi otomatis dari NPSN)</td>
                                            <td class="px-3 py-2 text-muted">Tidak</td>
                                        </tr>
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-brand-700 dark:text-brand-400">telepon</td>
                                            <td class="px-3 py-2 text-muted">Nomor telepon sekolah</td>
                                            <td class="px-3 py-2 text-muted">Tidak</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <x-primary-button class="btn-minimal-primary">Proses Import</x-primary-button>
                                <a href="{{ route('admin.schools.index') }}" class="btn-minimal-secondary text-sm">Batal</a>
                            </div>
                        </form>
                    </div>

                    <div class="space-y-4">
                        <div class="card-minimal p-5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-muted dark:text-slate-500 mb-3">Sumber Data Otomatis</h4>
                            <ul class="space-y-2.5 text-xs text-muted dark:text-slate-400">
                                <li class="flex items-start gap-2">
                                    <span class="badge-minimal badge-minimal-active mt-0.5 shrink-0">Dapodik</span>
                                    <span>NPSN 8 digit → nama, kota, level, status negeri/swasta, lembaga</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="badge-minimal badge-minimal-pending mt-0.5 shrink-0">EMIS</span>
                                    <span>NSM madrasah → nama, NPSN, alamat, level MI/MTS/MA, Kemenag</span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-minimal p-5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-muted dark:text-slate-500 mb-3">Contoh Format CSV</h4>
                            <code class="block bg-slate-100 dark:bg-slate-800 rounded p-2.5 text-[11px] font-mono text-slate-600 dark:text-slate-300 break-all leading-relaxed">
nama_sekolah,npsn,email,owner_name,owner_email,owner_password,kota<br>
SDN 001 Jakarta,00100001,sd001@example.com,Ahmad,Ahmad@example.com,password123,Jakarta
                            </code>
                        </div>
                    </div>
                </div>

            @else
                {{-- Results --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 stagger-in">
                    @php $totalRows = count($successes) + count($failures); @endphp
                    <div class="card-minimal p-4">
                        <p class="text-[11px] uppercase tracking-wider text-muted dark:text-slate-500 font-semibold">Total Baris</p>
                        <p class="mt-1 text-2xl font-bold text-ink dark:text-slate-100">{{ $totalRows }}</p>
                    </div>
                    <div class="card-minimal p-4">
                        <p class="text-[11px] uppercase tracking-wider text-emerald-700 dark:text-emerald-400 font-semibold">Berhasil</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ count($successes) }}</p>
                    </div>
                    <div class="card-minimal p-4">
                        <p class="text-[11px] uppercase tracking-wider text-red-700 dark:text-red-400 font-semibold">Gagal</p>
                        <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ count($failures) }}</p>
                    </div>
                    <div class="card-minimal p-4">
                        <p class="text-[11px] uppercase tracking-wider text-muted dark:text-slate-500 font-semibold">Skipped</p>
                        <p class="mt-1 text-2xl font-bold text-ink dark:text-slate-100">{{ max(0, $totalRows - count($successes) - count($failures)) }}</p>
                    </div>
                </div>

                @if (!empty($successes))
                    <div class="card-minimal overflow-hidden stagger-in" style="animation-delay:60ms;">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-ink dark:text-slate-100">Baris Berhasil ({!! count($successes) !!})</h3>
                            <button type="button" onclick="window.print()" class="btn-minimal-ghost text-xs">Cetak</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-minimal table-responsive-card text-xs">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">No</th>
                                        <th class="px-4 py-2 text-left">Nama Sekolah</th>
                                        <th class="px-4 py-2 text-left">NPSN</th>
                                        <th class="px-4 py-2 text-left">Email</th>
                                        <th class="px-4 py-2 text-left">Password Sementara</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @foreach ($successes as $i => $s)
                                        <tr>
                                            <td class="px-4 py-2 text-muted">{{ $i + 1 }}</td>
                                            <td class="px-4 py-2 font-semibold text-ink dark:text-slate-200">{{ e($s['nama']) }}</td>
                                            <td class="px-4 py-2 font-mono text-muted">{{ e($s['npsn'] ?? '—') }}</td>
                                            <td class="px-4 py-2 text-muted">{{ e($s['email']) }}</td>
                                            <td class="px-4 py-2">
                                                <code class="bg-slate-100 dark:bg-slate-800 rounded px-1.5 py-0.5 text-brand-700 dark:text-brand-400 font-mono">{{ e($s['temp_password']) }}</code>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if (!empty($failures))
                    <div class="card-minimal overflow-hidden stagger-in" style="animation-delay:120ms;">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-sm font-bold text-red-700 dark:text-red-400">Baris Gagal ({!! count($failures) !!})</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-minimal table-responsive-card text-xs">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">Baris</th>
                                        <th class="px-4 py-2 text-left">Error</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @foreach ($failures as $f)
                                        <tr>
                                            <td class="px-4 py-2 font-mono text-muted">#{{ $f['row'] }}</td>
                                            <td class="px-4 py-2 text-red-700 dark:text-red-400">{{ e($f['error']) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-2 stagger-in" style="animation-delay:180ms;">
                    <a href="{{ route('admin.schools.import') }}" class="btn-minimal-primary text-sm">Import Lagi</a>
                    <a href="{{ route('admin.schools.index') }}" class="btn-minimal-secondary text-sm">Kembali ke Daftar</a>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
