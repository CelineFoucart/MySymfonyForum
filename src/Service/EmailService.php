<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;

/**
 * Class EmailService
 * 
 * EmailService handles email sending for contact purpose.
 * 
 * @method bool notify($contact)
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class EmailService
{
    private string $adminEmail;
    protected MailerInterface $mailer;

    public function __construct(string $adminEmail, MailerInterface $mailer)
    {
        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
    }

    /**
     * Sends an email to the administrator from a user.
     */
    public function notify(Contact $contact): bool
    {
        try {
            $this->send($contact);
            return true;
        } catch (TransportExceptionInterface $th) {
            return false;
        }
    }

    /**
     * Creates and sends an email.
     */
    private function send(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($contact->getEmail(), $contact->getUsername()))
            ->to(new Address($this->adminEmail))
            ->subject($contact->getSubject())
            ->htmlTemplate('email/contact.html.twig')
            ->context(['contact' => $contact]);
        $this->mailer->send($email);
    }
}