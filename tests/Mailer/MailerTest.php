<?php


namespace App\Tests\Mailer;


use App\Entity\User;
use App\Mailer\Mailer;
use Twig\Environment;

class MailerTest extends \PHPUnit\Framework\TestCase
{
    public function testConfirmationEmail()
    {
        $user = new User();
        $user->setEmail = 'john_doe@google.com';

        $swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->getMock();
        $swiftMailer->expects($this->once())->method('send')->with(
            $this->callback(function($subject) {
                $messageStr = (string) $subject;
                return strpos($messageStr, 'From: info@info.lt') !== false
                    && strpos($messageStr, 'Content-Type: text/html; charset=utf-8') !== false;
            })
        );

        $twig = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $twig->expects($this->once())->method('render')->with('emails/registration.html.twig', ['user' => $user]);

        $mailer = new Mailer($swiftMailer, $twig, 'info@info.lt');

        $mailer->sendConfirmationEmail($user);

    }
}