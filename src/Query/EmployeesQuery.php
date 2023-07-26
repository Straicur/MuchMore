<?php

namespace App\Query;

use App\Enums\EntitySort;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeesQuery
{
    #[Assert\NotNull(message: "Page is null")]
    #[Assert\NotBlank(message: "Page is empty")]
    #[Assert\Type(type: "integer")]
    private int $page;
    #[Assert\NotNull(message: "Limit is null")]
    #[Assert\NotBlank(message: "Limit is empty")]
    #[Assert\Type(type: "integer")]
    private int $limit;
//    #[Assert\NotNull(message: "Email is null")]
//    #[Assert\NotBlank(message: "Email is empty")]
//    #[Assert\Type(type: "string")]
//    private string $email;
//    #[Assert\NotNull(message: "Firstname is null")]
//    #[Assert\NotBlank(message: "Firstname is empty")]
//    #[Assert\Type(type: "string")]
//    #[Assert\Length(min: 1, max: 100)]
//    private string $firstname;
//    #[Assert\NotNull(message: "Lastname is null")]
//    #[Assert\NotBlank(message: "Lastname is empty")]
//    #[Assert\Type(type: "string")]
//    #[Assert\Length(min: 1, max: 100)]
//    private string $lastname;
//    #[Assert\NotNull(message: "Birthday is null")]
//    #[Assert\NotBlank(message: "Birthday is blank")]
//    #[Assert\Type(type: "datetime")]
//    private \DateTime $birthdayFrom;
//    #[Assert\NotNull(message: "Birthday is null")]
//    #[Assert\NotBlank(message: "Birthday is blank")]
//    #[Assert\Type(type: "datetime")]
//    private \DateTime $birthdayTo;
//    #[Assert\NotNull(message: "Pesel is null")]
//    #[Assert\NotBlank(message: "Pesel password is empty")]
//    #[Assert\Type(type: "string")]
//    private string $pesel;
//    #[Assert\NotNull(message: "GenderID is null")]
//    #[Assert\NotBlank(message: "GenderID is blank")]
//    #[Assert\Uuid]
//    private Uuid $genderID;
//    #[Assert\NotNull(message: "Sort is null")]
//    #[Assert\NotBlank(message: "Sort is empty")]
//    #[Assert\Type(type: "integer")]
//    #[Assert\Range(
//        notInRangeMessage: 'You must be between {{ min }} and {{ max }}',
//        min: 1,
//        max: 6,
//    )]
//    private int $sort;
    protected array $searchData = [];

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('searchData', new Assert\Collection([
            'fields' => [
                'email' => new Assert\Optional([
                    new Assert\NotBlank(message: 'Email is empty'),
                    new Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}'),
                ]),
                'firstname' => new Assert\Optional([
                    new Assert\NotBlank(message: 'Firstname is empty'),
                    new Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}'),
                ]),
                'lastname' => new Assert\Optional([
                    new Assert\NotBlank(message: 'Lastname is empty'),
                    new Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}'),
                ]),
                'pesel' => new Assert\Optional([
                    new Assert\NotBlank(message: 'Pesel is empty'),
                    new Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}'),
                    new Assert\GreaterThan(0)
                ]),
                'genderID' => new Assert\Optional([
                    new Assert\NotBlank(message: 'GenderID is empty'),
                    new Assert\Uuid(message: 'The value {{ value }} is not a valid {{ type }}'),
                    new Assert\GreaterThan(0)
                ]),
                'sort' => new Assert\Optional([
                    new Assert\NotBlank(message: 'Sort is empty'),
                    new Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}'),
                    new Assert\GreaterThan(1),
                    new Assert\LessThan(6)
                ]),
                'birthdayFrom' => new Assert\Optional([
                    new Assert\NotBlank(message: 'BirthdayFrom is empty'),
                    new Assert\Type(type: 'datetime', message: 'The value {{ value }} is not a valid {{ type }}'),
                ]),
                'birthdayTo' => new Assert\Optional([
                    new Assert\NotBlank(message: 'BirthdayTo is empty'),
                    new Assert\Type(type: 'datetime', message: 'The value {{ value }} is not a valid {{ type }}'),
                ]),
            ],
        ]));
    }

    /**
     * @param string[] $searchData
     */
    #[OA\Property(property: 'searchData', properties: [
        new OA\Property(property: 'email', type: 'string', example: 'email', nullable: true),
        new OA\Property(property: 'firstname', type: 'string', example: 'firstname', nullable: true),
        new OA\Property(property: 'lastname', type: 'string', example: 'lastname', nullable: true),
        new OA\Property(property: 'pesel', type: 'integer', example: "12312313232", nullable: true),
        new OA\Property(property: 'genderID', type: 'string', example: "60266c4e-16e6-1ecc-9890-a7e8b0073d3b", nullable: true),
        new OA\Property(property: 'sort', type: 'integer', example: 1, nullable: true),
        new OA\Property(property: 'birthdayFrom', type: 'datetime', example: 'd.m.Y', nullable: true),
        new OA\Property(property: 'birthdayTo', type: 'datetime', example: 'd.m.Y', nullable: true),
    ], type: 'object')]
    public function setSearchData(array $searchData): void
    {
        if (array_key_exists('genderID', $searchData)) {
            $searchData['genderID'] =   Uuid::fromString($searchData['genderID'] );
        }

        if (array_key_exists('birthdayFrom', $searchData)) {
            $searchData['birthdayFrom'] = \DateTime::createFromFormat('d.m.Y', $searchData['birthdayFrom']);
        }

        if (array_key_exists('birthdayTo', $searchData)) {
            $searchData['birthdayTo'] = \DateTime::createFromFormat('d.m.Y', $searchData['birthdayTo']);
        }

        $this->searchData = $searchData;
    }

    /**
     * @return string[]
     */
    public function getSearchData(): array
    {
        return $this->searchData;
    }
    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}