<?php

namespace App\Filament\Resources\Businesses\Tables;

use App\Models\SystemSetting;
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
            ->modifyQueryUsing(fn ($query) => $query->withSum('activeInvestments', 'total_amount'))
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
                TextColumn::make('collected_amount')
                    ->label('Collected')
                    ->state(fn ($record) => (float) ($record->active_investments_sum_total_amount ?? 0))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('target_amount')
                    ->label('Target')
                    ->state(fn ($record) => $record->target_investors * (float) SystemSetting::get('investment_full_amount', 1500000))
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('funding_progress')
                    ->label('Progress')
                    ->badge()
                    ->state(function ($record) {
                        $collected = (float) ($record->active_investments_sum_total_amount ?? 0);
                        $target    = $record->target_investors * (float) SystemSetting::get('investment_full_amount', 1500000);
                        return $target > 0 ? min(100, (int) round(($collected / $target) * 100)) : 0;
                    })
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50  => 'warning',
                        default       => 'gray',
                    }),
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
