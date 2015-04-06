<?php
namespace Mindweb\TrackerKernelStandard;

use Mindweb\TrackerKernel as Adapter;
use Mindweb\TrackerKernelStandard\Configuration\Loader\JsonFileLoader;
use Mindweb\TrackerKernelStandard\Configuration\Configuration;
use Silex;
use Symfony\Component\Config;

class Kernel implements Adapter\Kernel
{
    /**
     * @var string
     */
    private $env;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var Silex\Application
     */
    private $application;

    /**
     * @var Configuration
     */
    private $configurationObject;

    /**
     * @param string $env
     * @param bool $debug
     */
    public function __construct($env, $debug = false)
    {
        $this->env = $env;
        $this->debug = $debug;
        $this->application = new Silex\Application();
    }

    /**
     * @param Adapter\Configuration\File $config
     * @param Adapter\Configuration\Cache $cache
     */
    public function loadConfiguration(Adapter\Configuration\File $config, Adapter\Configuration\Cache $cache)
    {
        $configCache = new Config\ConfigCache(
            $cache->getPath(),
            $this->debug
        );

        if (!$configCache->isFresh()) {
            $locator = new Config\FileLocator($config->getPath());
            $this->configurationObject = new Configuration();

            $resolver = new Config\Loader\LoaderResolver(array(
                new JsonFileLoader(
                    $this->configurationObject,
                    $locator
                )
            ));

            $delegatingLoader = new Config\Loader\DelegatingLoader($resolver);
            $delegatingLoader->load($config->getFile($this->env));

            $resources = array(
                new Config\Resource\FileResource($config->getFile($this->env))
            );

            $configCache->write(
                serialize($this->configurationObject),
                $resources
            );
        } else {
            $cached = require $cache->getPath();

            $this->configurationObject = unserialize($cached);
        }
    }

    /**
     * @param Adapter\Subscriber\Loader $loader
     */
    public function registerSubscribers(Adapter\Subscriber\Loader $loader)
    {
        $processor = new Config\Definition\Processor();
        $subscribers = $processor->processConfiguration(
            new Subscriber\Configuration(),
            $this->configurationObject->asArray()
        );

        var_dump($subscribers);exit;
    }

    public function registerEndPoint()
    {
        // TODO: Implement registerEndPoint() method.
    }

    public function run()
    {
        // TODO: Implement run() method.
    }
}