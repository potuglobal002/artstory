<?php

namespace App\Filament\Resources\CourseEnrollments;

use App\Filament\Resources\CourseEnrollments\Pages\ListCourseEnrollments;
use App\Filament\Resources\CourseEnrollments\Pages\ViewCourseEnrollment;
use App\Models\CourseEnrollment;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CourseEnrollmentResource extends Resource
{
    protected static ?string $model = CourseEnrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    protected static ?string $navigationLabel = 'Course Enrolments';

    protected static ?string $recordTitleAttribute = 'student_name';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Student')
                ->schema([
                    TextEntry::make('student_name'),
                    TextEntry::make('student_email'),
                    TextEntry::make('student_phone'),
                    TextEntry::make('guardian_phone'),
                    TextEntry::make('address')->columnSpanFull(),
                    TextEntry::make('note')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Course & Payment')
                ->schema([
                    TextEntry::make('course.title')->label('Course'),
                    TextEntry::make('original_amount')->money('BDT')->label('Original amount'),
                    TextEntry::make('discount_amount')->money('BDT')->label('Discount'),
                    TextEntry::make('amount')->money('BDT')->label('Payable amount'),
                    TextEntry::make('coupon_code')->label('Coupon')->badge()->placeholder('-'),
                    TextEntry::make('gateway_name'),
                    TextEntry::make('payment_status')->badge(),
                    TextEntry::make('enrollment_status')->badge(),
                    TextEntry::make('transaction_id'),
                    TextEntry::make('paid_at')->dateTime(),
                    TextEntry::make('uuid')->copyable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('student_name')->searchable(),
                TextColumn::make('student_phone')->searchable(),
                TextColumn::make('course.title')->label('Course')->searchable(),
                TextColumn::make('coupon_code')->label('Coupon')->badge()->placeholder('-')->searchable(),
                TextColumn::make('discount_amount')->money('BDT')->label('Discount')->sortable(),
                TextColumn::make('amount')->money('BDT')->label('Payable')->sortable(),
                TextColumn::make('gateway_name')->badge(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('enrollment_status')->badge()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseEnrollments::route('/'),
            'view' => ViewCourseEnrollment::route('/{record}'),
        ];
    }
}
