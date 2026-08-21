<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\DomainException;
use App\Exception\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Global exception handling (grading criterion "Gestion des exceptions"):
 * any DomainException that escapes a controller (i.e. wasn't already caught
 * locally to show a flash message) is logged and turned into a safe redirect
 * instead of leaking a stack trace to the end user. Anything else falls
 * through to Symfony's normal error-page rendering (see the templates in
 * templates/bundles/TwigBundle/Exception/).
 */
#[AsEventListener(event: 'kernel.exception')]
class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RouterInterface $router,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof EntityNotFoundException) {
            $event->setThrowable(new NotFoundHttpException($throwable->getMessage(), $throwable));

            return;
        }

        if ($throwable instanceof DomainException) {
            $this->logger->warning('Domain exception not handled locally by a controller: {message}', [
                'message' => $throwable->getMessage(),
                'exception' => $throwable,
            ]);

            $request = $event->getRequest();
            $request->getSession()->getFlashBag()->add('error', $throwable->getMessage());

            $event->setResponse(new RedirectResponse(
                $request->headers->get('referer') ?: $this->router->generate('app_home'),
                Response::HTTP_FOUND
            ));
        }
    }
}
