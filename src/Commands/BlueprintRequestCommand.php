<?php

namespace PHPJuice\Blueprint\Commands;

class BlueprintRequestCommand extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:request
                            {name : The name of request.}
                            {--blueprint= : blueprint from a json file.}
                            {--force : Overwrite already existing request.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new request.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

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
                    ->replaceValidations($stub)
                    ->replaceClass($stub, $name.$this->type);
    }

    /**
     * Replace the validation for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceValidations(&$stub)
    {
        $validations = $this->blueprint->get('controller')['validations'];
        $validationsStr = '';
        foreach ($validations as $validation) {
            $validationsStr .= sprintf("\n          '%s' => '%s',", $validation['field'], $validation['rules']);
        }
        $stub = str_replace('{{validations}}', $validationsStr, $stub);

        return $this;
    }
}
