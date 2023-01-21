<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Swagger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commad generate a current swagger api documentation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $openapi = \OpenApi\Generator::scan([config('swagger.sources')]);
        file_put_contents("public/api-documentation/swagger.json", $openapi->toJson()) ;
        $this->info('Api documentation generated successfully!');
        return Command::SUCCESS;
    }
}
