<?php

namespace Database\Seeders;

use App\Models\Stack;
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StackAndTechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stackTechnologies = [
            'Back-end' => [
                'Python',
                'PHP',
                'Java',
                '.NET',
            ],
            'Front-end' => [
                'React',
            ],
            'Mobile' => [
                'iOS',
                'Flutter',
                'Android',
            ],
            'Analytics' => [
                'BA',
                'DevOps',
                'Design',
                'QA',
            ],
        ];

        foreach ($stackTechnologies as $stackName => $technologies) {
            $stack = Stack::updateOrCreate(['name' => $stackName]);

            foreach ($technologies as $technologyName) {
                Technology::updateOrCreate([
                    'name' => $technologyName,
                    'stack_id' => $stack->id,
                ]);
            }
        }
    }
}
