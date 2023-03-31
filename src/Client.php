<?php

namespace Polygontech\NagadDisbursement;

use Carbon\Carbon;
use Polygontech\CommonHelpers\HTTP\URL;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Polygontech\NagadDisbursement\DTO\Output\FinalOut;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;
use Polygontech\NagadDisbursement\DTO\Output\LoanPaymentOutput;
use Polygontech\NagadDisbursement\DTO\Request\BatchApproveRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchItemCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\CheckPaymentStatusRequest;
use Polygontech\NagadDisbursement\DTO\Request\HandshakeRequest;
use Polygontech\NagadDisbursement\DTO\Request\LoanPaymentRequest;
use Polygontech\NagadDisbursement\DTO\Request\Request;
use Polygontech\NagadDisbursement\DTO\Request\RequestWithHandshake;
use Polygontech\NagadDisbursement\DTO\Response\BatchApiResponse;
use Polygontech\NagadDisbursement\DTO\Response\HandshakeApiResponse;
use Polygontech\NagadDisbursement\DTO\Response\LoanPaymentApiResponse;
use Polygontech\NagadDisbursement\DTO\Response\PaymentStatusApiResponse;

/**
 * @internal
 */
class Client
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function handshake($requestDateTime): HandshakeOutput
    {
        $url = $this->config->makeUrlOfPath("api/secure-handshake/dfs/disbursement");
        $data = HandshakeRequest::fromMerchantAggregatorID($this->config->merchantAggegatorId, $requestDateTime);
        list($resultData, $headerSize) = $this->postWithoutHandshake($url, $data->withSignature($this->config));
        $response = HandshakeApiResponse::fromCurlResponse($resultData, $headerSize);
        return $response->decrypt($this->config);
    }

    public function createBatch(HandshakeOutput $handshakeResult, BatchCreateRequest $request): BatchDisburseOutput
    {
        $url = $this->config->makeUrlOfPath("api/disbursement-batch/create");
        return $this->postAndGetBatchResult($url, $request, $handshakeResult);
    }

    public function makeLoanPayment(HandshakeOutput $handshakeResult, LoanPaymentRequest $request): LoanPaymentOutput
    {
        $url = $this->config->makeUrlOfPath("api/secure-session/dfs/recharge/loan-payment");
        return $this->postAndGetBatchLoanPaymentResult($url, $request->setConfig($this->config), $handshakeResult);
    }

    public function checkPaymentStatus(HandshakeOutput $handshakeResult, CheckPaymentStatusRequest $request): FinalOut
    {
        $url = $this->config->makeUrlOfPath("api/dfs/recharge/status");
        return $this->getPaymentStatus($url, $request, $handshakeResult);
    }

    public function createDisbursementItem(HandshakeOutput $handshakeResult, BatchItemCreateRequest $request): BatchDisburseOutput
    {
        $url = $this->config->makeUrlOfPath("api/disbursement-batch/create-disbursement-item");
        return $this->postAndGetBatchResult($url, $request, $handshakeResult);
    }

    public function approveBatch(HandshakeOutput $handshake, BatchApproveRequest $request): BatchDisburseOutput
    {
        $url = $this->config->makeUrlOfPath("api/disbursement-batch/approve-batch");
        return $this->postAndGetBatchResult($url, $request, $handshake);
    }

    private function postAndGetBatchLoanPaymentResult($uri, RequestWithHandshake $request, HandshakeOutput $handshake): LoanPaymentOutput
    {
        list($resultData, $headerSize) = $this->postWithHandshake($uri, $request, $handshake);
        $response = LoanPaymentApiResponse::fromJson(substr($resultData, $headerSize));
        return $response->decrypt($handshake);
    }

    private function postAndGetBatchResult($uri, RequestWithHandshake $request, HandshakeOutput $handshake): BatchDisburseOutput
    {
        list($resultData, $headerSize) = $this->postWithHandshake($uri, $request, $handshake);
        $response = BatchApiResponse::fromJson(substr($resultData, $headerSize));
        return $response->decrypt($handshake);
    }

    private function getPaymentStatus(URL $url, CheckPaymentStatusRequest $request, HandshakeOutput $handshake): FinalOut
    {
        if ($request->getCreationTime()) {
            $url = $url->mutateQuery("requestDateTime", $request->getCreationTime());
        }
        if ($request->getReferenceNo()) {
            $url = $url->mutateQuery("referenceNo", $request->getReferenceNo());
        }
        if ($request->getType() == 'success') {
            $url = $url->mutateQuery("rechargeId", $request->getRechargeId());
        }

        list($resultData, $headerSize) = $this->getWithHandshake($url, $request, $handshake);
        $response = PaymentStatusApiResponse::fromJson(substr($resultData, $headerSize));
        return $response->decrypt();
    }


    private function post(URL $url, string $data, array $header = []): array
    {
        $header = array_merge([
            'Content-Type:application/json',
            'X-KM-MC-Id:' . $this->config->merchantId,
            'X-KM-MA-Id:' . $this->config->merchantAggegatorId,
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-Client-Type:PC_WEB',
        ], $header);

        $ch = curl_init($url->getFull());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $resultData = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        //  dump('raw-data-post:',$url->getFull(),$header,$data,$resultData);

        return [$resultData, $headerSize];
    }

    private function get(URL $url, string $data, array $header = []): array
    {
        $header = array_merge([
            'Content-Type:application/json',
            'X-KM-MC-Id:' . $this->config->merchantId,
            'X-KM-MA-Id:' . $this->config->merchantAggegatorId,
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-Client-Type:PC_WEB',
        ], $header);


        $ch = curl_init($url->getFull());
        //  dump($url->getFull());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);


        $resultData = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        //  dump('raw-data-post:',$url->getFull(),$header,$data,$resultData);
        return [$resultData, $headerSize];
    }

    private function postWithoutHandshake(URL $url, Request $request): array
    {
        return $this->post($url, $request->toJson());
    }

    private function postWithHandshake(URL $url, RequestWithHandshake $request, HandshakeOutput $handshake): array
    {
        $url = $url->mutateQuery("key-id", $handshake->keyId);
        $url = $url->mutateQuery("ma-id", $this->config->merchantAggegatorId);

        return $this->post($url, $request->toJsonWithHandshake($handshake), [
            'X-KM-Api-Key:' . $handshake->apiKey
        ]);
    }

    private function getWithHandshake(URL $url, RequestWithHandshake $request, HandshakeOutput $handshake): array
    {
        return $this->get($url, $request->toJsonWithHandshake($handshake), [
            'X-KM-Api-Key:' . $handshake->apiKey
        ]);
    }
}
