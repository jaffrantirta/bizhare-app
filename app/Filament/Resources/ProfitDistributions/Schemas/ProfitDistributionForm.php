<?php

namespace App\Filament\Resources\ProfitDistributions\Schemas;

use App\Models\Business;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProfitDistributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('business_id')
                    ->label('Business')
                    ->options(Business::where('status', 'active')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('total_amount')
                    ->label('Total Amount to Distribute (IDR)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefix('Rp'),
                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable(),
            ]);
    }
}
