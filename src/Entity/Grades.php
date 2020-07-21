<?php

namespace App\Entity;

use App\Repository\GradesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GradesRepository::class)
 */
class Grades
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"newGrade", "updateStudent"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="0", max="20")
     * @Groups({"newGrade", "updateStudent"})
     */
    private $grade;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @Groups({"newGrade", "updateStudent"})
     */
    private $matter;

    /**
     * @ORM\ManyToOne(targetEntity=Students::class, inversedBy="grades")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"newGrade"})
     */
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(int $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getMatter(): ?string
    {
        return $this->matter;
    }

    public function setMatter(string $matter): self
    {
        $this->matter = $matter;

        return $this;
    }

    public function getStudent(): ?Students
    {
        return $this->student;
    }

    public function setStudent(?Students $student): self
    {
        $this->student = $student;
        return $this;
    }
}
