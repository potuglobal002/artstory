<?php

namespace App\Filament\Resources\Artists\Tables;

use App\Mail\ArtistApprovedMail;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ArtistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('picture_path')
                    ->label('Picture')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Portal Email')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('approval_status')
                    ->label('Portal')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => str($state ?: 'approved')->headline()->toString())
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('date_of_birth')
                    ->label('DOB')
                    ->date()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('nationality')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('artworks_count')
                    ->label('Artworks')
                    ->counts('artworks')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Active toggle')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('Portal status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active status'),
            ])
            ->recordActions([
                Action::make('approvePortal')
                    ->label('Approve access')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve artist portal access')
                    ->modalDescription('This creates or links a frontend login account and emails the artist their temporary password.')
                    ->visible(fn ($record): bool => filled($record->email) && ($record->approval_status !== 'approved' || ! $record->user_id))
                    ->action(function ($record): void {
                        $password = null;
                        $user = User::query()->where('email', $record->email)->first();

                        if ($user && $user->artistProfile()->whereKeyNot($record->getKey())->exists()) {
                            Notification::make()
                                ->title('Email already linked')
                                ->body('This email is already linked to another artist profile.')
                                ->danger()
                                ->send();

                            return;
                        }

                        if (! $user) {
                            $password = static::temporaryPassword();
                            $user = User::query()->create([
                                'name' => $record->name,
                                'email' => $record->email,
                                'password' => $password,
                                'is_active' => true,
                                'email_verified_at' => now(),
                            ]);
                        }

                        $record->forceFill([
                            'user_id' => $user->id,
                            'approval_status' => 'approved',
                            'approved_at' => now(),
                            'is_active' => true,
                        ])->save();

                        Mail::to($record->email)->send(new ArtistApprovedMail($record, $password));

                        Notification::make()
                            ->title('Artist approved')
                            ->body('Login details were emailed to the artist.')
                            ->success()
                            ->send();
                    }),
                Action::make('resetPortalPassword')
                    ->label('Reset portal password')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->modalHeading('Reset artist portal password')
                    ->modalDescription('This creates a new temporary password and emails it to the artist.')
                    ->visible(fn ($record): bool => filled($record->email) && filled($record->user_id))
                    ->action(function ($record): void {
                        $user = $record->user;

                        if (! $user) {
                            Notification::make()
                                ->title('No linked account')
                                ->body('Approve this artist first to create or link a portal account.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $password = static::temporaryPassword();

                        $user->forceFill([
                            'email' => str($record->email)->trim()->lower()->toString(),
                            'password' => $password,
                            'is_active' => true,
                        ])->save();

                        $record->forceFill([
                            'approval_status' => 'approved',
                            'approved_at' => $record->approved_at ?: now(),
                            'is_active' => true,
                        ])->save();

                        Mail::to($record->email)->send(new ArtistApprovedMail($record, $password));

                        Notification::make()
                            ->title('Password reset')
                            ->body('A new temporary password was emailed to the artist.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn ($record): bool => $record->artworks()->exists())
                    ->tooltip('Artists with artworks cannot be deleted. Move or delete their artworks first.'),
            ]);
    }

    private static function temporaryPassword(): string
    {
        return Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4)) . '-' . random_int(100, 999);
    }
}
