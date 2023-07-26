<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Gender;
use App\Enums\GenderEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $faker;
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $manGender = new Gender(GenderEnum::MAN->value);
        $womanGender = new Gender(GenderEnum::WOMAN->value);
        $manager->persist($manGender);
        $manager->persist($womanGender);
        $this->faker = Factory::create();

        for ($i = 0; $i < 99; $i++) {
            $pesel = "";
            for ($j = 0; $j < 11; $j++) {
                $pesel = $pesel . $this->faker->numberBetween(0, 9);
            }
            $employee = new Employee($this->faker->email, $this->faker->firstName, $this->faker->lastName, $this->faker->dateTime, $pesel, mt_rand(0, 1) ? $manGender : $womanGender);

            $employee->setPassword($this->hasher->hashPassword($employee, 'Zaq12wsx'));

            $manager->persist($employee);
        }

        $manager->flush();
    }
}
