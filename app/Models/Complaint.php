<?php

namespace App\Models;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'user_id',
        'category_id',
        'issue_type_id',
        'department_id',
        'subject',
        'description',
        'priority',
        'status',
        'attachment_path',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => ComplaintPriority::class,
            'status' => ComplaintStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ComplaintResponse::class)->latest();
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(ComplaintFeedback::class);
    }

    // Query Scopes
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeStatus(Builder $query, string|ComplaintStatus $status): Builder
    {
        $val = $status instanceof ComplaintStatus ? $status->value : $status;
        return $query->where('status', $val);
    }

    /**
     * Generate unique reference number (CC-2026-0001 format)
     */
    public static function generateReferenceNumber(): string
    {
        $year = date('Y');
        $prefix = "CC-{$year}-";

        $latest = self::where('reference_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('reference_no');

        if (! $latest) {
            $next = 1;
        } else {
            $parts = explode('-', $latest);
            $next = isset($parts[2]) ? ((int) $parts[2] + 1) : 1;
        }

        return sprintf('%s%04d', $prefix, $next);
    }
}
