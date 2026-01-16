<?php
// app/Jobs/SendProcessAssignedEmail.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProcessAssignedNotification;

class SendProcessAssignedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $executiveDetails;

    public function __construct($executiveDetails)
    {
        $this->executiveDetails = $executiveDetails;
    }

    public function handle()
    {
        if (!empty($this->executiveDetails['email'])) {
            Mail::to($this->executiveDetails['email'])
                ->send(new ProcessAssignedNotification($this->executiveDetails));
        }
    }
}