<?php

declare(strict_types=1);

namespace OCA\Easynova\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000003Date20200925134731 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('files_easynova')) {
            $table = $schema->createTable('files_easynova');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('file_id', 'integer', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('file_name', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('ip', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('user_agent', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('readed_at', 'datetime', [
                'notnull' => false,
            ]);
            $table->addColumn('created_at', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('deleted_at', 'datetime', [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);
        }
        return $schema;
    }
}
