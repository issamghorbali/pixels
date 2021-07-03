<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * @ORM\Entity(repositoryClass=QuizRepository::class)
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="quiz")
     */
    private $questions;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     */
    private $duree;

    /**
     * @ORM\OneToMany(targetEntity=Condidat::class, mappedBy="quiz")
     */
    private $condidats;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->condidats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

public function __toString()
{
    // TODO: Implement __toString() method.
    return $this->title;
}

public function getDuree(): ?string
{
    return $this->duree;
}

public function setDuree(string $duree): self
{
    $this->duree = $duree;

    return $this;
}

/**
 * @return Collection|Condidat[]
 */
public function getCondidats(): Collection
{
    return $this->condidats;
}

public function addCondidat(Condidat $condidat): self
{
    if (!$this->condidats->contains($condidat)) {
        $this->condidats[] = $condidat;
        $condidat->setQuiz($this);
    }

    return $this;
}

public function removeCondidat(Condidat $condidat): self
{
    if ($this->condidats->removeElement($condidat)) {
        // set the owning side to null (unless already changed)
        if ($condidat->getQuiz() === $this) {
            $condidat->setQuiz(null);
        }
    }

    return $this;
}

}
