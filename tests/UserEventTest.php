<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserEventTest extends KernelTestCase
{

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        DatabasePrimer::truncateAll($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }
    
    /** @test  */
    public function createEventTest_givenUserAndTime_EventSuccessfulCreatedInDataBase()
    {
        //Set up
        $user = new User();
        $time = date("Y-m-d H:i:s");

        $event = new Event();
        $event->setUser($user);
        $event->setTime($time);

        //Act
        $this->entityManager->flush();
        $eventRepository = $this->entityManager->getRepository(Event::class);
        $eventRecord = $eventRepository->findOneBy(['user'=> $user->getId(),
            'time'=>$time]);

        //Assert
        $this->assertEquals($time, $eventRecord->getTime());
        
    }

}