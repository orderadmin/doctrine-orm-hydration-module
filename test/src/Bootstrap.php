<?php

namespace ApiSkeletonsTest\DoctrineORMHydrationModule;

error_reporting(E_ALL | E_STRICT);
define('PROJECT_BASE_PATH', __DIR__.'/../..');
define('TEST_BASE_PATH', __DIR__.'/..');

$autoloadFile = PROJECT_BASE_PATH.'/vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    throw new \RuntimeException('Install dependencies to run test suite.');
}

/**
 * Test bootstrap, for setting up autoloading etc.
 */
class Bootstrap
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected $autoLoader;

    /**
     * @param $autoLoader
     */
    public function __construct($autoLoader)
    {
        $this->autoLoader = $autoLoader;
    }

    /**
     * Bootstrap the tests:.
     */
    public function init()
    {
        $this->initAutoLoading();
    }

    /**
     * Add dependencies:.
     */
    protected function initAutoLoading()
    {
        $this->autoLoader->addPsr4('ApiSkeletonsTest\\DoctrineORMHydrationModule\\Tests\\', __DIR__.'/Tests/');
        $this->autoLoader->addPsr4('ApiSkeletonsTest\\DoctrineORMHydrationModule\\Fixtures\\', __DIR__.'/Fixtures/');

        $this->autoLoader->addClassMap(array(
            'Doctrine\\ODM\\MongoDB\\Tests\\BaseTest' => PROJECT_BASE_PATH.'/vendor/doctrine/mongodb-odm/tests/Doctrine/ODM/MongoDB/Tests/BaseTest.php',
        ));
    }
}

$autoLoader = require $autoloadFile;
$bootstrap = new Bootstrap($autoLoader);
$bootstrap->init();
