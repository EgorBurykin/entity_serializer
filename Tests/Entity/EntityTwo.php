<?php

namespace Jett\JSONEntitySerializerBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jett\JSONEntitySerializerBundle\Tests\Consts;

/**
 * @ORM\Entity()
 */
class EntityTwo extends EntityOne
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $datetime;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    public static function get()
    {
        $o = parent::get();
        $o->setDate(Consts::date());
        $o->setDatetime(Consts::date());

        return $o;
    }
}
