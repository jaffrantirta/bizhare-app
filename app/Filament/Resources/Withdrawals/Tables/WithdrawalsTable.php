<?php

namespace App\Filament\Resources\Withdrawals\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('bank_name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->searchable(),
                TextColumn::make('account_name'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'processed' => 'success',
                        'approved' => 'info',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->toggleable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'processed' => 'Processed',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved',
                        'processed_by' => Auth::id(),
                        'processed_at' => now(),
                    ])),
                Action::make('process')
                    ->label('Mark as Processed')
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update([
                                'status' => 'processed',
                                'processed_by' => Auth::id(),
                                'processed_at' => now(),
                            ]);

                            // Update related transaction
                            $record->user->transactions()
                                ->where('type', 'withdrawal')
                                ->where('reference_id', $record->id)
                                ->where('status', 'pending')
                                ->update(['status' => 'success', 'confirmed_at' => now()]);
                        });
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'approved']))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->required()
                            ->label('Rejection reason'),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => 'rejected',
                                'notes' => $data['notes'],
                                'processed_by' => Auth::id(),
                                'processed_at' => now(),
                            ]);

                            // Refund the balance
                            $record->user->increment('balance', $record->amount);

                            // Update related transaction to failed
                            $record->user->transactions()
                                ->where('type', 'withdrawal')
                                ->where('reference_id', $record->id)
                                ->where('status', 'pending')
                                ->update(['status' => 'failed', 'confirmed_at' => now()]);
                        });
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
