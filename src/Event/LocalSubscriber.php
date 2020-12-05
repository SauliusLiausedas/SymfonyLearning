<?php


namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocalSubscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                [
                    'onKernelRequest',
                    20
                ]
            ]
        ];
    }

    /**
     * @param RequestEvent $responseEvent
     */
    public function onKernelRequest(RequestEvent $responseEvent)
    {
        $request = $responseEvent->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale[0]));
        }

    }

}