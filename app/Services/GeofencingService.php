<?php

namespace App\Services;

class GeofencingService
{
    /**
     * Calculate distance between two GPS coordinates using Haversine formula (in meters)
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return (int) round($angle * $earthRadius);
    }

    /**
     * Check if coordinates are within the branch radius
     */
    public static function isWithinRadius(float $lat1, float $lon1, float $branchLat, float $branchLon, int $allowedRadiusMeters): array
    {
        $distance = self::calculateDistance($lat1, $lon1, $branchLat, $branchLon);
        $isWithin = $distance <= $allowedRadiusMeters;

        return [
            'distance_meters' => $distance,
            'is_valid' => $isWithin,
            'allowed_radius' => $allowedRadiusMeters,
        ];
    }
}
