<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class SchoolYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'year',
        'is_active',
        'is_locked',
        'is_enrollment_open',
        'enrollment_start_at',
        'enrollment_end_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_locked' => 'boolean',
            'is_enrollment_open' => 'boolean',
            'enrollment_open' => 'boolean',
            'enrollment_start_at' => 'datetime',
            'enrollment_end_at' => 'datetime',
        ];
    }

    public function getEnrollmentOpenAttribute(): bool
    {
        return (bool) ($this->attributes['is_enrollment_open'] ?? false);
    }

    public function setEnrollmentOpenAttribute(bool $value): void
    {
        if (Schema::hasColumn($this->getTable(), 'is_enrollment_open')) {
            $this->attributes['is_enrollment_open'] = $value;
            return;
        }

        $this->attributes['enrollment_open'] = $value;
    }

    public function isEnrollmentOpenNow(): bool
    {
        if (!$this->is_active || !$this->isEnrollmentSwitchOpen()) {
            return false;
        }

        $now = now();
        $startsOk = !$this->enrollment_start_at || $now->greaterThanOrEqualTo($this->enrollment_start_at);
        $endsOk = !$this->enrollment_end_at || $now->lessThanOrEqualTo($this->enrollment_end_at);

        return $startsOk && $endsOk;
    }

    public function hasEnrolledStudents(): bool
    {
        return $this->applications()->where('status', 'approved')->exists();
    }

    public function isEnrollmentSwitchOpen(): bool
    {
        if (array_key_exists('is_enrollment_open', $this->attributes)) {
            return (bool) $this->attributes['is_enrollment_open'];
        }

        return (bool) ($this->attributes['enrollment_open'] ?? false);
    }

    public function applications() { return $this->hasMany(Application::class); }
}
