<?php

namespace App\Tests;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Database Primer
 */
class DatabasePrimer
{
    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return void
     */
    public static function prime(KernelInterface $kernel)
    {
        // Make sure we are in the dev environment
        if ('dev' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the dev environment');
        }

        // Get the entity manager from the service container
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        // If you are using the Doctrine Fixtures Bundle you could load these here
    }

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return void
     */
    public static function truncateAll(KernelInterface $kernel)
    {
        // Make sure we are in the dev environment
        if ('dev' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the dev environment');
        }



        // Get the entity and schema manager from the service container
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $schemaManager = $kernel->getContainer()
            ->get('doctrine.dbal.default_connection')
            ->getSchemaManager();

        // Get all tables
        $tables = $schemaManager->listTableNames();

        // Get more required stuff
        $connection = $entityManager->getConnection();
        $platform   = $connection->getDatabasePlatform();

        // Truncate all tables
        foreach ($tables as $table) {
            $connection->executeUpdate($platform->getTruncateTableSQL($table, true /* whether to cascade */));
        }
    }
}