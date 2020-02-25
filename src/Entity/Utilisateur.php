<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", unique=true)
     */

    protected $id;
    /**
     * @ORM\Column(name ="nom" ,type="string")
     * @Groups("api")
     */
    protected $nom;

    /**
     * @ORM\Column(name ="prenom" ,type="string")
     */
    protected $prenom;

    /**
     * @ORM\Column(name ="cin" ,type="integer", unique=true)
     */
    protected $cin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $sexe;

    /**
     * @ORM\Column(name ="username", type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="date")
     */
    protected $dateNais;

    /**
     * @ORM\Column(name ="numtel" ,type="integer", unique=true)
     */
    protected $numtel;

    /**
     * @ORM\Column(name ="email" ,type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    protected $confirmer_password;

    /**

     * @ORM\Column(type="json_array")

     */

    private $roles = array();

    /**
     * @return mixed
     */
    public function getConfirmerPassword()
    {
        return $this->confirmer_password;
    }

    /**
     * @param mixed $confirmer_password
     */
    public function setConfirmerPassword($confirmer_password): void
    {
        $this->confirmer_password = $confirmer_password;
    }



    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Adresse", inversedBy="utilisateurs", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $adresse;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }


    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDateNais(): ?\DateTimeInterface
    {
        return $this->dateNais;
    }

    public function setDateNais(\DateTimeInterface $dateNais): self
    {
        $this->dateNais = $dateNais;

        return $this;
    }

    public function getNumtel(): ?int
    {
        return $this->numtel;
    }

    public function setNumtel(int $numtel): self
    {
        $this->numtel = $numtel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return Utilisateur
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }



    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }



}
