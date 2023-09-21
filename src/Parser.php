<?php

declare(strict_types=1);

namespace AlexKassel\Parser;

class Parser
{
   public static function parse(string $input, array $data = []): array
   {
       if ($url = $data['url'] ?? '') {
           return (static::getClass($url))::parse($input, $data);
       }

       throw new \InvalidArgumentException("Parameter 'url' in second argument (array) expected");
   }

   protected static function getClass(string $url): string
    {
        #$globBrace = '{DigitalMarketingPlatforms,MarketPlaces}';
        #$pattern = sprintf('%s/%s/*/*Parser.php', __DIR__, $globBrace);
        #$files = glob($pattern, GLOB_BRACE);
        $files = glob(sprintf('%s/*/*/*Parser.php', __DIR__));

        foreach($files as $file) {
            $parser = __NAMESPACE__ . str_replace('/', '\\',
                substr($file, strpos($file, '/src/') + 4, -4)
            );

            foreach((array) $parser::urlPattern() as $pattern) {
                if (str_contains_all($url, $pattern)) {
                    return $parser;
                }
            }
        }

        throw new \InvalidArgumentException("No parser found for '$url'");
    }
}
