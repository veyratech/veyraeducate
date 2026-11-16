<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_requests', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('priority')->default('normal')->after('category');
            $table->date('target_date')->nullable()->after('priority');
            $table->text('additional_notes')->nullable()->after('target_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_requests', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority', 'target_date', 'additional_notes']);
        });
    }
};
