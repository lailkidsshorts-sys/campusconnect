<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintFeedback extends Model
{
    use HasFactory;

    protected $table = 'complaint_feedback';

    protected $fillable = [
        'complaint_id',
        'rating',
        'comment',
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
