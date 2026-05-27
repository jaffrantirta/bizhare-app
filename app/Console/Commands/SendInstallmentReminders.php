<?php

namespace App\Console\Commands;

use App\Models\InstallmentPayment;
use App\Notifications\InstallmentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInstallmentReminders extends Command
{
    protected $signature = 'app:send-installment-reminders {--days=3 : Days before due date to send reminder}';

    protected $description = 'Send installment payment reminders to investors for upcoming due dates';

    public function handle(): int
    {
        $daysAhead = (int) $this->option('days');
        $targetDate = Carbon::now()->addDays($daysAhead)->toDateString();

        $this->info("Sending reminders for installments due on: {$targetDate}");

        $installments = InstallmentPayment::where('status', 'pending')
            ->where('due_date', $targetDate)
            ->with(['investment.user', 'investment.business'])
            ->get();

        if ($installments->isEmpty()) {
            $this->info('No installments due in the next ' . $daysAhead . ' days.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($installments as $installment) {
            $investment = $installment->investment;
            $user = $investment->user ?? null;

            if (!$user) {
                continue;
            }

            try {
                $user->notify(new InstallmentReminderNotification($installment, $investment));
                $count++;
                $this->line("  Sent reminder to: {$user->email} for installment #{$installment->month_number}");
            } catch (\Exception $e) {
                $this->error("  Failed to send reminder to {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} reminders successfully.");

        return Command::SUCCESS;
    }
}
