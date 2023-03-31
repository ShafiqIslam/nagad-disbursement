<?php

namespace Polygontech\NagadDisbursement;

use Carbon\Carbon;
use Polygontech\NagadDisbursement\DTO\Input\BatchItem;
use Polygontech\NagadDisbursement\DTO\Input\DisbursementBatch;
use Polygontech\NagadDisbursement\DTO\Input\LoanPayment;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Polygontech\NagadDisbursement\DTO\Output\FinalOut;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;
use Polygontech\NagadDisbursement\DTO\Output\LoanPaymentOutput;
use Polygontech\NagadDisbursement\DTO\Request\BatchApproveRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchItemCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\CheckPaymentStatusRequest;
use Polygontech\CommonHelpers\Mobile\BDMobile;
use Polygontech\CommonHelpers\Money\BDT;

class NagadDisbursement
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    /**
     * @param DisbursementBatch $batch
     * @return BatchDisburseOutput
     */
    public function disburseNow(DisbursementBatch $disbursement): BatchDisburseOutput
    {
        $requestDateTime = Carbon::now();
        $handshake = $this->handshake($requestDateTime);
        $batch = $this->createBatch($handshake, $disbursement->getBatchCreateRequest());
        $batch = $this->addItemToBatch($handshake, $batch, $disbursement->items);
        return $this->approveBatch($handshake, $batch);
    }

    public function makeInstantPayment(BDMobile $account, BDT $amount): FinalOut
    {
        $requestDateTime = Carbon::now();
        $handshake = $this->handshake($requestDateTime);
        $loanPayment = new LoanPayment($account, $amount, $handshake->challenge, $requestDateTime);
        $loanPayment = $this->loanPayment($handshake, $loanPayment);
        return $this->checkPaymentStatus($handshake, new CheckPaymentStatusRequest($loanPayment));
    }

    /**
     * @return HandshakeOutput
     */
    public function handshake($requestDateTime): HandshakeOutput
    {
        return $this->client->handshake($requestDateTime);
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchCreateRequest $batch
     * @return BatchDisburseOutput
     */
    public function createBatch(HandshakeOutput $handshake, BatchCreateRequest $batch): BatchDisburseOutput
    {
        return $this->client->createBatch($handshake, $batch);
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchDisburseOutput $batch
     * @param array<BatchItem> $batchItems
     * @return BatchDisburseOutput
     */
    public function addItemToBatch(HandshakeOutput $handshake, BatchDisburseOutput $batch, array $batchItems): BatchDisburseOutput
    {
        return $this->client->createDisbursementItem($handshake, new BatchItemCreateRequest($batch, $batchItems));
    }

    public function loanPayment(HandshakeOutput $handshake, LoanPayment $loanPayment): LoanPaymentOutput
    {
        return $this->client->makeLoanPayment($handshake, $loanPayment->getLoanCreateRequest());
    }

    public function checkPaymentStatus(HandshakeOutput $handshake, CheckPaymentStatusRequest $checkPaymentStatusRequest): FinalOut
    {
        return $this->client->checkPaymentStatus($handshake, $checkPaymentStatusRequest);
    }

    /**
     * @param HandshakeOutput $handshake
     * @param BatchDisburseOutput $batch
     * @return BatchDisburseOutput
     */
    public function approveBatch(HandshakeOutput $handshake, BatchDisburseOutput $batch): BatchDisburseOutput
    {
        return $this->client->approveBatch($handshake, new BatchApproveRequest($batch));
    }
}
