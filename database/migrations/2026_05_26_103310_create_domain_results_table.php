<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('domain_results', function (Blueprint $table) {
            $table->id();
            $table->uuid('share_uuid')->unique();
            $table->string('domain');
            $table->json('results');
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index('share_uuid');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_results');
    }
};
