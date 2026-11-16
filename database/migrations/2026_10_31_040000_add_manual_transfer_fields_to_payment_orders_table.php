<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->string('proof_path')->nullable()->after('webhook_payload');
            $table->string('bank_sender')->nullable()->after('proof_path');
            $table->text('notes')->nullable()->after('bank_sender');
        });
    }

    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn(['proof_path', 'bank_sender', 'notes']);
        });
    }
};
