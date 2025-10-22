<?php

use Illuminate\Support\Facades\Route;
use Fernando\Arc\Models\ArcJob;
use Fernando\Arc\Services\ArcWorker;

Route::prefix('jobs')->group(function () {
    Route::get('/{queue}/ack', function (string $queue) {
        $pending = ArcJob::ack($queue);

        if (!$pending) {
            return response('No pending jobs', 204);
        }

        return $pending->id;
    });

    Route::get('/{id}/run', function ($id) {
        $job = ArcJob::find($id);

        if (!$job) {
            return response('Job not found', 404);
        }

        app(ArcWorker::class)->process($job);

        return response('Job is being processed', 200);
    });
});
