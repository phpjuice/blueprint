<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Console\GeneratorCommand;

class BlueprintMigrationCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:migration
                        {name : The name of migration table.}
                        {--schema= : table structure json input.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $indent = '            ';

    /**
     *  Migration column types collection.
     *
     * @var array
     */
    protected $typeLookup = [
        'char' => 'char',
        'date' => 'date',
        'datetime' => 'dateTime',
        'time' => 'time',
        'timestamp' => 'timestamp',
        'text' => 'text',
        'mediumtext' => 'mediumText',
        'longtext' => 'longText',
        'json' => 'json',
        'jsonb' => 'jsonb',
        'binary' => 'binary',
        'number' => 'integer',
        'integer' => 'integer',
        'bigint' => 'bigInteger',
        'mediumint' => 'mediumInteger',
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'boolean' => 'boolean',
        'decimal' => 'decimal',
        'double' => 'double',
        'float' => 'float',
        'enum' => 'enum',
    ];

    /**
     * The schema of the class being generated.
     *
     * @var array
     */
    protected $schema;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../Stubs/migration.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        $datePrefix = date('Y_m_d_His');

        return database_path('/migrations/').$datePrefix.'_create_'.$name.'_table.php';
    }

    /**
     * Build the model class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        // get stub file
        $stub = $this->files->get($this->getStub());
        // get table name
        $tableName = $this->argument('name');
        // generate table name
        $className = $this->generateClassName($tableName);

        // get schema
        $this->schema = $this->option('schema');

        // primary key
        $primaryKey = isset($this->schema['keys']['primary']) ? $this->schema['keys']['primary'] : 'id';

        // build schema output
        $schemaFields = $this->buildFieldsSegment();
        $schemaFields .= $this->buildIndexesSegment();
        $schemaFields .= $this->buildSoftDeletesSegment();

        return $this
            ->replaceTableName($stub, $tableName)
            ->replaceSchemaFields($stub, $schemaFields)
            ->replacePrimaryKey($stub, $primaryKey)
            ->replaceClass($stub, $className);
    }

    /**
     * Replace the tableName for the given stub.
     *
     * @param string $stub
     * @param string $tableName
     *
     * @return $this
     */
    protected function replaceTableName(&$stub, $tableName)
    {
        $stub = str_replace('{{tableName}}', $tableName, $stub);

        return $this;
    }

    /**
     * Replace the primary key for the given stub.
     *
     * @param string $stub
     * @param string $primaryKey
     *
     * @return $this
     */
    protected function replacePrimaryKey(&$stub, $primaryKey)
    {
        $stub = str_replace('{{primaryKey}}', $primaryKey, $stub);

        return $this;
    }

    /**
     * Replace the primary key for the given stub.
     *
     * @param string $stub
     * @param string $schemaFields
     *
     * @return $this
     */
    protected function replaceSchemaFields(&$stub, $schemaFields)
    {
        $stub = str_replace('{{schemaFields}}', $schemaFields, $stub);

        return $this;
    }

    protected function buildIndexesSegment()
    {
        $schema = $this->schema;
        if (isset($schema['keys']) && isset($schema['keys']['indexes'])) {
            // add indexes and unique indexes as necessary
            $uniqueFields = '';
            $indexFields = '';
            $segment = '';
            foreach ($schema['keys']['indexes'] as $index) {
                $field = $index['field'];
                if ('unique' === $index['type']) {
                    $uniqueFields .= "'$field',";
                } else {
                    $indexFields .= "'$field',";
                }
            }
            if ('' != $uniqueFields) {
                $segment .= '$table->unique('.rtrim($uniqueFields, ',').");\n".$this->indent;
            }
            if ('' != $indexFields) {
                $segment .= '$table->index('.rtrim($indexFields, ',').");\n".$this->indent;
            }

            return $segment;
        }

        return '';
    }

    protected function buildFieldsSegment()
    {
        $schema = $this->schema;
        if (isset($schema['keys']) && isset($schema['keys']['indexes'])) {
            $segment = '';
            foreach ($schema['fields'] as $field) {
                // check if present in the lockup table
                if (isset($this->typeLookup[$field['type']])) {
                    $type = $this->typeLookup[$field['type']];

                    if ('select' === $type || 'enum' === $type) {
                        $enumOptions = array_keys(json_decode($field['options'], true));
                        $enumOptionsStr = implode(',', array_map(function ($string) {
                            return '"'.$string.'"';
                        }, $enumOptions));
                        $segment .= '$table->'.$type."('".$field['name']."', [".$enumOptionsStr.'])';
                    } else {
                        $segment .= '$table->'.$type."('".$field['name']."')";
                    }
                } else {
                    $segment .= "\$table->string('".$field['name']."')";
                }
                // Append column modifier
                $modifierLookup = [
                    'comment',
                    'default',
                    'first',
                    'nullable',
                    'unsigned',
                ];
                if (isset($field['modifier']) && in_array(trim($field['modifier']), $modifierLookup)) {
                    $segment .= '->'.trim($field['modifier']).'()';
                }
                $segment .= ";\n".$this->indent;
            }

            return $segment;
        }

        return '';
    }

    protected function buildSoftDeletesSegment()
    {
        $schema = $this->schema;
        if (isset($schema['softDeletes']) && true === $schema['softDeletes']) {
            return "\$table->softDeletes();\n".$this->indent;
        }

        return '';
    }

    protected function generateClassName($tableName)
    {
        return 'Create'.str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))).'Table';
    }
}
