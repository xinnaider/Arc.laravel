<?php

namespace Fernando\Arc\Services;

use Fernando\Arc\Models\ArcJob;

class ArcWorker
{
    public static function process($job) : void
    {
        $job->addAttempt();

        $jobable = $job->jobable();

        if ($jobable instanceof \Fernando\Arc\Abstracts\ArcSpecs) {
            if ($job->status !== ArcJob::STATUS_PENDING) {
                return;
            }

            try {
                $job->update(['status' => ArcJob::STATUS_RUNNING]);
                $jobable->handle($job);
                $job->update(['status' => ArcJob::STATUS_SUCCESS]);
            } catch (\Exception $e) {
                if ($job->ratedOutOfAttempts()) {
                    $job->update(['status' => ArcJob::STATUS_FAILED]);
                } else {
                    $job->update(['status' => ArcJob::STATUS_PENDING]);
                }

                $job->update(['status' => ArcJob::STATUS_FAILED, 'trace' => $e->getTraceAsString()]);
            }
        } else {
            $job->update(['status' => 'failed', 'trace' => 'Jobable does not implement ArcSpecs']);
        }
    }
}
