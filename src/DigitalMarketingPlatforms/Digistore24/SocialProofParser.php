<?php

declare(strict_types=1);

namespace AlexKassel\Parser\DigitalMarketingPlatforms\Digistore24;

use AlexKassel\Parser\JsParser;

class SocialProofParser extends JsParser
{
    public static function urlPattern(): array
    {
        return [
            '.checkout-ds24.com/socialproof/'
        ];
    }

    protected function processOutput(): void
    {
        if(! str_contains($this->input, 'DS24_BUYER_LIST')) {
            $this->output = [
                'error' => trim($this->input, '/*. ')
            ];

            return;
        }

        $json = substr(
            $this->input,
            $start = strpos($this->input, '['),
            strpos($this->input, ']') - $start + 1
        );

        $json = preg_replace(
            '~(headline|message|footline|image_url): ~',
            '"$1": ',
            $json
        );

        $json_decoded = json_decode(strip_tags($json), true);

        $this->output = [
            'DS24_BUYER_COUNT' => count($json_decoded),
            'DS24_BUYER_LIST' => $json_decoded,
        ];
    }

    public function getDto(): SocialProofDto
    {
        return new SocialProofDto($this->output);
    }
}
