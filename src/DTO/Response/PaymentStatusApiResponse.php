<?php

namespace Polygontech\NagadDisbursement\DTO\Response;

use Polygontech\NagadDisbursement\DTO\Output\FinalOut;
use Polygontech\NagadDisbursement\Helpers;

class PaymentStatusApiResponse
{
    public function __construct(
        public readonly array $data,
    ) {
    }

    public function decrypt()
    {
        return new FinalOut(
            type: isset($this->data['status']) && $this->data['status'] == 'COMPLETED' ? 'success' : 'error',
            merchantId: isset($this->data['merchantId']) ? $this->data['merchantId'] : null,
            amount: isset($this->data['amount']) ? $this->data['amount'] : null,
            customerMobileNo: isset($this->data['customerMobileNo']) ? $this->data['customerMobileNo'] : null,
            merchantMobileNo: isset($this->data['merchantMobileNo']) ? $this->data['merchantMobileNo'] : null,
            merchantAggregatorId: isset($this->data['merchantAggregatorId']) ? $this->data['merchantAggregatorId'] : null,
            requestDateTime: isset($this->data['requestDateTime']) ? Helpers::strToTime($this->data['requestDateTime']) : null,
            referenceNo: isset($this->data['referenceNo']) ? $this->data['referenceNo'] : null,
            rechargeId: isset($this->data['rechargeId']) ? $this->data['rechargeId'] : null,
            issuerTxnDateTime: isset($this->data['issuerTxnDateTime']) ? Helpers::strToTime($this->data['issuerTxnDateTime']) : null,
            issuerTxnId: isset($this->data['issuerTxnId']) ? $this->data['issuerTxnId'] : null,
            status: isset($this->data['status']) ? $this->data['status'] : null,
            statusCode: isset($this->data['statusCode']) ? $this->data['statusCode'] : null,
            merchantBalance: isset($this->data['merchantBalance']) ?  $this->data['merchantBalance'] : null,
            errorCode: isset($this->data['reason']) ? $this->data['reason'] : null,
            errorMessage: isset($this->data['devMessage']) ? $this->data['devMessage'] : null,
        );
    }


    public static function fromArray(array $array)
    {
        return new PaymentStatusApiResponse($array);
    }

    public static function fromJson(string $result)
    {
        return self::fromArray(json_decode($result, true));
    }
}
