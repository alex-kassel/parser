<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

abstract readonly class DataTransferObject
{
    public array $output;

    public function __construct(
        public array $input
    ) {
        $this->output = array_filter_recursive(
            [
                ...$this->processOutput(),
                ...$this->debug(),
            ],
            function($value, $key) {
                return ! is_null($value);
            },
            false
        );
    }

    abstract protected function processOutput(): array;

    protected function debug(): array
    {
        return [
            'exec_time' => exec_time(),
        ];
    }
}
