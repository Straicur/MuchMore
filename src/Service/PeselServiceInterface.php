<?php

namespace App\Service;

/**
 * PeselServiceInterface
 *
 */
interface PeselServiceInterface
{
    public function checkSum(): bool;

    public function getGender(): string;

    public function getBirthDate(): \DateTime|false;
}