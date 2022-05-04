<?php

namespace L33tnoob\Console\Commands;

use Cycle\Database\DatabaseManager;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Migrator;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Registry;
use Illuminate\Console\Command;

class SchemaMigrate extends Command
{
    protected $signature = 'cycle:schema:migrate';

    protected $description = 'Migrate';

    public function handle(Migrator $migrator, Capsule $capsule)
    {
        $migrator->configure();
        $migrator->run($capsule);
        print_r($migrator->getMigrations());
        // TODO add messages
    }
}
