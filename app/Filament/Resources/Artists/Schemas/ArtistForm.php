<?php

namespace App\Filament\Resources\Artists\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArtistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Artist Profile')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Artist login email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Use this to approve or create frontend login access for this artist.'),
                        TextInput::make('phone')
                            ->maxLength(50),
                        FileUpload::make('picture_path')
                            ->label('Artist picture')
                            ->disk('public')
                            ->directory('artists')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096),
                        DatePicker::make('date_of_birth')
                            ->label('Date of birth'),
                        DatePicker::make('date_of_death')
                            ->label('Date of death'),
                        TextInput::make('nationality')
                            ->maxLength(255),
                        TextInput::make('birth_place')
                            ->label('Birth place')
                            ->maxLength(255),
                        Select::make('approval_status')
                            ->label('Portal status')
                            ->options([
                                'pending' => 'Pending Review',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('approved')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required()
                            ->inline(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 7]),
                Section::make('Biography')
                    ->schema([
                        Textarea::make('biography')
                            ->rows(12)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 5]),
            ]);
    }
}
