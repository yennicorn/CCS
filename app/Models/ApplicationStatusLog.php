<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'changed_by',
        'status',
        'remarks',
        'changed_at',
    ];

    protected function casts(): array
    {
        return ['changed_at' => 'datetime'];
    }

    public function application() { return $this->belongsTo(Application::class); }
    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }
}
