<?php

namespace App\Controller\UserAuthentification;

use App\Entity\Adresse;
use App\Entity\Medecin;
use App\Entity\Secretaire;
use App\Repository\AdresseRepository;
use App\Repository\MedecinRepository;
use App\Repository\UtilisateurRepository;
use App\Security\ConfirmerCompte;
use App\Security\InscruptionNotification;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;

class MedecinController extends AbstractFOSRestController
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
     * @var SerializerInterface
     */
    private $Serializer;
    /**
     * @var ConfirmerCompte
     */
    private $confirmerCompte;

    /**
     * MedecinController constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdresseRepository $adresseRepository
     * @param UtilisateurRepository $utilisateurRepository
     * @param SerializerInterface $serializer
     * @param ConfirmerCompte $confirmerCompte
     */
    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $passwordEncoder ,
                                AdresseRepository $adresseRepository ,UtilisateurRepository $utilisateurRepository
                                , SerializerInterface $serializer,ConfirmerCompte $confirmerCompte)
    {
        $this->AdressRepository = $adresseRepository;
        $this->EntityManager = $manager;
        $this->PasswordEncoder = $passwordEncoder;
        $this->UtilisateurRepository = $utilisateurRepository;
        $this->Serializer = $serializer;
        $this->confirmerCompte = $confirmerCompte;
    }

    /**
     * @param Request $request
     * @return View
     * @Route("/register" , name="api_register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $data = $this->Serializer->deserialize($request->getContent(),Medecin::class,"json");
        $errors = [];
        if ($data->getPassword() != $data->getConfirmerPassword()) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($data->getPassword()) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $this->PasswordEncoder->encodePassword($data, $data->getPassword());
            $data->setPassword($encodedPassword);
                    $adr = $this->AdressRepository->findOneBy(['codePostal' => $data->getAdresse()->getCodePostal()]);
                    if($adr == null) {
                            $adr = $data->getAdresse();
                            $this->EntityManager->persist($adr);
                            $this->EntityManager->flush();
                    }
                    $data->setRoles(['ROLE_SUPER_ADMIN']);
                    $data->setAdresse($adr);
            $this->EntityManager->persist($data);
            $this->EntityManager->flush();
            $this->confirmerCompte->MailConfirmation($data);
            return View::create($data,Response::HTTP_OK);
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param $token
     * @Route("verify/{token}",methods={GET})
     * @return View
     */
    public function VerifyAccount($token){
        $tk = $this->UtilisateurRepository->findOneBy(["token"=>$token]);
        if ($token == $tk->getToken()){
            $tk->setEnabledAccount(true);
            $this->EntityManager->persist($tk);
            $this->EntityManager->flush();
        }
        return View::create(["verify Account" =>$tk->getEnabledAccount()],Response::HTTP_OK);
    }

    /**
     * @Route(path="/login",name="api_login",methods={"POST"})
     * @return void
     */
    public function login(){
    }

    /**
     * @param Request $request
     * @param $cin
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @return View
     * @Route("/modify/{cin}" , name="api_modify", methods={"PUT"})
     */
    public function ModifyAccountDoctor(Request $request, $cin){
        try {
            $old = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);
                if ($request->request->get("nom")!= null){
                    $old->setNom($request->request->get("nom"));
                }
                if ($request->request->get("prenom")!=null){
                    $old->setPrenom($request->request->get("prenom"));
                }
                if ($request->request->get("Numtl") !=null){
                    $old->setNumtel($request->request->get("Numtl"));
                }
                if ($request->request->get("image")!=null){
                    $old->setImage($request->request->get("image"));
                }
                $adr = new Adresse();
                if (($request->request->get("adresse"))["codePostal"] != null){
                    $adr = $this->AdressRepository->findOneBy(["codePostal"=>($request->request->get("adresse"))["codePostal"]]);
                    if ($adr == null) {
                        $adr->setCodePostal(($request->request->get("adresse"))["codePostal"]);
                        $adr->setVille(($request->request->get("adresse"))["ville"]);
                        $adr->setPays(($request->request->get("adresse"))["pays"]);
                        $adr->setAdresse(($request->request->get("adresse"))["adresse"]);
                    }
                    $old->setAdresse($adr);
                }
                $this->EntityManager->persist($old);
                $this->EntityManager->flush();
                return View::create(["modify"=>true], Response::HTTP_OK);

        }catch (CircularReferenceException $e){
            $errors[] = "findAll error";
            return View::create($errors,Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $cin
     * @return View
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/api/delete/{cin}" , name="api_delete", methods={"DELETE"})
     */
    public function delete($cin){
        $user = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);
        $this->EntityManager->remove($user);
        $this->EntityManager->flush();
        return View::create(null ,Response::HTTP_NO_CONTENT);

    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/api/register_secretaire" , name="api_register_secretaire", methods={"POST"})
     * @param Request $request
     * @return View
     */

    public function registerSecretaire(Request $request){
        $data = $this->Serializer->deserialize($request->getContent(),Secretaire::class,"json");
        $errors = [];
        if ($data->getPassword() != $data->getConfirmerPassword()) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($data->getPassword()) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $this->PasswordEncoder->encodePassword($data, $data->getPassword());
            $data->setPassword($encodedPassword);
            $adr = $this->AdressRepository->findOneBy(['codePostal' => $data->getAdresse()->getCodePostal()]);
            if($adr == null) {
                $errors[] = "code postal not found";
                $adr = $data->getAdresse();
                $this->EntityManager->persist($adr);
                $this->EntityManager->flush();
            }
            $data->setRoles(['ROLE_ADMIN']);
            $data->setAdresse($adr);
            $this->EntityManager->persist($data);
            $this->EntityManager->flush();
            $data->setConfirmerPassword(null);
            return $this->view($data,200);
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);

    }

    /**
     * @Route("/api/logout" , name="api_logout", methods={"GET"})
     * @throws Exception
     */
    public function logoout(){
        throw new \Exception('Will be intercepted before getting here');
    }
}
