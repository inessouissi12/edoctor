<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Repository\AdresseRepository;
use App\Repository\MedecinRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
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
     * @var EncoderInterface
     */
    private $Encoder;


    /**
     * MedecinController constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param AdresseRepository $adresseRepository
     * @param UtilisateurRepository $utilisateurRepository
     * @param SerializerInterface $serializer
     * @param EncoderInterface $decode
     */
    public function __construct(EntityManagerInterface $manager , UserPasswordEncoderInterface $passwordEncoder ,
                                AdresseRepository $adresseRepository ,UtilisateurRepository $utilisateurRepository
                                , SerializerInterface $serializer,EncoderInterface  $decode)
    {
        $this->AdressRepository = $adresseRepository;
        $this->EntityManager = $manager;
        $this->PasswordEncoder = $passwordEncoder;
        $this->UtilisateurRepository = $utilisateurRepository;
        $this->Serializer = $serializer;
        $this->Encoder = $decode;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function postRegisterAction(Request $request)
    {
        $data = $this->Encoder->Encode($request->getContent(),"json");
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
                            $errors[] = "code postal not found";
                            $adr = $data->getAdresse();
                            $this->EntityManager->persist($adr);
                            $this->EntityManager->flush();
                    }
                    $data->setAdresse($adr);
                    $this->EntityManager->persist($data);
                    $this->EntityManager->flush();

                    return $this->view($data,200);
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route(path="/api/login_check",name="api_login",methods={"POST"})
     */
    public function login(Request $request){
        $user = $this->getUser();
        return new Response([
            'email' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @param Request $request
     * @param $cin
     * @return View
     */
    public function putDoctorAction(Request $request, $cin){
        try {
            $old = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);

            $data = $this->Encoder->Encode($request->getContent(),"json");
            $data = $this->Serializer->deserialize($request->getContent(),Medecin::class,"json");
            $encodedPassword = $this->PasswordEncoder->encodePassword($old, $data->getPassword());
            $errors = [];
            if ($data->getPassword() != $data->getConfirmerPassword()) {
                $errors[] = "Password does not match the password confirmation.";
            }
            if (!$errors) {
                $adr = $this->AdressRepository->findOneBy(['codePostal' => $data->getAdresse()->getCodePostal()]);
                if ($adr == null) {
                    $adr = $data->getAdresse()->getCodePostal();
                    $this->EntityManager->persist($adr);
                    $this->EntityManager->flush();
                }
                $data->setAdresse($adr);
                $this->EntityManager->persist($old);
                $this->EntityManager->flush();
                return View::create($old, Response::HTTP_OK);
            }
        }catch (CircularReferenceException $e){
            $errors[] = "findAll error";
            return View::create($errors,Response::HTTP_BAD_REQUEST);
        }
    }
    public function deleteDoctorAction($cin){
        $user = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);
        $this->EntityManager->remove($user);
        $this->EntityManager->flush();
        return View::create(null ,Response::HTTP_NO_CONTENT);

    }
}
