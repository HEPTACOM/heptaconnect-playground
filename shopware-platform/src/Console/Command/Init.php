<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\IndexerRegistryInterface;
use Shopware\Core\Framework\Migration\MigrationCollectionLoader;
use Shopware\Core\Framework\Migration\MigrationRuntime;
use Shopware\Core\Framework\Migration\MigrationSource;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Init extends Command
{
    protected static $defaultName = 'playground:init';

    private string $dsn;

    private string $projectDir;

    private TagAwareAdapterInterface $cache;

    private LoggerInterface $logger;

    private IndexerRegistryInterface $indexer;

    private EntityIndexerRegistry $entityIndexerRegistry;

    /** @var array|iterable|\Traversable|MigrationSource[] */
    private array $migrationSources;

    public function __construct(
        string $dsn,
        string $projectDir,
        TagAwareAdapterInterface $cache,
        LoggerInterface $logger,
        IndexerRegistryInterface $indexer,
        EntityIndexerRegistry $entityIndexerRegistry,
        iterable $migrationSources
    ) {
        parent::__construct();
        $this->dsn = $dsn;
        $this->projectDir = $projectDir;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->indexer = $indexer;
        $this->entityIndexerRegistry = $entityIndexerRegistry;
        $this->migrationSources = iterable_to_array($migrationSources);
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = (bool) $input->getOption('force');
        $connection = $this->getDatabaseLessConnection();

        $this->setupDatabase($io, $force, $connection);
        $this->runMigrations($io, new MigrationCollectionLoader(
            $connection,
            new MigrationRuntime($connection, $this->logger),
            $this->migrationSources
        ));
        $this->runIndexers($io);
        $this->cache->clear();

        return 0;
    }

    private function getDatabaseName(): string
    {
        return substr(parse_url($this->dsn)['path'], 1);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getDatabaseLessConnection(): Connection
    {
        $params = parse_url($this->dsn);

        return DriverManager::getConnection([
            'url' => sprintf(
                '%s://%s%s:%s',
                $params['scheme'],
                isset($params['pass'], $params['user']) ? ($params['user'] . ':' . $params['pass'] . '@') : '',
                $params['host'],
                $params['port'] ?? 3306
            ),
            'charset' => 'utf8mb4',
        ], new Configuration());
    }

    private function setupDatabase(SymfonyStyle $io, bool $force, Connection $connection): void
    {
        $dbName = $this->getDatabaseName();
        $io->section('Setup database');

        if ($io->confirm(sprintf('Is it ok to create %s?', $dbName), $force)) {
            $connection->executeUpdate('CREATE DATABASE IF NOT EXISTS `' . $dbName . '` CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`');
            $io->success('Created database `' . $dbName . '`');
        }

        $connection->exec('USE `' . $dbName . '`');

        $tables = $connection->query('SHOW TABLES')->fetchAll(FetchMode::COLUMN);

        if (!in_array('migration', $tables, true)) {
            $io->writeln('Importing base schema.sql');
            $connection->exec(file_get_contents($this->projectDir . '/vendor/shopware/core/schema.sql'));
            $io->success('Importing base schema.sql');
        }
    }

    /**
     * @throws \Throwable
     */
    private function runMigrations(SymfonyStyle $io, MigrationCollectionLoader $loader): void
    {
        $io->section('Run migrations');

        foreach ($loader->collectAll() as $migrationSourceName => $collection) {
            $collection->sync();
            $total = \count($collection->getExecutableMigrations());
            $io->progressStart($total);

            try {
                foreach ($collection->migrateInSteps() as $_return) {
                    $io->progressAdvance();
                }
            } catch (\Throwable $e) {
                $io->progressFinish();
                throw $e;
            }

            $collection->sync();
            $total = \count($collection->getExecutableDestructiveMigrations());
            $io->progressStart($total);

            try {
                foreach ($collection->migrateDestructiveInSteps() as $_return) {
                    $io->progressAdvance();
                }
            } catch (\Throwable $e) {
                $io->progressFinish();
                throw $e;
            }
        }

        $io->success('Successfully run migrations');
    }

    private function runIndexers(SymfonyStyle $io): void
    {
        $io->section('Run indexers');

        $this->indexer->index(new \DateTime());
        $this->entityIndexerRegistry->index(false);

        $io->success('Successfully run indexers');
    }
}
