<?php

namespace App\Filament\Resources\ProfitDistributions;

use App\Filament\Resources\ProfitDistributions\Pages\CreateProfitDistribution;
use App\Filament\Resources\ProfitDistributions\Pages\ListProfitDistributions;
use App\Filament\Resources\ProfitDistributions\Schemas\ProfitDistributionForm;
use App\Filament\Resources\ProfitDistributions\Tables\ProfitDistributionsTable;
use App\Models\ProfitDistribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfitDistributionResource extends Resource
{
    protected static ?string $model = ProfitDistribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Profit Distributions';

    public static function form(Schema $schema): Schema
    {
        return ProfitDistributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfitDistributionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfitDistributions::route('/'),
            'create' => CreateProfitDistribution::route('/create'),
        ];
    }
}
