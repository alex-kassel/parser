<?php

declare(strict_types=1);

namespace AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24;

use AlexKassel\Parser\HtmlParser;

class MarketPlaceParser extends HtmlParser
{
    use ServiceTrait;

    public static function urlPattern(): array|string
    {
        return '.checkout-ds24.com*/marketplace/';
    }

    protected function processOutput(): void
    {
        $this->output = [
            'base_url' => (string) $this->base_url,
            'page' => $page = (int) $this->crawler->filter('.pagination li.active a')->text('1'),
            'pages' => (int) explode('=', $this->crawler->filter('.pagination li:last-child a')->attr('href', '=1'))[1],
            'itemsTotal' => (int) $this->crawler->filter('.resultCount .count')->text(),
            'items' => $this->crawler->filter('.product')->each(function($node, $i) use($page) {
                return [
                    'sort_order' => ($page - 1) * 5 + $i + 1,
                    'product_id' => (int) explode('_', $node->filter('.promo_button')->attr('id'))[1],
                    'name' => $node->filter('h3')->innerText(),
                    'description' => $node->filter('.productText')->html(),
                    'image' => $this->formatUrl($node->filter('.product_thumb')->attr('src')),
                    'rating' => str_to_float(explode(':', $node->filter('.performance .staricon_container')->attr('title'))[1]),
                    'tags' => $node->filter('.tags .shadow')->each(function ($node) {
                        return explode(': ', $node->text());
                    }, true),
                    'links' => $node->filter('.col-lg-3 .underline')->each(function($node) {
                        return [
                            $node->text(),
                            $node->attr('href'),
                        ];
                    }, true),
                ];
            }),
        ];
    }

    public function getDto(): MarketPlaceDto
    {
        return new MarketPlaceDto($this->output);
    }
}
