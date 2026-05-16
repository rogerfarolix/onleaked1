<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_caches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email_hash', 64)->unique()->index(); // sha256
            $table->string('email_domain', 191)->index();
            $table->integer('breaches_count')->default(0);
            $table->integer('accounts_count')->default(0);
            $table->string('risk_level', 5)->nullable();
            $table->jsonb('cached_data');
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_caches');
    }
};
