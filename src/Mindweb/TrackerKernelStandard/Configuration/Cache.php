<?php
namespace Mindweb\TrackerKernelStandard\Configuration;

use Mindweb\TrackerKernel as Adapter;
use SplFileInfo;

class Cache implements Adapter\Configuration\Cache
{
    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    /**
     * @param SplFileInfo $fileInfo
     */
    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->fileInfo->getPathname();
    }
}