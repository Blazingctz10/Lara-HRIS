<?php

namespace App\Support;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class HrisErrorCode
{
    /**
     * Klasifikasi dan petakan Throwable menjadi Kode Error Standar Core HRIS.
     */
    public static function fromThrowable(Throwable $e): array
    {
        // 1. DATABASE EXCEPTIONS (Prefix: DB)
        if ($e instanceof QueryException) {
            $message = $e->getMessage();
            $sqlCode = $e->getCode();

            // Unique constraint violation
            if (str_contains($message, 'UNIQUE constraint failed') || str_contains($message, 'Duplicate entry') || $sqlCode === '23000') {
                return [
                    'code' => 'DB002',
                    'category' => 'DATABASE_CONSTRAINT',
                    'title' => 'Duplikasi Data Terdeteksi',
                    'description' => 'Data yang Anda masukkan (seperti NIP, NIK, No. Rekening, atau Email) sudah terdaftar dalam sistem.',
                    'action' => 'Pastikan data yang diinput bersifat unik dan belum pernah digunakan sebelumnya.',
                ];
            }

            // Foreign key constraint violation
            if (str_contains($message, 'FOREIGN KEY constraint failed') || str_contains($message, 'Cannot delete or update a parent row')) {
                return [
                    'code' => 'DB003',
                    'category' => 'DATABASE_INTEGRITY',
                    'title' => 'Pelanggaran Relasi Integritas Data',
                    'description' => 'Operasi ini tidak dapat diselesaikan karena data ini masih terhubung dengan data aktif lainnya (misal: cabang yang masih memiliki pegawai).',
                    'action' => 'Hapus atau pindahkan relasi data terkait terlebih dahulu sebelum menghapus data ini.',
                ];
            }

            // Connection issues
            if (str_contains($message, 'Connection refused') || str_contains($message, 'unable to open database file') || str_contains($message, 'Access denied')) {
                return [
                    'code' => 'DB001',
                    'category' => 'DATABASE_CONNECTION',
                    'title' => 'Koneksi Database Terputus',
                    'description' => 'Aplikasi gagal membuka atau berkomunikasi dengan berkas/server database.',
                    'action' => 'Periksa izin hak akses berkas SQLite atau status server database Anda.',
                ];
            }

            // General query execution error
            return [
                'code' => 'DB005',
                'category' => 'DATABASE_QUERY',
                'title' => 'Kegagalan Kueri Database',
                'description' => 'Terjadi kesalahan saat memproses data ke database.',
                'action' => 'Periksa struktur kolom dan format data yang dikirimkan.',
            ];
        }

        if ($e instanceof \PDOException) {
            return [
                'code' => 'DB001',
                'category' => 'DATABASE_CONNECTION',
                'title' => 'Koneksi Database Terputus',
                'description' => 'Driver PDO database tidak dapat membuat koneksi yang stabil.',
                'action' => 'Pastikan layanan database lokal atau file SQLite dapat diakses.',
            ];
        }

        if ($e instanceof ModelNotFoundException) {
            return [
                'code' => 'DB004',
                'category' => 'DATABASE_NOT_FOUND',
                'title' => 'Data Tidak Ditemukan di Database',
                'description' => 'Rekord atau entitas data yang diminta tidak tersedia di dalam basis data.',
                'action' => 'Pastikan ID data yang diakses masih aktif dan belum terhapus.',
            ];
        }

        // 2. AUTHENTICATION & AUTHORIZATION (Prefix: AUTH)
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return [
                'code' => 'AUTH003',
                'category' => 'AUTH_SESSION',
                'title' => 'Sesi Login Berakhir',
                'description' => 'Masa berlaku sesi login Anda telah habis.',
                'action' => 'Silakan masuk kembali dengan email dan kata sandi Anda.',
            ];
        }

        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException || $e instanceof AccessDeniedHttpException) {
            return [
                'code' => 'AUTH005',
                'category' => 'AUTH_FORBIDDEN',
                'title' => 'Hak Akses Ditolak (403)',
                'description' => 'Akun Anda tidak memiliki kewenangan (role) untuk membuka laman atau data ini.',
                'action' => 'Hubungi Administrator atau HRD jika Anda memerlukan hak akses untuk menu ini.',
            ];
        }

        if ($e instanceof TokenMismatchException) {
            return [
                'code' => 'AUTH003',
                'category' => 'AUTH_CSRF',
                'title' => 'Kedaluwarsa Token Keamanan (419)',
                'description' => 'Token CSRF form telah kedaluwarsa karena halaman tidak aktif terlalu lama.',
                'action' => 'Muat ulang halaman (F5) dan lakukan pengiriman ulang form.',
            ];
        }

        // 3. VALIDATION (Prefix: VAL)
        if ($e instanceof ValidationException) {
            return [
                'code' => 'VAL001',
                'category' => 'VALIDATION_ERROR',
                'title' => 'Validasi Formulir Gagal',
                'description' => 'Terdapat kolom wajib yang belum terisi dengan benar.',
                'action' => 'Periksa kolom isian yang ditandai merah lalu simpan kembali.',
            ];
        }

        // 4. HTTP EXCEPTIONS (Prefix: SYS)
        if ($e instanceof NotFoundHttpException) {
            return [
                'code' => 'SYS404',
                'category' => 'SYSTEM_HTTP',
                'title' => 'Halaman Tidak Ditemukan (404)',
                'description' => 'Alamat URL atau rute yang Anda tuju tidak terdaftar di sistem.',
                'action' => 'Periksa kembali ejaan URL atau klik tombol kembali ke Dashboard.',
            ];
        }

        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
            return [
                'code' => 'SYS' . $statusCode,
                'category' => 'SYSTEM_HTTP',
                'title' => 'Peringatan HTTP ' . $statusCode,
                'description' => $e->getMessage() ?: 'Terjadi respon HTTP khusus pada permintaan ini.',
                'action' => 'Coba muat ulang halaman atau hubungi pengelola sistem.',
            ];
        }

        // 5. GENERAL UNCAUGHT SYSTEM EXCEPTION (SYS500)
        return [
            'code' => 'SYS500',
            'category' => 'SYSTEM_ERROR',
            'title' => 'Kesalahan Internal Sistem (500)',
            'description' => 'Sistem mendeteksi galat yang tidak terduga saat menjalankan instruksi aplikasi.',
            'action' => 'Laporkan kode error ini kepada tim teknis IT BPR Banda Raya.',
        ];
    }
}
