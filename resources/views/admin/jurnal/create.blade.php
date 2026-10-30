<x-admin-layout>
    <div class="mb-6"><h1 class="text-3xl font-bold text-gray-800">Tambah Jurnal Manual</h1></div>

    @if(session('error')) <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div> @endif

    <form action="{{ route('jurnal.store') }}" method="POST">
        @csrf
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No Referensi</label>
                    <input type="text" name="no_referensi" value="{{ $no_referensi }}" class="w-full px-4 py-2 border rounded-lg bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                    <select name="cabang_id" class="w-full px-4 py-2 border rounded-lg" required>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ Auth::user()->cabang_id == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Jurnal</label>
                <input type="text" name="keterangan" class="w-full px-4 py-2 border rounded-lg" placeholder="Misal: Penyesuaian kas cabang..." required>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
            <table class="w-full mb-4" id="jurnal-table">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-2 w-1/2">Pilih Akun</th>
                        <th class="pb-2 text-right">Debit (Rp)</th>
                        <th class="pb-2 text-right">Kredit (Rp)</th>
                        <th class="pb-2 text-center w-12">Aksi</th>
                    </tr>
                </thead>
                <tbody id="jurnal-body">
                    <!-- Baris Default 1 -->
                    <tr class="item-row border-b border-gray-50">
                        <td class="py-2 pr-2">
                            <select name="akun_id[]" class="w-full px-3 py-2 border rounded-lg" required>
                                <option value="">- Pilih Akun -</option>
                                @foreach($akuns as $akun) <option value="{{ $akun->id }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option> @endforeach
                            </select>
                        </td>
                        <td class="py-2 pr-2"><input type="number" name="debit[]" class="w-full px-3 py-2 border rounded-lg text-right debit-input" value="0" min="0" oninput="calculateTotal()"></td>
                        <td class="py-2 pr-2"><input type="number" name="kredit[]" class="w-full px-3 py-2 border rounded-lg text-right kredit-input" value="0" min="0" oninput="calculateTotal()"></td>
                        <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="removeRow(this)"><i class="fa-solid fa-xmark"></i></button></td>
                    </tr>
                    <!-- Baris Default 2 -->
                    <tr class="item-row border-b border-gray-50">
                        <td class="py-2 pr-2">
                            <select name="akun_id[]" class="w-full px-3 py-2 border rounded-lg" required>
                                <option value="">- Pilih Akun -</option>
                                @foreach($akuns as $akun) <option value="{{ $akun->id }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option> @endforeach
                            </select>
                        </td>
                        <td class="py-2 pr-2"><input type="number" name="debit[]" class="w-full px-3 py-2 border rounded-lg text-right debit-input" value="0" min="0" oninput="calculateTotal()"></td>
                        <td class="py-2 pr-2"><input type="number" name="kredit[]" class="w-full px-3 py-2 border rounded-lg text-right kredit-input" value="0" min="0" oninput="calculateTotal()"></td>
                        <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="removeRow(this)"><i class="fa-solid fa-xmark"></i></button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-bold text-gray-800 bg-gray-50">
                        <td class="py-3 px-3 text-right">TOTAL :</td>
                        <td class="py-3 px-2 text-right" id="total-debit">Rp 0</td>
                        <td class="py-3 px-2 text-right" id="total-kredit">Rp 0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <button type="button" onclick="addRow()" class="text-sm font-medium text-blue-600 hover:text-blue-800"><i class="fa-solid fa-plus mt-1"></i> Tambah Baris Akun</button>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('jurnal.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg font-medium">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Simpan Jurnal</button>
        </div>
    </form>

    <script>
        function addRow() {
            let tbody = document.getElementById('jurnal-body');
            let firstRow = tbody.querySelector('.item-row').cloneNode(true);
            firstRow.querySelector('select').value = "";
            firstRow.querySelector('.debit-input').value = "0";
            firstRow.querySelector('.kredit-input').value = "0";
            tbody.appendChild(firstRow);
        }

        function removeRow(btn) {
            let rows = document.querySelectorAll('.item-row');
            if(rows.length > 2) {
                btn.closest('.item-row').remove();
                calculateTotal();
            } else {
                alert('Minimal harus ada 2 baris akun untuk menyeimbangkan jurnal.');
            }
        }

        function calculateTotal() {
            let debits = document.querySelectorAll('.debit-input');
            let kredits = document.querySelectorAll('.kredit-input');
            let totalDebit = 0;
            let totalKredit = 0;

            debits.forEach(input => totalDebit += parseFloat(input.value) || 0);
            kredits.forEach(input => totalKredit += parseFloat(input.value) || 0);

            document.getElementById('total-debit').innerText = 'Rp ' + totalDebit.toLocaleString('id-ID');
            document.getElementById('total-kredit').innerText = 'Rp ' + totalKredit.toLocaleString('id-ID');

            let totalRow = document.querySelector('tfoot tr');
            if(totalDebit !== totalKredit || totalDebit === 0) {
                totalRow.classList.add('text-red-600');
                totalRow.classList.remove('text-green-600', 'text-gray-800');
            } else {
                totalRow.classList.add('text-green-600');
                totalRow.classList.remove('text-red-600', 'text-gray-800');
            }
        }
    </script>
</x-admin-layout>