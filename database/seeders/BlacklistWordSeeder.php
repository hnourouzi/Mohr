<?php

namespace Database\Seeders;

use App\Models\BlacklistWord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlacklistWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlacklistWord::factory()->count(100)->create();
    }
}
