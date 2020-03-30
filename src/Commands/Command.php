<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Support\Facades\File;

class Command extends IlluminateCommand
{
    /**
     * Determine if the crud already exists.
     *
     * @return bool
     */
    protected function alreadyExists()
    {
        foreach (File::files($this->getBlueprintsDirectory()) as $file) {
            preg_match('/_create_(\w{1,})_crud_/', $file->getFilename(), $matches);
            if ($this->getCrudName() === studly_case($matches[1])) {
                return $this->handleJson(File::get($file));
            }
        }

        return false;
    }

    /**
     * Get the blueprints folder path.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getBlueprintsDirectory()
    {
        return database_path().'/blueprints/';
    }

    /**
     * return the formated curd name.
     *
     * @return string curd name
     */
    protected function getCrudName()
    {
        return  $this->option('precise') ?
                    $this->argument('name') :
                    str_singular(preg_replace('/crud$/i', '', $this->argument('name')));
    }

    /**
     * Decodes Json Input.
     *
     * @param string $input
     *
     * @return array
     *
     * @throws \Exception $e
     */
    protected function handleJson($input)
    {
        try {
            // get and decode json file
            $json = json_decode($input, true);
            if (is_null($json)) {
                throw new \Exception(json_last_error());
            }

            return $json;
        } catch (\Exception $e) {
            $this->error('provided json is not valide! check for errors');
        }
    }

    /**
     * Checks if we are using database to generate cruds.
     *
     * @return bool
     */
    protected function runningWithDatabase()
    {
        return false;
    }
}
