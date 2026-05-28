<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\IdVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    use ApiResponse;

    public function status(Request $request): JsonResponse
    {
        $user         = $request->user();
        $verification = $user->idVerification;

        return $this->success([
            'verification'                 => $verification,
            'is_verified'                  => $user->is_verified,
            'verification_status'          => $user->verification_status,
            'has_initial_deposit'          => $user->has_initial_deposit,
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
            'id_type'        => 'required|in:ktp,sim,passport',
            'id_number'      => 'required|string|max:50',
            'id_photo'       => 'required|file|image|max:5120',
            'selfie_photo'   => 'nullable|file|image|max:5120',
            'full_name'      => 'nullable|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth'  => 'nullable|date',
            'phone_number'   => 'nullable|string|max:20',
            'occupation'     => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'province'       => 'nullable|string|max:100',
            'kabupaten'      => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        $idPhotoPath = $request->file('id_photo')->store('id-verifications/id-photos', 'public');

        $selfiePhotoPath = null;
        if ($request->hasFile('selfie_photo')) {
            $selfiePhotoPath = $request->file('selfie_photo')->store('id-verifications/selfie-photos', 'public');
        }

        $verification = IdVerification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type'          => $validated['id_type'],
                'id_number'        => $validated['id_number'],
                'id_photo'         => $idPhotoPath,
                'selfie_photo'     => $selfiePhotoPath,
                'full_name'        => $validated['full_name'] ?? null,
                'place_of_birth'   => $validated['place_of_birth'] ?? null,
                'date_of_birth'    => $validated['date_of_birth'] ?? null,
                'phone_number'     => $validated['phone_number'] ?? null,
                'occupation'       => $validated['occupation'] ?? null,
                'marital_status'   => $validated['marital_status'] ?? null,
                'province'         => $validated['province'] ?? null,
                'kabupaten'        => $validated['kabupaten'] ?? null,
                'kecamatan'        => $validated['kecamatan'] ?? null,
                'address'          => $validated['address'] ?? null,
                'status'           => 'pending',
                'rejection_reason' => null,
                'reviewed_by'      => null,
                'reviewed_at'      => null,
            ]
        );

        $user->update(['verification_status' => 'pending']);

        return $this->created([
            'verification' => $verification,
        ], 'ID verification documents uploaded successfully. Please wait for review.');
    }
}
