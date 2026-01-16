<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProcessAssignmentNotification extends Notification
{
    use Queueable;

    protected $process;
    protected $assignDate;
    protected $projectRegistration;

    public function __construct($process, $assignDate, $projectRegistration = null)
    {
        $this->process = $process;
        $this->assignDate = $assignDate;
        $this->projectRegistration = $projectRegistration ?? $process->projectRegistration;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $description = "You have been assigned to process '{$this->process->process_name}'";
        
        if ($this->projectRegistration) {
            $description .= " for project registration '{$this->projectRegistration->society_name}'";
        }

        $projectId = $this->projectRegistration ? $this->projectRegistration->id : $this->process->project_registration_id;
        $url = url("/master/project/{$projectId}/processes");

        return (new MailMessage)
            ->subject('New Process Assignment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($description)
            ->line('Due Date: ' . $this->assignDate)
            ->action('View Process', $url)
            ->line('Please log in to your dashboard to view the complete details.')
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        $description = "You have been assigned to process '{$this->process->process_name}'";
        
        if ($this->projectRegistration) {
            $description .= " for project registration '{$this->projectRegistration->society_name}'";
        }
        
        $description .= ". Due date: {$this->assignDate}";

        $projectId = $this->projectRegistration ? $this->projectRegistration->id : $this->process->project_registration_id;

        return [
            'title' => 'New Process Assignment',
            'icon' => 'clipboard',
            'colour' => 'primary',
            'description' => $description,
            'process_id' => $this->process->process_id,
            'assign_date' => $this->assignDate,
            'url' => "/master/project/{$projectId}/processes"
        ];
    }
}
