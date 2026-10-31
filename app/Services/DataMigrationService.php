<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\CareerMovement;
use App\Models\EmployeeFamily;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeePerformanceAppraisal;
use App\Models\EmployeeTraining;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DataMigrationService
{
    /**
     * Menghasilkan template CSV Formulir CV / Database Karyawan Resmi BPR Banda Raya
     */
    public function generateEmployeeBprCvTemplateCsv(): string
    {
        return <<<'CSV'
I. DATA PRIBADI;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Nama;;;;;;:;RIA ARDILA;;;;;;;;;29-Okt-26;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
NIK;;;;;;:;1204 0077;;;;;;;;;;;;;;;;;;;;;;;;;;Kantor Pusat;;;;;;;;;;;;;;;;;;;;;;
Jabatan;;;;;;:;Manager Operational;;;;;;;;;;;;;;;;;;;;;;;;;;KC Botania;;;;;;;;;;;;;;;;;;;;;;
Unit Kerja;;;;;;:;Kantor Pusat;;;;;;;;;;;;;;;;;;;;;;;;;;KC Aviari;;;;;;;;;;;;;;;;;;;;;;
Tgl Efektif Jabatan;;;;;;:;22-Nov-21;;;;;;;;;;;;;;;;;;;;;;;;;;K. Kas Bengkong;;;;;;;;;;;;;;;;;;;;;;
Grade;;;;;;:;6;;;;;;;;;;;;;;;;;;;;;;Aktif;;;;;;;;;;;;;;;;;;;;;;;;;;
Tgl Efektif Grade;;;;;;:;01-Jan-20;;;;;;;;;;;;;;;;;;;;;;Keluar;;;;;;;;;;;;;;;;;;;;;;;;;;
Join Date;;;;;;:;09-Jan-12;;;;;;;;;;;;;;;;;;;;;;;;Laki-laki;;;;;;;;;;;;;;;;;;;;;;;;
Tgl Permanent;;;;;;:;09-Apr-14;;;;;;;;;;;;;;;;;;;;;;;;Perempuan;;;;;;;;;;;;;;;;;;;;;;;;
Aktif / Keluar;;;;;;:;Keluar;;;Tgl Keluar;;;:;31-Agu-23;;;;Keterangan ;;;:;Mengundurkan diri;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Masa Kerja;;;;;;:;14  tahun;;;9  bulan;;;;;;;;;;;;;;;;;;;;;;Mengundurkan diri;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;Habis kontrak;;;;;;;;;;;;;;;;;;;;;;;
Tempat Lahir;;;;;;:;Parakan;;;;;;;;;;;;;;;;;;;;;;;;;Diberhentikan;;;;;;;;;;;;;;;;;;;;;;;
Tgl Lahir;;;;;;:;17-Jan-90;;;;;;;;;;;;;;;;;;;;;;;;;Usia Pensiun;;;;;;;;;;;;;;;;;;;;;;;
Usia;;;;;;:;36;tahun;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
L/P;;;;;;:;Perempuan;;;;;;;;;;;;;;;;;;;;;;;SMP;;;;TK/0;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;SMA;;;;TK/1;;;;;;;;;;;;;;;;;;;;;
Jenjang Pendidikan;;;;;;:;S-1;;;;;;;;;;;;;;;;;;;;;;;SMK;;;;TK/2;;;;;;;;;;;;;;;;;;;;;
Jurusan;;;;;;:;Akuntansi;;;;;;;;;;;;;;;;;;;;;;;D-1;;;;TK/3;;;;;;;;;;;;;;;;;;;;;
Univ;;;;;;:;Univ. Batam;;;;;;;;;;;;;;;;;;;;;;;D-3;;;;K/0;;;;;;;;;;;;;;;;;;;;;
IPK;;;;;;:;2,98;;;;;;;;;;;;;;;;;;;;;;;D-4;;;;K/1;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;S-1;;;;K/2;;;;;;;;;;;;;;;;;;;;;
Alamat;;;;;;:;Perum. Marbella Residence Blok C5 No. 01;;;;;;;;;;;;;;;;;;;;;;;S-2;;;;K/3;;;;;;;;;;;;;;;;;;;;;
No. HP;;;;;;:;0822 8383 0547;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Email;;;;;;:;riaardilla2016@gmail.com;;;;;;;ria@bprbandaraya.com;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. KTP;;;;;;:;2171045701909002;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. NPWP;;;;;;:;69.519.009.0-215.000;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. BPJSTK;;;;;;:;08011529719;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No. BPJS Kes;;;;;;:;0001289893544;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Rek. BR;;;;;;:;01.10.05857;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Rek. Mandiri;;;;;;:;109-00-1514589-9;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
II. DATA KELUARGA;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Status PTKP;;;;;;:;TK/0;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No;;Nama;;;;;;;;;NIK;;;;;Hubungan Keluarga;;;;;Tempat Lahir;;;;;;Tgl Lahir;;;;;;;;;;;;;;;;;;;;;;;;;;;;
1;;Ria Ardila;;;;;;;;;2171045701909002;;;;;ybs;;;;;Parakan;;;;;;17-Jan-90;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Emergency Call;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Nama;;;;;;:;Ade Rahmat;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No.HP;;;;;;:;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
Hubungan;;;;;;:;Suami;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
III. RIWAYAT KARIR;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No;;Unit Kerja;;;;;;;Posisi;;;;;;;;;Tgl Efektif Jabatan;;;;No. Surat Pengangkatan;;;;;;;;;Grade;;;Tgl Efektif Grade;;;;Keterangan;;;;;;;;;;;;;;;;;
1;;KC Botania;;;;;;;Service Assistant;;;;;;;;;09-Apr-12;;;;005/KK-HRD/BPRBR/IV/2012;;;;;;;;;2;;;09-Apr-12;;;;Karyawan Kontrak;;;;;;;;;;;;;;;;;
2;;KC Botania;;;;;;;Service Assistant;;;;;;;;;09-Apr-12;;;;025/KK2-HRD/BPRBR/IV/2013;;;;;;;;;2;;;09-Apr-12;;;;Karyawan Kontrak;;;;;;;;;;;;;;;;;
3;;KC Botania;;;;;;;Customer Service;;;;;;;;;01-Mei-13;;;;;;;;;;;;;;2;;09-Apr-12;;;;Mutasi Jabatan;;;;;;;;;;;;;;;;;
4;;KC Botania;;;;;;;Customer Service;;;;;;;;;01-Mei-13;;;;004/KEP/DIR/BPRBR/IV/2014;;;;;;;;;;3;;01-Jan-14;;;;Karyawan Tetap dan Kenaikan Grade;;;;;;;;;;;;;;;;;
5;;KC Botania;;;;;;;Customer Service;;;;;;;;;01-Mei-13;;;;006/KEP/DIR/BPRBR/V/2016;;;;;;;;;4;;;01-Mei-16;;;;Kenaikan Grade;;;;;;;;;;;;;;;;;
6;;KC Botania;;;;;;;Admin Kredit;;;;;;;;;01-Okt-16;;;;014/KEP/DIR/BPR-BR/X/2016;;;;;;;;;;4;;01-Mei-16;;;;Mutasi Jabatan;;;;;;;;;;;;;;;;;
7;;KC Botania;;;;;;;Admin Kredit;;;;;;;;;01-Okt-16;;;;014/KEP/DIR/BPRBR/IV/2018;;;;;;;;;5;;;01-Jan-18;;;;Kenaikan Grade;;;;;;;;;;;;;;;;;
8;;KC Botania;;;;;;;Kord. Operational;;;;;;;;;01-Nov-18;;;;032/KEP/DIR/BPRBR/X/2018;;;;;;;;;5;;;01-Jan-18;;;;Mutasi Jabatan;;;;;;;;;;;;;;;;;
9;;KC Botania;;;;;;;Kord. Operational;;;;;;;;;01-Nov-18;;;;016/KEP/DIR/BPRBR/IV/2020;;;;;;;;;6;;;01-Jan-20;;;;Kenaikan Grade;;;;;;;;;;;;;;;;;
10;;Kantor Pusat;;;;;;;Kord. Accounting;;;;;;;;;22-Nov-21;;;;037/KEP/DIR/BPRBR/XI/2021;;;;;;;;;6;;;01-Jan-20;;;;Mutasi Jabatan dan Mutasi Penempatan;;;;;;;;;;;;;;;;;
11;;Kantor Pusat;;;;;;;Manager Operational;;;;;;;;;25-Mar-23;;;;010/KEP/DIR/BPRBR/III/2023;;;;;;;;;7;;;25-Mar-23;;;;Kenaikan Jabatan dan Kenaikan Grade;;;;;;;;;;;;;;;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
IV. RIWAYAT PELATIHAN;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;V. PERFORMANCE APPRAISAL (PA);;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
No;;Nama Pelatihan;;;;;;;;;;;;;;;;Jadwal;;;;Penyelenggara;;;;;;;;;Pemateri;;;;;;;Lokasi;;;;;;;;;;;;;;No;Periode Penilaian (Tahun Buku);Nilai;Angka Mutu
1;;Aspek Legalitas Dalam Perkreditan;;;;;;;;;;;;;;;;22-Des-12;;;;BPR Banda Raya;;;;;;;;;LBKH An-Nisa;;;;;;;Nongsa Point Marina;;;;;;;;;;;;;;1;2012;B; 3,73 
2;;Menciptakan SDM yang berkualitas & berintegritas;;;;;;;;;;;;;;;;13 Des'14;;;;BPR Banda Raya;;;;;;;;;Direksi;;;;;;;Batam View Beach Resort;;;;;;;;;;;;;;2;2013;B; 3,53 
3;;Motivasi Karyawan;;;;;;;;;;;;;;;;15-Feb-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;4;2014;B; 3,51 
4;;Communication Skill;;;;;;;;;;;;;;;;28-Mar-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;5;2015;B; 3,93 
5;;Service Excellent;;;;;;;;;;;;;;;;29-Mar-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;6;2016;B; 3,84 
6;;APU & PPT;;;;;;;;;;;;;;;;16-Mei-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;7;2017;B; 3,82 
7;;Perlindungan Konsumen;;;;;;;;;;;;;;;;15-Agu-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;8;2018;B; 3,72 
8;;Pencegahan Fraud pada BPR;;;;;;;;;;;;;;;;05-Des-15;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;9;2019;B; 3,58 
9;;7 Habits for Highly Effective People;;;;;;;;;;;;;;;;16-Jan-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;10;2020;B; 3,51 
10;;Etika Profesional Karyawan ;;;;;;;;;;;;;;;;12-Mar-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;11;2021;B; 3,88 
11;;Marketing for Non Marketing;;;;;;;;;;;;;;;;14-Mei-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;12;2022;B; 3,59 
12;;Pengenalan KPI utk BPR;;;;;;;;;;;;;;;;21-Jul-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;;;;
13;;SAK ETAP BPR;;;;;;;;;;;;;;;;06-Agu-16;;;;DPD Perbarindo Kepri;;;;;;;;;Bp. Yakub, SE, Msi;;;;;;;Batam;;;;;;;;;;;;;;;;;
14;;Monitoring Kredit;;;;;;;;;;;;;;;;09-Sep-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Batam;;;;;;;;;;;;;;;;;
15;;Integritas Karyawan;;;;;;;;;;;;;;;;27-Nov-16;;;;BPR Banda Raya;;;;;;;;;Tiga Sekawan Solution;;;;;;;Tg. Pinang;;;;;;;;;;;;;;;;;
16;;Pelatihan dan Studi Banding ;;;;;;;;;;;;;;;;25-Mar-17;;;;BPR Banda Raya;;;;;;;;;Direksi;;;;;;;Bandung;;;;;;;;;;;;;;;;;
17;;Penerapan Manajemen Resiko bagi karyawan;;;;;;;;;;;;;;;;26-Jan-19;;;;BPR Banda Raya;;;;;;;;;Bp. Lucas S Muliawan;;;;;;;Batam;;;;;;;;;;;;;;;;;
18;;Drive Change;;;;;;;;;;;;;;;;21-Sep-19;;;;BPR Banda Raya;;;;;;;;;Ibu Sitta Chirana;;;;;;;Batam;;;;;;;;;;;;;;;;;
19;;Meaning Of Work & Character Building;;;;;;;;;;;;;;;;15-Feb-20;;;;BPR Banda Raya;;;;;;;;;ESQ Team;;;;;;;Batam;;;;;;;;;;;;;;;;;
20;;PPBJ General Banking Angkatan 3;;;;;;;;;;;;;;;;03 Agt - 30 Nov'20;;;;LPPI;;;;;;;;;LPPI;;;;;;;Jakarta;;;;;;;;;;;;;;;;;
21;;ESQ - Quantum Excellence High Performance Program;;;;;;;;;;;;;;;;21-Jan-23;;;;BPR Banda Raya;;;;;;;;;ESQ Team;;;;;;;Batam;;;;;;;;;;;;;;;;;
22;;Pelatihan Standar Akuntansi Keuangan Entitas Privat (SAK EP);;;;;;;;;;;;;;;;24-Jun-23;;;;DPD Perbarindo;;;;;;;;;Bp. Hery Prasetyo;;;;;;;Batam;;;;;;;;;;;;;;;;;
23;;"Training ESQ ""NLP For Leaders""";;;;;;;;;;;;;;;;07-08 Jun'23;;;;BPR Banda Raya;;;;;;;;;ESQ Team;;;;;;;Batam;;;;;;;;;;;;;;;;;
CSV;
    }

    /**
     * Menghasilkan isi template CSV untuk impor data master pegawai lengkap sesuai spesifikasi BPR
     */
    public function generateEmployeeTemplateCsv(): string
    {
        $headers = [
            'NIP',
            'NIK',
            'Nama_Lengkap',
            'Email_Kantor',
            'Email_Pribadi',
            'Nomor_Telepon',
            'Jenis_Kelamin',
            'Tempat_Lahir',
            'Tanggal_Lahir',
            'Alamat',
            'Kantor_Cabang',
            'Divisi',
            'Jabatan',
            'Tanggal_Efektif_Jabatan',
            'Grade',
            'Tanggal_Efektif_Grade',
            'Status_Kerja',
            'Tanggal_Masuk',
            'Tanggal_Pengangkatan_Tetap',
            'Akhir_Kontrak',
            'Status_Pegawai',
            'Jenjang_Pendidikan',
            'Jurusan',
            'Universitas',
            'IPK',
            'NPWP',
            'No_BPJSTK',
            'No_BPJS_Kes',
            'Rekening_BPR',
            'Nama_Bank_Lain',
            'Nomor_Rekening_Bank_Lain',
            'Atas_Nama_Rekening',
            'Status_PTKP',
            'Kontak_Darurat_Nama',
            'Kontak_Darurat_Nomor',
            'Kontak_Darurat_Hubungan',
        ];

        $sampleRows = [
            [
                'BPR-2026-0101',
                '2171012304950001',
                'Hendri Prasetyo',
                'hendri.prasetyo@bprbandaraya.com',
                'hendri.pribadi@gmail.com',
                '081266554411',
                'Male',
                'Batam',
                '1995-04-23',
                'Jl. Raja Ali Haji No. 12, Batam',
                'Kantor Cabang Botania',
                'Operasional & Pelayanan',
                'Customer Service',
                '2024-01-15',
                '4',
                '2024-01-15',
                'Permanent',
                '2024-01-15',
                '2025-01-15',
                '',
                'Active',
                'S-1',
                'Manajemen Perbankan',
                'Universitas Internasional Batam',
                '3.45',
                '81.234.567.8-215.000',
                '08017700462',
                '0001617375611',
                '01.10.05307',
                'Bank Mandiri',
                '109-00-1514578-2',
                'Hendri Prasetyo',
                'K/1',
                'Dewi Anggraini',
                '081299887711',
                'Istri',
            ],
            [
                'BPR-2026-0102',
                '2171025508980002',
                'Siti Rahmadani',
                'siti.rahmadani@bprbandaraya.com',
                'siti.rahmadani@gmail.com',
                '081377889922',
                'Female',
                'Padang',
                '1998-08-15',
                'Komplek Aviari Blok B No. 5, Batu Aji',
                'Kantor Cabang Aviari',
                'Kredit & Pemasaran',
                'Account Officer (AO)',
                '2024-06-01',
                '5',
                '2024-06-01',
                'Contract',
                '2024-06-01',
                '',
                '2027-06-01',
                'Active',
                'D-3',
                'Akuntansi',
                'Politeknik Negeri Padang',
                '3.60',
                '82.345.678.9-215.000',
                '08017700499',
                '0001617375699',
                '01.10.05308',
                'BCA',
                '8210349911',
                'Siti Rahmadani',
                'TK/0',
                'Hj. Maryam',
                '081366551122',
                'Ibu',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Menghasilkan template Microsoft Excel (.xls) resmi lengkap dengan format kolom & data percontohan
     */
    public function generateEmployeeTemplateXls(): string
    {
        $headers = [
            'NIP', 'NIK', 'Nama_Lengkap', 'Email_Kantor', 'Email_Pribadi',
            'Nomor_Telepon', 'Jenis_Kelamin', 'Tempat_Lahir', 'Tanggal_Lahir', 'Alamat',
            'Kantor_Cabang', 'Divisi', 'Jabatan', 'Tanggal_Efektif_Jabatan',
            'Grade', 'Tanggal_Efektif_Grade', 'Status_Kerja', 'Tanggal_Masuk',
            'Tanggal_Pengangkatan_Tetap', 'Akhir_Kontrak', 'Status_Pegawai',
            'Jenjang_Pendidikan', 'Jurusan', 'Universitas', 'IPK',
            'NPWP', 'No_BPJSTK', 'No_BPJS_Kes', 'Rekening_BPR', 'Nama_Bank_Lain',
            'Nomor_Rekening_Bank_Lain', 'Atas_Nama_Rekening', 'Status_PTKP',
            'Kontak_Darurat_Nama', 'Kontak_Darurat_Nomor', 'Kontak_Darurat_Hubungan',
        ];

        $sampleRows = [
            [
                'BPR-2026-0101', '2171012304950001', 'Hendri Prasetyo', 'hendri.prasetyo@bprbandaraya.com', 'hendri.pribadi@gmail.com',
                '081266554411', 'Male', 'Batam', '1995-04-23', 'Jl. Raja Ali Haji No. 12, Batam',
                'Kantor Pusat BPR Banda Raya', 'Operasional & Pelayanan', 'Customer Service', '2024-01-15',
                '4', '2024-01-15', 'Permanent', '2024-01-15',
                '2024-01-15', '', 'Active',
                'S-1', 'Manajemen Perbankan', 'Universitas Internasional Batam', '3.45',
                '81.234.567.8-215.000', '08017700462', '0001617375611', '01.10.05307', 'Bank Mandiri',
                '109-00-1514578-2', 'Hendri Prasetyo', 'K/1',
                'Dewi Anggraini', '081299887711', 'Istri',
            ],
            [
                'BPR-2026-0102', '2171025508980002', 'Siti Rahmadani', 'siti.rahmadani@bprbandaraya.com', 'siti.rahmadani@gmail.com',
                '081377889922', 'Female', 'Padang', '1998-08-15', 'Komplek Botania Garden Blok B No. 5, Batam',
                'Kantor Cabang Botania', 'Kredit & Pemasaran', 'Account Officer (AO)', '2024-06-01',
                '5', '2024-06-01', 'Contract', '2024-06-01',
                '', '2027-06-01', 'Active',
                'D-3', 'Akuntansi', 'Politeknik Negeri Padang', '3.60',
                '82.345.678.9-215.000', '08017700499', '0001617375699', '01.10.05308', 'BCA',
                '8210349911', 'Siti Rahmadani', 'TK/0',
                'Hj. Maryam', '081366551122', 'Ibu',
            ],
        ];

        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Master Pegawai</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:FreezePanes/>
     <x:FrozenNoSplit/>
     <x:SplitHorizontal>4</x:SplitHorizontal>
     <x:TopRowBottomPane>4</x:TopRowBottomPane>
     <x:SplitVertical>3</x:SplitVertical>
     <x:LeftColumnRightPane>3</x:LeftColumnRightPane>
     <x:ActivePane>0</x:ActivePane>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; }
  table { border-collapse: collapse; }
  .title-main { font-size: 14px; font-weight: bold; color: #0E6838; }
  .instructions { font-size: 10px; color: #555555; background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 6px; }
  .th-header { background-color: #0E6838; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #084022; padding: 6px 10px; }
  .td-text { mso-number-format: "\@"; border: 1px solid #cbd5e1; padding: 4px 6px; }
</style>
</head>
<body>
<table>
  <tr><td colspan="' . count($headers) . '" class="title-main">PT BPR BANDA RAYA - TEMPLATE IMPORT DATA MASTER PEGAWAI LENGKAP</td></tr>
  <tr><td colspan="' . count($headers) . '" class="instructions">Petunjuk: Jangan mengubah nama baris header di bawah ini. Kolom NIK, No BPJS, Rekening, dan Telepon otomatis bertipe teks agar angka 0 tidak terpotong. Simpan dalam format XLS atau CSV saat mengunggah.</td></tr>
  <tr><td colspan="' . count($headers) . '">&nbsp;</td></tr>
  <thead>
    <tr>';
        foreach ($headers as $h) {
            $html .= '<th class="th-header">' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr>
  </thead>
  <tbody>';

        foreach ($sampleRows as $row) {
            $html .= '<tr>';
            foreach ($row as $val) {
                $html .= '<td class="td-text">' . htmlspecialchars($val) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
</table>
</body>
</html>';

        return $html;
    }

    /**
     * Menghasilkan template Microsoft Excel (.xls) resmi untuk Log Absensi Mesin
     */
    public function generateAttendanceTemplateXls(): string
    {
        $headers = ['NIP', 'Tanggal', 'Jam_Masuk', 'Jam_Keluar', 'Status', 'Catatan'];
        $sampleRows = [
            ['BPR-2026-0101', '2026-10-01', '07:55:00', '17:05:00', 'Present', 'Log mesin fingerprint utama'],
            ['BPR-2026-0102', '2026-10-01', '08:14:00', '17:00:00', 'Late', 'Keterlambatan 14 menit'],
        ];

        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; }
  table { border-collapse: collapse; }
  .title-main { font-size: 14px; font-weight: bold; color: #0E6838; }
  .instructions { font-size: 10px; color: #555555; background-color: #fefce8; border: 1px solid #fef08a; padding: 6px; }
  .th-header { background-color: #0E6838; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #084022; padding: 6px 10px; }
  .td-text { mso-number-format: "\@"; border: 1px solid #cbd5e1; padding: 4px 6px; }
</style>
</head>
<body>
<table>
  <tr><td colspan="6" class="title-main">PT BPR BANDA RAYA - TEMPLATE IMPORT LOG ABSENSI</td></tr>
  <tr><td colspan="6" class="instructions">Format Status: Present (Tepat Waktu), Late (Terlambat), Excused (Izin/Cuti), Absent (Alpa). Format Tanggal: YYYY-MM-DD.</td></tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <thead>
    <tr>';
        foreach ($headers as $h) {
            $html .= '<th class="th-header">' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr>
  </thead>
  <tbody>';

        foreach ($sampleRows as $row) {
            $html .= '<tr>';
            foreach ($row as $val) {
                $html .= '<td class="td-text">' . htmlspecialchars($val) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody>
</table>
</body>
</html>';

        return $html;
    }

    /**
     * Menghasilkan isi template CSV untuk impor data absensi / fingerprint
     */
    public function generateAttendanceTemplateCsv(): string
    {
        $headers = [
            'NIP',
            'Tanggal',
            'Jam_Masuk',
            'Jam_Keluar',
            'Status',
            'Catatan',
        ];

        $sampleRows = [
            ['BPR-2026-0101', '2026-10-01', '07:55:00', '17:05:00', 'Present', 'Log mesin fingerprint utama'],
            ['BPR-2026-0102', '2026-10-01', '08:14:00', '17:00:00', 'Late', 'Keterlambatan 14 menit'],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Eksekusi impor pegawai dari berkas CSV dengan dukungan seluruh atribut profil BPR
     */
    public function importEmployeesFromCsv(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'Berkas CSV tidak ditemukan atau tidak dapat dibaca.',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        $parsedRows = $this->parseAnyFile($filePath);
        if (empty($parsedRows)) {
            return [
                'success' => false,
                'message' => 'Berkas tidak ditemukan, kosong, atau format berkas tidak dapat diproses.',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        // 1. Deteksi apakah berkas merupakan format Formulir CV / Dokumen Resmi Master Pegawai BPR Banda Raya (I. DATA PRIBADI)
        $isBprCvFormat = false;
        foreach ($parsedRows as $r) {
            $joined = strtolower(implode(' ', (array) $r));
            if (str_contains($joined, 'data pribadi') || (str_contains($joined, 'nama') && str_contains($joined, ':') && (str_contains($joined, 'nik') || str_contains($joined, 'jabatan')))) {
                $isBprCvFormat = true;
                break;
            }
        }

        if ($isBprCvFormat) {
            return $this->importEmployeesFromBprCvRows($parsedRows);
        }

        // Cari baris header yang mengandung NIP atau Nama Lengkap
        $headerIndex = -1;
        foreach ($parsedRows as $idx => $r) {
            $joined = strtolower(implode(' ', (array) $r));
            if (str_contains($joined, 'nip') || (str_contains($joined, 'nama') && (str_contains($joined, 'cabang') || str_contains($joined, 'jabatan')))) {
                $headerIndex = $idx;
                break;
            }
        }

        if ($headerIndex === -1 || count($parsedRows) <= $headerIndex + 1) {
            return [
                'success' => false,
                'message' => 'Kolom header tidak ditemukan (kolom NIP / Nama Lengkap wajib ada).',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        $rawHeader = $parsedRows[$headerIndex];
        $header = array_map(fn ($h) => trim(strtolower(str_replace([' ', '-'], '_', (string) $h))), $rawHeader);
        $dataRows = array_slice($parsedRows, $headerIndex + 1);

        $imported = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $index => $row) {
                if (count($row) < 2) {
                    continue;
                }
                if (count($row) < count($header)) {
                    $errors[] = "Baris " . ($index + 2) . ": Jumlah kolom tidak sesuai dengan header.";
                    continue;
                }

                $data = array_combine(array_slice($header, 0, count($row)), $row);

                $nip = trim($data['nip'] ?? '');
                $fullName = trim($data['nama_lengkap'] ?? ($data['nama'] ?? ($data['full_name'] ?? '')));

                if (empty($nip) || empty($fullName)) {
                    $errors[] = "Baris " . ($index + 2) . ": NIP dan Nama Lengkap wajib diisi.";
                    continue;
                }

                // 1. Tangani Email & Domain (.com)
                $email = trim($data['email_kantor'] ?? ($data['email'] ?? ($data['email_dinas'] ?? '')));
                if (empty($email)) {
                    $cleanNip = Str::slug($nip);
                    $email = "pegawai.{$cleanNip}@bprbandaraya.com";
                }
                // Pastikan domain .com
                $email = str_ireplace('@bprbandaraya.co.id', '@bprbandaraya.com', $email);

                // 2. Hubungkan / Buat Kantor Cabang
                $branchName = trim($data['kantor_cabang'] ?? ($data['cabang'] ?? ($data['branch'] ?? 'Kantor Cabang Botania')));
                $branch = Branch::where('name', 'LIKE', "%{$branchName}%")->first();
                if (! $branch) {
                    $branch = Branch::create([
                        'name' => $branchName,
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $branchName), 0, 5)) . rand(10, 99),
                        'city' => 'Batam',
                        'address' => 'Batam, Kepulauan Riau',
                        'latitude' => 1.1185,
                        'longitude' => 104.0532,
                        'radius_meters' => 100,
                    ]);
                }

                // 3. Hubungkan / Buat Divisi / Departemen
                $deptName = trim($data['divisi'] ?? ($data['departemen'] ?? ($data['department'] ?? 'Operasional & Pelayanan')));
                $dept = Department::where('name', 'LIKE', "%{$deptName}%")->first();
                if (! $dept) {
                    $dept = Department::create([
                        'name' => $deptName,
                        'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $deptName), 0, 4)) . rand(10, 99),
                    ]);
                }

                // 4. Hubungkan / Buat Jabatan
                $posName = trim($data['jabatan'] ?? ($data['posisi'] ?? ($data['position'] ?? 'Staf Operasional')));
                $position = $this->resolveBprPosition($posName, $dept);

                // 5. Buat / Perbarui Akun User
                $user = User::where('email', $email)->first();
                if (! $user) {
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'role' => 'employee',
                        'branch_id' => $branch->id,
                    ]);
                }

                // 6. Tangani Status Kerja & Format Tanggal
                $employmentStatus = ucfirst(strtolower(trim($data['status_kerja'] ?? ($data['employment_status'] ?? 'Permanent'))));
                if (! in_array($employmentStatus, ['Permanent', 'Contract', 'Probation', 'Internship'])) {
                    $employmentStatus = 'Permanent';
                }

                $gender = ucfirst(strtolower(trim($data['jenis_kelamin'] ?? ($data['gender'] ?? 'Male'))));
                if (! in_array($gender, ['Male', 'Female'])) {
                    $gender = (str_contains(strtolower($gender), 'wanita') || str_contains(strtolower($gender), 'perempuan') || str_contains(strtolower($gender), 'p')) ? 'Female' : 'Male';
                }

                $birthDate = ! empty($data['tanggal_lahir']) ? Carbon::parse($data['tanggal_lahir'])->toDateString() : null;
                $joinDate = ! empty($data['tanggal_masuk']) ? Carbon::parse($data['tanggal_masuk'])->toDateString() : now()->toDateString();
                $jobEffectiveDate = ! empty($data['tanggal_efektif_jabatan']) ? Carbon::parse($data['tanggal_efektif_jabatan'])->toDateString() : $joinDate;
                $gradeEffectiveDate = ! empty($data['tanggal_efektif_grade']) ? Carbon::parse($data['tanggal_efektif_grade'])->toDateString() : $joinDate;
                $permanentDate = ! empty($data['tanggal_pengangkatan_tetap']) ? Carbon::parse($data['tanggal_pengangkatan_tetap'])->toDateString() : null;
                $contractEndDate = ! empty($data['akhir_kontrak']) ? Carbon::parse($data['akhir_kontrak'])->toDateString() : null;

                $status = trim($data['status_pegawai'] ?? ($data['status'] ?? 'Active'));
                if (! in_array($status, ['Active', 'On Leave', 'Resigned', 'Terminated'])) {
                    $status = 'Active';
                }

                // 7. Simpan atau Perbarui Profil Employee
                $employee = Employee::where('nip', $nip)->first();
                $isNew = ! $employee;

                if ($isNew) {
                    $employee = new Employee();
                    $employee->nip = $nip;
                }

                $externalBankAcc = $data['nomor_rekening_bank_lain'] ?? ($data['nomor_rekening'] ?? ($data['bank_external_account'] ?? null));
                $bprBankAcc = $data['rekening_bpr'] ?? ($data['rek_bpr'] ?? ($data['bank_bpr_account'] ?? null));

                $employee->fill([
                    'user_id' => $user->id,
                    'nik' => $data['nik'] ?? null,
                    'full_name' => $fullName,
                    'email' => $email,
                    'personal_email' => $data['email_pribadi'] ?? ($data['personal_email'] ?? null),
                    'phone' => $data['nomor_telepon'] ?? ($data['telepon'] ?? ($data['phone'] ?? '081200000000')),
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'birth_place' => $data['tempat_lahir'] ?? ($data['birth_place'] ?? null),
                    'address' => $data['alamat'] ?? ($data['address'] ?? 'Batam, Kepulauan Riau'),
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'position_id' => $position->id,
                    'job_effective_date' => $jobEffectiveDate,
                    'grade' => ! empty($data['grade']) ? (int) $data['grade'] : null,
                    'grade_effective_date' => $gradeEffectiveDate,
                    'employment_status' => $employmentStatus,
                    'join_date' => $joinDate,
                    'permanent_date' => $permanentDate,
                    'contract_end_date' => $contractEndDate,
                    'status' => $status,
                    'education_level' => $data['jenjang_pendidikan'] ?? ($data['education_level'] ?? null),
                    'education_major' => $data['jurusan'] ?? ($data['education_major'] ?? null),
                    'education_institution' => $data['universitas'] ?? ($data['sekolah'] ?? ($data['education_institution'] ?? null)),
                    'ipk' => ! empty($data['ipk']) ? (float) $data['ipk'] : null,
                    'bank_account_number' => $externalBankAcc ?: $bprBankAcc,
                    'bank_account_holder' => $data['atas_nama_rekening'] ?? ($data['bank_account_holder'] ?? $fullName),
                    'bank_bpr_account' => $bprBankAcc,
                    'bank_external_name' => $data['nama_bank_lain'] ?? ($data['bank_external_name'] ?? null),
                    'bank_external_account' => $externalBankAcc,
                    'npwp' => $data['npwp'] ?? null,
                    'bpjs_tk_number' => $data['no_bpjstk'] ?? ($data['bpjstk'] ?? ($data['bpjs_tk_number'] ?? null)),
                    'bpjs_kes_number' => $data['no_bpjs_kes'] ?? ($data['bpjs_kes'] ?? ($data['bpjs_kes_number'] ?? null)),
                    'ptkp_status' => $data['status_ptkp'] ?? ($data['ptkp'] ?? ($data['ptkp_status'] ?? null)),
                    'emergency_contact_name' => $data['kontak_darurat_nama'] ?? ($data['emergency_contact_name'] ?? null),
                    'emergency_contact_phone' => $data['kontak_darurat_nomor'] ?? ($data['emergency_contact_phone'] ?? null),
                    'emergency_contact_relation' => $data['kontak_darurat_hubungan'] ?? ($data['emergency_contact_relation'] ?? null),
                ]);
                $employee->save();

                // 8. Tambahkan data keluarga otomatis jika kontak darurat diisi
                if (! empty($employee->emergency_contact_name)) {
                    EmployeeFamily::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'name' => $employee->emergency_contact_name,
                        ],
                        [
                            'relationship' => $employee->emergency_contact_relation ?: 'Keluarga',
                            'birth_place' => $employee->birth_place ?: 'Batam',
                            'gender' => in_array($employee->emergency_contact_relation, ['Istri', 'Ibu']) ? 'Perempuan' : 'Laki-laki',
                        ]
                    );
                }

                // 9. Inisialisasi Saldo Cuti Pegawai jika belum ada
                $annualLeaveType = \App\Models\LeaveType::where('code', 'ANNUAL')->orWhere('name', 'LIKE', '%Tahunan%')->first();
                $leaveTypeId = $annualLeaveType ? $annualLeaveType->id : 1;

                $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('year', (int) date('Y'))
                    ->first();

                if (! $balance) {
                    EmployeeLeaveBalance::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveTypeId,
                        'year' => (int) date('Y'),
                        'initial_quota' => 12,
                        'used_quota' => 0,
                        'remaining_quota' => 12,
                    ]);
                }

                if ($isNew) {
                    $imported++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Proses impor selesai: {$imported} pegawai baru ditambahkan, {$updated} pegawai diperbarui.",
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses berkas CSV: ' . $e->getMessage(),
                'imported' => 0,
                'updated' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    /**
     * Eksekusi impor pegawai dari format Formulir Biodata / Dokumen Master CV BPR Banda Raya (I. DATA PRIBADI)
     */
    public function importEmployeesFromBprCvRows(array $allRows): array
    {
        $chunks = [];
        $currentChunk = [];

        foreach ($allRows as $row) {
            $joined = strtolower(implode(' ', (array) $row));
            if ($joined === '__bpr_sheet_break__' || str_contains($joined, 'data pribadi') || str_contains($joined, 'i. data pribadi')) {
                if (! empty($currentChunk)) {
                    $chunks[] = $currentChunk;
                    $currentChunk = [];
                }
            }
            if ($joined !== '__bpr_sheet_break__') {
                $currentChunk[] = $row;
            }
        }
        if (! empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        if (empty($chunks)) {
            return [
                'success' => false,
                'message' => 'Tidak ditemukan data formulir pegawai yang valid dalam berkas.',
                'imported' => 0,
                'updated' => 0,
                'errors' => [],
            ];
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($chunks as $chunk) {
                $empData = $this->parseCompleteBprCvChunk($chunk);
                if (empty($empData['full_name']) && empty($empData['nip'])) {
                    continue;
                }

                $fullName = $empData['full_name'] ?? 'Pegawai BPR';
                $nip = $empData['nip'] ?? '';
                if (empty($nip)) {
                    $nip = 'BPR-' . date('Y') . '-' . str_pad((string) (Employee::count() + 1), 4, '0', STR_PAD_LEFT);
                }

                // 1. Hubungkan Kantor Cabang
                $branch = $this->resolveBprBranch($empData['branch'] ?? 'Kantor Pusat');

                // 2. Hubungkan Departemen & Posisi
                $dept = $this->resolveBprDepartment($empData['position'] ?? 'Staf Operasional', $branch->name);
                $position = $this->resolveBprPosition($empData['position'] ?? 'Staf Operasional', $dept);

                // 3. Tangani Akun User & Domain Email (.com)
                $email = $empData['email'] ?? '';
                if (empty($email)) {
                    $cleanNip = Str::slug(str_replace(' ', '', $nip));
                    $email = "pegawai.{$cleanNip}@bprbandaraya.com";
                }
                $email = str_ireplace('@bprbandaraya.co.id', '@bprbandaraya.com', $email);

                $user = User::where('email', $email)->first();
                if (! $user) {
                    $user = User::create([
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'role' => 'employee',
                        'branch_id' => $branch->id,
                    ]);
                }

                // 4. Cari Employee berdasarkan NIP, NIK (KTP), Email, atau Nama
                $cleanNip = str_replace(' ', '', $nip);
                $employee = Employee::where('nip', $nip)
                    ->orWhereRaw('REPLACE(nip, " ", "") = ?', [$cleanNip])
                    ->orWhere('email', $email)
                    ->first();

                if (! $employee && ! empty($empData['nik'])) {
                    $employee = Employee::where('nik', $empData['nik'])->first();
                }

                if (! $employee) {
                    $employee = Employee::whereRaw('LOWER(full_name) = ?', [strtolower($fullName)])->first();
                }

                $isNew = ! $employee;
                if ($isNew) {
                    $employee = new Employee();
                    $employee->nip = $nip;
                }

                $externalBankAcc = $empData['bank_external_account'] ?? null;
                $bprBankAcc = $empData['bank_bpr_account'] ?? null;

                $employee->fill([
                    'user_id' => $user->id,
                    'nik' => $empData['nik'] ?? $employee->nik,
                    'full_name' => $fullName,
                    'email' => $email,
                    'personal_email' => $empData['personal_email'] ?? $employee->personal_email,
                    'phone' => $empData['phone'] ?? ($employee->phone ?: '081200000000'),
                    'gender' => $empData['gender'] ?? ($employee->gender ?: 'Female'),
                    'birth_date' => $empData['birth_date'] ?? $employee->birth_date,
                    'birth_place' => $empData['birth_place'] ?? $employee->birth_place,
                    'address' => $empData['address'] ?? ($employee->address ?: 'Batam, Kepulauan Riau'),
                    'branch_id' => $branch->id,
                    'department_id' => $dept->id,
                    'position_id' => $position->id,
                    'job_effective_date' => $empData['job_effective_date'] ?? ($employee->job_effective_date ?: $empData['join_date']),
                    'grade' => $empData['grade'] ?? $employee->grade,
                    'grade_effective_date' => $empData['grade_effective_date'] ?? ($employee->grade_effective_date ?: $empData['join_date']),
                    'employment_status' => $empData['employment_status'] ?? ($employee->employment_status ?: 'Permanent'),
                    'join_date' => $empData['join_date'] ?? ($employee->join_date ?: now()->toDateString()),
                    'permanent_date' => $empData['permanent_date'] ?? $employee->permanent_date,
                    'exit_date' => $empData['exit_date'] ?? $employee->exit_date,
                    'exit_reason' => $empData['exit_reason'] ?? $employee->exit_reason,
                    'status' => $empData['status'] ?? ($employee->status ?: 'Active'),
                    'education_level' => $empData['education_level'] ?? $employee->education_level,
                    'education_major' => $empData['education_major'] ?? $employee->education_major,
                    'education_institution' => $empData['education_institution'] ?? $employee->education_institution,
                    'ipk' => $empData['ipk'] ?? $employee->ipk,
                    'bank_account_number' => $externalBankAcc ?: ($bprBankAcc ?: $employee->bank_account_number),
                    'bank_account_holder' => $fullName,
                    'bank_bpr_account' => $bprBankAcc ?: $employee->bank_bpr_account,
                    'bank_external_name' => $empData['bank_external_name'] ?? $employee->bank_external_name,
                    'bank_external_account' => $externalBankAcc ?: $employee->bank_external_account,
                    'npwp' => $empData['npwp'] ?? $employee->npwp,
                    'bpjs_tk_number' => $empData['bpjs_tk_number'] ?? $employee->bpjs_tk_number,
                    'bpjs_kes_number' => $empData['bpjs_kes_number'] ?? $employee->bpjs_kes_number,
                    'ptkp_status' => $empData['ptkp_status'] ?? $employee->ptkp_status,
                    'emergency_contact_name' => $empData['emergency_contact_name'] ?? $employee->emergency_contact_name,
                    'emergency_contact_phone' => $empData['emergency_contact_phone'] ?? $employee->emergency_contact_phone,
                    'emergency_contact_relation' => $empData['emergency_contact_relation'] ?? $employee->emergency_contact_relation,
                ]);
                $employee->save();

                if ($isNew) {
                    $imported++;
                } else {
                    $updated++;
                }

                // 5. Tambahkan Data Keluarga Otomatis
                if (! empty($employee->emergency_contact_name)) {
                    EmployeeFamily::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'name' => $employee->emergency_contact_name,
                        ],
                        [
                            'relationship' => $employee->emergency_contact_relation ?: 'Keluarga',
                            'birth_place' => $employee->birth_place ?: 'Batam',
                            'gender' => in_array($employee->emergency_contact_relation, ['Istri', 'Ibu']) ? 'Perempuan' : 'Laki-laki',
                        ]
                    );
                }

                // 6. Sinkronisasi Riwayat Karir (CareerMovement)
                if (! empty($empData['careers'])) {
                    foreach ($empData['careers'] as $c) {
                        $cBranch = ! empty($c['branch']) ? $this->resolveBprBranch($c['branch']) : $branch;
                        $cDept = $this->resolveBprDepartment($c['position'] ?? '', $cBranch->name);
                        $cPos = $this->resolveBprPosition($c['position'] ?? '', $cDept);

                        CareerMovement::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'effective_date' => $c['effective_date'] ?? ($employee->join_date ?: now()->toDateString()),
                                'sk_number' => $c['sk_number'] ?? '-',
                            ],
                            [
                                'type' => $c['type'] ?? 'Mutasi Cabang',
                                'to_branch_id' => $cBranch->id,
                                'to_position_id' => $cPos->id,
                                'grade' => $c['grade'] ?? null,
                                'grade_effective_date' => $c['grade_effective_date'] ?? null,
                                'movement_notes' => $c['movement_notes'] ?? null,
                                'notes' => 'Diimpor dari Riwayat Karir Master Pegawai BPR',
                            ]
                        );
                    }
                }

                // 7. Sinkronisasi Riwayat Pelatihan (EmployeeTraining)
                if (! empty($empData['trainings'])) {
                    foreach ($empData['trainings'] as $t) {
                        if (empty($t['training_name'])) continue;
                        EmployeeTraining::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'training_name' => $t['training_name'],
                                'schedule' => $t['schedule'] ?? '-',
                            ],
                            [
                                'organizer' => $t['organizer'] ?? 'BPR Banda Raya',
                                'trainer' => $t['trainer'] ?? null,
                                'location' => $t['location'] ?? 'Batam',
                            ]
                        );
                    }
                }

                // 8. Sinkronisasi Performance Appraisal (PA)
                if (! empty($empData['appraisals'])) {
                    foreach ($empData['appraisals'] as $pa) {
                        if (empty($pa['period_year'])) continue;
                        EmployeePerformanceAppraisal::updateOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'period_year' => $pa['period_year'],
                            ],
                            [
                                'letter_grade' => $pa['letter_grade'] ?? 'B',
                                'score' => $pa['score'] ?? 0,
                                'notes' => 'Diimpor dari Penilaian Kinerja BPR',
                            ]
                        );
                    }
                }

                // 9. Inisialisasi Saldo Cuti Pegawai
                $annualLeaveType = \App\Models\LeaveType::where('code', 'ANNUAL')->orWhere('name', 'LIKE', '%Tahunan%')->first();
                $leaveTypeId = $annualLeaveType ? $annualLeaveType->id : 1;

                EmployeeLeaveBalance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveTypeId,
                        'year' => (int) date('Y'),
                    ],
                    [
                        'allocated' => 12,
                        'used' => 0,
                        'remaining' => 12,
                    ]
                );
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Migrasi pegawai format resmi BPR berhasil: {$imported} pegawai baru ditambahkan, {$updated} diperbarui.",
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses data pegawai BPR: ' . $e->getMessage(),
                'imported' => 0,
                'updated' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }

    /**
     * Parsing satu blok formulir CV / Biodata Karyawan BPR Banda Raya
     */
    public function parseCompleteBprCvChunk(array $rows): array
    {
        $emp = [
            'careers' => [],
            'trainings' => [],
            'appraisals' => [],
        ];

        $currentSection = 'I';

        foreach ($rows as $row) {
            $cleanCells = array_values(array_filter(array_map('trim', (array) $row), fn ($c) => $c !== ''));
            if (empty($cleanCells)) {
                continue;
            }

            $lineJoined = implode(' ', $cleanCells);
            if (stripos($lineJoined, 'II. DATA KELUARGA') !== false) {
                $currentSection = 'II';
                continue;
            } elseif (stripos($lineJoined, 'III. RIWAYAT KARIR') !== false) {
                $currentSection = 'III';
                continue;
            } elseif (stripos($lineJoined, 'IV. RIWAYAT PELATIHAN') !== false) {
                $currentSection = 'IV';
                continue;
            }

            if ($currentSection === 'I') {
                $pairs = $this->extractKeyValuesFromCells($cleanCells);
                foreach ($pairs as $key => $val) {
                    if (str_contains($key, 'nama') && ! isset($emp['full_name'])) {
                        $emp['full_name'] = $val;
                    } elseif ($key === 'nik') {
                        $emp['nip'] = trim(preg_replace('/\s+/', ' ', $val));
                    } elseif (str_contains($key, 'jabatan') && ! str_contains($key, 'efektif')) {
                        $emp['position'] = $val;
                    } elseif (str_contains($key, 'unit kerja')) {
                        $emp['branch'] = $val;
                    } elseif (str_contains($key, 'tgl efektif jabatan')) {
                        $emp['job_effective_date'] = $this->parseIndonesianDate($val);
                    } elseif ($key === 'grade') {
                        $emp['grade'] = (int) $val;
                    } elseif (str_contains($key, 'tgl efektif grade')) {
                        $emp['grade_effective_date'] = $this->parseIndonesianDate($val);
                    } elseif (str_contains($key, 'join date')) {
                        $emp['join_date'] = $this->parseIndonesianDate($val);
                    } elseif (str_contains($key, 'tgl permanent')) {
                        $emp['permanent_date'] = $this->parseIndonesianDate($val);
                        $emp['employment_status'] = 'Permanent';
                    } elseif (str_contains($key, 'aktif / keluar')) {
                        $emp['status'] = (stripos($val, 'keluar') !== false) ? 'Resigned' : 'Active';
                    } elseif (str_contains($key, 'tgl keluar')) {
                        $emp['exit_date'] = $this->parseIndonesianDate($val);
                    } elseif (str_contains($key, 'keterangan') && ! empty($val)) {
                        $emp['exit_reason'] = $val;
                    } elseif (str_contains($key, 'tempat lahir')) {
                        $emp['birth_place'] = $val;
                    } elseif (str_contains($key, 'tgl lahir')) {
                        $emp['birth_date'] = $this->parseIndonesianDate($val);
                    } elseif ($key === 'l/p' || str_contains($key, 'jenis kelamin')) {
                        $emp['gender'] = (stripos($val, 'perempuan') !== false || stripos($val, 'p') === 0) ? 'Female' : 'Male';
                    } elseif (str_contains($key, 'jenjang')) {
                        $emp['education_level'] = $val;
                    } elseif (str_contains($key, 'jurusan')) {
                        $emp['education_major'] = $val;
                    } elseif (str_contains($key, 'univ') || str_contains($key, 'institusi')) {
                        $emp['education_institution'] = $val;
                    } elseif ($key === 'ipk') {
                        $emp['ipk'] = (float) str_replace(',', '.', $val);
                    } elseif (str_contains($key, 'alamat')) {
                        $emp['address'] = $val;
                    } elseif (str_contains($key, 'no. hp') || str_contains($key, 'telepon')) {
                        $emp['phone'] = str_replace(' ', '', $val);
                    } elseif (str_contains($key, 'ktp')) {
                        $emp['nik'] = str_replace([' ', '-', '.'], '', $val);
                    } elseif (str_contains($key, 'npwp')) {
                        $emp['npwp'] = $val;
                    } elseif (str_contains($key, 'bpjstk')) {
                        $emp['bpjs_tk_number'] = $val;
                    } elseif (str_contains($key, 'bpjs kes')) {
                        $emp['bpjs_kes_number'] = $val;
                    } elseif (str_contains($key, 'rek. br') || str_contains($key, 'rek br')) {
                        $emp['bank_bpr_account'] = $val;
                    } elseif (str_contains($key, 'rek. mandiri') || str_contains($key, 'rek mandiri')) {
                        $emp['bank_external_name'] = 'Bank Mandiri';
                        $emp['bank_external_account'] = $val;
                    }
                }

                foreach ($cleanCells as $c) {
                    if (filter_var($c, FILTER_VALIDATE_EMAIL)) {
                        if (str_contains($c, '@bprbandaraya')) {
                            $emp['email'] = $c;
                        } else {
                            $emp['personal_email'] = $c;
                        }
                    }
                }
            } elseif ($currentSection === 'II') {
                $pairs = $this->extractKeyValuesFromCells($cleanCells);
                foreach ($pairs as $key => $val) {
                    if (str_contains($key, 'status ptkp') || $key === 'ptkp') {
                        $emp['ptkp_status'] = $val;
                    } elseif (str_contains($key, 'nama') && ! isset($emp['emergency_contact_name'])) {
                        $emp['emergency_contact_name'] = $val;
                    } elseif ((str_contains($key, 'hp') || str_contains($key, 'telp')) && ! isset($emp['emergency_contact_phone'])) {
                        $emp['emergency_contact_phone'] = $val;
                    } elseif (str_contains($key, 'hubungan') && ! isset($emp['emergency_contact_relation'])) {
                        $emp['emergency_contact_relation'] = $val;
                    }
                }
            } elseif ($currentSection === 'III') {
                if (is_numeric($cleanCells[0])) {
                    $unitKerja = $cleanCells[1] ?? '';
                    $posisi = $cleanCells[2] ?? '';
                    $tglJabatan = $this->parseIndonesianDate($cleanCells[3] ?? '');
                    $noSk = $cleanCells[4] ?? '';
                    $grade = isset($cleanCells[5]) && is_numeric($cleanCells[5]) ? (int) $cleanCells[5] : null;
                    $tglGrade = $this->parseIndonesianDate($cleanCells[6] ?? '');
                    $keterangan = $cleanCells[7] ?? '';

                    $type = 'Pengangkatan Tetap';
                    if (stripos($keterangan, 'kenaikan jabatan') !== false || stripos($keterangan, 'promosi') !== false || stripos($keterangan, 'kenaikan grade') !== false) {
                        $type = 'Promosi Jabatan';
                    } elseif (stripos($keterangan, 'mutasi penempatan') !== false || stripos($keterangan, 'cabang') !== false) {
                        $type = 'Mutasi Cabang';
                    } elseif (stripos($keterangan, 'mutasi jabatan') !== false || stripos($keterangan, 'rotasi') !== false) {
                        $type = 'Rotasi Divisi';
                    } elseif (stripos($keterangan, 'tetap') !== false || stripos($keterangan, 'pengangkatan') !== false) {
                        $type = 'Pengangkatan Tetap';
                    } elseif (stripos($keterangan, 'demosi') !== false) {
                        $type = 'Demosi';
                    } else {
                        $type = 'Mutasi Cabang';
                    }

                    $emp['careers'][] = [
                        'branch' => $unitKerja,
                        'position' => $posisi,
                        'effective_date' => $tglJabatan,
                        'sk_number' => $noSk,
                        'grade' => $grade,
                        'grade_effective_date' => $tglGrade,
                        'movement_notes' => $keterangan,
                        'type' => $type,
                    ];
                }
            } elseif ($currentSection === 'IV') {
                if (is_numeric($cleanCells[0])) {
                    $trainingName = $cleanCells[1] ?? '';
                    $schedule = $cleanCells[2] ?? '';
                    $organizer = $cleanCells[3] ?? '';
                    $trainer = $cleanCells[4] ?? '';
                    $location = $cleanCells[5] ?? '';

                    if (! empty($trainingName)) {
                        $emp['trainings'][] = [
                            'training_name' => $trainingName,
                            'schedule' => $schedule,
                            'organizer' => $organizer,
                            'trainer' => $trainer,
                            'location' => $location,
                        ];
                    }

                    $cellCount = count($cleanCells);
                    for ($ci = 5; $ci < $cellCount; $ci++) {
                        if (is_numeric($cleanCells[$ci]) && isset($cleanCells[$ci + 1]) && preg_match('/^\d{4}$/', $cleanCells[$ci + 1])) {
                            $period = (int) $cleanCells[$ci + 1];
                            $letter = $cleanCells[$ci + 2] ?? '';
                            $score = isset($cleanCells[$ci + 3]) ? (float) str_replace(',', '.', trim($cleanCells[$ci + 3])) : null;
                            $emp['appraisals'][] = [
                                'period_year' => $period,
                                'letter_grade' => $letter,
                                'score' => $score,
                            ];
                            break;
                        }
                    }
                }
            }
        }

        if (empty($emp['employment_status'])) {
            $emp['employment_status'] = ! empty($emp['permanent_date']) ? 'Permanent' : 'Contract';
        }

        return $emp;
    }

    /**
     * Mengekstrak pasangan Key-Value dari baris formulir BPR
     */
    public function extractKeyValuesFromCells(array $cleanCells): array
    {
        $pairs = [];
        $total = count($cleanCells);
        for ($i = 0; $i < $total; $i++) {
            $c = $cleanCells[$i];
            // Format 1: "Key", ":", "Value"
            if (($cleanCells[$i + 1] ?? '') === ':') {
                $key = strtolower(trim($c));
                $val = trim($cleanCells[$i + 2] ?? '');
                $pairs[$key] = $val;
                $i += 2;
                continue;
            }
            // Format 2: "Key: Value"
            if (preg_match('/^([A-Za-z0-9\s\.\/\-\(\)]+?)\s*:\s*(.*)$/u', $c, $m)) {
                $key = strtolower(trim($m[1]));
                $val = trim($m[2]);
                $pairs[$key] = $val;
                continue;
            }
        }
        return $pairs;
    }

    /**
     * Resolusi string tanggal Indonesia maupun serial number Excel ke format YYYY-MM-DD
     */
    public function parseIndonesianDate(?string $str): ?string
    {
        if (empty($str)) {
            return null;
        }
        $str = trim((string) $str);
        if (is_numeric($str) && (int) $str > 20000 && (int) $str < 60000) {
            return Carbon::createFromTimestampUTC(((int) $str - 25569) * 86400)->toDateString();
        }

        $months = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'mei' => '05', 'may' => '05', 'jun' => '06', 'jul' => '07',
            'agu' => '08', 'ags' => '08', 'aug' => '08', 'sep' => '09',
            'okt' => '10', 'oct' => '10', 'nov' => '11', 'des' => '12', 'dec' => '12',
        ];

        if (preg_match('/^(\d{1,2})[\s\-\/\.]([A-Za-z]{3,})[\s\-\/\.](\d{2,4})$/i', $str, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $monthStr = strtolower(substr($m[2], 0, 3));
            $month = $months[$monthStr] ?? '01';
            $year = (int) $m[3];
            if ($year < 100) {
                $year = ($year > 35) ? (1900 + $year) : (2000 + $year);
            }
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) {
            return $str;
        }

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $str, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        try {
            return Carbon::parse($str)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Resolusi kantor cabang BPR berdasarkan teks formulir
     */
    public function resolveBprBranch(string $rawName): Branch
    {
        $clean = trim($rawName);
        $lower = strtolower($clean);

        if (str_contains($lower, 'botania')) {
            $branch = Branch::where('name', 'LIKE', '%Botania%')->first();
        } elseif (str_contains($lower, 'aviari')) {
            $branch = Branch::where('name', 'LIKE', '%Aviari%')->first();
        } elseif (str_contains($lower, 'bengkong')) {
            $branch = Branch::where('name', 'LIKE', '%Bengkong%')->first();
        } elseif (str_contains($lower, 'tiban')) {
            $branch = Branch::where('name', 'LIKE', '%Tiban%')->first();
        } elseif (str_contains($lower, 'pusat')) {
            $branch = Branch::where('name', 'LIKE', '%Kantor Pusat%')->first();
        } else {
            $branch = Branch::where('name', 'LIKE', "%{$clean}%")->first();
        }

        if (! $branch) {
            $branch = Branch::create([
                'name' => $clean ?: 'Kantor Pusat BPR Banda Raya',
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $clean ?: 'KP'), 0, 5)) . rand(10, 99),
                'city' => 'Batam',
                'address' => 'Batam, Kepulauan Riau',
                'latitude' => 1.1185,
                'longitude' => 104.0532,
                'radius_meters' => 100,
            ]);
        }

        return $branch;
    }

    /**
     * Resolusi departemen / divisi BPR berdasarkan nama jabatan
     */
    public function resolveBprDepartment(string $posName, string $branchName = ''): Department
    {
        $lower = strtolower($posName . ' ' . $branchName);

        if (str_contains($lower, 'direktur') || str_contains($lower, 'direksi') || str_contains($lower, 'komisaris')) {
            $dept = Department::where('name', 'LIKE', '%Direksi%')->first();
        } elseif (str_contains($lower, 'kredit') || str_contains($lower, 'bisnis') || str_contains($lower, 'ao') || str_contains($lower, 'funding') || str_contains($lower, 'marketing')) {
            $dept = Department::where('name', 'LIKE', '%Kredit%')->orWhere('name', 'LIKE', '%Bisnis%')->first();
        } elseif (str_contains($lower, 'hr') || str_contains($lower, 'sdm') || str_contains($lower, 'ga') || str_contains($lower, 'human')) {
            $dept = Department::where('name', 'LIKE', '%Human%')->orWhere('name', 'LIKE', '%GA%')->first();
        } elseif (str_contains($lower, 'skai') || str_contains($lower, 'audit')) {
            $dept = Department::where('name', 'LIKE', '%SKAI%')->orWhere('name', 'LIKE', '%Audit%')->first();
        } elseif (str_contains($lower, 'kepatuhan') || str_contains($lower, 'risiko') || str_contains($lower, 'risk')) {
            $dept = Department::where('name', 'LIKE', '%Kepatuhan%')->orWhere('name', 'LIKE', '%Risiko%')->first();
        } elseif (str_contains($lower, 'it') || str_contains($lower, 'core') || str_contains($lower, 'teknologi')) {
            $dept = Department::where('name', 'LIKE', '%Teknologi%')->first();
        } else {
            $dept = Department::where('name', 'LIKE', '%Operasional%')->first();
        }

        if (! $dept) {
            $dept = Department::firstOrCreate(['name' => 'Operasional & Layanan Kas'], ['code' => 'OPS01']);
        }

        return $dept;
    }

    /**
     * Resolusi jabatan BPR
     */
    public function resolveBprPosition(string $posName, Department $dept): Position
    {
        $clean = trim($posName);
        if (empty($clean)) {
            $clean = 'Staf Operasional';
        }

        $position = Position::where('name', 'LIKE', "%{$clean}%")->first();
        if (! $position) {
            $gradeLevel = 'Staff';
            $lower = strtolower($clean);

            if (str_contains($lower, 'direktur') || str_contains($lower, 'direksi') || str_contains($lower, 'komisaris') || str_contains($lower, 'kadiv') || str_contains($lower, 'executive') || str_contains($lower, 'kepala divisi')) {
                $gradeLevel = 'Division Head';
            } elseif (str_contains($lower, 'vice president') || str_contains($lower, 'vp')) {
                $gradeLevel = 'Vice President';
            } elseif (str_contains($lower, 'asisten manajer') || str_contains($lower, 'assistant manager') || str_contains($lower, 'asst manager')) {
                $gradeLevel = 'Assistant Manager';
            } elseif (str_contains($lower, 'manager') || str_contains($lower, 'manajer') || str_contains($lower, 'pincab') || str_contains($lower, 'kabag') || str_contains($lower, 'kepala cabang') || str_contains($lower, 'pemimpin cabang') || str_contains($lower, 'kepala bagian')) {
                $gradeLevel = 'Manager';
            } elseif (str_contains($lower, 'kord') || str_contains($lower, 'supervisor') || str_contains($lower, 'spv') || str_contains($lower, 'koordinator') || str_contains($lower, 'kasie') || str_contains($lower, 'kepala seksi') || str_contains($lower, 'team leader')) {
                $gradeLevel = 'Supervisor';
            } elseif (str_contains($lower, 'officer') || str_contains($lower, 'analis') || str_contains($lower, 'account officer') || str_contains($lower, 'funding officer') || str_contains($lower, 'ao') || str_contains($lower, 'fo') || str_contains($lower, 'ro')) {
                $gradeLevel = 'Officer';
            }

            // Validasi ketat terhadap enum tabel positions untuk mencegah SQL CHECK constraint failed
            $allowedGrades = ['Staff', 'Officer', 'Supervisor', 'Assistant Manager', 'Manager', 'Vice President', 'Division Head'];
            if (! in_array($gradeLevel, $allowedGrades, true)) {
                $gradeLevel = 'Staff';
            }

            $position = Position::create([
                'department_id' => $dept->id,
                'name' => $clean,
                'grade_level' => $gradeLevel,
            ]);
        }

        return $position;
    }

    /**
     * Membaca berkas data dalam berbagai format (XLSX, XLS, CSV, TXT, PDF)
     * dan mengembalikannya sebagai array 2D (baris dan kolom).
     */
    public function parseAnyFile(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [];
        }

        // Cek 4 byte pertama (Magic Bytes)
        $fp = @fopen($filePath, 'rb');
        $magic = $fp ? fread($fp, 4) : '';
        if ($fp) {
            fclose($fp);
        }

        $lowerPath = strtolower($filePath);

        // 1. Format Modern Excel (.xlsx) / ZIP archive
        if (str_starts_with($magic, "PK\x03\x04") || str_ends_with($lowerPath, '.xlsx')) {
            $rows = $this->parseXlsxFile($filePath);
            if (! empty($rows)) {
                return $rows;
            }
        }

        // 2. Format PDF (.pdf)
        if (str_starts_with($magic, '%PDF') || str_ends_with($lowerPath, '.pdf')) {
            $rows = $this->parsePdfFile($filePath);
            if (! empty($rows)) {
                return $rows;
            }
        }

        // 3. Format Biner BIFF2/BIFF8 Excel (.xls)
        if (str_starts_with($magic, "\x09\x08") || str_starts_with($magic, "\x09\x00") || str_starts_with($magic, "\xD0\xCF") || str_ends_with($lowerPath, '.xls')) {
            $rows = $this->parseBiometricXls($filePath);
            if (! empty($rows)) {
                return $rows;
            }
        }

        // 4. Baca konten teks (CSV, TSV, HTML Table, XML Spreadsheet)
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return [];
        }
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // Bersihkan UTF-8 BOM

        // 4a. HTML Table (.xls berbasis HTML)
        if (stripos($content, '<table') !== false) {
            $rows = [];
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $content, $trMatches);
            foreach ($trMatches[1] as $trHtml) {
                preg_match_all('/<(?:td|th)[^>]*>(.*?)<\/(?:td|th)>/is', $trHtml, $tdMatches);
                $rowCells = array_map(function ($cell) {
                    $clean = html_entity_decode(strip_tags($cell), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    return trim($clean);
                }, $tdMatches[1]);

                if (! empty(array_filter($rowCells, fn ($c) => $c !== ''))) {
                    $rows[] = $rowCells;
                }
            }
            if (! empty($rows)) {
                return $rows;
            }
        }

        // 4b. XML Spreadsheet 2003 (<Workbook>)
        if (stripos($content, '<Workbook') !== false && stripos($content, '<Row') !== false) {
            $rows = [];
            preg_match_all('/<Row[^>]*>(.*?)<\/Row>/is', $content, $rowMatches);
            foreach ($rowMatches[1] as $rowXml) {
                preg_match_all('/<Data[^>]*>(.*?)<\/Data>/is', $rowXml, $cellMatches);
                $rowCells = array_map(function ($val) {
                    return trim(html_entity_decode($val, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                }, $cellMatches[1]);

                if (! empty(array_filter($rowCells, fn ($c) => $c !== ''))) {
                    $rows[] = $rowCells;
                }
            }
            if (! empty($rows)) {
                return $rows;
            }
        }

        // 4c. Format CSV / TSV / TXT
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        $rows = [];
        if (! empty($lines)) {
            $firstLine = $lines[0];
            $delimiter = ',';
            if (str_contains($firstLine, ';') && substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (str_contains($firstLine, "\t") && substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            } elseif (str_contains($firstLine, '|') && substr_count($firstLine, '|') > substr_count($firstLine, ',')) {
                $delimiter = '|';
            }

            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                $row = str_getcsv($line, $delimiter);
                $row = array_map('trim', (array) $row);
                if (! empty(array_filter($row, fn ($c) => $c !== ''))) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * Ekstraksi dan parsing berkas Excel modern (.xlsx) secara native via ZipArchive.
     */
    public function parseXlsxFile(string $filePath): array
    {
        if (! class_exists('\ZipArchive')) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $sharedStrings = [];
        if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = @simplexml_load_string($zip->getFromIndex($idx));
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Loop through all sheets in the XLSX
        $sheetIndices = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('/^xl\/worksheets\/sheet\d+\.xml$/i', $name)) {
                $sheetIndices[] = $i;
            }
        }

        // Urutkan sheet secara natural (sheet1, sheet2, ...)
        usort($sheetIndices, function ($a, $b) use ($zip) {
            $nameA = $zip->getNameIndex($a);
            $nameB = $zip->getNameIndex($b);
            preg_match('/sheet(\d+)\.xml/i', $nameA, $mA);
            preg_match('/sheet(\d+)\.xml/i', $nameB, $mB);
            return ((int) ($mA[1] ?? 0)) <=> ((int) ($mB[1] ?? 0));
        });

        if (empty($sheetIndices)) {
            $zip->close();
            return [];
        }

        $allRows = [];

        foreach ($sheetIndices as $sIdx) {
            $sheetXml = @simplexml_load_string($zip->getFromIndex($sIdx));
            if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
                continue;
            }

            $sheetRows = [];
            foreach ($sheetXml->sheetData->row as $row) {
                $rowArray = [];
                $maxCol = 0;
                foreach ($row->c as $c) {
                    $ref = (string) $c['r'];
                    preg_match('/^([A-Z]+)(\d+)$/', $ref, $m);
                    $colLetters = $m[1] ?? 'A';

                    $colIdx = 0;
                    $len = strlen($colLetters);
                    for ($k = 0; $k < $len; $k++) {
                        $colIdx = $colIdx * 26 + (ord($colLetters[$k]) - ord('A') + 1);
                    }
                    $colIdx -= 1;

                    $val = '';
                    $type = (string) $c['t'];
                    if ($type === 's') {
                        $sIndex = (int) $c->v;
                        $val = $sharedStrings[$sIndex] ?? '';
                    } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                        $val = (string) $c->is->t;
                    } else {
                        $val = (string) $c->v;
                    }

                    $rowArray[$colIdx] = trim($val);
                    if ($colIdx > $maxCol) {
                        $maxCol = $colIdx;
                    }
                }

                $normalizedRow = [];
                for ($cIdx = 0; $cIdx <= $maxCol; $cIdx++) {
                    $normalizedRow[] = $rowArray[$cIdx] ?? '';
                }
                if (! empty(array_filter($normalizedRow, fn ($v) => $v !== ''))) {
                    $sheetRows[] = $normalizedRow;
                }
            }

            if (! empty($sheetRows)) {
                if (! empty($allRows)) {
                    $allRows[] = ['__BPR_SHEET_BREAK__'];
                }
                foreach ($sheetRows as $sr) {
                    $allRows[] = $sr;
                }
            }
        }

        $zip->close();
        return $allRows;
    }

    /**
     * Ekstraksi teks dan parsing tabel / profil berkas PDF (.pdf) secara native.
     */
    public function parsePdfFile(string $filePath): array
    {
        $content = @file_get_contents($filePath);
        if (! $content) {
            return [];
        }

        $rawText = '';
        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $streamMatches)) {
            foreach ($streamMatches[1] as $stream) {
                $decompressed = @gzuncompress($stream);
                $streamData = ($decompressed !== false) ? $decompressed : $stream;

                if (preg_match_all('/BT[\r\n]+(.*?)[\r\n]+ET/s', $streamData, $btMatches)) {
                    foreach ($btMatches[1] as $bt) {
                        $line = '';
                        if (preg_match_all('/\(((?:[^\\\\\)]|\\\\.)*)\)\s*(?:Tj|\'|")/s', $bt, $tjMatches)) {
                            foreach ($tjMatches[1] as $m) {
                                $line .= stripcslashes($m) . " ";
                            }
                        }
                        if (preg_match_all('/\[(.*?)\]\s*TJ/s', $bt, $tjArrayMatches)) {
                            foreach ($tjArrayMatches[1] as $arr) {
                                if (preg_match_all('/\(((?:[^\\\\\)]|\\\\.)*)\)/s', $arr, $subParts)) {
                                    foreach ($subParts[1] as $sp) {
                                        $line .= stripcslashes($sp);
                                    }
                                }
                                $line .= " ";
                            }
                        }
                        if (trim($line) !== '') {
                            $rawText .= trim($line) . "\n";
                        }
                    }
                } else {
                    if (preg_match_all('/\(((?:[^\\\\\)]|\\\\.)*)\)\s*(?:Tj|\'|")/s', $streamData, $tjMatches)) {
                        foreach ($tjMatches[1] as $m) {
                            $rawText .= stripcslashes($m) . "\n";
                        }
                    }
                }
            }
        }

        if (trim($rawText) === '') {
            $rawText = strip_tags($content);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($rawText));
        $lines = array_values(array_filter(array_map('trim', (array) $lines)));

        // 1. Cek format Formulir Biodata Pegawai Key-Value (NIP: ..., Nama: ...)
        $kvRecords = [];
        $currentRecord = [];
        $isKv = false;
        foreach ($lines as $line) {
            if (preg_match('/^(NIP|NIK|Nama[_\s]*Lengkap|Nama|Email|Kantor[_\s]*Cabang|Cabang|Jabatan|Divisi)[\s]*[:=](.*)$/i', $line, $kvMatch)) {
                $isKv = true;
                $k = strtolower(str_replace([' ', '-'], '_', trim($kvMatch[1])));
                $v = trim($kvMatch[2]);
                if ($k === 'nip' && ! empty($currentRecord)) {
                    $kvRecords[] = $currentRecord;
                    $currentRecord = [];
                }
                $currentRecord[$k] = $v;
            }
        }
        if (! empty($currentRecord)) {
            $kvRecords[] = $currentRecord;
        }

        if ($isKv && ! empty($kvRecords)) {
            $allKeys = array_unique(array_merge(...array_map('array_keys', $kvRecords)));
            $rows = [array_values($allKeys)];
            foreach ($kvRecords as $rec) {
                $row = [];
                foreach ($allKeys as $k) {
                    $row[] = $rec[$k] ?? '';
                }
                $rows[] = $row;
            }
            return $rows;
        }

        // 2. Format Tabel Terstruktur (Dipisahkan oleh |, ;, ,, tab, atau spasi ganda)
        $rows = [];
        foreach ($lines as $line) {
            if (str_contains($line, '|')) {
                $cells = array_map('trim', explode('|', $line));
            } elseif (str_contains($line, "\t")) {
                $cells = array_map('trim', explode("\t", $line));
            } elseif (str_contains($line, ';')) {
                $cells = array_map('trim', explode(';', $line));
            } elseif (str_contains($line, ',')) {
                $cells = str_getcsv($line);
            } else {
                $cells = preg_split('/\s{2,}/', $line);
            }

            $cells = array_values(array_filter(array_map('trim', (array) $cells), fn ($c) => $c !== ''));
            if (count($cells) >= 2) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    /**
     * Membaca berkas biner BIFF2/BIFF8 hasil ekspor mesin presensi biometric Solution/ZK (.xls)
     */
    public function parseBiometricXls(string $filePath): array
    {
        $fp = @fopen($filePath, 'rb');
        if (! $fp) {
            return [];
        }

        $grid = [];
        while (! feof($fp)) {
            $hdr = fread($fp, 4);
            if (strlen($hdr) < 4) {
                break;
            }
            $u = unpack('vopcode/vlength', $hdr);
            $opcode = $u['opcode'];
            $len = $u['length'];
            $data = $len > 0 ? fread($fp, $len) : '';

            // 0x0004: BIFF2 LABEL record (row, col, attr, len, string data)
            if ($opcode === 0x0004 && $len >= 8) {
                $c = unpack('vrow/vcol/C3attr/Clen', substr($data, 0, 8));
                $str = substr($data, 8, $c['len']);
                $grid[$c['row']][$c['col']] = trim($str);
            }
        }
        fclose($fp);
        ksort($grid);

        return array_values($grid);
    }

    /**
     * Eksekusi impor log presensi dari berkas CSV, XLS, XLSX mesin cabang
     */
    public function importAttendancesFromCsv(string $filePath): array
    {
        return $this->importAttendancesFromFile($filePath);
    }

    /**
     * Eksekusi impor log presensi dari berkas CSV, XLS, atau XLSX mesin cabang terpadu
     */
    public function importAttendancesFromFile(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'success' => false,
                'message' => 'Berkas log presensi tidak ditemukan atau tidak dapat dibaca.',
                'imported' => 0,
                'errors' => [],
            ];
        }

        $rows = $this->parseAnyFile($filePath);
        if (count($rows) < 2) {
            return [
                'success' => false,
                'message' => 'Berkas presensi tidak memiliki baris data.',
                'imported' => 0,
                'errors' => [],
            ];
        }

        // Cari baris header yang mengandung nip atau tanggal / date / pin
        $headerIndex = -1;
        foreach ($rows as $idx => $r) {
            $joined = strtolower(implode(' ', (array) $r));
            if (str_contains($joined, 'nip') || str_contains($joined, 'tanggal') || str_contains($joined, 'date') || str_contains($joined, 'pin')) {
                $headerIndex = $idx;
                break;
            }
        }

        if ($headerIndex === -1 || count($rows) <= $headerIndex + 1) {
            $headerIndex = 0;
        }

        $rawHeader = $rows[$headerIndex];
        $header = array_map(fn ($h) => trim(strtolower(str_replace([' ', '-'], '_', (string) $h))), $rawHeader);
        $dataRows = array_slice($rows, $headerIndex + 1);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($dataRows as $index => $rowValues) {
                if (empty($rowValues)) {
                    continue;
                }

                $data = [];
                foreach ($header as $hIdx => $hKey) {
                    $data[$hKey] = isset($rowValues[$hIdx]) ? trim((string) $rowValues[$hIdx]) : '';
                }

                // Resolusi Identitas Pegawai (NIP atau Nama Lengkap)
                $identifier = trim(preg_replace('/\s+/', ' ', (string) ($data['nip'] ?? ($data['name'] ?? ($data['nama'] ?? ($data['nama_pegawai'] ?? ''))))));
                if (empty($identifier)) {
                    continue;
                }

                $employee = Employee::where('nip', $identifier)->first();
                if (! $employee) {
                    $employee = Employee::whereRaw('LOWER(full_name) = ?', [strtolower($identifier)])->first();
                }
                if (! $employee) {
                    $cleanName = trim(preg_replace('/,.*$/', '', $identifier));
                    $employee = Employee::whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($cleanName) . '%'])->first();
                }
                if (! $employee) {
                    $errors[] = "Baris " . ($index + 2) . ": Pegawai '{$identifier}' tidak terdaftar di sistem HRIS.";
                    continue;
                }

                // Resolusi Tanggal Presensi (Mendukung DD/MM/YYYY, DD-MM-YYYY, dan YYYY-MM-DD)
                $rawDate = trim((string) ($data['tanggal'] ?? ($data['date'] ?? '')));
                if (empty($rawDate)) {
                    $errors[] = "Baris " . ($index + 2) . ": Tanggal presensi wajib diisi.";
                    continue;
                }

                if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $rawDate, $m)) {
                    $dateParsed = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
                } else {
                    $dateParsed = Carbon::parse($rawDate)->toDateString();
                }

                // Resolusi Jam Masuk & Jam Keluar (Format titik 07.31 atau titik dua 07:31)
                $rawIn = trim((string) ($data['jam_masuk'] ?? ($data['clock_in'] ?? '')));
                $rawOut = trim((string) ($data['jam_keluar'] ?? ($data['clock_out'] ?? '')));

                $cleanIn = str_replace('.', ':', $rawIn);
                $cleanOut = str_replace('.', ':', $rawOut);

                $hasActualClockIn = ! empty($cleanIn) && $cleanIn !== '-' && $cleanIn !== '00:00' && $cleanIn !== '00:00:00';
                $hasActualClockOut = ! empty($cleanOut) && $cleanOut !== '-' && $cleanOut !== '00:00' && $cleanOut !== '00:00:00';

                $clockIn = $hasActualClockIn ? Carbon::parse("{$dateParsed} {$cleanIn}") : null;
                $clockOut = $hasActualClockOut ? Carbon::parse("{$dateParsed} {$cleanOut}") : null;

                // Jadwal Baku (On Duty) dari template mesin Solution X105 jika tersedia
                $onDutyRaw = trim((string) ($data['on_duty'] ?? ''));
                $onDutyClean = str_replace('.', ':', $onDutyRaw);
                $standardStart = (! empty($onDutyClean) && $onDutyClean !== '-' && $onDutyClean !== '00:00' && preg_match('/^\d{1,2}:\d{2}/', $onDutyClean))
                    ? Carbon::parse("{$dateParsed} {$onDutyClean}")
                    : Carbon::parse("{$dateParsed} 08:00:00");

                // Lembur Mesin (OT Time) dari template mesin Solution X105 jika tersedia
                $otRaw = trim((string) ($data['ot_time'] ?? ''));
                $otNote = (! empty($otRaw) && $otRaw !== '-' && $otRaw !== '00:00' && $otRaw !== '00.00')
                    ? " (Lembur: {$otRaw})"
                    : '';

                $fileStatus = ucfirst(strtolower(trim($data['status'] ?? 'Present')));
                $lateMinutes = 0;
                $penaltyAmount = 0;

                // 1. Cek Cuti Resmi yang disetujui pada tanggal ini
                $approvedLeave = \App\Models\LeaveRequest::where('employee_id', $employee->id)
                    ->where(fn ($q) => $q->where('status', 'LIKE', 'Approved%')->orWhere('status', 'approved'))
                    ->whereDate('start_date', '<=', $dateParsed)
                    ->whereDate('end_date', '>=', $dateParsed)
                    ->with('leaveType')
                    ->first();

                // 2. Cek Logbook Dispensasi HRD/GA pada tanggal ini
                $dispensation = \App\Models\AttendanceDispensation::where('employee_id', $employee->id)
                    ->whereDate('date', $dateParsed)
                    ->first();

                if ($approvedLeave) {
                    // Cuti resmi disetujui: status Excused/Cuti, tanpa denda
                    $status = 'Excused';
                    $lateMinutes = 0;
                    $penaltyAmount = 0;
                    $notes = '[Cuti Resmi: ' . ($approvedLeave->leaveType?->name ?? 'Cuti') . '] ' . ($approvedLeave->reason ?: 'Permohonan Cuti Disetujui');
                } elseif ($dispensation) {
                    $isLateDisp = $dispensation->isLateArrivalDispensation();
                    $dispReason = ! empty($dispensation->custom_reason)
                        ? ($dispensation->dispensation_type === 'Lainnya / Isi Sendiri' ? $dispensation->custom_reason : "{$dispensation->dispensation_type} - {$dispensation->custom_reason}")
                        : $dispensation->dispensation_type;

                    if ($isLateDisp && $hasActualClockIn) {
                        // Karyawan izin telat masuk dan tetap hadir di kantor (ada rekaman jam masuk mesin)
                        $status = 'Present'; // Hadir!
                        if ($clockIn && $clockIn->gt($standardStart)) {
                            $lateMinutes = $clockIn->diffInMinutes($standardStart);
                        }
                        $penaltyAmount = 0; // Bebas denda keterlambatan karena izin telat sah
                        $notes = "[Dispensasi Telat: {$dispReason}] Jam masuk " . ($clockIn ? $clockIn->format('H:i') : '') . " tercatat. Status Hadir (Denda ditiadakan).{$otNote}";
                    } elseif ($isLateDisp && ! $hasActualClockIn) {
                        // Izin telat masuk tapi tidak ada rekaman jam masuk mesin sama sekali
                        $status = 'Excused';
                        $lateMinutes = 0;
                        $penaltyAmount = 0;
                        $notes = "[Dispensasi Telat: {$dispReason}] Tidak ada rekaman jam masuk di mesin presensi.";
                    } else {
                        // Dispensasi Izin Penuh / Sakit seharian
                        $status = 'Excused';
                        $lateMinutes = 0;
                        $penaltyAmount = 0;
                        $notes = "[Dispensasi HRD/GA: {$dispReason}] " . ($dispensation->description ?: '');
                    }
                } else {
                    // Murni presensi mesin tanpa cuti maupun dispensasi
                    if ($hasActualClockIn) {
                        if ($clockIn->gt($standardStart)) {
                            $lateMinutes = $clockIn->diffInMinutes($standardStart);
                            $status = 'Late';
                            $penaltyAmount = $lateMinutes * 50000; // Denda standar Rp50.000 / menit
                        } else {
                            $status = 'Present';
                            $lateMinutes = 0;
                            $penaltyAmount = 0;
                        }
                        $baseNote = trim($data['catatan'] ?? ($data['notes'] ?? 'Tarik Berkas Mesin Presensi Cabang'));
                        $notes = $baseNote . $otNote;
                    } else {
                        // Tidak ada jam masuk dan tidak ada izin/cuti -> Alpa
                        $status = 'Absent';
                        $lateMinutes = 0;
                        $penaltyAmount = 0;
                        $notes = 'Tidak Ada Rekaman Mesin Presensi (Alpa)';
                    }
                }

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $dateParsed,
                    ],
                    [
                        'branch_id' => $employee->branch_id,
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => $status,
                        'late_minutes' => $lateMinutes,
                        'penalty_amount' => $penaltyAmount,
                        'notes' => $notes,
                        'lat_in' => $employee->branch?->latitude ?? 1.1185,
                        'long_in' => $employee->branch?->longitude ?? 104.0532,
                        'distance_in_meters' => 5,
                    ]
                );

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Impor log kehadiran sukses: {$imported} catatan presensi tersimpan/diperbarui.",
                'imported' => $imported,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memproses log absensi: ' . $e->getMessage(),
                'imported' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
