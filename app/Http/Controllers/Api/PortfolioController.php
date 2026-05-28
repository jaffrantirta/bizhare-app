<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Investment;
use App\Models\ProfitDistribution;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/portfolio
     * Summary + list of all investments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $investments = Investment::where('user_id', $user->id)
            ->with(['business', 'installmentPayments'])
            ->latest()
            ->get();

        $totalInvested = $investments->sum('total_amount');
        $totalProfit   = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'profit')
            ->where('status', 'success')
            ->sum('amount');

        $summary = [
            'total_investments'  => $investments->count(),
            'active_investments' => $investments->where('status', 'active')->count(),
            'pending_investments'=> $investments->where('status', 'pending')->count(),
            'total_invested'     => (float) $totalInvested,
            'total_profit'       => $totalProfit,
            'current_balance'    => (float) $user->balance,
        ];

        $list = $investments->map(fn (Investment $inv) => $this->formatInvestment($inv, $user->id));

        return $this->success([
            'summary'     => $summary,
            'investments' => $list,
        ]);
    }

    /**
     * GET /api/portfolio/{investment}
     * Full detail of a single investment.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $user = $request->user();

        $investment = Investment::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['business', 'installmentPayments'])
            ->first();

        if (!$investment) {
            return $this->notFound('Investment not found.');
        }

        // Profit transactions for this business
        $distributionIds = ProfitDistribution::where('business_id', $investment->business_id)
            ->pluck('id')
            ->map(fn ($id) => (string) $id);

        $profitHistory = Transaction::where('user_id', $user->id)
            ->where('type', 'profit')
            ->where('status', 'success')
            ->whereIn('reference_id', $distributionIds)
            ->latest()
            ->get(['id', 'amount', 'notes', 'confirmed_at', 'created_at']);

        // Payment transactions for this investment
        $paymentHistory = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['investment', 'installment'])
            ->where('reference_id', (string) $investment->id)
            ->latest()
            ->get(['id', 'type', 'amount', 'status', 'payment_method', 'confirmed_at', 'created_at']);

        return $this->success([
            'investment'      => $this->formatInvestment($investment, $user->id),
            'installment_schedule' => $investment->isInstallment()
                ? $investment->installmentPayments->map(fn ($p) => [
                    'month_number' => $p->month_number,
                    'amount'       => (float) ($p->amount + $p->admin_fee),
                    'status'       => $p->status,
                    'due_date'     => $p->due_date,
                    'paid_at'      => $p->paid_at,
                ])
                : null,
            'profit_history'  => $profitHistory,
            'payment_history' => $paymentHistory,
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function formatInvestment(Investment $inv, int $userId): array
    {
        $business = $inv->business;

        $profitReceived = 0.0;
        if ($business) {
            $distributionIds = ProfitDistribution::where('business_id', $business->id)
                ->pluck('id')
                ->map(fn ($id) => (string) $id);

            $profitReceived = (float) Transaction::where('user_id', $userId)
                ->where('type', 'profit')
                ->where('status', 'success')
                ->whereIn('reference_id', $distributionIds)
                ->sum('amount');
        }

        $installmentProgress = null;
        if ($inv->isInstallment()) {
            $next = $inv->nextInstallment();
            $installmentProgress = [
                'months_paid'    => $inv->months_paid,
                'tenure_months'  => $inv->tenure_months,
                'remaining'      => max(0, $inv->tenure_months - $inv->months_paid),
                'next_due_date'  => $next?->due_date,
                'next_amount'    => $next ? (float) ($next->amount + $next->admin_fee) : null,
                'completed'      => $inv->months_paid >= $inv->tenure_months,
            ];
        }

        return [
            'id'                   => $inv->id,
            'status'               => $inv->status,
            'payment_type'         => $inv->payment_type,
            'total_amount'         => (float) $inv->total_amount,
            'admin_fee'            => (float) $inv->admin_fee,
            'profit_received'      => $profitReceived,
            'installment_progress' => $installmentProgress,
            'joined_at'            => $inv->created_at->toDateString(),
            'business'             => $business ? [
                'id'               => $business->id,
                'name'             => $business->name,
                'category'         => $business->category,
                'location'         => $business->location,
                'status'           => $business->status,
                'image_url'        => $business->image
                    ? Storage::disk('public')->url($business->image)
                    : null,
                'current_investors' => $business->current_investors,
                'target_investors'  => $business->target_investors,
                'activation_date'   => $business->activation_date?->toDateString(),
            ] : null,
        ];
    }
}
