<?php

namespace Database\Seeders;

use App\Models\UnitTemplate;
use App\Models\ViewTemplate;
use Illuminate\Database\Seeder;

class ViewTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'GMC' => [
                ['name' => 'GMC - Doctor View',     'code' => 'gmc-doctor',     'blade_path' => 'clinical.gmc.doctor'],
                ['name' => 'GMC - Clerk View',      'code' => 'gmc-clerk',      'blade_path' => 'clinical.gmc.clerk'],
                ['name' => 'GMC - Nurse View',      'code' => 'gmc-nurse',      'blade_path' => 'clinical.gmc.nurse'],
            ],
            'DC' => [
                ['name' => 'DC - Doctor View',      'code' => 'dc-doctor',      'blade_path' => 'clinical.dc.doctor'],
                ['name' => 'DC - Clerk View',       'code' => 'dc-clerk',       'blade_path' => 'clinical.dc.clerk'],
                ['name' => 'DC - Nurse View',       'code' => 'dc-nurse',       'blade_path' => 'clinical.dc.nurse'],
            ],
            'GI' => [
                ['name' => 'GI - Doctor View',      'code' => 'gi-doctor',      'blade_path' => 'clinical.gi.doctor'],
                ['name' => 'GI - Clerk View',       'code' => 'gi-clerk',       'blade_path' => 'clinical.gi.clerk'],
                ['name' => 'GI - Nurse View',       'code' => 'gi-nurse',       'blade_path' => 'clinical.gi.nurse'],
            ],
            'GP' => [
                ['name' => 'GP - Doctor View',      'code' => 'gp-doctor',      'blade_path' => 'clinical.gp.doctor'],
                ['name' => 'GP - Pharmacist View',  'code' => 'gp-pharmacist',  'blade_path' => 'clinical.gp.pharmacist'],
                ['name' => 'GP - Clerk View',       'code' => 'gp-clerk',       'blade_path' => 'clinical.gp.clerk'],
            ],
            'OFFICE' => [
                ['name' => 'Office - Doctor View',  'code' => 'office-doctor',  'blade_path' => 'clinical.office.doctor'],
                ['name' => 'Office - Nurse View',   'code' => 'office-nurse',   'blade_path' => 'clinical.office.nurse'],
                ['name' => 'Office - Clerk View',   'code' => 'office-clerk',   'blade_path' => 'clinical.office.clerk'],
            ],
        ];

        foreach ($data as $unitCode => $views) {
            $unitTemplate = UnitTemplate::where('code', $unitCode)->first();
            if (!$unitTemplate) continue;

            foreach ($views as $view) {
                ViewTemplate::firstOrCreate(
                    ['code' => $view['code']],
                    array_merge($view, ['unit_template_id' => $unitTemplate->id])
                );
            }
        }
    }
}
