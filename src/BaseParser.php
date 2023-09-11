<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

abstract class BaseParser
{
    protected array $output;

    public function __construct(
        protected string $input,
        protected array $data = []
    ) {
        $this->prepareProcess();
        $this->processOutput();
    }

    public static function parse(...$arguments): static
    {
        return new static(...$arguments);
    }

    abstract protected function prepareProcess(): void;

    abstract protected function processOutput(): void;

    abstract public function getDto(): DataTransferObject;
}
