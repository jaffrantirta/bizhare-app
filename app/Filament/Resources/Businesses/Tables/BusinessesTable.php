<?php

namespace App\Filament\Resources\Businesses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BusinessesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->size(50),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable()
                    ->badge(),
                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('current_investors')
                    ->label('Investors')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => "{$state} / {$record->target_investors}"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'open' => 'info',
                        'draft' => 'gray',
                        'closed' => 'danger',
                    }),
                TextColumn::make('activation_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Open',
                        'active' => 'Active',
                        'closed' => 'Closed',
                    ]),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Activate')
                    ->color('success')
                    ->icon('heroicon-o-play')
                    ->visible(fn ($record) => $record->status === 'open')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'active',
                        'activation_date' => now()->toDateString(),
                    ])),
                Action::make('open')
                    ->label('Open for Investment')
                    ->color('info')
                    ->icon('heroicon-o-lock-open')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'open'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
