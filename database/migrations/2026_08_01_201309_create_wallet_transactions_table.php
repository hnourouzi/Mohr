<?php

use App\Enums\WalletTransactionType;
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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets');
            $table->integer('amount');
            $table->enum('type', WalletTransactionType::cases())->default(WalletTransactionType::Credit->value);
            $table->integer('balance_after');
            $table->string('reference_type');
            $table->foreignId('reference_id');
            $table->text('description');
            $table->timestamps();

            $table->index('wallet_id');
            $table->index('type');
            $table->index('reference_type');
            $table->index('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
