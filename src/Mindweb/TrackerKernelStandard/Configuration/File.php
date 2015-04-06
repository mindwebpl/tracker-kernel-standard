<?php
namespace Mindweb\TrackerKernelStandard\Configuration;

use Mindweb\TrackerKernel as Adapter;
use SplFileInfo;

class File implements Adapter\Configuration\File
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
        return $this->fileInfo->getPath();
    }

    /**
     * @param string $env
     * @return string
     */
    public function getFile($env)
    {
        return sprintf($this->fileInfo->getFilename(), $env);
    }
}