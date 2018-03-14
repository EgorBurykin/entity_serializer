<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class EntitySix extends EntityOne
{
    /**
     * @ORM\OneToMany(targetEntity="Jett\JSONEntitySerializerBundle\Tests\Entity\EntityFive", mappedBy="entity")
     */
    protected $entities1;
    /**
     * @ORM\ManyToMany(targetEntity="Jett\JSONEntitySerializerBundle\Tests\Entity\EntityOne")
     */
    protected $entities2;

    public function __construct()
    {
        $this->entities1 = new ArrayCollection();
        $this->entities2 = new ArrayCollection();
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function addEntity1($entity)
    {
        return $this->entities1->add($entity);
    }

    public function getEntities1s()
    {
        return $this->entities1;
    }

    public function addEntity2($entity)
    {
        return $this->entities2->add($entity);
    }

    public function getEntities2s()
    {
        return $this->entities2;
    }

    public static function get()
    {
        $o = parent::get();
        $o->addEntity1(EntityFive::get(false));
        $o->addEntity1(EntityFive::get(false));
        $o->addEntity2(EntityOne::get());
        $o->addEntity2(EntityOne::get());

        return $o;
    }
}
