<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jett\JSONEntitySerializerBundle\Tests\Consts;

/**
 * @ORM\Entity()
 */
class EntityOne
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public static function get()
    {
        $o = new static();
        $o->setId(Consts::ID);
        $o->setTitle(Consts::TITLE);

        return $o;
    }
}
