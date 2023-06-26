<?php

namespace Database\Seeders;

use App\Models\Stack;
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StackAndTechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stacks = [
            'Backend',
            'Frontend',
            'Mobile',
            'Analysis',
        ];

        foreach ($stacks as $stackName) {
            Stack::updateOrCreate(['name' => $stackName]);
        }
        $stackTechnologies = [
            'Backend' => [
                'Python',
                'PHP',
                'Java',
                '.NET',
            ],
            'Frontend' => [
                'React',
            ],
            'Mobile' => [
                'iOS',
                'Flutter',
                'Android',
            ],
            'Analysis' => [
                'BA',
                'DevOps',
                'Design',
                'QA',
            ],
        ];

        foreach ($stackTechnologies as $stackName => $technologies) {
            $stack = Stack::where('name', $stackName)->firstOrNew();

            foreach ($technologies as $technologyName) {
                Technology::updateOrCreate([
                    'name' => $technologyName,
                    'stack_id' => $stack->id,
                ]);
            }
        }
    }
}
