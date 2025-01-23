<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function packages() {
        return $this->hasMany(Package::class);
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public static function getDistance($lat1, $lon1, $lat2, $lon2) {
        $r = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat/2) * sin($dLat/2) +
             cos($lat1) * cos($lat2) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $r * $c;

        return $distance;
    }

    public static function findNearestShop($latitude, $longitude, $radius = 1) {
        $shops = self::active()->get();
        $nearestShop = null;
        $shortestDistance = $radius;

        foreach ($shops as $shop) {
            $distance = self::getDistance(
                $latitude, 
                $longitude, 
                $shop->latitude, 
                $shop->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestShop = $shop;
            }
        }

        return $nearestShop;
    }
}
