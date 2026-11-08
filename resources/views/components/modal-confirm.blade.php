<!-- Modal Konfirmasi Universal HAFIS (Aksesibilitas WCAG 2.2 & Theme HAFIS) -->
<div id="hafis-confirm-modal" 
     class="hidden fixed inset-0 z-60 overflow-y-auto overscroll-contain p-4 flex items-center justify-center" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="hafis-confirm-title" 
     aria-describedby="hafis-confirm-message">
     
    <!-- Backdrop Gelap -->
    <div id="hafis-confirm-backdrop" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>

    <!-- Kotak Dialog Panel -->
    <div id="hafis-confirm-panel" class="relative bg-white rounded-2xl border border-gray-200 shadow-2xl w-full max-w-md p-5 sm:p-6 overflow-hidden transform transition-all duration-150 motion-reduce:transition-none motion-safe:scale-100">
        <div class="flex items-start gap-3.5">
            <!-- Icon Wrapper Dinamis (Optically Aligned) -->
            <div id="hafis-confirm-icon-box" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border">
                <!-- Icon Logout / Sesi (Door Exit) -->
                <svg id="hafis-confirm-icon-logout" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <!-- Icon Danger (Trash/Delete) -->
                <svg id="hafis-confirm-icon-danger" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <!-- Icon Warning (Exclamation/Session/Access) -->
                <svg id="hafis-confirm-icon-warning" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <!-- Icon Success / Primary (Check/Complete) -->
                <svg id="hafis-confirm-icon-success" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <div class="min-w-0 flex-1 pt-0.5">
                <h3 id="hafis-confirm-title" class="text-sm sm:text-base font-semibold text-gray-900 tracking-tight leading-snug">
                    Konfirmasi Tindakan
                </h3>
                <p id="hafis-confirm-message" class="text-xs text-gray-600 mt-1 leading-relaxed">
                    Apakah Anda yakin ingin melanjutkan tindakan ini?
                </p>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
            <button type="button" 
                    id="hafis-confirm-btn-cancel" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900 text-xs font-semibold min-h-[40px] cursor-pointer transition-colors shadow-2xs">
                Batal
            </button>
            <button type="button" 
                    id="hafis-confirm-btn-submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 text-xs font-semibold min-h-[40px] cursor-pointer transition-colors shadow-xs motion-safe:active:scale-[0.98] motion-reduce:transform-none">
                Lanjutkan
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        let activeCallback = null;
        let lastFocusedElement = null;

        const modal = document.getElementById('hafis-confirm-modal');
        const backdrop = document.getElementById('hafis-confirm-backdrop');
        const iconBox = document.getElementById('hafis-confirm-icon-box');
        const iconLogout = document.getElementById('hafis-confirm-icon-logout');
        const iconDanger = document.getElementById('hafis-confirm-icon-danger');
        const iconWarning = document.getElementById('hafis-confirm-icon-warning');
        const iconSuccess = document.getElementById('hafis-confirm-icon-success');
        const titleEl = document.getElementById('hafis-confirm-title');
        const messageEl = document.getElementById('hafis-confirm-message');
        const btnCancel = document.getElementById('hafis-confirm-btn-cancel');
        const btnSubmit = document.getElementById('hafis-confirm-btn-submit');

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            activeCallback = null;
            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }

        window.hafisConfirm = function (options) {
            if (!modal) return;

            const config = Object.assign({
                title: 'Konfirmasi Tindakan',
                message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                confirmText: 'Lanjutkan',
                cancelText: 'Batal',
                type: 'danger', // 'danger' | 'warning' | 'logout' | 'primary' | 'success'
                onConfirm: null
            }, options);

            lastFocusedElement = document.activeElement;
            activeCallback = config.onConfirm;

            titleEl.textContent = config.title;
            messageEl.textContent = config.message;
            btnCancel.textContent = config.cancelText;
            btnSubmit.textContent = config.confirmText;

            // Reset icon classes
            iconLogout.classList.add('hidden');
            iconDanger.classList.add('hidden');
            iconWarning.classList.add('hidden');
            iconSuccess.classList.add('hidden');

            iconBox.className = 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border';
            btnSubmit.className = 'w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 text-xs font-semibold min-h-[40px] cursor-pointer transition-colors shadow-xs motion-safe:active:scale-[0.98] motion-reduce:transform-none';

            if (config.type === 'danger') {
                iconDanger.classList.remove('hidden');
                iconBox.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-600');
                btnSubmit.classList.add('bg-rose-600', 'hover:bg-rose-700', 'focus-visible:ring-rose-600');
            } else if (config.type === 'logout') {
                iconLogout.classList.remove('hidden');
                iconBox.classList.add('bg-gray-100', 'border-gray-200', 'text-gray-700');
                btnSubmit.classList.add('bg-gray-900', 'hover:bg-black', 'focus-visible:ring-gray-900');
            } else if (config.type === 'warning') {
                iconWarning.classList.remove('hidden');
                iconBox.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-700');
                btnSubmit.classList.add('bg-gray-900', 'hover:bg-black', 'focus-visible:ring-gray-900');
            } else {
                iconSuccess.classList.remove('hidden');
                iconBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
                btnSubmit.classList.add('bg-gray-900', 'hover:bg-black', 'focus-visible:ring-gray-900');
            }

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Default fokus ke tombol Batal untuk keselamatan (prevent accidental Enter)
            setTimeout(function () {
                btnCancel?.focus();
            }, 50);
        };

        btnCancel?.addEventListener('click', closeModal);
        backdrop?.addEventListener('click', closeModal);

        btnSubmit?.addEventListener('click', function () {
            const cb = activeCallback;
            closeModal();
            if (typeof cb === 'function') {
                cb();
            }
        });

        // Keyboard handler: Escape untuk menutup, Tab trap untuk fokus
        modal?.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeModal();
                return;
            }

            if (e.key === 'Tab') {
                const focusables = [btnCancel, btnSubmit].filter(Boolean);
                if (focusables.length < 2) return;

                const first = focusables[0];
                const last = focusables[focusables.length - 1];

                if (e.shiftKey) {
                    if (document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    if (document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }
        });

        // Global declarative form confirmation handler via [data-confirm]
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !form.hasAttribute('data-confirm')) return;

            if (form.dataset.confirmed === 'true') {
                // Sudah dikonfirmasi, izinkan submit berlanjut
                delete form.dataset.confirmed;
                return;
            }

            e.preventDefault();

            const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
            const message = form.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const confirmText = form.getAttribute('data-confirm-text') || 'Lanjutkan';
            const cancelText = form.getAttribute('data-confirm-cancel') || 'Batal';
            const type = form.getAttribute('data-confirm-type') || 'danger';

            window.hafisConfirm({
                title: title,
                message: message,
                confirmText: confirmText,
                cancelText: cancelText,
                type: type,
                onConfirm: function () {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        });
    })();
</script>
