<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Repository\QuestionHistoryRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionHistoryRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class QuestionHistory
{
    use CreatedAt;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"csv_export"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups({"csv_export"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="history")
     */
    private $question;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
