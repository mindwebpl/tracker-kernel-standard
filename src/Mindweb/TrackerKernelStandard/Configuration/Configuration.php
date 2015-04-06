<?php
namespace Mindweb\TrackerKernelStandard\Configuration;

use Mindweb\TrackerKernel as Adapter;

class Configuration implements Adapter\Configuration\Config
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
        return var_export($this->entries, true);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        eval('$this->entries = ' . $serialized);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->entries;
    }
}