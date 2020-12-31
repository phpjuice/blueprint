<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class Generator extends GeneratorCommand
{
    /**
     * The blueprint of class being generated.
     *
     * @var object
     */
    protected $blueprint;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->option('blueprint')) {
            $this->error('must provide a blueprint for this generator to work!');

            return 0;
        }
        $this->blueprint = $this->option('blueprint');
        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $name = Str::snake($this->type, '-').'.stub';

        return config('blueprint.custom_template')
        ? config('blueprint.path').$name
        : __DIR__.'/../Stubs/'.$name;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        switch ($this->type) {
            case 'Model':
                $rootNamespace .= '\\Models\\';
                break;
            case 'Request':
                $rootNamespace .= '\\Http\\Requests\\';
                break;
            case 'Resource':
                $rootNamespace .= '\\Http\\Resources\\';
                break;
            case 'ApiController':
                $rootNamespace .= '\\Http\\Controllers\\API\\';
                break;
            case 'Controller':
                $rootNamespace .= '\\Http\\Controllers\\';
                break;
            case 'Test':
                $rootNamespace .= '\\Feature\\';
                break;
            default:
                break;
        }

        return $rootNamespace.$this->getCrudNamespace();
    }

    /**
     * Determine if the class already exists.
     *
     * @param string $rawName
     *
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        if ($this->option('force')) {
            return false;
        }

        return parent::alreadyExists($rawName);
    }

    /**
     * Gets the crud namespace.
     *
     * @return string
     */
    protected function getCrudNamespace()
    {
        return $this->blueprint->get('crud')['namespace'];
    }

    /**
     * Gets the crud name.
     *
     * @return string
     */
    protected function getCrudName()
    {
        return $this->blueprint->get('crud')['name'];
    }

    /**
     * Gets the model name.
     *
     * @return string
     */
    protected function getModelName()
    {
        return $this->blueprint->get('model')['name'];
    }

    /**
     * Gets the controller name.
     *
     * @return string
     */
    protected function getControllerName()
    {
        return $this->blueprint->get('controller')['name'];
    }

    /**
     * Gets the table name.
     *
     * @return string
     */
    protected function getTableName()
    {
        return $this->blueprint->get('table')['name'];
    }

    /**
     * Gets the route name.
     *
     * @return string
     */
    protected function getRouteName()
    {
        return $this->blueprint->get('route')['name'];
    }

    /**
     * Gets the route url.
     *
     * @return string
     */
    protected function getRouteUrl()
    {
        return $this->blueprint->get('route')['url'];
    }

    /**
     * Is a restful api crud.
     *
     * @return bool
     */
    protected function isApi()
    {
        return $this->blueprint->get('crud')['isApi'];
    }
}
