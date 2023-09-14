<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

abstract class BaseParser
{
    protected array $output = [];

    public function __construct(
        protected string $input,
        protected array $data = []
    ) {
        $this->prepareProcess();
        $this->processOutput();
    }

    public static function parse(...$arguments): array
    {
        return (new static(...$arguments))
            ->getDto()
            ->output
        ;
    }

    abstract protected function prepareProcess(): void;

    abstract protected function processOutput(): void;

    abstract public function getDto(): DataTransferObject;
}
