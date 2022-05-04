<?php

namespace L33tnoob\Console\Commands;

use Cycle\Migrations\Migrator;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use Illuminate\Console\Command;

class SchemaDiff extends Command
{
    protected $signature = 'cycle:schema:diff';

    protected $description = 'Generate migration based on schema diff';

    public function handle(GenerateMigrations $generator, Migrator $migrator, Registry $registry, SchemaInterface $schema)
    {
        $migrator->configure();
        $generator->run($registry);
        print_r($migrator->getMigrations());
        // TODO add messages
    }
}
