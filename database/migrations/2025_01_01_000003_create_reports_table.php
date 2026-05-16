<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('scan_id')->constrained()->onDelete('cascade')->unique();
            $table->integer('accounts_found')->default(0);
            $table->integer('breaches_found')->default(0);
            $table->string('risk_level', 5)->nullable();
            $table->text('gravatar_url')->nullable();
            $table->jsonb('full_report');
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
