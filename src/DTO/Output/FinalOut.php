<?php

namespace Polygontech\NagadDisbursement\DTO\Output;

use Carbon\Carbon;

class FinalOut
{

    public function __construct(
        public readonly string $type,
        public readonly ?string $merchantId,
        public readonly ?string $amount,
        public readonly ?string $customerMobileNo,
        public readonly ?string $merchantMobileNo,
        public readonly ?string $merchantAggregatorId,
        public readonly ?Carbon $requestDateTime,
        public readonly ?string $referenceNo,
        public readonly ?string $rechargeId,
        public readonly ?string $issuerTxnDateTime,
        public readonly ?string $issuerTxnId,
        public readonly ?string $status,
        public readonly ?string $statusCode,
        public readonly ?string $merchantBalance,
        public readonly ?string $errorCode,
        public readonly ?string $errorMessage,
    ) {
    }
}
