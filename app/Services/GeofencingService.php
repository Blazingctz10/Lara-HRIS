<?php

namespace App\Services;

class GeofencingService
{
    /**
     * Status aktif/nonaktif fitur pembatasan radius GPS Geofencing.
     * Dinonaktifkan dahulu sesuai kebijakan operasional bank.
     */
    protected static ?bool $enabled = null;

    public static function isEnabled(): bool
    {
        if (static::$enabled !== null) {
            return static::$enabled;
        }

        return (bool) env('GEOFENCING_ENABLED', false);
    }

    public static function setEnabled(bool $state): void
    {
        static::$enabled = $state;
    }

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
        $isGeofenceActive = self::isEnabled();

        // Jika geofencing dinonaktifkan, selalu valid (bebas lokasi)
        $isWithin = $isGeofenceActive ? ($distance <= $allowedRadiusMeters) : true;

        return [
            'distance_meters' => $distance,
            'is_valid' => $isWithin,
            'allowed_radius' => $allowedRadiusMeters,
            'geofencing_enabled' => $isGeofenceActive,
        ];
    }
}
