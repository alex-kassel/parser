<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce\Gambio;

use AlexKassel\Parser\HtmlParser;
use AlexKassel\Parser\ECommerce\ProductListDto;

class ProductListParser extends HtmlParser
{
    protected function processOutput(): void
    {
        $this->output = [
            'url' => $this->data['url'] ?? null,
            'title' => $this->crawler->filter('title')->text(),
            'products' => $this->crawler->filter('.product-container')->each(function ($node) {
                return [
                    'article' => null,
                    'title' => $node->filter('.title')->text(),
                    'description' => $node->filter('.description')->textRows(),
                    'image' => $node->filter('img')->attr('src'),
                    'url' => $node->filter('a')->attr('href'),
                    'price-tax' => $node->filter('.price-tax')->textRows(function ($rows) {
                        array_pop($rows);
                        return $rows;
                    }),
                    'price' => $node->filter('.current-price-container')->each(function ($node, $i) {
                        return [
                            'unit' => null,
                            'old' => $node->filter('.productOldPrice')->text(),
                            'now' => $node->filter('.productNewPrice')->text() ?: $node->innerText(),
                        ];
                    })[0],
                    'tax' => $node->filter('.tax')->text(),
                    'shipping' => $node->filter('.shipping-info-short')->text(),
                    'ribbons' => $node->filter('.ribbons div')->each(function ($node, $i) {
                        return [
                            'image' => null,
                            'alt' => null,
                            'content' => $node->text(),
                        ];
                    }),
                ];
            }),
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
