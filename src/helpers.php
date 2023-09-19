<?php

declare(strict_types=1);

if (! function_exists('json_decode_recursive')) {
    function json_decode_recursive(mixed $mixed, ?bool $associative = null, int $depth = 512, int $flags = 0): mixed {
        if (is_object($mixed)) {
            $mixed = (array) $mixed;
        }

        if (is_array($mixed)) {
            $data = [];
            foreach ($mixed as $key => $value) {
                $data[$key] = json_decode_recursive($value, $associative, $depth, $flags);
            }

            return $associative ? $data : (object) $data;
        }

        if (is_string($mixed)) {
            return  is_null($json_decoded = json_decode($mixed, $associative, $depth, $flags))
                ? $mixed
                : json_decode_recursive($json_decoded, $associative, $depth, $flags)
                ;
        }

        return $mixed;
    }
}

if (! function_exists('convertHtmlToTextRows')) {
    function convertHtmlToTextRows(string $html): array {
        $search = ['<hr>', '<br>', '<br />', '</li>', '</tr>', '</p>', '</div>'];
        
        $html = preg_replace('/<(script|style).*?<\/\\1>/si', '', $html);
        $html = str_replace($search, ' • ', html_entity_decode($html));
        $html = preg_replace('/\s+/u', ' ', strip_tags($html));

        $lines = [];
        foreach (explode('•', $html) as $line) {
            if ($line = trim($line)) {
                $lines[] = $line;
            }
        }

        return $lines;
    }
}

if (! function_exists('str_squish')) {
    /**
     * Squish a string by replacing multiple spaces with a single replacement and trim it.
     *
     * @param string $string The input string to squish.
     * @param bool $replaceNonBreakingSpaces (Optional) Whether to replace non-breaking spaces with spaces. Default is true.
     * @param string $characters (Optional) A list of additional characters to trim. Default is null.
     * @param string $replacement (Optional) The character to replace multiple spaces with. Default is a single space " ".
     *
     * @return string The squished string.
     */
    function str_squish(
        string $string,
        bool $replaceNonBreakingSpaces = true,
        string $characters = null,
        string $replacement = ' '
    ): string {
        if ($replaceNonBreakingSpaces) {
            // Replace &nbsp; and its UTF-8 incarnations with spaces if they exist
            $string = preg_replace('/(&nbsp;|\xC2\xA0|\xE1\x9A\x80|\xE2\x80\xAF|\xE3\x80\x80)/u', ' ', $string);
        }

        // Trim the string, optionally using the provided character list
        $string = trim($string, strval($characters) . " \n\r\t\v\x00");

        // Replace multiple spaces with the specified replacement string and return
        return preg_replace('/(?:' . preg_quote($replacement, '/') . '|\s)+/', $replacement, $string);
    }
}

if (! function_exists('str_contains_a')) {
    /**
     * Like str_contains(), but accepts placeholder asterisk *
     */
    function str_contains_a(string $haystack, string $needle): bool {
        foreach (explode('*', $needle) as $part) {
            if (false === $pos = strpos($haystack, $part)) {
                return false;
            }
            $haystack = substr($haystack, $pos + strlen($part));
        }

        return true;
    }
}

if (! function_exists('format_url')) {
    function format_url(?string $url, string $base_url = '', mixed $mixed = null): ?string
    {
        if (is_null($url)) {
            return null;
        }

        if (isset($mixed['regex']) && is_string($mixed['regex'])) {
            $regex = $mixed['regex'];
        } elseif (is_string($mixed)) {
            $regex = $mixed;
        } else {
            $regex = '';
        }

        if ($regex === '' || ! preg_match(sprintf('~%s~i', $regex), $url)) {
            return null;
        }

        if (substr($url, 0, 4) !== 'http') {
            $url = sprintf('%s/%s', rtrim($base_url, '/'), ltrim($url, '/'));
        }

        if (isset($mixed['replace']['search']) && isset($mixed['replace']['replace'])) {
            $url = str_replace($mixed['replace']['search'], $mixed['replace']['replace'], $url);
        }

        return $url;
    }
}

if (! function_exists('exec_time')) {
    function exec_time(float $start_time_float = 0, int $precision = 3): float {
        return round(
            microtime(true) - ($start_time_float ?: $_SERVER['REQUEST_TIME_FLOAT']),
            $precision
        );
    }
}

if (!function_exists('str_squish')) {
    function str_squish(string $string): string {
        return preg_replace('/\s+/', ' ', trim($string));
    }
}

if (! function_exists('str_to_float')) {
    function str_to_float(string $num): float {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : null);

        if (!$sep) {
            return floatval(
                preg_replace(
                    sprintf(
                        '/[^0-9\%s]/',
                        $sep ? '\\' . $sep : ''
                    ), "", $num
                )
            );
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }
}

if (! function_exists('array_filter_recursive')) {
    function array_filter_recursive(array $array, callable $callback = null, bool $remove_empty_arrays = false): array {
        foreach ($array as $key => & $value) { // mind the reference
            if (is_array($value)) {
                $value = call_user_func_array(__FUNCTION__, array($value, $callback, $remove_empty_arrays));
                if ($remove_empty_arrays && ! $value) {
                    unset($array[$key]);
                }
            } elseif (is_null($callback)) {
                if (in_array($value, ['', null], true)) {
                    unset($array[$key]);
                }
            } elseif (! $callback($value, $key)) {
                unset($array[$key]);
            }
        }
        unset($value); // kill the reference

        return $array;
    }
}
