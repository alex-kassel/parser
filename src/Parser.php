<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

use JetBrains\PhpStorm\NoReturn;

class Parser
{
   public static function glob(): array
    {
        return glob(__DIR__ . '/DigitalMarketingPlatforms/*/*Parser.php');
    }

    public static function use(string $url): string
    {
        $config = require('config.php');

        switch ($url) {
            case str_contains($url, '.checkout-ds24.com/product/'):
            case str_contains($url, '.checkout-ds24.com/redir/'):
                return \AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24\CheckoutParser::class;
            case str_contains_a($url, '.checkout-ds24.com*/marketplace'):
                return \AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24\MarketPlaceParser::class;
            case str_contains($url, '.checkout-ds24.com/socialproof/'):
                return \AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24\SocialProofParser::class;
            default: throw new \InvalidArgumentException("No proper parser for '$url'");
        }
    }
}
