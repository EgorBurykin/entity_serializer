<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Programmer extends Employee
{
    /**
     * @ORM\Column(type="string")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     */
    private $team;

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     * @return self
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }


}