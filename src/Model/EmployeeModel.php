<?php

namespace App\Model;

class EmployeeModel implements ModelInterface
{
    private string $id;
    private string $email;
    private string $roles;
    private string $firstname;
    private string $lastname;
    private int $birthday;
    private int $pesel;
    private GenderModel $gender;

    /**
     * @param string $id
     * @param string $email
     * @param string $roles
     * @param string $firstname
     * @param string $lastname
     * @param \DateTime $birthday
     * @param int $pesel
     * @param GenderModel $gender
     */
    public function __construct(string $id, string $email, string $roles, string $firstname, string $lastname, \DateTime $birthday, int $pesel, GenderModel $gender)
    {
        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birthday = $birthday->getTimestamp();
        $this->pesel = $pesel;
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
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
    public function getRoles(): string
    {
        return $this->roles;
    }

    /**
     * @param string $roles
     */
    public function setRoles(string $roles): void
    {
        $this->roles = $roles;
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
     * @return int
     */
    public function getBirthday(): int
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday(\DateTime $birthday): void
    {
        $this->birthday = $birthday->getTimestamp();
    }

    /**
     * @return int
     */
    public function getPesel(): int
    {
        return $this->pesel;
    }

    /**
     * @param int $pesel
     */
    public function setPesel(int $pesel): void
    {
        $this->pesel = $pesel;
    }

    /**
     * @return GenderModel
     */
    public function getGender(): GenderModel
    {
        return $this->gender;
    }

    /**
     * @param GenderModel $gender
     */
    public function setGender(GenderModel $gender): void
    {
        $this->gender = $gender;
    }

}