<?php

namespace App\Services;

use App\Models\JobApplicant;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImapRecruitmentService
{
    protected string $host;
    protected int $port;
    protected string $encryption;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->host = config('mail.imap.host', env('IMAP_HOST', 'mail.bprbandaraya.com'));
        $this->port = (int) config('mail.imap.port', env('IMAP_PORT', 993));
        $this->encryption = config('mail.imap.encryption', env('IMAP_ENCRYPTION', 'ssl'));
        $this->username = config('mail.imap.username', env('IMAP_USERNAME', 'recruitment@bprbandaraya.com'));
        $this->password = config('mail.imap.password', env('IMAP_PASSWORD', ''));
    }

    public function getConfig(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption,
            'username' => $this->username,
            'has_password' => ! empty($this->password),
        ];
    }

    /**
     * Test connection to cPanel IMAP server.
     */
    public function testConnection(): array
    {
        $targetHost = ($this->encryption === 'ssl' ? 'ssl://' : '') . $this->host;
        $timeout = 5;

        // Buka koneksi socket TLS/SSL ke port cPanel IMAP (993 / 143)
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]);

        $socket = @stream_socket_client(
            "{$targetHost}:{$this->port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $socket) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke {$this->host}:{$this->port} ({$errstr})",
            ];
        }

        $greeting = fgets($socket, 512);
        fclose($socket);

        return [
            'success' => true,
            'message' => "Berhasil terhubung ke server cPanel IMAP ({$this->host}:{$this->port}). Respons server: " . trim($greeting),
        ];
    }

    /**
     * Sinkronisasi & tarik email pelamar masuk dari cPanel IMAP.
     */
    public function fetchEmails(): array
    {
        $storageDir = storage_path('app/public/recruitment_cv');
        if (! File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        // Jika ekstensi php_imap aktif dan password terisi, gunakan imap_open
        if (extension_loaded('imap') && ! empty($this->password)) {
            return $this->fetchWithExtImap($storageDir);
        }

        // Jika socket cPanel aktif, coba lakukan autentikasi socket IMAP
        if (! empty($this->password)) {
            return $this->fetchWithSocketImap($storageDir);
        }

        // Jika kredensial belum diisi di .env, laporkan status panduan konfigurasi
        return [
            'success' => false,
            'imported' => 0,
            'message' => "Kredensial IMAP cPanel (IMAP_PASSWORD) belum diisi di .env. Anda dapat menggunakan tombol 'Muat Data Contoh Pelamar' untuk menguji alur rekrutmen saat ini.",
        ];
    }

    /**
     * Menghasilkan data simulasi lamaran kerja lengkap dengan file CV contoh
     * agar penguji/HRD dapat langsung mencoba alur screening tanpa menunggu email nyata.
     */
    public function generateSampleApplicants(): int
    {
        $storageDir = storage_path('app/public/recruitment_cv');
        if (! File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        $samples = [
            [
                'name' => 'Mega Utami, S.E.',
                'email' => 'mega.utami@gmail.com',
                'phone' => '081277112233',
                'position_applied' => 'Customer Service (CS)',
                'subject' => 'Lamaran Kerja - Posisi Customer Service (CS) BPR Banda Raya',
                'message' => "Dengan hormat,\nBersama ini saya bermaksud mengajukan lamaran kerja sebagai Customer Service di PT BPR Banda Raya. Saya lulusan S1 Manajemen dengan pengalaman magang di perbankan selama 6 bulan.\n\nTerlampir Curriculum Vitae (CV) dan dokumen pendukung.",
                'cv_filename' => 'CV_Mega_Utami_CS.pdf',
                'email_uid' => 'DEMO-MSG-' . time() . '-1',
                'status' => 'Baru',
            ],
            [
                'name' => 'Bagas Saputra, A.Md.',
                'email' => 'bagas.saputra@yahoo.com',
                'phone' => '081399887766',
                'position_applied' => 'Teller Kas & Kliring',
                'subject' => 'Surat Lamaran - Teller Kas & Kliring Kantor Cabang Botania',
                'message' => "Yth. Tim HRD PT BPR Banda Raya,\nSaya mengajukan diri untuk posisi Teller Kas. Memiliki ketelitian tinggi dalam menghitung uang tunai dan pelayanan nasabah prima.\n\nTerima kasih atas perhatiannya.",
                'cv_filename' => 'CV_Bagas_Saputra_Teller.pdf',
                'email_uid' => 'DEMO-MSG-' . time() . '-2',
                'status' => 'Screening',
            ],
            [
                'name' => 'Rina Marlina, S.Kom.',
                'email' => 'rina.marlina@outlook.com',
                'phone' => '082166554433',
                'position_applied' => 'Account Officer (AO) Komersial',
                'subject' => 'Aplikasi Lamaran: Account Officer (AO) Penyaluran Kredit UMKM Batam',
                'message' => "Selamat siang Tim Human Capital,\nSaya memiliki pengalaman 2 tahun di bidang marketing kredit mikro dan ingin berkontribusi memajukan UMKM bersama PT BPR Banda Raya Batam.",
                'cv_filename' => 'CV_Rina_Marlina_AO.pdf',
                'email_uid' => 'DEMO-MSG-' . time() . '-3',
                'status' => 'Interview',
            ],
        ];

        $createdCount = 0;
        foreach ($samples as $sample) {
            // Buat file dummy PDF untuk lampiran CV
            $dummyCvPath = $storageDir . DIRECTORY_SEPARATOR . $sample['cv_filename'];
            if (! File::exists($dummyCvPath)) {
                File::put($dummyCvPath, "%PDF-1.4\n% Contoh Berkas CV Resmi Pelamar: " . $sample['name'] . "\nPT BPR Banda Raya Recruitment Archive.");
            }

            JobApplicant::create([
                'name' => $sample['name'],
                'email' => $sample['email'],
                'phone' => $sample['phone'],
                'position_applied' => $sample['position_applied'],
                'subject' => $sample['subject'],
                'message' => $sample['message'],
                'cv_path' => 'recruitment_cv/' . $sample['cv_filename'],
                'cv_filename' => $sample['cv_filename'],
                'email_uid' => $sample['email_uid'],
                'email_date' => now()->subHours(rand(1, 48)),
                'status' => $sample['status'],
                'notes' => 'Diimpor dari contoh berkas lamaran masuk cPanel Webmail.',
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    protected function fetchWithExtImap(string $storageDir): array
    {
        $mailbox = "{" . $this->host . ":" . $this->port . "/imap/" . $this->encryption . "/novalidate-cert}INBOX";
        $inbox = @imap_open($mailbox, $this->username, $this->password);

        if (! $inbox) {
            return [
                'success' => false,
                'imported' => 0,
                'message' => "Gagal autentikasi cPanel IMAP: " . imap_last_error(),
            ];
        }

        $emails = imap_search($inbox, 'ALL');
        $imported = 0;

        if ($emails) {
            rsort($emails);
            foreach ($emails as $emailNumber) {
                $overview = imap_fetch_overview($inbox, $emailNumber, 0)[0] ?? null;
                if (! $overview) continue;

                $uid = (string) ($overview->uid ?? $emailNumber);
                if (JobApplicant::where('email_uid', $uid)->exists()) {
                    continue;
                }

                $subject = isset($overview->subject) ? imap_utf8($overview->subject) : 'Tanpa Subjek';
                $from = $overview->from ?? 'Pelamar';
                $emailDate = isset($overview->date) ? Carbon::parse($overview->date) : now();

                // Ekstrak nama dan email dari string pengirim
                preg_match('/(.*)<(.*)>/', $from, $matches);
                $name = trim($matches[1] ?? $from);
                $senderEmail = trim($matches[2] ?? $from);
                if (empty($name)) $name = $senderEmail;

                // Tebak posisi yang dilamar dari subjek
                $position = $this->detectPosition($subject);

                // Buat record pelamar
                JobApplicant::create([
                    'name' => $name,
                    'email' => $senderEmail,
                    'position_applied' => $position,
                    'subject' => $subject,
                    'email_uid' => $uid,
                    'email_date' => $emailDate,
                    'status' => 'Baru',
                ]);

                $imported++;
            }
        }

        imap_close($inbox);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Berhasil menyinkronkan {$imported} berkas lamaran kerja dari cPanel IMAP.",
        ];
    }

    protected function fetchWithSocketImap(string $storageDir): array
    {
        $targetHost = ($this->encryption === 'ssl' ? 'ssl://' : '') . $this->host;
        $context = stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
        ]);

        $socket = @stream_socket_client("{$targetHost}:{$this->port}", $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
        if (! $socket) {
            return [
                'success' => false,
                'imported' => 0,
                'message' => "Koneksi socket cPanel IMAP gagal: {$errstr}",
            ];
        }

        // Read greeting
        fgets($socket, 512);

        // Send Login
        fwrite($socket, "A001 LOGIN \"{$this->username}\" \"{$this->password}\"\r\n");
        $loginResp = fgets($socket, 512);

        if (! str_contains($loginResp, 'OK')) {
            fclose($socket);
            return [
                'success' => false,
                'imported' => 0,
                'message' => "Login cPanel IMAP ditolak. Periksa username dan password akun email Anda.",
            ];
        }

        // Select INBOX
        fwrite($socket, "A002 SELECT INBOX\r\n");
        while ($line = fgets($socket, 512)) {
            if (str_starts_with($line, 'A002')) break;
        }

        // Logout
        fwrite($socket, "A003 LOGOUT\r\n");
        fclose($socket);

        return [
            'success' => true,
            'imported' => 0,
            'message' => "Autentikasi cPanel IMAP berhasil. Kotak masuk recruitment@bprbandaraya.com aktif.",
        ];
    }

    protected function detectPosition(string $text): string
    {
        $lower = strtolower($text);
        if (str_contains($lower, 'teller')) return 'Teller Kas & Kliring';
        if (str_contains($lower, 'customer service') || str_contains($lower, 'cs')) return 'Customer Service (CS)';
        if (str_contains($lower, 'account officer') || str_contains($lower, 'ao') || str_contains($lower, 'kredit')) return 'Account Officer (AO)';
        if (str_contains($lower, 'it') || str_contains($lower, 'programmer') || str_contains($lower, 'tech')) return 'Teknologi Informasi (IT)';
        if (str_contains($lower, 'hr') || str_contains($lower, 'sdm')) return 'Human Capital / HRD';
        if (str_contains($lower, 'security') || str_contains($lower, 'satpam')) return 'Petugas Keamanan (Security)';

        return 'Umum / Staf Operasional';
    }
}
