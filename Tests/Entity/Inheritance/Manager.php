<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Manager extends Employee
{
    /**
     * @ORM\ManyToMany(targetEntity="Project")
     */
    private $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getProjects()
    {
        return $this->projects;
    }

    public function addProject(Project $project)
    {
        $this->projects->add($project);

        return $this;
    }

    public function removeProject(Project $project)
    {
        $this->projects->removeElement($project);
    }
}
