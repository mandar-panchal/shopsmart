<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProcessAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $process;
    public $assignDate;
    public $projectId;
    public $notifiable;
    public $projectRegistration;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $process
     * @param  string  $assignDate
     * @param  int  $projectId
     * @param  mixed  $notifiable
     * @param  mixed  $projectRegistration
     */
    public function __construct($process, $assignDate, $projectId, $notifiable, $projectRegistration = null)
    {
        $this->process = $process;
        $this->assignDate = $assignDate;
        $this->projectId = $projectId;
        $this->notifiable = $notifiable;
        $this->projectRegistration = $projectRegistration;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        // Ensure there's a recipient and email address
        if (!$this->notifiable || !$this->notifiable->email) {
            \Log::error('Missing recipient email in ProcessAssigned mailable');
            throw new \RuntimeException('Recipient email is required');
        }

        return $this->to($this->notifiable->email)
                    ->subject('New Process Assignment')
                    ->markdown('emails.process_assigned')
                    ->with([
                        'process' => $this->process,
                        'assignDate' => $this->assignDate,
                        'projectId' => $this->projectId,
                        'notifiable' => $this->notifiable,
                        'projectRegistration' => $this->projectRegistration,
                    ]);
    }
}
