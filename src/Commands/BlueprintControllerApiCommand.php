<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Support\Str;

class BlueprintControllerApiCommand extends Generator
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:controller:api
                            {name : The name of the controller.}
                            {--blueprint= : blueprint from a json file.}
                            {--force : Overwrite already existing controller.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new api controller.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ApiController';

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
            ->replaceModelName($stub)
            ->replaceModelNameSingular($stub)
            ->replaceModelNamespace($stub)
            ->replaceModelNamespaceSegments($stub)
            ->replacePaginationNumber($stub)
            ->replaceClass($stub, $name);
    }

    /**
     * Replace the modelName for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceModelName(&$stub)
    {
        $stub = str_replace('{{modelName}}', $this->getModelName(), $stub);

        return $this;
    }

    /**
     * Replace the modelNameSingular for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceModelNameSingular(&$stub)
    {
        $modelNameSingular = Str::snake(Str::singular($this->getModelName()));
        $stub = str_replace('{{modelNameSingular}}', $modelNameSingular, $stub);

        return $this;
    }

    /**
     * Replace the modelNamespace for the given stub.
     *
     * @param string $stub
     *
     * @return $this
     */
    protected function replaceModelNamespace(&$stub)
    {
        $stub = str_replace('{{modelNamespace}}', $this->getCrudNamespace(), $stub);

        return $this;
    }

    /**
     * Replace the modelNamespace segments for the given stub.
     *
     * @param $stub
     *
     * @return $this
     */
    protected function replaceModelNamespaceSegments(&$stub)
    {
        $modelNamespace = $this->getCrudNamespace();
        $modelSegments = explode('\\', $modelNamespace);
        foreach ($modelSegments as $key => $segment) {
            $stub = str_replace('{{modelNamespace['.$key.']}}', $segment, $stub);
        }
        $stub = preg_replace('{{modelNamespace\[\d*\]}}', '', $stub);

        return $this;
    }

    /**
     * Replace the pagination placeholder for the given stub.
     *
     * @param $stub
     * @param $perPage
     *
     * @return $this
     */
    protected function replacePaginationNumber(&$stub)
    {
        $perPage = intval($this->blueprint->get('controller')['pagination']);
        $stub = str_replace('{{pagination}}', $perPage, $stub);

        return $this;
    }
}
