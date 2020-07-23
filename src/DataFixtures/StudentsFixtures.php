<?php

namespace App\DataFixtures;

use App\Entity\Students;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class StudentsFixtures
 * This is used to fill the database with fake data for test.
 * @package App\DataFixtures
 */
class StudentsFixtures extends Fixture
{
    /**
     * Creation of a fake student
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $student = new Students();
        $student->setFirstname("firstname")
            ->setLastname("lastname")
            ->setBirthdate(\DateTime::createFromFormat('Y-m-d', "1987-08-25"));

        $manager->persist($student);
        $manager->flush();
        // Store the student on reference to be use on the GradesFixtures
        $this->addReference("student", $student);
    }
}
