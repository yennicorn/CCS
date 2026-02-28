<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_active',
        'enrollment_open',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'enrollment_open' => 'boolean',
        ];
    }

    public function applications() { return $this->hasMany(Application::class); }
}
