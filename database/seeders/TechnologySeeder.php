<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technologies = [
            'PHP',
            'Laravel',
            'Python',
            'Django',
            'JavaScript',
            'React',
            'Node.js',
            'PostgreSQL',
            'MySQL',
            'Nginx',
            'Docker',
            'Kubernetes',
            'WordPress'
        ];

        foreach ($technologies as $tech) {
            Technology::firstOrCreate(['name' => $tech]);
        }
    }
}
