<?php

namespace App\Support;

use App\Filament\Resources\Artists\ArtistResource;
use App\Filament\Resources\ArtworkInquiries\ArtworkInquiryResource;
use App\Filament\Resources\Artworks\ArtworkResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkInquiry;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminNotifier
{
    public static function artistRegistered(Artist $artist): void
    {
        self::notify(
            Notification::make()
                ->title('New artist registration')
                ->body($artist->name.' submitted an artist profile for review.')
                ->warning()
                ->actions([
                    Action::make('open')
                        ->label('Open artist')
                        ->button()
                        ->url(ArtistResource::getUrl('edit', ['record' => $artist]))
                        ->markAsRead(),
                ]),
        );
    }

    public static function artworkSubmitted(Artwork $artwork): void
    {
        $artistName = $artwork->artist?->name ?: 'An artist';

        self::notify(
            Notification::make()
                ->title('New artwork submitted')
                ->body($artistName.' uploaded "'.$artwork->title.'" for review.')
                ->info()
                ->actions([
                    Action::make('open')
                        ->label('Review artwork')
                        ->button()
                        ->url(ArtworkResource::getUrl('edit', ['record' => $artwork]))
                        ->markAsRead(),
                ]),
        );
    }

    public static function artworkInquiryReceived(ArtworkInquiry $inquiry): void
    {
        self::notify(
            Notification::make()
                ->title('New artwork inquiry')
                ->body($inquiry->name.' asked about "'.($inquiry->artwork_title ?: 'an artwork').'".')
                ->warning()
                ->actions([
                    Action::make('open')
                        ->label('Open inquiry')
                        ->button()
                        ->url(ArtworkInquiryResource::getUrl('view', ['record' => $inquiry]))
                        ->markAsRead(),
                ]),
        );
    }

    public static function contactMessageReceived(ContactMessage $message): void
    {
        self::notify(
            Notification::make()
                ->title('New contact message')
                ->body($message->name.' sent: "'.$message->subject.'".')
                ->warning()
                ->actions([
                    Action::make('open')
                        ->label('Open message')
                        ->button()
                        ->url(ContactMessageResource::getUrl('view', ['record' => $message]))
                        ->markAsRead(),
                ]),
        );
    }

    private static function notify(Notification $notification): void
    {
        $admins = self::admins();

        if ($admins->isEmpty()) {
            return;
        }

        foreach ($admins as $admin) {
            DB::table('notifications')->insert([
                'id' => (string) Str::orderedUuid(),
                'type' => 'filament',
                'notifiable_type' => $admin::class,
                'notifiable_id' => $admin->getKey(),
                'data' => json_encode($notification->getDatabaseMessage()),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DatabaseNotificationsSent::dispatch($admin);
        }
    }

    private static function admins(): Collection
    {
        $query = User::query()->where('is_active', true);

        try {
            $admins = (clone $query)->role('Admin')->get();

            if ($admins->isNotEmpty()) {
                return $admins;
            }
        } catch (\Throwable) {
            //
        }

        return $query->get();
    }
}
