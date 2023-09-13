<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce\Shopware;

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
                return $this->crawler->filter('.product--box')->each(function ($node, $i) {
                    return [
                        'title' => $node->filter('.product--title')->text(),
                        'description' => convertHtmlToTextRows($node->filter('.product--description')->html()),
                        'image' => explode(' ', $node->filter('img')->attr('srcset'))[0],
                        'url' => $node->filter('a')->attr('href'),
                        'price' => $node->filter('.product--price-info')->each(function ($node, $i) {
                            return [
                                'unit' => $node->filter('.price--unit')->text(),
                                'old' => $node->filter('.price--pseudo')->text(),
                                'now' => $node->filter('.price--default')->text(),
                            ];
                        })[0],
                        'tax' => $node->filter('.tax')->text(),
                        'shipping' => $node->filter('.delivery--information')->text(),
                        'ribbons' => $node->filter('.product--badge')->each(function ($node, $i) {
                            return $node->text();
                        }),
                    ];
                });
            })(),
            'pagination' => [
                'page' => $page = (int) $this->crawler->filter('#pagination .is--active')->text(),
                'pages' => $pages = (int) $this->crawler->filter('#pagination .paging--display strong')->text(),
                'nextPage' => $page < $pages,
            ],
        ];
    }

    public function getDto(): ProductListDto
    {
        return new ProductListDto($this->output);
    }
}
