<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Medecin;
use App\Repository\AdresseRepository;
use App\Repository\MedecinRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
class MedecinAPIController extends AbstractFOSRestController
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
     * @var AdresseRepository
     */
    private $AdressRepository;
    /**
     * @var MedecinRepository
     */
    private $UtilisateurRepository;

    /**
     * MedecinAPIController constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdresseRepository $adresseRepository
     * @param UtilisateurRepository $utilisateurRepository
     */
    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $passwordEncoder ,
                                AdresseRepository $adresseRepository ,UtilisateurRepository $utilisateurRepository)
    {
        $this->AdressRepository = $adresseRepository;
        $this->EntityManager = $manager;
        $this->PasswordEncoder = $passwordEncoder;
        $this->UtilisateurRepository = $utilisateurRepository;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function postDoctorAction(Request $request)
    {
        $user = new Medecin();
        $nom = $request->request->get("nom");
        $prenom = $request->request->get("prenom");
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $passwordConfirmation = $request->request->get("password_confirmation");
        $cin = $request->request->get("cin");
        $username = $request->request->get("username");
        $datenais = $request->request->get("datenais");
        $numtel = $request->request->get("numtel");
        $image = $request->request->get("image");
        $sexe = $request->request->get("sexe");
        $adresse = ($request->request->get("adresse"))["adr"];
        $codePostal = ($request->request->get("adresse"))["codePostal"];
        $ville = ($request->request->get("adresse"))["ville"];
        $pays = ($request->request->get("adresse"))["pays"];
        $numserieM = $request->request->get("numserieM");

        $errors = [];
        if ($password != $passwordConfirmation) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $this->PasswordEncoder->encodePassword($user, $password);
            $user->setnom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setPassword($encodedPassword);
            $user->setNumtel($numtel);
            $user->setUsername($username);
            $user->setImage($image);
            $user->setCin($cin);
            $user->setDateNais(\DateTime::createFromFormat('d-m-Y', $datenais));
            $user->setNumserieM($numserieM);
            $user->setSexe($sexe);

                    $adr = $this->AdressRepository->findOneBy(['codePostal' => $codePostal]);

                if ($adr == null) {
                    $adr = new Adresse();
                    $errors[] = "code postal not found";
                    $adr->setPays($pays);
                    $adr->setVille($ville);
                    $adr->setCodePostal($codePostal);
                    $adr->setAdresse($adresse);
                    $this->EntityManager->persist($adr);
                    $this->EntityManager->flush();
                }

                    $user->setAdresse($adr);
                    $this->EntityManager->persist($user);
                    $this->EntityManager->flush();
                    return View::create($user, Response::HTTP_CREATED);
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param $cin
     * @return View
     */
    public function getDoctorAction($cin){
        $user = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);
        //$adr = $this->AdressRepository->find($users->getAdresse()->getId())
        return View::create($user,Response::HTTP_OK);
    }
}
