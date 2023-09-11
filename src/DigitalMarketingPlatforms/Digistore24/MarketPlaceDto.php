<?php

declare(strict_types=1);

namespace AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24;

use AlexKassel\Parser\DataTransferObject;

final readonly class MarketPlaceDto extends DataTransferObject
{
    protected function processOutput(): array
    {
        return $this->input;
    }
}
