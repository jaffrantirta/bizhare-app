<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Investment;
use App\Services\InvestmentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvestmentController extends Controller
{
    use ApiResponse;

    public function __construct(private InvestmentService $investmentService) {}

    public function index(Request $request): JsonResponse
    {
        $investments = Investment::where('user_id', $request->user()->id)
            ->with(['business', 'installmentPayments'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->success($investments);
    }

    /**
     * POST /api/investments  — body: business_id, payment_type, ...
     * POST /api/businesses/{id}/invest — business_id from URL
     */
    public function store(Request $request, ?int $businessId = null): JsonResponse
    {
        Log::info('[InvestmentController@store] hit', [
            'businessId_from_route' => $businessId,
            'body'                  => $request->all(),
            'user_id'               => $request->user()?->id,
        ]);

        $validated = $request->validate([
            'business_id'    => $businessId ? 'nullable' : 'required|exists:businesses,id',
            'payment_type'   => 'required|in:full,installment',
            'tenure_months'  => 'required_if:payment_type,installment|nullable|integer|min:1|max:12',
            'payment_method' => 'required|in:manual_transfer,gopay,qris',
        ]);

        if ($businessId) {
            $validated['business_id'] = $businessId;
        }

        if (($validated['payment_method'] ?? null) === 'qris') {
            $validated['payment_method'] = 'gopay';
        }

        Log::info('[InvestmentController@store] validated', $validated);

        try {
            $result = $this->investmentService->createInvestment($request->user(), $validated);

            Log::info('[InvestmentController@store] success', [
                'investment_id'  => $result['investment']->id,
                'transaction_id' => $result['transaction']->id,
            ]);

            return $this->created([
                'investment'  => $result['investment']->load(['business', 'installmentPayments']),
                'transaction' => $result['transaction'],
            ], 'Investment created successfully');
        } catch (Exception $e) {
            Log::error('[InvestmentController@store] exception', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->error($e->getMessage());
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $investment = Investment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['business', 'installmentPayments'])
            ->first();

        if (!$investment) {
            return $this->notFound('Investment not found');
        }

        return $this->success($investment);
    }
}
