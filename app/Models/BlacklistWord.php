<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Enums\BlacklistSeverity;

#[Fillable(['word', 'severity'])]
class BlacklistWord extends Model
{
    /** @use HasFactory<\Database\Factories\BlacklistWordFactory> */
    use HasFactory;

    protected $casts = [
        'severity' => BlacklistSeverity::class,
    ];
}
