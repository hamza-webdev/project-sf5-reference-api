<?php
namespace App\Services;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


class SendEmail
{
    private MailerInterface $mailer;
    private string $senderEmail;
    private string $senderName;

    public function __construct(MailerInterface $mailer, string $senderEmail, string $senderName)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;        
    }

    /**
     * [send description]
     *
     * @param   array<mixed>  $arguments  [$arguments description]
     *
     * @return  void               [return description]
     */
    public function send(array $arguments): void
    {
        [
            'recepient_email'   => $recepientEmail, 
            'subject'           => $subject, 
            'html_template'     => $htmlTemplate,
            'contexts'          => $context
        ] = $arguments;

        $email = new TemplatedEmail();

        $email->from(new Address($this->senderEmail, $this->senderName))
              ->to($recepientEmail)
              ->subject($subject)
              ->htmlTemplate($htmlTemplate)
              ->context($context);

        try {
            // envoi email
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $th) {
            throw $th;
        }

    }

}

