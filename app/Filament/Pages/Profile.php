<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use SensitiveParameter;

class Profile extends EditProfile
{
    protected static ?string $title = 'My Profile';

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar_path')
            ->label('Profile image')
            ->disk('public')
            ->directory('profile-images')
            ->visibility('public')
            ->image()
            ->avatar()
            ->imageEditor()
            ->imageCropAspectRatio('1:1')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                    ]),
                Section::make('Password')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(#[SensitiveParameter] array $data): array
    {
        unset($data['email']);

        return $data;
    }

    public function getMultiFactorAuthenticationContentComponent(): ?Component
    {
        return null;
    }
}
