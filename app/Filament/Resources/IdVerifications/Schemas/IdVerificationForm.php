<?php

namespace App\Filament\Resources\IdVerifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IdVerificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')
                    ->label('User')
                    ->disabled(),
                Select::make('id_type')
                    ->options([
                        'ktp' => 'KTP',
                        'sim' => 'SIM',
                        'passport' => 'Passport',
                    ])
                    ->disabled(),
                TextInput::make('id_number')
                    ->label('ID Number')
                    ->disabled(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->visible(fn ($get) => $get('status') === 'rejected'),
            ]);
    }
}
