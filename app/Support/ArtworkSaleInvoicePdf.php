<?php

namespace App\Support;

use App\Models\ArtworkSale;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArtworkSaleInvoicePdf
{
    public static function download(ArtworkSale $sale): BinaryFileResponse
    {
        $path = self::file($sale);

        return response()
            ->download($path, ($sale->invoice_number ?: 'artwork-invoice') . '.pdf', ['Content-Type' => 'application/pdf'])
            ->deleteFileAfterSend();
    }

    public static function file(ArtworkSale $sale): string
    {
        $sale->loadMissing(['artwork.style', 'artwork.subjectStyle', 'artwork.medium', 'artwork.size', 'artist', 'buyer', 'paymentMethod']);
        $sale->invoice_number ??= ArtworkSale::makeInvoiceNumber();
        $sale->saveQuietly();

        $path = storage_path('app/private/invoices/' . $sale->invoice_number . '.pdf');
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($path, self::pdf($sale));

        return $path;
    }

    private static function pdf(ArtworkSale $sale): string
    {
        $commands = [];
        $images = [];
        $site = SiteSettings::current();
        $items = $sale->displayLineItems();
        $subtotal = $sale->subtotal();
        $taxAmount = (float) ($sale->tax_amount ?? 0);
        $totalPaid = $sale->totalPaid();
        $width = 595;
        $height = 842;
        $margin = 42;
        $y = $height - $margin;
        $logoPath = self::siteLogoPath($site);
        $isPaid = $totalPaid > 0 || $sale->artwork?->status === 'sold';

        self::rect($commands, 0, 0, $width, $height, 'ffffff');
        self::rect($commands, 0, $height - 12, $width, 12, '111827');

        if ($logoPath) {
            self::image($commands, $images, $logoPath, $margin, $height - 82, 118, 44, false);
        } else {
            self::text($commands, $margin, $height - 62, $site?->site_title ?: 'ART Story', 23, true, '111827');
        }

        self::text($commands, $width - 191, $height - 58, 'INVOICE', 30, true, '111827');
        self::text($commands, $width - 191, $height - 81, 'Invoice No. ' . ($sale->invoice_number ?: '-'), 10, true, '111827');
        self::text($commands, $width - 191, $height - 99, 'Invoice Date: ' . ($sale->sold_at?->format('M d, Y') ?: now()->format('M d, Y')), 9, false, '64748b');
        $y -= 118;

        self::text($commands, $margin, $y - 8, 'Customer Details', 11, true, '111827');
        self::text($commands, $margin, $y - 30, $sale->buyer_name ?: 'Walk-in Customer', 13, true);
        self::text($commands, $margin, $y - 48, $sale->buyer_designation ?: '-', 9, false, '64748b');
        self::text($commands, $margin, $y - 64, collect([$sale->buyer_phone, $sale->buyer_email])->filter()->implode(' | ') ?: '-', 8, false, '64748b');

        self::text($commands, $width - $margin - 190, $y - 8, 'Sale Details', 11, true, '111827');
        self::data($commands, $width - $margin - 190, $y - 30, 'Paid By', $sale->paid_by ?: '-');
        self::data($commands, $width - $margin - 190, $y - 48, 'Status', 'Confirmed');
        self::data($commands, $width - $margin - 190, $y - 66, 'Items', $sale->lineItemsLabel());
        self::data($commands, $width - $margin - 190, $y - 84, 'Currency', $sale->currency ?: 'BDT');
        $y -= 116;

        self::text($commands, $margin, $y - 8, 'Purchased Artworks', 14, true, '111827');
        $y -= 30;

        self::rect($commands, $margin, $y - 30, $width - ($margin * 2), 30, '111827', '111827');
        self::text($commands, $margin + 16, $y - 19, 'Image', 9, true, 'ffffff');
        self::text($commands, $margin + 74, $y - 19, 'Artwork', 9, true, 'ffffff');
        self::text($commands, $width - 184, $y - 19, 'Amount', 9, true, 'ffffff');
        $y -= 30;

        foreach ($items->take(4) as $index => $item) {
            self::rect($commands, $margin, $y - 62, $width - ($margin * 2), 62, $index % 2 === 0 ? 'ffffff' : 'f8fafc', 'e2e8f0');

            $imagePath = filled($item['image_path'] ?? null) ? Storage::disk('public')->path($item['image_path']) : null;
            if ($imagePath && is_file($imagePath)) {
                self::image($commands, $images, $imagePath, $margin + 14, $y - 51, 42, 42);
            } else {
                self::rect($commands, $margin + 14, $y - 51, 42, 42, 'f8fafc', 'e2e8f0');
                self::text($commands, $margin + 20, $y - 30, 'No img', 6, true, '94a3b8');
            }

            self::text($commands, $margin + 74, $y - 20, $item['title'] ?? 'Untitled', 10, true);
            self::text($commands, $margin + 74, $y - 38, collect([
                $item['artist_name'] ?? 'Unknown Artist',
                $item['year'] ?? null,
                $item['medium'] ?? null,
                $item['size'] ?? null,
            ])->filter()->implode(' | '), 8, false, '64748b');
            self::text($commands, $width - 184, $y - 33, self::money($item['sold_price'] ?? 0, $sale->currency), 11, true);
            $y -= 62;
        }

        if ($items->count() > 4) {
            self::text($commands, $margin + 16, $y - 18, '+' . ($items->count() - 4) . ' more artwork item(s) included in this purchase.', 8, false, '64748b');
            $y -= 26;
        }

        $y -= 24;

        self::rect($commands, $width - $margin - 188, $y - 84, 188, 84, 'f8fafc', 'e2e8f0');
        self::text($commands, $width - $margin - 170, $y - 22, 'Subtotal', 9, true, '64748b');
        self::text($commands, $width - $margin - 88, $y - 22, self::money($subtotal, $sale->currency), 9, true);
        self::text($commands, $width - $margin - 170, $y - 41, 'Tax ' . ((float) ($sale->tax_percentage ?? 0)) . '%', 9, true, '64748b');
        self::text($commands, $width - $margin - 88, $y - 41, self::money($taxAmount, $sale->currency), 9, true);
        self::line($commands, $width - $margin - 170, $y - 54, $width - $margin - 18, $y - 54, 'e2e8f0', .7);
        self::text($commands, $width - $margin - 170, $y - 68, 'Total Paid', 10, true, '64748b');
        self::text($commands, $width - $margin - 88, $y - 68, self::money($totalPaid, $sale->currency), 13, true);

        if ($isPaid) {
            self::paidSeal($commands, $images, $margin + 8, $y - 84);
        }

        $signatureX = $width - 242;
        self::line($commands, $signatureX, 94, $width - $margin, 94, '111827', .8);
        self::text($commands, $signatureX + 42, 76, 'Authorized Signature', 9, true, '111827');

        self::line($commands, $margin, 56, $width - $margin, 56, 'e5e7eb', .8);
        self::text($commands, $margin, 39, 'Thank you for supporting ' . ($site?->site_title ?: 'ART Story') . '.', 9, true, '111827');
        self::text($commands, $margin, 25, collect([$site?->contact_phone, $site?->contact_email, $site?->address])->filter()->implode(' | ') ?: 'Issued by ART Story for artwork purchase confirmation.', 7, false, '64748b');

        return self::buildPdf([$commands], $width, $height, $images);
    }

    private static function money(mixed $value, ?string $currency = 'BDT'): string
    {
        $currency = strtoupper($currency ?: 'BDT');

        return $currency . ' ' . number_format((float) $value, $currency === 'BDT' ? 0 : 2);
    }

    private static function data(array &$commands, float $x, float $y, string $label, string $value): void
    {
        self::text($commands, $x, $y, $label . ':', 9, true, '64748b');
        self::text($commands, $x + 68, $y, $value, 9);
    }

    private static function paidSeal(array &$commands, array &$images, float $x, float $y): void
    {
        $sealPath = public_path('images/paid-seal.png');

        if (is_file($sealPath)) {
            self::image($commands, $images, $sealPath, $x, $y, 96, 96, false);

            return;
        }

        $centerX = $x + 48;
        $centerY = $y + 48;
        self::circle($commands, $centerX, $centerY, 37, 'dc2626', 2.4);
        self::circle($commands, $centerX, $centerY, 29, 'dc2626', 1.2);
        self::text($commands, $centerX - 27, $centerY - 3, 'PAID', 24, true, 'dc2626');
        self::text($commands, $centerX - 31, $centerY - 22, 'SALE CONFIRMED', 7, true, 'dc2626');
    }

    private static function siteLogoPath(?object $site): ?string
    {
        $path = $site?->admin_logo_path ?: $site?->login_logo_path ?: $site?->registration_logo_path ?: $site?->default_og_image_path;

        if (! $path) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);

        return is_file($fullPath) ? $fullPath : null;
    }

    private static function image(array &$commands, array &$images, string $path, float $x, float $y, float $maxWidth, float $maxHeight, bool $crop = true): void
    {
        $image = self::thumbnailJpeg($path, (int) ($maxWidth * 3), (int) ($maxHeight * 3), $crop);

        if (! $image) {
            return;
        }

        $name = 'Im' . (count($images) + 1);
        $images[$name] = $image;
        $commands[] = sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /%s Do Q', $image['display_width'] / 3, $image['display_height'] / 3, $x, $y, $name);
    }

    private static function thumbnailJpeg(string $path, int $maxWidth, int $maxHeight, bool $crop = true): ?array
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

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = $crop
            ? max($maxWidth / max($sourceWidth, 1), $maxHeight / max($sourceHeight, 1))
            : min($maxWidth / max($sourceWidth, 1), $maxHeight / max($sourceHeight, 1), 1);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $canvasWidth = $crop ? $maxWidth : $targetWidth;
        $canvasHeight = $crop ? $maxHeight : $targetHeight;
        $target = imagecreatetruecolor($canvasWidth, $canvasHeight);
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255));
        imagecopyresampled($target, $source, (int) (($canvasWidth - $targetWidth) / 2), (int) (($canvasHeight - $targetHeight) / 2), 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
        ob_start();
        imagejpeg($target, null, 90);
        $data = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);

        return $data ? ['data' => $data, 'width' => $canvasWidth, 'height' => $canvasHeight, 'display_width' => $canvasWidth, 'display_height' => $canvasHeight] : null;
    }

    private static function line(array &$commands, float $x1, float $y1, float $x2, float $y2, string $color = '111827', float $width = 1): void
    {
        [$r, $g, $b] = self::rgb($color);
        $commands[] = sprintf('%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S', $r, $g, $b, $width, $x1, $y1, $x2, $y2);
    }

    private static function circle(array &$commands, float $centerX, float $centerY, float $radius, string $color = '111827', float $width = 1): void
    {
        [$r, $g, $b] = self::rgb($color);
        $c = $radius * 0.5522847498;

        $commands[] = sprintf(
            '%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c %.2F %.2F %.2F %.2F %.2F %.2F c S',
            $r,
            $g,
            $b,
            $width,
            $centerX + $radius,
            $centerY,
            $centerX + $radius,
            $centerY + $c,
            $centerX + $c,
            $centerY + $radius,
            $centerX,
            $centerY + $radius,
            $centerX - $c,
            $centerY + $radius,
            $centerX - $radius,
            $centerY + $c,
            $centerX - $radius,
            $centerY,
            $centerX - $radius,
            $centerY - $c,
            $centerX - $c,
            $centerY - $radius,
            $centerX,
            $centerY - $radius,
            $centerX + $c,
            $centerY - $radius,
            $centerX + $radius,
            $centerY - $c,
            $centerX + $radius,
            $centerY
        );
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
        return [hexdec(substr($hex, 0, 2)) / 255, hexdec(substr($hex, 2, 2)) / 255, hexdec(substr($hex, 4, 2)) / 255];
    }

    private static function escape(string $text): string
    {
        $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text) ?: $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private static function buildPdf(array $pages, int $pageWidth, int $pageHeight, array $images = []): string
    {
        $objects = [1 => '<< /Type /Catalog /Pages 2 0 R >>', 3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>', 4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>'];
        $nextObjectNumber = 5;
        $imageObjectNumbers = [];

        foreach ($images as $name => $image) {
            $imageObjectNumber = $nextObjectNumber++;
            $imageObjectNumbers[$name] = $imageObjectNumber;
            $objects[$imageObjectNumber] = "<< /Type /XObject /Subtype /Image /Width {$image['width']} /Height {$image['height']} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length " . strlen($image['data']) . " >>\nstream\n{$image['data']}\nendstream";
        }

        $xObjects = collect($imageObjectNumbers)->map(fn (int $number, string $name): string => "/{$name} {$number} 0 R")->implode(' ');
        $pageObjectNumbers = [];

        foreach ($pages as $commands) {
            $content = implode("\n", $commands);
            $contentObjectNumber = $nextObjectNumber++;
            $pageObjectNumber = $nextObjectNumber++;
            $pageObjectNumbers[] = $pageObjectNumber;
            $objects[$contentObjectNumber] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}\nendstream";
            $xObjectResources = $xObjects ? " /XObject << {$xObjects} >>" : '';
            $objects[$pageObjectNumber] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 3 0 R /F2 4 0 R >>{$xObjectResources} >> /Contents {$contentObjectNumber} 0 R >>";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . collect($pageObjectNumbers)->map(fn (int $number): string => "{$number} 0 R")->implode(' ') . '] /Count ' . count($pageObjectNumbers) . ' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= "{$number} 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
    }
}
