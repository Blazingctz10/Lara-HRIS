<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

class BprExcelExport
{
    /**
     * Render a styled corporate BPR Banda Raya Excel (.xls) StreamedResponse.
     *
     * @param  string  $filename
     * @param  string  $sheetName
     * @param  string  $reportTitle
     * @param  string  $periodLabel
     * @param  array<int, array{label: string, class?: string}>  $columns
     * @param  iterable<int, array<int, array{value: string|int|float, class?: string}>>  $rows
     * @param  array<int, array{value: string|int|float, class?: string}>|null  $summaryRow
     */
    public static function stream(
        string $filename,
        string $sheetName,
        string $reportTitle,
        string $periodLabel,
        array $columns,
        iterable $rows,
        ?array $summaryRow = null,
    ): StreamedResponse {
        $colCount = count($columns);
        $safeSheet = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $sheetName) ?: 'Laporan';
        $safeSheet = mb_substr($safeSheet, 0, 31);

        return response()->streamDownload(function () use (
            $safeSheet,
            $reportTitle,
            $periodLabel,
            $columns,
            $rows,
            $summaryRow,
            $colCount
        ) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            $headerHtml = '<!DOCTYPE html>
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
    <x:Name>' . htmlspecialchars($safeSheet) . '</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:FreezePanes/>
     <x:FrozenNoSplit/>
     <x:SplitHorizontal>5</x:SplitHorizontal>
     <x:TopRowBottomPane>5</x:TopRowBottomPane>
     <x:ActivePane>0</x:ActivePane>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; }
  table { border-collapse: collapse; width: 100%; }
  .title-main { font-size: 15px; font-weight: bold; color: #0E6838; }
  .subtitle { font-size: 11px; font-weight: bold; color: #1F2937; }
  .meta { font-size: 10px; color: #64748B; }
  .th-head { background-color: #0E6838; color: #FFFFFF; font-weight: bold; text-align: center; border: 1px solid #084022; padding: 7px 10px; font-size: 11px; white-space: nowrap; }
  .td-cell { border: 1px solid #CBD5E1; padding: 6px 9px; font-size: 10px; vertical-align: middle; }
  .td-center { text-align: center; }
  .td-right { text-align: right; }
  .td-bold { font-weight: bold; }
  .td-text { mso-number-format: "\@"; white-space: nowrap; }
  .td-nip { font-family: "Courier New", monospace; text-align: center; mso-number-format: "\@"; white-space: nowrap; }
  .td-num { text-align: right; mso-number-format: "\#\,\#\#0"; white-space: nowrap; }
  .td-rp { text-align: right; mso-number-format: "\0022Rp \0022\#\,\#\#0;\0022-Rp \0022\#\,\#\#0;\0022-\0022"; white-space: nowrap; }
  .tr-odd { background-color: #F8FAFC; }
  .tr-even { background-color: #FFFFFF; }
  .tr-total td { background-color: #E8F3EC; font-weight: bold; color: #0E6838; border-top: 2px solid #0E6838; }
  .badge-ok { color: #047857; font-weight: bold; background-color: #D1FAE5; text-align: center; }
  .badge-warn { color: #B45309; font-weight: bold; background-color: #FEF3C7; text-align: center; }
  .badge-danger { color: #B91C1C; font-weight: bold; background-color: #FEE2E2; text-align: center; }
</style>
</head>
<body>
<table>
  <tr><td colspan="' . $colCount . '" class="title-main">PT BPR BANDA RAYA</td></tr>
  <tr><td colspan="' . $colCount . '" class="subtitle">' . htmlspecialchars($reportTitle) . '</td></tr>
  <tr><td colspan="' . $colCount . '" class="meta">Periode: ' . htmlspecialchars($periodLabel) . ' | Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB</td></tr>
  <tr><td colspan="' . $colCount . '">&nbsp;</td></tr>
  <thead>
    <tr>';

            foreach ($columns as $col) {
                $headerHtml .= '<th class="th-head">' . htmlspecialchars($col['label']) . '</th>';
            }

            $headerHtml .= '</tr></thead><tbody>';
            fputs($handle, $headerHtml);

            $renderCell = static function (array $cell): string {
                $rawClass = $cell['class'] ?? '';
                $cls = trim('td-cell ' . $rawClass);
                $inlineStyle = '';

                if (str_contains($rawClass, 'td-rp')) {
                    $inlineStyle = ' style="mso-number-format:\'\0022Rp \0022#,##0;\0022-Rp \0022#,##0;\0022-\0022\'"';
                } elseif (str_contains($rawClass, 'td-num')) {
                    $inlineStyle = ' style="mso-number-format:\'#,##0\'"';
                } elseif (str_contains($rawClass, 'td-nip') || str_contains($rawClass, 'td-text')) {
                    $inlineStyle = ' style="mso-number-format:\'\@\'"';
                }

                return '<td class="' . htmlspecialchars($cls) . '"' . $inlineStyle . '>' . htmlspecialchars((string) $cell['value']) . '</td>';
            };

            $rowIndex = 1;
            foreach ($rows as $row) {
                $rowClass = ($rowIndex++ % 2 === 0) ? 'tr-even' : 'tr-odd';
                $tr = '<tr class="' . $rowClass . '">';
                foreach ($row as $cell) {
                    $tr .= $renderCell($cell);
                }
                $tr .= '</tr>';
                fputs($handle, $tr);
            }

            if ($summaryRow !== null) {
                $tr = '<tr class="tr-total">';
                foreach ($summaryRow as $cell) {
                    $tr .= $renderCell($cell);
                }
                $tr .= '</tr>';
                fputs($handle, $tr);
            }

            fputs($handle, '</tbody></table></body></html>');
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
