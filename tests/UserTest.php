<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 */
class UserTest extends KernelTestCase
{
/** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /** @test */
    public function a_user_record_can_be_created_in_database()
    {
        // Set up
        $user = new User();
        $user->setName('user1');

        $this->entityManager->persist($user);

        // Do something
        $this->entityManager->flush();

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRecord = $userRepository->findOneBy(['name'=> 'user1']);

        //Make assertion
        $this->assertEquals('user1', $userRecord->getName());

    }

}