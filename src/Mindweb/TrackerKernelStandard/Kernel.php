<?php
namespace Mindweb\TrackerKernelStandard;

use Mindweb\TrackerKernel as Adapter;
use Mindweb\TrackerKernelStandard\Configuration;
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
     * @var Configuration\Config
     */
    private $configurationObject;

    /**
     * @var array
     */
    private $subscribers;

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
            $this->configurationObject = new Configuration\Config();

            $resolver = new Config\Loader\LoaderResolver(array(
                new Configuration\Loader\JsonFileLoader(
                    $this->configurationObject,
                    $locator
                )
            ));

            $delegatingLoader = new Config\Loader\DelegatingLoader($resolver);
            $file = $locator->locate($config->getFile($this->env));
            $delegatingLoader->load($file);

            $resources = array(
                new Config\Resource\FileResource($file)
            );

            $configCache->write(
                serialize($this->configurationObject),
                $resources
            );
        } else {
            $cached = file_get_contents($cache->getPath());

            $this->configurationObject = unserialize($cached);
        }
    }

    /**
     * @param Adapter\Subscriber\Loader $loader
     * @param Adapter\Configuration\Cache $cache
     */
    public function registerSubscribers(Adapter\Subscriber\Loader $loader, Adapter\Configuration\Cache $cache)
    {
        $configCache = new Config\ConfigCache(
            $cache->getPath(),
            $this->debug
        );

        if (!$configCache->isFresh()) {
            $processor = new Config\Definition\Processor();
            $this->subscribers = $processor->processConfiguration(
                new Subscriber\Configuration(),
                $this->configurationObject->asArray()
            );

            $configCache->write(
                serialize($this->subscribers)
            );
        } else {
            $cached = file_get_contents($cache->getPath());

            $this->subscribers = unserialize($cached);
        }

        $loader->load(
            $this->application['dispatcher'],
            $this->subscribers,
            $this->configurationObject
        );
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