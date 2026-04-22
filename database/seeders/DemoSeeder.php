<?php

namespace Database\Seeders;

use App\Models\BloodPressureReading;
use App\Models\ClinicVisit;
use App\Models\DrugName;
use App\Models\DrugNameDefault;
use App\Models\Institution;
use App\Models\Investigation;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PharmacyRestockLog;
use App\Models\PharmacyStock;
use App\Models\PrescriptionDispensing;
use App\Models\TerminologyTerm;
use App\Models\Unit;
use App\Models\UnitTemplate;
use App\Models\UnitView;
use App\Models\User;
use App\Models\ViewTemplate;
use App\Models\VisitDrug;
use App\Models\VisitNote;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearData();

        [$institutions, $units, $unitViews] = $this->seedHierarchy();
        $users = $this->seedUsers($institutions, $units, $unitViews);
        $this->seedDrugs();
        $this->seedTerminology();
        $this->seedPatientsAndVisits($units, $unitViews, $users);
        $this->seedPharmacyData($unitViews, $users);

        $this->command->info('Demo data seeded successfully.');
    }

    // -------------------------------------------------------------------------
    // 1. CLEAR
    // -------------------------------------------------------------------------

    private function clearData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('pharmacy_restock_logs')->truncate();
        DB::table('prescription_dispensings')->truncate();
        DB::table('visit_drug_changes')->truncate();
        DB::table('visit_drugs')->truncate();
        DB::table('investigations')->truncate();
        DB::table('blood_pressure_readings')->truncate();
        DB::table('visit_notes')->truncate();
        DB::table('clinic_visits')->truncate();
        DB::table('patient_allergies')->truncate();
        DB::table('patients')->truncate();
        DB::table('pharmacy_stock')->truncate();
        DB::table('user_views')->truncate();
        DB::table('user_units')->truncate();
        DB::table('unit_views')->truncate();
        DB::table('units')->truncate();
        DB::table('drug_name_defaults')->truncate();
        DB::table('drug_names')->truncate();
        DB::table('terminology_terms')->truncate();
        DB::table('users')->where('role', '!=', 'admin')->delete();
        DB::table('institutions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Cleared existing demo data.');
    }

    // -------------------------------------------------------------------------
    // 2. INSTITUTION HIERARCHY + UNITS + UNIT VIEWS
    // -------------------------------------------------------------------------

    private function seedHierarchy(): array
    {
        // --- Institutions ---
        $moh  = Institution::create(['name' => 'National Department of Health',       'code' => 'NH']);
        $cphd = Institution::create(['name' => 'Northern Regional Health Authority',  'code' => 'NR', 'parent_id' => $moh->id]);
        $thk  = Institution::create(['name' => 'St. George\'s General Hospital',      'code' => 'GH', 'parent_id' => $cphd->id]);
        $bha  = Institution::create(['name' => 'Riverside Community Hospital',        'code' => 'RC', 'parent_id' => $cphd->id]);
        $wphd = Institution::create(['name' => 'Southern Regional Health Authority',  'code' => 'SR', 'parent_id' => $moh->id]);
        $csth = Institution::create(['name' => 'Westfield Teaching Hospital',         'code' => 'WT', 'parent_id' => $wphd->id]);

        $institutions = compact('moh', 'cphd', 'thk', 'bha', 'wphd', 'csth');

        // --- Unit Templates ---
        $gmcT = UnitTemplate::where('code', 'GMC')->first();
        $dcT  = UnitTemplate::where('code', 'DC')->first();
        $giT  = UnitTemplate::where('code', 'GI')->first();
        $gpT  = UnitTemplate::where('code', 'GP')->first();

        // --- View Templates ---
        $vtGmcDoc  = ViewTemplate::where('code', 'gmc-doctor')->first();
        $vtGmcClk  = ViewTemplate::where('code', 'gmc-clerk')->first();
        $vtGmcNrs  = ViewTemplate::where('code', 'gmc-nurse')->first();
        $vtDcDoc   = ViewTemplate::where('code', 'dc-doctor')->first();
        $vtDcClk   = ViewTemplate::where('code', 'dc-clerk')->first();
        $vtDcNrs   = ViewTemplate::where('code', 'dc-nurse')->first();
        $vtGiDoc   = ViewTemplate::where('code', 'gi-doctor')->first();
        $vtGiClk   = ViewTemplate::where('code', 'gi-clerk')->first();
        $vtGiNrs   = ViewTemplate::where('code', 'gi-nurse')->first();
        $vtGpDoc   = ViewTemplate::where('code', 'gp-doctor')->first();
        $vtGpPhar  = ViewTemplate::where('code', 'gp-pharmacist')->first();
        $vtGpClk   = ViewTemplate::where('code', 'gp-clerk')->first();

        // --- Units: Teaching Hospital Kandy ---
        $thkGmc = Unit::create(['name' => 'GMC Ward A',             'unit_number' => 1, 'institution_id' => $thk->id, 'unit_template_id' => $gmcT->id]);
        $thkDc  = Unit::create(['name' => 'Dental Clinic',          'unit_number' => 2, 'institution_id' => $thk->id, 'unit_template_id' => $dcT->id]);
        $thkGi  = Unit::create(['name' => 'General Inward Ward B',  'unit_number' => 3, 'institution_id' => $thk->id, 'unit_template_id' => $giT->id]);
        $thkGp  = Unit::create(['name' => 'General Practice OPD',   'unit_number' => 4, 'institution_id' => $thk->id, 'unit_template_id' => $gpT->id]);

        // --- Units: Base Hospital Akurana ---
        $bhaGmc = Unit::create(['name' => 'General Medical Clinic', 'unit_number' => 1, 'institution_id' => $bha->id, 'unit_template_id' => $gmcT->id]);
        $bhaGp  = Unit::create(['name' => 'General Pharmacy OPD',   'unit_number' => 2, 'institution_id' => $bha->id, 'unit_template_id' => $gpT->id]);

        // --- Units: Colombo South Teaching Hospital ---
        $csthGmc = Unit::create(['name' => 'General Medicine Ward', 'unit_number' => 1, 'institution_id' => $csth->id, 'unit_template_id' => $gmcT->id]);
        $csthGp  = Unit::create(['name' => 'General Practice',      'unit_number' => 2, 'institution_id' => $csth->id, 'unit_template_id' => $gpT->id]);

        $units = compact('thkGmc', 'thkDc', 'thkGi', 'thkGp', 'bhaGmc', 'bhaGp', 'csthGmc', 'csthGp');

        // --- Unit Views: THK GMC Ward A ---
        $vThkGmcDoc = UnitView::create(['name' => 'GMC Ward A – Doctor',  'unit_id' => $thkGmc->id, 'view_template_id' => $vtGmcDoc->id]);
        $vThkGmcClk = UnitView::create(['name' => 'GMC Ward A – Clerk',   'unit_id' => $thkGmc->id, 'view_template_id' => $vtGmcClk->id]);
        $vThkGmcNrs = UnitView::create(['name' => 'GMC Ward A – Nurse',   'unit_id' => $thkGmc->id, 'view_template_id' => $vtGmcNrs->id]);

        // --- Unit Views: THK Dental Clinic ---
        $vThkDcDoc  = UnitView::create(['name' => 'Dental Clinic – Doctor', 'unit_id' => $thkDc->id, 'view_template_id' => $vtDcDoc->id]);
        $vThkDcClk  = UnitView::create(['name' => 'Dental Clinic – Clerk',  'unit_id' => $thkDc->id, 'view_template_id' => $vtDcClk->id]);
        $vThkDcNrs  = UnitView::create(['name' => 'Dental Clinic – Nurse',  'unit_id' => $thkDc->id, 'view_template_id' => $vtDcNrs->id]);

        // --- Unit Views: THK General Inward ---
        $vThkGiDoc  = UnitView::create(['name' => 'General Inward – Doctor', 'unit_id' => $thkGi->id, 'view_template_id' => $vtGiDoc->id]);
        $vThkGiClk  = UnitView::create(['name' => 'General Inward – Clerk',  'unit_id' => $thkGi->id, 'view_template_id' => $vtGiClk->id]);
        $vThkGiNrs  = UnitView::create(['name' => 'General Inward – Nurse',  'unit_id' => $thkGi->id, 'view_template_id' => $vtGiNrs->id]);

        // --- Unit Views: THK General Practice OPD ---
        $vThkGpDoc  = UnitView::create(['name' => 'GP OPD – Doctor',      'unit_id' => $thkGp->id, 'view_template_id' => $vtGpDoc->id]);
        $vThkGpPhar = UnitView::create(['name' => 'GP OPD – Pharmacist',  'unit_id' => $thkGp->id, 'view_template_id' => $vtGpPhar->id]);
        $vThkGpClk  = UnitView::create(['name' => 'GP OPD – Clerk',       'unit_id' => $thkGp->id, 'view_template_id' => $vtGpClk->id]);

        // --- Unit Views: BHA ---
        $vBhaGmcDoc  = UnitView::create(['name' => 'General Medical Clinic – Doctor',     'unit_id' => $bhaGmc->id, 'view_template_id' => $vtGmcDoc->id]);
        $vBhaGmcClk  = UnitView::create(['name' => 'General Medical Clinic – Clerk',      'unit_id' => $bhaGmc->id, 'view_template_id' => $vtGmcClk->id]);
        $vBhaGmcNrs  = UnitView::create(['name' => 'General Medical Clinic – Nurse',      'unit_id' => $bhaGmc->id, 'view_template_id' => $vtGmcNrs->id]);
        $vBhaGpPhar  = UnitView::create(['name' => 'General Pharmacy OPD – Pharmacist',   'unit_id' => $bhaGp->id,  'view_template_id' => $vtGpPhar->id]);
        $vBhaGpClk   = UnitView::create(['name' => 'General Pharmacy OPD – Clerk',        'unit_id' => $bhaGp->id,  'view_template_id' => $vtGpClk->id]);

        // --- Unit Views: CSTH ---
        $vCsthGmcDoc  = UnitView::create(['name' => 'General Medicine Ward – Doctor',   'unit_id' => $csthGmc->id, 'view_template_id' => $vtGmcDoc->id]);
        $vCsthGmcClk  = UnitView::create(['name' => 'General Medicine Ward – Clerk',    'unit_id' => $csthGmc->id, 'view_template_id' => $vtGmcClk->id]);
        $vCsthGmcNrs  = UnitView::create(['name' => 'General Medicine Ward – Nurse',    'unit_id' => $csthGmc->id, 'view_template_id' => $vtGmcNrs->id]);
        $vCsthGpPhar  = UnitView::create(['name' => 'General Practice – Pharmacist',    'unit_id' => $csthGp->id,  'view_template_id' => $vtGpPhar->id]);
        $vCsthGpClk   = UnitView::create(['name' => 'General Practice – Clerk',         'unit_id' => $csthGp->id,  'view_template_id' => $vtGpClk->id]);

        $unitViews = compact(
            'vThkGmcDoc', 'vThkGmcClk', 'vThkGmcNrs',
            'vThkDcDoc',  'vThkDcClk',  'vThkDcNrs',
            'vThkGiDoc',  'vThkGiClk',  'vThkGiNrs',
            'vThkGpDoc',  'vThkGpPhar', 'vThkGpClk',
            'vBhaGmcDoc', 'vBhaGmcClk', 'vBhaGmcNrs',
            'vBhaGpPhar', 'vBhaGpClk',
            'vCsthGmcDoc','vCsthGmcClk','vCsthGmcNrs',
            'vCsthGpPhar','vCsthGpClk'
        );

        $this->command->info('Institution hierarchy, units and views created.');

        return [$institutions, $units, $unitViews];
    }

    // -------------------------------------------------------------------------
    // 3. USERS
    // -------------------------------------------------------------------------

    private function seedUsers(array $institutions, array $units, array $unitViews): array
    {
        $thk  = $institutions['thk'];
        $bha  = $institutions['bha'];
        $csth = $institutions['csth'];

        $users = [];

        $defs = [
            // St. George's – GMC Ward A
            ['key' => 'thkGmcDoc', 'name' => 'Dr. Sarah Mitchell',    'email' => 'sarah.mitchell@stgeorges.nhs',  'gender' => 'female', 'designation' => 'Senior Registrar',      'specialty' => 'General Medicine',  'qualification' => 'MBChB, MD (Medicine)', 'reg' => 'GMC-112345', 'inst' => $thk,  'unit' => 'thkGmc', 'view' => 'vThkGmcDoc'],
            ['key' => 'thkGmcClk', 'name' => 'Mr. James Harrison',    'email' => 'james.harrison@stgeorges.nhs',  'gender' => 'male',   'designation' => 'Medical Records Officer', 'specialty' => null,                'qualification' => null,                   'reg' => null,         'inst' => $thk,  'unit' => 'thkGmc', 'view' => 'vThkGmcClk'],
            ['key' => 'thkGmcNrs', 'name' => 'Nurse Emily Clarke',    'email' => 'emily.clarke@stgeorges.nhs',    'gender' => 'female', 'designation' => 'Staff Nurse',            'specialty' => null,                'qualification' => 'BSc Nursing',          'reg' => null,         'inst' => $thk,  'unit' => 'thkGmc', 'view' => 'vThkGmcNrs'],
            // St. George's – Dental Clinic
            ['key' => 'thkDcDoc',  'name' => 'Dr. Robert Anderson',   'email' => 'robert.anderson@stgeorges.nhs', 'gender' => 'male',   'designation' => 'Dental Surgeon',         'specialty' => 'Dentistry',         'qualification' => 'BDS, MDS',             'reg' => 'GDC-223456', 'inst' => $thk,  'unit' => 'thkDc',  'view' => 'vThkDcDoc'],
            ['key' => 'thkDcClk',  'name' => 'Ms. Laura Thompson',    'email' => 'laura.thompson@stgeorges.nhs',  'gender' => 'female', 'designation' => 'Clinic Clerk',           'specialty' => null,                'qualification' => null,                   'reg' => null,         'inst' => $thk,  'unit' => 'thkDc',  'view' => 'vThkDcClk'],
            ['key' => 'thkDcNrs',  'name' => 'Nurse Patricia White',  'email' => 'patricia.white@stgeorges.nhs',  'gender' => 'female', 'designation' => 'Staff Nurse',            'specialty' => null,                'qualification' => 'BSc Nursing',          'reg' => null,         'inst' => $thk,  'unit' => 'thkDc',  'view' => 'vThkDcNrs'],
            // St. George's – GP OPD
            ['key' => 'thkGpDoc',  'name' => 'Dr. Michael Roberts',   'email' => 'michael.roberts@stgeorges.nhs', 'gender' => 'male',   'designation' => 'General Practitioner',   'specialty' => 'General Practice',  'qualification' => 'MBChB, MRCGP',         'reg' => 'GMC-334567', 'inst' => $thk,  'unit' => 'thkGp',  'view' => 'vThkGpDoc'],
            ['key' => 'thkGpPhar', 'name' => 'Mr. David Collins',     'email' => 'david.collins@stgeorges.nhs',   'gender' => 'male',   'designation' => 'Pharmacist',             'specialty' => null,                'qualification' => 'MPharm',               'reg' => null,         'inst' => $thk,  'unit' => 'thkGp',  'view' => 'vThkGpPhar'],
            ['key' => 'thkGpClk',  'name' => 'Ms. Jennifer Baker',    'email' => 'jennifer.baker@stgeorges.nhs',  'gender' => 'female', 'designation' => 'OPD Clerk',              'specialty' => null,                'qualification' => null,                   'reg' => null,         'inst' => $thk,  'unit' => 'thkGp',  'view' => 'vThkGpClk'],
            // Riverside Community Hospital
            ['key' => 'bhaGmcDoc', 'name' => 'Dr. William Turner',    'email' => 'william.turner@riverside.nhs',  'gender' => 'male',   'designation' => 'Medical Officer',        'specialty' => 'General Medicine',  'qualification' => 'MBChB',                'reg' => 'GMC-445678', 'inst' => $bha,  'unit' => 'bhaGmc', 'view' => 'vBhaGmcDoc'],
            ['key' => 'bhaGmcNrs', 'name' => 'Nurse Catherine Evans', 'email' => 'catherine.evans@riverside.nhs', 'gender' => 'female', 'designation' => 'Staff Nurse',            'specialty' => null,                'qualification' => 'BSc Nursing',          'reg' => null,         'inst' => $bha,  'unit' => 'bhaGmc', 'view' => 'vBhaGmcNrs'],
            // Westfield Teaching Hospital
            ['key' => 'csthGmcDoc','name' => 'Dr. Thomas Hughes',     'email' => 'thomas.hughes@westfield.nhs',   'gender' => 'male',   'designation' => 'Registrar',              'specialty' => 'General Medicine',  'qualification' => 'MBChB, MD',            'reg' => 'GMC-556789', 'inst' => $csth, 'unit' => 'csthGmc','view' => 'vCsthGmcDoc'],
            ['key' => 'csthGmcNrs','name' => 'Nurse Margaret Hall',   'email' => 'margaret.hall@westfield.nhs',   'gender' => 'female', 'designation' => 'Staff Nurse',            'specialty' => null,                'qualification' => 'BSc Nursing',          'reg' => null,         'inst' => $csth, 'unit' => 'csthGmc','view' => 'vCsthGmcNrs'],
        ];

        foreach ($defs as $d) {
            $user = User::create([
                'name'            => $d['name'],
                'email'           => $d['email'],
                'password'        => Hash::make('password'),
                'role'            => 'user',
                'institution_id'  => $d['inst']->id,
                'gender'          => $d['gender'],
                'designation'     => $d['designation'],
                'specialty'       => $d['specialty'],
                'qualification'   => $d['qualification'],
                'registration_no' => $d['reg'],
            ]);

            $user->units()->attach($units[$d['unit']]->id);
            $user->views()->attach($unitViews[$d['view']]->id);

            $users[$d['key']] = $user;
        }

        $this->command->info('Users created and assigned.');

        return $users;
    }

    // -------------------------------------------------------------------------
    // 4. DRUGS
    // -------------------------------------------------------------------------

    private function seedDrugs(): void
    {
        $drugs = [
            // Antidiabetics
            ['name' => 'Metformin',        'type' => 'Oral',  'dose' => '500',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Glibenclamide',    'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Glipizide',        'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Sitagliptin',      'type' => 'Oral',  'dose' => '100',   'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Empagliflozin',    'type' => 'Oral',  'dose' => '10',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            // Antihypertensives
            ['name' => 'Amlodipine',       'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Lisinopril',       'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Enalapril',        'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Losartan',         'type' => 'Oral',  'dose' => '50',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Valsartan',        'type' => 'Oral',  'dose' => '80',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Metoprolol',       'type' => 'Oral',  'dose' => '25',    'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Hydrochlorothiazide','type'=>'Oral',  'dose' => '25',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Furosemide',       'type' => 'Oral',  'dose' => '40',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '14 days'],
            ['name' => 'Spironolactone',   'type' => 'Oral',  'dose' => '25',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            // Statins
            ['name' => 'Atorvastatin',     'type' => 'Oral',  'dose' => '20',    'unit' => 'mg',   'frequency' => 'nocte', 'duration' => '30 days'],
            ['name' => 'Simvastatin',      'type' => 'Oral',  'dose' => '20',    'unit' => 'mg',   'frequency' => 'nocte', 'duration' => '30 days'],
            ['name' => 'Rosuvastatin',     'type' => 'Oral',  'dose' => '10',    'unit' => 'mg',   'frequency' => 'nocte', 'duration' => '30 days'],
            // Antiplatelets / Anticoagulants
            ['name' => 'Aspirin',          'type' => 'Oral',  'dose' => '75',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Clopidogrel',      'type' => 'Oral',  'dose' => '75',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Warfarin',         'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Digoxin',          'type' => 'Oral',  'dose' => '0.25',  'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            // Respiratory
            ['name' => 'Salbutamol',       'type' => 'MDI',   'dose' => '100',   'unit' => 'mcg',  'frequency' => 'SOS',   'duration' => null],
            ['name' => 'Beclomethasone',   'type' => 'MDI',   'dose' => '200',   'unit' => 'mcg',  'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Theophylline',     'type' => 'Oral',  'dose' => '200',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            // GI
            ['name' => 'Omeprazole',       'type' => 'Oral',  'dose' => '20',    'unit' => 'mg',   'frequency' => 'bd',    'duration' => '14 days'],
            ['name' => 'Pantoprazole',     'type' => 'Oral',  'dose' => '40',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '14 days'],
            ['name' => 'Ranitidine',       'type' => 'Oral',  'dose' => '150',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '14 days'],
            ['name' => 'Domperidone',      'type' => 'Oral',  'dose' => '10',    'unit' => 'mg',   'frequency' => 'tds',   'duration' => '7 days'],
            ['name' => 'Ondansetron',      'type' => 'Oral',  'dose' => '4',     'unit' => 'mg',   'frequency' => 'tds',   'duration' => '5 days'],
            ['name' => 'Metronidazole',    'type' => 'Oral',  'dose' => '400',   'unit' => 'mg',   'frequency' => 'tds',   'duration' => '7 days'],
            // Analgesics / Anti-inflammatory
            ['name' => 'Paracetamol',      'type' => 'Oral',  'dose' => '500',   'unit' => 'mg',   'frequency' => 'tds',   'duration' => '5 days'],
            ['name' => 'Ibuprofen',        'type' => 'Oral',  'dose' => '400',   'unit' => 'mg',   'frequency' => 'tds',   'duration' => '5 days'],
            ['name' => 'Tramadol',         'type' => 'Oral',  'dose' => '50',    'unit' => 'mg',   'frequency' => 'tds',   'duration' => '5 days'],
            ['name' => 'Prednisolone',     'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'mane',  'duration' => '14 days'],
            ['name' => 'Dexamethasone',    'type' => 'IM',    'dose' => '4',     'unit' => 'mg',   'frequency' => 'SOS',   'duration' => null],
            ['name' => 'Colchicine',       'type' => 'Oral',  'dose' => '0.5',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '5 days'],
            ['name' => 'Allopurinol',      'type' => 'Oral',  'dose' => '100',   'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            // Antibiotics
            ['name' => 'Amoxicillin',      'type' => 'Oral',  'dose' => '500',   'unit' => 'mg',   'frequency' => 'tds',   'duration' => '7 days'],
            ['name' => 'Ciprofloxacin',    'type' => 'Oral',  'dose' => '500',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '7 days'],
            ['name' => 'Cotrimoxazole',    'type' => 'Oral',  'dose' => '960',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '7 days'],
            ['name' => 'Doxycycline',      'type' => 'Oral',  'dose' => '100',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '7 days'],
            ['name' => 'Azithromycin',     'type' => 'Oral',  'dose' => '500',   'unit' => 'mg',   'frequency' => 'mane',  'duration' => '5 days'],
            ['name' => 'Ceftriaxone',      'type' => 'IV',    'dose' => '1',     'unit' => 'g',    'frequency' => 'mane',  'duration' => '7 days'],
            // Thyroid
            ['name' => 'Levothyroxine',    'type' => 'Oral',  'dose' => '50',    'unit' => 'mcg',  'frequency' => 'mane',  'duration' => '30 days'],
            // Neuro / Psych
            ['name' => 'Amitriptyline',    'type' => 'Oral',  'dose' => '25',    'unit' => 'mg',   'frequency' => 'nocte', 'duration' => '30 days'],
            ['name' => 'Sertraline',       'type' => 'Oral',  'dose' => '50',    'unit' => 'mg',   'frequency' => 'mane',  'duration' => '30 days'],
            ['name' => 'Gabapentin',       'type' => 'Oral',  'dose' => '300',   'unit' => 'mg',   'frequency' => 'tds',   'duration' => '30 days'],
            ['name' => 'Carbamazepine',    'type' => 'Oral',  'dose' => '200',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Phenytoin',        'type' => 'Oral',  'dose' => '100',   'unit' => 'mg',   'frequency' => 'bd',    'duration' => '30 days'],
            ['name' => 'Diazepam',         'type' => 'Oral',  'dose' => '5',     'unit' => 'mg',   'frequency' => 'nocte', 'duration' => '7 days'],
        ];

        foreach ($drugs as $d) {
            $dn = DrugName::create(['name' => $d['name']]);
            DrugNameDefault::create([
                'drug_name_id' => $dn->id,
                'type'         => $d['type'],
                'dose'         => $d['dose'],
                'unit'         => $d['unit'],
                'frequency'    => $d['frequency'],
                'duration'     => $d['duration'],
            ]);
        }

        $this->command->info('Drug names and defaults created (' . count($drugs) . ' drugs).');
    }

    // -------------------------------------------------------------------------
    // 5. TERMINOLOGY
    // -------------------------------------------------------------------------

    private function seedTerminology(): void
    {
        $terms = [
            'presenting_complaints' => [
                'Headache', 'Chest pain', 'Shortness of breath', 'Fever', 'Cough',
                'Abdominal pain', 'Dizziness', 'Weakness', 'Fatigue', 'Back pain',
                'Joint pain', 'Leg swelling', 'Palpitations', 'Nausea', 'Vomiting',
                'Diarrhoea', 'Constipation', 'Dysuria', 'Haematuria', 'Weight loss',
                'Weight gain', 'Loss of appetite', 'Blurred vision', 'Difficulty swallowing',
                'Skin rash', 'Numbness', 'Tingling', 'Syncope', 'Polyuria', 'Polydipsia',
                'Ear pain', 'Nasal congestion', 'Tremor', 'Insomnia', 'Toothache',
            ],
            'complaint_durations' => [
                '1 day', '2 days', '3 days', '4 days', '5 days', '6 days',
                '1 week', '2 weeks', '3 weeks',
                '1 month', '2 months', '3 months', '6 months',
                '1 year', '2 years', 'More than 2 years',
                'Since childhood', 'Worsening over time', 'Intermittent',
            ],
            'past_medical_history' => [
                'Diabetes Mellitus Type 2', 'Hypertension', 'Ischemic Heart Disease',
                'Dyslipidaemia', 'Asthma', 'COPD', 'Chronic Kidney Disease',
                'Hypothyroidism', 'Hyperthyroidism', 'Epilepsy', 'Stroke',
                'Atrial Fibrillation', 'Heart Failure', 'Rheumatoid Arthritis',
                'Osteoporosis', 'Gout', 'Peptic Ulcer Disease', 'GORD',
                'Liver Cirrhosis', 'Chronic Hepatitis B', 'Chronic Hepatitis C',
                'Tuberculosis', 'Depression', 'Anxiety', 'Anaemia',
                'Thalassaemia', 'Dengue', 'Malaria', 'Cancer (specify)',
                'Parkinson\'s Disease', 'Bipolar Disorder',
            ],
            'past_surgical_history' => [
                'Appendicectomy', 'Cholecystectomy', 'Hernia Repair', 'CABG',
                'Valve Replacement', 'Hip Replacement', 'Knee Replacement',
                'Hysterectomy', 'Caesarean Section', 'Tonsillectomy', 'Thyroidectomy',
                'Mastectomy', 'Prostatectomy', 'Splenectomy', 'Colostomy',
                'Cataract Surgery', 'PTCA with Stenting', 'Laparotomy', 'Dental Extraction',
            ],
            'social_history' => [
                'Non-smoker', 'Current smoker', 'Ex-smoker',
                'Social alcohol use', 'Regular alcohol use', 'Non-alcoholic',
                'Betel chewing', 'Independent ADLs', 'Dependent for ADLs',
                'Lives alone', 'Lives with family', 'Farmer',
                'Office worker', 'Manual labourer', 'Retired',
            ],
            'menstrual_history' => [
                'Regular cycles', 'Irregular cycles', 'Oligomenorrhoea', 'Amenorrhoea',
                'Dysmenorrhoea', 'Menorrhagia', 'Post-menopausal', 'Pre-menopausal',
                'LMP normal', 'Peri-menopausal',
            ],
            'investigations' => [
                'Full Blood Count', 'Serum Creatinine', 'eGFR', 'Blood Urea',
                'Serum Electrolytes', 'Fasting Blood Sugar', 'HbA1c', 'Lipid Profile',
                'Liver Function Tests', 'TSH', 'FT4', 'ECG', 'Chest X-ray',
                'Echocardiogram', 'Urine Full Report', 'Urine Culture',
                'Urine Protein/Creatinine Ratio', 'Stool Full Report',
                'Peripheral Blood Film', 'Serum Uric Acid', 'Serum Calcium',
                'Serum Phosphate', 'Serum Albumin', 'CRP', 'ESR',
                'Prothrombin Time / INR', 'Blood Culture', 'D-dimer',
                'HBsAg', 'Anti-HCV', 'Serum Vitamin B12', 'Serum Folate',
                'Renal Ultrasound', 'Abdominal Ultrasound', 'CT Abdomen',
                'MRI Brain', 'Fasting Lipid Profile', 'Random Blood Sugar',
            ],
            'diabetes_instructions' => [
                'Follow diabetic diet — avoid simple sugars',
                'Monitor blood glucose regularly',
                'Check fasting blood glucose daily',
                'HbA1c test every 3 months',
                'Annual eye review',
                'Annual foot examination',
                'Carry glucose tablets at all times',
                'Avoid hypoglycaemic episodes',
                'Exercise 30 minutes daily',
                'Regular follow-up required',
                'Annual creatinine and urine microalbumin',
                'Sick day rules explained',
                'Diabetic foot care education given',
            ],
            'hypertension_instructions' => [
                'Low salt diet — limit sodium to < 2 g per day',
                'Monitor blood pressure at home regularly',
                'Avoid NSAIDs and COX-2 inhibitors',
                'Reduce weight if overweight',
                'Exercise 30–45 minutes most days',
                'Limit alcohol intake',
                'Stop smoking',
                'Medication adherence important',
                'Regular follow-up required',
                'Home BP log to be maintained',
            ],
            'dyslipidaemia_instructions' => [
                'Low saturated fat diet',
                'Avoid trans fats',
                'Increase dietary fibre',
                'Regular aerobic exercise',
                'Annual lipid profile',
                'Mediterranean diet advised',
                'Avoid processed foods',
                'Limit red meat consumption',
            ],
            'general_instructions' => [
                'Review in 1 week', 'Review in 2 weeks', 'Review in 1 month',
                'Review in 3 months', 'Review in 6 months',
                'Come back if symptoms worsen', 'Seek urgent care if chest pain',
                'Medication compliance is important',
                'Take medications with meals',
                'Take medications before meals',
                'Do not stop medications without advice',
                'Reduce weight', 'Increase physical activity',
                'Stay well hydrated', 'Rest and adequate sleep',
                'Smoking cessation strongly advised',
                'Return immediately if breathless or severe pain',
            ],
            'differential_diagnosis' => [
                'Diabetes Mellitus Type 2', 'Hypertension', 'Ischemic Heart Disease',
                'Heart Failure', 'COPD', 'Asthma', 'Pneumonia', 'Pulmonary Embolism',
                'Pleural Effusion', 'Acute Coronary Syndrome', 'Stroke', 'TIA',
                'Urinary Tract Infection', 'Pyelonephritis', 'Chronic Kidney Disease',
                'Nephrotic Syndrome', 'Peptic Ulcer', 'GORD', 'Acute Pancreatitis',
                'Hepatitis', 'Liver Cirrhosis', 'Anaemia', 'Thyroid Disease',
                'Gout', 'Rheumatoid Arthritis', 'SLE', 'Epilepsy', 'Migraine',
                'Depression', 'Anxiety', 'Cellulitis', 'DVT', 'Dental Caries', 'Periodontitis',
            ],
            'working_diagnosis' => [
                'Diabetes Mellitus Type 2 – Poor Glycaemic Control',
                'Diabetes Mellitus Type 2 – Well Controlled',
                'Hypertension – Uncontrolled',
                'Hypertension – Controlled',
                'Ischemic Heart Disease – Stable',
                'Heart Failure – NYHA Class II',
                'COPD – Stable',
                'Asthma – Moderate Persistent',
                'Community Acquired Pneumonia',
                'Urinary Tract Infection – Lower',
                'Pyelonephritis',
                'Chronic Kidney Disease – Stage 3',
                'Gout – Acute Attack',
                'Anaemia – Iron Deficiency',
                'Hypothyroidism',
                'Dyslipidaemia',
                'Dental Caries',
                'Acute Periodontitis',
            ],
            'general_looking' => [
                'Well-looking', 'Well-nourished', 'Ill-looking', 'Mildly unwell',
                'Moderately unwell', 'Severely unwell', 'Pale', 'Icteric', 'Cyanosed',
                'Oedematous', 'Dehydrated', 'Febrile', 'Afebrile',
                'Alert and conscious', 'Confused', 'In no distress',
                'In mild distress', 'In moderate distress',
            ],
            'cardiology_findings' => [
                'Normal S1 S2 heard', 'No murmurs', 'Regular rhythm', 'Irregular rhythm',
                'Apex beat not displaced', 'Apex beat displaced laterally',
                'No raised JVP', 'Raised JVP', 'Muffled heart sounds',
                'Systolic murmur grade II', 'Systolic murmur grade III',
                'Pansystolic murmur at apex', 'Ejection systolic murmur',
            ],
            'respiratory_findings' => [
                'Clear air entry bilaterally', 'Reduced air entry left base',
                'Reduced air entry right base', 'Bilateral basal crepitations',
                'Bilateral wheeze', 'Dullness to percussion at left base',
                'Dullness to percussion at right base', 'No added sounds',
                'Tachypnoeic at rest', 'Good air entry bilaterally', 'Rhonchi bilaterally',
            ],
            'abdominal_findings' => [
                'Soft and non-tender', 'Mild epigastric tenderness',
                'Right iliac fossa tenderness', 'Hepatomegaly – 2 cm below costal margin',
                'Splenomegaly – 3 cm below costal margin', 'No organomegaly',
                'Ascites present', 'No ascites', 'Renal angle tenderness on right',
                'Bowel sounds normal', 'Distended abdomen', 'Guarding present',
            ],
            'neurological_findings' => [
                'Alert and oriented', 'GCS 15/15', 'Normal power all four limbs',
                'Normal tone', 'Normal reflexes', 'No focal neurological deficits',
                'Confused – GCS 14/15', 'Mild weakness right upper limb',
                'Ataxic gait', 'Tremor present', 'Sensory loss in feet bilaterally',
            ],
            'dermatological_findings' => [
                'No rash', 'No skin lesions', 'Dry skin', 'Oedema of feet',
                'Pitting oedema bilateral ankles', 'Erythema present',
                'Petechiae noted', 'Cellulitis right leg', 'Normal skin',
                'Pallor of palmar creases', 'Jaundice noted', 'Gum inflammation',
            ],
        ];

        $rows = [];
        foreach ($terms as $category => $list) {
            foreach ($list as $term) {
                $rows[] = ['category' => $category, 'term' => $term];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('terminology_terms')->insert($chunk);
        }

        $this->command->info('Terminology terms created (' . count($rows) . ' terms).');
    }

    // -------------------------------------------------------------------------
    // 6. PATIENTS + VISITS
    // -------------------------------------------------------------------------

    private function seedPatientsAndVisits(array $units, array $unitViews, array $users): void
    {
        $unit    = $units['thkGmc'];     // Teaching Hospital Kandy – GMC Ward A
        $doctor  = $users['thkGmcDoc'];
        $clerk   = $users['thkGmcClk'];
        $nurse   = $users['thkGmcNrs'];

        $patientData = [
            [
                'name'   => 'James Wilson',
                'dob'    => '1966-03-12',
                'gender' => 'male',
                'nic'    => 'NI660312001',
                'mobile' => '07700900001',
                'phn'    => 'PHN001',
                'address'=> '14 Elm Street, Northfield',
                'allergies' => ['Penicillin'],
                'pmh'    => ['Diabetes Mellitus Type 2', 'Hypertension'],
                'drugs'  => [
                    ['name' => 'Metformin',    'type' => 'Oral', 'dose' => '500', 'unit' => 'mg',  'frequency' => 'bd',   'duration' => '30 days'],
                    ['name' => 'Amlodipine',   'type' => 'Oral', 'dose' => '5',   'unit' => 'mg',  'frequency' => 'mane', 'duration' => '30 days'],
                    ['name' => 'Atorvastatin', 'type' => 'Oral', 'dose' => '20',  'unit' => 'mg',  'frequency' => 'nocte','duration' => '30 days'],
                ],
            ],
            [
                'name'   => 'Emma Thompson',
                'dob'    => '1979-07-25',
                'gender' => 'female',
                'nic'    => 'NI790725002',
                'mobile' => '07700900002',
                'phn'    => 'PHN002',
                'address'=> '7 Birch Avenue, Westbrook',
                'allergies' => ['Sulfonamides'],
                'pmh'    => ['Hypertension', 'Dyslipidaemia'],
                'drugs'  => [
                    ['name' => 'Losartan',     'type' => 'Oral', 'dose' => '50',  'unit' => 'mg',  'frequency' => 'mane', 'duration' => '30 days'],
                    ['name' => 'Atorvastatin', 'type' => 'Oral', 'dose' => '20',  'unit' => 'mg',  'frequency' => 'nocte','duration' => '30 days'],
                ],
            ],
            [
                'name'   => 'Robert Davis',
                'dob'    => '1958-11-04',
                'gender' => 'male',
                'nic'    => 'NI581104003',
                'mobile' => '07700900003',
                'phn'    => 'PHN003',
                'address'=> '32 Oak Lane, Milltown',
                'allergies' => [],
                'pmh'    => ['Diabetes Mellitus Type 2', 'Ischemic Heart Disease', 'Dyslipidaemia'],
                'drugs'  => [
                    ['name' => 'Metformin',    'type' => 'Oral', 'dose' => '500', 'unit' => 'mg',  'frequency' => 'bd',   'duration' => '30 days'],
                    ['name' => 'Aspirin',      'type' => 'Oral', 'dose' => '75',  'unit' => 'mg',  'frequency' => 'mane', 'duration' => '30 days'],
                    ['name' => 'Atorvastatin', 'type' => 'Oral', 'dose' => '40',  'unit' => 'mg',  'frequency' => 'nocte','duration' => '30 days'],
                    ['name' => 'Metoprolol',   'type' => 'Oral', 'dose' => '25',  'unit' => 'mg',  'frequency' => 'bd',   'duration' => '30 days'],
                ],
            ],
            [
                'name'   => 'Sophie Johnson',
                'dob'    => '1988-02-18',
                'gender' => 'female',
                'nic'    => 'NI880218004',
                'mobile' => '07700900004',
                'phn'    => 'PHN004',
                'address'=> '5 Maple Close, Eastfield',
                'allergies' => ['Aspirin', 'Ibuprofen'],
                'pmh'    => ['Asthma', 'Hypothyroidism'],
                'drugs'  => [
                    ['name' => 'Salbutamol',     'type' => 'MDI',  'dose' => '100', 'unit' => 'mcg', 'frequency' => 'SOS',  'duration' => null],
                    ['name' => 'Beclomethasone', 'type' => 'MDI',  'dose' => '200', 'unit' => 'mcg', 'frequency' => 'bd',   'duration' => '30 days'],
                    ['name' => 'Levothyroxine',  'type' => 'Oral', 'dose' => '50',  'unit' => 'mcg', 'frequency' => 'mane', 'duration' => '30 days'],
                ],
            ],
            [
                'name'   => 'George Carter',
                'dob'    => '1952-09-30',
                'gender' => 'male',
                'nic'    => 'NI520930005',
                'mobile' => '07700900005',
                'phn'    => 'PHN005',
                'address'=> '19 Cedar Road, Southgate',
                'allergies' => ['Codeine'],
                'pmh'    => ['Hypertension', 'Chronic Kidney Disease', 'Gout'],
                'drugs'  => [
                    ['name' => 'Amlodipine',  'type' => 'Oral', 'dose' => '10',  'unit' => 'mg', 'frequency' => 'mane', 'duration' => '30 days'],
                    ['name' => 'Furosemide',  'type' => 'Oral', 'dose' => '40',  'unit' => 'mg', 'frequency' => 'mane', 'duration' => '30 days'],
                    ['name' => 'Allopurinol', 'type' => 'Oral', 'dose' => '100', 'unit' => 'mg', 'frequency' => 'mane', 'duration' => '30 days'],
                ],
            ],
        ];

        // OPD vital progressions per patient (5 OPD visits each)
        $opdVitals = [
            // Sunil – DM+HTN, weight loss over time, BP improving
            [['h'=>168,'w'=>87.5,'sys'=>162,'dia'=>98,'opd'=>'OPD-1001'],['h'=>168,'w'=>86.0,'sys'=>155,'dia'=>94,'opd'=>'OPD-1022'],['h'=>168,'w'=>84.5,'sys'=>148,'dia'=>90,'opd'=>'OPD-1045'],['h'=>168,'w'=>83.0,'sys'=>142,'dia'=>88,'opd'=>'OPD-1067'],['h'=>168,'w'=>82.0,'sys'=>138,'dia'=>86,'opd'=>'OPD-1089']],
            // Kamala – HTN, stable
            [['h'=>158,'w'=>68.0,'sys'=>148,'dia'=>92,'opd'=>'OPD-2001'],['h'=>158,'w'=>67.5,'sys'=>145,'dia'=>90,'opd'=>'OPD-2015'],['h'=>158,'w'=>67.0,'sys'=>142,'dia'=>88,'opd'=>'OPD-2030'],['h'=>158,'w'=>66.5,'sys'=>140,'dia'=>86,'opd'=>'OPD-2044'],['h'=>158,'w'=>66.0,'sys'=>138,'dia'=>84,'opd'=>'OPD-2059']],
            // Nimal – IHD+DM, weight stable
            [['h'=>172,'w'=>78.0,'sys'=>140,'dia'=>88,'opd'=>'OPD-3001'],['h'=>172,'w'=>78.5,'sys'=>138,'dia'=>86,'opd'=>'OPD-3018'],['h'=>172,'w'=>77.5,'sys'=>136,'dia'=>84,'opd'=>'OPD-3035'],['h'=>172,'w'=>77.0,'sys'=>134,'dia'=>82,'opd'=>'OPD-3052'],['h'=>172,'w'=>76.5,'sys'=>132,'dia'=>80,'opd'=>'OPD-3069']],
            // Dilini – Asthma, normal BP
            [['h'=>163,'w'=>58.0,'sys'=>118,'dia'=>76,'opd'=>'OPD-4001'],['h'=>163,'w'=>57.5,'sys'=>116,'dia'=>74,'opd'=>'OPD-4010'],['h'=>163,'w'=>57.5,'sys'=>118,'dia'=>76,'opd'=>'OPD-4022'],['h'=>163,'w'=>58.0,'sys'=>120,'dia'=>78,'opd'=>'OPD-4035'],['h'=>163,'w'=>58.0,'sys'=>118,'dia'=>76,'opd'=>'OPD-4048']],
            // Rohan – CKD+HTN, weight slight decrease
            [['h'=>170,'w'=>72.0,'sys'=>158,'dia'=>98,'opd'=>'OPD-5001'],['h'=>170,'w'=>71.5,'sys'=>152,'dia'=>94,'opd'=>'OPD-5020'],['h'=>170,'w'=>71.0,'sys'=>148,'dia'=>92,'opd'=>'OPD-5039'],['h'=>170,'w'=>70.5,'sys'=>145,'dia'=>90,'opd'=>'OPD-5058'],['h'=>170,'w'=>70.0,'sys'=>142,'dia'=>88,'opd'=>'OPD-5077']],
        ];

        // Clinic visit numbers per patient
        $clinicNumbers = [
            ['CLN-A-2401','CLN-A-2402','CLN-A-2403','CLN-A-2404','CLN-A-2405'],
            ['CLN-B-2401','CLN-B-2402','CLN-B-2403','CLN-B-2404','CLN-B-2405'],
            ['CLN-C-2401','CLN-C-2402','CLN-C-2403','CLN-C-2404','CLN-C-2405'],
            ['CLN-D-2401','CLN-D-2402','CLN-D-2403','CLN-D-2404','CLN-D-2405'],
            ['CLN-E-2401','CLN-E-2402','CLN-E-2403','CLN-E-2404','CLN-E-2405'],
        ];

        // Spread visits over the last 5 months (one visit every ~3 weeks)
        $baseDate = Carbon::now()->subMonths(5)->startOfMonth();

        foreach ($patientData as $pi => $pd) {
            $patient = Patient::create([
                'name'    => $pd['name'],
                'dob'     => $pd['dob'],
                'gender'  => $pd['gender'],
                'nic'     => $pd['nic'],
                'mobile'  => $pd['mobile'],
                'phn'     => $pd['phn'],
                'address' => $pd['address'],
            ]);

            foreach ($pd['allergies'] as $allergen) {
                PatientAllergy::create(['patient_id' => $patient->id, 'allergen' => $allergen]);
            }

            // Track visit number per date to avoid uniqueness conflicts
            $visitNumberByDate = [];

            for ($vi = 0; $vi < 5; $vi++) {
                $visitDate = $baseDate->copy()->addWeeks($vi * 3 + $pi)->format('Y-m-d');

                // OPD visit
                $opdVisitNum = ($visitNumberByDate[$visitDate] ?? 0) + 1;
                $visitNumberByDate[$visitDate] = $opdVisitNum;

                $opdNum = $pi * 100 + $vi + 1;

                $opdVisit = ClinicVisit::create([
                    'patient_id'   => $patient->id,
                    'unit_id'      => $unit->id,
                    'visit_date'   => $visitDate,
                    'visit_number' => $opdNum * 10 + 1,
                    'queue_session'=> 1,
                    'category'     => 'opd',
                    'status'       => 'visited',
                    'registered_by'=> $clerk->id,
                    'opd_number'   => $opdVitals[$pi][$vi]['opd'],
                    'height'       => $opdVitals[$pi][$vi]['h'],
                    'weight'       => $opdVitals[$pi][$vi]['w'],
                    'bp_systolic'  => $opdVitals[$pi][$vi]['sys'],
                    'bp_diastolic' => $opdVitals[$pi][$vi]['dia'],
                ]);

                VisitNote::create([
                    'visit_id'              => $opdVisit->id,
                    'presenting_complaints' => ['Routine review', 'Fatigue'],
                    'complaint_durations'   => ['1 month'],
                    'past_medical_history'  => $pd['pmh'],
                    'social_history'        => ['Non-smoker', 'Lives with family'],
                    'general_looking'       => ['Well-looking', 'Afebrile'],
                    'management_instruction'=> ['Review in 1 month', 'Medication compliance is important'],
                ]);

                BloodPressureReading::create([
                    'visit_id'    => $opdVisit->id,
                    'systolic'    => $opdVitals[$pi][$vi]['sys'],
                    'diastolic'   => $opdVitals[$pi][$vi]['dia'],
                    'recorded_at' => Carbon::parse($visitDate)->setTime(9, 0 + $vi * 5),
                    'recorded_by' => $nurse->id,
                ]);

                // Clinic visit (same day, different visit number)
                $clnNum = $pi * 100 + $vi + 1;

                $clinicDate = $baseDate->copy()->addWeeks($vi * 3 + $pi)->addDays(1)->format('Y-m-d');

                $clinicVisit = ClinicVisit::create([
                    'patient_id'    => $patient->id,
                    'unit_id'       => $unit->id,
                    'visit_date'    => $clinicDate,
                    'visit_number'  => $clnNum * 10 + 2,
                    'queue_session' => 1,
                    'category'      => $vi === 0 ? 'new_clinic_visit' : 'recurrent_clinic_visit',
                    'status'        => 'visited',
                    'registered_by' => $clerk->id,
                    'clinic_number' => $clinicNumbers[$pi][$vi],
                    'height'        => $opdVitals[$pi][$vi]['h'],
                    'weight'        => $opdVitals[$pi][$vi]['w'],
                    'bp_systolic'   => $opdVitals[$pi][$vi]['sys'],
                    'bp_diastolic'  => $opdVitals[$pi][$vi]['dia'],
                ]);

                VisitNote::create([
                    'visit_id'              => $clinicVisit->id,
                    'presenting_complaints' => ['Routine review', $vi === 0 ? $pd['pmh'][0] : 'Follow-up'],
                    'complaint_durations'   => [$vi === 0 ? '6 months' : '1 month'],
                    'past_medical_history'  => $pd['pmh'],
                    'social_history'        => ['Non-smoker', 'Lives with family'],
                    'general_looking'       => ['Well-looking', 'Afebrile'],
                    'cardiology_findings'   => ['Normal S1 S2 heard', 'No murmurs', 'Regular rhythm'],
                    'respiratory_findings'  => ['Clear air entry bilaterally'],
                    'abdominal_findings'    => ['Soft and non-tender', 'No organomegaly'],
                    'management_instruction'=> ['Review in 1 month', 'Medication compliance is important'],
                ]);

                BloodPressureReading::create([
                    'visit_id'    => $clinicVisit->id,
                    'systolic'    => $opdVitals[$pi][$vi]['sys'],
                    'diastolic'   => $opdVitals[$pi][$vi]['dia'],
                    'recorded_at' => Carbon::parse($clinicDate)->setTime(10, 0 + $vi * 5),
                    'recorded_by' => $nurse->id,
                ]);

                Investigation::create([
                    'visit_id'    => $clinicVisit->id,
                    'name'        => 'HbA1c',
                    'value'       => number_format(7.8 - ($vi * 0.2) - ($pi * 0.1), 1) . '%',
                    'recorded_at' => Carbon::parse($clinicDate)->setTime(10, 30),
                    'recorded_by' => $doctor->id,
                ]);

                if (in_array('Hypertension', $pd['pmh'])) {
                    Investigation::create([
                        'visit_id'    => $clinicVisit->id,
                        'name'        => 'Serum Creatinine',
                        'value'       => number_format(90 + $pi * 15 - $vi * 2, 0) . ' µmol/L',
                        'recorded_at' => Carbon::parse($clinicDate)->setTime(10, 32),
                        'recorded_by' => $doctor->id,
                    ]);
                }

                // Prescribe drugs on clinic visits
                foreach ($pd['drugs'] as $drug) {
                    VisitDrug::create([
                        'visit_id'  => $clinicVisit->id,
                        'section'   => 'clinic',
                        'type'      => $drug['type'],
                        'name'      => $drug['name'],
                        'dose'      => $drug['dose'],
                        'unit'      => $drug['unit'],
                        'frequency' => $drug['frequency'],
                        'duration'  => $drug['duration'],
                        'created_by'=> $doctor->id,
                        'updated_by'=> $doctor->id,
                    ]);
                }
            }
        }

        $this->command->info('5 patients with 5 OPD + 5 clinic visits each created (THK GMC Ward A).');
    }

    // -------------------------------------------------------------------------
    // 7. PHARMACY DATA
    // -------------------------------------------------------------------------

    private function seedPharmacyData(array $unitViews, array $users): void
    {
        $view      = $unitViews['vThkGpPhar'];  // GP OPD – Pharmacist (THK)
        $pharmacist = $users['thkGpPhar'];
        $now        = Carbon::now();

        $stocks = [
            ['drug' => 'Metformin',      'initial' => 1000, 'remaining' => 680, 'expiry' => '2027-06-30', 'threshold' => 100],
            ['drug' => 'Amlodipine',     'initial' => 500,  'remaining' => 312, 'expiry' => '2027-03-31', 'threshold' => 50],
            ['drug' => 'Atorvastatin',   'initial' => 600,  'remaining' => 445, 'expiry' => '2027-09-30', 'threshold' => 60],
            ['drug' => 'Losartan',       'initial' => 400,  'remaining' => 195, 'expiry' => '2026-12-31', 'threshold' => 50],
            ['drug' => 'Omeprazole',     'initial' => 800,  'remaining' => 520, 'expiry' => '2027-04-30', 'threshold' => 80],
            ['drug' => 'Aspirin',        'initial' => 1000, 'remaining' => 834, 'expiry' => '2027-06-30', 'threshold' => 100],
            ['drug' => 'Paracetamol',    'initial' => 1200, 'remaining' => 940, 'expiry' => '2027-08-31', 'threshold' => 100],
            ['drug' => 'Salbutamol',     'initial' => 50,   'remaining' => 28,  'expiry' => '2026-11-30', 'threshold' => 10],
            ['drug' => 'Amoxicillin',    'initial' => 300,  'remaining' => 42,  'expiry' => '2026-10-31', 'threshold' => 30],  // low stock
            ['drug' => 'Levothyroxine',  'initial' => 200,  'remaining' => 148, 'expiry' => '2027-02-28', 'threshold' => 20],
        ];

        foreach ($stocks as $s) {
            $stock = PharmacyStock::create([
                'unit_view_id'       => $view->id,
                'drug_name'          => $s['drug'],
                'initial_amount'     => $s['initial'],
                'remaining'          => $s['remaining'],
                'expiry_date'        => $s['expiry'],
                'is_out_of_stock'    => false,
                'low_stock_threshold'=> $s['threshold'],
                'created_by'         => $pharmacist->id,
                'updated_by'         => $pharmacist->id,
            ]);

            PharmacyRestockLog::create([
                'unit_view_id' => $view->id,
                'stock_id'     => $stock->id,
                'drug_name'    => $s['drug'],
                'action'       => 'new_stock',
                'amount'       => $s['initial'],
                'expiry_date'  => $s['expiry'],
                'notes'        => 'Initial stock entry',
                'performed_by' => $pharmacist->id,
            ]);
        }

        // One restock for Amoxicillin (was low)
        $amoxStock = PharmacyStock::where('unit_view_id', $view->id)
            ->where('drug_name', 'Amoxicillin')
            ->first();

        if ($amoxStock) {
            $amoxStock->increment('remaining', 200);
            $amoxStock->increment('initial_amount', 200);

            PharmacyRestockLog::create([
                'unit_view_id' => $view->id,
                'stock_id'     => $amoxStock->id,
                'drug_name'    => 'Amoxicillin',
                'action'       => 'restock',
                'amount'       => 200,
                'expiry_date'  => '2027-03-31',
                'notes'        => 'Emergency restock — stock running low',
                'performed_by' => $pharmacist->id,
            ]);
        }

        $this->command->info('Pharmacy stock and restock logs created (THK GP OPD).');
    }
}
