<?php

declare(strict_types=1);

namespace AlexKassel\Parser\MarketPlaces\KleinanzeigenDe;

use AlexKassel\Parser\HtmlParser;
use AlexKassel\Parser\MarketPlaces\ProductListDto;

class ProductListParser extends HtmlParser
{
    use ServiceTrait;

    public static function urlPattern(): array
    {
        return [
            'www.kleinanzeigen.de/s-',
        ];
    }

    protected function processOutput(): void
    {
        $this->output = [
            'items' => $this->crawler->filter('.aditem')->each(function ($node) {
                return [
                    'id' => $node->attr('data-adid'),
                    'sponsored' => ! is_null($node->closest('li.is-topad')),
                    'name' => $node->filter('h2')->text(),
                    'description' => $node->filter('.aditem-main--middle--description')->text(),
                    'details' => $node->filter('.aditem-main--bottom > p > span')->texts(),
                    'bullets' => [],
                    'image' => $node->filter('img')->attr('src'),
                    'url' => $this->base_url . $node->attr('data-href'),
                    'price' => [
                        'old' => '',
                        'now' => $node->filter('.aditem-main--middle--price-shipping')->text(),
                        'vat' => '',
                        'label' => '',
                    ],
                    'seller' => $node->filter('.aditem-main--bottom a')->last()->each(function ($node) {
                        return [
                            'name' => $node->text(),
                            'url' => $this->base_url . $node->attr('href'),
                        ];
                    })[0] ?? [],
                    'place' => $node->filter('.aditem-main--top--left')->text(),
                    'date' => $node->filter('.aditem-main--top--right')->text(),
                    #'image_counter' => $node->filter('.galleryimage--counter')->text(),
                ];
            }),
        ];
    }

    public function getDto(): ProductListDto
    {
        return new ProductListDto($this->output);
    }
}
