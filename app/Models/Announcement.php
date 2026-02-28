<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'content',
        'image_path',
        'publish_at',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
        ];
    }

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
