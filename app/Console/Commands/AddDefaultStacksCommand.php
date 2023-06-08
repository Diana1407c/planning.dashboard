<?php

namespace App\Console\Commands;

use App\Models\Stack;
use App\Models\Technology;
use Illuminate\Console\Command;

class AddDefaultStacksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'command:add-stacks';

    /**
     * The console command description.
     */
    protected $description = 'Insert or ignore the default technologies';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Stack::query()->insertOrIgnore([
            ['name' => 'Back-end', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Front-end', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mobile', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Analytics', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
