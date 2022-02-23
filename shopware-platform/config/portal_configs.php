<?php

declare(strict_types=1);

/* Example usage for alias 'bottle' */
/*
function bottle(array $storedConfig): array {
    $storedConfig['black'] = "#abcdef";
    return $storedConfig;
}
*/

return function (string $alias, array $storedConfig){
    try {
        return $alias($storedConfig) ? : $storedConfig;
    } catch (\Throwable $undefinedMethodError) {
        return $storedConfig;
    }
};
