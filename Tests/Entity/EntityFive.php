<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class EntityFive extends EntityOne
{
    /**
     * @ORM\ManyToOne(targetEntity="Jett\JSONEntitySerializerBundle\Tests\Entity\EntitySix", inversedBy="entities")
     */
    protected $entity;

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    public static function get($link = true)
    {
        $o = parent::get();
        if ($link) {
            $o->setEntity(EntitySix::get());
        }

        return $o;
    }
}
