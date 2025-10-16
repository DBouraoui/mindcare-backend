<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['roles'] = $user->getRoles();
        $payload['username'] = $user->getUserIdentifier();
        $payload['firstname'] = $user->getFirstname();
        $payload['lastname'] = $user->getLastname();
        $payload['city'] = $user->getCity();
        $payload['phone'] = $user->getPhone();

        $event->setData($payload);
    }
}
