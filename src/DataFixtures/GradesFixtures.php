<?php

namespace App\DataFixtures;

use App\Entity\Grades;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GradesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Loop to create 10 grades for the student. They be used in test
        for ($index = 1; $index <= 10; $index++) {
            // Generation of dynamic variables
            $grade = new Grades();
            $grade->setGrade($index)
                ->setCourse("Course")
                ->setStudent($this->getReference("student"));
            $manager->persist($grade);
        }
        $manager->flush();
    }

    /**
     * This fixture depends of the Student's Fixture
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            StudentsFixtures::class
        ];
    }
}
