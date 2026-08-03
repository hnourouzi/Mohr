<?php

use App\Enums\BlacklistSeverity;
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
        Schema::create('blacklist_words', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->enum('severity', BlacklistSeverity::cases())->default(BlacklistSeverity::Flag->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_words');
    }
};
