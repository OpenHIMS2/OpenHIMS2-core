<?php

namespace Database\Seeders;

use App\Models\UnitTemplate;
use Illuminate\Database\Seeder;

class UnitTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'General Medical Clinic', 'code' => 'GMC'],
            ['name' => 'Dental Clinic',           'code' => 'DC'],
            ['name' => 'General Inward',          'code' => 'GI'],
            ['name' => 'General Pharmacy',        'code' => 'GP'],
            ['name' => 'Office',                  'code' => 'OFFICE'],
        ];

        foreach ($templates as $template) {
            UnitTemplate::firstOrCreate(
                ['code' => $template['code']],
                array_merge($template, ['is_system' => true])
            );
        }
    }
}
