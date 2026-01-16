<div class="d-flex align-items-center">
    @if(auth()->user()->hasRole('cs') && $job->status === 'new')
        <button class="btn btn-sm btn-primary me-1" onclick="assignPlanner({{ $job->id }})">
            Assign Planner
        </button>
    @endif
    
    @if(auth()->user()->hasRole('planner') && $job->status === 'assigned_to_planner')
        <button class="btn btn-sm btn-warning me-1" onclick="assignOperator({{ $job->id }})">
            Assign Operator
        </button>
    @endif
    
    @if(auth()->user()->hasRole('qc') && $job->status === 'completed_by_operator')
        <button class="btn btn-sm btn-success me-1" onclick="reviewJob({{ $job->id }})">
            Review
        </button>
    @endif
    
    <button class="btn btn-sm btn-info me-1" onclick="viewDetails({{ $job->id }})">
        <i class="fas fa-eye"></i>
    </button>
</div>