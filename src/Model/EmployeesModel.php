<?php

namespace App\Model;

class EmployeesModel implements ModelInterface
{
    /**
     * @var EmployeesModel[]
     */
    private array $employees = [];
    private int $page;
    private int $maxPage;
    private int $limit;
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
    public function getMaxPage(): int
    {
        return $this->maxPage;
    }

    /**
     * @param int $maxPage
     */
    public function setMaxPage(int $maxPage): void
    {
        $this->maxPage = $maxPage;
    }

    /**
     * @return EmployeesModel[]
     */
    public function getEmployees(): array
    {
        return $this->employees;
    }

    /**
     * @param EmployeesModel[] $employees
     */
    public function setEmployees(array $employees): void
    {
        $this->employees = $employees;
    }

    public function addEmployee(EmployeeModel $book): void
    {
        $this->employees[] = $book;
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