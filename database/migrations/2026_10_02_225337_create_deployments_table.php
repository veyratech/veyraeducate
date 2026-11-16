<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_app_id')->constrained('web_apps')->onDelete('cascade');
            $table->string('slug');
            $table->string('version')->nullable();
            $table->string('status')->default('pending');
            $table->string('source_file')->nullable();
            $table->integer('file_count')->default(0);
            $table->integer('size_bytes')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
