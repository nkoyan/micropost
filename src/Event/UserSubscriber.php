<?php

namespace App\Event;

use App\Entity\UserPreferences;
use App\Utils\Mailer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(Mailer $mailer, ObjectManager $manager, string $defaultLocale)
    {
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLocale);

        $user = $event->getRegisteredUser();
        $user->setPreferences($preferences);

        $this->manager->flush();

        $this->mailer->sendConfirmationEmail($user);
    }
}