<?php

namespace Polygontech\NagadDisbursement\Facade;

use Illuminate\Support\Facades\Facade;
use Polygontech\NagadDisbursement\DTO\DisburseBatchResult;
use Polygontech\NagadDisbursement\DTO\HandshakeResult;
use Polygontech\NagadDisbursement\DTO\DisburseBatchCreatePostData;
use Polygontech\NagadDisbursement\DTO\BatchItem;

/**
 * @method static DisburseBatchResult disburseNow(DisburseBatchCreatePostData $disbursement, array<BatchItem> $batchItems)
 * @method static HandshakeResult handshake()
 * @method static DisburseBatchResult createBatch(HandshakeResult $handshakeResult, DisburseBatchCreatePostData $disbursement)
 * @method static DisburseBatchResult addItemToBatch(HandshakeResult $handshakeResult, DisburseBatchResult $batch, array<BatchItem> $batchItems)
 * @method static DisburseBatchResult approveBatch(HandshakeResult $handshakeResult, DisburseBatchResult $batch)
 *
 * @see \Polygontech\NagadDisbursement\NagadDisbursement
 */
class NagadDisbursement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "nagad.disbursement";
    }
}
