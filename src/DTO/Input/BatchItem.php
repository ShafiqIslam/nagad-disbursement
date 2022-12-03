<?php

namespace Polygontech\NagadDisbursement\DTO\Input;

use Polygontech\CommonHelpers\Mobile\BDMobile;
use Polygontech\CommonHelpers\Money\BDT;

class BatchItem
{
    /**
     * @param NagadAccount $account
     * @param BDT $amount
     * @param string $description
     * @param array<string, string> $additional
     */
    public function __construct(
        public readonly BDMobile $account,
        public readonly BDT $amount,
        public readonly string $description,
        public readonly array $additional
    ) {
    }

    public function toArray(): array
    {
        return [
            "mobileNo" => $this->account->getWithoutCountryCode(),
            "trxAmount" => $this->amount->toString(),
            "description" => $this->description,
            "additionalInfo" => $this->additional
        ];
    }
}
