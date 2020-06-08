<?php

namespace IvanSotelo\OpenpayCashier\Tests;

use IvanSotelo\OpenpayCashier\OpenpayCashierServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [OpenpayCashierServiceProvider::class];
    }
}
