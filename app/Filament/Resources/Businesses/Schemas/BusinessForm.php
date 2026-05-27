<?php

namespace App\Filament\Resources\Businesses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->required()
                    ->rows(4),
                TextInput::make('category')
                    ->required()
                    ->maxLength(100),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                TextInput::make('target_investors')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open for Investment',
                        'active' => 'Active',
                        'closed' => 'Closed',
                    ])
                    ->required()
                    ->default('draft'),
                DatePicker::make('activation_date')
                    ->label('Activation Date'),
                FileUpload::make('image')
                    ->image()
                    ->directory('businesses')
                    ->disk('public')
                    ->nullable(),
            ]);
    }
}
