<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;

/**
 * @internal
 */
class BatchApproveRequest extends RequestWithHandshake
{
    public function __construct(
        public readonly BatchDisburseOutput $batch,
    ) {
    }

    public function toArray(): array
    {
        return [
            "batchId" => $this->batch->batchId
        ];
    }
}
