<?php


namespace App\Security;


use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEnableChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Utilisateur){
            return;
        }
        if (!$user->getEnabledAccount()){
            throw new DisabledException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
    }


}
