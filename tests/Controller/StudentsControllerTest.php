<?php

namespace App\Tests\Controller;

use App\Entity\Students;
use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentsControllerTest extends WebTestCase
{
    // Base url of the API
    CONST BASE_URI = "/api/students";

    // Average of the unique student store for test and of the class
    const AVERAGE = 5.5;

    /**
     * @var KernelBrowser
     */
    private $client;


    protected function setUp()
    {
        // Generate a client with the headers Content-type and Accept set to `application/json`
        $this->client = self::createClient([], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json']);
    }

    /**
     * Test of the list action
     */
    public function testList()
    {
        $this->client->request("GET", self::BASE_URI);
        $this->assertResponseIsSuccessful();
        $content = $this->parseResponse();
        // Checking the presence of key average and students
        $this->assertArrayHasKey("average", $content);
        $this->assertArrayHasKey("students", $content);
        // Checking the value of the average
        $this->assertEquals(self::AVERAGE, $content['average']);
    }

    /**
     * Test of the show action
     */
    public function testShow()
    {
        $student = $this->getStudent();
        $this->client->request("GET", self::BASE_URI . "/" . $student->getId());
        $this->assertResponseIsSuccessful();
        $content = $this->parseResponse();
        // Checking the number of key retunr and the value of the average
        $this->assertCount(6, $content);
        $this->assertEquals(self::AVERAGE, $content['average']);
    }

    /**
     * Testing the add
     */
    public function testAdd()
    {
        $birthdate = (new \DateTime("yesterday"))->format("Y-m-d");
        // Request to add a student
        $this->client->request("POST", self::BASE_URI, [], [], [],
            json_encode(['firstname' => "Fake", "lastname" => "Student", "birthdate" => $birthdate]));
        // Check the success response and the status code
        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Validate data send
        $this->assertEquals("Fake", $content['firstname']);
        $this->assertEquals("Student", $content['lastname']);
        $this->assertEquals($birthdate, $content['birthdate']);
    }

    /**
     * Testing the add action with wrong or missing data
     */
    public function testFailAdd()
    {
        $this->client->request("POST", self::BASE_URI, [], [], [],
            json_encode(['firstname' => "Philippe", "birthdate" => (new \DateTime("tomorrow"))->format("Y-m-d")]));
        // Check the status code
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Validate that the errors key has 2 lines and the partial content of each
        $this->assertCount(2, $content['errors']);
        $this->assertContains("null", $content['errors']['lastname']);
        $this->assertContains("This value should be less than", $content['errors']['birthdate']);
    }

    /**
     * Testing the update action
     */
    public function testUpdate()
    {
        $student = $this->getStudent();
        $this->client->request("PUT", self::BASE_URI . "/" . $student->getId(), [], [], [],
            json_encode(['firstname' => "Change", "lastname" => $student->getLastname(), "birthdate" => $student->getBirthdate()->format("Y-m-d")]));
        // Check the success response and the status code
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Check the number of row and the firstname has change but the lastname dont
        $this->assertCount(5, $content);
        $this->assertEquals("Change", $content['firstname']);
        $this->assertEquals($student->getLastname(), $content['lastname']);
    }

    /**
     * Testing  the update action with wrong or missing data
     */
    public function testBadUpdate()
    {
        $student = $this->getStudent();
        $this->client->request("PUT", self::BASE_URI . "/" . $student->getId(), [], [], [],
            json_encode(["lastname" => $student->getLastname(), "birthdate" => 5]));
        // Check the status code
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Check the number of row on the array errors and the value of firstname
        $this->assertCount(2, $content["errors"]);
        $this->assertEquals("This value should not be null.", $content['errors']['firstname']);
    }

    /**
     * Testing  the update action with a nonexistent student
     */
    public function testStudentNotFoundForUpdate()
    {
        $this->client->request("PUT", self::BASE_URI . "/0", [], [], [],
            json_encode(["lastname" => "Fake", "firstname" => "user", "birthdate" => (new \DateTime("yesterday"))->format("Y-m-d")]));
        // Check the success response and the status code
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Check the number of row on the array errors and the value of the ressource row
        $this->assertCount(1, $content["errors"]);
        $this->assertEquals("Student not found", $content['errors']['resource']);
    }


    /**
     * Testing add  grade on student
     */
    public function testAddGrade()
    {
        $student = $this->getStudent();
        $crawler = $this->client->request("POST", self::BASE_URI . "/" . $student->getId() . "/grades", [], [], [],
            json_encode(['grade' => 5, "course" => "course"]));
        // Check the success response and the status code
        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Check the number of row and the value of grade and the student is an array
        $this->assertCount(4, $content);
        $this->assertEquals(5, $content['grade']);
        $this->assertIsArray($content['student']);
    }


    /**
     * Testing add  grade on student with wrong data
     */
    public function testFailedAddGrade()
    {
        $student = $this->getStudent();
        $crawler = $this->client->request("POST", self::BASE_URI . "/" . $student->getId() . "/grades", [], [], [],
            json_encode(['grade' => 22, "course" => "te"]));
        // Check the status code
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = $this->parseResponse();
        // Check the value of each error message
        $this->assertEquals('This value should be between "0" and "20".', $content['errors']['grade']);
        $this->assertEquals('This value is too short. It should have 3 characters or more.', $content['errors']['course']);
    }


    /**
     * Testing  deletion of student
     */
    public function testDeleteUser()
    {
        $student = $this->getStudent();
        $crawler = $this->client->request("DELETE", self::BASE_URI . "/" . $student->getId());
        // Check the status code and the content is empty
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());
    }

    /**
     * Test a request with the content type application/x-www-form-urlencoded
     */
    public function testContentType()
    {

        $this->client->request("POST",self::BASE_URI, ['firstname' => "Fake", "lastname" => "Student", "birthdate" => (new \DateTime("yesterday"))->format("Y-m-d")],[], ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_ACCEPT' => 'application/json']);
        // Check the success response and the status code
        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test a request with a wrong content type
     */
    public function testWrongContentType()
    {
        $this->client->request("POST",self::BASE_URI, ['firstname' => "Fake", "lastname" => "Student", "birthdate" => (new \DateTime("yesterday"))->format("Y-m-d")],[], ['CONTENT_TYPE' => 'application/pdf', 'HTTP_ACCEPT' => 'application/json']);
        // Check the status code
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }


    /**
     * Function to parse the content of the response and store in array
     * @return array
     */
    private function parseResponse() : array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    /**
     * Function to get the test student to make action on him like :
     * - Update him
     * - Add grade
     * - Delete him
     * @return Students
     */
    private function getStudent() : Students
    {
        $em = self::$container->get('doctrine');
        return $em->getRepository(Students::class)->findOneBy(['firstname' => "firstname", "lastname" => "lastname"]);
    }
}
