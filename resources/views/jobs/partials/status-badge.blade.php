@php
    $statusColors = [
        'assigned_to_planner' => 'warning',
        'assigned_to_operator' => 'info',
        'in_qc' => 'primary',
        'changes_requested' => 'danger',
        'approved' => 'success',
        'delivered' => 'success'
    ];

    $statusLabels = [
        'assigned_to_planner' => 'Assigned to Planner',
        'assigned_to_operator' => 'Assigned to Operator',
        'in_qc' => 'In QC Review',
        'changes_requested' => 'Changes Requested',
        'approved' => 'Approved',
        'delivered' => 'Delivered'
    ];

    $badgeColor = $statusColors[$job->status] ?? 'secondary';
    $statusLabel = $statusLabels[$job->status] ?? ucfirst(str_replace('_', ' ', $job->status));
@endphp

<span class="badge bg-{{ $badgeColor }}">{{ $statusLabel }}</span>