<?php

namespace App\Controller;

use App\Annotation\AuthValidation;
use App\Entity\Employee;
use App\Exception\DataNotFoundException;
use App\Exception\InvalidJsonDataException;
use App\Model\EmployeeModel;
use App\Model\EmployeesModel;
use App\Model\GenderModel;
use App\Query\EmployeeAddQuery;
use App\Query\EmployeeEditQuery;
use App\Query\EmployeesQuery;
use App\Repository\EmployeeRepository;
use App\Repository\GenderRepository;
use App\Service\AuthorizedUserServiceInterface;
use App\Service\PeselService;
use App\Service\RequestServiceInterface;
use App\Tool\ResponseTool;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "Employee")]
class EmployeeController extends AbstractController
{
    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param LoggerInterface $endpointLogger
     * @param EmployeeRepository $employeeRepository
     * @param GenderRepository $genderRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param PeselService $peselService
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route('/api/employee/add', name: 'app_employee_add', methods: ["PUT"])]
    #[AuthValidation(checkAuthToken: true)]
    #[OA\Put(
        description: "Endpoint is used to add new employee",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: EmployeeAddQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Success",
                content: new Model(type: EmployeeModel::class),
            )
        ]
    )]
    public function employeeAdd(
        Request                     $request,
        RequestServiceInterface     $requestServiceInterface,
        LoggerInterface             $endpointLogger,
        EmployeeRepository          $employeeRepository,
        GenderRepository            $genderRepository,
        UserPasswordHasherInterface $passwordHasher,
        PeselService                $peselService
    ): Response
    {
        $employeeAddQuery = $requestServiceInterface->getRequestBodyContent($request, EmployeeAddQuery::class);

        if ($employeeAddQuery instanceof EmployeeAddQuery) {
            $userExists = $employeeRepository->findOneBy([
                "email" => $employeeAddQuery->getEmail()
            ]);

            if ($userExists != null) {
                $endpointLogger->error('Email in system');
                throw new DataNotFoundException(["employee.add.used.email"]);
            }

            if ($employeeAddQuery->getPassword() != $employeeAddQuery->getConfirmPassword()) {
                $endpointLogger->error('Passwords are not the same');
                throw new DataNotFoundException(["employee.add.invalid.passwords"]);
            }

            $gender = $genderRepository->findOneBy([
                "name" => $employeeAddQuery->getGender()->value
            ]);

            if ($gender == null) {
                $endpointLogger->error('Gender dont exist');
                throw new DataNotFoundException(["employee.add.gender.dont.exist"]);
            }

            $peselService->setPesel($employeeAddQuery->getPesel());

            if ($peselService->checkSum() || ($peselService->getGender() != $gender->getName())) {
                $endpointLogger->error('Wrong given gender');
                throw new DataNotFoundException(["employee.add.invalid.gender"]);
            }

            if (!$peselService->getBirthDate() || $peselService->getBirthDate()->getTimestamp() != $employeeAddQuery->getBirthday()->getTimestamp()) {
                $endpointLogger->error('Wrong giver birthday');
                throw new DataNotFoundException(["employee.add.invalid.birthday"]);
            }

            $employee = new Employee($employeeAddQuery->getEmail(), $employeeAddQuery->getFirstname(), $employeeAddQuery->getLastname(), $employeeAddQuery->getBirthday(), $employeeAddQuery->getPesel(), $gender);

            $hashedPassword = $passwordHasher->hashPassword(
                $employee,
                $employeeAddQuery->getPassword()
            );

            $employee->setPassword($hashedPassword);

            $employeeRepository->add($employee);

            return ResponseTool::getResponse(new EmployeeModel($employee->getId(), $employee->getEmail(), implode($employee->getRoles()), $employee->getFirstname(), $employee->getLastname(), $employee->getBirthday(), $employee->getPesel(), new GenderModel($employee->getGender()->getId(), $employee->getGender()->getName())), 201);
        } else {
            $endpointLogger->error('Invalid query');
            throw new InvalidJsonDataException("employee.add.invalid.query");
        }
    }

    /**
     * @param Employee $id
     * @return Response
     */
    #[Route('/api/employee/get/{id}', name: 'app_employee_get', methods: ["GET"])]
    #[AuthValidation(checkAuthToken: true)]
    #[OA\Get(
        description: "Endpoint is used to get details of employee",
        requestBody: new OA\RequestBody(),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: EmployeeModel::class),
            )
        ]
    )]
    public function employeeGet(
        Employee $id
    ): Response
    {
        return ResponseTool::getResponse(new EmployeeModel($id->getId(), $id->getEmail(), implode($id->getRoles()), $id->getFirstname(), $id->getLastname(), $id->getBirthday(), $id->getPesel(), new GenderModel($id->getGender()->getId(), $id->getGender()->getName())));
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param LoggerInterface $endpointLogger
     * @param EmployeeRepository $employeeRepository
     * @param GenderRepository $genderRepository
     * @param PeselService $peselService
     * @return Response
     * @throws DataNotFoundException
     * @throws InvalidJsonDataException
     */
    #[Route('/api/employee/edit', name: 'app_employee_edit', methods: ["PATCH"])]
    #[AuthValidation(checkAuthToken: true)]
    #[OA\Patch(
        description: "Endpoint is used to edit employee",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: EmployeeEditQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: EmployeeModel::class),
            )
        ]
    )]
    public function employeeEdit(
        Request                 $request,
        RequestServiceInterface $requestServiceInterface,
        LoggerInterface         $endpointLogger,
        EmployeeRepository      $employeeRepository,
        GenderRepository        $genderRepository,
        PeselService            $peselService
    ): Response
    {
        $employeeEditQuery = $requestServiceInterface->getRequestBodyContent($request, EmployeeEditQuery::class);

        if ($employeeEditQuery instanceof EmployeeEditQuery) {
            $employee = $employeeRepository->findOneBy([
                "id" => $employeeEditQuery->getEmployeeID()
            ]);

            if ($employee == null) {
                $endpointLogger->error('Employee dont exist');
                throw new DataNotFoundException(["employee.edit.dont.exist"]);
            }

            if ($employee->getEmail() != $employeeEditQuery->getEmail()) {
                $userExists = $employeeRepository->findOneBy([
                    "email" => $employeeEditQuery->getEmail()
                ]);

                if ($userExists != null) {
                    $endpointLogger->error('Email in system');
                    throw new DataNotFoundException(["employee.edit.used.email"]);
                }
            }

            $employee->setEmail($employeeEditQuery->getEmail());

            $gender = $genderRepository->findOneBy([
                "name" => $employeeEditQuery->getGender()->value
            ]);

            if ($gender == null) {
                $endpointLogger->error('Gender dont exist');
                throw new DataNotFoundException(["employee.edit.gender.dont.exist"]);
            }

            $peselService->setPesel($employeeEditQuery->getPesel());

            if ($peselService->checkSum() || ($peselService->getGender() != $gender->getName())) {
                $endpointLogger->error('Wrong given gender');
                throw new DataNotFoundException(["employee.edit.invalid.gender"]);
            }

            $employee->setGender($gender);

            if (!$peselService->getBirthDate() || $peselService->getBirthDate()->getTimestamp() != $employeeEditQuery->getBirthday()->getTimestamp()) {
                $endpointLogger->error('Wrong giver birthday');
                throw new DataNotFoundException(["employee.edit.invalid.birthday"]);
            }

            $employee->setPesel($employeeEditQuery->getPesel());
            $employee->setBirthday($employeeEditQuery->getBirthday());
            $employee->setFirstname($employeeEditQuery->getFirstname());
            $employee->setLastname($employeeEditQuery->getLastname());

            $employeeRepository->add($employee);

            return ResponseTool::getResponse(new EmployeeModel($employee->getId(), $employee->getEmail(), implode($employee->getRoles()), $employee->getFirstname(), $employee->getLastname(), $employee->getBirthday(), $employee->getPesel(), new GenderModel($employee->getGender()->getId(), $employee->getGender()->getName())));
        } else {
            $endpointLogger->error('Invalid query');
            throw new InvalidJsonDataException("employee.edit.invalid.query");
        }
    }

    /**
     * @param Request $request
     * @param RequestServiceInterface $requestServiceInterface
     * @param AuthorizedUserServiceInterface $authorizedUserService
     * @param LoggerInterface $endpointLogger
     * @param EmployeeRepository $employeeRepository
     * @return Response
     * @throws InvalidJsonDataException
     */
    #[Route('/api/employee/list', name: 'app_employees', methods: ["POST"])]
    #[AuthValidation(checkAuthToken: true)]
    #[OA\Post(
        description: "Endpoint is used to get list of employees",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: EmployeesQuery::class),
                type: "object"
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new Model(type: EmployeesModel::class),
            )
        ]
    )]
    public function employees(
        Request                        $request,
        RequestServiceInterface        $requestServiceInterface,
        AuthorizedUserServiceInterface $authorizedUserService,
        LoggerInterface                $endpointLogger,
        EmployeeRepository             $employeeRepository
    ): Response
    {
        $employeesQuery = $requestServiceInterface->getRequestBodyContent($request, EmployeesQuery::class);

        if ($employeesQuery instanceof EmployeesQuery) {
            $searchData = $employeesQuery->getSearchData();

            $email = null;
            $firstname = null;
            $lastname = null;
            $pesel = null;
            $genderName = null;
            $sort = null;
            $birthdayFrom = null;
            $birthdayTo = null;

            if (array_key_exists('email', $searchData)) {
                $email = ($searchData['email'] && '' != $searchData['email']) ? "%" . $searchData['email'] . "%" : null;
            }
            if (array_key_exists('firstname', $searchData)) {
                $firstname = ($searchData['firstname'] && '' != $searchData['firstname']) ? "%" . $searchData['firstname'] . "%" : null;
            }
            if (array_key_exists('lastname', $searchData)) {
                $lastname = ($searchData['lastname'] && '' != $searchData['lastname']) ? "%" . $searchData['lastname'] . "%" : null;
            }
            if (array_key_exists('pesel', $searchData)) {
                $pesel = ($searchData['pesel'] && '' != $searchData['pesel']) ? "%" . $searchData['pesel'] . "%" : null;
            }
            if (array_key_exists('sort', $searchData)) {
                $sort = $searchData['sort'];
            }
            if (array_key_exists('genderName', $searchData)) {
                $genderName = ($searchData['genderName'] && '' != $searchData['genderName']) ? "%" . $searchData['genderName'] . "%" : null;
            }
            if (array_key_exists('birthdayFrom', $searchData) && $searchData['birthdayFrom']) {
                $birthdayFrom = $searchData['birthdayFrom'];
            }
            if (array_key_exists('birthdayTo', $searchData) && $searchData['birthdayTo']) {
                $birthdayTo = $searchData['birthdayTo'];
            }

            $employees = $employeeRepository->searchEmployees($email, $firstname, $lastname, $pesel, $genderName, $sort, $birthdayFrom, $birthdayTo);

            $successModel = new EmployeesModel();

            $minResult = $employeesQuery->getPage() * $employeesQuery->getLimit();
            $maxResult = $employeesQuery->getLimit() + $minResult;

            foreach ($employees as $index => $employee) {
                if ($index < $minResult) {
                    continue;
                } elseif ($index < $maxResult) {
                    $employeeModel = new EmployeeModel(
                        $employee->getId(),
                        $employee->getEmail(),
                        implode($employee->getRoles()),
                        $employee->getFirstname(),
                        $employee->getLastname(),
                        $employee->getBirthday(),
                        $employee->getPesel(),
                        new GenderModel($employee->getGender()->getId(), $employee->getGender()->getName())
                    );

                    $successModel->addEmployee($employeeModel);
                } else {
                    break;
                }
            }

            $successModel->setPage($employeesQuery->getPage());
            $successModel->setLimit($employeesQuery->getLimit());
            $successModel->setMaxPage(ceil(count($employees) / $employeesQuery->getLimit()));

            return ResponseTool::getResponse($successModel);
        } else {
            $endpointLogger->error('Invalid query');
            throw new InvalidJsonDataException("employees.invalid.query");
        }
    }
}