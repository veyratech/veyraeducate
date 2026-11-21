<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\School;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class CbtStatsService
{
    public function __construct(
        private readonly SsoService $ssoService
    ) {}

    /**
     * Ambil statistik ujian & siswa dari CBT Online untuk sekolah terkait.
     *
     * @return array{
     *     synced: bool,
     *     source: string,
     *     tenant_found: bool,
     *     tenant_name: string,
     *     package: string,
     *     students_active: int,
     *     students_total: int,
     *     student_quota: int,
     *     quota_percentage: int,
     *     classes_count: int,
     *     teachers_count: int,
     *     subjects_count: int,
     *     question_banks_count: int,
     *     questions_count: int,
     *     active_exams_count: int,
     *     ongoing_sessions_count: int,
     *     completed_sessions_count: int,
     *     active_exams: array<int, array<string, mixed>>,
     *     synced_at: string
     * }
     */
    public function getSchoolStats(?School $school, bool $forceRefresh = false): array
    {
        $schoolId = $school?->id ?? 0;
        $cacheKey = "cbt_stats_school_{$schoolId}";

        if ($forceRefresh) {
            try {
                Cache::forget($cacheKey);
            } catch (Throwable) {
                // Ignore cache driver issues
            }
        }

        try {
            return Cache::remember($cacheKey, 60, fn (): array => $this->fetchLiveStats($school));
        } catch (Throwable) {
            return $this->fetchLiveStats($school);
        }
    }

    /**
     * Tarik data langsung dari endpoint API CBT Online atau fallback database lokal/server.
     *
     * @return array<string, mixed>
     */
    protected function fetchLiveStats(?School $school): array
    {
        $fromApi = $this->fetchViaHttpApi($school);
        if ($fromApi !== null) {
            return $fromApi;
        }

        $fromDb = $this->fetchViaDirectDatabase($school);
        if ($fromDb !== null) {
            return $fromDb;
        }

        return $this->defaultStats($school);
    }

    /**
     * Panggil API /api/sso.php?action=tenant_stats pada server CBT Online.
     *
     * @return array<string, mixed>|null
     */
    protected function fetchViaHttpApi(?School $school): ?array
    {
        try {
            $cbtUrl = rtrim($this->ssoService->getCbtUrl(), '/');
            if ($cbtUrl === '' || $school === null) {
                return null;
            }

            $secretKey = $this->ssoService->getSecretKey();
            $payload = [
                'action' => 'tenant_stats',
                'school_id' => $school->id,
                'school_code' => $school->npsn ?: ('SCH'.$school->id),
                'school_slug' => Str::slug($school->name ?: ('sekolah-'.$school->id)),
                'school_name' => $school->name,
            ];

            $timeout = app()->environment('testing') ? 0.5 : 2.5;
            $response = Http::timeout($timeout)
                ->withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-SSO-Secret' => $secretKey,
                ])
                ->post("{$cbtUrl}/api/sso.php", $payload);

            if (! $response->successful()) {
                return null;
            }

            $json = $response->json();
            if (! is_array($json) || empty($json['success']) || ! is_array($json['data'] ?? null)) {
                return null;
            }

            return $this->normalizeStats($json['data'], $school, 'api');
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Fallback koneksi database langsung jika CBT berada pada server MySQL yang sama.
     *
     * @return array<string, mixed>|null
     */
    protected function fetchViaDirectDatabase(?School $school): ?array
    {
        if (app()->environment('testing')) {
            return null;
        }

        $host = (string) config('database.connections.mysql.host', '127.0.0.1');
        $port = (string) config('database.connections.mysql.port', '3306');
        $username = (string) config('database.connections.mysql.username', 'root');
        $password = (string) config('database.connections.mysql.password', '');

        $candidateDbs = array_values(array_unique(array_filter([
            (string) $this->readEnvValue('CBT_DB_DATABASE'),
            app()->environment('production') ? 'u77oazzk_cbtonline' : 'cbtonline_db',
            'cbtonline_db',
        ])));

        foreach ($candidateDbs as $dbName) {
            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 2,
                ]);

                $tenant = $this->resolveTenantFromPdo($pdo, $school);
                if ($tenant === null) {
                    return $this->normalizeStats(['tenant_found' => false], $school, 'database');
                }

                $tid = (int) $tenant['id'];

                $studentsActive = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM students WHERE tenant_id = ? AND is_active = 1', [$tid]);
                $studentsTotal = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM students WHERE tenant_id = ?', [$tid]);
                $classesCount = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM classes WHERE tenant_id = ?', [$tid]);
                $teachersCount = (int) $this->scalar($pdo, "SELECT COUNT(*) FROM users WHERE tenant_id = ? AND role IN ('admin', 'teacher', 'proctor') AND is_active = 1", [$tid]);
                $subjectsCount = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM subjects WHERE tenant_id = ?', [$tid]);
                $banksCount = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM question_banks WHERE tenant_id = ?', [$tid]);
                $questionsCount = (int) $this->scalar(
                    $pdo,
                    'SELECT COUNT(*) FROM questions q JOIN question_banks qb ON q.bank_id = qb.id WHERE qb.tenant_id = ?',
                    [$tid]
                );
                $activeExamsCount = (int) $this->scalar($pdo, 'SELECT COUNT(*) FROM exams WHERE tenant_id = ? AND is_active = 1', [$tid]);
                $ongoingSessionsCount = (int) $this->scalar($pdo, "SELECT COUNT(*) FROM exam_sessions WHERE tenant_id = ? AND status = 'in_progress'", [$tid]);
                $completedSessionsCount = (int) $this->scalar($pdo, "SELECT COUNT(*) FROM exam_sessions WHERE tenant_id = ? AND status = 'completed'", [$tid]);

                $stmt = $pdo->prepare(
                    "SELECT e.id, e.title, e.token, e.duration_minutes, e.start_time, e.end_time, e.is_active,
                            qb.title AS bank_title,
                            s.name AS subject_name,
                            COALESCE(c.name, 'Semua Kelas') AS class_name,
                            (SELECT COUNT(*) FROM exam_sessions es WHERE es.exam_id = e.id AND es.status = 'in_progress') AS in_progress_count,
                            (SELECT COUNT(*) FROM exam_sessions es WHERE es.exam_id = e.id AND es.status = 'completed') AS completed_count
                     FROM exams e
                     JOIN question_banks qb ON e.bank_id = qb.id
                     JOIN subjects s ON qb.subject_id = s.id
                     LEFT JOIN classes c ON e.class_id = c.id
                     WHERE e.tenant_id = ? AND e.is_active = 1
                     ORDER BY e.start_time ASC, e.id DESC
                     LIMIT 5"
                );
                $stmt->execute([$tid]);
                $activeExams = $stmt->fetchAll() ?: [];

                return $this->normalizeStats([
                    'tenant_found' => true,
                    'tenant' => [
                        'id' => $tid,
                        'code' => $tenant['code'] ?? '',
                        'slug' => $tenant['slug'] ?? '',
                        'name' => $tenant['name'] ?? '',
                        'package' => $tenant['package'] ?? 'semester',
                        'student_quota' => (int) ($tenant['student_quota'] ?? 500),
                    ],
                    'students_active' => $studentsActive,
                    'students_total' => $studentsTotal,
                    'student_quota' => (int) ($tenant['student_quota'] ?? 500),
                    'classes_count' => $classesCount,
                    'teachers_count' => $teachersCount,
                    'subjects_count' => $subjectsCount,
                    'question_banks_count' => $banksCount,
                    'questions_count' => $questionsCount,
                    'active_exams_count' => $activeExamsCount,
                    'ongoing_sessions_count' => $ongoingSessionsCount,
                    'completed_sessions_count' => $completedSessionsCount,
                    'active_exams' => $activeExams,
                    'synced_at' => now()->toIso8601String(),
                ], $school, 'database');
            } catch (Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * Resolusi tenant pada database CBT menggunakan strategi multi-match.
     *
     * @return array<string, mixed>|null
     */
    private function resolveTenantFromPdo(PDO $pdo, ?School $school): ?array
    {
        if (! $school) {
            return null;
        }

        $schoolId = (int) $school->id;
        $schoolCode = trim((string) ($school->npsn ?? ''));
        $schoolName = trim((string) ($school->name ?? ''));
        $schoolSlug = Str::slug($schoolName ?: ('sekolah-'.$schoolId));

        if ($schoolCode !== '') {
            $stmt = $pdo->prepare('SELECT * FROM tenants WHERE code = ? AND is_active = 1 LIMIT 1');
            $stmt->execute([$schoolCode]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        }

        if ($schoolSlug !== '') {
            $stmt = $pdo->prepare('SELECT * FROM tenants WHERE slug = ? AND is_active = 1 LIMIT 1');
            $stmt->execute([$schoolSlug]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        }

        if ($schoolName !== '') {
            $stmt = $pdo->prepare('SELECT * FROM tenants WHERE LOWER(name) = LOWER(?) AND is_active = 1 LIMIT 1');
            $stmt->execute([$schoolName]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        }

        if ($schoolId > 0) {
            $stmt = $pdo->prepare('SELECT * FROM tenants WHERE id = ? AND is_active = 1 LIMIT 1');
            $stmt->execute([$schoolId]);
            $row = $stmt->fetch();
            if ($row) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $params
     */
    private function scalar(PDO $pdo, string $sql, array $params = []): mixed
    {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /**
     * Normalisasi output statistik CBT agar aman dikonsumsi oleh Blade.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeStats(array $raw, ?School $school, string $source): array
    {
        $studentsActive = max(0, (int) ($raw['students_active'] ?? 0));
        $studentsTotal = max($studentsActive, (int) ($raw['students_total'] ?? 0));
        $quota = (int) ($raw['student_quota'] ?? ($raw['tenant']['student_quota'] ?? 500));
        if ($quota <= 0) {
            $quota = 1000;
        }

        $quotaPercentage = min(100, (int) round(($studentsActive / max(1, $quota)) * 100));

        $activeExams = [];
        if (is_array($raw['active_exams'] ?? null)) {
            foreach ($raw['active_exams'] as $exam) {
                if (! is_array($exam)) {
                    continue;
                }
                $activeExams[] = [
                    'id' => (int) ($exam['id'] ?? 0),
                    'title' => (string) ($exam['title'] ?? 'Ujian CBT'),
                    'token' => (string) ($exam['token'] ?? '-'),
                    'duration_minutes' => (int) ($exam['duration_minutes'] ?? 60),
                    'start_time' => $exam['start_time'] ?? null,
                    'end_time' => $exam['end_time'] ?? null,
                    'subject_name' => (string) ($exam['subject_name'] ?? 'Mata Pelajaran'),
                    'class_name' => (string) ($exam['class_name'] ?? 'Semua Kelas'),
                    'in_progress_count' => (int) ($exam['in_progress_count'] ?? 0),
                    'completed_count' => (int) ($exam['completed_count'] ?? 0),
                ];
            }
        }

        return [
            'synced' => true,
            'source' => $source,
            'tenant_found' => (bool) ($raw['tenant_found'] ?? false),
            'tenant_name' => (string) ($raw['tenant']['name'] ?? $school?->name ?? 'Sekolah'),
            'package' => (string) ($raw['tenant']['package'] ?? $school?->activeSubscription?->plan?->code ?? 'semester'),
            'students_active' => $studentsActive,
            'students_total' => $studentsTotal,
            'student_quota' => $quota,
            'quota_percentage' => $quotaPercentage,
            'classes_count' => max(0, (int) ($raw['classes_count'] ?? 0)),
            'teachers_count' => max(0, (int) ($raw['teachers_count'] ?? 0)),
            'subjects_count' => max(0, (int) ($raw['subjects_count'] ?? 0)),
            'question_banks_count' => max(0, (int) ($raw['question_banks_count'] ?? 0)),
            'questions_count' => max(0, (int) ($raw['questions_count'] ?? 0)),
            'active_exams_count' => max(0, (int) ($raw['active_exams_count'] ?? count($activeExams))),
            'ongoing_sessions_count' => max(0, (int) ($raw['ongoing_sessions_count'] ?? 0)),
            'completed_sessions_count' => max(0, (int) ($raw['completed_sessions_count'] ?? 0)),
            'active_exams' => $activeExams,
            'synced_at' => (string) ($raw['synced_at'] ?? now()->toIso8601String()),
        ];
    }

    /**
     * Default struktur aman ketika server CBT belum tersinkronisasi.
     *
     * @return array<string, mixed>
     */
    public function defaultStats(?School $school): array
    {
        return [
            'synced' => false,
            'source' => 'fallback',
            'tenant_found' => false,
            'tenant_name' => $school?->name ?? 'Sekolah',
            'package' => $school?->activeSubscription?->plan?->code ?? 'semester',
            'students_active' => 0,
            'students_total' => 0,
            'student_quota' => 500,
            'quota_percentage' => 0,
            'classes_count' => 0,
            'teachers_count' => 0,
            'subjects_count' => 0,
            'question_banks_count' => 0,
            'questions_count' => 0,
            'active_exams_count' => 0,
            'ongoing_sessions_count' => 0,
            'completed_sessions_count' => 0,
            'active_exams' => [],
            'synced_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Read an environment variable directly from .env file, bypassing Laravel's env()
     * helper which returns null when config cache is enabled.
     */
    protected function readEnvValue(string $key): ?string
    {
        $path = base_path('.env');
        if (! is_file($path)) {
            return null;
        }

        foreach (file($path) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (preg_match('/^'.preg_quote($key, '/').'\s*=\s*(.+)/', $line, $matches)) {
                $value = trim($matches[1], '"\'');

                return $value !== '' ? $value : null;
            }
        }

        return null;
    }
}
