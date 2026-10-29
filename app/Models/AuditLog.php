<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * One entry in the audit trail: who did what, to which record, and when.
 *
 * The audit trail is append-only. Entries can be added but never changed or
 * deleted, so the record of what happened can be trusted. Write entries with
 * AuditLog::record().
 */
#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'details', 'ip_address'])]
class AuditLog extends Model
{
    protected $table = 'audit_log';

    // The table has created_at but no updated_at, because rows never change.
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['details' => 'array'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit log entries cannot be changed.'));
        static::deleting(fn () => throw new LogicException('Audit log entries cannot be deleted.'));
    }

    /**
     * Record that the signed-in person did something.
     *
     * @param  string  $action  What happened, in plain words, such as "Deactivated user".
     * @param  Model|null  $subject  The record it happened to.
     * @param  array<string, mixed>  $details  Extra facts. Never passwords or patient information.
     */
    public static function record(string $action, ?Model $subject = null, array $details = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'details' => $details ?: null,
            'ip_address' => request()?->ip(),
        ]);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
