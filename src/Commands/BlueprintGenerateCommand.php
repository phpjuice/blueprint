<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class BlueprintGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:generate
                            {name : The name of the crud.}
                            {--P|precise}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a full crud including controller, model, views & migrations.';

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
        if ($blueprint = $this->alreadyExists()) {
            $this->blueprint = collect($blueprint);

            // generate all the scaffolding
            $this->createTest();
            $this->createModel();
            $this->createRequest();
            $this->createResource();
            $this->createMigration();
            $this->createController();

            $this->createRoute();

            // For optimizing the class loader
            if (App::VERSION() < '5.6') {
                $this->callSilent('optimize');
            }

            return 0;
        }

        if ($this->confirm('This crud doesn\'t exists yet!, Do you wish to create it?')) {
            $this->call('blueprint:make', ['name' => $this->argument('name')]);
        }

        return 0;
    }

    /**
     * creates a migration file.
     *
     * @return $this
     */
    protected function createMigration()
    {
        $this->call('blueprint:migration', [
            'name' => $this->blueprint->get('table')['name'],
            '--schema' => $this->blueprint->get('table')['schema'],
        ]);
        $foreignKeys = $this->blueprint->get('table')['schema']['keys']['foreign'];
        if (count($foreignKeys) > 0) {
            $this->call('blueprint:migration:fk', [
                'name' => $this->blueprint->get('table')['name'],
                '--keys' => $foreignKeys,
            ]);
        }

        return $this;
    }

    /**
     * creates a Model.
     *
     * @return $this
     */
    protected function createModel()
    {
        $args = [
            'name' => $this->blueprint->get('model')['name'],
            '--blueprint' => $this->blueprint,
            '--force' => true,
        ];
        $this->call('blueprint:model', $args);

        return $this;
    }

    /**
     * creates a test case.
     *
     * @return $this
     */
    protected function createTest()
    {
        $args = [
            'name' => $this->blueprint->get('model')['name'],
            '--blueprint' => $this->blueprint,
            '--force' => true,
        ];
        $this->call('blueprint:test', $args);

        return $this;
    }

    /**
     * creates a Controller.
     *
     * @return $this
     */
    protected function createController()
    {
        $args = [
            'name' => $this->blueprint->get('controller')['name'],
            '--blueprint' => $this->blueprint,
            '--force' => true,
        ];
        if ((bool) $this->blueprint->get('crud')['isApi']) {
            // creates either an api or default controller
            $this->call('blueprint:controller:api', $args);

            return $this;
        }
        //$this->call('blueprint:controller', $args);
        return $this;
    }

    /**
     * creates a resource.
     *
     * @return $this
     */
    protected function createResource()
    {
        $args = [
            'name' => $this->blueprint->get('model')['name'],
            '--blueprint' => $this->blueprint,
            '--force' => true,
        ];
        $this->call('blueprint:resource', $args);

        return $this;
    }

    /**
     * creates a request.
     *
     * @return $this
     */
    protected function createRequest()
    {
        $args = [
            'name' => $this->blueprint->get('model')['name'],
            '--blueprint' => $this->blueprint,
            '--force' => true,
        ];
        $this->call('blueprint:request', $args);

        return $this;
    }

    /**
     * creates a route.
     *
     * @return $this
     */
    protected function createRoute()
    {
        // Updating the Http/routes.php file
        $routeFile = app_path('Http/routes.php');

        if (App::VERSION() >= '5.3') {
            $routeFile = base_path('routes/web.php');
        }

        if (file_exists($routeFile) && isset($this->blueprint->get('route')['name'])) {
            $isAdded = File::append($routeFile, "\n".implode("\n", $this->addAPIRoute()));

            if ($isAdded) {
                $this->info('Crud/Resource route added to '.$routeFile);
            } else {
                $this->info('Unable to add the route to '.$routeFile);
            }
        } else {
            $this->info('no route option is provided');
        }

        return $this;
    }

    /**
     * Add routes.
     *
     * @return array
     */
    protected function addAPIRoute()
    {
        $namespace = $this->blueprint->get('crud')['namespace'];
        $controller = 'API\\'.$namespace.'\\'.$this->blueprint->get('controller')['name'];
        $url = strtolower($namespace).'/'.$this->blueprint->get('route')['url'];

        return ["Route::apiResource('".$url."', '".$controller."');"];
    }
}
