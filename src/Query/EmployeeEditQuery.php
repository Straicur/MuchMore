<?php

namespace App\Query;

use App\Enums\GenderEnum;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeeEditQuery
{
    #[Assert\NotNull(message: "EmployeeID is null")]
    #[Assert\NotBlank(message: "EmployeeID is blank")]
    #[Assert\Uuid]
    private Uuid $employeeID;

    #[Assert\NotNull(message: "Email is null")]
    #[Assert\NotBlank(message: "Email is empty")]
    #[Assert\Email(message: "It's not an email")]
    private string $email;

    #[Assert\NotNull(message: "Firstname is null")]
    #[Assert\NotBlank(message: "Firstname is empty")]
    #[Assert\Type(type: "string")]
    #[Assert\Length(min: 2, max: 50)]
    #[Assert\Regex(pattern: '/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/', message: 'Bad firstname')]
    private string $firstname;

    #[Assert\NotNull(message: "Lastname is null")]
    #[Assert\NotBlank(message: "Lastname is empty")]
    #[Assert\Type(type: "string")]
    #[Assert\Length(min: 2, max: 100)]
    #[Assert\Regex(pattern: '/^[A-Za-z][A-Za-z\'\-]+([\ A-Za-z][A-Za-z\'\-]+)*/', message: 'Bad lastname')]
    private string $lastname;

    #[Assert\NotNull(message: "Birthday is null")]
    #[Assert\NotBlank(message: "Birthday is blank")]
    #[Assert\Type(type: "datetime")]
    private \DateTime $birthday;

    #[Assert\NotNull(message: "Pesel is null")]
    #[Assert\NotBlank(message: "Pesel password is empty")]
    #[Assert\Type(type: "string")]
    #[Assert\Regex(pattern: '/^[0-9]{11}$/', message: 'Bad pesel')]
    private string $pesel;

    #[Assert\NotNull(message: "GenderID is null")]
    #[Assert\NotBlank(message: "GenderID is blank")]
    #[Assert\Type(type: "string")]
    private string $gender;

    /**
     * @return Uuid
     */
    #[OA\Property(type: "string", example: "60266c4e-16e6-1ecc-9890-a7e8b0073d3b")]
    public function getEmployeeID(): Uuid
    {
        return $this->employeeID;
    }

    /**
     * @param string $employeeID
     */
    public function setEmployeeID(string $employeeID): void
    {
        $this->employeeID = Uuid::fromString($employeeID);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return \DateTime
     */
    #[OA\Property(property: "year", example: "d.m.Y")]
    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = DateTime::createFromFormat('d.m.Y', $birthday);
    }

    /**
     * @return string
     */
    public function getPesel(): string
    {
        return $this->pesel;
    }

    /**
     * @param string $pesel
     */
    public function setPesel(string $pesel): void
    {
        $this->pesel = $pesel;
    }

    /**
     * @return GenderEnum
     */
    public function getGender(): GenderEnum
    {
        return match ($this->gender) {
            "Mężczyzna" => GenderEnum::MAN,
            "Kobieta" => GenderEnum::WOMAN,
        };
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

}