<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

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
        DatabasePrimer::prime($kernel);
        DatabasePrimer::truncateAll($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    /** @test */
    public function createUserTest_givenEmailAndPassword_UserSuccessfulCreatedInDataBase()
    {
        // Set up
        $user = new User();
        $user->setEmail('email@domain.com');
        $user->setPassword('123');

        $this->entityManager->persist($user);

        // Do something
        $this->entityManager->flush();

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRecord = $userRepository->findOneBy(['email'=> 'email@domain.com']);

        //Make assertion
        $this->assertEquals('email@domain.com', $userRecord->getEmail());

    }

}