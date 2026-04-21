@extends('layouts.admin')
@section('title', 'Hierarchy Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Hierarchy Management</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"
            data-parent-id="" data-parent-label="Root Level">
        <i class="bi bi-plus-lg me-1"></i>Add Root Institution
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($institutions->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-diagram-3" style="font-size: 3.5rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">No institutions yet. Add the first one above.</p>
            </div>
        @else
            @include('admin.hierarchy._tree', ['institutions' => $institutions, 'depth' => 0])
        @endif
    </div>
</div>

{{-- Add / Add Child Modal --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.hierarchy.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="parent_id" id="addParentId">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Add Institution</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Parent: <strong id="addParentLabel">Root Level</strong>
                    </p>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label form-label-sm mb-1">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Institution name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-sm mb-1">
                                Code <span class="text-danger">*</span>
                                <span class="text-muted fw-normal" style="font-size:.72rem;">(1–3 digits)</span>
                            </label>
                            <input type="number" name="code"
                                   class="form-control form-control-sm font-monospace @error('code') is-invalid @enderror"
                                   placeholder="e.g. 7" min="0" max="999" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text" style="font-size:.7rem;">7 → 007. Used for visit numbers.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="clinic@example.lk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Phone / Contact</label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="+94 11 234 5678">
                        </div>
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Address</label>
                            <textarea name="address" class="form-control form-control-sm" rows="2" placeholder="Street, City, Province"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Logo <span class="text-muted fw-normal" style="font-size:.72rem;">(JPG/PNG, max 2 MB)</span></label>
                            <input type="file" name="logo" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp">
                            <div class="form-text" style="font-size:.7rem;">Used on printed reports and letters.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Add Institution</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title">Edit Institution</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label form-label-sm mb-1">Institution Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-sm mb-1">
                                Code <span class="text-danger">*</span>
                                <span class="text-muted fw-normal" style="font-size:.72rem;">(1–3 digits)</span>
                            </label>
                            <input type="number" name="code" id="editCode"
                                   class="form-control form-control-sm font-monospace"
                                   min="0" max="999" required>
                            <div class="form-text" style="font-size:.7rem;">Changing only affects future visit numbers.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control form-control-sm" placeholder="clinic@example.lk">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-sm mb-1">Phone / Contact</label>
                            <input type="text" name="phone" id="editPhone" class="form-control form-control-sm" placeholder="+94 11 234 5678">
                        </div>
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Address</label>
                            <textarea name="address" id="editAddress" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label form-label-sm mb-1">Logo</label>
                            <div id="editLogoPreview" class="mb-2 d-none">
                                <img id="editLogoImg" src="" alt="Current logo"
                                     style="height:60px; border:1px solid #dee2e6; border-radius:6px; padding:4px; background:#fff;">
                                <span class="text-muted small ms-2">Current logo</span>
                            </div>
                            <input type="file" name="logo" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp">
                            <div class="form-text" style="font-size:.7rem;">Leave blank to keep the existing logo.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-target="#addModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('addParentId').value         = btn.dataset.parentId || '';
        document.getElementById('addParentLabel').textContent = btn.dataset.parentLabel || 'Root Level';
    });
});

document.querySelectorAll('[data-bs-target="#editModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editName').value    = btn.dataset.name;
        document.getElementById('editCode').value    = btn.dataset.code || '';
        document.getElementById('editEmail').value   = btn.dataset.email || '';
        document.getElementById('editPhone').value   = btn.dataset.phone || '';
        document.getElementById('editAddress').value = btn.dataset.address || '';
        document.getElementById('editForm').action   = '{{ url("admin/hierarchy") }}/' + btn.dataset.id;

        // Logo preview
        const logoUrl     = btn.dataset.logoUrl || '';
        const preview     = document.getElementById('editLogoPreview');
        const logoImg     = document.getElementById('editLogoImg');
        if (logoUrl) {
            logoImg.src = logoUrl;
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }
    });
});
</script>
@endpush
