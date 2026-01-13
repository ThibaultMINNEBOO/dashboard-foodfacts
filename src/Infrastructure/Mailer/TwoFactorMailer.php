<?php

namespace App\Infrastructure\Mailer;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;

class TwoFactorMailer implements AuthCodeMailerInterface
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%scheb_two_factor.email.sender_email%')]
        private string $from,
    ) {
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $email = $user->getEmailAuthRecipient();
        $authCode = $user->getEmailAuthCode();

        $email = new TemplatedEmail()
            ->from($this->from)
            ->to($email)
            ->subject('Your authentication code')
            ->htmlTemplate('emails/2fa_email.html.twig')
            ->context([
                'authCode' => $authCode,
            ]);

        $this->mailer->send($email);
    }
}
