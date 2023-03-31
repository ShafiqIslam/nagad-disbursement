<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Polygontech\NagadDisbursement\DTO\Input\BatchItem;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;

/**
 * @internal
 */
class BatchItemCreateRequest extends RequestWithHandshake
{
    /**
     * @param BatchDisburseResult $batch
     * @param array<BatchItem> $batchItems
     */
    public function __construct(
        public readonly BatchDisburseOutput $batch,
        public readonly array $batchItems,
    ) {
    }

    public function toArray(): array
    {
        return [
            "batchId" => $this->batch->batchId,
            "batchItemModelList" => array_map(fn (BatchItem $item) => $item->toArray(), $this->batchItems)
        ];
    }
}
