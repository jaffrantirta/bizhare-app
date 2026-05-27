<?php

namespace App\Filament\Resources\Investments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvestmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')->disabled(),
                TextInput::make('business.name')->disabled(),
                TextInput::make('payment_type')->disabled(),
                TextInput::make('total_amount')->disabled(),
                TextInput::make('status')->disabled(),
            ]);
    }
}
