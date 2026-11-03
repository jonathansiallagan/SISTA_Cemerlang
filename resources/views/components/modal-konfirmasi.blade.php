<dialog id="globalConfirmModal"
    class="m-auto w-[calc(100%-2rem)] max-w-sm rounded-xl bg-white p-0 shadow-2xl backdrop:bg-black/50 backdrop:backdrop-blur-sm">

    <form id="globalConfirmForm" method="POST" action="">
        @csrf
        <input type="hidden" name="_method" id="globalConfirmMethod" value="POST">

        <div class="overflow-hidden">
            <div class="pt-6 flex justify-center">
                <div id="globalConfirmIconBg" class="w-12 h-12 rounded-xl flex items-center justify-center">
                    <i id="globalConfirmIcon" class="text-xl"></i>
                </div>
            </div>

            <div class="px-6 pt-4 pb-6 text-center">
                <h2 id="globalConfirmTitle" class="text-lg font-bold text-gray-900">Konfirmasi</h2>
                <p id="globalConfirmMessage" class="mt-2 text-[13px] leading-relaxed text-gray-500">
                    Apakah Anda yakin?
                </p>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-5 py-3">
                <button type="button" id="globalConfirmCancelBtn"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-200">
                    Batal
                </button>
                <button type="submit" id="globalConfirmSubmitBtn"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-semibold text-white transition">
                    <span id="globalConfirmSubmitText">Ya, Lanjutkan</span>
                </button>
            </div>
        </div>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('globalConfirmModal');
        const form = document.getElementById('globalConfirmForm');
        const methodInput = document.getElementById('globalConfirmMethod');
        const title = document.getElementById('globalConfirmTitle');
        const message = document.getElementById('globalConfirmMessage');
        const iconBg = document.getElementById('globalConfirmIconBg');
        const icon = document.getElementById('globalConfirmIcon');
        const submitBtn = document.getElementById('globalConfirmSubmitBtn');
        const submitText = document.getElementById('globalConfirmSubmitText');
        const cancelBtn = document.getElementById('globalConfirmCancelBtn');

        let targetFormId = null;

        const closeModal = () => modal.close();
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        document.body.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-confirm-url], [data-confirm-form]');
            if (!trigger) return;

            targetFormId = trigger.getAttribute('data-confirm-form');

            if (targetFormId) {
                const realForm = document.getElementById(targetFormId);

                // Jika form ditemukan, cek validasinya
                if (realForm) {
                    if (!realForm.checkValidity()) {
                        // Jika tidak valid, tampilkan pesan peringatan browser & batalkan modal
                        realForm.reportValidity();
                        return;
                    }
                }
            }

            e.preventDefault();

            // Ambil data
            targetFormId = trigger.getAttribute('data-confirm-form');
            const url = trigger.getAttribute('data-confirm-url');
            const method = (trigger.getAttribute('data-confirm-method') || 'POST').toUpperCase();

            const textTitle = trigger.getAttribute('data-confirm-title') || 'Konfirmasi';
            const textMessage = trigger.getAttribute('data-confirm-message') || 'Apakah Anda yakin?';
            const textAction = trigger.getAttribute('data-confirm-action') || 'Ya, Lanjutkan';
            const theme = trigger.getAttribute('data-confirm-theme') || 'primary';

            // Ambil kustomisasi ikon jika ada
            const customIcon = trigger.getAttribute('data-confirm-icon');
            const customBtnIcon = trigger.getAttribute('data-confirm-btn-icon');

            if (!targetFormId) {
                form.action = url;
                methodInput.value = method;
            } else {
                form.action = "javascript:void(0);";
            }

            title.textContent = textTitle;
            message.innerHTML = textMessage;

            // Reset Kelas CSS Dasar
            iconBg.className = 'w-12 h-12 rounded-xl flex items-center justify-center';
            icon.className = 'text-xl fas';
            submitBtn.className =
                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-semibold text-white transition';

            let mainIconClass = '';
            let btnIconClass = '';

            // Terapkan Tema dan Ikon Bawaan
            if (theme === 'danger') {
                iconBg.classList.add('bg-red-50');
                icon.classList.add('text-red-500');
                submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                mainIconClass = 'fa-exclamation-triangle';
                btnIconClass = 'fa-trash';
            } else if (theme === 'success') {
                iconBg.classList.add('bg-emerald-50');
                icon.classList.add('text-emerald-500');
                submitBtn.classList.add('bg-emerald-500', 'hover:bg-emerald-600');
                mainIconClass = 'fa-check-circle';
                btnIconClass = 'fa-save';
            } else if (theme === 'warning') {
                iconBg.classList.add('bg-orange-50');
                icon.classList.add('text-orange-500');
                submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                mainIconClass = 'fa-exclamation-circle';
                btnIconClass = 'fa-exclamation-triangle';
            } else if (theme === 'logout') {
                iconBg.classList.add('bg-red-50');
                icon.classList.add('text-red-500');
                submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                mainIconClass = 'fa-sign-out-alt';
                btnIconClass = 'fa-sign-out-alt';
            } else {
                iconBg.classList.add('bg-blue-50');
                icon.classList.add('text-blue-500');
                submitBtn.classList.add('bg-[#6777ef]', 'hover:bg-[#394eea]');
                mainIconClass = 'fa-info-circle';
                btnIconClass = 'fa-check';
            }

            // Timpa dengan ikon kustom jika atribut disertakan di tombol
            if (customIcon) mainIconClass = customIcon;
            if (customBtnIcon) btnIconClass = customBtnIcon;

            // Render ikon
            icon.classList.add(...mainIconClass.split(' '));
            submitBtn.innerHTML =
                `<i class="fas ${btnIconClass} mr-1"></i> <span>${textAction}</span>`;

            modal.showModal();
        });

        form.addEventListener('submit', (e) => {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-1"></i> <span>Memproses...</span>';

            if (targetFormId) {
                e.preventDefault();
                const realForm = document.getElementById(targetFormId);
                if (realForm) realForm.submit();
            }
        });
    });
</script>
