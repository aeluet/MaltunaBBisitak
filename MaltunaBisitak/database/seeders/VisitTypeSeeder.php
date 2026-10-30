<?php

namespace Database\Seeders;

use App\Models\VisitType;
use Illuminate\Database\Seeder;

class VisitTypeSeeder extends Seeder
{
    /**
     * Seed the visit types offered by the original public booking form.
     */
    public function run(): void
    {
        $types = [
            ['Orokorra', 'General', 'General'],
            ['Mekanika saila', 'Departamento de Mecánica', 'Department of Mechanics'],
            ['Elektrizitate saila', 'Departamento de Electricidad', 'Department of Electricity'],
            ['Administraritza saila', 'Departamento de Administración', 'Department of Administration'],
            ['LPO saila', 'Departamento de FOL', 'Department of Career Guidance'],
            ['ETHAZI metodologia', 'Metodología ETHAZI', 'ETHAZI methodology'],
            ['DUALa', 'Formación Dual', 'Dual training'],
            ['Gela interaktiboak', 'Aulas interactivas', 'Interactive classrooms'],
            ['Tailerra 4.0', 'Taller 4.0', 'Workshop 4.0'],
            ['Scape Room', 'Scape Room', 'Scape Room'],
        ];

        foreach ($types as $index => [$eu, $es, $en]) {
            VisitType::updateOrCreate(
                ['name_eu' => $eu],
                ['name_es' => $es, 'name_en' => $en, 'sort_order' => $index + 1],
            );
        }
    }
}
