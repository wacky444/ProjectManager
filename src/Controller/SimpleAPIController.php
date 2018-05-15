<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping\Entity;
use PhpParser\Node\Stmt\Switch_;
use Psr\Log\LoggerInterface;

use App\Util\ValidationFunctions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SimpleAPIController extends Controller
{


    // create project or task
    public function create($type, $name, $description, $projectId, ValidationFunctions $validationFunctions,
                           LoggerInterface $logger)
    {

        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

        $validCharacters = $validationFunctions->checkValidCharacters($name, $description);

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
                        'errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                        'message' => $type . ' saved successfully',  // TODO translate
                        'entityId' => $newEntity->getId()
                    ));

                } catch (\Doctrine\DBAL\DBALException  $e) {
                    $response->setData(array('errorCode' => $validationFunctions::ERROR_CODES['QUERY_ERROR'],
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

        $validCharacters = $validationFunctions->checkValidCharacters($name, $description);

        $entity = null;

        if ($validCharacters["isValid"]) {
            Switch ($type) {
                case 'Project':
                    $projectRepository = $this->getDoctrine()->getRepository(Project::class);
                    $entity = $projectRepository->find($entityId);

                    if (!is_null($entity)) {
                        // TODO check if $this->getUser() has permissions to edit this project, not only the owner
                        if ($entity->getUser()->getUsername() == $this->getUser()) {
                            $entity->setName($name);
                            $entity->setDescription($description);
                        }else{
                            $entity = null;

                            $response->setData(array(
                                'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                                'message' => 'not allowed'  // TODO translate
                            ));
                        }

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
                            if ($project->getUser()->getUsername() == $this->getUser()) {
                                $entity->setName($name);
                                $entity->setDescription($description);
                                $entity->setProjectId($project);
                            }else{
                                $entity = null;

                                $response->setData(array(
                                    'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                                    'message' => 'not allowed'  // TODO translate
                                ));
                            }

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
                        'errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                        'message' => $type . ' saved successfully',  // TODO translate
                        'entityId' => $entity->getId()
                    ));

                } catch (\Doctrine\DBAL\DBALException  $e) {
                    $response->setData(array('errorCode' => $validationFunctions::ERROR_CODES['QUERY_ERROR'],
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


    // create project or task
    public function remove($entityId, $type, ValidationFunctions $validationFunctions,
                           LoggerInterface $logger)
    {

        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();


        $entity = null;

        Switch ($type) {
            case 'Project':
                $projectRepository = $this->getDoctrine()->getRepository(Project::class);
                $entity = $projectRepository->find($entityId);

                if (!is_null($entity)) {
                    // TODO check if $this->getUser() has permissions to edit this project, not only the owner
                    if ($entity->getUser()->getUsername() == $this->getUser()) {
                        $entityManager->remove($entity);
                    }else{
                        $entity = null;

                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                            'message' => 'not allowed'  // TODO translate
                        ));
                    }

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

                        if ($entity->getProject()->getUser()->getUsername() == $this->getUser()) {
                            $entityManager->remove($entity);

                        }else{
                            $entity = null;

                            $response->setData(array(
                                'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                                'message' => 'not allowed'  // TODO translate
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

            try {
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $response->setData(array(
                    'errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                    'message' => $type . ' saved successfully',  // TODO translate
                    'entityId' => $entity->getId()
                ));

            } catch (\Doctrine\DBAL\DBALException  $e) {
                $response->setData(array('errorCode' => $validationFunctions::ERROR_CODES['QUERY_ERROR'],
                    'message' => $type . ' not saved: ' . $e->getMessage()));
                $logger->error('Error saving to database ' . $e->getMessage());
            }
        }


        return $response;

    }



    // create project or task
    public function list($type, $entityId, ValidationFunctions $validationFunctions,
                           LoggerInterface $logger)
    {

        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();


        $entity = null;

        Switch ($type) {
            case 'Project':
                $projectRepository = $this->getDoctrine()->getRepository(Project::class);
                $entity = $projectRepository->find($entityId);

                if (!is_null($entity)) {
                    // TODO check if $this->getUser() has permissions to edit this project, not only the owner
                    if ($entity->getUser()->getUsername() == $this->getUser()) {

                    }else{
                        $entity = null;

                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                            'message' => 'not allowed'  // TODO translate
                        ));
                    }

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

                    if ($entity->getProject()->getUser()->getUsername() == $this->getUser()) {


                    }else{
                        $entity = null;

                        $response->setData(array(
                            'errorCode' => $validationFunctions::ERROR_CODES['NOT_ALLOWED'],
                            'message' => 'not allowed'  // TODO translate
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

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array((new ObjectNormalizer())
                ->setIgnoredAttributes(array( // TODO use entity annotations
                        'password', 'email', 'emailCanonical', 'plainPassword', 'salt',
                        'lastLogin', 'location', 'confirmationToken', 'roles', 'accountNonExpired',
                        'accountNonLocked', 'credentialsNonExpired', 'enabled', 'superAdmin', 'passwordRequestedAt',
                        'groups', 'groupNames')
                ));
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($entity, 'json',array('groups' => array('api')));

            $response->setData(array(
                'entity' => $jsonContent,
                'errorCode' => $validationFunctions::ERROR_CODES['SUCCESS'],
                'message' => $type . ' loaded successfully',  // TODO translate
                'entityId' => $entity->getId()
            ));
        }


        return $response;

    }


}