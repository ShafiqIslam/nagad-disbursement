<?php

namespace Polygontech\NagadDisbursement\DTO\Output;

use Carbon\Carbon;

class LoanPaymentOutput
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $referenceNo,
        public readonly ?string $rechargeId,
        public readonly ?string $issuerTxnId,
        public readonly ?string $merchantBalance,
        public readonly ?string $issuerTxnDt,
        public readonly ?string $creationTime,
        public readonly ?string $errorCode,
        public readonly ?string $errorMessage,
    ) {
    }
}