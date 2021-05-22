<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\ShopwarePlatform\Console\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\FetchMode;
use Shopware\Core\Framework\Migration\MigrationSource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Init extends Command
{
    private const BLOCKED_MIGRATION_SOURCES = [
        'core.',
        'null',
        'Framework',
        'Storefront',
    ];

    protected static $defaultName = 'playground:init';

    private string $dsn;

    private string $projectDir;

    /** @var array|iterable|\Traversable|MigrationSource[] */
    private array $migrationSources;

    public function __construct(
        string $dsn,
        string $projectDir,
        iterable $migrationSources
    ) {
        parent::__construct();
        $this->dsn = $dsn;
        $this->projectDir = $projectDir;
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
        $output->writeln('');

        $commands = [];

        foreach ($this->migrationSources as $migrationSource) {
            foreach (self::BLOCKED_MIGRATION_SOURCES as $blockedMigrationSource) {
                if (strpos($migrationSource->getName(), $blockedMigrationSource) === 0) {
                    continue 2;
                }
            }

            array_push($commands, ...[
                [
                    'command' => 'database:migrate',
                    'identifier' => $migrationSource->getName(),
                    '--all' => true,
                ],
                [
                    'command' => 'database:migrate-destructive',
                    'identifier' => $migrationSource->getName(),
                    '--all' => true,
                ]
            ]);
        }

        array_push($commands, ...[
            [
                'command' => 'dal:refresh:index',
            ],
            [
                'command' => 'user:create',
                'allowedToFail' => true,
                'username' => 'admin',
                '--admin' => true,
                '--password' => 'shopware',
            ],
            [
                'command' => 'assets:install',
            ],
            [
                'command' => 'cache:clear',
            ],
        ]);

        $this->runCommands($commands, $output);

        if (!file_exists($this->projectDir . '/public/.htaccess')
            && file_exists($this->projectDir . '/public/.htaccess.dist')
        ) {
            copy($this->projectDir . '/public/.htaccess.dist', $this->projectDir . '/public/.htaccess');
        }

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

        $tables = $connection->executeQuery('SHOW TABLES')->fetchAll(FetchMode::COLUMN);

        if (!in_array('migration', $tables, true)) {
            $io->writeln('Importing base schema.sql');
            $connection->exec(file_get_contents($this->projectDir . '/vendor/shopware/core/schema.sql'));
            $io->success('Importing base schema.sql');
        }
    }

    private function runCommands(array $commands, OutputInterface $output): int
    {
        $application = $this->getApplication();
        if ($application === null) {
            throw new \RuntimeException('No application initialised');
        }

        foreach ($commands as $parameters) {
            $output->writeln('');

            $command = $application->find((string) $parameters['command']);
            $allowedToFail = $parameters['allowedToFail'] ?? false;
            unset($parameters['command'], $parameters['allowedToFail']);

            try {
                $returnCode = $command->run(new ArrayInput($parameters), $output);
                if ($returnCode !== 0 && !$allowedToFail) {
                    return $returnCode;
                }
            } catch (\Throwable $e) {
                if (!$allowedToFail) {
                    throw $e;
                }
            }
        }

        return 0;
    }
}
