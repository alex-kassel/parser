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
            'page_title' => strval($this->input['title'] ?? ''),
            'items' => (function (): array {
                $validated = [];
                foreach ($this->input['items'] ?? [] as $item) {
                    $validated[] = [
                        'id' => strval($item['id'] ?? ''),
                        'slug' => strval($item['slug'] ?? ''),
                        'name' => strval($item['name'] ?? ''),
                        'description' => (function () use ($item): array {
                            $validated = [];
                            foreach ($item['description'] ?? [] as $row) {
                                if ($row = trim($row)) $validated[] =$row;
                            }

                            return $validated;
                        })(),
                        'image_url' => strval($item['image_url'] ?? ''),
                        'product_url' => strval($item['product_url'] ?? ''),
                        'price_info' => (array) ($item['price_info'] ?? []),
                        'price' => [
                            'unit' => strval($item['price']['unit'] ?? ''),
                            'old' => strval($item['price']['old'] ?? ''),
                            'now' => strval($item['price']['now'] ?? ''),
                        ],
                        'tax' => strval($item['tax'] ?? ''),
                        'shipping' => strval($item['shipping'] ?? ''),
                        'ribbons' => (function () use ($item): array {
                            $validated = [];
                            foreach ($item['ribbons'] ?? [] as $ribbon) {
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
            'current_page' => intval($this->input['page'] ?? 0) ?: null,
            'total_pages' => intval($this->input['pages'] ?? 0) ?: null,
            'next_page' => boolval($this->input['nextPage'] ?? false),
        ];
    }
}
