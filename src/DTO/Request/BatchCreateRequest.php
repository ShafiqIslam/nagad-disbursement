<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Carbon\Carbon;
use Polygontech\NagadDisbursement\Helpers;

/**
 * @internal
 */
class BatchCreateRequest extends RequestWithHandshake
{
    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly Carbon $scheduleTime,
    ) {
    }

    public function toArray(): array
    {
        return [
            "batchTitle" => $this->title,
            "disbursementType" => $this->type,
            "disbursementScheduledDT" => Helpers::formatTime($this->scheduleTime),
        ];
    }
}
