<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'name',
        'phone_number',
        'delivery_date',
        'status',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'collection_date' => 'datetime',
        'delivery_date' => 'date',
    ];

    /**
     * Get the student that owns the package
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'student_id');
    }

    /**
     * Check if package has been collected
     */
    public function isCollected(): bool
    {
        return $this->status === 'collected';
    }

    /**
     * Mark package as collected
     */
    public function markAsCollected(): void
    {
        $this->update([
            'status' => 'collected',
            'collection_date' => now(),
        ]);
    }
}
