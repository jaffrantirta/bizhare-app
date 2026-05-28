<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Investment;
use App\Notifications\InvestmentPaymentFailedNotification;
use App\Notifications\InvestmentPaymentSuccessNotification;
use App\Notifications\TopUpFailedNotification;
use App\Notifications\TopUpSuccessNotification;
use App\Services\InvestmentService;
use App\Services\ReferralService;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'initial_deposit'  => 'info',
                        'investment'       => 'primary',
                        'installment'      => 'warning',
                        'profit'           => 'success',
                        'withdrawal'       => 'danger',
                        'refund'           => 'gray',
                        'referral_reward'  => 'purple',
                        default            => 'gray',
                    }),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success'   => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),
                TextColumn::make('payment_method')
                    ->badge()
                    ->toggleable(),
                ImageColumn::make('proof_image')
                    ->disk('public')
                    ->toggleable(),
                TextColumn::make('confirmed_at')
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
                        'pending'   => 'Pending',
                        'success'   => 'Success',
                        'failed'    => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'initial_deposit' => 'Initial Deposit',
                        'investment'      => 'Investment',
                        'installment'     => 'Installment',
                        'profit'          => 'Profit',
                        'withdrawal'      => 'Withdrawal',
                        'refund'          => 'Refund',
                        'referral_reward' => 'Referral Reward',
                    ]),
                SelectFilter::make('payment_method')
                    ->options([
                        'manual_transfer' => 'Manual Transfer',
                        'qris'            => 'QRIS',
                    ]),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirm Payment')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->status === 'pending' && $record->payment_method === 'manual_transfer')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update([
                                'status'       => 'success',
                                'confirmed_by' => Auth::id(),
                                'confirmed_at' => now(),
                            ]);

                            $user = $record->user;

                            if ($record->type === 'initial_deposit') {
                                $user->update([
                                    'has_initial_deposit'          => true,
                                    'initial_deposit_confirmed_at' => now(),
                                ]);
                                app(ReferralService::class)->distributeRewards($user);
                                $user->notify(new TopUpSuccessNotification($record));
                            } elseif (in_array($record->type, ['investment', 'installment'])) {
                                $investment = Investment::find($record->reference_id);
                                if ($investment) {
                                    app(InvestmentService::class)->activateInvestment($investment);
                                }
                                $user->notify(new InvestmentPaymentSuccessNotification($record));
                            }
                        });
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'       => 'failed',
                            'confirmed_by' => Auth::id(),
                            'confirmed_at' => now(),
                        ]);

                        $user = $record->user;

                        if ($record->type === 'initial_deposit') {
                            $user->notify(new TopUpFailedNotification($record));
                        } elseif (in_array($record->type, ['investment', 'installment'])) {
                            $user->notify(new InvestmentPaymentFailedNotification($record));
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
