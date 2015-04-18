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

    public function __construct()
    {
        $this->processor = new Config\Definition\Processor();
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
        $schema = $subscriber->getConfiguration();
        if ($schema !== null) {
            return $this->processor->processConfiguration(
                $schema,
                $configuration->asArray()
            );
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