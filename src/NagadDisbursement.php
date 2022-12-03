<?php

namespace Polygontech\NagadDisbursement;

use Polygontech\NagadDisbursement\DTO\Input\BatchItem;
use Polygontech\NagadDisbursement\DTO\Input\DisbursementBatch;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;
use Polygontech\NagadDisbursement\DTO\Request\BatchApproveRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchItemCreateRequest;

class NagadDisbursement
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    /**
     * @param DisbursementBatch $batch
     * @return BatchDisburseOutput
     */
    public function disburseNow(DisbursementBatch $disbursement): BatchDisburseOutput
    {
        $handshake = $this->handshake();
        $batch = $this->createBatch($handshake, $disbursement->getBatchCreateRequest());
        $batch = $this->addItemToBatch($handshake, $batch, $disbursement->items);
        return $this->approveBatch($handshake, $batch);
    }

    /**
     * @return HandshakeOutput
     */
    public function handshake(): HandshakeOutput
    {
        return $this->client->handshake();
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchCreateRequest $batch
     * @return BatchDisburseOutput
     */
    public function createBatch(HandshakeOutput $handshake, BatchCreateRequest $batch): BatchDisburseOutput
    {
        return $this->client->createBatch($handshake, $batch);
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchDisburseOutput $batch
     * @param array<BatchItem> $batchItems
     * @return BatchDisburseOutput
     */
    public function addItemToBatch(HandshakeOutput $handshake, BatchDisburseOutput $batch, array $batchItems): BatchDisburseOutput
    {
        return $this->client->createDisbursementItem($handshake, new BatchItemCreateRequest($batch, $batchItems));
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchDisburseOutput $batch
     * @return BatchDisburseOutput
     */
    public function approveBatch(HandshakeOutput $handshake, BatchDisburseOutput $batch): BatchDisburseOutput
    {
        return $this->client->approveBatch($handshake, new BatchApproveRequest($batch));
    }
}
