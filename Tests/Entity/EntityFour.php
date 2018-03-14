<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jett\JSONEntitySerializerBundle\Tests\Consts;

/**
 * @ORM\Entity()
 */
class EntityFour extends EntityThree
{
    /**
     * @ORM\Column(type="array")
     */
    protected $array1;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $array2;

    /**
     * @ORM\Column(type="simple_array")
     */
    protected $array3;

    /**
     * @return mixed
     */
    public function getArray1()
    {
        return $this->array1;
    }

    /**
     * @param mixed $array1
     */
    public function setArray1($array1)
    {
        $this->array1 = $array1;
    }

    /**
     * @return mixed
     */
    public function getArray2()
    {
        return $this->array2;
    }

    /**
     * @param mixed $array2
     */
    public function setArray2($array2)
    {
        $this->array2 = $array2;
    }

    /**
     * @return mixed
     */
    public function getArray3()
    {
        return $this->array3;
    }

    /**
     * @param mixed $array3
     */
    public function setArray3($array3)
    {
        $this->array3 = $array3;
    }

    public static function get()
    {
        $o = parent::get();
        $array = ['id' => Consts::ID, 'title' => Consts::TITLE];
        $o->setArray1($array);
        $o->setArray2($array);
        $o->setArray3($array);

        return $o;
    }
}
