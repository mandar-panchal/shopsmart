<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\ProjectRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskDocumentMail;
use Illuminate\Support\Facades\Storage;
use Exception;

class EmailController extends Controller
{
    /**
     * Get task details
     */
    public function getTaskDetails($id)
    {
        try {
            $task = Task::with(['project', 'process'])->findOrFail($id);
           
            return response()->json([
                'success' => true,
                'data' => $task
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch task details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get executives list
     */
    public function getExecutives()
    {
        try {
            $executives = User::whereHas('roles', function($query) {
                $query->where('name', 'executive');
            })->get(['id', 'name', 'email']);
            
            return response()->json([
                'success' => true,
                'data' => $executives
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch executives: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project contacts
     */
    public function getProjectContacts($projectId)
    {
        try {
            $project = ProjectRegistration::findOrFail($projectId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'president_email' => $project->president_email,
                    'vice_president_email' => $project->vice_president_email,
                    'secretary_email' => $project->secretary_email
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch project contacts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email with document
     */
/**
 * Send email with document
 */
public function sendMail(Request $request)
{
    try {
        // Validate the request
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'project_id' => 'required|exists:project_registrations,id',
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'note' => 'nullable|string'
        ]);

        $task = Task::with('project')->findOrFail($validated['task_id']);
        
        // Check if file exists
        if (!Storage::exists('public/' . $task->file_path)) {
            throw new Exception('Document file not found');
        }

        $document = storage_path('app/public/' . $task->file_path);

        // Send emails
        foreach ($validated['emails'] as $email) {
            Mail::to($email)->send(new TaskDocumentMail(
                $task,
                $validated['note'],
                $document
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully'
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error: ' . $e->getMessage(),
            'errors' => $e->errors()
        ], 422);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email: ' . $e->getMessage()
        ], 500);
    }
}
}