<?php

declare(strict_types=1);

namespace AlexKassel\Parser\MarketPlaces\MobileDe;

use AlexKassel\Parser\HtmlParser;
use AlexKassel\Parser\MarketPlaces\ProductListDto;

class ProductListParser extends HtmlParser
{
    public static function urlPattern(): array
    {
        return [
            'suchen.mobile.de/fahrzeuge/search.html',
        ];
    }

    protected function processOutput(): void
    {
        $this->output = [
            'items' => $items = $this->crawler->filter('a[data-listing-id]')->each(function ($node) {
                return [
                    'id' => $node->attr('data-listing-id'),
                    'sponsored' => $node->filter('.top-label')->text() != '',
                    'name' => $node->filter('.headline-block .h3')->text(),
                    'description' => '',
                    'details' => $node->filter('div[class^="vehicle-data"]')->textRows(),
                    'bullets' => $node->filter('.bullet-list p')->texts(),
                    'image' => $node->filter('img')->attr('src'),
                    'url' => $node->attr('href'),
                    'price' => [
                        'old' => $node->filter('.price-block .h2')->text(),
                        'now' => $node->filter('.price-block .h3')->text(),
                        'vat' => $node->filter('.price-block [class="u-block"]')->text(),
                        'label' => $node->filter('.mde-price-rating__badge__label')->text(),
                    ],
                    'seller' => $node->filter('.u-valign-middle')->closest('.g-row')->textRows(),
                    'place' => '',
                    'date' => '',
                    'more_info' => [],
                ];
            }),
            'pagination' => [
                'current_page' => $this->crawler->filter('.pagination .disabled')->text(),
                'previous_page' => $this->crawler->filter('#page-back')->attr('data-href') ?: '',
                'next_page' => $this->crawler->filter('#page-forward')->attr('data-href') ?: '',
            ],
            'more_info' => [],
        ];
    }

    public function getDto(): ProductListDto
    {
        return new ProductListDto($this->output);
    }
}
