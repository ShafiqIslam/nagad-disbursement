<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

use Polygontech\NagadDisbursement\DTO\Output\HandshakeOutput;

/**
 * @internal
 */
abstract class RequestWithHandshake extends Request
{
    public function toArrayWithHandshake(HandshakeOutput $handshake): array
    {
        return [
            "sensitiveData" => $handshake->encrypt($this->toJson()),
        ];
    }

    public function toJsonWithHandshake(HandshakeOutput $handshake): string
    {
        return json_encode($this->toArrayWithHandshake($handshake));
    }
}
