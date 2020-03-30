<?php

namespace PHPJuice\Blueprint\Commands;

use Illuminate\Console\Command;

class BlueprintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a list of cruds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
