<?php

namespace Fernando\Arc\Services;

use Fernando\Arc\Models\ArcJob;

class ArcWorker
{
    public static function process($job) : void
    {
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
                $job->update(['status' => 'failed', 'trace' => $e->getMessage()]);
            }
        } else {
            $job->update(['status' => 'failed', 'trace' => 'Jobable does not implement ArcSpecs']);
        }
    }
}
