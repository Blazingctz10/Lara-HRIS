<?php

namespace App\Filament\Portal\Pages;

use App\Models\Attendance;
use App\Services\GeofencingService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class QuickAttendance extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationLabel = 'Presensi Backup / Dinas Luar';

    protected static ?string $title = 'Presensi Mandiri (Backup Fingerprint / Dinas Luar)';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.portal.pages.quick-attendance';

    public ?float $userLat = null;
    public ?float $userLong = null;
    public ?int $distanceMeters = null;
    public bool $isWithinGeofence = true;
    public ?string $photoData = null; // optional selfie
    public ?string $notes = '';

    public ?Attendance $todayAttendance = null;

    public function mount(): void
    {
        $this->refreshTodayAttendance();
    }

    public function refreshTodayAttendance(): void
    {
        $employee = auth()->user()->employee;
        if ($employee) {
            $this->todayAttendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', now())
                ->first();
        }
    }

    public function updateCoordinates(float $lat, float $long): void
    {
        $this->userLat = $lat;
        $this->userLong = $long;

        $employee = auth()->user()->employee;
        if (! $employee || ! $employee->branch) {
            $this->isWithinGeofence = true;
            return;
        }

        $branch = $employee->branch;
        if ($branch->latitude && $branch->longitude) {
            $result = GeofencingService::isWithinRadius(
                $lat,
                $long,
                (float) $branch->latitude,
                (float) $branch->longitude,
                $branch->radius_meters ?? 50
            );

            $this->distanceMeters = $result['distance_meters'];
            $this->isWithinGeofence = $result['is_valid'];
        } else {
            $this->distanceMeters = 0;
            $this->isWithinGeofence = true;
        }
    }

    public function clockIn(?string $photoBase64 = null, ?float $lat = null, ?float $long = null): void
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            Notification::make()->title('Profil pegawai tidak ditemukan.')->danger()->send();
            return;
        }

        if ($lat && $long) {
            $this->updateCoordinates($lat, $long);
        }

        $photoPath = null;
        if ($photoBase64 && str_starts_with($photoBase64, 'data:image')) {
            $imageParts = explode(';base64,', $photoBase64);
            $imageTypeAux = explode('image/', $imageParts[0]);
            $imageType = $imageTypeAux[1] ?? 'png';
            $imageBase64 = base64_decode($imageParts[1]);

            $fileName = 'attendances/selfies/' . $employee->nip . '_' . date('Ymd_His') . '.' . $imageType;
            Storage::disk('public')->put($fileName, $imageBase64);
            $photoPath = $fileName;
        }

        $now = Carbon::now('Asia/Jakarta');
        $isLate = $now->gt(Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta')); // 08:00 cutoff
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            $attendance->update([
                'clock_in' => $attendance->clock_in ?: $now->toTimeString(),
                'status' => $attendance->clock_in ? $attendance->status : ($isLate ? 'Late' : 'Present'),
                'lat_in' => $this->userLat ?? $attendance->lat_in,
                'long_in' => $this->userLong ?? $attendance->long_in,
                'distance_in_meters' => $this->distanceMeters ?? $attendance->distance_in_meters,
                'proof_photo' => $photoPath ?? $attendance->proof_photo,
                'notes' => $this->notes ?: $attendance->notes,
            ]);
        } else {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'clock_in' => $now->toTimeString(),
                'status' => $isLate ? 'Late' : 'Present',
                'lat_in' => $this->userLat,
                'long_in' => $this->userLong,
                'distance_in_meters' => $this->distanceMeters,
                'proof_photo' => $photoPath,
                'notes' => $this->notes ?: ($isLate ? 'Hadir (Terlambat)' : 'Hadir Tepat Waktu'),
            ]);
        }

        $this->refreshTodayAttendance();

        Notification::make()
            ->title('Presensi Masuk Berhasil Tercatat!')
            ->body('Jam masuk: ' . $now->format('H:i') . ($isLate ? ' (Terlambat)' : ' (Tepat Waktu)'))
            ->success()
            ->send();
    }

    public function clockOut(?float $lat = null, ?float $long = null): void
    {
        $employee = auth()->user()->employee;
        if (! $employee) {
            return;
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', Carbon::today('Asia/Jakarta')->toDateString())
            ->first();

        if (! $attendance) {
            Notification::make()->title('Anda belum melakukan presensi masuk hari ini!')->warning()->send();
            return;
        }

        $now = Carbon::now('Asia/Jakarta');
        $attendance->update([
            'clock_out' => $now->toTimeString(),
            'lat_out' => $lat ?? $this->userLat,
            'long_out' => $long ?? $this->userLong,
        ]);

        $this->refreshTodayAttendance();

        Notification::make()
            ->title('Presensi Pulang Berhasil!')
            ->body('Jam pulang: ' . $now->format('H:i'))
            ->success()
            ->send();
    }
}
