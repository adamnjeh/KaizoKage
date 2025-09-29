<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadTodos($manager);
        $this->loadTags($manager);
    }
    
    private function loadTodos(ObjectManager $manager)
    {
        foreach ($this->getTodosData() as [$title, $completed]) {
            $todo = new Todo();
            $todo->setTitle($title);
            $todo->setCompleted($completed);
            $manager->persist($todo);
        }
        $manager->flush();
    }
    
    private function loadTags(ObjectManager $manager)
    {
        foreach ($this->getTagsData() as [$name]) {
            $todo = new Tag();
            $todo->setName($name);
            $manager->persist($todo);
        }
        $manager->flush();
    }
    
    private function getTagsData()
    {
        // tag = [name];
        yield ['important'];
        yield ['facile'];
        yield ['urgent'];
        yield ['seum'];
    }
    
    
    private function getTodosData()
    {
        // todo = [title, completed];
        yield ['apprendre les bases de PHP', true];
        yield ['devenir un pro du Web', false];
        yield ['monter une startup',  false];
        yield ['devenir maître du monde', false];
        
    }
    
    
}