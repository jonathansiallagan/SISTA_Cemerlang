{{--
    Kolom password dengan tombol ikon mata (tampilkan / sembunyikan).
    Semua atribut (id, name, class, placeholder, required, minlength, dll.) diteruskan ke <input>.
--}}
<div class="password-field" style="position:relative;">
    <input type="password" {{ $attributes->merge(['style' => 'padding-right:2.75rem;background-image:none;']) }}>

    <button type="button"
            onclick="togglePasswordVisibility(this)"
            aria-label="Tampilkan password"
            title="Tampilkan password"
            style="position:absolute;top:0;bottom:0;right:0;width:2.75rem;display:flex;align-items:center;justify-content:center;background:transparent;border:0;padding:0;color:#6b7280;cursor:pointer;">
        {{-- Mata terbuka: tampil saat password tersembunyi --}}
        <svg class="pw-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block;">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{-- Mata dicoret: tampil saat password terlihat --}}
        <svg class="pw-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
            <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        </svg>
    </button>
</div>

@once
<script>
    function setPasswordVisible(field, tampil) {
        const input = field.querySelector('input');
        const btn = field.querySelector('button');
        input.type = tampil ? 'text' : 'password';
        btn.querySelector('.pw-eye').style.display = tampil ? 'none' : 'block';
        btn.querySelector('.pw-eye-off').style.display = tampil ? 'block' : 'none';
        const label = tampil ? 'Sembunyikan password' : 'Tampilkan password';
        btn.setAttribute('aria-label', label);
        btn.title = label;
    }

    function togglePasswordVisibility(btn) {
        const field = btn.closest('.password-field');
        setPasswordVisible(field, field.querySelector('input').type === 'password');
    }

    // Saat modal ditutup, kembalikan semua kolom password ke keadaan tersembunyi
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery) {
            window.jQuery(document).on('hidden.bs.modal', '.modal', function () {
                this.querySelectorAll('.password-field').forEach(function (f) { setPasswordVisible(f, false); });
            });
        }
    });
</script>
@endonce