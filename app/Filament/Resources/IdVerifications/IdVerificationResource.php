<?php

namespace App\Filament\Resources\IdVerifications;

use App\Filament\Resources\IdVerifications\Pages\ListIdVerifications;
use App\Filament\Resources\IdVerifications\Pages\ViewIdVerification;
use App\Filament\Resources\IdVerifications\Schemas\IdVerificationForm;
use App\Filament\Resources\IdVerifications\Tables\IdVerificationsTable;
use App\Models\IdVerification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IdVerificationResource extends Resource
{
    protected static ?string $model = IdVerification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;
    protected static string|\UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'ID Verifications';

    public static function form(Schema $schema): Schema
    {
        return IdVerificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IdVerificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIdVerifications::route('/'),
            'view' => ViewIdVerification::route('/{record}'),
        ];
    }
}
