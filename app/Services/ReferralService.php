<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ReferralRewardNotification;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    /**
     * Walk up the referral chain and credit each upline a level-specific reward.
     * Called exactly once per new user — when their initial deposit is confirmed.
     */
    public function distributeRewards(User $newUser): void
    {
        if (!$newUser->referred_by || $newUser->referral_rewarded) {
            return;
        }

        $maxDepth = (int) SystemSetting::get('max_referral_depth', 5);
        $upline   = $newUser->referredBy;
        $level    = 1;

        while ($upline && $level <= $maxDepth) {
            $reward = (int) SystemSetting::get("referral_reward_level_{$level}", 0);

            if ($reward > 0) {
                $transaction = null;

                DB::transaction(function () use ($upline, $newUser, $level, $reward, &$transaction) {
                    $upline->increment('balance', $reward);

                    $transaction = Transaction::create([
                        'user_id'      => $upline->id,
                        'type'         => 'referral_reward',
                        'amount'       => $reward,
                        'status'       => 'success',
                        'reference_id' => (string) $newUser->id,
                        'notes'        => "Level {$level} referral reward — {$newUser->name} completed initial deposit",
                        'confirmed_at' => now(),
                    ]);
                });

                if ($transaction) {
                    $upline->notify(new ReferralRewardNotification($transaction, $newUser, $level));
                }
            }

            $upline = $upline->referredBy;
            $level++;
        }

        $newUser->update(['referral_rewarded' => true]);
    }

    /**
     * Build the referral tree for a user.
     * Returns full detail for levels 1–$detailDepth, aggregate count for deeper levels.
     */
    public function buildTree(User $user, int $detailDepth = 3): array
    {
        $maxDepth   = (int) SystemSetting::get('max_referral_depth', 5);
        $tree       = [];
        $parentIds  = [$user->id];
        $deeperCount = 0;

        for ($level = 1; $level <= $maxDepth; $level++) {
            $members = User::whereIn('referred_by', $parentIds)
                ->select('id', 'name', 'created_at', 'has_initial_deposit')
                ->get();

            if ($members->isEmpty()) {
                break;
            }

            if ($level <= $detailDepth) {
                $tree["level_{$level}"] = $members->map(fn (User $m) => [
                    'name'                => $m->name,
                    'joined_at'           => $m->created_at->toDateString(),
                    'has_initial_deposit' => $m->has_initial_deposit,
                ])->values();
            } else {
                $deeperCount += $members->count();
            }

            $parentIds = $members->pluck('id')->all();
        }

        if ($deeperCount > 0) {
            $tree['deeper_levels'] = ['total_count' => $deeperCount];
        }

        return $tree;
    }
}
