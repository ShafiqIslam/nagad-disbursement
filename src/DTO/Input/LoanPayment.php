<?php

namespace Polygontech\NagadDisbursement\DTO\Input;

use Carbon\Carbon;
use Polygontech\CommonHelpers\Mobile\BDMobile;
use Polygontech\CommonHelpers\Money\BDT;
use Polygontech\NagadDisbursement\DTO\Request\LoanPaymentRequest;


class LoanPayment
{
    /**
     * @param BDMobile $account
     * @param BDT $amount
     * @param string|null $challenge
     */
    public function __construct(
        public readonly BDMobile $account,
        public readonly BDT $amount,
        public readonly ?string $challenge = null,
        public readonly ?Carbon $requestDateTime = null,
    ) {
    }


    public function getLoanCreateRequest()
    {
        return new LoanPaymentRequest(
            accountNo: $this->account->getWithoutCountryCode(),
            amount: $this->amount->toString(),
            challenge: $this->challenge,
            requestDateTime: $this->requestDateTime
        );
    }
}
