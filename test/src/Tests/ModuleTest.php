<?php

namespace ApiSkeletonsTest\DoctrineORMHydrationModule\Tests;

use ApiSkeletons\DoctrineORMHydrationModule\Module;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest.
 */
class ModuleTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_be_initializable()
    {
        $module = new Module();
        $this->assertInstanceOf('ApiSkeletons\DoctrineORMHydrationModule\Module', $module);
    }

    /**
     * @test
     */
    public function it_should_provide_configuration()
    {
        $module = new Module();
        $this->assertIsArray($module->getConfig());
    }
}
