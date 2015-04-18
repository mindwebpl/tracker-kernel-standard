<?php
namespace Mindweb\TrackerKernelStandard\Subscriber;

use Mindweb\Subscriber\Subscriber;
use Mindweb\TrackerKernelStandard\Exception;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mindweb\TrackerKernel as Adapter;
use Symfony\Component\Config;

class Loader implements Adapter\Subscriber\Loader
{
    /**
     * @var Config\Definition\Processor
     */
    private $processor;

    /**
     * @var Adapter\Configuration\Cache
     */
    private $cache;
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param Adapter\Configuration\Cache $cache
     * @param bool $debug
     */
    public function __construct(Adapter\Configuration\Cache $cache, $debug = false)
    {
        $this->processor = new Config\Definition\Processor();
        $this->cache = $cache;
        $this->debug = $debug;
    }
    /**
     * @param EventDispatcherInterface $dispatcher
     * @param array $subscribers
     * @param Adapter\Configuration\Config $configuration
     * @throws Exception\InvalidSubscriberInstanceException
     * @throws Exception\SubscriberDoesNotExists
     */
    public function load(EventDispatcherInterface $dispatcher, array $subscribers,
                         Adapter\Configuration\Config $configuration)
    {
        foreach ($subscribers as $type => $classNames) {
            foreach ($classNames as $className) {
                if (!class_exists($className)) {
                    throw new Exception\SubscriberDoesNotExists($className, $type);
                }

                $subscriber = new $className();
                if (!$subscriber instanceof Subscriber) {
                    throw new Exception\InvalidSubscriberInstanceException($className, $type);
                }

                $subscriber->initialize(
                    $this->getConfigurationForSubscriber($subscriber, $configuration)
                );

                $this->registerSubscribers($subscriber, $dispatcher);
            }
        }
    }

    /**
     * @param Subscriber $subscriber
     * @param Adapter\Configuration\Config $configuration
     * @return array
     */
    private function getConfigurationForSubscriber(Subscriber $subscriber, Adapter\Configuration\Config $configuration)
    {
        $configCache = new Config\ConfigCache(
            $this->cache->getPath(),
            $this->debug
        );

        $schema = $subscriber->getConfiguration();
        if ($schema !== null) {
            if (!$configCache->isFresh()) {
                $configurationForSubscriber =  $this->processor->processConfiguration(
                    $schema,
                    $configuration->asArray()
                );

                $configCache->write(
                    serialize($configurationForSubscriber)
                );
            } else {
                $configurationForSubscriber = unserialize(
                    file_get_contents($this->cache->getPath())
                );
            }

            return $configurationForSubscriber;
        }

        return array();
    }

    /**
     * @param Subscriber $subscriber
     * @param EventDispatcherInterface $dispatcher
     */
    private function registerSubscribers(Subscriber $subscriber, EventDispatcherInterface $dispatcher)
    {
        $calls = $subscriber->register();
        foreach ($calls as $call) {
            list ($methodName, $priority) = $call;

            $dispatcher->addListener(
                $subscriber->getEventName(),
                array($subscriber, $methodName),
                $priority
            );
        }
    }
}