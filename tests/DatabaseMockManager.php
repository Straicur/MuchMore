<?php

namespace App\Tests;

use App\Entity\Employee;
use App\Entity\Gender;
use App\Enums\GenderEnum;
use App\Repository\EmployeeRepository;
use App\Repository\GenderRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabaseMockManager
{
    private KernelInterface $kernel;
    private ?KernelBrowser $webClient;

    public function __construct(KernelInterface $kernel, ?KernelBrowser $webClient = null)
    {
        $this->kernel = $kernel;
        $this->webClient = $webClient;
    }

    protected function getService(string $serviceName): object
    {
        return $this->kernel->getContainer()->get($serviceName);
    }

    public function testFunc_loginUser(Employee $user): string
    {
        $content = [
            "email" => $user->getEmail(),
            "security" => [
                "credentials" => [
                    "password" => $user->getPassword()
                ]
            ]
        ];

        $crawler = $this->webClient->request("POST", "/api/login_check", server: [
            'CONTENT_TYPE' => 'application/json'
        ], content: json_encode($content));

        $response = $this->webClient->getResponse();

        $responseContent = json_decode($response->getContent(), true);

        return $responseContent["token"];
    }

    public function testFunc_addEmployee(string $email, string $firstname, string $lastname, \DateTime $birthday, string $pesel, Gender $gender, string $password): Employee
    {
        $employeeRepository = $this->getService(EmployeeRepository::class);

        $newEmployee = new Employee($email, $firstname, $lastname, $birthday, $pesel, $gender);

        $newEmployee->setPassword($password);

        $employeeRepository->add($newEmployee);

        return $newEmployee;
    }

    public function testFunc_addGender(GenderEnum $name): Gender
    {
        $genderRepository = $this->getService(GenderRepository::class);

        $newGender = new Gender($name->value);

        $genderRepository->add($newGender);

        return $newGender;
    }
}