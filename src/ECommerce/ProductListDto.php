<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce;

use AlexKassel\Parser\DataTransferObject;

final readonly class ProductListDto extends DataTransferObject
{
    protected function processOutput(): array
    {
        return $this->input;
        return [
            'page_url' => strval($this->input['page_url'] ?? '') ?: null,
            'page_title' => strval($this->input['page_title'] ?? ''),
            'items' => (function (): array {
                $data = [];
                foreach ($this->input['items'] ?? [] as $item) {
                    $data[] = [
                        'id' => strval($item['id'] ?? ''),
                        'slug' => strval($item['slug'] ?? ''),
                        'name' => strval($item['name'] ?? ''),
                        'description' => (function () use ($item): array {
                            $data = [];
                            foreach ($item['description'] ?? [] as $row) {
                                if ($row = trim($row)) $data[] = $row;
                            }

                            return $data;
                        })(),
                        'image_url' => strval($item['image_url'] ?? ''),
                        'item_url' => strval($item['item_url'] ?? ''),
                        'price_info' => (function () use ($item): array {
                            $data = [];
                            foreach ($item['price_info'] ?? [] as $row) {
                                if ($row = trim($row)) $data[] = $row;
                            }

                            return $data;
                        })(),
                        'price' => [
                            'unit' => strval($item['price']['unit'] ?? ''),
                            'old' => strval($item['price']['old'] ?? ''),
                            'now' => strval($item['price']['now'] ?? ''),
                        ],
                        'tax' => strval($item['tax'] ?? ''),
                        'shipping' => strval($item['shipping'] ?? ''),
                        'ribbons' => (function () use ($item): array {
                            $data = [];
                            foreach ($item['ribbons'] ?? [] as $ribbon) {
                                $data[] = [
                                    'image' => strval($ribbon['image'] ?? ''),
                                    'alt' => strval($ribbon['alt'] ?? ''),
                                    'content' => strval($ribbon['content'] ?? ''),
                                ];
                            }

                            return $data;
                        })(),
                    ];
                }

                return $data;
            })(),
            'current_page' => intval($this->input['page'] ?? 0) ?: null,
            'total_pages' => intval($this->input['pages'] ?? 0) ?: null,
            'next_page' => boolval($this->input['nextPage'] ?? false),
        ];
    }
}
