<?php

namespace Polygontech\NagadDisbursement\DTO\Response;

use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;
use Polygontech\NagadDisbursement\Helpers;

/**
 * @internal
 */
class BatchApiResponse
{
    public function __construct(
        public readonly string $sensitiveData
    ) {
    }

    public function decrypt(HandshakeOutput $handshakeResult): BatchDisburseOutput
    {
        $sensitiveResponse = json_decode($handshakeResult->decrypt($this->sensitiveData), true);

        return new BatchDisburseOutput(
            batchId: $sensitiveResponse['batchId'],
            status: $sensitiveResponse['status'],
            scheduledTime: Helpers::strToTime($sensitiveResponse['disbursementScheduledDT']),
            creationTime: Helpers::strToTime($sensitiveResponse['batchCreationTime'])
        );
    }

    public static function fromArray(array $array): BatchApiResponse
    {
        return new BatchApiResponse($array['sensitiveData']);
    }

    public static function fromJson(string $result): BatchApiResponse
    {
        return self::fromArray(json_decode($result, true));
    }
}
