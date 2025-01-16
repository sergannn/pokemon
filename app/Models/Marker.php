<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'lat1',
        'lon1',
        'lat2',
        'lon2',
        'lat3',
        'lon3',
        'lat4',
        'lon4',
        'lat5',
        'lon5',
        'user_id',
        'status',
        'present_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function present()
    {
        return $this->belongsTo(Present::class);
    }
    public function updateStatus($newStatus)
    {
        if (!in_array($newStatus, ['available', 'used', 'archived'])) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $newStatus;
        $this->save();

        return $this;
    }
}