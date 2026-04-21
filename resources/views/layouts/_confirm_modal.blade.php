{{-- ============================================================ --}}
{{-- Global confirm dialog — included in admin & clinical layouts --}}
{{-- Usage: confirmDialog({title, body, confirmText, confirmClass, icon}, callback) --}}
{{-- Shorthand: confirmDialog('Are you sure?', callback) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-1">
                <div class="d-flex align-items-center gap-2">
                    <i id="confirm-modal-icon" class="fs-4"></i>
                    <h6 class="modal-title fw-bold mb-0" id="confirm-modal-title">Confirm</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-1 pb-3 text-muted" id="confirm-modal-body"></div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm px-4"
                        id="confirm-modal-btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modalEl    = document.getElementById('confirmModal');
    const bsModal    = new bootstrap.Modal(modalEl);
    const titleEl    = document.getElementById('confirm-modal-title');
    const iconEl     = document.getElementById('confirm-modal-icon');
    const bodyEl     = document.getElementById('confirm-modal-body');
    const confirmBtn = document.getElementById('confirm-modal-btn');

    let _pendingCallback = null;
    let _confirmed       = false;

    confirmBtn.addEventListener('click', function () {
        _confirmed = true;
        bsModal.hide();
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (_confirmed && _pendingCallback) {
            _pendingCallback();
        }
        _pendingCallback = null;
        _confirmed       = false;
    });

    // Remove any stuck backdrop when the page is shown (handles bfcache restores)
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.overflow     = '';
        document.body.style.paddingRight = '';
    });

    /**
     * Show a styled confirmation modal.
     *
     * @param {string|object} opts  - Message string OR options object:
     *   opts.title        (string)  Modal heading          default: 'Confirm'
     *   opts.body         (string)  Body text              default: 'Are you sure?'
     *   opts.confirmText  (string)  Confirm button label   default: 'Confirm'
     *   opts.confirmClass (string)  Bootstrap btn class    default: 'btn-danger'
     *   opts.icon         (string)  Bootstrap icon class   default: 'bi-exclamation-triangle-fill text-warning'
     * @param {function} callback   - Called only when the user clicks Confirm.
     */
    window.confirmDialog = function (opts, callback) {
        if (typeof opts === 'string') {
            opts = { body: opts };
        }

        titleEl.textContent    = opts.title        ?? 'Confirm';
        bodyEl.textContent     = opts.body         ?? 'Are you sure?';
        iconEl.className       = 'bi ' + (opts.icon ?? 'bi-exclamation-triangle-fill text-warning') + ' fs-4';
        confirmBtn.className   = 'btn btn-sm px-4 ' + (opts.confirmClass ?? 'btn-danger');
        confirmBtn.textContent = opts.confirmText  ?? 'Confirm';

        _pendingCallback = callback;
        _confirmed       = false;
        bsModal.show();
    };
})();
</script>
