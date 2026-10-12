<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permohonan Cuti - {{ $employee->full_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #f8fafc;
        }

        .paper-container {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        @media print {
            html, body {
                margin: 0 !important;
                padding: 10mm 15mm !important;
                background: #ffffff !important;
            }
            .paper-container {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-logo-cell {
            width: 140px;
            vertical-align: middle;
            text-align: left;
        }

        .header-logo {
            max-height: 58px;
            width: auto;
            display: block;
        }

        .header-title-cell {
            text-align: center;
            vertical-align: middle;
            padding-right: 140px; /* Menjaga teks judul tetap simetris tepat di tengah kertas */
        }

        .header-company {
            font-size: 12pt;
            font-weight: bold;
            color: #8b3a0f; /* Warna oranye kecokelatan resmi logo BPR Banda Raya */
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }

        .header-form {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .box-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000000;
            margin-bottom: 12px;
        }

        .box-table td {
            padding: 5px 8px;
            vertical-align: middle;
            font-size: 10.5pt;
        }

        .border-bottom {
            border-bottom: 1.5px solid #000000;
        }

        .border-top {
            border-top: 1.5px solid #000000;
        }

        .section-header {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 6px;
        }

        .checkbox-container {
            display: inline-block;
            width: 13px;
            height: 13px;
            border: 1.2px solid #000000;
            text-align: center;
            line-height: 12px;
            font-size: 9pt;
            font-weight: bold;
            margin-right: 5px;
            vertical-align: middle;
        }

        .grid-leave-types {
            width: 100%;
            margin-bottom: 8px;
        }

        .grid-leave-types td {
            padding: 3px 6px;
            font-size: 10pt;
            vertical-align: middle;
        }

        .fill-line {
            display: inline-block;
            border-bottom: 1px dotted #000000;
            padding: 0 4px;
            font-weight: bold;
        }

        .nik-box {
            display: inline-block;
            border: 1px solid #000000;
            width: 16px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            font-family: monospace;
            font-size: 10.5pt;
            font-weight: bold;
            margin-right: 1px;
        }

        .btn-print-bar {
            text-align: right;
            margin-bottom: 15px;
        }

        .btn-print {
            background: #0E6838;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            font-size: 10.5pt;
        }
    </style>
</head>
<body>

    <div class="paper-container">
        <!-- Action Bar (Tidak Tercetak) -->
        <div class="btn-print-bar no-print">
            <button class="btn-print" onclick="window.print()">
                &#128438; Cetak Formulir Cuti Resmi (Print / Save PDF)
            </button>
            <div style="font-size: 8.5pt; color: #64748b; margin-top: 5px;">
                *Tips Cetak: Pada jendela print browser, uncheck/hilangkan centang pada opsi <strong>"Headers and footers"</strong> agar header & footer otomatis browser (tanggal, URL web) tidak ikut tercetak.
            </div>
        </div>

        <!-- Header Resmi dengan Logo BPR Banda Raya -->
        <table class="header-table">
            <tr>
                <td class="header-logo-cell">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo BPR Banda Raya" class="header-logo">
                </td>
                <td class="header-title-cell">
                    <div class="header-company">BANK PEREKONOMIAN RAKYAT BANDA RAYA</div>
                    <div class="header-form">FORMULIR PERMOHONAN CUTI</div>
                </td>
            </tr>
        </table>

        <!-- Identitas Pemohon -->
        <table class="box-table">
            <tr>
                <td style="width: 12%;">NIK</td>
                <td style="width: 38%;">: 
                    @php
                        $cleanNip = str_replace(['-', ' '], '', $employee->nip ?? '');
                        $chars = str_split(substr($cleanNip, 0, 10));
                    @endphp
                    @foreach($chars as $c)
                        <span class="nik-box">{{ $c }}</span>
                    @endforeach
                </td>
                <td style="width: 15%;">Bagian</td>
                <td style="width: 35%;">: <strong>{{ strtoupper($employee->branch?->name ?? 'KANTOR PUSAT') }}</strong></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <strong>{{ strtoupper($employee->full_name) }}</strong></td>
                <td>Jabatan</td>
                <td>: <strong>{{ strtoupper($employee->position?->name ?? 'STAF') }}</strong></td>
            </tr>
        </table>

        <!-- BAGIAN I: DIISI OLEH PEGAWAI YBS -->
        <div style="border: 1.5px solid #000000; padding: 10px; margin-bottom: 12px;">
            <div class="section-header">Diisi oleh pegawai ybs. Mengajukan cuti:</div>
            
            @php
                $code = $leaveRequest->leaveType?->code ?? '';
            @endphp

            <table class="grid-leave-types">
                <tr>
                    <td style="width: 45%;">
                        <span class="checkbox-container">{{ $code === 'ANNUAL' ? 'X' : '' }}</span> Cuti Tahunan
                    </td>
                    <td style="width: 55%;">
                        <span class="checkbox-container">{{ $code === 'PATERNITY' ? 'X' : '' }}</span> Cuti istri melahirkan (2 HK)
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="checkbox-container">{{ $code === 'LONG_LEAVE' ? 'X' : '' }}</span> Cuti Besar (6 HK)
                    </td>
                    <td>
                        <span class="checkbox-container">{{ $code === 'RELIGIOUS_LEAVE' ? 'X' : '' }}</span> Cuti menjalankan ibadah agama
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="checkbox-container">{{ $code === 'BEREAVEMENT' ? 'X' : '' }}</span> Cuti duka
                    </td>
                    <td>
                        <span class="checkbox-container">{{ $code === 'UNPAID_LEAVE' ? 'X' : '' }}</span> Cuti diluar tanggungan perusahaan
                    </td>
                </tr>
            </table>

            <div style="margin-top: 8px; line-height: 1.8;">
                <div>Untuk keperluan : <span class="fill-line" style="min-width: 80%;">{{ $leaveRequest->reason }}</span></div>
                <div>
                    Dari Tanggal : <span class="fill-line" style="min-width: 25%;">{{ $leaveRequest->start_date ? $leaveRequest->start_date->format('d/m/Y') : '' }}</span>
                    Sampai Dengan Tanggal : <span class="fill-line" style="min-width: 25%;">{{ $leaveRequest->end_date ? $leaveRequest->end_date->format('d/m/Y') : '' }}</span>
                    (Total: <strong>{{ $leaveRequest->total_days }} Hari Kerja</strong>)
                </div>
                <div>Alamat yang dapat dihubungi selama menjalani cuti : <span class="fill-line" style="min-width: 60%;">{{ $leaveRequest->leave_address ?: 'Sesuai domisili / tempat tinggal terdata' }}</span></div>
                <div>Nomor telpon : <span class="fill-line" style="min-width: 35%;">{{ $leaveRequest->contact_phone ?: ($employee->phone ?: '-') }}</span></div>
            </div>

            <div style="margin-top: 20px; text-align: right; padding-right: 40px;">
                <div>P. Batam, {{ $leaveRequest->created_at ? $leaveRequest->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
                <div style="height: 45px;"></div>
                <div style="text-decoration: underline; font-weight: bold;">{{ strtoupper($employee->full_name) }}</div>
                <div style="font-size: 9.5pt;">Pegawai Pemohon</div>
            </div>
        </div>

        <!-- BAGIAN II: DIISI OLEH PERSONALIA (HRD & GA) -->
        <div style="border: 1.5px solid #000000; padding: 10px; margin-bottom: 12px;">
            <div class="section-header">Diisi oleh Personalia</div>
            
            <table style="width: 100%;">
                <tr>
                    <td style="width: 55%; vertical-align: top; line-height: 1.6;">
                        <div>Sisa hak cuti sampai saat ini : <strong>{{ str_pad($initialQuota, 2, '0', STR_PAD_LEFT) }} HK periode {{ $year }}</strong></div>
                        <div style="margin-left: 15px;">
                            Catatan &nbsp;&nbsp;&nbsp;&nbsp;: dipotong &nbsp;: <strong>{{ str_pad($leaveRequest->total_days, 2, '0', STR_PAD_LEFT) }} HK</strong><br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sisa &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ str_pad(max(0, $initialQuota - $leaveRequest->total_days), 2, '0', STR_PAD_LEFT) }} HK</strong>
                        </div>
                    </td>
                    <td style="width: 45%; text-align: center; vertical-align: bottom;">
                        <div>Batam, {{ date('d/m/Y') }}</div>
                        <div style="height: 40px; line-height: 40px; font-style: italic; color: #0E6838; font-weight: bold;">
                            @if($leaveRequest->approved_hrd_by || in_array($leaveRequest->status, ['Approved_HR', 'Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris']))
                                [ Terverifikasi Sistem HRD & GA ]
                            @endif
                        </div>
                        <div style="border-top: 1px solid #000000; display: inline-block; width: 180px; padding-top: 3px; font-weight: bold;">
                            HRD & GA
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- BAGIAN III: PERSETUJUAN & OTORISASI BERJENJANG -->
        <div style="border: 1.5px solid #000000; padding: 10px;">
            <div style="margin-bottom: 10px;">
                <span class="checkbox-container">{{ in_array($leaveRequest->status, ['Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris', 'Approved_HR']) ? 'X' : '' }}</span> Disetujui &nbsp;&nbsp;&nbsp;&nbsp;
                <span class="checkbox-container">{{ $leaveRequest->status === 'Rejected' ? 'X' : '' }}</span> Ditolak &nbsp;&nbsp;&nbsp;&nbsp;
                Sampai tanggal : <span class="fill-line" style="min-width: 150px;">{{ $leaveRequest->end_date ? $leaveRequest->end_date->format('d/m/Y') : '' }}</span>
            </div>

            <table style="width: 100%; text-align: center; margin-top: 15px;">
                <tr>
                    <td style="width: 33%; vertical-align: bottom;">
                        <div style="height: 45px; line-height: 45px; font-style: italic; color: #0E6838; font-size: 9pt;">
                            @if($leaveRequest->status !== 'Pending') [ Terverifikasi ] @endif
                        </div>
                        <div style="border-bottom: 1px solid #000000; font-weight: bold; display: inline-block; min-width: 150px;">
                            {{ $leaveRequest->employee->department?->name ? 'Leader ' . $leaveRequest->employee->department->name : 'Atasan Langsung' }}
                        </div>
                        <div style="font-size: 9.5pt;">Atasan Langsung / Kabag</div>
                    </td>

                    <td style="width: 34%; vertical-align: bottom;">
                        <div style="height: 45px; line-height: 45px; font-style: italic; color: #0E6838; font-size: 9pt;">
                            @if(in_array($leaveRequest->status, ['Approved_Direksi', 'Approved_HRD', 'Approved_Komisaris'])) [ Disetujui Digital ] @endif
                        </div>
                        <div style="border-bottom: 1px solid #000000; font-weight: bold; display: inline-block; min-width: 150px;">
                            {{ $leaveRequest->isDireksiRequest() ? 'Kepala Bagian HRD' : 'Direktur Operasional' }}
                        </div>
                        <div style="font-size: 9.5pt;">{{ $leaveRequest->isDireksiRequest() ? 'HRD (Persetujuan Cuti Direksi)' : 'Direktur' }}</div>
                    </td>

                    <td style="width: 33%; vertical-align: bottom;">
                        <div style="height: 45px; line-height: 45px; font-style: italic; color: #0E6838; font-size: 9pt;">
                            @if(in_array($leaveRequest->status, ['Approved_Direksi', 'Approved_Komisaris'])) [ Otorisasi Final ] @endif
                        </div>
                        <div style="border-bottom: 1px solid #000000; font-weight: bold; display: inline-block; min-width: 150px;">
                            {{ $leaveRequest->isDireksiRequest() && $leaveRequest->total_days > 2 ? 'Dewan Komisaris' : 'Direktur Utama' }}
                        </div>
                        <div style="font-size: 9.5pt;">{{ $leaveRequest->isDireksiRequest() && $leaveRequest->total_days > 2 ? 'Dewan Komisaris' : 'Direktur Utama' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
