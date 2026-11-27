<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\CareerMovement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOrganizationOct26Command extends Command
{
    protected $signature = 'hris:sync-org-oct26';

    protected $description = "Bersihkan duplikat jabatan dan selaraskan Departemen, Jabatan, Grade, serta Cabang dengan Struktur Organisasi Okt'26 PT BPR Banda Raya.";

    public function handle(): int
    {
        DB::transaction(function () {
            // 1. Sinkronisasi Cabang (Bengkong sudah menjadi KC Bengkong per Okt'26)
            $bengkong = Branch::where('name', 'K. Kas Bengkong')->first();
            if ($bengkong) {
                $bengkong->update([
                    'name' => 'KC Bengkong',
                    'type' => 'Branch',
                ]);
                $this->info('Cabang K. Kas Bengkong diperbarui menjadi KC Bengkong (Branch).');
            }

            // Hapus Kantor Kas kosong jika tidak memiliki pegawai sama sekali
            $emptyKas = Branch::where('name', 'Kantor Kas')
                ->whereDoesntHave('employees')
                ->first();
            if ($emptyKas) {
                $emptyKas->delete();
                $this->info('Cabang kosong "Kantor Kas" dihapus.');
            }

            // 2. Pastikan Departemen Baku tersedia
            $depts = [
                'DIR01' => 'Direksi & Dewan Komisaris',
                'SKAI01' => 'SKAI & Audit Internal',
                'KPMR01' => 'Kepatuhan & Manajemen Risiko',
                'OPS01' => 'Operasional & Layanan Kas',
                'HRGA01' => 'Human Resources & GA',
                'FIN01' => 'Keuangan & Akuntansi',
                'BKP01' => 'Bisnis, Kredit & Pemasaran',
                'LEG01' => 'Legal & Collection',
            ];

            $deptIds = [];
            foreach ($depts as $code => $name) {
                $dept = Department::firstOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'is_active' => true]
                );
                $deptIds[$name] = $dept->id;
            }

            // 3. Gabungkan Jabatan Duplikat & Typo ke Nama Kanonik
            $mergeMap = [
                'Account Ofiicer' => 'Account Officer',
                'Kord. Operasional' => 'Kord. Operational',
                'Koord. Operational' => 'Kord. Operational',
                'Koordinator Operasional' => 'Kord. Operational',
                'PJS Kord. Operational' => 'Kord. Operational',
                'Plt. Kord. Operational' => 'Kord. Operational',
                'Ka. Kantor Kas & Customer Service' => 'Ka. Kantor Kas & CS',
                'Staff Audit Internal' => 'Staff Audit',
                'PE Koord. Internal Audit' => 'PE Audit Internal',
                'Koord. Internal Audit' => 'PE Audit Internal',
                'PE Kepatuhan dan MR' => 'PE Kepatuhan dan Manajemen Risiko',
                'PE Kepatuhan & MR' => 'PE Kepatuhan dan Manajemen Risiko',
                'Staff Kepatuhan & MR' => 'PE Kepatuhan dan Manajemen Risiko',
                'Ldr. Collector' => 'Leader Collector',
                'Admin GA' => 'GA & Sekretaris',
                'Plt. Pimpinan Cabang' => 'Plt. Manajer Cabang',
                'Pimpinan Cabang' => 'Manajer Cabang',
                'General Affair' => 'GA & Sekretaris',
                'Staf Operasional' => 'Service Assistant',
            ];

            $mergedCount = 0;
            foreach ($mergeMap as $aliasName => $canonicalName) {
                $aliasPositions = Position::where('name', $aliasName)->get();
                if ($aliasPositions->isEmpty()) {
                    continue;
                }

                $canonical = Position::firstOrCreate(
                    ['name' => $canonicalName],
                    ['department_id' => $deptIds['Operasional & Layanan Kas'], 'grade_level' => 'Staff', 'is_active' => true]
                );

                foreach ($aliasPositions as $alias) {
                    if ($alias->id === $canonical->id) {
                        continue;
                    }

                    Employee::where('position_id', $alias->id)->update(['position_id' => $canonical->id]);
                    CareerMovement::where('from_position_id', $alias->id)->update(['from_position_id' => $canonical->id]);
                    CareerMovement::where('to_position_id', $alias->id)->update(['to_position_id' => $canonical->id]);

                    $alias->delete();
                    $mergedCount++;
                }
            }
            $this->info("Jabatan duplikat/typo digabungkan: {$mergedCount} entri.");

            // 4. Selaraskan Department & Grade Level sesuai Struktur Organisasi Okt'26
            $rules = [
                // Direksi & Dewan Komisaris
                'Direktur Utama' => ['Direksi & Dewan Komisaris', 'Division Head'],
                'Direktur' => ['Direksi & Dewan Komisaris', 'Division Head'],
                'Direktur Kepatuhan' => ['Direksi & Dewan Komisaris', 'Division Head'],

                // SKAI & Audit Internal
                'PE Audit Internal' => ['SKAI & Audit Internal', 'Supervisor'],
                'Staff Audit' => ['SKAI & Audit Internal', 'Staff'],
                'Internal Control' => ['SKAI & Audit Internal', 'Officer'],

                // Kepatuhan & Manajemen Risiko
                'PE Kepatuhan dan Manajemen Risiko' => ['Kepatuhan & Manajemen Risiko', 'Supervisor'],

                // Human Resources & GA (termasuk IT per Struktur Okt'26)
                'HRD & GA' => ['Human Resources & GA', 'Supervisor'],
                'GA & Sekretaris' => ['Human Resources & GA', 'Staff'],
                'IT Staff' => ['Human Resources & GA', 'Staff'],
                'Driver' => ['Human Resources & GA', 'Staff'],
                'Security' => ['Human Resources & GA', 'Staff'],
                'Pramubakti' => ['Human Resources & GA', 'Staff'],

                // Operasional & Layanan Kas (termasuk Manajer Cabang, Front Liner & Div. Credit Support)
                'Manager Operational' => ['Operasional & Layanan Kas', 'Manager'],
                'Manajer Cabang' => ['Operasional & Layanan Kas', 'Manager'],
                'Plt. Manajer Cabang' => ['Operasional & Layanan Kas', 'Manager'],
                'Kord. Operational' => ['Operasional & Layanan Kas', 'Supervisor'],
                'Ldr. Credit Support' => ['Operasional & Layanan Kas', 'Supervisor'],
                'Ka. Kantor Kas & CS' => ['Operasional & Layanan Kas', 'Supervisor'],
                'Customer Service' => ['Operasional & Layanan Kas', 'Staff'],
                'Teller' => ['Operasional & Layanan Kas', 'Staff'],
                'Service Assistant' => ['Operasional & Layanan Kas', 'Staff'],
                'Prabakti' => ['Operasional & Layanan Kas', 'Staff'],
                'Appraisal' => ['Operasional & Layanan Kas', 'Staff'],
                'Admin Kredit' => ['Operasional & Layanan Kas', 'Staff'],
                'Analis Kredit' => ['Operasional & Layanan Kas', 'Officer'],

                // Keuangan & Akuntansi
                'Kord. Accounting' => ['Keuangan & Akuntansi', 'Supervisor'],
                'Accounting' => ['Keuangan & Akuntansi', 'Staff'],
                'Accounting Kredit' => ['Keuangan & Akuntansi', 'Staff'],

                // Bisnis, Kredit & Pemasaran
                'Mgr. Marketing' => ['Bisnis, Kredit & Pemasaran', 'Manager'],
                'Manager Kredit' => ['Bisnis, Kredit & Pemasaran', 'Manager'],
                'Manajer Bisnis' => ['Bisnis, Kredit & Pemasaran', 'Manager'],
                'Ldr. Funding' => ['Bisnis, Kredit & Pemasaran', 'Supervisor'],
                'Ldr. Account Officer' => ['Bisnis, Kredit & Pemasaran', 'Supervisor'],
                'Team Leader AO' => ['Bisnis, Kredit & Pemasaran', 'Supervisor'],
                'Account Officer' => ['Bisnis, Kredit & Pemasaran', 'Officer'],
                'Funding Officer' => ['Bisnis, Kredit & Pemasaran', 'Officer'],
                'AO Emas' => ['Bisnis, Kredit & Pemasaran', 'Officer'],
                'Funding' => ['Bisnis, Kredit & Pemasaran', 'Officer'],
                'Funding Tabungan' => ['Bisnis, Kredit & Pemasaran', 'Officer'],

                // Legal & Collection (Div. TPKB)
                'Leader Collector' => ['Legal & Collection', 'Supervisor'],
                'Leader Litigasi' => ['Legal & Collection', 'Supervisor'],
                'Legal Officer' => ['Legal & Collection', 'Officer'],
                'Legal Staff' => ['Legal & Collection', 'Staff'],
                'Collector' => ['Legal & Collection', 'Staff'],
                'Admin Collection' => ['Legal & Collection', 'Staff'],
                'TPKB' => ['Legal & Collection', 'Staff'],
            ];

            $updatedCount = 0;
            foreach ($rules as $posName => [$deptName, $gradeLevel]) {
                $pos = Position::where('name', $posName)->first();
                if ($pos && isset($deptIds[$deptName])) {
                    $pos->update([
                        'department_id' => $deptIds[$deptName],
                        'grade_level' => $gradeLevel,
                        'is_active' => true,
                    ]);
                    $updatedCount++;
                }
            }
            $this->info("Departemen & grade jabatan diselaraskan: {$updatedCount} jabatan.");
        });

        return self::SUCCESS;
    }
}
