<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReponseRepository::class)
 */
class Reponse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reponse;

    /**
     * @ORM\ManyToOne(targetEntity=Condidat::class, inversedBy="reponses")
     */
    private $condidate;

    /**
     * @ORM\ManyToOne(targetEntity=OptionQuestion::class, inversedBy="reponses")
     */
    private $optionQuestion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?bool
    {
        return $this->reponse;
    }

    public function setReponse(?bool $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getCondidate(): ?Condidat
    {
        return $this->condidate;
    }

    public function setCondidate(?Condidat $condidate): self
    {
        $this->condidate = $condidate;

        return $this;
    }

    public function getOptionQuestion(): ?OptionQuestion
    {
        return $this->optionQuestion;
    }

    public function setOptionQuestion(?OptionQuestion $optionQuestion): self
    {
        $this->optionQuestion = $optionQuestion;

        return $this;
    }
}
