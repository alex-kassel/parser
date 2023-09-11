<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

use function Symfony\Component\Translation\t;

/**
 * A factory to instantiate the proper parser.
 */
class Parser
{
    public static function parse(string $url, string $html): BaseParser
    {
        return (new static())->getInstance($url, $html);
    }

    protected function getInstance(string $url, string $html): BaseParser
    {
        $class = $this->getClass($url);
        return new $class($html);
    }

    protected function getClass(string $url): string
    {
        switch ($url) {
            case str_contains_a($url, '.checkout-ds24.com/product/'):
            case str_contains_a($url, '.checkout-ds24.com/redir/'):
                return \AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24\CheckoutParser::class;
            case str_contains_a($url, '.checkout-ds24.com*/marketplace'):
                return \AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24\MarketPlaceParser::class;
            default: throw new \InvalidArgumentException("No proper parser for '$url'");
        }
    }
}
