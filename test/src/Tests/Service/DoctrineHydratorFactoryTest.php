<?php

namespace ApiSkeletonsTest\DoctrineORMHydrationModule\Tests\Service;

use ApiSkeletonsTest\DoctrineORMHydrationModule\Hydrator\CustomBuildHydratorFactory;
use ApiSkeletons\DoctrineORMHydrationModule\Service\DoctrineHydratorFactory;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Hydrator\HydratorPluginManager;

class DoctrineHydratorFactoryTest extends TestCase
{
    /**
     * @var array
     */
    protected $serviceConfig;

    /**
     * @var HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Setup the service manager.
     */
    protected function setUp(): void
    {
        $this->serviceConfig = require TEST_BASE_PATH.'/config/module.config.php';

        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('config', $this->serviceConfig);
        $this->serviceManager->setService(
            'custom.strategy',
            $this->getMockBuilder('Laminas\Hydrator\Strategy\StrategyInterface')->getMock()
        );
        $this->serviceManager->setService(
            'custom.filter',
            $this->getMockBuilder('Laminas\Hydrator\Filter\FilterInterface')->getMock()
        );
        $this->serviceManager->setService(
            'custom.naming_strategy',
            $this->getMockBuilder('Laminas\Hydrator\NamingStrategy\NamingStrategyInterface')->getMock()
        );

        $this->hydratorManager = $this->getMockBuilder(HydratorPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->hydratorManager
            ->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($this->serviceManager));
    }

    /**
     * @param $objectManagerClass
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function stubObjectManager($objectManagerClass)
    {
        $objectManager = $this->getMockBuilder($objectManagerClass)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceManager->setService('doctrine.default.object-manager', $objectManager);

        return $objectManager;
    }

    /**
     * @return \ApiSkeletons\DoctrineORMHydrationModule\Hydrator\DoctrineHydrator
     */
    protected function createOrmHydrator()
    {
        $this->stubObjectManager('Doctrine\ORM\EntityManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        return $hydrator;
    }

    /**
     * @test
     */
    public function itShouldBeInitializable()
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf('ApiSkeletons\DoctrineORMHydrationModule\Service\DoctrineHydratorFactory', $factory);
    }

    /**
     * @test
     */
    public function itShouldBeAnAbstractFactory()
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf('Laminas\ServiceManager\AbstractFactoryInterface', $factory);
    }

    /**
     * @test
     */
    public function itShouldKnowWhichServicesItCanCreate()
    {
        // $this->stubObjectManager('Doctrine\Common\Persistence\ObjectManager');
        $factory = new DoctrineHydratorFactory();

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');
        $this->assertTrue($result);

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'invalidhydrator', 'invalid-hydrator');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function itShouldCreateACustomORMHydrator()
    {
        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('ApiSkeletons\DoctrineORMHydrationModule\Hydrator\DoctrineHydrator', $hydrator);
        $this->assertInstanceOf(\Doctrine\Laminas\Hydrator\DoctrineObject::class, $hydrator->getExtractService());
        $this->assertInstanceOf(\Doctrine\Laminas\Hydrator\DoctrineObject::class, $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function itShouldBePossibleToConfigureACustomHydrator()
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setService(
            'custom.hydrator',
            $this->getMockBuilder('Laminas\Hydrator\ArraySerializableHydrator')->getMock()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('Laminas\Hydrator\ArraySerializableHydrator', $hydrator->getHydrateService());
        $this->assertInstanceOf('Laminas\Hydrator\ArraySerializableHydrator', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function itShouldBePossibleToConfigureACustomHydratorAsFactory()
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.build.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setFactory(
            'custom.build.hydrator',
            new CustomBuildHydratorFactory()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf('Laminas\Hydrator\ArraySerializableHydrator', $hydrator->getHydrateService());
        $this->assertInstanceOf('Laminas\Hydrator\ArraySerializableHydrator', $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function itShouldBePossibleToConfigureHydrationStategies()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasStrategy('fieldname'));
        $this->assertInstanceOf('Laminas\Hydrator\Strategy\StrategyInterface', $realHydrator->getStrategy('fieldname'));
    }

    /**
     * @test
     */
    public function itShouldBePossibleToConfigureANamingStategy()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasNamingStrategy());
        $this->assertInstanceOf(
            'Laminas\Hydrator\NamingStrategy\NamingStrategyInterface',
            $realHydrator->getNamingStrategy()
        );
    }

    /**
     * @test
     */
    public function itShouldBePossibleToConfigureHydrationFilters()
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasFilter('custom.filter.name'));
    }
}
