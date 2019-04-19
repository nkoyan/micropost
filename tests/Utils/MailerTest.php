<?php

namespace App\Tests\Utils;

use App\Entity\User;
use App\Utils\Mailer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class MailerTest extends TestCase
{
    public function testConfirmationEmail()
    {
        $user = new User();
        $user->setEmail('john@doe.com');

        $swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $swiftMailerMock->expects($this->once())->method('send')
            ->with($this->callback(function ($subject) {
                $messsageStr = (string)$subject;

                return strpos($messsageStr, "From: me@domain.com") !== false
                    && strpos($messsageStr, "Content-Type: text/html; charset=utf-8") !== false
                    && strpos($messsageStr, "Subject: Welcome to the micro-post app!") !== false
                    && strpos($messsageStr, "To: john@doe.com") !== false
                    && strpos($messsageStr, "This is a message body") !== false;

            }));

        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock->expects($this->once())
            ->method('render')
            ->with('email/registration.html.twig', ['user' => $user])
            ->willReturn('This is a message body');

        $mailer = new Mailer($swiftMailerMock, $twigMock, 'me@domain.com');
        $mailer->sendConfirmationEmail($user);
    }
}