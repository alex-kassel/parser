<?php

declare(strict_types=1);

namespace AlexKassel\Parser\DigitalMarketingPlatforms;

use AlexKassel\Parser\HtmlParser;

abstract class AbstractParser extends HtmlParser
{
    abstract public static function urlPattern(): array|string;
}
