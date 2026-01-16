@component('mail::message')
# New Process Assignment

Hello {{ $notifiable->name }},

You have been assigned to process **'{{ $process->process_name }}'** 
@if($projectRegistration)
for project registration **'{{ $projectRegistration->society_name }}'**
@endif

**Due Date:** {{ $assignDate }}

Please log in to your dashboard to view the complete details.

@component('mail::button', ['url' => url("/master/project/{$projectId}/processes")])
View Process
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
