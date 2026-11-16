<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Siswa — {{ $school->name }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: #e2e8f0;
            color: #0f172a;
        }
        .no-print {
            background: #ffffff;
            border-bottom: 2px solid #cbd5e1;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .btn-print {
            background: #0f766e;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: #115e59;
        }
        .btn-close {
            background: #64748b;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Standard A4 Paper */
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            margin: 10mm auto;
            background: #ffffff;
            padding: 8mm 6mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            page-break-after: always;
            position: relative;
        }

        /* 8 Cards Grid (2 cols x 4 rows) */
        .grid-8 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5mm 6mm;
        }
        .card-box-8 {
            width: 93mm;
            height: 62mm;
        }

        /* 10 Cards Grid (2 cols x 5 rows) */
        .grid-10 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3.5mm 6mm;
        }
        .card-box-10 {
            width: 93mm;
            height: 52mm;
        }

        .card-box {
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
            position: relative;
        }

        /* Crop Marks / Corner Guides */
        .crop-mark {
            position: absolute;
            font-family: monospace;
            font-size: 8pt;
            color: #94a3b8;
            line-height: 1;
            user-select: none;
        }

        /* Theme Accent Headers */
        .header-teal { background: #0f766e !important; color: #ffffff !important; }
        .header-blue { background: #1d4ed8 !important; color: #ffffff !important; }
        .header-emerald { background: #059669 !important; color: #ffffff !important; }
        .header-amber { background: #d97706 !important; color: #ffffff !important; }
        .header-rose { background: #e11d48 !important; color: #ffffff !important; }
        .header-indigo { background: #4338ca !important; color: #ffffff !important; }
        .header-slate { background: #334155 !important; color: #ffffff !important; }

        .logo-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            background: #ffffff;
            border-radius: 4px;
            padding: 2px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            line-height: 1.25;
        }
        .info-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 24mm;
            color: #475569;
            font-weight: 700;
        }
        .info-table .colon {
            width: 2.5mm;
            text-align: center;
        }
        .info-table .value {
            font-weight: 800;
            color: #0f172a;
        }

        /* Photo Box */
        .photo-box {
            width: 20mm;
            height: 26mm;
            border: 1px solid #94a3b8;
            background: #f8fafc;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 6.5pt;
            color: #64748b;
            text-align: center;
            flex-shrink: 0;
            line-height: 1.2;
        }

        @media print {
            body {
                background: #ffffff !important;
            }
            .no-print {
                display: none !important;
            }
            .a4-page {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 4mm 5mm !important;
                box-shadow: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 4mm;
            }
        }
    </style>
</head>
<body>

    {{-- Interactive Top Toolbar (Hidden on actual print) --}}
    <div class="no-print">
        <div>
            <div style="font-size: 14px; font-weight: 800; color: #0f172a;">
                🪪 Pratinjau Cetak: {{ count($students) }} Siswa &bull; {{ ceil(count($students) / $cardsPerPage) }} Lembar A4
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                Template: <strong style="color: #0f766e; text-transform: uppercase;">{{ $cardTemplate }}</strong> &bull; Format: {{ $cardsPerPage }} Kartu / Halaman A4
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print">
                <span>🖨️ Cetak Dokumen / Simpan PDF</span>
            </button>
            <button onclick="if (window.opener) { window.close(); } else { window.location.href='{{ route('school.cards.index') }}'; }" class="btn-close">
                ← Kembali / Tutup
            </button>
        </div>
    </div>

    @php
        $pages = array_chunk($students, $cardsPerPage);
        $headerClass = 'header-' . ($cardTheme ?? 'teal');
        $gridClass = $cardsPerPage === 10 ? 'grid-10' : 'grid-8';
        $cardBoxClass = $cardsPerPage === 10 ? 'card-box-10' : 'card-box-8';
    @endphp

    @forelse ($pages as $pageIndex => $pageStudents)
        <div class="a4-page">
            <div class="{{ $gridClass }}">
                @foreach ($pageStudents as $s)
                    <div class="card-box {{ $cardBoxClass }}">

                        {{-- ================= TEMPLATE 1: MODERN MINIMALIS ================= --}}
                        @if ($cardTemplate === 'modern')
                            {{-- Header --}}
                            <div class="card-header {{ $headerClass }}" style="padding: 4px 8px; display: flex; align-items: center; gap: 8px;">
                                @if ($school->logo_path)
                                    <img src="{{ asset('storage/' . $school->logo_path) }}" class="logo-img">
                                @else
                                    <div class="logo-img" style="display:flex;align-items:center;justify-content:center;font-weight:900;font-size:8pt;color:#0f766e;">SK</div>
                                @endif
                                <div style="flex: 1; line-height: 1.15; min-width: 0;">
                                    <div style="font-size: 7pt; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; opacity: 0.95;">
                                        {{ $cardType === 'exam' ? 'KARTU PESERTA ASESMEN (CBT)' : 'KARTU TANDA PELAJAR' }}
                                    </div>
                                    <div style="font-size: 9pt; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $school->name }}
                                    </div>
                                    <div style="font-size: 6.5pt; opacity: 0.85;">
                                        {{ $cardType === 'exam' ? $examTitle : 'NISN Resmi Terdaftar' }} &bull; TP {{ $academicYear }}
                                    </div>
                                </div>
                                <div style="font-size: 7pt; font-weight: 800; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px;">
                                    {{ $s['class'] ?? 'KELAS' }}
                                </div>
                            </div>

                            {{-- Body --}}
                            <div style="padding: 5px 8px; display: flex; gap: 8px; flex: 1; align-items: center;">
                                <div class="photo-box">
                                    PAS FOTO<br>2 × 3 cm
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">Nama Lengkap</td>
                                            <td class="colon">:</td>
                                            <td class="value" style="font-size: 7.8pt;">{{ $s['name'] ?? 'Nama Siswa' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">NISN / NIK</td>
                                            <td class="colon">:</td>
                                            <td class="value">{{ $s['nisn'] ?? '-' }} / {{ $s['nik'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Kelas / Rombel</td>
                                            <td class="colon">:</td>
                                            <td class="value">{{ $s['class'] ?? '-' }}</td>
                                        </tr>
                                        @if ($cardType === 'exam')
                                            <tr>
                                                <td class="label">Sesi &bull; Ruang</td>
                                                <td class="colon">:</td>
                                                <td class="value">{{ $s['session'] ?? 'Sesi 1' }} &bull; {{ $s['room'] ?? 'Lab 1' }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="label">TTL</td>
                                                <td class="colon">:</td>
                                                <td class="value">{{ $s['birth_place'] ?? '-' }}, {{ $s['birth_date'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    </table>

                                    @if ($cardType === 'exam')
                                        <div style="margin-top: 3px; padding: 2px 6px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 6.8pt; display: flex; justify-content: space-between;">
                                            <span>User: <strong style="font-family: monospace;">{{ $s['exam_user'] ?? $s['nisn'] }}</strong></span>
                                            <span>Sandi: <strong style="color: #b91c1c; font-family: monospace;">{{ $s['exam_pass'] ?? 'PASS26' }}</strong></span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Barcode --}}
                            <div style="padding: 2px 8px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; font-size: 6pt;">
                                <div>
                                    {!! \App\Support\CardCodeGenerator::barcodeSvg($s['nisn'] ?? '0081234567', 120, 22) !!}
                                </div>
                                <div style="text-align: right; line-height: 1.1;">
                                    <strong>{{ $headmasterName }}</strong><br>
                                    <span style="color: #64748b;">Kepala Sekolah</span>
                                </div>
                            </div>

                        {{-- ================= TEMPLATE 2: FORMAL KEDINASAN ================= --}}
                        @elseif ($cardTemplate === 'formal')
                            {{-- Formal Kop Surat with Double Line --}}
                            <div style="padding: 4px 8px 3px; border-bottom: 2px solid #0f172a; text-align: center;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 28px; vertical-align: middle;">
                                            @if ($school->logo_path)
                                                <img src="{{ asset('storage/' . $school->logo_path) }}" style="width: 26px; height: 26px; object-fit: contain;">
                                            @else
                                                <div style="font-size: 8pt; font-weight: bold;">SK</div>
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle; line-height: 1.1;">
                                            <div style="font-size: 6.5pt; font-weight: bold; text-transform: uppercase; color: #475569;">PEMERINTAH DAERAH PROVINSI</div>
                                            <div style="font-size: 8.5pt; font-weight: 900; text-transform: uppercase; color: #0f172a;">{{ $school->name }}</div>
                                            <div style="font-size: 6pt; color: #64748b;">{{ $cardType === 'exam' ? $examTitle : ('TAHUN PELAJARAN ' . $academicYear) }}</div>
                                        </td>
                                        <td style="width: 28px; text-align: right; vertical-align: middle; font-size: 10pt;">
                                            🏛️
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="background: #f1f5f9; text-align: center; padding: 1.5px 0; border-bottom: 1px solid #cbd5e1;">
                                <span style="font-size: 7.2pt; font-weight: 900; text-transform: uppercase; text-decoration: underline; letter-spacing: 0.5px;">
                                    {{ $cardType === 'exam' ? 'KARTU TANDA PESERTA UJIAN' : 'KARTU TANDA PELAJAR' }}
                                </span>
                            </div>

                            {{-- Body --}}
                            <div style="padding: 4px 8px; display: flex; gap: 8px; flex: 1; align-items: center;">
                                <div class="photo-box" style="border: 1.5px solid #0f172a; background: #fff1f2;">
                                    PAS FOTO<br>2 × 3 cm<br><span style="font-size: 5.5pt; color: #e11d48;">RESMI</span>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <table class="info-table">
                                        <tr>
                                            <td class="label">Nama Siswa</td>
                                            <td class="colon">:</td>
                                            <td class="value">{{ $s['name'] ?? 'Nama Siswa' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">NISN / NIK</td>
                                            <td class="colon">:</td>
                                            <td class="value" style="font-family: monospace;">{{ $s['nisn'] ?? '-' }} / {{ $s['nik'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label">Kelas / Jurusan</td>
                                            <td class="colon">:</td>
                                            <td class="value">{{ $s['class'] ?? '-' }}</td>
                                        </tr>
                                        @if ($cardType === 'exam')
                                            <tr>
                                                <td class="label">Sesi & Ruang</td>
                                                <td class="colon">:</td>
                                                <td class="value">{{ $s['session'] ?? 'Sesi 1' }} &bull; {{ $s['room'] ?? 'Ruang 1' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">User & Sandi</td>
                                                <td class="colon">:</td>
                                                <td class="value" style="font-family: monospace; color: #b91c1c;">
                                                    {{ $s['exam_user'] ?? $s['nisn'] }} / {{ $s['exam_pass'] ?? 'PASS26' }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="label">TTL</td>
                                                <td class="colon">:</td>
                                                <td class="value">{{ $s['birth_place'] ?? '-' }}, {{ $s['birth_date'] ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            {{-- Formal Signature Footer --}}
                            <div style="padding: 2px 8px; border-top: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: space-between; font-size: 5.8pt; background: #ffffff;">
                                <div style="font-family: monospace; color: #64748b;">
                                    *NPSN: {{ $school->npsn ?? 'RESMI' }}*
                                </div>
                                <div style="text-align: right; line-height: 1.1;">
                                    <span>Kepala Sekolah,</span><br>
                                    <strong style="font-size: 6.5pt;">{{ $headmasterName }}</strong><br>
                                    <span style="color: #64748b;">NIP. {{ $headmasterNip }}</span>
                                </div>
                            </div>

                        {{-- ================= TEMPLATE 3: CYBER CBT PRO ================= --}}
                        @else
                            {{-- CBT Tech Header --}}
                            <div style="padding: 4px 8px; background: #047857; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 10pt;">⚡</span>
                                    <div>
                                        <div style="font-size: 6.5pt; font-weight: 900; letter-spacing: 1px; color: #a7f3d0; text-transform: uppercase;">PORTAL CBT ONLINE</div>
                                        <div style="font-size: 8.5pt; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 50mm;">{{ $school->name }}</div>
                                    </div>
                                </div>
                                <div style="font-size: 6.5pt; font-weight: bold; background: rgba(0,0,0,0.3); padding: 2px 5px; border-radius: 3px;">
                                    TP {{ $academicYear }}
                                </div>
                            </div>

                            {{-- CBT Body with Prominent Login Box --}}
                            <div style="padding: 5px 8px; display: flex; gap: 8px; flex: 1; align-items: center; background: #f0fdf4;">
                                <div style="width: 22mm; height: 26mm; border: 1.5px solid #059669; background: #ffffff; border-radius: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 6.5pt; color: #047857;">
                                    {!! \App\Support\CardCodeGenerator::qrCodeSvg('CBT-' . ($s['nisn'] ?? '0081234567'), 50) !!}
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 8.5pt; font-weight: 900; color: #064e3b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $s['name'] ?? 'Nama Siswa' }}
                                    </div>
                                    <div style="font-size: 6.8pt; color: #047857; font-weight: bold; margin-bottom: 3px;">
                                        NISN: {{ $s['nisn'] ?? '-' }} &bull; {{ $s['class'] ?? '-' }}
                                    </div>

                                    {{-- High Contrast Box --}}
                                    <div style="background: #064e3b; color: #ffffff; padding: 3px 6px; border-radius: 4px; font-size: 6.8pt; font-family: monospace;">
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>USER: <strong>{{ $s['exam_user'] ?? $s['nisn'] }}</strong></span>
                                            <span>PASS: <strong style="color: #fde047;">{{ $s['exam_pass'] ?? 'CBT26' }}</strong></span>
                                        </div>
                                    </div>
                                    <div style="font-size: 6.2pt; color: #065f46; margin-top: 2px; font-weight: bold;">
                                        {{ $s['session'] ?? 'Sesi 1' }} &bull; {{ $s['room'] ?? 'Lab Komputer' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Tech Footer --}}
                            <div style="padding: 2px 8px; background: #064e3b; color: #d1fae5; display: flex; align-items: center; justify-content: space-between; font-size: 5.8pt; font-family: monospace;">
                                <span>STATUS: TERDAFTAR RESMI</span>
                                <span>{{ $headmasterName }}</span>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 50px;">
            <p>Tidak ada data siswa untuk dicetak.</p>
        </div>
    @endforelse

</body>
</html>
