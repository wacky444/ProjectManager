<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Project;
use Psr\Log\LoggerInterface;

use App\Utils\ValidationFunctions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class SimpleAPIController extends Controller
{

    public function createProject($projectName, $projectDescription, ValidationFunctions $validationFunctions,
                                  LoggerInterface $logger)
    {
        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

        //Check that the input has only valid characters
        $validCharacters = $validationFunctions->checkValidCharacters($logger, $projectName, $projectDescription);
        $logger->error('An error occurred');
        if($validCharacters["isValid"]){
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
            }
        }else{
            $invalidStrings = $validCharacters["invalidStrings"];

            $response->setData(array(
                'errorCode' => 2,
                'message' => 'Project not saved: '
                    . implode(",", $invalidStrings) . " contains invalid characters"));
        };


        return $response;
    }

    public function addTask($project, $taskName, $taskDescription)
    {

    }

}