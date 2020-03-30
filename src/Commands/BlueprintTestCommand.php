<?php

namespace PHPJuice\Blueprint\Commands;

class BlueprintTestCommand extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:test
                            {name : The name of the test.}
                            {--blueprint= : blueprint from a json file.}
                            {--force : Overwrite already existing test class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

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

        return base_path('tests/').str_replace('\\', DIRECTORY_SEPARATOR, $name).$this->type.'.php';
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
        // get the stub file
        $stub = $this->files->get($this->getStub());

        // replace all placeholder
        $this->replaceRouteUrl($stub);
        $this->replacePrimaryKey($stub);
        $this->replaceSavePayload($stub);
        $this->replaceUpdatePayload($stub);

        // replace dummy namespace and class
        $namespace = str_replace($this->laravel->getNamespace(), '\\Tests\\', $name);
        $this->replaceNamespace($stub, $namespace);

        return $this->replaceClass($stub, $name.$this->type);
    }

    /**
     * Replace the table for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceRouteUrl(&$stub)
    {
        $url = strtolower($this->getCrudNamespace()).'/'.$this->getRouteUrl();
        $stub = str_replace('{{route.url}}', $url, $stub);

        return $this;
    }

    /**
     * Replace the payload.save for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceSavePayload(&$stub)
    {
        $fields = explode(',', $this->blueprint->get('model')['fillable']);
        $fieldsStr = '';
        foreach ($fields as $field) {
            $fieldsStr .= sprintf("\n      '%s' => 'test save',", $field);
        }
        $fieldsStr = rtrim($fieldsStr, ',')."\n\t";
        $stub = str_replace('{{payload.save}}', $fieldsStr, $stub);

        return $this;
    }

    /**
     * Replace the payload.update for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceUpdatePayload(&$stub)
    {
        $fields = explode(',', $this->blueprint->get('model')['fillable']);
        $fieldsStr = '';
        foreach ($fields as $field) {
            $fieldsStr .= sprintf("\n      '%s' => 'test update',", $field);
        }
        $fieldsStr = rtrim($fieldsStr, ',')."\n\t";
        $stub = str_replace('{{payload.update}}', $fieldsStr, $stub);

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
}
