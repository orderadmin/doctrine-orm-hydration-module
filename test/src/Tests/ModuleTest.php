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
    public function itShouldBeInitializable()
    {
        $module = new Module();
        $this->assertInstanceOf('ApiSkeletons\DoctrineORMHydrationModule\Module', $module);
    }

    /**
     * @test
     */
    public function itShouldProvideConfiguration()
    {
        $module = new Module();
        $this->assertIsArray($module->getConfig());
    }
}
