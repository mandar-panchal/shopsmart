<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use Carbon\Carbon;

class TaskDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $note;
    public $document;
    public $senderName;
    public $projectName;
    public $formattedUploadDate;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, ?string $note, string $document)
    {
        $this->task = $task;
        $this->note = $note;
        $this->document = $document;
        $this->senderName = auth()->user()->name;
        $this->projectName = $task->project->society_name ?? 'Project';
        $this->formattedUploadDate = $this->formatUploadDate($task->uploaded_date);
    }

    /**
     * Format the upload date safely
     */
    protected function formatUploadDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('d M Y');
        }
        
        try {
            return Carbon::parse($date)->format('d M Y');
        } catch (\Exception $e) {
            return date('d M Y');
        }
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "Document Shared - {$this->projectName} - {$this->task->process->process_name}";
        
        return $this->view('emails.task-document')
                    ->subject($subject)
                    ->attach($this->document, [
                        'as' => basename($this->document),
                        'mime' => 'application/pdf'
                    ])
                    ->with([
                        'processName' => $this->task->process->process_name,
                        'projectName' => $this->projectName,
                        'senderName' => $this->senderName,
                        'note' => $this->note,
                        'uploadedDate' => $this->formattedUploadDate
                    ]);
    }
}