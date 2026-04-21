@extends('layouts.clinical')

@section('title', 'My Profile')

@push('styles')
<style>
.profile-page { max-width: 880px; margin: 0 auto; }

/* ── Avatar ── */
.avatar-wrap {
    position: relative;
    width: 110px;
    height: 110px;
    margin: 0 auto .9rem;
}
.avatar-circle {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    border: 3px solid rgba(255,255,255,.45);
    overflow: hidden;
    background: rgba(255,255,255,.18);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.6rem;
    font-weight: 700;
    color: #fff;
}
.avatar-circle img { width:100%; height:100%; object-fit:cover; }
.avatar-cam-btn {
    position: absolute;
    bottom: 3px;
    right: 3px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #fff;
    color: #1c3561;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: .8rem;
    box-shadow: 0 1px 6px rgba(0,0,0,.25);
    transition: background .15s;
}
.avatar-cam-btn:hover { background: #e9ecef; }

/* ── Card ── */
.profile-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 16px rgba(0,0,0,.08);
    overflow: hidden;
}
.profile-header {
    background: linear-gradient(135deg, #1c3561 0%, #2a5298 100%);
    padding: 2rem 2rem 1.25rem;
    color: #fff;
    text-align: center;
}
.role-badge {
    display: inline-block;
    background: rgba(255,255,255,.18);
    border-radius: 20px;
    padding: .2rem .85rem;
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .07em;
    text-transform: uppercase;
    margin-top: .4rem;
}

/* ── Tabs ── */
.profile-tabs {
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
    padding: 0 1.25rem;
    display: flex;
    gap: .25rem;
}
.profile-tabs .ptab {
    padding: .7rem 1rem;
    border: none;
    border-bottom: 3px solid transparent;
    background: transparent;
    color: #6c757d;
    font-size: .8125rem;
    font-weight: 500;
    cursor: pointer;
    transition: color .15s, border-color .15s;
    white-space: nowrap;
}
.profile-tabs .ptab:hover { color: #1c3561; }
.profile-tabs .ptab.active { color: #1c3561; border-bottom-color: #1c3561; }
.profile-tabs .ptab i { font-size: .9rem; }

.ptab-body { display: none; padding: 1.75rem; }
.ptab-body.active { display: block; }

/* ── Form ── */
.form-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
.form-control, .form-select { font-size: .875rem; }
.sec-title {
    font-size: .63rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #9ca3af;
    padding-bottom: .4rem;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: .9rem;
}
.strength-track { height: 4px; background: #e9ecef; border-radius: 2px; }
.strength-fill  { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
</style>
@endpush

@section('content')
<div class="profile-page">

    {{-- Back button + heading --}}
    <div class="d-flex align-items-center mb-3">
        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('clinical.dashboard') }}"
           class="btn btn-sm btn-outline-secondary me-3">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <h5 class="mb-0 fw-semibold">My Profile</h5>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 py-2" id="profileSuccessAlert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="profile-card">

        {{-- ── HEADER ── --}}
        <div class="profile-header">
            <div class="avatar-wrap" id="avatarWrap">
                <div class="avatar-circle" id="avatarCircle">
                    @if($user->profileImageUrl())
                        <img src="{{ $user->profileImageUrl() }}" id="avatarImg" alt="Profile photo">
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <button type="button" class="avatar-cam-btn" id="avatarCamBtn" title="Upload photo">
                    <i class="bi bi-camera-fill"></i>
                </button>
            </div>

            <h5 class="mb-0 fw-bold">
                {{ $user->designation ? $user->designation . ' ' : '' }}{{ $user->name }}
            </h5>
            @if($user->specialty)
                <div class="mt-1" style="font-size:.83rem; opacity:.85;">{{ $user->specialty }}</div>
            @endif
            <span class="role-badge">{{ $user->isAdmin() ? 'Administrator' : 'Clinical Staff' }}</span>
            @if($user->institution)
                <div class="mt-2" style="font-size:.76rem; opacity:.72;">
                    <i class="bi bi-building me-1"></i>{{ $user->institution->name }}
                </div>
            @endif
        </div>

        {{-- ── TABS ── --}}
        <div class="profile-tabs" role="tablist">
            <button class="ptab active" data-tab="personal"><i class="bi bi-person-fill me-1"></i>Personal</button>
            <button class="ptab" data-tab="professional"><i class="bi bi-briefcase-fill me-1"></i>Professional</button>
            <button class="ptab" data-tab="contact"><i class="bi bi-telephone-fill me-1"></i>Contact</button>
            <button class="ptab" data-tab="password"><i class="bi bi-shield-lock-fill me-1"></i>Password</button>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 1 — PERSONAL  (has file upload → enctype required)
        ══════════════════════════════════════════════════════════ --}}
        <div class="ptab-body active" id="tab-personal">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Hidden file input lives here so it's inside the form with enctype --}}
                <input type="file" id="profileImageInput" name="profile_image"
                       accept="image/jpeg,image/png,image/webp" class="d-none">

                @if($errors->hasAny(['name','email','dob','gender','bio','profile_image']))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        @foreach($errors->only(['name','email','dob','gender','bio','profile_image']) as $field => $msgs)
                            @foreach($msgs as $m)<div>{{ $m }}</div>@endforeach
                        @endforeach
                    </div>
                @endif

                <p class="sec-title">Personal Information</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required maxlength="255">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required maxlength="255">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob"
                               class="form-control @error('dob') is-invalid @enderror"
                               value="{{ old('dob', $user->dob?->format('Y-m-d')) }}">
                        @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">— Select —</option>
                            <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Age</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ $user->dob ? $user->dob->age . ' years old' : '—' }}" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio / About Me</label>
                        <textarea name="bio" rows="4"
                                  class="form-control @error('bio') is-invalid @enderror"
                                  placeholder="Write a short bio about yourself…"
                                  maxlength="1000" id="bioTextarea">{{ old('bio', $user->bio) }}</textarea>
                        <div class="d-flex justify-content-end mt-1">
                            <small class="text-muted" id="bioCount">{{ strlen(old('bio', $user->bio ?? '')) }}/1000</small>
                        </div>
                        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        <i class="bi bi-image me-1"></i>
                        Use the <strong>camera icon</strong> on your photo to upload a new profile picture.
                    </small>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Save Personal Info
                    </button>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 2 — PROFESSIONAL
        ══════════════════════════════════════════════════════════ --}}
        <div class="ptab-body" id="tab-professional">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf

                @if($errors->hasAny(['designation','specialty','qualification','registration_no']))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the errors below.
                    </div>
                @endif

                {{-- Required fields carried over silently --}}
                <input type="hidden" name="name"  value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">

                <p class="sec-title">Professional Details</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Title / Designation</label>
                        <select name="designation" class="form-select @error('designation') is-invalid @enderror">
                            <option value="">— None —</option>
                            @foreach(['Dr.','Prof.','Mr.','Mrs.','Ms.','Rev.'] as $d)
                                <option value="{{ $d }}" {{ old('designation', $user->designation) === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Specialty / Role</label>
                        <input type="text" name="specialty"
                               class="form-control @error('specialty') is-invalid @enderror"
                               placeholder="e.g. General Medicine, Cardiology, Staff Nurse…"
                               value="{{ old('specialty', $user->specialty) }}" maxlength="100">
                        @error('specialty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Qualifications</label>
                        <input type="text" name="qualification"
                               class="form-control @error('qualification') is-invalid @enderror"
                               placeholder="e.g. MBBS, MD (Medicine), MRCP (UK) — separate with commas"
                               value="{{ old('qualification', $user->qualification) }}" maxlength="200">
                        @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Registration No.</label>
                        <input type="text" name="registration_no"
                               class="form-control @error('registration_no') is-invalid @enderror"
                               placeholder="SLMC / professional reg."
                               value="{{ old('registration_no', $user->registration_no) }}" maxlength="50">
                        @error('registration_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if($user->qualification || $user->registration_no || $user->specialty)
                <div class="mt-4 p-3 rounded" style="background:#f8f9fa; border:1px solid #e9ecef; font-size:.84rem;">
                    <p class="sec-title mb-2">Currently on Record</p>
                    <div class="d-flex gap-4 flex-wrap">
                        @if($user->designation)
                            <div><i class="bi bi-person-badge-fill text-primary me-1"></i><strong>Title:</strong> {{ $user->designation }}</div>
                        @endif
                        @if($user->specialty)
                            <div><i class="bi bi-star-fill text-warning me-1"></i><strong>Specialty:</strong> {{ $user->specialty }}</div>
                        @endif
                        @if($user->qualification)
                            <div><i class="bi bi-mortarboard-fill text-success me-1"></i><strong>Qualifications:</strong> {{ $user->qualification }}</div>
                        @endif
                        @if($user->registration_no)
                            <div><i class="bi bi-card-text text-info me-1"></i><strong>Reg. No:</strong> {{ $user->registration_no }}</div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Save Professional Info
                    </button>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 3 — CONTACT
        ══════════════════════════════════════════════════════════ --}}
        <div class="ptab-body" id="tab-contact">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <input type="hidden" name="name"  value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">

                @if($errors->hasAny(['phone','address']))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Please fix the errors below.
                    </div>
                @endif

                <p class="sec-title">Contact Information</p>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Phone / Mobile</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone-fill text-muted"></i></span>
                            <input type="tel" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="+94 77 000 0000"
                                   value="{{ old('phone', $user->phone) }}" maxlength="20">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Residential Address</label>
                        <textarea name="address" rows="3"
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="No., Street, City, Province…"
                                  maxlength="500">{{ old('address', $user->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if($user->phone || $user->address)
                <div class="mt-4 p-3 rounded" style="background:#eff6ff; border:1px solid #bfdbfe; font-size:.84rem;">
                    <p class="sec-title mb-2">Saved Contact</p>
                    @if($user->phone)
                        <div class="mb-1"><i class="bi bi-telephone-fill text-primary me-2"></i>{{ $user->phone }}</div>
                    @endif
                    @if($user->address)
                        <div><i class="bi bi-geo-alt-fill text-danger me-2"></i>{{ $user->address }}</div>
                    @endif
                </div>
                @endif

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Save Contact Info
                    </button>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             TAB 4 — PASSWORD
        ══════════════════════════════════════════════════════════ --}}
        <div class="ptab-body" id="tab-password">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf

                @if($errors->hasAny(['current_password','password']))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        @foreach($errors->only(['current_password','password']) as $field => $msgs)
                            @foreach($msgs as $m)<div>{{ $m }}</div>@endforeach
                        @endforeach
                    </div>
                @endif

                <p class="sec-title">Change Password</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="currentPwd"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="current-password">
                            <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="currentPwd">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="newPwd"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password" minlength="8"
                                   oninput="checkStrength(this.value)">
                            <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="newPwd">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="strength-track mt-2">
                            <div class="strength-fill" id="strengthFill" style="width:0;"></div>
                        </div>
                        <small id="strengthLabel" class="text-muted" style="font-size:.75rem; min-height:1.2em; display:block;"></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="confirmPwd"
                                   class="form-control" autocomplete="new-password" minlength="8"
                                   oninput="checkMatch()">
                            <button type="button" class="btn btn-outline-secondary toggle-pwd" data-target="confirmPwd">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                        <small id="matchLabel" style="font-size:.75rem; min-height:1.2em; display:block;"></small>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded" style="background:#fff8f0; border:1px solid #fed7aa; font-size:.8125rem;">
                            <i class="bi bi-info-circle-fill text-warning me-1"></i>
                            Minimum <strong>8 characters</strong>. Mix uppercase, lowercase, numbers and symbols for a stronger password.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning px-4 fw-semibold">
                        <i class="bi bi-shield-lock-fill me-1"></i>Change Password
                    </button>
                </div>
            </form>
        </div>

    </div>{{-- .profile-card --}}
</div>
@endsection

@push('scripts')
<script>
// ── Auto-dismiss success alert after 3 s ──
(function () {
    const alert = document.getElementById('profileSuccessAlert');
    if (alert) {
        setTimeout(function () {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }, 3000);
    }
})();

// ── Custom tab switching ──
document.querySelectorAll('.ptab').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.ptab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.ptab-body').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// ── Jump to password tab from URL fragment ──
(function () {
    if (window.location.hash === '#tab-password') {
        document.querySelector('[data-tab="password"]').click();
    }
})();

// ── Camera button triggers file input inside personal tab form ──
document.getElementById('avatarCamBtn').addEventListener('click', function () {
    // Switch to personal tab so the file input is reachable
    document.querySelector('[data-tab="personal"]').click();
    document.getElementById('profileImageInput').click();
});

// ── Preview uploaded profile image ──
document.getElementById('profileImageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const circle = document.getElementById('avatarCircle');
        circle.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        // Update navbar avatar too
        const nbAvatar = document.querySelector('.nb-avatar');
        if (nbAvatar) {
            nbAvatar.style.background = 'transparent';
            nbAvatar.style.border = 'none';
            nbAvatar.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">';
        }
    };
    reader.readAsDataURL(file);
});

// ── Bio counter ──
const bioEl = document.getElementById('bioTextarea');
const bioCountEl = document.getElementById('bioCount');
if (bioEl && bioCountEl) {
    bioEl.addEventListener('input', function () {
        bioCountEl.textContent = this.value.length + '/1000';
    });
}

// ── Toggle password visibility ──
document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const inp = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'bi bi-eye-slash-fill';
        } else {
            inp.type = 'password';
            icon.className = 'bi bi-eye-fill';
        }
    });
});

