<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: white;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .note-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Document Shared: {{ $processName }}</h2>
        <p>Project: {{ $projectName }}</p>
    </div>

    <div class="content">
        <p>Hello,</p>
        
        <p>A document has been shared with you by {{ $senderName }} for the project "{{ $projectName }}".</p>
        
        <p><strong>Details:</strong></p>
        <ul>
        <li>Process: {{ $processName }}</li>
        <li>Upload Date: {{ $uploadedDate }}</li>
        </ul>

        @if($note)
        <div class="note-section">
            <strong>Note from sender:</strong>
            <p>{{ $note }}</p>
        </div>
        @endif

        <p>The document is attached to this email. Please review it at your earliest convenience.</p>
    </div>

    <div class="footer">
        <p>This is an automated message from {{ config('app.name') }}. Please do not reply to this email.</p>
    </div>
</body>
</html>