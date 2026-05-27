<?php

namespace App\Filament\Resources\ProfitDistributions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfitDistributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('per_investor_amount')
                    ->money('IDR')
                    ->label('Per Investor'),
                TextColumn::make('distributor.name')
                    ->label('Distributed By')
                    ->sortable(),
                TextColumn::make('distributed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->defaultSort('distributed_at', 'desc');
    }
}
