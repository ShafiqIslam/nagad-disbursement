<?php

namespace Polygontech\NagadDisbursement\DTO\Output;

use Carbon\Carbon;

class BatchDisburseOutput
{
    public function __construct(
        public readonly string $batchId,
        public readonly string $status,
        public readonly Carbon $scheduledTime,
        public readonly Carbon $creationTime,
    ) {
    }
}
