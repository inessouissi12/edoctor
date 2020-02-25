<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Patient;
use App\Repository\AdresseRepository;
use App\Repository\PatientRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PatientController extends AbstractFOSRestController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $PasswprdEncoder;
    /**
     * @var EncoderInterface
     */
    private $EncoderInterface;

    /**
     * @var SerializerInterface
     */
    private $Serializer;
    /**
     * @var UtilisateurRepository
     */
    private $UtilisateurRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $PasswordEncoder;
    /**
     * @var AdresseRepository
     */
    private $AdresseRepository;
    /**
     * @var EntityManagerInterface
     */
    private $EntityManager;
    private $PatientRepository;

    /**
     * PatientController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EncoderInterface $encoder
     * @param SerializerInterface $serializer
     * @param PatientRepository $patientRepository
     * @param AdresseRepository $adresseRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder ,EncoderInterface $encoder, SerializerInterface $serializer ,
PatientRepository $patientRepository , AdresseRepository $adresseRepository , EntityManagerInterface $entityManager)
    {
        $this->PasswordEncoder = $passwordEncoder;
        $this->EncoderInterface = $encoder;
        $this->Serializer = $serializer;
        $this->PatientRepository = $patientRepository;
        $this->AdresseRepository = $adresseRepository;
        $this->EntityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @return View
     * @Route("patient/register", name="api_patient" , methods={"POST"})
     */
    public function registerPatient(Request $request)
    {
        $data=$this->Serializer->deserialize($request ->getContent(),Patient::class, 'json');
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
            $adr = $this->AdresseRepository->findOneBy(['codePostal' => $data->getAdresse()->getCodePostal()]);
            if($adr == null) {
                $errors[] = "code postal not found";
                $adr = $data->getAdresse();
                $this->EntityManager->persist($adr);
                $this->EntityManager->flush();
            }
            $data->setAdresse($adr);
            $this->EntityManager->persist($data);
            $this->EntityManager->flush();

            return View::create($data, Response::HTTP_OK);
        }
        return View::create($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route(path="/patient/login",name="api_login",methods={"POST"})
     * @return void
     */
    public function login()
    {
        
    }

    /**
     * @param $cin
     * @param Request $request
     * @return View
     * @IsGranted("ROLE_USER")
     * @Route(path="patient/modify/{cin}",name="api_patient_modify", methods={"PUT"})
     */
    public function ModifyAccountPatient($cin,Request $request){
        try {
            $old = $this->PatientRepository->findOneBy(["cin"=> $cin]);
            if ($request ->request->get("nom" )!=null){
                $old->setNom($request->request->get("nom"));
            }
            if ($request ->request->get("prenom" )!=null){
                $old->setPrenom($request ->request->get("prenom" ));
            }
            if ($request ->request->get("numtel" )!=null){
                $old->setNumtel($request ->request->get("numtel" ));
            }
            if ($request ->request->get("image" )!=null){
                $old->setImage($request ->request->get("image" ));
            }
            if ($request ->request->get("validiteCarnet" )!=null){
                $old->setValiditeCarnet(\DateTime::createFromFormat("d-m-Y",$request ->request->get("validiteCarnet" )));
            }
            if ($request ->request->get("profession" )!=null){
                $old->setProfession($request ->request->get("profession" ));
            }
            if ($request ->request->get("etatCivile" )!=null){
                $old->setEtatCivile($request ->request->get("etatCivile" ));
            }
            $adr=new Adresse();
            if(($request->request->get("adresse"))["codePostal"] !=null){
                $adr=$this->AdresseRepository->findOneBy(["codePostal"=>($request->request->get("adresse"))["codePostal"]]);
                if($adr ==null){
                    $adr->setCodePostal($request ->request->get("codePostal" ));
                    $adr->setAdresse($request ->request->get("adresse" ));
                    $adr->setVille($request ->request->get("ville" ));
                    $adr->setCodePays($request ->request->get("pays" ));


                }
                $old->setAdresse($adr);
                $this->EntityManager->persist($old);
                $this->EntityManager->flush();
                return View::create(["modify"=>true], Response::HTTP_OK);


            }




        }    catch (CircularReferenceException $e){
$errors[] = "findAll error";
return View::create($errors,Response::HTTP_BAD_REQUEST);
}


    }

    /**
     * @param $cin
     * @return View
     * @IsGranted("ROLE_USER")
     * @Route("/patient/delete/{cin}" , name="api_delete", methods={"DELETE"})
     */
    public function delete($cin){
        $user = $this->UtilisateurRepository->findOneBy(["cin"=>$cin]);
        $this->EntityManager->remove($user);
        $this->EntityManager->flush();
        return View::create(null ,Response::HTTP_NO_CONTENT);

    }



}
