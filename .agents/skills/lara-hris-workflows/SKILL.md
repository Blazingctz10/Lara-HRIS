---
name: lara-hris-workflows
description: >-
  Panduan prosedur kerja, runbook testing, sinkronisasi model pegawai, dan pengelolaan
  aplikasi Core HRIS PT BPR Banda Raya (Lara-HRIS). Gunakan skill ini saat menjalankan tes,
  memperbarui data master, mengonfigurasi Git, atau membuat modul baru.
---

# Runbook & Prosedur Kerja Lara-HRIS

Panduan ini berisi langkah-langkah praktis dan operasional untuk mengelola dan mengembangkan sistem **Core HRIS PT BPR Banda Raya**.

---

## 1. Struktur Direktori & Lingkungan Eksekusi

```text
d:\File Magang\Codeworks\Lara-HRIS\
├── start.bat                     # Console menu interaktif (log live & tombol matikan server)
├── run.bat                       # Alias cepat menjalankan start.bat
├── Pass.txt                      # Catatan akun default pengujian
└── hris-system\                  # Root aplikasi Laravel & Filament
    ├── app\
    │   ├── Filament\             # Backoffice Admin Panel (/admin)
    │   ├── Filament\Portal\      # Employee Self-Service Panel (/portal)
    │   ├── Models\               # Eloquent Models & Lifecycle Observers
    │   └── Support\              # HrisErrorCode.php (Domain error dictionary)
    ├── resources\views\
    │   ├── auth\                 # welcome.blade.php (Split-screen login)
    │   ├── errors\               # Custom layout & template error M3
    │   └── filament\             # m3-theme.blade.php & components
    └── tests\                    # 19 Feature & Unit Tests (133 assertions)
```

> [!IMPORTANT]
> **Repositori Git:** Terletak di dalam folder `d:\File Magang\Codeworks\Lara-HRIS\hris-system` (bukan di root folder pembungkus).
> Remote origin: `https://github.com/Blazingctz10/Lara-HRIS.git`
> Branch utama: `main`

---

## 2. Alur Pengujian & Validasi Sistem (Testing)

Sebelum melakukan push atau setelah melakukan perubahan kode, selalu jalankan pengujian:

```powershell
cd "d:\File Magang\Codeworks\Lara-HRIS\hris-system"
php artisan test
```

### Checklist Pengujian yang Harus Lulus 100%:
1. `Tests\Unit\ExampleTest`
2. `Tests\Feature\ComprehensiveResourceTest` (Memvalidasi seluruh tabel & halaman admin dan portal)
3. `Tests\Feature\HrisTest` (Relasi model, perhitungan geofencing GPS, deduction saldo cuti, scope pengumuman, dan update profil pegawai)
4. `Tests\Feature\PanelRoutesTest` (Render seluruh modul tanpa error 500)
5. `Tests\Feature\SecurityTest` (Header keamanan, honeypot bot trap, rate-limiting anti brute-force, dan isolasi role)

---

## 3. Sinkronisasi Model Pegawai & Peringatan Kontrak

Dalam model `App\Models\Employee`, terdapat event lifecycle `booted()` yang wajib dipertahankan:

```php
protected static function booted(): void
{
    static::saving(function (Employee $employee) {
        if ($employee->employment_status === 'Contract' && $employee->contract_end_date) {
            $employee->end_date = $employee->contract_end_date;
        } elseif ($employee->employment_status === 'Probation' && $employee->probation_end_date) {
            $employee->end_date = $employee->probation_end_date;
        } elseif ($employee->employment_status === 'Permanent') {
            $employee->end_date = null;
            $employee->contract_end_date = null;
            $employee->probation_end_date = null;
        }
    });
}
```

- **Widget Peringatan Kontrak:** Mengambil data pegawai PKWT/Probation dengan masa sisa `<= 60` hari. Perhitungan selisih hari wajib dibulatkan ke atas menjadi integer dengan `(int) ceil(...)` untuk menghindari desimal floating point.

---

## 4. Penggunaan Kode Error Domain

Ketika melempar exception atau membuat validasi baru, gunakan kelas pendukung `App\Support\HrisErrorCode`:

```php
use App\Support\HrisErrorCode;

// Mendapatkan data error terstandar
$errorData = HrisErrorCode::fromException($exception);
// Menghasilkan array: ['code' => 'DB002', 'title' => '...', 'message' => '...', 'action' => '...']
```

Pada respon JSON API:
```json
{
  "code": "VAL001",
  "title": "Validasi Gagal",
  "message": "Silakan lengkapi kolom yang wajib diisi.",
  "errors": { ... }
}
```

---

## 5. Prosedur Push ke GitHub

Jika ada perubahan yang sudah teruji dan siap dipublish:
1. Pastikan working tree bersih dan sudah dicommit.
2. Jalankan perintah push di terminal pengguna:
   ```powershell
   cd "d:\File Magang\Codeworks\Lara-HRIS\hris-system"
   git push origin main
   ```
