<?php
namespace Mindweb\TrackerKernelStandard\Exception;

use Exception;

class InvalidSubscriberInstanceException extends Exception
{
    public function __construct($subscriberClassName, $type)
    {
        parent::__construct(
            sprintf(
                'Invalid subscriber %s for %s.',
                $subscriberClassName,
                $type
            )
        );
    }
}