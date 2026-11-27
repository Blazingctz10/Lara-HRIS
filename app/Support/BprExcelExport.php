<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class BprExcelExport
{
    /**
     * Render a native OpenXML Excel (.xlsx) StreamedResponse for PT BPR Banda Raya.
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
        int $freezeCols = 0,
    ): StreamedResponse {
        $xlsxFilename = preg_replace('/\.xls$/i', '.xlsx', $filename) ?: ($filename . '.xlsx');
        if (! str_ends_with(strtolower($xlsxFilename), '.xlsx')) {
            $xlsxFilename .= '.xlsx';
        }

        $safeSheet = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $sheetName) ?: 'Laporan';
        $safeSheet = mb_substr($safeSheet, 0, 31);

        $rowsArray = is_array($rows) ? $rows : iterator_to_array($rows);

        return response()->streamDownload(function () use (
            $safeSheet,
            $reportTitle,
            $periodLabel,
            $columns,
            $rowsArray,
            $summaryRow,
            $freezeCols,
        ) {
            $tempFile = tempnam(sys_get_temp_dir(), 'bpr_xlsx_');
            $zip = new ZipArchive();
            $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $zip->addFromString('[Content_Types].xml', self::contentTypesXml());
            $zip->addFromString('_rels/.rels', self::relsXml());
            $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRelsXml());
            $zip->addFromString('xl/workbook.xml', self::workbookXml($safeSheet));
            $zip->addFromString('xl/styles.xml', self::stylesXml());
            $zip->addFromString(
                'xl/worksheets/sheet1.xml',
                self::worksheetXml($reportTitle, $periodLabel, $columns, $rowsArray, $summaryRow, $freezeCols)
            );

            $zip->close();

            readfile($tempFile);
            @unlink($tempFile);
        }, $xlsxFilename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    private static function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function colLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private static function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private static function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private static function workbookXml(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::xmlEscape($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private static function stylesXml(): string
    {
        // numFmtId 164 = Rupiah, 165 = Integer #,##0
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="2">'
            . '<numFmt numFmtId="164" formatCode="&quot;Rp &quot;#,##0;-&quot;Rp &quot;#,##0;&quot;-&quot;"/>'
            . '<numFmt numFmtId="165" formatCode="#,##0"/>'
            . '</numFmts>'
            . '<fonts count="11">'
            . '<font><sz val="10"/><color rgb="FF1E293B"/><name val="Calibri"/></font>' // 0: body
            . '<font><b/><sz val="15"/><color rgb="FF0E6838"/><name val="Calibri"/></font>' // 1: title
            . '<font><b/><sz val="11"/><color rgb="FF1F2937"/><name val="Calibri"/></font>' // 2: subtitle
            . '<font><sz val="9"/><color rgb="FF64748B"/><name val="Calibri"/></font>' // 3: meta
            . '<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>' // 4: table head
            . '<font><b/><sz val="10"/><color rgb="FF0F172A"/><name val="Calibri"/></font>' // 5: bold body
            . '<font><sz val="10"/><color rgb="FF0F172A"/><name val="Courier New"/></font>' // 6: nip
            . '<font><b/><sz val="10"/><color rgb="FF0E6838"/><name val="Calibri"/></font>' // 7: total green
            . '<font><b/><sz val="10"/><color rgb="FF047857"/><name val="Calibri"/></font>' // 8: badge ok
            . '<font><b/><sz val="10"/><color rgb="FFB45309"/><name val="Calibri"/></font>' // 9: badge warn
            . '<font><b/><sz val="10"/><color rgb="FFB91C1C"/><name val="Calibri"/></font>' // 10: badge danger
            . '</fonts>'
            . '<fills count="8">'
            . '<fill><patternFill patternType="none"/></fill>' // 0
            . '<fill><patternFill patternType="gray125"/></fill>' // 1
            . '<fill><patternFill patternType="solid"><fgColor rgb="FF0E6838"/></patternFill></fill>' // 2: header green
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>' // 3: odd row
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFE8F3EC"/></patternFill></fill>' // 4: total row
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFD1FAE5"/></patternFill></fill>' // 5: badge ok
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>' // 6: badge warn
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>' // 7: badge danger
            . '</fills>'
            . '<borders count="4">'
            . '<border><left/><right/><top/><bottom/><diagonal/></border>' // 0: none
            . '<border><left style="thin"><color rgb="FF084022"/></left><right style="thin"><color rgb="FF084022"/></right><top style="thin"><color rgb="FF084022"/></top><bottom style="thin"><color rgb="FF084022"/></bottom><diagonal/></border>' // 1: head
            . '<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border>' // 2: cell
            . '<border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="medium"><color rgb="FF0E6838"/></top><bottom style="thin"><color rgb="FF0E6838"/></bottom><diagonal/></border>' // 3: total
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="30">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>' // 0: default
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 1: title
            . '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 2: subtitle
            . '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/>' // 3: meta
            . '<xf numFmtId="0" fontId="4" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>' // 4: head
            // Even rows (fillId=0, borderId=2)
            . '<xf numFmtId="49" fontId="0" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 5: text left
            . '<xf numFmtId="49" fontId="0" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 6: text center
            . '<xf numFmtId="49" fontId="5" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 7: bold left
            . '<xf numFmtId="49" fontId="5" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 8: bold center
            . '<xf numFmtId="49" fontId="6" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 9: nip
            . '<xf numFmtId="165" fontId="0" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 10: num
            . '<xf numFmtId="165" fontId="5" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 11: num bold
            . '<xf numFmtId="164" fontId="0" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 12: rp
            . '<xf numFmtId="164" fontId="5" fillId="0" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 13: rp bold
            // Odd rows (fillId=3, borderId=2)
            . '<xf numFmtId="49" fontId="0" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 14: text left odd
            . '<xf numFmtId="49" fontId="0" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 15: text center odd
            . '<xf numFmtId="49" fontId="5" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 16: bold left odd
            . '<xf numFmtId="49" fontId="5" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 17: bold center odd
            . '<xf numFmtId="49" fontId="6" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 18: nip odd
            . '<xf numFmtId="165" fontId="0" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 19: num odd
            . '<xf numFmtId="165" fontId="5" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 20: num bold odd
            . '<xf numFmtId="164" fontId="0" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 21: rp odd
            . '<xf numFmtId="164" fontId="5" fillId="3" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 22: rp bold odd
            // Badges (23..25)
            . '<xf numFmtId="49" fontId="8" fillId="5" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 23: ok
            . '<xf numFmtId="49" fontId="9" fillId="6" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 24: warn
            . '<xf numFmtId="49" fontId="10" fillId="7" borderId="2" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 25: danger
            // Total row (26..29)
            . '<xf numFmtId="49" fontId="7" fillId="4" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>' // 26: total left
            . '<xf numFmtId="49" fontId="7" fillId="4" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>' // 27: total center
            . '<xf numFmtId="165" fontId="7" fillId="4" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 28: total num
            . '<xf numFmtId="164" fontId="7" fillId="4" borderId="3" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>' // 29: total rp
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private static function resolveStyleAndType(string $rawClass, bool $isOdd, bool $isTotal): array
    {
        if ($isTotal) {
            if (str_contains($rawClass, 'td-rp')) {
                return [29, true];
            }
            if (str_contains($rawClass, 'td-num')) {
                return [28, true];
            }
            if (str_contains($rawClass, 'td-center')) {
                return [27, false];
            }

            return [26, false];
        }

        if (str_contains($rawClass, 'badge-ok')) {
            return [23, false];
        }
        if (str_contains($rawClass, 'badge-warn')) {
            return [24, false];
        }
        if (str_contains($rawClass, 'badge-danger')) {
            return [25, false];
        }

        $offset = $isOdd ? 9 : 0;
        $isBold = str_contains($rawClass, 'td-bold');

        if (str_contains($rawClass, 'td-rp')) {
            return [($isBold ? 13 : 12) + $offset, true];
        }
        if (str_contains($rawClass, 'td-num')) {
            return [($isBold ? 11 : 10) + $offset, true];
        }
        if (str_contains($rawClass, 'td-nip')) {
            return [9 + $offset, false];
        }
        if (str_contains($rawClass, 'td-center')) {
            return [($isBold ? 8 : 6) + $offset, false];
        }
        if ($isBold) {
            return [7 + $offset, false];
        }

        return [5 + $offset, false];
    }

    private static function worksheetXml(
        string $reportTitle,
        string $periodLabel,
        array $columns,
        array $rows,
        ?array $summaryRow,
        int $freezeCols = 0,
    ): string {
        $colCount = max(1, count($columns));
        $lastCol = self::colLetter($colCount);

        // Compute auto-fit column widths
        $colWidths = [];
        foreach ($columns as $cIdx => $col) {
            $colWidths[$cIdx] = max(8, mb_strlen($col['label']) + 4);
        }

        foreach ($rows as $row) {
            foreach ($row as $cIdx => $cell) {
                $len = mb_strlen((string) $cell['value']);
                $cls = $cell['class'] ?? '';
                if (str_contains($cls, 'td-rp')) {
                    $len += 6; // "Rp " + thousands separators
                } elseif (str_contains($cls, 'td-num')) {
                    $len += 3;
                }
                $colWidths[$cIdx] = min(42, max($colWidths[$cIdx] ?? 10, $len + 3));
            }
        }

        if ($summaryRow !== null) {
            foreach ($summaryRow as $cIdx => $cell) {
                $len = mb_strlen((string) $cell['value']) + 5;
                $colWidths[$cIdx] = min(42, max($colWidths[$cIdx] ?? 10, $len));
            }
        }

        $colsXml = '<cols>';
        foreach ($colWidths as $cIdx => $width) {
            $num = $cIdx + 1;
            $colsXml .= '<col min="' . $num . '" max="' . $num . '" width="' . $width . '" customWidth="1"/>';
        }
        $colsXml .= '</cols>';

        $sheetData = '<sheetData>';

        // Row 1: Company Name
        $sheetData .= '<row r="1" ht="24" customHeight="1"><c r="A1" t="inlineStr" s="1"><is><t>PT BPR BANDA RAYA</t></is></c></row>';
        // Row 2: Report Title
        $sheetData .= '<row r="2" ht="18" customHeight="1"><c r="A2" t="inlineStr" s="2"><is><t>' . self::xmlEscape($reportTitle) . '</t></is></c></row>';
        // Row 3: Meta
        $meta = "Periode: {$periodLabel} | Dicetak: " . now()->format('d/m/Y H:i') . ' WIB';
        $sheetData .= '<row r="3" ht="16" customHeight="1"><c r="A3" t="inlineStr" s="3"><is><t>' . self::xmlEscape($meta) . '</t></is></c></row>';
        // Row 4: Blank spacer
        $sheetData .= '<row r="4" ht="8" customHeight="1"/>';

        // Row 5: Header
        $sheetData .= '<row r="5" ht="26" customHeight="1">';
        foreach ($columns as $cIdx => $col) {
            $ref = self::colLetter($cIdx + 1) . '5';
            $sheetData .= '<c r="' . $ref . '" t="inlineStr" s="4"><is><t>' . self::xmlEscape($col['label']) . '</t></is></c>';
        }
        $sheetData .= '</row>';

        $rowNum = 6;
        foreach ($rows as $idx => $row) {
            $isOdd = ($idx % 2 === 0);
            $sheetData .= '<row r="' . $rowNum . '" ht="20" customHeight="1">';
            foreach ($row as $cIdx => $cell) {
                $ref = self::colLetter($cIdx + 1) . $rowNum;
                [$styleId, $isNumeric] = self::resolveStyleAndType($cell['class'] ?? '', $isOdd, false);
                $val = $cell['value'];
                if ($isNumeric && is_numeric($val)) {
                    $sheetData .= '<c r="' . $ref . '" t="n" s="' . $styleId . '"><v>' . (0 + $val) . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $ref . '" t="inlineStr" s="' . $styleId . '"><is><t>' . self::xmlEscape((string) $val) . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
            $rowNum++;
        }

        if ($summaryRow !== null) {
            $sheetData .= '<row r="' . $rowNum . '" ht="22" customHeight="1">';
            foreach ($summaryRow as $cIdx => $cell) {
                $ref = self::colLetter($cIdx + 1) . $rowNum;
                [$styleId, $isNumeric] = self::resolveStyleAndType($cell['class'] ?? '', false, true);
                $val = $cell['value'];
                if ($isNumeric && is_numeric($val)) {
                    $sheetData .= '<c r="' . $ref . '" t="n" s="' . $styleId . '"><v>' . (0 + $val) . '</v></c>';
                } else {
                    $sheetData .= '<c r="' . $ref . '" t="inlineStr" s="' . $styleId . '"><is><t>' . self::xmlEscape((string) $val) . '</t></is></c>';
                }
            }
            $sheetData .= '</row>';
        }

        $sheetData .= '</sheetData>';

        $mergeXml = '<mergeCells count="3">'
            . '<mergeCell ref="A1:' . $lastCol . '1"/>'
            . '<mergeCell ref="A2:' . $lastCol . '2"/>'
            . '<mergeCell ref="A3:' . $lastCol . '3"/>'
            . '</mergeCells>';

        $freezeCols = max(0, min($colCount - 1, $freezeCols));
        $topLeftCell = self::colLetter($freezeCols + 1) . '6';
        $paneAttr = $freezeCols > 0
            ? 'xSplit="' . $freezeCols . '" ySplit="5" topLeftCell="' . $topLeftCell . '" activePane="bottomRight" state="frozen"'
            : 'ySplit="5" topLeftCell="A6" activePane="bottomLeft" state="frozen"';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView tabSelected="1" workbookViewId="0">'
            . '<pane ' . $paneAttr . '/>'
            . '</sheetView></sheetViews>'
            . $colsXml
            . $sheetData
            . $mergeXml
            . '</worksheet>';
    }
}
