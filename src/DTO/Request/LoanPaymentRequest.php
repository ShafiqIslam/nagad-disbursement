<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Carbon\Carbon;
use Polygontech\NagadDisbursement\Config;
use Polygontech\NagadDisbursement\Helpers;

class LoanPaymentRequest extends RequestWithHandshake
{
    private Config $config;

    public function __construct(
        private readonly string $amount,
        private readonly string $accountNo,
        private readonly string $challenge,
        private readonly Carbon $requestDateTime
    ) {
    }

    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function toArray(): array
    {
        $txnInfoArr = [
            "beneficiaryInfo" => [
                "accountNoType" => "M",
                "accountNo" => $this->accountNo,
                "userType" => "00"
            ],
            "merchantId" => $this->config->merchantId,
            "amount" => $this->amount,
            "currency" => "050",
            "requestDateTime" => Helpers::formatTime($this->requestDateTime),
            "referenceNo" => 'DISBURSE' . Helpers::generateRandomString(6),
            "channel" => "APP",
            "challenge" => $this->challenge,
            "purpose" => "loan-payment",
            "txnAdditionalInfo" => [
                "disbursementProductId" => 'qrCode000',
                "description" => 'Rkpl Agami Reward',
                "notificationId" => '',
            ],
        ];

        return [
            "transactionSignature" => $this->config->generateSignatureWithoutKey($txnInfoArr),
            "transactionInfo" => $txnInfoArr
        ];
    }
}
