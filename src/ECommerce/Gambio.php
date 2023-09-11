<?php

declare(strict_types=1);

namespace AlexKassel\Parser\ECommerce;

use AlexKassel\Parser\HtmlParser;

class Gambio extends HtmlParser
{
    protected function process(): void
    {
        $this->output = (object) [
            'dies' => 'und das',
        ];
    }
}
