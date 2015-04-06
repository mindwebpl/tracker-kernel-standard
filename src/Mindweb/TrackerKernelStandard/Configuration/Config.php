<?php
namespace Mindweb\TrackerKernelStandard\Configuration;

use Mindweb\TrackerKernel as Adapter;

class Config implements Adapter\Configuration\Config
{
    /**
     * @var array
     */
    private $entries;

    public function __construct()
    {
        $this->entries = array();
    }
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->entries[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->entries[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->entries[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if (isset($this->entries[$offset])) {
            unset ($this->entries[$offset]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->entries);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->entries = unserialize($serialized);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->entries;
    }
}