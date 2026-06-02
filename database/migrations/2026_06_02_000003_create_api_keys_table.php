<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('key', 64)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('key');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
