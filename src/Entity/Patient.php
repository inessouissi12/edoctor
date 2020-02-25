<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient extends Utilisateur
{
    /**
     * @ORM\Column(type="integer")
     */
    private $numcarnet;

    /**
     * @ORM\Column(type="date")
     */
    private $validiteCarnet;

    /**
     * @ORM\Column(type="string")
     */
    private $groupSang;

    /**
     * @ORM\Column(type="string")
     */
    private $profession;

    /**
     * @ORM\Column(type="string")
     */
    private $etatCivile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultations", mappedBy="patient")
     */
    private $consultation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rendezvous", mappedBy="patient", orphanRemoval=true)
     */
    private $rendezvouses;

    public function __construct()
    {
        parent::__construct();
        $this->consultation = new ArrayCollection();
        $this->rendezvouses = new ArrayCollection();
        $this->setRoles(["ROLE_USER"]);
    }

    public function getNumcarnet(): ?int
    {
        return $this->numcarnet;
    }

    public function setNumcarnet(int $numcarnet): self
    {
        $this->numcarnet = $numcarnet;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValiditeCarnet()
    {
        return $this->validiteCarnet;
    }

    /**
     * @param mixed $validiteCarnet
     */
    public function setValiditeCarnet($validiteCarnet): void
    {
        $this->validiteCarnet = $validiteCarnet;
    }

    /**
     * @return mixed
     */
    public function getGroupSang()
    {
        return $this->groupSang;
    }

    /**
     * @param mixed $groupSang
     */
    public function setGroupSang($groupSang): void
    {
        $this->groupSang = $groupSang;
    }

    /**
     * @return mixed
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param mixed $profession
     */
    public function setProfession($profession): void
    {
        $this->profession = $profession;
    }

    /**
     * @return mixed
     */
    public function getEtatCivile()
    {
        return $this->etatCivile;
    }

    /**
     * @param mixed $etatCivile
     */

    public function setEtatCivile($etatCivile): void
    {
        $this->etatCivile = $etatCivile;
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
            $consultation->setPatient($this);
        }

        return $this;
    }

    public function removeConsultation(Consultations $consultation): self
    {
        if ($this->consultation->contains($consultation)) {
            $this->consultation->removeElement($consultation);
            // set the owning side to null (unless already changed)
            if ($consultation->getPatient() === $this) {
                $consultation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Rendezvous[]
     */
    public function getRendezvouses(): Collection
    {
        return $this->rendezvouses;
    }

    public function addRendezvouse(Rendezvous $rendezvouse): self
    {
        if (!$this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses[] = $rendezvouse;
            $rendezvouse->setPatient($this);
        }

        return $this;
    }

    public function removeRendezvouse(Rendezvous $rendezvouse): self
    {
        if ($this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses->removeElement($rendezvouse);
            // set the owning side to null (unless already changed)
            if ($rendezvouse->getPatient() === $this) {
                $rendezvouse->setPatient(null);
            }
        }

        return $this;
    }
}
