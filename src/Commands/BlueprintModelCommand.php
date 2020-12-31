<?php

namespace PHPJuice\Blueprint\Commands;

class BlueprintModelCommand extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:model
                            {name : The name of the model.}
                            {--blueprint= : blueprint from a json file.}
                            {--force : Overwrite already existing model.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Build the model class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        // get the stub file
        $stub = $this->files->get($this->getStub());

        // replace all placeholder
        $ret = $this->replaceTableName($stub)
            ->replacePrimaryKey($stub)
            ->replaceFillable($stub)
            ->replaceHidden($stub)
            ->replaceSoftDelete($stub)
            ->replaceRelationships($stub);

        // replace dummy namespace and class
        $ret->replaceNamespace($stub, $name);

        return $ret->replaceClass($stub, $name);
    }

    /**
     * Replace the table for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceTableName(&$stub)
    {
        $stub = str_replace('{{tableName}}', $this->getTableName(), $stub);

        return $this;
    }

    /**
     * Replace the fillable for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceFillable(&$stub)
    {
        $fillable = "'".str_replace(',', "','", $this->blueprint->get('model')['fillable'])."'";
        $stub = str_replace('{{fillable}}', $fillable, $stub);

        return $this;
    }

    /**
     * Replace the hidden for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceHidden(&$stub)
    {
        $hidden = "'".str_replace(',', "','", $this->blueprint->get('model')['hidden'])."'";
        $stub = str_replace('{{hidden}}', $hidden, $stub);

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
    protected function replacePrimaryKey(&$stub)
    {
        $key = $this->blueprint->get('table')['schema']['keys']['primary'];
        $stub = str_replace('{{primaryKey}}', $key, $stub);

        return $this;
    }

    /**
     * Replace the (optional) soft deletes part for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceSoftDelete(&$stub)
    {
        if ($this->blueprint->get('model')['softDeletes']) {
            $stub = str_replace('{{softDeletes}}', "use SoftDeletes;\n    ", $stub);
            $stub = str_replace('{{useSoftDeletes}}', "use Illuminate\Database\Eloquent\SoftDeletes;\n", $stub);
        } else {
            $stub = str_replace('{{softDeletes}}', '', $stub);
            $stub = str_replace('{{useSoftDeletes}}', '', $stub);
        }

        return $this;
    }

    /**
     * Replace the (optional) relationships from the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceRelationships(&$stub)
    {
        $relationships = $this->blueprint->get('model')['relationships'];
        $relationshipsCode = '';
        if (count($relationships) > 0) {
            foreach ($relationships as $relation) {
                $args = [
                    $relation['class'],
                    isset($relation['foreignKey']) ? $relation['foreignKey'] : '',
                    isset($relation['localKey']) ? $relation['localKey'] : '',
                ];
                $relationshipsCode .= $this->createRelationshipFunction($relation['name'], $relation['type'], $args);
            }
            // do relationship stuff here
            $stub = str_replace('{{relationships}}', $relationshipsCode."\n", $stub);
        }
        $stub = str_replace('{{relationships}}', '', $stub);

        return $this;
    }

    /**
     * Create the code for a model relationship.
     *
     * @param string $stub
     * @param string $relationshipName the name of the function, e.g. owners
     * @param string $relationshipType the type of the relationship, hasOne, hasMany, belongsTo etc
     * @param array  $relationshipArgs args for the relationship function
     *
     * @return string
     */
    protected function createRelationshipFunction($relationshipName, $relationshipType, $relationshipArgs)
    {
        $commentStr = "/**\n    * {$relationshipType} relationship.\n    */\n";
        $code = $commentStr."    public function %s()\n    {\n        return \$this->%s(%s);\n    }";
        $argsStr = implode(',', $relationshipArgs);
        $argsStr = rtrim($argsStr, ',');
        $argsStr = "'".str_replace(',', "','", $argsStr)."'";

        return sprintf($code, $relationshipName, $relationshipType, $argsStr);
    }
}
