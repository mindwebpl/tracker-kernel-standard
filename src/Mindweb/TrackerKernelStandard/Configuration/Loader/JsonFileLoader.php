<?php
namespace Mindweb\TrackerKernelStandard\Configuration\Loader;

use Mindweb\TrackerKernel\Configuration\Config as ConfigInterface;
use Symfony\Component\Config;

class JsonFileLoader extends Config\Loader\FileLoader
{
    /**
     * @var ConfigInterface
     */
    private $configuration;

    public function __construct(ConfigInterface $config, Config\FileLocatorInterface $locator)
    {
        $this->configuration = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $configuration = json_decode(
            file_get_contents(
                $this->getLocator()->locate($resource)
            ),
            true
        );

        foreach ($configuration as $key => $value) {
            $this->configuration[$key] = $value;
        }
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'json' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}