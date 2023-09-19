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
                    'name' => $name = $node->filter('h2')->text(),
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
                    'place' => explode(' ', $node->filter('.aditem-main--top--left')->text())[0]
                        . rtrim(str_replace([$name, 'Vorschau'], '', $node->filter('img')->attr('alt')))
                        ,
                    'date' => $node->filter('.aditem-main--top--right')->text(),
                    'more_info' => [
                        'image_counter' => (int) $node->filter('.galleryimage--counter')->text(),
                    ],
                ];
            }),
            'pagination' => [
                'current_page' => $this->crawler->filter('.pagination-current')->text(),
                'previous_page' => ($path = $this->crawler->filter('.pagination-prev')->attr('href'))
                    ? $this->base_url . $path
                    : ''
                    ,
                'next_page' => ($path = $this->crawler->filter('.pagination-next')->attr('href'))
                    ? $this->base_url . $path
                    : ''
                    ,
            ],
            'more_info' => [
                'dimensions' => preg_match('/dimensions: ({.*?}),\s*extraDimensions:/s', $this->crawler->text(), $matches)
                    ? json_decode_recursive($matches[1])
                    : []
                    ,
            ],
        ];
    }

    public function getDto(): ProductListDto
    {
        return new ProductListDto($this->output);
    }
}
