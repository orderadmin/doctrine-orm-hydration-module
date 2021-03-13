<?php

namespace ApiSkeletonsTest\DoctrineORMHydrationModule\Tests\Hydrator;

use ApiSkeletons\DoctrineORMHydrationModule\Hydrator\DoctrineHydrator;
use PHPUnit\Framework\TestCase;

/**
 * Class DoctrineHydratorTest.
 */
class DoctrineHydratorTest extends TestCase
{
    /**
     * @param null $hydrateService
     * @param null $extractService
     *
     * @return DoctrineHydrator
     */
    protected function createHydrator($hydrateService = null, $extractService = null)
    {
        $hydrateService = $hydrateService ? $hydrateService
            : $this->getMockBuilder('Laminas\Hydrator\HydratorInterface')->getMock();
        $extractService = $extractService ? $extractService
            : $this->getMockBuilder('Laminas\Hydrator\HydratorInterface')->getMock();

        return new DoctrineHydrator($extractService, $hydrateService);
    }

    /**
     * @test
     */
    public function itShouldBeInitializable()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('ApiSkeletons\DoctrineORMHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
    }

    /**
     * @test
     */
    public function itShouldHaveAHydratorService()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Laminas\Hydrator\HydratorInterface', $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function itShouldHaveAnExtractorService()
    {
        $hydrator = $this->createHydrator();
        $this->assertInstanceOf('Laminas\Hydrator\HydratorInterface', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function itShouldExtractAnObject()
    {
        $object = new \stdClass();
        $extracted = array('extracted' => true);
        $extractService = $this->getMockBuilder('Laminas\Hydrator\HydratorInterface')->getMock();
        $extractService
            ->expects($this->any())
            ->method('extract')
            ->will($this->returnValue($extracted));

        $hydrator = $this->createHydrator(null, $extractService);
        $result = $hydrator->extract($object);

        $this->assertEquals($extracted, $result);
    }

    /**
     * @test
     */
    public function itShouldHydrateAnObject()
    {
        $object = new \stdClass();
        $data = array('field' => 'value');

        $hydrateService = $this->getMockBuilder('Laminas\Hydrator\HydratorInterface')->getMock();
        $hydrateService
            ->expects($this->any())
            ->method('hydrate')
            ->with($data, $object)
            ->will($this->returnValue($object));

        $hydrator = $this->createHydrator($hydrateService, null);
        $result = $hydrator->hydrate($data, $object);

        $this->assertEquals($object, $result);
    }
}
