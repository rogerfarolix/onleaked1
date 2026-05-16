<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('scan_id')->constrained()->onDelete('cascade');
            $table->string('source', 50)->index();
            $table->jsonb('raw_data');
            $table->jsonb('parsed_data')->nullable();
            $table->string('risk_score', 5)->nullable();
            $table->timestamp('fetched_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_results');
    }
};
