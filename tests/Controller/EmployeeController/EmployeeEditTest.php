<?php

namespace App\Tests\Controller\EmployeeController;

use App\Enums\GenderEnum;
use App\Repository\EmployeeRepository;
use App\Tests\AbstractWebTest;
use DateTime;

class EmployeeEditTest extends AbstractWebTest
{
    public function test_employeeEditSuccess()
    {
        $employeeRepository = $this->getService(EmployeeRepository::class);

        $this->assertInstanceOf(EmployeeRepository::class, $employeeRepository);

        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();

        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("id", $responseContent);
        $this->assertArrayHasKey("email", $responseContent);
        $this->assertArrayHasKey("roles", $responseContent);
        $this->assertArrayHasKey("firstname", $responseContent);
        $this->assertArrayHasKey("lastname", $responseContent);
        $this->assertArrayHasKey("birthday", $responseContent);
        $this->assertArrayHasKey("pesel", $responseContent);
        $this->assertArrayHasKey("gender", $responseContent);
        $this->assertArrayHasKey("id", $responseContent["gender"]);
        $this->assertArrayHasKey("name", $responseContent["gender"]);
        $this->assertSame($gender->getName(), $responseContent["gender"]["name"]);

        $employeeAfter = $employeeRepository->findOneBy([
            "email" => $content["email"]
        ]);

        $this->assertNotNull($employeeAfter);
    }

    public function test_employeeEditOtherUserSuccess()
    {
        $employeeRepository = $this->getService(EmployeeRepository::class);

        $this->assertInstanceOf(EmployeeRepository::class, $employeeRepository);

        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");
        $employee2 = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee2->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey("id", $responseContent);
        $this->assertArrayHasKey("email", $responseContent);
        $this->assertArrayHasKey("roles", $responseContent);
        $this->assertArrayHasKey("firstname", $responseContent);
        $this->assertArrayHasKey("lastname", $responseContent);
        $this->assertArrayHasKey("birthday", $responseContent);
        $this->assertArrayHasKey("pesel", $responseContent);
        $this->assertArrayHasKey("gender", $responseContent);
        $this->assertArrayHasKey("id", $responseContent["gender"]);
        $this->assertArrayHasKey("name", $responseContent["gender"]);
        $this->assertSame($gender->getName(), $responseContent["gender"]["name"]);

        $employeeAfter = $employeeRepository->findOneBy([
            "email" => $content["email"]
        ]);

        $this->assertNotNull($employeeAfter);
    }

    public function test_employeeEditIncorrectUserDontExistsCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $employee2 = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => "66666c4e-16e6-1ecc-9890-a7e8b0073d3b",
            "email" => "test3@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }

    public function test_employeeEditIncorrectUsedEmailExistsCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $employee2 = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "test2@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }

    public function test_employeeEditWrongBirthdayCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $employee2 = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "test2@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1999",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }

    public function test_employeeEditOneEmptyRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditBadEmailRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@c",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditBadFirstnameRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "111",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditBadLastnameRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "22222",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditEmptyRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditBadGender()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1998",
            "pesel" => "98090100444",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(404);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
        $this->assertArrayHasKey("data", $responseContent);
    }

    public function test_employeeEditBadBirthday()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.090199",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditBadPesel()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "employeeID" => $employee->getId(),
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "birthday" => "01.09.1999",
            "pesel" => "9844122737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeEditEmptyNotAuthorized()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $content = [];

        $crawler = self::$webClient->request("PATCH", "/api/employee/edit", content: json_encode($content));

        $this->assertResponseStatusCodeSame(401);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }
}