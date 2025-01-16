<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Present extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'price',
        'marker_id',
        'img'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marker()
    {
        return $this->belongsTo(Marker::class);
    }
}