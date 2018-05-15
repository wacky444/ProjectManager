<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\ExclusionPolicy;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $user_id;


    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="projects")
     */
    private $projects;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}