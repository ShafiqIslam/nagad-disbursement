<?php

namespace Polygontech\NagadDisbursement\DTO\Output;

class HandshakeOutput
{
    private static $cipher = "AES-128-CBC";

    public readonly string $decodedKey;
    public readonly string $decodedIv;

    public function __construct(
        public readonly string $apiKey,
        public readonly string $key,
        public readonly string $iv,
        public readonly string $keyId,
        public readonly ?string $challenge
    ) {
        $this->decodedKey = base64_decode($key);
        $this->decodedIv = base64_decode($iv);
    }

    public function encrypt(string $data): string
    {
        return base64_encode(openssl_encrypt($data, self::$cipher, $this->decodedKey, OPENSSL_RAW_DATA, $this->decodedIv));
    }

    public function decrypt(string $data): string
    {
        return openssl_decrypt(base64_decode($data), self::$cipher, $this->decodedKey, OPENSSL_RAW_DATA, $this->decodedIv);
    }
}
