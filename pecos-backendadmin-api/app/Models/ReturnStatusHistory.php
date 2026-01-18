<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnStatusHistory extends Model
{
    protected $table = 'return_status_history';

    protected $fillable = [
        'return_id',
        'old_status',
        'new_status',
        'notes',
        'changed_by',
    ];

    /**
     * Get the return request.
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    /**
     * Get the user who made the change.
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get formatted status change description.
     */
    public function getChangeDescription(): string
    {
        $statuses = ReturnRequest::getStatuses();
        $old = $this->old_status ? ($statuses[$this->old_status] ?? $this->old_status) : 'New';
        $new = $statuses[$this->new_status] ?? $this->new_status;

        return "Status changed from {$old} to {$new}";
    }
}
