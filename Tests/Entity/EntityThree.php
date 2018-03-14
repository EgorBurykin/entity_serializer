<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jett\JSONEntitySerializerBundle\Tests\Consts;

/**
 * @ORM\Entity()
 */
class EntityThree extends EntityTwo
{
    /**
     * @ORM\Column(type="object")
     */
    protected $object;

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    public static function get()
    {
        $o = parent::get();
        $o->setObject((object) ['id' => Consts::ID, 'title' => Consts::TITLE]);

        return $o;
    }
}
