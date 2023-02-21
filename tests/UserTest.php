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


    const TEST_USER_EMAIL = 'email@domain.com';
    const TEST_USER_PASSWORD = '123' ;

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
    public function createUserTest_givenEmailAndPassword_UserSuccessfulCreatedInDataBase()
    {
        // Set up
        $user = new User();
        $user->setEmail(self::TEST_USER_EMAIL);
        $user->setPassword(self::TEST_USER_PASSWORD);

        $this->entityManager->persist($user);

        // Do something
        $this->entityManager->flush();

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRecord = $userRepository->findOneBy(['email'=> $this::TEST_USER_EMAIL]);

        //Make assertion
        $this->assertEquals('email@domain.com', $userRecord->getEmail());

    }

}