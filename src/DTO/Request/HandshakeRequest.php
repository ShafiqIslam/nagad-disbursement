<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Carbon\Carbon;
use Polygontech\NagadDisbursement\Config;
use Polygontech\NagadDisbursement\Helpers;

/**
 * @internal
 */
class HandshakeRequest extends Request
{
    private readonly string $signature;
    private readonly string $formattedTime;

    public function __construct(
        private readonly string $merchantAggregatorId,
        private readonly Carbon $time,
        private readonly string $random,
    ) {
        $this->formattedTime = Helpers::formatTime($time);
    }

    private function concat(): string
    {
        return $this->merchantAggregatorId . $this->formattedTime . $this->random;
    }

    public function withSignature(Config $config): self
    {
        $this->signature = $config->generateSignature($this->concat());
        return $this;
    }

    public function toArray(): array
    {
        return [
            'merchantAggregatorId' => $this->merchantAggregatorId,
            'requestDateTime' => $this->formattedTime,
            'random' => $this->random,
            'signature' => $this->signature,
        ];
    }

    public static function fromMerchantAggregatorID(string $merchantAggregatorId): HandshakeRequest
    {
        return new HandshakeRequest(
            merchantAggregatorId: $merchantAggregatorId,
            time: Carbon::now(),
            random: Helpers::generateRandomString()
        );
    }
}
