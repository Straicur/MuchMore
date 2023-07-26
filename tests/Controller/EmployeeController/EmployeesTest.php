<?php

namespace App\Tests\Controller\EmployeeController;

use App\Enums\GenderEnum;
use App\Tests\AbstractWebTest;
use DateTime;

class EmployeesTest extends AbstractWebTest
{
    public function test_employeesSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(7, $responseContent["employees"]);
    }

    public function test_employeesSpecificEmailSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(7, $responseContent["employees"]);
    }

    public function test_employeesSpecificFirstnameSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "Dam1",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(1, $responseContent["employees"]);
    }

    public function test_employeesSpecificLastnameSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos1", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "Mos1",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(1, $responseContent["employees"]);
    }

    public function test_employeesSpecificPeselSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98090700888",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(1, $responseContent["employees"]);
    }

    public function test_employeesSpecificGenderSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "Kobieta",
                "sort" => 1,
                "birthdayFrom" => "01.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(4, $responseContent["employees"]);
    }

    public function test_employeesSpecificDatesSearchSuccess()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "06.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        /// step 5
        $this->assertIsArray($responseContent);


        $this->assertArrayHasKey("page", $responseContent);
        $this->assertArrayHasKey("maxPage", $responseContent);
        $this->assertArrayHasKey("employees", $responseContent);
        $this->assertCount(2, $responseContent["employees"]);
    }

    public function test_employeesMissingRequestData()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "06.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", server: [
            'HTTP_Authorization' => sprintf('%s %s', 'Bearer', $token),
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], content: json_encode($content));

        $this->assertResponseStatusCodeSame(400);

        $response = self::$webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey("error", $responseContent);

    }

    public function test_employeesNotAuthorized()
    {
        $genderMan = $this->databaseMockManager->testFunc_addGender(GenderEnum::MAN);
        $genderWoman = $this->databaseMockManager->testFunc_addGender(GenderEnum::WOMAN);

        $employee = $this->databaseMockManager->testFunc_addEmployee("test@cos.pl", "Dam1", "Mos", DateTime::createFromFormat('d.m.Y', "01.09.1998"), "98090100737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test2@cos.pl", "Dam2", "Mos", DateTime::createFromFormat('d.m.Y', "02.09.1998"), "98090200737", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test3@cos.pl", "Dam3", "Mos", DateTime::createFromFormat('d.m.Y', "03.09.1998"), "98090300646", $genderMan, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test4@cos.pl", "Dam4", "Mos", DateTime::createFromFormat('d.m.Y', "04.09.1998"), "98090400444", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test5@cos.pl", "Dam5", "Mos", DateTime::createFromFormat('d.m.Y', "05.09.1998"), "98090500666", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test6@cos.pl", "Dam6", "Mos", DateTime::createFromFormat('d.m.Y', "06.09.1998"), "98090600222", $genderWoman, "zaq12wsx");
        $employee = $this->databaseMockManager->testFunc_addEmployee("test7@cos.pl", "Dam7", "Mos", DateTime::createFromFormat('d.m.Y', "07.09.1998"), "98090700888", $genderWoman, "zaq12wsx");
        $token = $this->databaseMockManager->testFunc_loginUser($employee);

        $content = [
            "page" => 0,
            "limit" => 10,
            "searchData" => [
                "email" => "test",
                "firstname" => "D",
                "lastname" => "M",
                "pesel" => "98",
                "genderName" => "a",
                "sort" => 1,
                "birthdayFrom" => "06.09.1998",
                "birthdayTo" => "07.09.1998"
            ]
        ];

        $crawler = self::$webClient->request("POST", "/api/employee/list", content: json_encode($content));

        $this->assertResponseStatusCodeSame(401);

        $responseContent = self::$webClient->getResponse()->getContent();

        $this->assertNotNull($responseContent);
        $this->assertNotEmpty($responseContent);
        $this->assertJson($responseContent);

    }
}