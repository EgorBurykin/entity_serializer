<?php

/**
 * Copyright 2017, Egor Burykin <c5.Jett@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jett\JSONEntitySerializerBundle\Tests\Service\Serializer;


use Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance\Department;
use Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance\Manager;
use Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance\Programmer;
use Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance\Project;
use Jett\JSONEntitySerializerBundle\Tests\Entity\Inheritance\Team;
use Jett\JSONEntitySerializerBundle\Tests\SerializerTestCase;


class InheritanceTest extends SerializerTestCase
{

    public function testSimpleObjectSerialization()
    {
        $team = new Team();
        $team->setTitle('Team');

        $project = new Project();
        $project->setTitle('Project');

        $department = new Department();
        $department->setTitle('Department');

        $programmer = new Programmer();
        $programmer->setLevel('Senior')->setTeam($team)->setName('Programmer')->setDepartment($department);

        $manager = new Manager();
        $manager->addProject($project)->setDepartment($department)->setName('Manager');

        $department->addEmployee($programmer);
        $department->addEmployee($manager);

        $json = $this->serializer->serialize($department);
        $expected = '
            {
                "title": "Department",
                "employees": [
                    {
                        "name": "Programmer",
                        "level": "Senior",
                        "team": "Team"
                    },
                    {
                        "name": "Manager",
                        "projects": ["Project"]
                    }
                ]
            }
        ';
        $this->assertJsonStringEqualsJsonString($expected, $json);
        $json = $this->serializer->serialize($department, 'merged');
        $this->assertJsonStringEqualsJsonString($expected, $json);
        $team->setId(1);
        $project->setId(1);
        $expected = '
            {
                "title": "Department",
                "employees": [
                    {
                        "name": "Programmer",
                        "level": "Senior",
                        "team": "1"
                    },
                    {
                        "name": "Manager",
                        "projects": ["1"]
                    }
                ]
            }
        ';
        $json = $this->serializer->serialize($department, 'simple');
        $this->assertJsonStringEqualsJsonString($expected, $json);
    }
}