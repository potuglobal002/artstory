<?php

namespace App\Providers\Filament;

use App\Auth\LoginEmailAuthentication;
use App\Filament\Pages\ArtworkInventoryReport;
use App\Filament\Pages\EmailSettings;
use App\Filament\Pages\LandingPageSettings;
use App\Filament\Pages\Profile;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Artists\ArtistResource;
use App\Filament\Resources\ArtworkEmailLogs\ArtworkEmailLogResource;
use App\Filament\Resources\ArtworkInquiries\ArtworkInquiryResource;
use App\Filament\Resources\ArtworkMediums\ArtworkMediumResource;
use App\Filament\Resources\ArtworkPaymentMethods\ArtworkPaymentMethodResource;
use App\Filament\Resources\Artworks\ArtworkResource;
use App\Filament\Resources\ArtworkSales\ArtworkSaleResource;
use App\Filament\Resources\ArtworkSizes\ArtworkSizeResource;
use App\Filament\Resources\ArtworkStyles\ArtworkStyleResource;
use App\Filament\Resources\ArtworkSubjectStyles\ArtworkSubjectStyleResource;
use App\Filament\Resources\ArtworkTaxRates\ArtworkTaxRateResource;
use App\Filament\Resources\Buyers\BuyerResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\EmailTemplates\EmailTemplateResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Exhibitions\ExhibitionResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\VirtualGalleries\VirtualGalleryResource;
use App\Filament\Resources\VirtualGalleryArtworks\VirtualGalleryArtworkResource;
use App\Filament\Resources\VirtualGalleryRooms\VirtualGalleryRoomResource;
use App\Filament\Widgets\ArtworkDashboardPanel;
use App\Notifications\Auth\LoginOtpNotification;
use App\Support\SiteSettings as SiteSettingsStore;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $emailAuthentication = $this->shouldBypassEmailOtpForLocalhost()
            ? []
            : [
                LoginEmailAuthentication::make()
                    ->codeExpiryMinutes(5)
                    ->codeNotification(LoginOtpNotification::class),
            ];

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(Profile::class, isSimple: false)
            ->brandName(fn (): string => SiteSettingsStore::title())
            ->brandLogo(fn (): ?string => SiteSettingsStore::adminLogoUrl())
            ->brandLogoHeight('2.75rem')
            ->favicon(fn (): ?string => SiteSettingsStore::faviconUrl())
            ->databaseNotifications()
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => view('filament.components.visit-website-button')->render(),
            )
            ->multiFactorAuthentication($emailAuthentication)
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                ActivityResource::class,
                EmailTemplateResource::class,
                ArtworkResource::class,
                ArtworkSaleResource::class,
                ArtworkEmailLogResource::class,
                ArtworkInquiryResource::class,
                ContactMessageResource::class,
                BuyerResource::class,
                ArtworkPaymentMethodResource::class,
                ArtworkTaxRateResource::class,
                ArtistResource::class,
                ArtworkStyleResource::class,
                ArtworkSubjectStyleResource::class,
                ArtworkMediumResource::class,
                ArtworkSizeResource::class,
                VirtualGalleryResource::class,
                VirtualGalleryRoomResource::class,
                VirtualGalleryArtworkResource::class,
                ExhibitionResource::class,
                EventResource::class,
                UserResource::class,
            ])
            ->pages([
                Dashboard::class,
                ArtworkInventoryReport::class,
                LandingPageSettings::class,
                EmailSettings::class,
                SiteSettings::class,
            ])
            ->widgets([
                ArtworkDashboardPanel::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function shouldBypassEmailOtpForLocalhost(): bool
    {
        if (! app()->environment('local')) {
            return false;
        }

        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        return in_array($host, ['127.0.0.1', 'localhost'], true);
    }
}
