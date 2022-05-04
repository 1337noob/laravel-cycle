<?php

namespace L33tnoob;

use Cycle\Database\Config\DriverConfig;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema as ORMSchema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema;
use Cycle\Schema\Registry;
use Exception;
use Illuminate\Support\ServiceProvider;
use Cycle\Database;
use Cycle\Database\Config;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;


class CycleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../cfg/cycle.php', 'cycle'
        );

        $this->app->singleton(Config\DatabaseConfig::class, function (){
            $dbconfig = $this->app['config']['database'];

            $dbalconfig = [
                'default' => $dbconfig['default'],
                'databases' => [],
                'connections' => [],
            ];

            foreach ($dbconfig['connections'] as $name => $connection) {
                $dbalconfig['databases'][$name] = ['connection' => $name];
                $dbalconfig['connections'][$name] = $this->configFactory($connection);
            }

            return new Config\DatabaseConfig($dbalconfig);
        });

        $this->app->singleton(ClassesInterface::class, function (){
            return new ClassLocator((new Finder())->files()->in(
                $this->app['config']['cycle']['entities_locations']
            ));
        });

        $this->app->singleton(SchemaInterface::class, function () {
            return new ORMSchema($this->app->make(Schema\Compiler::class)->compile(
                $this->app->make(Registry::class),
                array_map(
                    fn ($class) => $this->app->make($class),
                    $this->app['config']['cycle.generators']
                )
            ));
        });

        $this->app->singleton(MigrationConfig::class, function () {
            return new MigrationConfig([
                'directory' => $this->app['config']['cycle.migrations_directory'],  // where to store migrations
                'table' => $this->app['config']['database.migrations']                      // database table to store migration status
            ]);
        });

        $this->app->singleton(Schema\Generator\Migrations\GenerateMigrations::class);

        $this->app->singleton(RepositoryInterface::class, FileRepository::class);

        $this->app->singleton(Migrator::class);

        $this->app->singleton(Database\DatabaseProviderInterface::class, Database\DatabaseManager::class);

        $this->app->singleton(Registry::class);

        $this->app->singleton(Schema\Compiler::class);

        $this->app->singleton(FactoryInterface::class, Factory::class);

        $this->app->singleton(ORMInterface::class, ORM::class);

        $this->app->singleton(EntityManager::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../cfg/cycle.php' => config_path('cycle.php'),
        ]);
    }


    public function configFactory(array $config): DriverConfig
    {
        $factories = [
            'pgsql' => function (array $config): Config\PostgresDriverConfig {
                return new Config\PostgresDriverConfig(
                    connection: new Config\Postgres\DsnConnectionConfig(
                        "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']}",
                        $config['username'],
                        $config['password'],
                    ),
                );
            },
            'mysql' => function (array $config): Config\MySQLDriverConfig {
                throw new Exception('not implemented');
            },
            'sqlite' => function (array $config): Config\SQLiteDriverConfig {
                throw new Exception('not implemented');
            },
            'sqlsrv' => function (array $config): Config\SQLServerDriverConfig {
                throw new Exception('not implemented');
            },
        ];

        return $factories[$config['driver']]($config);
    }
}
