<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $businesses = Business::where('status', 'open')
            ->orWhere('status', 'active')
            ->withCount(['investments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->success($businesses);
    }

    public function show(int $id): JsonResponse
    {
        $business = Business::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })
            ->withCount(['investments' => function ($query) {
                $query->where('status', 'active');
            }])
            ->first();

        if (!$business) {
            return $this->notFound('Business not found');
        }

        return $this->success($business);
    }
}
