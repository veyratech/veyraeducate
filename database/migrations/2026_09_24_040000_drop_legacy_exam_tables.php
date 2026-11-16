<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'exam_answers',
            'exam_participants',
            'exam_session_questions',
            'exam_sessions',
            'questions',
            'subjects',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }

        if (Schema::hasColumn('plans', 'modules')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('modules');
            });
        }
    }

    public function down(): void
    {
        // Legacy exam schema intentionally removed; not restorable.
    }
};
