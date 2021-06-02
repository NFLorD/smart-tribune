<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\UpdatedAt;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @ORM\EntityListeners({"App\EventListener\OnQuestionUpdate"})
 */
class Question
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUSES = [Question::STATUS_DRAFT, Question::STATUS_PUBLISHED];

    use CreatedAt;
    use UpdatedAt;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * 
     * @Assert\Length(
     *      min = 5,
     *      max = 100
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     * 
     * @Assert\NotNull
     */
    private $promoted;

    /**
     * @ORM\Column(type="string", length=32)
     * 
     * @Assert\NotNull
     * @Assert\Choice(choices=Question::STATUSES)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="question", cascade={"persist"})
     * 
     * @Assert\Valid
     */
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity=QuestionHistory::class, mappedBy="question")
     */
    private $history;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->history = new ArrayCollection();
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

    public function getPromoted(): ?bool
    {
        return $this->promoted;
    }

    public function setPromoted(bool $promoted): self
    {
        $this->promoted = $promoted;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getQuestion() === $this) {
                $answer->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|QuestionHistory[]
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(QuestionHistory $history): self
    {
        if (!$this->history->contains($history)) {
            $this->history[] = $history;
            $history->setQuestion($this);
        }

        return $this;
    }

    public function removeHistory(QuestionHistory $history): self
    {
        if ($this->history->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getQuestion() === $this) {
                $history->setQuestion(null);
            }
        }

        return $this;
    }
}
