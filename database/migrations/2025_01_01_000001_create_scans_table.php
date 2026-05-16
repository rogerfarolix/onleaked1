<?php
// ============================================================
// MIGRATION 1: create_scans_table
// ============================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('uuid', 36)->unique()->index();
            $table->string('email_target', 191)->index();
            $table->string('contact_email', 191)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('from_cache')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
