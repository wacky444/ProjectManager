<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Project;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class SimpleAPIController extends Controller
{
    public function number()
    {
        $number = mt_rand(0, 100);


        $entityManager = $this->getDoctrine()->getManager();

        $project = new Project();
        $project->setName('Keyboard');
        $project->setUserId($this->getUser());

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($project);

        // actually executes the queries (i.e. the INSERT query)
//        $entityManager->flush();

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }

    public function createProject($projectName, $projectDescription)
    {
        $response = new JsonResponse(); // Automatically sets the 'Content-Type'

        $entityManager = $this->getDoctrine()->getManager();

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

        return $response;
    }

    public function addTask($project, $taskName, $taskDescription)
    {

    }

}