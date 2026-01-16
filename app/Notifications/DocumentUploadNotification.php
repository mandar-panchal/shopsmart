<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentUploadNotification extends Notification
{
    use Queueable;

    protected $process;
    protected $executive;
    protected $uploadedDate;
    protected $projectRegistration;

    public function __construct($process, $executive, $uploadedDate, $projectRegistration = null)
    {
        $this->process = $process;
        $this->executive = $executive;
        $this->uploadedDate = $uploadedDate;
        $this->projectRegistration = $projectRegistration;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // public function toMail($notifiable)
    // {
    //     $description = "Executive {$this->executive->name} has uploaded a document for process '{$this->process->process_name}'";
        
    //     if ($this->projectRegistration) {
    //         $description .= " related to project '{$this->projectRegistration->society_name}'";
    //     }

    //     return (new MailMessage)
    //         ->subject('New Document Upload')
    //         ->greeting('Hello ' . $notifiable->name . ',')
    //         ->line($description)
    //         ->line('Upload Date: ' . $this->uploadedDate)
    //         ->action('View Document', url('/processes/' . $this->process->process_id))
    //         ->line('Please login to your dashboard to review the uploaded document.');
    // }

    public function toArray($notifiable)
    {
        $description = "Executive {$this->executive->name} has uploaded a document for process '{$this->process->process_name}'";
        
        if ($this->projectRegistration) {
            $description .= " related to project '{$this->projectRegistration->society_name}'";
        }
        
        $description .= ". Upload date: {$this->uploadedDate}";

        return [
            'title' => 'New Document Upload',
            'icon' => 'file-text',
            'colour' => 'success',
            'description' => $description,
            'process_id' => $this->process->process_id,
            'executive_id' => $this->executive->id,
            'upload_date' => $this->uploadedDate
        ];
    }
}