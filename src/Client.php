<?php

namespace Polygontech\NagadDisbursement;

use Polygontech\CommonHelpers\HTTP\URL;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;
use Polygontech\NagadDisbursement\DTO\Request\BatchApproveRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\BatchItemCreateRequest;
use Polygontech\NagadDisbursement\DTO\Request\HandshakeRequest;
use Polygontech\NagadDisbursement\DTO\Request\Request;
use Polygontech\NagadDisbursement\DTO\Request\RequestWithHandshake;
use Polygontech\NagadDisbursement\DTO\Response\BatchApiResponse;
use Polygontech\NagadDisbursement\DTO\Response\HandshakeApiResponse;

/**
 * @internal
 */
class Client
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    public function handshake(): HandshakeOutput
    {
        $url = $this->config->makeUrlOfPath("api/secure-handshake/dfs/disbursement");
        $data = HandshakeRequest::fromMerchantAggregatorID($this->config->merchantAggegatorId);
        list($resultData, $headerSize) = $this->postWithoutHandshake($url, $data->withSignature($this->config));
        $response = HandshakeApiResponse::fromCurlResponse($resultData, $headerSize);
        return $response->decrypt($this->config);
    }

    public function createBatch(HandshakeOutput $handshakeResult, BatchCreateRequest $request): BatchDisburseOutput
    {
        $url = $this->config->makeUrlOfPath("api/disbursement-batch/create");
        return $this->postAndGetBatchResult($url, $request, $handshakeResult);
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

    private function postAndGetBatchResult($uri, RequestWithHandshake $request, HandshakeOutput $handshake): BatchDisburseOutput
    {
        list($resultData, $headerSize) = $this->postWithHandshake($uri, $request, $handshake);
        $response = BatchApiResponse::fromJson(substr($resultData, $headerSize));
        return $response->decrypt($handshake);
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

        return [$resultData, $headerSize];
    }

    private function postWithoutHandshake(URL $url, Request $request): array
    {
        return $this->post($url, $request->toJson());
    }

    private function postWithHandshake(URL $url, RequestWithHandshake $request, HandshakeOutput $handshake): array
    {
        $url = $url->mutateQuery("key-id", $handshake->keyId);

        return $this->post($url, $request->toJsonWithHandshake($handshake), [
            'X-KM-Api-Key:' . $handshake->apiKey
        ]);
    }

    // private function getBatchApproveStatus($PostURL, $X_KM_Api_Key)
    // {
    //     $ch = curl_init();
    //     $timeout = 10;
    //     $header = array(
    //         'Content-Type:application/json',
    //         'X-KM-Api-Key:' . $X_KM_Api_Key,
    //         'X-KM-MC-Id:' . $this->MC_ID,
    //         'X-KM-MA-Id:' . $this->MA_ID,
    //         'X-KM-Api-Version:v-0.2.0',
    //         'X-KM-Client-Type:PC_WEB',
    //     );

    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($ch, CURLOPT_URL, $PostURL);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     //$file_contents = curl_exec($ch);
    //     //curl_close($ch);
    //     //return $file_contents;

    //     $response = curl_exec($ch);
    //     echo curl_error($ch);

    //     curl_close($ch);
    //     return $response;
    // }

    // private function getStatusCheck($PostURL, $X_KM_Api_Key)
    // {
    //     $ch = curl_init();
    //     $timeout = 10;
    //     $header = array(
    //         'Content-Type:application/json',
    //         'X-KM-Api-Key:' . $X_KM_Api_Key,
    //         'X-KM-MC-Id:' . $this->MC_ID,
    //         'X-KM-MA-Id:' . $this->MA_ID,
    //         'X-KM-Api-Version:v-0.2.0',
    //         'X-KM-Client-Type:PC_WEB',
    //     );

    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($ch, CURLOPT_URL, $PostURL);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     //$file_contents = curl_exec($ch);
    //     //curl_close($ch);
    //     //return $file_contents;

    //     $response = curl_exec($ch);
    //     echo curl_error($ch);

    //     curl_close($ch);
    //     return $response;
    // }
}
