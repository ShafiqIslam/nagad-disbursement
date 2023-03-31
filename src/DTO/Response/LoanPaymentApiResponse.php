<?php

namespace Polygontech\NagadDisbursement\DTO\Response;

use Polygontech\NagadDisbursement\DTO\Output\LoanPaymentOutput;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;

class LoanPaymentApiResponse
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $sensitiveData,
        public readonly ?array $errorData,
    ) {
    }

    public function decrypt(HandshakeOutput $handshakeResult): LoanPaymentOutput
    {
        if ($this->type == 'error') return $this->getErrorOutput();

        $sensitiveResponse = json_decode($handshakeResult->decrypt($this->sensitiveData), true);

        return new LoanPaymentOutput(
            type: 'success',
            referenceNo: $sensitiveResponse['referenceNo'],
            rechargeId: $sensitiveResponse['rechargeId'],
            issuerTxnId: $sensitiveResponse['issuerTxnId'],
            merchantBalance: $sensitiveResponse['merchantBalance'],
            issuerTxnDt: $sensitiveResponse['issuerTxnDt'],
            creationTime: $sensitiveResponse['requestDateTime'],
            errorCode: null,
            errorMessage: null,
        );
    }

    private function getErrorOutput(): LoanPaymentOutput
    {
        return new LoanPaymentOutput(
            type: 'error',
            referenceNo: null,
            rechargeId: null,
            issuerTxnId: null,
            merchantBalance: null,
            issuerTxnDt: null,
            creationTime: null,
            errorCode: $this->errorData['reason'],
            errorMessage: $this->errorData['message'],
        );
    }

    public static function fromArray(array $array): LoanPaymentApiResponse
    {
        if (self::isError($array)) return new LoanPaymentApiResponse('error', null, $array);

        return new LoanPaymentApiResponse('success', $array['sensitiveData'], null);
    }

    public static function fromJson(string $result): LoanPaymentApiResponse
    {
        return self::fromArray(json_decode($result, true));
    }

    private static function isError(array $array): bool
    {
        return !isset($array['sensitiveData']);
    }
}
