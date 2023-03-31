<?php

namespace Polygontech\NagadDisbursement;

use Carbon\Carbon;
use Polygontech\CommonHelpers\HTTP\URL;

/**
 * @internal
 */
class Config
{
    public function __construct(
        private readonly string $baseUrl,
        public readonly string $merchantAggegatorId,
        public readonly string $merchantId,
        public readonly string $pgPublicKey,
        public readonly string $merchantPrivateKey,
        public readonly string $hmacKey,
    ) {
    }

    private function getPgPublicKeyWithHeader()
    {
        return "-----BEGIN PUBLIC KEY-----\n" . $this->pgPublicKey . "\n-----END PUBLIC KEY-----";
    }

    private function getMerchantPrivateKeyWithHeader()
    {
        return "-----BEGIN RSA PRIVATE KEY-----\n" . $this->merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
    }

    public function encrypt(string $data): string
    {
        $publicKey = $this->getPgPublicKeyWithHeader();
        $keyResource = openssl_get_publickey($publicKey);
        openssl_public_encrypt($data, $cryptText, $keyResource);
        return base64_encode($cryptText);
    }

    public function generateSignature(string $data): string
    {
        $privateKey = $this->getMerchantPrivateKeyWithHeader();
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    private function concat(): string
    {
        return $this->merchantAggegatorId . Helpers::formatTime(Carbon::now()) . Helpers::generateRandomString();
    }

    public function generateSignatureWithOutData(): string
    {
        $data = $this->concat();
        $privateKey = $this->getMerchantPrivateKeyWithHeader();
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function decrypt(string $cryptText): string
    {
        $privateKey = $this->getMerchantPrivateKeyWithHeader();
        openssl_private_decrypt(base64_decode($cryptText), $plainText, $privateKey);
        return $plainText;
    }

    public function makeBaseUrl(): URL
    {
        return URL::parse($this->baseUrl);
    }

    public function makeUrlOfPath(string $path): URL
    {
        return $this->makeBaseUrl()->mutatePath($path);
    }

    public function generateSignatureWithoutKey(array $data): string
    {
        $HMAC_KEY = $this->hmacKey;
        $hmac_key = hex2bin($HMAC_KEY);
        $hash_value = hash_hmac('sha256', json_encode($data), $hmac_key, true);
        return base64_encode($hash_value);
    }
}
