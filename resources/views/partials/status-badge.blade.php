@php
    $statusColors = [
        'new' => 'primary',
        'assigned_to_planner' => 'info',
        'assigned_to_operator' => 'warning',
        'completed_by_operator' => 'success',
        'in_qc' => 'secondary',
        'changes_requested' => 'danger',
        'approved' => 'success',
        'delivered' => 'dark'
    ];
    $color = $statusColors[$job->status] ?? 'light';
@endphp
<span class="badge bg-{{ $color }}">{{ str_replace('_', ' ', ucfirst($job->status)) }}</span>
