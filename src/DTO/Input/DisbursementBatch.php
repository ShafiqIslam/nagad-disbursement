<?php

namespace Polygontech\NagadDisbursement\DTO\Input;

use Carbon\Carbon;
use Polygontech\NagadDisbursement\DTO\Request\BatchCreateRequest;

class DisbursementBatch
{
    /**
     * @param string $title
     * @param string $type
     * @param Carbon $scheduleTime
     * @param array<BatchItem> $items
     */
    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly Carbon $scheduleTime,
        public readonly array $items,
    ) {
    }

    public function getBatchCreateRequest(): BatchCreateRequest
    {
        return new BatchCreateRequest(
            title: $this->title,
            type: $this->type,
            scheduleTime: $this->scheduleTime,
        );
    }
}
