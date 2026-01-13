<?php

namespace App\Infrastructure\Security;

use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

final readonly class LoginFailureSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $email = $event->getPassport()->getUser()?->getUserIdentifier();

        if (!$email) return;

        $user = $this->userRepository->findByEmail($email);

        if (!$user) return;

        $user->recordFailedLoginAttempts();
        $this->userRepository->save($user);
    }
}
