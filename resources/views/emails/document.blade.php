<!DOCTYPE html>
<html>
<body>
    <h2>Project Document</h2>
    <p>Please find attached the document for project: {{ $project->project_name }}</p>
    <p>Process: {{ $task->process->process_name }}</p>
    <p>Status: {{ ucfirst($task->status) }}</p>
</body>
</html>
