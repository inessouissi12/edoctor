<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MedecinRepository")
 */
class Medecin extends Utilisateur implements \Serializable
{
    /**
     * @ORM\Column( name = "numserieM" ,type="integer", unique=true)
     */
    private $numserieM;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultations", mappedBy="medecin", orphanRemoval=true)
     */
    private $consultation;

    public function __construct()
    {
        parent::__construct();
        $this->consultation = new ArrayCollection();
    }

    public function getNumserieM(): ?string
    {
        return $this->numserieM;
    }

    public function setNumserieM(string $numserieM): self
    {
        $this->numserieM = $numserieM;

        return $this;
    }

    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    public function serialize()
    {
        return   array(
            $this->nom,
            $this->prenom,
            $this->username,
            $this->password,
            $this->email,
            $this->cin,
            $this->sexe,
            $this->image,
            $this->dateNais,
            $this->numserieM,
            $this->consultation,
            $this->numtel);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list($this->nom,
            $this->prenom,
            $this->username,
            $this->password,
            $this->email,
            $this->cin,
            $this->sexe,
            $this->image,
            $this->dateNais,
            $this->numserieM,
            $this->consultation,
            $this->numtel)= unserialize($serialized);
    }


    /**
     * @return Collection|Consultations[]
     */
    public function getConsultation(): Collection
    {
        return $this->consultation;
    }

    public function addConsultation(Consultations $consultation): self
    {
        if (!$this->consultation->contains($consultation)) {
            $this->consultation[] = $consultation;
            $consultation->setMedecin($this);
        }

        return $this;
    }

    public function removeConsultation(Consultations $consultation): self
    {
        if ($this->consultation->contains($consultation)) {
            $this->consultation->removeElement($consultation);
            // set the owning side to null (unless already changed)
            if ($consultation->getMedecin() === $this) {
                $consultation->setMedecin(null);
            }
        }

        return $this;
    }
}
