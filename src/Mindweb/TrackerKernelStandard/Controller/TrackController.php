<?php
namespace Mindweb\TrackerKernelStandard\Controller;

use Mindweb\Recognizer\Recognizer;
use Mindweb\Recognizer\Event\AttributionEvent;
use Mindweb\Persist\Persist;
use Mindweb\Persist\Event\PersistEvent;
use Mindweb\Resolve\Resolve;
use Mindweb\Resolve\Event\ResolveEvent;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $attributionEvent = new AttributionEvent($request);
        $this->dispatcher->dispatch(Recognizer::RECOGNIZE_EVENT, $attributionEvent);

        $persistEvent = new PersistEvent($attributionEvent);
        $this->dispatcher->dispatch(Persist::PERSIST_EVENT, $persistEvent);

        $response = new Response();
        $resolveEvent = new ResolveEvent($persistEvent, $response);
        $this->dispatcher->dispatch(Resolve::RESOLVE_EVENT, $resolveEvent);

        return $response;
    }
} 