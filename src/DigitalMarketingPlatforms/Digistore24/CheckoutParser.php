<?php

declare(strict_types=1);

namespace AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24;

use AlexKassel\Parser\HtmlParser;

class CheckoutParser extends HtmlParser
{
    use PlatformTrait;

    protected function processOutput(): void
    {
        $this->output = [
            'base_url' => $this->base_url,
            'status' => [
                'code' => $this->data['status_code'] ?? 201,
            ]
        ];

        switch ($this->output['status']['code']) {
            case 200:
                if(substr($this->input, 0, 3) == '<h1') {
                    $this->output['status']['message'] = strip_tags($this->input);
                    break; // very rare
                }

                if(strpos($this->input, 'redirect_to_target_page()')) {
                    $this->output['status']['message'] = 'Sorry, the product is not available in your country.';
                    break; // 396593, 483329
                }

                $this->output += [
                    'global' => [
                        'lang' => $this->crawler->filter('html')->attr('lang')
                            ?? (
                            preg_match('~language/([\w]+)/common~', $this->input, $matches)
                                ? $matches[1]
                                : null
                            ),
                        'title' => $this->crawler->filter('title')->text(''),
                        'variant' => preg_match('~orderform type: (\w+)~', $this->input, $matches)
                            ? $matches[1]
                            : ''
                        ,
                        'merchant' => preg_match('/merchant_([0-9]+)/', $this->input, $matches)
                            ? $matches[1]
                            : ''
                        ,
                    ],
                    'opengraph' => $this->crawler->filter('meta[property^="og:"]')->count() > 3
                        ? [
                            'image' => $this->formatUrl($this->crawler->filter('meta[property="og:image"]')->attr('content')),
                            'title' => $this->crawler->filter('meta[property="og:title"]')->attr('content'),
                            'description' => $this->crawler->filter('meta[property="og:description"]')->attr('content'),
                        ]
                        : [],
                ];

                if ($error = $this->crawler->filter('.error li')->text('')) {
                    $this->output['status']['message'] = $error;
                    break;
                }

                if (strpos($this->input, 'PGB_PUBLIC_PATH')) {
                    $this->output['global']['variant'] = 'pgb';
                }

                if (preg_match('~track/orderform/(\d+).+/initial/(\d+)~', $this->input, $matches)) {
                    $this->output['global']['merchant'] = $matches[1];
                    $this->output['global']['affiliate']['id'] = $matches[2];
                }

                if (preg_match('~[^"\']+24.com/socialproof/[^"\']+~', $this->input, $matches)) {
                    $this->output['assets']['socialproof'] = $matches[0];
                }

                if (preg_match_all('~[^"\']+/merchant_[^"\']+~', $this->input, $matches)) {
                    $images = str_replace('\\', '', $matches[0]);
                    $this->output['assets']['images'] = collect($images)->map(function($image) {
                        return $this->formatUrl($image);
                    })->all();
                }

                #$processor = $this->getProcessor($this->output['global']['variant'], $this->crawler);
                #$this->output = $processor->expandOutput($this->output);

                #$this->output['global']['affiliate']['default'] = ! in_array($affiliate_id,
                # $this->output['global']['affiliate']);

                break;
            case 301:
            case 302:
                $this->output['status']['location'] = $this->crawler->getHeaders()['location'][0];
                break;
            default:
                $this->output['status']['message'] = $this->crawler->filter('#container')->text('');
        }

        #$this->output['redir_info'] = $this->redirInfo($product_id);
    }

    public function getDto(): CheckoutDto
    {
        return new CheckoutDto($this->output);
    }
}
