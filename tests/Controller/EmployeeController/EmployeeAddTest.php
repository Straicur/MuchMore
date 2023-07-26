<?php

namespace App\Tests\Controller\EmployeeController;

use App\Enums\GenderEnum;
use App\Repository\EmployeeRepository;
use App\Tests\AbstractWebTest;
use DateTime;

class EmployeeAddTest extends AbstractWebTest
{
    public function test_employeeAddSuccess()
    {
        $employeeRepository = $this->getService(EmployeeRepository::class);

        $this->assertInstanceOf(EmployeeRepository::class, $employeeRepository);

        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);

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

    public function test_employeeAddIncorrectUsedEmailExistsCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "test@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
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

    public function test_employeeAddPasswordNotSameCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "test2@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq13wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
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

    public function test_employeeAddWrongPeselGenderCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "test2@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq13wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100444",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
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

    public function test_employeeAddWrongBirthDayCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "test2@cos.pl",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq13wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1999",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
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

    public function test_employeeAddIncorrectPasswordCredentials(): void
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Za",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);
    }

    public function test_employeeAddOneEmptyRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadEmailRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@c",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadFirstnameRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "111",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadLastnameRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "22222",
            "password" => "Zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadPasswordRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadConfirmPasswordRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "Zaq12wsx",
            "confirmPassword" => "zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddEmptyRequest()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadGender()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1998",
            "pesel" => "98090100747",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadBirthday()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1999",
            "pesel" => "98090100737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddBadPesel()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "email" => "mosinskidamian21@gmail.com",
            "firstname" => "Damian",
            "lastname" => "Mosiński",
            "password" => "zaq12wsx",
            "confirmPassword" => "Zaq12wsx",
            "birthday" => "01.09.1999",
            "pesel" => "98440122737",
            "gender" => $gender->getName()
        ];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }

    public function test_employeeAddEmptyNotAuthorized()
    {
        $gender = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $gender, "zaq12wsx");

        $content = [];

        $crawler = self::$webClient->request("PUT", "/api/employee/add", content: json_encode($content));

        $this->assertResponseStatusCodeSame(401);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);
    }
}