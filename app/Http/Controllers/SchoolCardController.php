<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolCardController extends Controller
{
    /**
     * Display the Student & Exam Card Generator interface.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->first();

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

        // Sample students formatted for immediate testing
        $defaultStudents = [
            [
                'nisn' => '0081234567',
                'nik' => '3201010101080001',
                'name' => 'Ahmad Pratama Putra',
                'gender' => 'L',
                'class' => '10 IPA 1',
                'birth_place' => 'Jakarta',
                'birth_date' => '2008-05-14',
                'session' => 'Sesi 1 (07.30 - 09.30)',
                'room' => 'Lab Komputer 1',
                'exam_user' => '0081234567',
                'exam_pass' => 'PRATAMA26',
            ],
            [
                'nisn' => '0081234568',
                'nik' => '3201010101080002',
                'name' => 'Nurul Hidayah Zahra',
                'gender' => 'P',
                'class' => '10 IPA 1',
                'birth_place' => 'Bandung',
                'birth_date' => '2008-08-22',
                'session' => 'Sesi 1 (07.30 - 09.30)',
                'room' => 'Lab Komputer 1',
                'exam_user' => '0081234568',
                'exam_pass' => 'NURUL26',
            ],
            [
                'nisn' => '0081234569',
                'nik' => '3201010101080003',
                'name' => 'Bagas Arya Wicaksono',
                'gender' => 'L',
                'class' => '10 IPA 1',
                'birth_place' => 'Surabaya',
                'birth_date' => '2008-03-10',
                'session' => 'Sesi 1 (07.30 - 09.30)',
                'room' => 'Lab Komputer 1',
                'exam_user' => '0081234569',
                'exam_pass' => 'BAGAS26',
            ],
            [
                'nisn' => '0081234570',
                'nik' => '3201010101080004',
                'name' => 'Siti Aisyah Putri',
                'gender' => 'P',
                'class' => '10 IPA 2',
                'birth_place' => 'Semarang',
                'birth_date' => '2008-11-05',
                'session' => 'Sesi 2 (10.00 - 12.00)',
                'room' => 'Lab Komputer 2',
                'exam_user' => '0081234570',
                'exam_pass' => 'AISYAH26',
            ],
            [
                'nisn' => '0081234571',
                'nik' => '3201010101080005',
                'name' => 'Rizky Ramadhan',
                'gender' => 'L',
                'class' => '10 IPA 2',
                'birth_place' => 'Yogyakarta',
                'birth_date' => '2008-09-17',
                'session' => 'Sesi 2 (10.00 - 12.00)',
                'room' => 'Lab Komputer 2',
                'exam_user' => '0081234571',
                'exam_pass' => 'RIZKY26',
            ],
            [
                'nisn' => '0081234572',
                'nik' => '3201010101080006',
                'name' => 'Dewi Anggraini',
                'gender' => 'P',
                'class' => '10 IPA 2',
                'birth_place' => 'Malang',
                'birth_date' => '2008-07-29',
                'session' => 'Sesi 2 (10.00 - 12.00)',
                'room' => 'Lab Komputer 2',
                'exam_user' => '0081234572',
                'exam_pass' => 'DEWI26',
            ],
            [
                'nisn' => '0081234573',
                'nik' => '3201010101080007',
                'name' => 'Fajar Setiawan',
                'gender' => 'L',
                'class' => '10 IPS 1',
                'birth_place' => 'Solo',
                'birth_date' => '2008-02-14',
                'session' => 'Sesi 3 (13.00 - 15.00)',
                'room' => 'Lab Komputer 1',
                'exam_user' => '0081234573',
                'exam_pass' => 'FAJAR26',
            ],
            [
                'nisn' => '0081234574',
                'nik' => '3201010101080008',
                'name' => 'Cantika Maharani',
                'gender' => 'P',
                'class' => '10 IPS 1',
                'birth_place' => 'Denpasar',
                'birth_date' => '2008-12-01',
                'session' => 'Sesi 3 (13.00 - 15.00)',
                'room' => 'Lab Komputer 1',
                'exam_user' => '0081234574',
                'exam_pass' => 'CANTIKA26',
            ],
        ];

        if (! empty($school?->meta['card_students']) && is_array($school->meta['card_students'])) {
            $defaultStudents = $school->meta['card_students'];
        }

        return view('school.cards.index', compact('school', 'user', 'defaultStudents'));
    }

    /**
     * Render the print-ready A4 cards view.
     */
    public function print(Request $request): View
    {
        $user = $request->user();
        $school = $user->school()->first() ?? School::where('owner_user_id', $user->id)->first();

        if (! $school) {
            $school = School::create([
                'owner_user_id' => $user->id,
                'name' => 'Sekolah '.$user->name,
                'email' => $user->email,
            ]);
            $user->forceFill(['school_id' => $school->id])->save();
        }

        $cardType = $request->input('card_type', 'exam'); // 'exam' or 'student'
        $cardTemplate = $request->input('card_template', 'modern'); // 'modern', 'formal', 'cbt'
        $cardsPerPage = in_array((int) $request->input('cards_per_page'), [8, 10], true) ? (int) $request->input('cards_per_page') : 8;
        $examTitle = $request->input('exam_title', 'ASESMEN SUMATIF AKHIR SEMESTER (SAS)');
        $academicYear = $request->input('academic_year', '2026/2027');
        $headmasterName = $request->input('headmaster_name', $school->meta['headmaster_name'] ?? 'Kepala Sekolah');
        $headmasterNip = $request->input('headmaster_nip', $school->meta['headmaster_nip'] ?? '19780512 200312 1 002');
        $cardTheme = $request->input('card_theme', 'brand'); // brand, emerald, gold, slate

        $rawStudents = $request->input('students_json');
        $students = [];

        if ($rawStudents && is_string($rawStudents)) {
            $decoded = json_decode($rawStudents, true);
            if (is_array($decoded)) {
                $students = $decoded;
            }
        }

        // If CSV file was uploaded directly
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (! empty($lines)) {
                $header = str_getcsv(array_shift($lines));
                foreach ($lines as $line) {
                    $row = str_getcsv($line);
                    if (count($row) >= 3) {
                        $students[] = [
                            'nisn' => trim($row[0] ?? ''),
                            'nik' => trim($row[1] ?? ''),
                            'name' => trim($row[2] ?? 'Nama Siswa'),
                            'gender' => trim($row[3] ?? 'L'),
                            'class' => trim($row[5] ?? $row[4] ?? 'Kelas'),
                            'birth_place' => trim($row[6] ?? ''),
                            'birth_date' => trim($row[7] ?? ''),
                            'session' => 'Sesi 1',
                            'room' => 'Ruang 1',
                            'exam_user' => trim($row[0] ?? 'USER'),
                            'exam_pass' => strtoupper(substr(md5(trim($row[0] ?? 'PASS')), 0, 6)),
                        ];
                    }
                }
            }
        }

        if ($school && ! empty($students)) {
            $meta = $school->meta ?? [];
            $meta['card_students'] = array_slice($students, 0, 500);
            $meta['headmaster_name'] = $headmasterName;
            $meta['headmaster_nip'] = $headmasterNip;
            $school->update(['meta' => $meta]);
        } elseif (empty($students) && ! empty($school->meta['card_students']) && is_array($school->meta['card_students'])) {
            $students = $school->meta['card_students'];
        }

        return view('school.cards.print', compact(
            'school',
            'students',
            'cardType',
            'cardTemplate',
            'cardsPerPage',
            'examTitle',
            'academicYear',
            'headmasterName',
            'headmasterNip',
            'cardTheme'
        ));
    }
}
