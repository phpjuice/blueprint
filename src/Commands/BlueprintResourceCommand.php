<?php

namespace PHPJuice\Blueprint\Commands;

class BlueprintResourceCommand extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:resource
                            {name : The name of resource.}
                            {--blueprint= : blueprint from a json file.}
                            {--force : Overwrite already existing resource.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';

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
        $name = str_replace('\\', DIRECTORY_SEPARATOR, $name);

        return app_path().DIRECTORY_SEPARATOR.$name.$this->type.'.php';
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
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
                    ->replaceFields($stub)
                    ->replaceClass($stub, $name.$this->type);
    }

    /**
     * Replace the fields for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceFields(&$stub)
    {
        $fields = explode(',', $this->blueprint->get('model')['fillable']);
        $fieldsStr = '';
        foreach ($fields as $field) {
            $fieldsStr .= sprintf("'%s' => \$this->%s,\n      ", $field, $field);
        }
        $stub = str_replace('{{fields}}', $fieldsStr, $stub);

        return $this;
    }
}
