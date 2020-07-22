<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentsControllerTest extends WebTestCase
{
    const actions = ['add' => [
        "uri" => "/api/students",
        "method" => "POST"
    ]];
    private $client;


    protected function setUp()
    {
        // Generate a client with the headers Content-type and Accept set to `application/json`
        $this->client = self::createClient([], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json']);
    }

    /**
     * Testing the add action with bad data send
     */
    public function testWrongAdd()
    {
        // Get params for the add action and generate a request
        $params = self::actions['add'];
        $crawler = $this->client->request($params['method'], $params['uri'], [], [], [],
        json_encode(['firstname' => "Philippe", "birthdate" => (new \DateTime("tomorrow"))->format("Y-m-d")]));
        // validate the status code
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = json_decode($this->client->getResponse()->getContent(), true);
        // Validate that the errors key has 2 lines and the partial content of each
        $this->assertCount(2, $content['errors']);
        $this->assertContains("null", $content['errors']['lastname']);
        $this->assertContains("This value should be less than", $content['errors']['birthdate']);
    }

    /**
     * Testing the add
     */
    public function testAdd()
    {
        // Get params for the add action and generate a request
        $params = self::actions['add'];
        $crawler = $this->client->request($params['method'], $params['uri'], [], [], [],
        json_encode(['firstname' => "Philippe","lastname" => "Pichet", "birthdate" => (new \DateTime("yesterday"))->format("Y-m-d")]));
        // validate the status code
        $this->assertResponseIsSuccessful();
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // Get the content on array form
        $content = json_decode($this->client->getResponse()->getContent(), true);
        // Validate data send
        $this->assertEquals("Philippe", $content['firstname']);
        $this->assertEquals("Pichet", $content['lastname']);
        $this->assertEquals((new \DateTime("yesterday"))->format("Y-m-d"), $content['birthdate']);
    }
}
