<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce\Magento2;

use AlexKassel\Parser\HtmlParser;
use AlexKassel\Parser\ECommerce\ProductListDto;

class ProductListParser extends HtmlParser
{
    protected function processOutput(): void
    {
        $this->output = [
            'url' => $this->data['url'] ?? null,
            'title' => $this->crawler->filter('title')->text(),
            'products' => (function () {
                return $this->crawler->filter('.product-item-info')->each(function ($node, $i) {
                    return [
                        'title' => $node->filter('.product-item-name')->text(),
                        'description' => [],
                        'image' => $node->filter('img')->attr('src'),
                        'url' => $node->filter('a.product-item-link')->attr('href'),
                        'price' => $node->filter('.price-box')->each(function ($node, $i) {
                            return [
                                'unit' => '',
                                'old' => $node->filter('.productOldPrice')->text(),
                                'now' => $node->filter('.normal-price')->text() ?: $node->innerText(),
                            ];
                        })[0],
                        'tax' => $node->filter('.tax-details')->text(),
                        'shipping' => '',
                        'ribbons' => [],
                    ];
                });
            })(),
            'pagination' => [
                'page' => (int) $this->crawler->filter('.pagination .active')->text(),
                'pages' => null,
                'nextPage' => (bool) $this->crawler->filter('.pagination li')->last()->text(),
            ],
        ];
    }

    public function getDto(): ProductListDto
    {
        return new ProductListDto($this->output);
    }
}
