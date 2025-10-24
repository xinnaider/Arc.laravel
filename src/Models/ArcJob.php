<?php

namespace Fernando\Arc\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcJob extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_RUNNING   = 'running';
    public const STATUS_SUCCESS   = 'success';
    public const STATUS_FAILED    = 'failed';

    protected $table = 'arc_jobs';

    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
    ];

    public static function ack(string $queue = 'default')
    {
        $pending = self::pending()
            ->where('queue', $queue)
            ->where('attempts', '<', 'max_attempts')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$pending) {
            return null;
        }

        $pending->update(['status' => self::STATUS_RUNNING]);

        return $pending;
    }

    public function jobable()
    {
        return new $this->jobable_type;
    }

    public function addAttempt(): void
    {
        $this->attempts += 1;
        $this->save();
    }

    public function retryIfCan(): void
    {
        if ($this->attempts < $this->max_attempts) {
            ArcJob::create([
                'jobable_type' => $this->jobable_type,
                'details' => $this->details,
                'status' => self::STATUS_PENDING,
                'max_attempts' => $this->max_attempts,
                'child_job_id' => $this->id,
                'attempts' => $this->attempts++,
            ]);
        } else {
            // Max attempts reached, do nothing
        }
    }


    public function scopePending($q) { return $q->where('status', self::STATUS_PENDING); }
    public function scopeRunning($q) { return $q->where('status', self::STATUS_RUNNING); }
    public function scopeSuccess($q) { return $q->where('status', self::STATUS_SUCCESS); }
    public function scopeFailed($q)  { return $q->where('status', self::STATUS_FAILED); }
}
