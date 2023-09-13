<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce;

use AlexKassel\Parser\DataTransferObject;

final readonly class ProductListDto extends DataTransferObject
{
    protected function processOutput(): array
    {
        return [
            'url' => strval($this->input['url'] ?? '') ?: null,
            'title' => strval($this->input['title'] ?? ''),
            'products' => (function (): array {
                $validated = [];
                foreach ($this->input['products'] ?? [] as $product) {
                    $validated[] = [
                        'article' => strval($product['article'] ?? ''),
                        'title' => strval($product['title'] ?? ''),
                        'description' => (function () use ($product): array {
                            $validated = [];
                            foreach ($product['description'] ?? [] as $row) {
                                if ($row) $validated[] = (string) $row;
                            }

                            return $validated;
                        })(),
                        'image' => strval($product['image'] ?? ''),
                        'url' => strval($product['url'] ?? ''),
                        'price' => [
                            'unit' => strval($product['price']['unit'] ?? ''),
                            'old' => strval($product['price']['old'] ?? ''),
                            'now' => strval($product['price']['now'] ?? ''),
                        ],
                        'tax' => strval($product['tax'] ?? ''),
                        'shipping' => strval($product['shipping'] ?? ''),
                        'ribbons' => (function () use ($product): array {
                            $validated = [];
                            foreach ($product['ribbons'] ?? [] as $ribbon) {
                                $validated[] = [
                                    'image' => strval($ribbon['image'] ?? ''),
                                    'alt' => strval($ribbon['alt'] ?? ''),
                                    'content' => strval($ribbon['content'] ?? ''),
                                ];
                            }

                            return $validated;
                        })(),
                    ];
                }

                return $validated;
            })(),
            'pagination' => [
                'page' => intval($this->input['pagination']['page'] ?? 0) ?: null,
                'pages' => intval($this->input['pagination']['pages'] ?? 0) ?: null,
                'nextPage' => boolval($this->input['pagination']['nextPage'] ?? false),
            ],
        ];
    }
}
