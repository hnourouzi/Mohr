<?php

use App\Models\BlacklistWord;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('blacklist_words')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                $trimmed = BlacklistWord::normalizeWord((string) $row->word);

                if ($trimmed === (string) $row->word) {
                    return;
                }

                DB::table('blacklist_words')
                    ->where('id', $row->id)
                    ->update(['word' => $trimmed]);
            });
    }

    public function down(): void
    {
        // irreversible trim
    }
};
