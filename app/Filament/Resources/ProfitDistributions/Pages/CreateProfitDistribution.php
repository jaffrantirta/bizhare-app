<?php

namespace App\Filament\Resources\ProfitDistributions\Pages;

use App\Filament\Resources\ProfitDistributions\ProfitDistributionResource;
use App\Models\Business;
use App\Services\ProfitService;
use Exception;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProfitDistribution extends CreateRecord
{
    protected static string $resource = ProfitDistributionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $business = Business::findOrFail($data['business_id']);
        $profitService = app(ProfitService::class);

        try {
            $distribution = $profitService->distribute(
                $business,
                (float) $data['total_amount'],
                Auth::user()
            );

            if (isset($data['notes'])) {
                $distribution->update(['notes' => $data['notes']]);
            }

            return $distribution;
        } catch (Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
