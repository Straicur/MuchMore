<?php

namespace App\Tests\Controller\EmployeeController;

use App\Enums\GenderEnum;
use App\Tests\AbstractWebTest;
use DateTime;

class EmployeeGetTest extends AbstractWebTest
{
    public function test_employeeGetSuccess()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"),"98090100737",$gender,"zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $crawler = self::$webClient->request("GET", "/api/employee/get/".$employee->getId()->__toString(),server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("id",$responseContent);
        $this->assertArrayHasKey("email",$responseContent);
        $this->assertArrayHasKey("roles",$responseContent);
        $this->assertArrayHasKey("firstname",$responseContent);
        $this->assertArrayHasKey("lastname",$responseContent);
        $this->assertArrayHasKey("birthday",$responseContent);
        $this->assertArrayHasKey("pesel",$responseContent);
        $this->assertArrayHasKey("gender",$responseContent);
        $this->assertArrayHasKey("id",$responseContent["gender"]);
        $this->assertArrayHasKey("name",$responseContent["gender"]);
        $this->assertSame($gender->getName(),$responseContent["gender"]["name"]);

    }

    public function test_employeeGetNotExistingIdCredentials()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"),"98090100737",$gender,"zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $crawler = self::$webClient->request("GET", "/api/employee/get/66666c4e-16e6-1ecc-9890-a7e8b0073d3b",server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    public function test_employeeGetEmptyCredentials()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"),"98090100737",$gender,"zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $crawler = self::$webClient->request("GET", "/api/employee/get/",server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }
    public function test_employeeGetNotAuthorized()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"),"98090100737",$gender,"zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $crawler = self::$webClient->request("GET", "/api/employee/get/".$employee->getId()->__toString());

        $this->assertResponseStatusCodeSame(401);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }
}