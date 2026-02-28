<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'user_id',
        'student_no',
        'full_name',
        'grade_level',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function application() { return $this->belongsTo(Application::class); }
}
