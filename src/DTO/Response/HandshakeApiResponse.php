<?php

namespace Polygontech\NagadDisbursement\DTO\Response;

use Polygontech\NagadDisbursement\Config;
use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;

/**
 * @internal
 */
class HandshakeApiResponse
{
    public function __construct(
        public readonly string $X_KM_Api_Key,
        public readonly string $sensitiveData,
        public readonly string $signature,
    ) {
    }

    public function decrypt(Config $config): HandshakeOutput
    {
        $sensitiveResponse = json_decode($config->decrypt($this->sensitiveData), true);

        return new HandshakeOutput(
            apiKey: $this->X_KM_Api_Key,
            key: $sensitiveResponse['key'],
            iv: $sensitiveResponse['iv'],
            keyId: $sensitiveResponse['keyId']
        );
    }

    public static function fromArray(array $array): HandshakeApiResponse
    {
        return new HandshakeApiResponse(
            X_KM_Api_Key: $array["X_KM_Api_Key"],
            sensitiveData: $array['sensitiveData'],
            signature: $array['signature']
        );
    }

    public static function fromCurlResponse($result, $headerSize): HandshakeApiResponse
    {
        $headers = explode("\r\n", substr($result, 0, $headerSize));
        $X_KM_Api_Key = substr($headers[7], 13);

        $body = substr($result, $headerSize);
        $data = json_decode($body, true);
        $sensitiveData = json_encode($data["sensitiveData"]);
        $signature = json_encode($data["signature"]);

        return self::fromArray([
            "X_KM_Api_Key" => $X_KM_Api_Key,
            "sensitiveData" => $sensitiveData,
            "signature" => $signature
        ]);
    }
}
