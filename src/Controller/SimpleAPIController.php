<?php
namespace App\Controller;

use App\Entity\Project;
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

        $newEntity = new Entity();

        if ($validCharacters["isValid"]) {
            Switch($type){
                case 'Project':
                    $newEntity = new Project($name, $this->getUser(), $description);


                    break;
                case 'Task':


                    break;
                default:

                    break;
            }

            // tell Doctrine to (eventually) save the object (no queries yet)
            $entityManager->persist($newEntity);

            try {
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $response->setData(array(
                    'errorCode' => 0,
                    'message' => $type . ' saved successfully',  // TODO translate
                    'entityId' => $newEntity->getId()
                ));

            } catch (\Doctrine\DBAL\DBALException  $e) {
                $response->setData(array('errorCode' => 1, 'message' => $type . ' not saved: ' . $e->getMessage()));
                $logger->error('Error saving to database ' . $e->getMessage());
            }

        } else {
            $response->setData(array(
                'errorCode' => 2,
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