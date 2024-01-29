<?php

namespace Sergmoro1\Imageable\Tests;

use Sergmoro1\Imageable\ImageableServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }
    
    protected function setUpDatabase()
    {
        $this->artisan('migrate')->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            ImageableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
