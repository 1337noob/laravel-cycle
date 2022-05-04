<?php

return [

    'entities_locations' => [
        app_path('Models'),
    ],
    'generators' => [

        Cycle\Schema\Generator\ResetTables::class,             // re-declared table schemas (remove columns)
        Cycle\Annotated\Embeddings::class,                     // register embeddable entities
        Cycle\Annotated\Entities::class,                       // register annotated entities
        Cycle\Annotated\TableInheritance::class,               // register STI/JTI
        Cycle\Annotated\MergeColumns::class,                   // add @Table column declarations
        Cycle\Schema\Generator\GenerateRelations::class,       // generate entity relations
        Cycle\Schema\Generator\GenerateModifiers::class,       // generate changes from schema modifiers
        Cycle\Schema\Generator\ValidateEntities::class,        // make sure all entity schemas are correct
        Cycle\Schema\Generator\RenderTables::class,            // declare table schemas
        Cycle\Schema\Generator\RenderRelations::class,         // declare relation keys and indexes
        Cycle\Schema\Generator\RenderModifiers::class,         // render all schema modifiers
        // Cycle\Schema\Generator\SyncTables::class,              // sync table changes to database
        Cycle\Annotated\MergeIndexes::class,                   // add @Table column declarations
        Cycle\Schema\Generator\Migrations\GenerateMigrations::class,  // generate migrations
        Cycle\Schema\Generator\GenerateTypecast::class,        // typecast non string columns

    ],
    'migrations_directory' => database_path('migrations')
];
