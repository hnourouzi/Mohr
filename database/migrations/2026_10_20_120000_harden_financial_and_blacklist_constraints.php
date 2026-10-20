<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->string('type', 20)->default('credit')->change();
                $table->unsignedBigInteger('reference_id')->nullable()->change();
                $table->string('reference_type')->nullable()->change();
            });

            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->unique(
                    ['reference_type', 'reference_id', 'type'],
                    'wallet_transactions_reference_type_unique'
                );
            });
        }

        if (Schema::hasTable('wallets')) {
            $duplicateUserIds = DB::table('wallets')
                ->select('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('user_id');

            if ($duplicateUserIds->isNotEmpty()) {
                throw new RuntimeException(
                    'Duplicate wallets detected for user_id(s): '.$duplicateUserIds->implode(', ').
                    '. Reconcile manually before running this migration.'
                );
            }

            Schema::table('wallets', function (Blueprint $table) {
                $table->unique('user_id');
            });
        }

        if (Schema::hasTable('blacklist_words')) {
            $duplicateWords = DB::table('blacklist_words')
                ->select('word')
                ->groupBy('word')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('word');

            if ($duplicateWords->isNotEmpty()) {
                throw new RuntimeException(
                    'Duplicate blacklist words detected: '.$duplicateWords->implode(', ').
                    '. Remove duplicates before running this migration.'
                );
            }

            Schema::table('blacklist_words', function (Blueprint $table) {
                $table->unique('word');
            });
        }

        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contents')) {
            Schema::table('contents', function (Blueprint $table) {
                $table->dropIndex(['user_id', 'created_at']);
            });
        }

        if (Schema::hasTable('blacklist_words')) {
            Schema::table('blacklist_words', function (Blueprint $table) {
                $table->dropUnique(['word']);
            });
        }

        if (Schema::hasTable('wallets')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->dropUnique(['user_id']);
            });
        }

        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->dropUnique('wallet_transactions_reference_type_unique');
            });
        }
    }
};
