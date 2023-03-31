<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Polygontech\NagadDisbursement\DTO\Output\LoanPaymentOutput;

class CheckPaymentStatusRequest  extends RequestWithHandshake
{
    public function __construct(
        private readonly LoanPaymentOutput $loanPaymentOutput
    ) {
    }

    public function getType()
    {
        return $this->loanPaymentOutput->type;
    }

    public function getCreationTime()
    {
        return $this->loanPaymentOutput->creationTime;
    }

    public function getReferenceNo()
    {
        return $this->loanPaymentOutput->referenceNo;
    }

    public function getRechargeId()
    {
        return $this->loanPaymentOutput->rechargeId;
    }


    public function toArray(): array
    {
        return [
            'requestDatetime' => $this->loanPaymentOutput->creationTime,
            'referenceNo' => $this->loanPaymentOutput->referenceNo,
            'rechargeId' => $this->loanPaymentOutput->rechargeId,
            'type' => $this->loanPaymentOutput->type
        ];
    }
}
