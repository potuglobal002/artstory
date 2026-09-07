<?php

namespace Tests\Unit;

use App\Models\Artwork;
use App\Models\ArtworkSale;
use App\Support\ArtworkQrCode;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkPricingTest extends TestCase
{
    public function test_artwork_uses_currency_specific_selling_price_before_base_price(): void
    {
        $artwork = new Artwork([
            'price' => 100000,
            'selling_price' => 90000,
            'usd_price' => 1200,
            'usd_selling_price' => 1100,
        ]);

        $this->assertSame(90000.0, $artwork->displayPrice('BDT'));
        $this->assertSame(1100.0, $artwork->displayPrice('USD'));
    }

    public function test_artwork_falls_back_to_currency_base_price(): void
    {
        $artwork = new Artwork(['price' => 100000, 'usd_price' => 1200]);

        $this->assertSame(100000.0, $artwork->displayPrice('BDT'));
        $this->assertSame(1200.0, $artwork->displayPrice('USD'));
        $this->assertSame(100000.0, $artwork->displayPrice('EUR'));
    }

    public function test_sale_calculates_tax_added_to_subtotal(): void
    {
        $sale = new ArtworkSale(['tax_percentage' => 15, 'is_tax_included' => false]);

        $this->assertSame(150.0, $sale->calculateTaxAmount(1000));
        $this->assertSame(1150.0, $sale->calculateTotal(1000));
    }

    public function test_sale_extracts_included_tax_without_changing_total(): void
    {
        $sale = new ArtworkSale(['tax_percentage' => 15, 'is_tax_included' => true]);

        $this->assertSame(130.43, $sale->calculateTaxAmount(1000));
        $this->assertSame(1000.0, $sale->calculateTotal(1000));
    }

    public function test_sale_formats_bdt_and_usd_consistently(): void
    {
        $bdtSale = new ArtworkSale(['currency' => 'BDT']);
        $usdSale = new ArtworkSale(['currency' => 'USD']);

        $this->assertSame('BDT 110,000', $bdtSale->formattedMoney(110000));
        $this->assertSame('USD 1,100.50', $usdSale->formattedMoney(1100.5));
    }

    public function test_gallery_urls_keep_the_primary_image_and_remove_duplicates(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('artworks/primary.webp', 'image');
        Storage::disk('public')->put('artworks/detail.webp', 'image');

        $artwork = new Artwork([
            'image_path' => 'artworks/primary.webp',
            'gallery_images' => ['artworks/detail.webp', 'artworks/primary.webp'],
        ]);

        $urls = $artwork->galleryImageUrls();

        $this->assertCount(2, $urls);
        $this->assertSame(Storage::disk('public')->url('artworks/primary.webp'), $urls[0]);
    }

    public function test_qr_code_is_accessible_svg(): void
    {
        $svg = ArtworkQrCode::svg('https://example.test/artwork-code/AGB-P-01');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('role="img"', $svg);
        $this->assertStringContainsString('aria-label="Artwork QR code"', $svg);
    }

    public function test_sale_rejects_unsupported_currency_before_persistence(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ArtworkSale(['currency' => 'EUR']))->save();
    }

    public function test_sale_rejects_negative_financial_values_before_persistence(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ArtworkSale(['currency' => 'BDT', 'subtotal_amount' => -1]))->save();
    }

    public function test_invoiced_sale_rejects_financial_snapshot_changes(): void
    {
        $sale = new ArtworkSale;
        $sale->setRawAttributes([
            'invoice_sent_at' => now()->toDateTimeString(),
            'currency' => 'BDT',
            'tax_amount' => 0,
            'tax_percentage' => 0,
        ]);
        $sale->exists = true;
        $sale->syncOriginal();
        $sale->currency = 'USD';

        $this->expectException(\LogicException::class);

        $sale->save();
    }
}
