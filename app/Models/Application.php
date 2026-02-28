<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_year_id',
        'learner_full_name',
        'grade_level',
        'gender',
        'status',
        'submitted_at',
        'reviewed_at',
        'finalized_at',
        'reviewed_by',
        'finalized_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'finalized_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function schoolYear() { return $this->belongsTo(SchoolYear::class); }
    public function documents() { return $this->hasMany(Document::class); }
    public function statusLogs() { return $this->hasMany(ApplicationStatusLog::class); }
}
