<?php

namespace App\DataFixtures;

use App\Entity\Students;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StudentsFixtures extends Fixture
{
    /**
     * Creation of a fake student use for tests
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
        $this->addReference("student", $student);
    }
}
