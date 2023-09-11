<?php

declare(strict_types=1);

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