// ── Password strength ──
function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!val) { fill.style.width = '0'; fill.style.background = ''; label.textContent = ''; return; }
    let s = 0;
    if (val.length >= 8)  s++;
    if (val.length >= 12) s++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) s++;
    if (/\d/.test(val)) s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    const lvls = [
        { w:'20%', c:'#ef4444', t:'Very weak' },
        { w:'40%', c:'#f97316', t:'Weak' },
        { w:'60%', c:'#eab308', t:'Fair' },
        { w:'80%', c:'#22c55e', t:'Strong' },
        { w:'100%',c:'#16a34a', t:'Very strong' },
    ];
    const lvl = lvls[Math.min(s - 1, 4)] || lvls[0];
    fill.style.width = lvl.w;
    fill.style.background = lvl.c;
    label.style.color = lvl.c;
    label.textContent = lvl.t;
    checkMatch();
}

// ── Password match check ──
function checkMatch() {
    const newVal  = document.getElementById('newPwd').value;
    const confVal = document.getElementById('confirmPwd').value;
    const label   = document.getElementById('matchLabel');
    if (!confVal) { label.textContent = ''; return; }
    if (newVal === confVal) {
        label.style.color = '#16a34a';
        label.textContent = '✓ Passwords match';
    } else {
        label.style.color = '#ef4444';
        label.textContent = '✗ Passwords do not match';
    }
}
</script>
@endpush
