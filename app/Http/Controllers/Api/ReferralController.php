<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    use ApiResponse;

    public function __construct(private ReferralService $referralService) {}

    public function code(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'referral_code'    => $user->referral_code,
            'total_referrals'  => $user->referrals()->count(),
            'total_rewarded'   => (float) $user->transactions()
                ->where('type', 'referral_reward')
                ->where('status', 'success')
                ->sum('amount'),
        ]);
    }

    public function tree(Request $request): JsonResponse
    {
        $user = $request->user();
        $tree = $this->referralService->buildTree($user);

        return $this->success([
            'referral_code'   => $user->referral_code,
            'direct_referrals' => $user->referrals()->count(),
            'tree'            => $tree,
        ]);
    }

    public function rewards(Request $request): JsonResponse
    {
        $rewards = $request->user()
            ->transactions()
            ->where('type', 'referral_reward')
            ->where('status', 'success')
            ->with('user:id,name')
            ->latest()
            ->paginate($request->get('per_page', 15));

        // Enrich each reward with the referred user's name
        $rewards->getCollection()->transform(function ($txn) {
            $referred = User::find($txn->reference_id);
            return array_merge($txn->toArray(), [
                'from_user' => $referred?->name,
                'level'     => $this->extractLevel($txn->notes),
            ]);
        });

        return $this->success($rewards);
    }

    private function extractLevel(string $notes): ?int
    {
        if (preg_match('/Level (\d+)/', $notes, $m)) {
            return (int) $m[1];
        }
        return null;
    }
}
