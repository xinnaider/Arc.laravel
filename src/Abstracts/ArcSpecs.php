<?php

namespace Fernando\Arc\Abstracts;

use Fernando\Arc\Models\ArcJob;

abstract class ArcSpecs
{
    abstract public function handle($job);

    public static function dispatch(array $args = []): void
    {
        $class = get_called_class();

        ArcJob::create([
            'jobable_type' => $class,
            'details' => json_encode($args),
            'status' => ArcJob::STATUS_PENDING,
        ]);
    }
}
