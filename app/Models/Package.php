<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\TelegramService;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'name',
        'phone_number',
        'delivery_date',
        'daily_number',
        'status',
        'shop_id',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'collection_date' => 'datetime',
        'delivery_date' => 'date',
    ];

    /**
     * Get the shop that owns the package
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

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

    /**
     * Get the discard date for the package
     *
     * @return string
     */
    public function getDiscardDateAttribute()
    {
        return Carbon::parse($this->delivery_date)->addWeek();
    }

    /**
     * Check if package should be marked as discarded
     */
    public function checkDiscardStatus(): void
    {
        if ($this->status === 'pending' && now()->isAfter($this->discard_date)) {
            $this->update(['status' => 'discarded']);
            
            // Send discard notification
            try {
                app(TelegramService::class)->sendDiscardNotification($this);
            } catch (\Exception $e) {
                \Log::error('Failed to send discard notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get packages that should be marked as discarded
     */
    public static function markDiscardedPackages(): void
    {
        try {
            $packagesToDiscard = static::where('status', 'pending')
                ->where('delivery_date', '<=', now()->subWeek())
                ->get();

            foreach ($packagesToDiscard as $package) {
                $package->update(['status' => 'discarded']);
                
                // Send discard notification
                try {
                    app(TelegramService::class)->sendDiscardNotification($package);
                } catch (\Exception $e) {
                    \Log::error('Failed to send discard notification for package ' . $package->id . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error marking packages as discarded: ' . $e->getMessage());
        }
    }
}
