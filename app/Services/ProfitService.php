<?php

namespace App\Services;

use App\Models\Business;
use App\Models\ProfitDistribution;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ProfitReceivedNotification;
use Exception;
use Illuminate\Support\Facades\DB;

class ProfitService
{
    /**
     * Distribute profit to all active investors of a business
     *
     * @param Business $business
     * @param float $amount Total amount to distribute
     * @param User $distributedBy Admin who distributed
     * @return ProfitDistribution
     * @throws Exception
     */
    public function distribute(Business $business, float $amount, User $distributedBy): ProfitDistribution
    {
        if (!$business->isActive()) {
            throw new Exception('Business is not active. Cannot distribute profit.');
        }

        $activeInvestments = $business->activeInvestments()->with('user')->get();

        if ($activeInvestments->isEmpty()) {
            throw new Exception('No active investors found for this business.');
        }

        $investorCount     = $activeInvestments->count();
        $perInvestorAmount = round($amount / $investorCount, 2);

        $credited = [];

        $distribution = DB::transaction(function () use (
            $business, $amount, $perInvestorAmount,
            $activeInvestments, $distributedBy, &$credited
        ) {
            $distribution = ProfitDistribution::create([
                'business_id'        => $business->id,
                'total_amount'       => $amount,
                'per_investor_amount' => $perInvestorAmount,
                'distributed_by'     => $distributedBy->id,
                'distributed_at'     => now(),
            ]);

            foreach ($activeInvestments as $investment) {
                $investor = $investment->user;

                $investor->increment('balance', $perInvestorAmount);

                $transaction = Transaction::create([
                    'user_id'      => $investor->id,
                    'type'         => 'profit',
                    'amount'       => $perInvestorAmount,
                    'status'       => 'success',
                    'reference_id' => (string) $distribution->id,
                    'notes'        => "Profit distribution for business: {$business->name}",
                    'confirmed_by' => $distributedBy->id,
                    'confirmed_at' => now(),
                ]);

                $credited[] = ['investor' => $investor, 'transaction' => $transaction];
            }

            return $distribution;
        });

        foreach ($credited as ['investor' => $investor, 'transaction' => $transaction]) {
            $investor->notify(new ProfitReceivedNotification($transaction, $business));
        }

        return $distribution;
    }
}
