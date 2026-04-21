# OpenHIMS2 — Open Health Information Management System

A free, open-source Health Information Management System built with **Laravel 10** and **MySQL**. Designed for multi-clinic hospital networks, it supports patient registration, clinical workflows (Doctor → Nurse → Pharmacist), drug prescriptions, pharmacy stock management, and medical terminology autocomplete.

---

## Table of Contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [First-Time Setup (Admin)](#first-time-setup-admin)
   - [Create Institutions](#1-create-institutions)
   - [Create Units](#2-create-units)
   - [Create Unit Views](#3-create-unit-views)
   - [Create Users & Assign Views](#4-create-users--assign-views)
   - [Manage Drugs & Defaults](#5-manage-drugs--defaults)
   - [Manage Terminology](#6-manage-terminology)
5. [Daily Clinical Use](#daily-clinical-use)
   - [Clerk Workflow](#clerk-workflow)
   - [Doctor Workflow](#doctor-workflow)
   - [Nurse Workflow](#nurse-workflow)
   - [Pharmacist Workflow](#pharmacist-workflow)
6. [Advanced: Adding New Unit Templates](#advanced-adding-new-unit-templates)
7. [Advanced: Adding New View Templates](#advanced-adding-new-view-templates)
8. [Advanced: Creating New Clinical View Pages](#advanced-creating-new-clinical-view-pages)
9. [Advanced: Adding New Terminology Categories](#advanced-adding-new-terminology-categories)
10. [Advanced: Using Terminology in Blade Views](#advanced-using-terminology-in-blade-views)
11. [Database Schema](#database-schema)
12. [License](#license)

---

## Features

- Multi-clinic, multi-institution hierarchy
- Role-based access: Admin and Clinical staff
- Patient registration with duplicate detection (NIC / mobile)
- Queue management per clinic per day
- Doctor visit notes: complaints, history, examination, investigations, drugs, allergies, BP readings
- Pharmacist stock management with low-stock alerts and expiry tracking
- Prescription dispensing with audit trail
- Medical terminology autocomplete (19 categories)
- Drug name master list with dose/frequency defaults
- Clinic confirmation letters and monthly reports
- Fully local frontend (Bootstrap 5 + Bootstrap Icons, no CDN dependency)

---

## Requirements

| Component | Version |
|-----------|---------|
| PHP | 8.1+ |
| Laravel | 10.x |
| MySQL / MariaDB | 10.4+ |
| Node.js | 16+ (for `npm install` only) |
| Composer | 2.x |

**Recommended:** XAMPP (Apache + MySQL + PHP bundled).

---

## Installation

### Option A — Fresh Laravel Install (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/your-org/openhims2.git
cd openhims2

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies (Bootstrap + Icons)
npm install bootstrap bootstrap-icons

# 4. Copy Bootstrap to public/vendor/ (no Vite build needed)
php artisan app:publish-assets

# 5. Create your environment file
cp .env.example .env
php artisan key:generate

# 6. Configure your database in .env
#    DB_DATABASE=phims
#    DB_USERNAME=root
#    DB_PASSWORD=

# 7. Run migrations and seed default data
php artisan migrate:fresh --seed

# 8. Start the development server
php artisan serve
# → http://127.0.0.1:8000
```

### Option B — Using XAMPP (Windows)

```bash
# Place project in C:\xampp\htdocs\openhims2
# Then access via: http://localhost/openhims2/public

# In .env set:
# DB_HOST=127.0.0.1
# DB_DATABASE=phims
# DB_USERNAME=root
# DB_PASSWORD=

# Then run from the project folder:
composer install
npm install bootstrap bootstrap-icons
php artisan app:publish-assets
php artisan migrate:fresh --seed
```

### Option C — Import from database.sql

If you have the exported `database.sql` file:

```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE phims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import schema + seed data
mysql -u root -p phims < database.sql

# 3. Then run only the AdminSeeder (not migrate:fresh)
php artisan db:seed --class=AdminSeeder
```

---

## First-Time Setup (Admin)

After installation, log in at `/` with:

```
Email:    admin@phims.lk
Password: password
```

> **Change this password immediately** via Admin → Profile.

---

### 1. Create Institutions

**Admin → Institution Hierarchy**

Institutions represent your physical locations. They support a **parent-child hierarchy**:

```
National Department of Health       ← top-level institution
  └─ Northern Regional Authority    ← child
       └─ St. George's Hospital     ← leaf (where units live)
```

Fields:
- **Name** — full institution name
- **Code** — short identifier (e.g., `GMC-AKR`)
- **Parent Institution** — leave blank for top-level
- Email, phone, address (optional)

You can nest as many levels as needed. Only institutions at the lowest level (leaves) should have units attached.

[![Screenshot-2026-04-21-224603.png](https://i.postimg.cc/XNQV6Zj2/Screenshot-2026-04-21-224603.png)](https://postimg.cc/BtPWxn1D)

---

### 2. Create Units

**Admin → Unit Management**

A **Unit** is a physical clinic room/ward within an institution. Each unit is typed by a **Unit Template** (e.g., GMC, DC, GP).

Fields:
- **Unit Name** — e.g., `City General Clinic`
- **Unit Number** — optional identifier
- **Institution** — which institution this unit belongs to
- **Unit Template** — the clinic type (see table below)

| Unit Template | Code | Typical use |
|---------------|------|-------------|
| General Medical Clinic | GMC | OPD / clinic consultations |
| Dental Clinic | DC | Dental consultations |
| General Inward | GI | Ward/inpatient |
| General Pharmacy | GP | Pharmacy dispensing |
| Office | OFFICE | Admin / staff |


[![Screenshot-2026-04-21-224710.png](https://i.postimg.cc/L4NHFq9x/Screenshot-2026-04-21-224710.png)](https://postimg.cc/Lhgdz8ZP)
---

### 3. Create Unit Views

**Admin → View Management**

A **Unit View** is an instance of a role-view at a specific unit. It is the access-control bridge between a user and a unit.

Example: "City General Clinic - Doctor View" is a UnitView that combines:
- Unit: `City General Clinic`
- View Template: `GMC - Doctor View`

**Steps:**
1. Select the Unit
2. Select the View Template (role-based view for that unit type)
3. Give it a name (e.g., `City General Clinic — Doctor`)
4. Save

Available View Templates per Unit Template:

| Unit Template | Available View Templates |
|---------------|--------------------------|
| GMC | Doctor View, Clerk View, Nurse View |
| DC | Doctor View, Clerk View, Nurse View |
| GI | Doctor View, Clerk View, Nurse View |
| GP | Doctor View, Pharmacist View, Clerk View |
| OFFICE | Doctor View, Nurse View, Clerk View |


[![Screenshot-2026-04-21-224729.png](https://i.postimg.cc/7PmxrCHm/Screenshot-2026-04-21-224729.png)](https://postimg.cc/6T4sdpGv)
---

### 4. Create Users & Assign Views

**Admin → User Management**

Fields:
- **Name, Email, Password** — login credentials
- **Role** — `admin` (full admin access) or `user` (clinical access)
- **Institution** — the user's home institution
- **Units** — which physical units this user can access (the unit selection drives which views appear)
- **Views** — which specific Unit Views this user can see (these determine the clinical pages shown at login)

**Important:** A user must have at least one View assigned to log in as clinical staff. A user with zero views will receive an error on login.

**Login behavior:**
- 1 view assigned → lands directly on that view's clinical page
- Multiple views → presented with a selection screen
- Admin role → always goes to `/admin/dashboard`

**AJAX-driven form:** When you pick an Institution, the Units dropdown loads automatically. When you pick Units, the Views checkboxes load automatically.

[![Screenshot-2026-04-21-224805.png](https://i.postimg.cc/pVq2M9WK/Screenshot-2026-04-21-224805.png)](https://postimg.cc/Th58r3WP)

---

### 5. Manage Drugs & Defaults

**Admin → Drug Management**

#### Adding Drug Names

Click **Add Drug** and type the drug name (e.g., `Metformin 500mg`). This creates an entry in the master drug list.

#### Setting Drug Defaults

After adding a drug, click **Set Default** to pre-fill prescribing defaults:

| Field | Options |
|-------|---------|
| Type | Oral, S/C, IM, IV, S/L, Syrup, MDI, DPI, Suppository, LA |
| Dose | Numeric value |
| Unit | mg, g, mcg, ml, tabs, item |
| Frequency | mane, nocte, bd, tds, daily, EOD, SOS |
| Duration | Number of days |

When a doctor searches for this drug during a visit, these defaults auto-fill the prescription form.

[![Screenshot-2026-04-21-224827.png](https://i.postimg.cc/pVq2M9W8/Screenshot-2026-04-21-224827.png)](https://postimg.cc/MXfkypjK)

[![Screenshot-2026-04-21-224854.png](https://i.postimg.cc/59nxWH4L/Screenshot-2026-04-21-224854.png)](https://postimg.cc/dDZM8VTV)

---

### 6. Manage Terminology

**Admin → Terminology**

Terminology terms are grouped into **19 medical categories** used for autocomplete in doctor visit forms.

| Category | Used in doctor form |
|----------|---------------------|
| presenting_complaints | Complaints section |
| complaint_durations | Duration of complaint |
| past_medical_history | Past medical history |
| past_surgical_history | Past surgical history |
| social_history | Social history |
| menstrual_history | Menstrual history |
| investigations | Investigation names |
| general_looking | Examination → General |
| cardiology_findings | Examination → Cardiology |
| respiratory_findings | Examination → Respiratory |
| abdominal_findings | Examination → Abdomen |
| neurological_findings | Examination → Neurology |
| dermatological_findings | Examination → Dermatology |
| differential_diagnosis | Diagnosis section |
| working_diagnosis | Working diagnosis |
| diabetes_instructions | Management instructions |
| hypertension_instructions | Management instructions |
| dyslipidemia_instructions | Management instructions |
| general_instructions | Management instructions |

Click **Add Term** next to a category, type the term, and save. Terms appear as autocomplete suggestions when clinical staff type in those fields.

[![Screenshot-2026-04-21-224918.png](https://i.postimg.cc/wx06Ytq5/Screenshot-2026-04-21-224918.png)](https://postimg.cc/Z0vkr5y0)

---

## Daily Clinical Use

### Clerk Workflow

The **Clerk View** is the front-desk interface for patient registration and queue management.

1. **Register a new patient**: Click `Register Patient` → fill in Name, DOB/Age, Gender, NIC, Mobile, Address → Save.
   - The system checks for duplicates by NIC and mobile number before saving.
2. **Add patient to queue**: From the patient list or search results, click `Add to Queue` → select visit category:
   - **OPD** — general outpatient visit (OPD number, height, weight, BP)
   - **New Clinic Visit** — first clinic attendance (clinic number assigned)
   - **Recurrent Clinic Visit** — follow-up clinic attendance
   - **Urgent** — priority case
3. **Monitor queue**: The queue panel shows all patients waiting, in-progress, and visited today.
4. **Reset queue**: At end of day, use `Reset Queue` to archive the session and start fresh the next day.

   [![Screenshot-2026-04-21-222812.png](https://i.postimg.cc/65qBW9T9/Screenshot-2026-04-21-222812.png)](https://postimg.cc/mhvvVWQq)

   [![Screenshot-2026-04-21-222844.png](https://i.postimg.cc/pXrPW2yN/Screenshot-2026-04-21-222844.png)](https://postimg.cc/jCppvYzv)

   [![Screenshot-2026-04-21-223813.png](https://i.postimg.cc/bNrqyzr7/Screenshot-2026-04-21-223813.png)](https://postimg.cc/Cd33Jpj7)

---

### Doctor Workflow

The **Doctor View** shows today's queue and opens a full visit form when a patient is selected.

1. **Select a patient from queue**: Click on a patient row to open the visit form.
2. **Presenting Complaints**: Add complaints with durations using autocomplete (terminology terms).
3. **History**: Past medical, surgical, social, and menstrual history — all with autocomplete.
4. **Examination**:
   - General looking, pulse rate
   - Cardiovascular, respiratory, abdominal, neurological, dermatological findings
5. **Investigations**: Add investigation name + result (e.g., `FBS: 6.2 mmol/L`).
6. **Blood Pressure**: Add one or more BP readings during the visit.
7. **Allergies**: Add patient allergies (persists across all visits for that patient).
8. **Diagnoses**: Differential and working diagnosis with autocomplete.
9. **Drugs**:
   - Search drug name → defaults auto-fill → adjust dose/frequency/duration if needed
   - Drugs are grouped by section
   - Full audit log of additions, edits, and deletions
10. **Management Instructions**: Free-text plus autocomplete for standard instruction bundles (diabetes, hypertension, etc.).
11. **End Visit**: Click `End Visit` to mark the patient as visited and move them to the pharmacist queue.

    [![Screenshot-2026-04-21-223849.png](https://i.postimg.cc/c4CdxsCV/Screenshot-2026-04-21-223849.png)](https://postimg.cc/Hjqq31Q2)

    [![Screenshot-2026-04-21-224008.png](https://i.postimg.cc/3JN38KNb/Screenshot-2026-04-21-224008.png)](https://postimg.cc/9z661HGG)

    [![Screenshot-2026-04-21-224120.png](https://i.postimg.cc/HsnTYpn6/Screenshot-2026-04-21-224120.png)](https://postimg.cc/23ssTN4W)

    [![Screenshot-2026-04-21-224244.png](https://i.postimg.cc/xT89f08s/Screenshot-2026-04-21-224244.png)](https://postimg.cc/fkGG5QxX)

    [![Screenshot-2026-04-21-224323.png](https://i.postimg.cc/zXvqzJvk/Screenshot-2026-04-21-224323.png)](https://postimg.cc/Dm9953qW)

    [![Screenshot-2026-04-21-230631.png](https://i.postimg.cc/SsQxccQk/Screenshot-2026-04-21-230631.png)](https://postimg.cc/cKjWSg8b)

---

### Nurse Workflow

The **Nurse View** provides a read-access summary of visits.

1. View the patient list for the day
2. Open patient history to see all past visits
3. View visit summaries (read-only — notes, drugs, BP readings, investigations)

The nurse view is primarily for monitoring and clinical handover; it does not modify clinical data.

---

### Pharmacist Workflow

The **Pharmacist View** (GP units only) handles dispensing and stock management.

#### Dispensing

1. The queue shows patients with `visited` status (doctor has finished their assessment)
2. Click a patient to view their full prescription
3. For each prescribed drug, click `Dispense` → select a matching stock item → enter quantity dispensed
4. Mark the visit as `Dispensed` when all drugs have been handled

#### Stock Management

Within the Pharmacist view:

- **Add Stock**: Drug name, initial quantity, expiry date, low-stock threshold
- **Restock**: Add quantity to existing stock (each restock is logged)
- **Mark Out of Stock**: Toggle flag manually
- **View Restock Log**: Full history of additions and adjustments

**Alerts panel** shows:
- Drugs expiring within 30 days
- Drugs below the low-stock threshold
- Drugs marked as out of stock

  [![Screenshot-2026-04-21-224434.png](https://i.postimg.cc/pXrPW2rQ/Screenshot-2026-04-21-224434.png)](https://postimg.cc/9z661HGz)

  [![Screenshot-2026-04-21-224508.png](https://i.postimg.cc/hPvKDSvL/Screenshot-2026-04-21-224508.png)](https://postimg.cc/WtBBScMz)

  [![Screenshot-2026-04-21-224520.png](https://i.postimg.cc/59nxWH47/Screenshot-2026-04-21-224520.png)](https://postimg.cc/nM98vhQB)

---

## Advanced: Adding New Unit Templates

A **Unit Template** defines a clinic type. The 5 built-in templates (GMC, DC, GI, GP, OFFICE) cover most use cases. To add a new one:

### Step 1 — Add to the seeder

In `database/seeders/UnitTemplateSeeder.php`:

```php
UnitTemplate::firstOrCreate(['code' => 'RH'], [
    'name' => 'Rehabilitation Hub',
    'code' => 'RH',
]);
```

Run:
```bash
php artisan db:seed --class=UnitTemplateSeeder
```

### Step 2 — Create View Templates for it

In `database/seeders/ViewTemplateSeeder.php`, add entries that reference the new template:

```php
$rhTemplate = UnitTemplate::where('code', 'RH')->first();

ViewTemplate::firstOrCreate(['code' => 'rh-doctor'], [
    'name'             => 'RH - Doctor View',
    'code'             => 'rh-doctor',
    'blade_path'       => 'clinical.rh.doctor',   // → resources/views/clinical/rh/doctor.blade.php
    'unit_template_id' => $rhTemplate->id,
]);
```

Run:
```bash
php artisan db:seed --class=ViewTemplateSeeder
```

### Step 3 — Create the blade files

Create `resources/views/clinical/rh/doctor.blade.php` (see the next section for the template structure).

### Step 4 — Use it in Admin

After seeding, the new Unit Template appears in Admin → Unit Management when creating a unit. The new View Templates appear in Admin → View Management when creating Unit Views.

---

## Advanced: Adding New View Templates

A **View Template** is a named role-view for a specific Unit Template. Each has a `blade_path` (dot-notation path to the blade file).

### Via Seeder (recommended)

Add to `ViewTemplateSeeder.php`:

```php
ViewTemplate::firstOrCreate(['code' => 'gmc-mo'], [
    'name'             => 'GMC - Medical Officer View',
    'code'             => 'gmc-mo',
    'blade_path'       => 'clinical.gmc.mo',   // → resources/views/clinical/gmc/mo.blade.php
    'unit_template_id' => $gmcTemplate->id,
]);
```

Then run:
```bash
php artisan db:seed --class=ViewTemplateSeeder
```

### Via direct SQL

```sql
INSERT INTO view_templates (name, code, blade_path, unit_template_id, created_at, updated_at)
VALUES ('GMC - MO View', 'gmc-mo', 'clinical.gmc.mo', 1, NOW(), NOW());
```

After adding the template:
1. Go to **Admin → View Management**
2. Create a **Unit View** linking a physical unit to the new view template
3. Assign the Unit View to a user

---

## Advanced: Creating New Clinical View Pages

Each clinical view is a Blade template at the path stored in `view_templates.blade_path` (dot-notation, e.g., `clinical.gmc.doctor` → `resources/views/clinical/gmc/doctor.blade.php`).

### Variables available in every clinical view

`ClinicalDashboardController::show()` injects these into every view:

```php
$unitView       // UnitView model — the specific view instance the user selected
$viewTemplate   // ViewTemplate model — has ->name, ->code, ->blade_path
$unit           // Unit model — has ->name, ->unit_number, ->institution
$institution    // Institution model — has ->name, ->code, ->address, etc.
$user           // The currently authenticated User model
```

### Minimal blade template skeleton

```blade
{{-- resources/views/clinical/rh/doctor.blade.php --}}
@extends('layouts.clinical')
@section('title', $pageTitle ?? 'RH - Doctor View')

@push('styles')
<style>
    :root { --c: #7c3aed; --c-light: #f5f3ff; --c-dark: #6d28d9; }
</style>
@endpush

@section('content')

{{-- Role banner --}}
<div class="p-3 mb-3 rounded-3 text-white" style="background: linear-gradient(135deg, var(--c-dark), var(--c));">
    <div class="d-flex align-items-center gap-3">
        <div>
            <h5 class="mb-0 fw-bold">{{ $viewTemplate->name }}</h5>
            <small class="opacity-75">{{ $unit->name }} · {{ $institution->name }}</small>
        </div>
    </div>
</div>

{{-- Your main content --}}
<div class="card">
    <div class="card-body">
        <p>Logged in as {{ $user->name }}</p>
        {{-- Patient list, queue, forms, etc. --}}
    </div>
</div>

@endsection

@push('scripts')
<script>
    // CSRF token for AJAX requests:
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
</script>
@endpush
```

### Reusable partials

| Partial | Blade path | Purpose |
|---------|------------|---------|
| Patient queue | `clinical.patients._queue` | Today's waiting queue |
| GMC queue | `clinical.gmc._queue` | GMC-styled queue with tabs |
| Doctor queue | `clinical.doctor._queue` | Doctor's queue panel |
| Patient visit list | `clinical.patients._visit_list` | Patient's visit history |
| Nurse visit list | `clinical.nurse._visit_list` | Nurse-styled visit list |

Include a partial:

```blade
@include('clinical.patients._queue', ['unitView' => $unitView])
```

### Useful route helpers inside clinical views

```blade
{{-- Register new patient --}}
<a href="{{ route('clinical.patients.register', $unitView) }}">Register Patient</a>

{{-- Patient list for this unit view --}}
<a href="{{ route('clinical.patients.list', $unitView) }}">All Patients</a>

{{-- Today's queue --}}
<a href="{{ route('clinical.patients.today-queue', $unitView) }}">Today Queue</a>

{{-- Add patient to queue (POST) --}}
<form method="POST" action="{{ route('clinical.patients.add-to-queue', [$unitView, $patient]) }}">
    @csrf
</form>

{{-- Start a doctor visit (POST) --}}
<form method="POST" action="{{ route('clinical.doctor.start-visit', [$unitView, $visit]) }}">
    @csrf
</form>

{{-- Doctor visit form page --}}
<a href="{{ route('clinical.doctor.visit', [$unitView, $visit]) }}">Open Visit</a>
```

---

## Advanced: Adding New Terminology Categories

Terminology categories are defined as a static array in the model. No migration is required.

### Step 1 — Add the category key

In `app/Models/TerminologyTerm.php`, add your new category to `$categories`:

```php
public static array $categories = [
    'presenting_complaints',
    'complaint_durations',
    // ... existing categories ...
    'ophthalmology_findings',   // ← add here
];
```

### Step 2 — The Admin UI picks it up automatically

Admin → Terminology will now show your new category with an "Add Term" button. No further changes needed.

### Step 3 — Optionally seed default terms

```php
// database/seeders/TerminologySeeder.php (create if not exists)
$terms = [
    ['category' => 'ophthalmology_findings', 'term' => 'Normal fundus'],
    ['category' => 'ophthalmology_findings', 'term' => 'Diabetic retinopathy'],
    ['category' => 'ophthalmology_findings', 'term' => 'Hypertensive retinopathy'],
];

foreach ($terms as $term) {
    TerminologyTerm::firstOrCreate($term);
}
```

---

## Advanced: Using Terminology in Blade Views

### The search endpoint

```
GET /terminology/search?category=presenting_complaints&q=fever
Authorization: Any authenticated user (auth middleware)
Response: ["Fever", "Fever with chills", "Fever and headache"]
```

### Full HTML + JS pattern

This is the exact pattern used in the existing doctor views. Copy and adapt it:

```html
<!-- Tag input container -->
<div class="position-relative">
    <input
        type="text"
        class="form-control terminology-ac"
        data-category="ophthalmology_findings"
        placeholder="Type to search findings..."
        autocomplete="off"
    >
    <!-- Autocomplete dropdown (injected by JS) -->
    <ul class="list-group position-absolute w-100 shadow-sm z-3" id="ophthal-suggestions"
        style="display:none; max-height:200px; overflow-y:auto;"></ul>
</div>

<!-- Committed tags display -->
<div id="ophthal-tags" class="d-flex flex-wrap gap-1 mt-2"></div>

<!-- Hidden field — stores JSON array submitted with the form -->
<input type="hidden" name="ophthalmology_findings" id="ophthal-hidden" value="[]">
```

```javascript
// Attach to all terminology autocomplete inputs
document.querySelectorAll('.terminology-ac').forEach(input => {
    const category    = input.dataset.category;
    const suggestBox  = document.getElementById(input.dataset.suggestId ?? category + '-suggestions');
    const tagsBox     = document.getElementById(input.dataset.tagsId ?? category + '-tags');
    const hiddenField = document.getElementById(input.dataset.hiddenId ?? category + '-hidden');

    let selected = JSON.parse(hiddenField.value || '[]');

    function renderTags() {
        tagsBox.innerHTML = selected.map((t, i) =>
            `<span class="badge bg-primary d-flex align-items-center gap-1">
                ${t}
                <button type="button" class="btn-close btn-close-white btn-sm"
                    data-index="${i}" style="font-size:.6rem;"></button>
            </span>`
        ).join('');
        hiddenField.value = JSON.stringify(selected);

        tagsBox.querySelectorAll('.btn-close').forEach(btn => {
            btn.addEventListener('click', () => {
                selected.splice(parseInt(btn.dataset.index), 1);
                renderTags();
            });
        });
    }

    let timer;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 1) { suggestBox.style.display = 'none'; return; }

        timer = setTimeout(() => {
            fetch(`/terminology/search?category=${category}&q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(terms => {
                suggestBox.innerHTML = terms
                    .filter(t => !selected.includes(t))
                    .map(t => `<li class="list-group-item list-group-item-action py-1 px-2"
                                   style="cursor:pointer;">${t}</li>`)
                    .join('') || '<li class="list-group-item py-1 px-2 text-muted">No results</li>';

                suggestBox.style.display = 'block';

                suggestBox.querySelectorAll('li:not(.text-muted)').forEach(li => {
                    li.addEventListener('click', () => {
                        selected.push(li.textContent.trim());
                        renderTags();
                        input.value = '';
                        suggestBox.style.display = 'none';
                    });
                });
            });
        }, 200);
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !suggestBox.contains(e.target)) {
            suggestBox.style.display = 'none';
        }
    });

    renderTags(); // render existing values on page load
});
```

### Saving terminology data in a controller

```php
// In your controller (e.g., DoctorController::saveNotes)
$visit->note()->updateOrCreate(
    ['visit_id' => $visit->id],
    [
        'ophthalmology_findings' => $request->input('ophthalmology_findings', []),
        // The input arrives as a JSON string from the hidden field;
        // Laravel's json cast in the model handles decode automatically.
    ]
);
```

### Adding a new JSON field to visit_notes

1. Create a migration:

```bash
php artisan make:migration add_ophthalmology_findings_to_visit_notes_table
```

```php
// In the migration up() method:
$table->json('ophthalmology_findings')->nullable()->after('dermatological_findings');
```

2. Add to `VisitNote` model (`app/Models/VisitNote.php`):

```php
protected $fillable = [
    // ... existing fields ...
    'ophthalmology_findings',
];

protected $casts = [
    // ... existing casts ...
    'ophthalmology_findings' => 'array',
];
```

3. Run the migration:

```bash
php artisan migrate
```

### Reading saved terminology data

```php
$note = $visit->note;

// Returns an array: ["Normal fundus", "Diabetic retinopathy"]
$findings = $note->ophthalmology_findings ?? [];

foreach ($findings as $finding) {
    echo $finding;
}
```

In a blade view:

```blade
@if($visit->note && $visit->note->ophthalmology_findings)
    <ul>
        @foreach($visit->note->ophthalmology_findings as $finding)
            <li>{{ $finding }}</li>
        @endforeach
    </ul>
@endif
```

---

## Database Schema

The file `database.sql` in this repository contains the full table structure and required seed data for `unit_templates` and `view_templates`.

### Quick import

```bash
mysql -u root -p -e "CREATE DATABASE phims;"
mysql -u root -p phims < database.sql
php artisan db:seed --class=AdminSeeder
```

### Core table relationships

```
institutions (id, name, parent_id, code, email, phone, address)
    └── units (id, name, unit_number, institution_id, unit_template_id)
            └── unit_views (id, name, unit_id, view_template_id)
                    └── [pivot] user_views (user_id, unit_view_id)

unit_templates (id, name, code)                              ← seeded, static
    └── view_templates (id, name, code, blade_path, unit_template_id)  ← seeded, static

users (id, name, email, role, institution_id, ...)
    ├── [pivot] user_units (user_id, unit_id)
    └── [pivot] user_views (user_id, unit_view_id)

patients (id, name, dob, nic, mobile, phn, address)
    ├── patient_allergies (id, patient_id, allergen)
    └── clinic_visits (id, patient_id, unit_id, visit_date, category, status)
            ├── visit_notes (id, visit_id, presenting_complaints JSON, ...)
            ├── blood_pressure_readings (id, visit_id, systolic, diastolic)
            ├── investigations (id, visit_id, name, value)
            ├── visit_drugs (id, visit_id, type, name, dose, frequency, duration)
            │       └── prescription_dispensings (id, visit_drug_id, stock_id, qty)
            └── visit_drug_changes (id, visit_id, action, old_values, new_values)

drug_names (id, name)
    └── drug_name_defaults (id, drug_name_id, type, dose, unit, frequency, duration)

pharmacy_stock (id, unit_view_id, drug_name, remaining, expiry_date, threshold)
    └── pharmacy_restock_logs (id, stock_id, action, amount, performed_by)

terminology_terms (id, category, term)
```

### Visit categories

| Category value | Description |
|----------------|-------------|
| `opd` | General OPD visit |
| `new_clinic_visit` | First clinic attendance |
| `recurrent_clinic_visit` | Follow-up clinic visit |
| `urgent` | Urgent / priority case |

### Visit statuses

| Status | Meaning |
|--------|---------|
| `waiting` | In queue, not yet seen |
| `in_progress` | Currently with doctor |
| `visited` | Doctor done, awaiting pharmacy |
| `dispensed` | Pharmacy dispensed |
| `cancelled` | Removed from queue |

---

## License

OpenHIMS2 is open-source software licensed under the [MIT License](LICENSE).

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

When contributing a new clinical view template:
1. Add a seeder entry in `ViewTemplateSeeder.php`
2. Create the blade file under `resources/views/clinical/`
3. Update this README with the new template details
