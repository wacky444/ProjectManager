<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping\Entity;
use PhpParser\Node\Stmt\Switch_;
use Psr\Log\LoggerInterface;

use App\Utils\ValidationFunctions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SimpleAPIController extends Controller
{


    // create project or task
    public function create($type, $name, $description, $projectId, ValidationFunctions $validationFunctions,
                           LoggerInterface $logger)
    {

        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

        $validCharacters = $validationFunctions->checkValidCharacters($logger, $name, $description);

        $newEntity = null;

        if ($validCharacters["isValid"]) {
            Switch ($type) {
                case 'Project':
                    $newEntity = new Project($name, $this->getUser(), $description);


                    break;
                case 'Task':
                    $projectRepository = $this->getDoctrine()->getRepository(Project::class);

                    $project = $projectRepository->find($projectId);

                    if (!is_null($project)) {
                        $newEntity = new Task($name, $project, $description);
                    } else {
                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['PROJECT_NOT_FOUND'],
                            'message' => $type . ' project not found with the id '  // TODO translate
                        ));
                    }

                    break;
                default:
                    break;
            }

            // The entity is ready to be saved
            if (!is_null($newEntity)) {
                // tell Doctrine to (eventually) save the object (no queries yet)
                $entityManager->persist($newEntity);

                try {
                    // actually executes the queries (i.e. the INSERT query)
                    $entityManager->flush();
                    $response->setData(array(
                        'errorCode' => $validationFunctions::ERROR_CODES['QUERY_ERROR'],
                        'message' => $type . ' saved successfully',  // TODO translate
                        'entityId' => $newEntity->getId()
                    ));

                } catch (\Doctrine\DBAL\DBALException  $e) {
                    $response->setData(array('errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                        'message' => $type . ' not saved: ' . $e->getMessage()));
                    $logger->error('Error saving to database ' . $e->getMessage());
                }
            }
        } else {
            $response->setData(array(
                'errorCode' => $validationFunctions::ERROR_CODES['INVALID_CHARACTERS'],
                'message' => $type . ' not saved: '
                    . $validCharacters['errorMessage']));

        };


        return $response;

    }

    // update project or task
    public function update($entityId, $type, $name, $description, $projectId, ValidationFunctions $validationFunctions,
                           LoggerInterface $logger)
    {

        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

        $validCharacters = $validationFunctions->checkValidCharacters($logger, $name, $description);

        $entity = null;

        if ($validCharacters["isValid"]) {
            Switch ($type) {
                case 'Project':
                    $projectRepository = $this->getDoctrine()->getRepository(Project::class);
                    $entity = $projectRepository->find($entityId);

                    if (!is_null($entity)) {
                        // TODO check if $this->getUser() can edit this project
                        $entity->setName($name);
                        $entity->setDescription($description);
                    } else {
                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['PROJECT_NOT_FOUND'],
                            'message' => $type . ' not found with the id ' . $entityId  // TODO translate
                        ));
                    }


                    break;
                case 'Task':
                    $taskRepository = $this->getDoctrine()->getRepository(Task::class);
                    $entity = $taskRepository->find($entityId);

                    if (!is_null($entity)) {

                        $projectRepository = $this->getDoctrine()->getRepository(Project::class);
                        $project = $projectRepository->find($projectId);

                        if (!is_null($project)) {
                            // TODO check if $this->getUser() can edit the project of this task
                            $entity->setName($name);
                            $entity->setDescription($description);
                            $entity->setProjectId($project);
                        } else {
                            $response->setData(array(
                                'errorCode' => $validationFunctions::ERROR_CODES['PROJECT_NOT_FOUND'],
                                'message' => $type . ' project not found with the id '  // TODO translate
                            ));
                        }
                    } else {
                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['TASK_NOT_FOUND'],
                            'message' => $type . ' not found with the id ' . $entityId // TODO translate
                        ));
                    }


                    break;
                default:
                    break;
            }

            // The entity is ready to be saved
            if (!is_null($entity)) {
                // tell Doctrine to (eventually) save the object (no queries yet)
                $entityManager->persist($entity);

                try {
                    // actually executes the queries (i.e. the INSERT query)
                    $entityManager->flush();
                    $response->setData(array(
                        'errorCode' => $validationFunctions::ERROR_CODES['QUERY_ERROR'],
                        'message' => $type . ' saved successfully',  // TODO translate
                        'entityId' => $entity->getId()
                    ));

                } catch (\Doctrine\DBAL\DBALException  $e) {
                    $response->setData(array('errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                        'message' => $type . ' not saved: ' . $e->getMessage()));
                    $logger->error('Error saving to database ' . $e->getMessage());
                }
            }
        } else {
            $response->setData(array(
                'errorCode' => $validationFunctions::ERROR_CODES['INVALID_CHARACTERS'],
                'message' => $type . ' not saved: '
                    . $validCharacters['errorMessage']));

        };


        return $response;

    }

    public function createProject($projectName, $projectDescription, ValidationFunctions $validationFunctions,
                                  LoggerInterface $logger)
    {
        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

        //Check that the input has only valid characters
        $validCharacters = $validationFunctions->checkValidCharacters($logger, $projectName, $projectDescription);

        if ($validCharacters["isValid"]) {
            $project = new Project();
            $project->setName($projectName);
            $project->setDescription($projectDescription);
            $project->setUserId($this->getUser());

            // tell Doctrine to (eventually) save the object (no queries yet)
            $entityManager->persist($project);

            try {
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $response->setData(array(
                    'errorCode' => 0,
                    'message' => 'Project saved successfully',  // TODO translate
                    'projectId' => $project->getProjectId()
                ));

            } catch (\Doctrine\DBAL\DBALException  $e) {
                $response->setData(array('errorCode' => 1, 'message' => 'Project not saved: ' . $e->getMessage()));
                $logger->error('Error saving to database ' . $e->getMessage());
            }
        } else {
            $invalidStrings = $validCharacters["invalidStrings"];

            $response->setData(array(
                'errorCode' => 2,
                'message' => 'Project not saved: '
                    . $validCharacters['errorMessage']));

        };


        return $response;
    }

    public function addTask($project, $taskName, $taskDescription)
    {

    }


}