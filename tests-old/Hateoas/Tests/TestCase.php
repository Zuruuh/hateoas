<?php

declare(strict_types=1);

namespace Zuruuh\Hateoas\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnit_Framework_IncompleteTestError;

abstract class TestCase extends BaseTestCase
{
    public static function rootPath()
    {
        return __DIR__;
    }

    protected function json($string)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new PHPUnit_Framework_IncompleteTestError('This test requires PHP 5.4+');
        }

        return json_encode(json_decode((string) $string), JSON_PRETTY_PRINT);
    }
}
