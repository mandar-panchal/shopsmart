<div class="d-flex">
    <button class="btn btn-sm btn-icon btn-primary me-1" onclick="viewJobDetails({{ $job->id }})" data-bs-toggle="tooltip" title="View Details">
        <i class="fas fa-eye"></i>
    </button>

    @if(auth()->user()->hasRole(['admin', 'cs']))
        <button class="btn btn-sm btn-icon btn-warning me-1" onclick="reassignPlanner({{ $job->id }})" data-bs-toggle="tooltip" title="Reassign Planner">
            <i class="fas fa-user-edit"></i>
        </button>
    @endif

    @if(auth()->user()->hasRole('cs') && $job->created_by == auth()->id())
        <button class="btn btn-sm btn-icon btn-danger me-1" onclick="deleteJob({{ $job->id }})" data-bs-toggle="tooltip" title="Delete Job">
            <i class="fas fa-trash-alt"></i>
        </button>
    @endif

    @if($job->status == 'changes_requested' && auth()->user()->hasRole('operator') && $job->assigned_operator_id == auth()->id())
        <button class="btn btn-sm btn-icon btn-info me-1" onclick="viewFeedback({{ $job->id }})" data-bs-toggle="tooltip" title="View Feedback">
            <i class="fas fa-comment-alt"></i>
        </button>
    @endif

    @if($job->status == 'approved' && auth()->user()->hasRole('cs') && $job->created_by == auth()->id())
        <button class="btn btn-sm btn-icon btn-success me-1" onclick="deliverJob({{ $job->id }})" data-bs-toggle="tooltip" title="Deliver to Client">
            <i class="fas fa-check-circle"></i>
        </button>
    @endif
</div>