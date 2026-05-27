<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')->disabled(),
                TextInput::make('type')->disabled(),
                TextInput::make('amount')->disabled(),
                TextInput::make('status')->disabled(),
            ]);
    }
}
