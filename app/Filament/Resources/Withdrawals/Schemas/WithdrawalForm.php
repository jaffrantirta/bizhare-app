<?php

namespace App\Filament\Resources\Withdrawals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')->disabled(),
                TextInput::make('amount')->disabled(),
                TextInput::make('bank_name')->disabled(),
                TextInput::make('account_number')->disabled(),
                TextInput::make('account_name')->disabled(),
                TextInput::make('status')->disabled(),
            ]);
    }
}
