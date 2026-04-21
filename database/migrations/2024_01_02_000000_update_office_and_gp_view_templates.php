<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the old Office - Staff View (cascades to any unit_views / user_views using it)
        DB::table('view_templates')->where('code', 'office-staff')->delete();

        $gp     = DB::table('unit_templates')->where('code', 'GP')->first();
        $office = DB::table('unit_templates')->where('code', 'OFFICE')->first();

        if ($gp) {
            DB::table('view_templates')->insertOrIgnore([
                'name'             => 'GP - Doctor View',
                'code'             => 'gp-doctor',
                'blade_path'       => 'clinical.gp.doctor',
                'unit_template_id' => $gp->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        if ($office) {
            foreach ([
                ['name' => 'Office - Doctor View', 'code' => 'office-doctor', 'blade_path' => 'clinical.office.doctor'],
                ['name' => 'Office - Nurse View',  'code' => 'office-nurse',  'blade_path' => 'clinical.office.nurse'],
                ['name' => 'Office - Clerk View',  'code' => 'office-clerk',  'blade_path' => 'clinical.office.clerk'],
            ] as $row) {
                DB::table('view_templates')->insertOrIgnore(array_merge($row, [
                    'unit_template_id' => $office->id,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('view_templates')
            ->whereIn('code', ['gp-doctor', 'office-doctor', 'office-nurse', 'office-clerk'])
            ->delete();

        $office = DB::table('unit_templates')->where('code', 'OFFICE')->first();
        if ($office) {
            DB::table('view_templates')->insertOrIgnore([
                'name'             => 'Office - Staff View',
                'code'             => 'office-staff',
                'blade_path'       => 'clinical.office.staff',
                'unit_template_id' => $office->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
};
