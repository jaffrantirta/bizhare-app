<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\IdVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    use ApiResponse;

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $verification = $user->idVerification;

        return $this->success([
            'verification' => $verification,
            'is_verified' => $user->is_verified,
            'verification_status' => $user->verification_status,
            'has_initial_deposit' => $user->has_initial_deposit,
            'initial_deposit_confirmed_at' => $user->initial_deposit_confirmed_at,
            'steps' => [
                'id_uploaded' => $verification !== null,
                'id_approved' => $user->is_verified,
                'deposit_paid' => $user->has_initial_deposit,
            ],
        ]);
    }

    public function uploadId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_type' => 'required|in:ktp,sim,passport',
            'id_number' => 'required|string|max:50',
            'id_photo' => 'required|file|image|max:5120',
            'selfie_photo' => 'nullable|file|image|max:5120',
        ]);

        $user = $request->user();

        // Store ID photo
        $idPhotoPath = $request->file('id_photo')->store('id-verifications/id-photos', 'public');

        $selfiePhotoPath = null;
        if ($request->hasFile('selfie_photo')) {
            $selfiePhotoPath = $request->file('selfie_photo')->store('id-verifications/selfie-photos', 'public');
        }

        $verification = IdVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type' => $validated['id_type'],
                'id_number' => $validated['id_number'],
                'id_photo' => $idPhotoPath,
                'selfie_photo' => $selfiePhotoPath,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        // Update user verification status to pending
        $user->update(['verification_status' => 'pending']);

        return $this->created([
            'verification' => $verification,
        ], 'ID verification documents uploaded successfully. Please wait for review.');
    }
}
