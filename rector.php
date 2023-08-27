<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rector): void {
    $rector->sets([
        SetList::PHP_81,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
    ]);

    $rector->rule(ClassPropertyAssignToConstructorPromotionRector::class);
    $rector->paths(['src', 'tests']);
};
