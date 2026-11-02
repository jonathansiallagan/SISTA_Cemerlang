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

        let targetFormId = null; // Menyimpan ID form jika mode simpan

        const closeModal = () => modal.close();
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Dengarkan klik pada tombol ber-atribut data-confirm-url ATAU data-confirm-form
        document.body.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-confirm-url], [data-confirm-form]');
            if (!trigger) return;

            e.preventDefault();

            // Ambil data
            targetFormId = trigger.getAttribute('data-confirm-form'); // Khusus form
            const url = trigger.getAttribute('data-confirm-url'); // Khusus aksi URL
            const method = (trigger.getAttribute('data-confirm-method') || 'POST').toUpperCase();

            const textTitle = trigger.getAttribute('data-confirm-title') || 'Konfirmasi';
            const textMessage = trigger.getAttribute('data-confirm-message') || 'Apakah Anda yakin?';
            const textAction = trigger.getAttribute('data-confirm-action') || 'Ya, Lanjutkan';
            const theme = trigger.getAttribute('data-confirm-theme') || 'primary';

            // Set Form Data di Modal
            if (!targetFormId) {
                form.action = url;
                methodInput.value = method;
            } else {
                form.action = "javascript:void(0);"; // Jangan submit internal jika ini mode simpan form
            }

            title.textContent = textTitle;
            message.innerHTML = textMessage;
            submitText.textContent = textAction;

            // Terapkan Tema
            iconBg.className = 'w-12 h-12 rounded-xl flex items-center justify-center';
            icon.className = 'text-xl fa-solid';
            submitBtn.className =
                'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-xs font-semibold text-white transition';

            if (theme === 'danger') {
                iconBg.classList.add('bg-red-50');
                icon.classList.add('text-red-500', 'fa-triangle-exclamation');
                submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                submitBtn.innerHTML =
                    `<i class="fa-solid fa-trash mr-1"></i> <span>${textAction}</span>`;
            } else if (theme === 'success') {
                iconBg.classList.add('bg-emerald-50');
                icon.classList.add('text-emerald-500', 'fa-circle-check');
                submitBtn.classList.add('bg-emerald-500', 'hover:bg-emerald-600');
                submitBtn.innerHTML =
                `<i class="fa-solid fa-save mr-1"></i> <span>${textAction}</span>`;
            } else {
                iconBg.classList.add('bg-blue-50');
                icon.classList.add('text-blue-500', 'fa-circle-info');
                submitBtn.classList.add('bg-[#6777ef]', 'hover:bg-[#394eea]');
                submitBtn.innerHTML =
                    `<i class="fa-solid fa-check mr-1"></i> <span>${textAction}</span>`;
            }

            modal.showModal();
        });

        // Tangani aksi konfirmasi akhir
        form.addEventListener('submit', (e) => {
            submitBtn.disabled = true;
            submitBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-1"></i> <span>Memproses...</span>';

            // Jika ini form simpan, cegah modal submit sendiri, lalu eksekusi form aslinya
            if (targetFormId) {
                e.preventDefault();
                const realForm = document.getElementById(targetFormId);
                if (realForm) realForm.submit();
            }
        });
    });
</script>
