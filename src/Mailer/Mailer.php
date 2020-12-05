<?php


namespace App\Mailer;


use App\Entity\User;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Mailer
{


    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var string
     */
    private $emailFrom;

    /**
     * UserSubscriber constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     * @param string $emailFrom
     */
    public function __construct(\Swift_Mailer $mailer, Environment $twig, string $emailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailFrom = $emailFrom;
    }

    /**
     * @param User $user
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('emails/registration.html.twig', [
            'user' => $user
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Welcome to micropost app')
            ->setFrom($this->emailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}