<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

declare(strict_types=1);

return static function (RectorConfig $rector): void {
    $rector->sets([
        SetList::PHP_81,
    ]);
};
