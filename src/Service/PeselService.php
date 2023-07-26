<?php

namespace App\Service;

use App\Enums\GenderEnum;
use App\Exception\DataNotFoundException;
use DateTime;

class PeselService implements PeselServiceInterface
{
    private ?string $pesel = null;

    /**
     * @param string $pesel
     */
    public function setPesel(string $pesel): void
    {
        $this->pesel = $pesel;
    }

    /**
     * @return bool
     * @throws DataNotFoundException
     */
    public function checkSum(): bool
    {
        if ($this->pesel == null) {
            throw new DataNotFoundException(["pesel.service.pesel.not.set"]);
        }

        $controlScales = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += $controlScales[$i] * $this->pesel[$i];
        }

        $int = 10 - $sum % 10;
        $controlNr = ($int == 10) ? 0 : $int;

        if ($controlNr == $this->pesel[10]) {
            return true;
        }
        return false;
    }

    /**
     * @return DateTime|false
     * @throws DataNotFoundException
     */
    public function getBirthDate(): \DateTime|false
    {
        if ($this->pesel == null) {
            throw new DataNotFoundException(["pesel.service.pesel.not.set"]);
        }

        $month = substr($this->pesel, 2, 2);
        $arrMonths = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $arrAdditionalMonths = array(0, 80, 20);

        foreach ($arrAdditionalMonths as $additionalMonth) {
            $arrMonthsBase = range(1, 12);
            foreach ($arrMonthsBase as $monthBase) {
                $arrMonths[] = $additionalMonth + $monthBase;
            }
        }

        if (!in_array($month, $arrMonths)) {
            return false;
        }

        switch (substr($month, 0, 1)) {
            case "0":
            case "1":
                $century = 1900;
                break;
            case "8":
                $century = 1800;
                break;
            case "2":
                $century = 2000;
                break;
            default:
            {
                return false;
            }
        }

        if ($century == '2000') $month = $month - 20;
        if ($century == '1800') $month = $month - 80;

        $year = $century . substr($this->pesel, 0, 2);
        $maxDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $day = substr($this->pesel, 4, 2);

        if ($day > $maxDays || $day <= 0) {
            return false;
        }

        return DateTime::createFromFormat('d.m.Y', "$year-$month-$day");
    }

    /**
     * @return string
     * @throws DataNotFoundException
     */
    public function getGender(): string
    {
        if ($this->pesel == null) {
            throw new DataNotFoundException(["pesel.service.pesel.not.set"]);
        }

        $tenthNumber = $this->pesel[9];
        if (($tenthNumber % 2 == 0)) {
            return GenderEnum::WOMAN->value;
        }
        return GenderEnum::MAN->value;
    }
}