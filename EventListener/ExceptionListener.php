<?php

namespace Fluedis\RedmineBundle\EventListener;

use Fluedis\RedmineBundle\Service\RedmineApiClient;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    /** @var RedmineApiClient */
    private $apiClient;

    public function __construct(RedmineApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->apiClient->isEnabled()) {
            $exception = $event->getException();

            $this->apiClient->createIssue($exception);
        }
    }
}
