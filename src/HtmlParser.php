<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

use AlexKassel\Fetcher\CrawlerWrapper;

abstract class HtmlParser extends BaseParser
{
    protected CrawlerWrapper $crawler;

    protected function prepareProcess(): void {
        $this->crawler = new CrawlerWrapper($this->input);
    }
}
