<?php

namespace App\Tests;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

/**
 *
 */
class UserEventTest extends KernelTestCase
{

    const TEST_USER_EMAIL = 'email@domain.com';
    const TEST_USER_PASSWORD = '123' ;

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

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /** @test */
    public function createEventTest_givenUserAndDate_EventSuccessfulCreatedInDataBase()
    {
        //Arrange
        $user = new User();
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPassword(self::TEST_USER_PASSWORD);
        $this->entityManager->persist($user);
        $date = new \DateTime('now');
        $event = new Event();
        $event->setUserEntity($user);
        $event->setDate($date);
        $this->entityManager->persist($event);
        $user->addEvent($event);
        $this->entityManager->persist($user);

        //Act
        $this->entityManager->flush();
        $userId = $user->getId();
        $eventRepository = $this->entityManager->getRepository(Event::class);
        $eventRecord = $eventRepository->findOneBy(['userEntity'=> $userId, 'date'=>$date]);

        //Assert
        $this->assertEquals($date, $eventRecord->getDate());

    }


}