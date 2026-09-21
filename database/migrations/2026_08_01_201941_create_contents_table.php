<?php

use App\Enums\ContentStatus;
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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->string('channel')->nullable(); 
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('status', ContentStatus::cases())->default(ContentStatus::Pending->value)->index();
            $table->string('rejection_reason')->nullable();
            $table->json('ai_categories')->nullable();
            $table->json('ai_raw_response')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
