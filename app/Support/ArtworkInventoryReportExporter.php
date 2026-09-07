<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class ArtworkInventoryReportExporter
{
    public static function downloadXlsx(Collection $artworks, array $summary, array $filters): BinaryFileResponse
    {
        $path = storage_path('app/private/exports/artwork-report-'.now()->format('Ymd-His').'.xlsx');
        self::ensureDirectory($path);

        self::buildXlsx($path, $artworks, $filters);

        return response()
            ->download($path, 'artwork-report-'.now()->format('Ymd-His').'.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }

    public static function downloadPdf(Collection $artworks, array $summary, array $filters): BinaryFileResponse
    {
        $path = storage_path('app/private/exports/artwork-report-'.now()->format('Ymd-His').'.pdf');
        self::ensureDirectory($path);

        file_put_contents($path, self::pdf($artworks, $summary, $filters));

        return response()
            ->download($path, 'artwork-report-'.now()->format('Ymd-His').'.pdf', [
                'Content-Type' => 'application/pdf',
            ])
            ->deleteFileAfterSend();
    }

    private static function money(mixed $value, string $currency): string
    {
        return strtoupper($currency) === 'USD'
            ? 'USD '.number_format((float) $value, 2)
            : 'BDT '.number_format((float) $value, 0);
    }

    private static function artDetails(mixed $artwork): string
    {
        return $artwork->title."\n"
            .($artwork->artist?->name ?: 'Unknown Artist')."\n"
            .($artwork->year ?: 'Unknown Year').' | '
            .($artwork->style?->name ?: 'No style').' | '
            .($artwork->medium?->name ?: 'No medium');
    }

    private static function priceDetails(mixed $artwork, string $currency): string
    {
        $isUsd = strtoupper($currency) === 'USD';
        $regularPrice = $isUsd ? $artwork->usd_price : $artwork->price;
        $sellingPrice = $isUsd ? $artwork->usd_selling_price : $artwork->selling_price;
        $regular = 'Regular- '.(blank($regularPrice) ? '-' : self::money($regularPrice, $currency));

        if (blank($sellingPrice)) {
            return $regular;
        }

        return $regular."\nSell- ".self::money($sellingPrice, $currency);
    }

    private static function ensureDirectory(string $path): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    private static function pdf(Collection $artworks, array $summary, array $filters): string
    {
        $pages = [[]];
        $images = [];
        $pageWidth = 792;
        $pageHeight = 612;
        $margin = 34;
        $y = $pageHeight - $margin;

        self::rect($pages[0], $margin, $y - 42, $pageWidth - ($margin * 2), 38, '34456f');
        self::text($pages[0], $margin + 18, $y - 28, 'ART STORY - ARTWORK REPORT', 18, true, 'ffffff');
        self::text($pages[0], $pageWidth - 190, $y - 26, now()->format('M d, Y h:i A'), 9, false, 'ffffff');
        $y -= 62;

        $filterLine = 'Artist: '.$filters['artist'].' | Year: '.$filters['year'].' | Status: '.$filters['status'];
        self::text($pages[0], $margin, $y, $filterLine, 9, false, '475569');
        $y -= 34;

        $detailColumns = [
            ['label' => 'Artwork Image', 'width' => 100],
            ['label' => 'Art Details', 'width' => 260],
            ['label' => 'BDT Price', 'width' => 135],
            ['label' => 'USD Price', 'width' => 135],
            ['label' => 'Status', 'width' => 94],
        ];
        self::tableHeader($pages[array_key_last($pages)], $detailColumns, $margin, $y);
        $y -= 28;

        foreach ($artworks as $artwork) {
            if ($y < 48) {
                $pages[] = [];
                $y = $pageHeight - $margin;
                self::tableHeader($pages[array_key_last($pages)], $detailColumns, $margin, $y);
                $y -= 28;
            }

            self::tableRow($pages[array_key_last($pages)], $detailColumns, [
                self::publicImagePath($artwork->image_path),
                self::artDetails($artwork),
                self::priceDetails($artwork, 'BDT'),
                self::priceDetails($artwork, 'USD'),
                ucfirst((string) $artwork->status),
            ], $margin, $y, $images);
            $y -= 42;
        }

        return self::buildPdf($pages, $pageWidth, $pageHeight, $images);
    }

    private static function buildXlsx(string $path, Collection $artworks, array $filters): void
    {
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $images = [];
        $rows = [];
        $rowNumber = 1;
        $rows[] = self::xlsxRow($rowNumber++, ['ART Story - Artwork Report'], 1, 32);
        $rows[] = self::xlsxRow($rowNumber++, ['Generated at', now()->timezone(config('app.timezone'))->format('M d, Y h:i A')], 4, 22);
        $rows[] = self::xlsxRow($rowNumber++, ['Artist', $filters['artist'], 'Year', $filters['year'], 'Status', $filters['status']], 4, 24);
        $rows[] = self::xlsxRow($rowNumber++, [], 0, 8);
        $rows[] = self::xlsxRow($rowNumber++, ['Artwork Image', 'Art Details', 'BDT Price', 'USD Price', 'Status'], 2, 28);

        foreach ($artworks as $artwork) {
            $imagePath = self::publicImagePath($artwork->image_path);
            $image = $imagePath ? self::thumbnailJpeg($imagePath, 74, 74) : null;

            if ($image) {
                $mediaName = 'image'.(count($images) + 1).'.jpg';
                $images[] = [
                    'name' => $mediaName,
                    'data' => $image['data'],
                    'row' => $rowNumber,
                    'width' => $image['width'],
                    'height' => $image['height'],
                ];
            }

            $rows[] = self::xlsxRow($rowNumber++, [
                '',
                self::artDetails($artwork),
                self::priceDetails($artwork, 'BDT'),
                self::priceDetails($artwork, 'USD'),
                ucfirst((string) $artwork->status),
            ], 3, 70);
        }

        $hasImages = count($images) > 0;
        $zip->addFromString('[Content_Types].xml', self::xlsxContentTypes($hasImages));
        $zip->addFromString('_rels/.rels', self::xlsxPackageRels());
        $zip->addFromString('docProps/core.xml', self::xlsxCore());
        $zip->addFromString('docProps/app.xml', self::xlsxApp());
        $zip->addFromString('xl/workbook.xml', self::xlsxWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::xlsxWorkbookRels());
        $zip->addFromString('xl/styles.xml', self::xlsxStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::xlsxSheet($rows, $hasImages));

        if ($hasImages) {
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', self::xlsxSheetRels());
            $zip->addFromString('xl/drawings/drawing1.xml', self::xlsxDrawing($images));
            $zip->addFromString('xl/drawings/_rels/drawing1.xml.rels', self::xlsxDrawingRels($images));

            foreach ($images as $image) {
                $zip->addFromString('xl/media/'.$image['name'], $image['data']);
            }
        }

        $zip->close();
    }

    private static function tableHeader(array &$commands, array $columns, float $x, float $y): void
    {
        $currentX = $x;

        foreach ($columns as $column) {
            self::rect($commands, $currentX, $y - 24, $column['width'], 24, '111827', '111827');
            self::text($commands, $currentX + 6, $y - 15, $column['label'], 8, true, 'ffffff');
            $currentX += $column['width'];
        }
    }

    private static function tableRow(array &$commands, array $columns, array $values, float $x, float $y, array &$images): void
    {
        $currentX = $x;
        $rowHeight = 38;

        foreach ($columns as $index => $column) {
            self::rect($commands, $currentX, $y - $rowHeight, $column['width'], $rowHeight, 'ffffff', 'e5e7eb');

            if ($index === 0 && is_string($values[$index] ?? null) && is_file($values[$index])) {
                self::image($commands, $images, $values[$index], $currentX + 7, $y - 34, 26, 26);
                $currentX += $column['width'];

                continue;
            }

            $lines = explode("\n", (string) ($values[$index] ?? '-'));
            foreach (array_slice($lines, 0, 3) as $lineIndex => $line) {
                self::text(
                    $commands,
                    $currentX + 6,
                    $y - 12 - ($lineIndex * 11),
                    self::clip($line, $index === 1 ? 52 : 24),
                    $lineIndex === 0 && $index === 1 ? 8 : 7,
                    $lineIndex === 0 && $index === 1,
                    $lineIndex === 1 && $index === 1 ? '34456f' : '111827',
                );
            }

            $currentX += $column['width'];
        }
    }

    private static function clip(string $value, int $length): string
    {
        return strlen($value) > $length ? substr($value, 0, $length - 3).'...' : $value;
    }

    private static function publicImagePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);

        return is_file($fullPath) ? $fullPath : null;
    }

    private static function thumbnailJpeg(string $path, int $maxWidth, int $maxHeight): ?array
    {
        $source = @match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'webp' => imagecreatefromwebp($path),
            'png' => imagecreatefrompng($path),
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            default => null,
        };

        if (! $source) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min($maxWidth / max($width, 1), $maxHeight / max($height, 1), 1);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagejpeg($target, null, 88);
        $data = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return $data ? ['data' => $data, 'width' => $targetWidth, 'height' => $targetHeight] : null;
    }

    private static function image(array &$commands, array &$images, string $path, float $x, float $y, float $maxWidth, float $maxHeight): void
    {
        $image = self::thumbnailJpeg($path, (int) ($maxWidth * 3), (int) ($maxHeight * 3));

        if (! $image) {
            return;
        }

        $name = 'Im'.(count($images) + 1);
        $images[$name] = $image;
        $width = min($maxWidth, $image['width']);
        $height = min($maxHeight, $image['height']);
        $commands[] = sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /%s Do Q', $width, $height, $x, $y, $name);
    }

    private static function xlsxRow(int $row, array $values, int $style = 0, ?int $height = null): string
    {
        $heightAttribute = $height ? ' ht="'.$height.'" customHeight="1"' : '';
        $cells = '';

        foreach ($values as $index => $value) {
            if ($value === '') {
                continue;
            }

            $cell = self::xlsxColumn($index + 1).$row;
            $styleAttribute = $style ? ' s="'.$style.'"' : '';
            $text = self::xml((string) $value);
            $cells .= '<c r="'.$cell.'" t="inlineStr"'.$styleAttribute.'><is><t xml:space="preserve">'.$text.'</t></is></c>';
        }

        return '<row r="'.$row.'"'.$heightAttribute.'>'.$cells.'</row>';
    }

    private static function xlsxColumn(int $number): string
    {
        $column = '';

        while ($number > 0) {
            $number--;
            $column = chr(65 + ($number % 26)).$column;
            $number = intdiv($number, 26);
        }

        return $column;
    }

    private static function xlsxSheet(array $rows, bool $hasImages): string
    {
        $drawing = $hasImages ? '<drawing r:id="rId1"/>' : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheetViews><sheetView workbookViewId="0"><pane ySplit="5" topLeftCell="A6" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<cols><col min="1" max="1" width="18" customWidth="1"/><col min="2" max="2" width="48" customWidth="1"/><col min="3" max="4" width="24" customWidth="1"/><col min="5" max="5" width="18" customWidth="1"/></cols>'
            .'<sheetData>'.implode('', $rows).'</sheetData>'
            .'<autoFilter ref="A5:E5"/>'
            .'<mergeCells count="1"><mergeCell ref="A1:E1"/></mergeCells>'
            .$drawing.'</worksheet>';
    }

    private static function xlsxContentTypes(bool $hasImages): string
    {
        $imageType = $hasImages ? '<Default Extension="jpg" ContentType="image/jpeg"/><Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>' : '';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'.$imageType
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }

    private static function xlsxPackageRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private static function xlsxWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Artwork Report" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private static function xlsxWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function xlsxSheetRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/>'
            .'</Relationships>';
    }

    private static function xlsxDrawing(array $images): string
    {
        $anchors = '';

        foreach ($images as $index => $image) {
            $row = $image['row'] - 1;
            $id = $index + 1;
            $anchors .= '<xdr:twoCellAnchor editAs="oneCell">'
                .'<xdr:from><xdr:col>0</xdr:col><xdr:colOff>95250</xdr:colOff><xdr:row>'.$row.'</xdr:row><xdr:rowOff>95250</xdr:rowOff></xdr:from>'
                .'<xdr:to><xdr:col>0</xdr:col><xdr:colOff>857250</xdr:colOff><xdr:row>'.$row.'</xdr:row><xdr:rowOff>800100</xdr:rowOff></xdr:to>'
                .'<xdr:pic><xdr:nvPicPr><xdr:cNvPr id="'.$id.'" name="Artwork '.$id.'"/><xdr:cNvPicPr/></xdr:nvPicPr>'
                .'<xdr:blipFill><a:blip r:embed="rId'.$id.'"/><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'
                .'<xdr:spPr><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></xdr:spPr></xdr:pic><xdr:clientData/></xdr:twoCellAnchor>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .$anchors.'</xdr:wsDr>';
    }

    private static function xlsxDrawingRels(array $images): string
    {
        $relationships = '';

        foreach ($images as $index => $image) {
            $relationships .= '<Relationship Id="rId'.($index + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/'.self::xml($image['name']).'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$relationships.'</Relationships>';
    }

    private static function xlsxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="5"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="18"/><color rgb="FF111827"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font><font><sz val="11"/><color rgb="FF111827"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FF34456F"/><name val="Calibri"/></font></fonts>'
            .'<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF111827"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFE5E7EB"/></left><right style="thin"><color rgb="FFE5E7EB"/></right><top style="thin"><color rgb="FFE5E7EB"/></top><bottom style="thin"><color rgb="FFE5E7EB"/></bottom><diagonal/></border></borders>'
            .'<cellXfs count="5"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="4" fillId="3" borderId="0" xfId="0" applyFill="1" applyAlignment="1"><alignment vertical="center"/></xf></cellXfs>'
            .'</styleSheet>';
    }

    private static function xlsxCore(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:title>ART Story - Artwork Report</dc:title><dc:creator>ART Story</dc:creator><cp:lastModifiedBy>ART Story</cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">'.now()->toAtomString().'</dcterms:created></cp:coreProperties>';
    }

    private static function xlsxApp(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>ART Story</Application></Properties>';
    }

    private static function xml(string $value): string
    {
        return str_replace(["\r\n", "\n", "\r"], '&#10;', htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
    }

    private static function text(array &$commands, float $x, float $y, string $text, int $size = 9, bool $bold = false, string $color = '111827'): void
    {
        [$r, $g, $b] = self::rgb($color);
        $font = $bold ? 'F2' : 'F1';
        $commands[] = sprintf('%.3F %.3F %.3F rg BT /%s %d Tf %.2F %.2F Td (%s) Tj ET', $r, $g, $b, $font, $size, $x, $y, self::escape($text));
    }

    private static function rect(array &$commands, float $x, float $y, float $width, float $height, string $fill, ?string $stroke = null): void
    {
        [$fr, $fg, $fb] = self::rgb($fill);
        $command = sprintf('%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f', $fr, $fg, $fb, $x, $y, $width, $height);

        if ($stroke) {
            [$sr, $sg, $sb] = self::rgb($stroke);
            $command .= sprintf(' %.3F %.3F %.3F RG %.2F %.2F %.2F %.2F re S', $sr, $sg, $sb, $x, $y, $width, $height);
        }

        $commands[] = $command;
    }

    private static function rgb(string $hex): array
    {
        return [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ];
    }

    private static function escape(string $text): string
    {
        $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text) ?: $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function buildPdf(array $pages, int $pageWidth, int $pageHeight, array $images = []): string
    {
        $objects = [];
        $pagesObjectNumber = 2;
        $fontRegularObjectNumber = 3;
        $fontBoldObjectNumber = 4;
        $nextObjectNumber = 5;
        $pageObjectNumbers = [];
        $imageObjectNumbers = [];

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[$fontRegularObjectNumber] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBoldObjectNumber] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        foreach ($images as $name => $image) {
            $imageObjectNumber = $nextObjectNumber++;
            $imageObjectNumbers[$name] = $imageObjectNumber;
            $objects[$imageObjectNumber] = "<< /Type /XObject /Subtype /Image /Width {$image['width']} /Height {$image['height']} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ".strlen($image['data'])." >>\nstream\n{$image['data']}\nendstream";
        }

        $xObjects = collect($imageObjectNumbers)
            ->map(fn (int $number, string $name): string => "/{$name} {$number} 0 R")
            ->implode(' ');

        foreach ($pages as $commands) {
            $content = implode("\n", $commands);
            $contentObjectNumber = $nextObjectNumber++;
            $pageObjectNumber = $nextObjectNumber++;
            $pageObjectNumbers[] = $pageObjectNumber;

            $objects[$contentObjectNumber] = '<< /Length '.strlen($content)." >>\nstream\n{$content}\nendstream";
            $xObjectResources = $xObjects ? " /XObject << {$xObjects} >>" : '';
            $objects[$pageObjectNumber] = "<< /Type /Page /Parent {$pagesObjectNumber} 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 {$fontRegularObjectNumber} 0 R /F2 {$fontBoldObjectNumber} 0 R >>{$xObjectResources} >> /Contents {$contentObjectNumber} 0 R >>";
        }

        $objects[$pagesObjectNumber] = '<< /Type /Pages /Kids ['.collect($pageObjectNumbers)->map(fn (int $number): string => "{$number} 0 R")->implode(' ').'] /Count '.count($pageObjectNumbers).' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= "{$number} 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
