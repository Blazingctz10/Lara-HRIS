<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
@php
$xmlBlock = '<!--[if gte mso 9]><xml>' .
  '<' . 'x:ExcelWorkbook>' .
  '<' . 'x:ExcelWorksheets>' .
  '<' . 'x:ExcelWorksheet>' .
  '<' . 'x:Name>Rekap Presensi ' . htmlspecialchars($periodName) . '<' . '/x:Name>' .
  '<' . 'x:WorksheetOptions>' .
  '<' . 'x:DisplayGridlines/>' .
  '<' . 'x:FreezePanes/>' .
  '<' . 'x:FrozenNoSplit/>' .
  '<' . 'x:SplitHorizontal>7<' . '/x:SplitHorizontal>' .
  '<' . 'x:TopRowBottomPane>7<' . '/x:TopRowBottomPane>' .
  '<' . 'x:SplitVertical>4<' . '/x:SplitVertical>' .
  '<' . 'x:LeftColumnRightPane>4<' . '/x:LeftColumnRightPane>' .
  '<' . 'x:ActivePane>0<' . '/x:ActivePane>' .
  '<' . '/x:WorksheetOptions>' .
  '<' . '/x:ExcelWorksheet>' .
  '<' . '/x:ExcelWorksheets>' .
  '<' . '/x:ExcelWorkbook>' .
  '</xml><![endif]-->';
