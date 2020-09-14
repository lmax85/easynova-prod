<?php

declare(strict_types=1);

namespace OCA\Easynova\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000001Date20200825124731 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('custom_properties')) {
            $table = $schema->createTable('custom_properties');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('type', 'string', [
                'notnull' => false,
                'length' => 200,
            ]);
            $table->addColumn('default', 'string', [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);
        }
        return $schema;
    }
}
