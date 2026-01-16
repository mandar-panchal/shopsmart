<?php
namespace App\Mail;

use App\Models\Task;
use App\Models\ProjectRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class DocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $project;

    public function __construct(Task $task, ProjectRegistration $project)
    {
        $this->task = $task;
        $this->project = $project;
    }

    public function build()
    {
        // Explicitly set the from address
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                   ->view('emails.document')
                   ->subject('Document for Project: ' . $this->project->name)
                   ->with([
                       'task' => $this->task,
                       'project' => $this->project
                   ]);
    }
}