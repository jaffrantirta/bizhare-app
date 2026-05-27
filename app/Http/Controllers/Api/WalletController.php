<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\SystemSetting;
use App\Models\Withdrawal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'balance'             => (float) $user->balance,
            'breakdown'           => $user->balanceBreakdown(),
            'is_verified'         => $user->is_verified,
            'has_initial_deposit' => $user->has_initial_deposit,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $query = $request->user()->transactions()->latest();

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        return $this->success(
            $query->paginate($request->get('per_page', 15))
        );
    }

    public function withdraw(Request $request): JsonResponse
    {
        $user         = $request->user();
        $minWithdrawal = (float) SystemSetting::get('minimum_withdrawal_amount', 100000);

        $validated = $request->validate([
            'amount'         => "required|numeric|min:{$minWithdrawal}",
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:255',
        ]);

        if ($user->balance < $validated['amount']) {
            return $this->error('Insufficient balance. Available: ' . number_format($user->balance, 0, ',', '.'));
        }

        $user->decrement('balance', $validated['amount']);

        $withdrawal = Withdrawal::create([
            'user_id'        => $user->id,
            'amount'         => $validated['amount'],
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name'   => $validated['account_name'],
            'status'         => 'pending',
        ]);

        $user->transactions()->create([
            'type'        => 'withdrawal',
            'amount'      => $validated['amount'],
            'status'      => 'pending',
            'reference_id' => (string) $withdrawal->id,
            'notes'       => "Withdrawal to {$validated['bank_name']} - {$validated['account_number']}",
        ]);

        return $this->created(['withdrawal' => $withdrawal], 'Withdrawal request submitted successfully.');
    }

    public function withdrawalHistory(Request $request): JsonResponse
    {
        $withdrawals = $request->user()
            ->withdrawals()
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->success($withdrawals);
    }
}
