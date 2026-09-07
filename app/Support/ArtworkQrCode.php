<?php

namespace App\Support;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ArtworkQrCode
{
    public static function svg(string $value, int $size = 220): string
    {
        $qr = new QRCode(new QROptions([
            'eccLevel' => EccLevel::M,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
        ]));

        $qr->addByteSegment($value);
        $matrix = $qr->getQRMatrix();
        $count = $matrix->getSize();
        $module = $size / $count;
        $paths = '';

        for ($row = 0; $row < $count; $row++) {
            for ($col = 0; $col < $count; $col++) {
                if ($matrix->check($col, $row)) {
                    $paths .= '<rect x="' . round($col * $module, 3) . '" y="' . round($row * $module, 3) . '" width="' . round($module + 0.04, 3) . '" height="' . round($module + 0.04, 3) . '"/>';
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" role="img" aria-label="Artwork QR code">'
            . '<rect width="100%" height="100%" fill="#ffffff"/>'
            . '<g fill="#111827">' . $paths . '</g>'
            . '</svg>';
    }
}
