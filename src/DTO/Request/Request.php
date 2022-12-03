<?php

namespace Polygontech\NagadDisbursement\DTO\Request;

/**
 * @internal
 */
abstract class Request
{
    abstract public function toArray(): array;

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
