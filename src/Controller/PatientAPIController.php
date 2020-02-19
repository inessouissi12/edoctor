<?php

namespace App\Controller;

use App\Entity\Patient;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PatientAPIController extends AbstractFOSRestController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $PasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $EntityManager;
    /**
     * PatientAPIController constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $passwordEncoder )
    {
        $this->EntityManager = $manager;
        $this->PasswordEncoder = $passwordEncoder;
    }

    /**
     * @param ParamFetcher $fetcher
     * @param Patient $user
     * @return View
     * @Rest\RequestParam(name="email", description="email du patient")
     * @Rest\RequestParam(name="password", description="password du patient")
     * @Rest\RequestParam(name="password_confirmation", description="password_confirmation")
     * @Rest\RequestParam(name="cin", description="cin du patient")
     * @Rest\RequestParam(name="username", description="username du patient")
     * @Rest\RequestParam(name="datenais", description="date naissence du patient")
     * @Rest\RequestParam(name="numtel", description="numero telephone du patient")
     * @Rest\RequestParam(name="image", description="image du patient")
     * @Rest\RequestParam(name="nom", description="nom du patient")
     * @Rest\RequestParam(name="prenom", description="prenom du patient")
     * @Rest\RequestParam(name="sexe", description="sexe du patient")
     * @Rest\RequestParam(name="numCarnet", description="numero carnet du patient")
     */
    public function postPatientAction(ParamFetcher $fetcher, Patient $user)
    {
        $email                  = $fetcher->get("email");
        $password               = $fetcher->get("password");
        $passwordConfirmation   = $fetcher->get("password_confirmation");
        $cin                    = $fetcher->get("cin");
        $username               = $fetcher->get("username");
        $datenais               = $fetcher->get("datenais");
        $numtel                 = $fetcher->get("numtel");
        $image                  = $fetcher->get("image");
        $nom                    = $fetcher->get("nom");
        $sexe                   = $fetcher->get("sexe");
        $prenom                 = $fetcher->get("prenom");
        $numcarnet              = $fetcher->get("numcarnet");
        $validiteCarnet         = $fetcher->get("validiteCarnet");
        $groupSang              = $fetcher->get("groupSang");
        $profession             = $fetcher->get("profession");
        $etatCivile             = $fetcher->get("etatCivile");
        $assurence              = $fetcher->get("assurence");






        $errors = [];
        if($password != $passwordConfirmation)
        {
            $errors[] = "Password does not match the password confirmation.";
        }
        if(strlen($password) < 6)
        {
            $errors[] = "Password should be at least 6 characters.";
        }
        if(!$errors) {
            $encodedPassword = $this->PasswordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setPassword($encodedPassword);
            $user->setNumtel($numtel);
            $user->setUsername($username);
            $user->setImage($image);
            $user->setCin($cin);
            $user->setDateNais(\DateTime::createFromFormat('d-m-Y',$datenais));
            $user->setNomP($nom);
            $user->setPrenomP($prenom);
            $user->setSexe($sexe);
            $user->setNumcarnet($numcarnet);
            $user->setValiditeCarnet($validiteCarnet);
            $user->setGroupSang($groupSang);
            $user->setProfession($profession);
            $user->setEtatCivile($etatCivile);
            $user->setAssurence($assurence);

            try
            {
                $this->EntityManager->persist($user);
                $this->EntityManager->flush();
                return View::create($user , Response::HTTP_CREATED);
            }
            catch(UniqueConstraintViolationException $exception)
            {
                $errors[] = "The email provided already has an account!";
            }
            catch(\Exception $ex)
            {
                $errors[] = "Unable to save new user at this time.";
            }
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);
    }
}
