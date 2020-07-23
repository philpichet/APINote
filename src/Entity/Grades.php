<?php

namespace App\Entity;

use App\Repository\GradesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GradesRepository::class)
 * This class is the representation of the grade
 */
class Grades
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"newGrade"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="0", max="20")
     * @Groups({"newGrade", "student"})
     */
    private $grade;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     * @Assert\Length(min=3, max=100)
     * @Groups({"newGrade", "student"})
     */
    private $course;

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

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(string $course): self
    {
        $this->course = $course;

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
