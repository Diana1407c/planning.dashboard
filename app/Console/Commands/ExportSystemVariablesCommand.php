<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportSystemVariablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export-system-variables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export vars to env file';

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
     * @return void
     */
    public function handle(): void
    {
        $file = File::get(base_path('docker/docker-compose.yaml'));
        $fileLines = explode(PHP_EOL, $file);

        $variableNames = [];
        foreach ($fileLines as $line){
            preg_match('/"\${(?P<var_name>.*)}"/', $line, $matches);
            if ($matches && isset($matches['var_name'])) {
                $variableNames[] = $matches['var_name'];
            }

        }
        $variables = [];

        foreach ($variableNames as $variableName) {
            $variableName = trim($variableName);
            if ($variableName) {
                $value = trim(env($variableName, ''));
                if (str_contains($value, ' ')) {
                    $value = '"' . $value . '"';
                }

                $variables[] = "$variableName=$value";
            }
        }

        File::put(base_path('.env.test'), implode(PHP_EOL, $variables));
    }
}
