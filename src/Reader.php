<?php

namespace Menrui;

use Iterator;
use Menrui\Exception;

class Reader
{
    public static function read(string $file, array $filters = []): Iterator
    {
        $items = static::readFile($file);
        if (empty($filters)) {
            foreach ($items as $item) {
                yield $item;
            }
        } else {
            foreach ($items as $item) {
                if (array_all($filters, fn ($filter) => self::filterItem($filter, $item))) {
                    yield $item;
                }
            }
        }
    }

    public static function readFile(string $file): Iterator
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            throw new Exception("Failed to open file: $file");
        }
        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
        fclose($handle);
    }

    public static function filterItem(string $filter, mixed $item): bool
    {
        if (is_string($filter)) {
            $method = 'is' . ucfirst($filter);
            if (method_exists(static::class, $method) && static::$method($item)) {
                return true;
            }
        }
        if (is_callable($filter) && call_user_func($filter, $item)) {
            return true;
        }
        return false;
    }
}
