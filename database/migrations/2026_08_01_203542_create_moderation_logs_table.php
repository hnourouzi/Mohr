<?php

use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
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
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents');
            $table->enum('layer', ModerationLayer::cases())->default(ModerationLayer::Layer1Blacklist->value);
            $table->enum('result', ModerationResult::cases())->default(ModerationResult::Passed->value);
            $table->text('detail')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_logs');
    }
};
