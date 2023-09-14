<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

use AlexKassel\Fetcher\Crawler;

abstract class HtmlParser extends BaseParser
{
    protected Crawler $crawler;

    protected function prepareProcess(): void {
        $this->crawler = new Crawler($this->input);
    }
}
