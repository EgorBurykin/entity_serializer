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
class Department
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Employee", mappedBy="department", cascade="all", orphanRemoval=true)
     */
    private $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    public function addEmployee(Employee $block)
    {
        $this->employees->add($block);

        return $this;
    }

    public function removeEmployee(Employee $block)
    {
        $this->employees->removeElement($block);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
