<?php

namespace ApiSkeletonsTest\DoctrineORMHydrationModule\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\ArraySerializableHydrator;

final class CustomBuildHydratorFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        return new ArraySerializableHydrator();
    }
}