@endphp
{!! $xmlBlock !!}
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; margin: 0; padding: 10px; }
  table { border-collapse: collapse; width: 100%; }
  .title-main { font-size: 16px; font-weight: bold; color: #0E6838; text-transform: uppercase; }
  .subtitle { font-size: 12px; font-weight: bold; color: #27272a; margin-top: 4px; }
  .meta-info { font-size: 11px; color: #52525b; margin-top: 2px; }
  
  .th-base { border: 1px solid #14532d; font-family: Calibri, sans-serif; }
  .th-main { background-color: #0E6838; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 4px; font-size: 11px; }
  .th-sun { background-color: #b91c1c; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 4px; font-size: 11px; }
  .th-sub { background-color: #15803d; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px 2px; font-size: 10px; }
  .th-sub-sun { background-color: #dc2626; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px 2px; font-size: 10px; }
  .th-summary { background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 6px; font-size: 11px; }
  .th-summary-sub { background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px; font-size: 10px; }

  .td-cell { border: 1px solid #cbd5e1; padding: 4px 5px; font-size: 10px; text-align: center; vertical-align: middle; mso-number-format: "\@"; }
  .td-left { text-align: left; }
  .td-right { text-align: right; }
  .td-nip { font-family: "Courier New", monospace; text-align: center; }
  .td-cuti { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
  .td-izin { background-color: #fef3c7; color: #92400e; font-weight: bold; }
  .td-menunggu { background-color: #f3e8ff; color: #6b21a8; font-style: italic; }
  .td-libur { background-color: #f1f5f9; color: #94a3b8; font-weight: bold; }
  .td-late { color: #dc2626; font-weight: bold; background-color: #fee2e2; }
  .td-present { color: #047857; font-weight: 500; }
  .td-alpa { background-color: #ffe4e6; color: #be123c; font-weight: bold; }

  .sum-col { font-weight: bold; font-size: 11px; }
  .sum-hadir { color: #047857; }
  .sum-cuti { color: #1d4ed8; }
  .sum-izin { color: #b45309; }
  .sum-telat { color: #b91c1c; }
  .sum-denda { color: #b91c1c; text-align: right; }
  .sum-alpa { color: #be123c; }

  .tr-even { background-color: #ffffff; }
  .tr-odd { background-color: #f8fafc; }

  .tfoot-total { background-color: #e2e8f0; font-weight: bold; font-size: 11px; border-top: 2px solid #0E6838; border-bottom: 2px solid #0E6838; }
  .sign-box { border: 1px solid #cbd5e1; padding: 12px; text-align: center; vertical-align: top; }
</style>
</head>
<body>
<table>
  <tr>
    <td colspan="{{ 4 + ($daysInMonth * 2) + 7 }}" class="title-main">PT BPR BANDA RAYA</td>
  </tr>
  <tr>
    <td colspan="{{ 4 + ($daysInMonth * 2) + 7 }}" class="subtitle">MATRIKS REKAPITULASI PRESENSI & KEHADIRAN BULANAN (TERPADU: MESIN, CUTI & LOGBOOK)</td>
  </tr>
  <tr>
    <td colspan="{{ 4 + ($daysInMonth * 2) + 7 }}" class="meta-info">Periode: <b>{{ $periodName }}</b> | Unit Kantor: <b>{{ $selectedBranch?->name ?? 'Semua Unit Kerja' }}</b> | Dicetak Pada: {{ now()->format('d/m/Y H:i:s') }} WIB | Status Pegawai: Aktif</td>
  </tr>
  <tr><td colspan="{{ 4 + ($daysInMonth * 2) + 7 }}">&nbsp;</td></tr>
  <thead>
    <tr>
      <th rowspan="2" class="th-main th-base" style="width: 35px;">NO</th>
      <th rowspan="2" class="th-main th-base" style="width: 90px;">NIP / NIK</th>
      <th rowspan="2" class="th-main th-base" style="width: 170px;">NAMA PEGAWAI</th>
      <th rowspan="2" class="th-main th-base" style="width: 130px;">KANTOR UNIT</th>
      @foreach($daysMeta as $d => $meta)
        <th colspan="2" class="{{ $meta['is_sunday'] ? 'th-sun' : 'th-main' }} th-base" style="width: 68px;">Tgl {{ $meta['pad'] }}<br><small>{{ $meta['name'] }}</small></th>
      @endforeach
      <th colspan="6" class="th-summary th-base">AKUMULASI KEHADIRAN</th>
      <th rowspan="2" class="th-summary th-base" style="width: 220px;">CATATAN TERPADU (LOGBOOK & CUTI)</th>
    </tr>
    <tr>
      @foreach($daysMeta as $d => $meta)
        <th class="{{ $meta['is_sunday'] ? 'th-sub-sun' : 'th-sub' }} th-base">In</th>
        <th class="{{ $meta['is_sunday'] ? 'th-sub-sun' : 'th-sub' }} th-base">Out</th>
      @endforeach
      <th class="th-summary-sub th-base" style="width: 45px;">Hadir</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Cuti</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Izin</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Telat</th>
      <th class="th-summary-sub th-base" style="width: 80px;">Denda (Rp)</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Alpa</th>
    </tr>
  </thead>
  <tbody>
    @foreach($matrixRows as $row)
      <tr class="{{ $row['no'] % 2 === 0 ? 'tr-even' : 'tr-odd' }}">
        <td class="td-cell">{{ $row['no'] }}</td>
        <td class="td-cell td-nip">{{ $row['employee']->nip }}</td>
        <td class="td-cell td-left" style="font-weight: 600;">{{ $row['employee']->full_name }}</td>
        <td class="td-cell td-left">{{ $row['employee']->branch?->name ?? 'Kantor Pusat' }}</td>
        @foreach($daysMeta as $d => $meta)
          @php
            $dayInfo = $row['days'][$d] ?? ['type' => 'normal', 'in' => '-', 'out' => '-'];
          @endphp
          @if(in_array($dayInfo['type'], ['cuti', 'izin', 'libur', 'menunggu']))
            <td colspan="2" class="td-cell td-{{ $dayInfo['type'] }}">{{ $dayInfo['in'] }}</td>
          @else
            <td class="td-cell {{ $dayInfo['type'] === 'present' ? 'td-present' : ($dayInfo['type'] === 'late' ? 'td-late' : ($dayInfo['type'] === 'alpa' ? 'td-alpa' : '')) }}">{{ $dayInfo['in'] }}</td>
            <td class="td-cell {{ $dayInfo['type'] === 'present' ? 'td-present' : ($dayInfo['type'] === 'alpa' ? 'td-alpa' : '') }}">{{ $dayInfo['out'] }}</td>
          @endif
        @endforeach
        <td class="td-cell sum-col sum-hadir">{{ $row['total_hadir'] }}</td>
        <td class="td-cell sum-col sum-cuti">{{ $row['total_cuti'] }}</td>
        <td class="td-cell sum-col sum-izin">{{ $row['total_izin'] }}</td>
        <td class="td-cell sum-col sum-telat">{{ $row['total_telat'] }}</td>
        <td class="td-cell sum-col sum-denda">{{ number_format($row['total_denda'], 0, ',', '.') }}</td>
        <td class="td-cell sum-col sum-alpa">{{ $row['total_alpa'] }}</td>
        <td class="td-cell td-left" style="font-size: 9px; color: #475569;">{{ $row['notes'] }}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="tfoot-total">
      <td colspan="4" class="td-cell" style="text-align: right; font-weight: bold; padding: 8px;">TOTAL REKAPITULASI:</td>
      <td colspan="{{ $daysInMonth * 2 }}" class="td-cell" style="color: #64748b; font-style: italic;">(Akumulasi {{ count($matrixRows) }} Karyawan Aktif)</td>
      <td class="td-cell sum-col sum-hadir">{{ $grandTotals['hadir'] }}</td>
      <td class="td-cell sum-col sum-cuti">{{ $grandTotals['cuti'] }}</td>
      <td class="td-cell sum-col sum-izin">{{ $grandTotals['izin'] }}</td>
      <td class="td-cell sum-col sum-telat">{{ $grandTotals['telat'] }}</td>
      <td class="td-cell sum-col sum-denda">{{ number_format($grandTotals['denda'], 0, ',', '.') }}</td>
      <td class="td-cell sum-col sum-alpa">{{ $grandTotals['alpa'] }}</td>
      <td class="td-cell">&nbsp;</td>
    </tr>
  </tfoot>
</table>

<br><br>
<table style="width: 100%; border: none;">
  <tr>
    <td style="width: 30%; text-align: center; vertical-align: top;">
      <b>Dibuat Oleh:</b><br><br><br><br><br>
      <u>( Staf Human Capital )</u><br>
      <span style="font-size: 10px; color: #64748b;">Pengelola Presensi & HRIS</span>
    </td>
    <td style="width: 40%; text-align: center; vertical-align: top;">
      <b>Diperiksa Oleh:</b><br><br><br><br><br>
      <u>( HC & GA Division Head )</u><br>
      <span style="font-size: 10px; color: #64748b;">Human Capital & General Affair</span>
    </td>
    <td style="width: 30%; text-align: center; vertical-align: top;">
      <b>Disetujui Oleh:</b><br><br><br><br><br>
      <u>( Direktur Operasional )</u><br>
      <span style="font-size: 10px; color: #64748b;">PT BPR Banda Raya</span>
    </td>
  </tr>
</table>
</body>
</html>
