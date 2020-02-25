<?php


namespace App\Security;


use App\Entity\Utilisateur;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

class ConfirmerCompte
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * ConfirmerCompte constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function MailConfirmation(Utilisateur $user){
        $message = (new Email())
            ->from("service@eDoctor.com")
            ->to($user->getEmail())
            ->subject('Confirmation')
            ->text('http://127.0.0.1:8000/verify/'.$user->getToken());
        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
        }
    }

}
