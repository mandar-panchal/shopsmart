<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProcessAssignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $executiveDetails;

    public function __construct($executiveDetails)
    {
        $this->executiveDetails = $executiveDetails;
    }

    public function build()
    {
        return $this->view('emails.process-assigned')
                    ->subject('New Task Assigned')
                    ->with([
                        'name' => $this->executiveDetails['name'],
                        'task' => $this->executiveDetails['task_details']
                    ]);
    }
}
