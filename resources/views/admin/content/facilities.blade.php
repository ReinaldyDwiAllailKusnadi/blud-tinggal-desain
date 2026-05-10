@extends('layouts.sidebar')

@section('content')

<main>
  <div class="bg-white dark:bg-slate-800 p-5 rounded-lg shadow max-w-6xl mx-auto mt-6 transition-colors duration-200">
    <h4 class="text-xl font-semibold mb-4 text-gray-800 dark:text-slate-50">
        Tambah Fasilitas untuk: <span class="font-bold text-blue-600">{{ $content->name }}</span>
    </h4>

    <form action="{{ route('features.store') }}" method="POST" id="form-facilities">
        @csrf
        <input type="hidden" name="location" value="{{ $content->id }}">

        {{-- Pricelist --}}
        <div class="mb-10">
            <label class="block font-bold text-gray-700 dark:text-slate-200 mb-2">Penyewaan / Harga Area</label>
            <p class="text-xs text-gray-500 dark:text-slate-400 mb-4 italic">Isi area atau bagian lokasi yang dapat disewa, beserta luas dan harga sewanya.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full border dark:border-slate-700 rounded-lg overflow-hidden">
                    <thead>
                      <tr class="bg-gray-100 dark:bg-slate-700/50 text-gray-700 dark:text-slate-200">
                        <th class="px-3 py-3 text-left font-semibold text-sm">Bagian / Area</th>
                        <th class="px-3 py-3 text-left font-semibold text-sm">Luas Area</th>
                        <th class="px-3 py-3 text-left font-semibold text-sm">Harga Sewa (Rp)</th>
                        <th class="px-3 py-3"></th>
                      </tr>
                    </thead>
                    <tbody id="price-rows">
                      <tr class="border-b dark:border-slate-700">
                        <td class="px-3 py-2">
                          <input type="text" name="features[0][bagian]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Pendopo, Area Tengah, Lapangan">
                          <input type="hidden" name="features[0][type]" value="price">
                        </td>
                        <td class="px-3 py-2">
                          <input type="text" name="features[0][luas]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: 20x20 m atau 400 m²">
                        </td>
                        <td class="px-3 py-2">
                          <input type="text" name="features[0][price]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50 rupiah-input" placeholder="Contoh: 1.000.000">
                        </td>
                        <td class="px-3 py-2 text-right">
                          <button type="button" class="remove-price-row text-red-500 hover:text-red-700 font-semibold" disabled>Hapus</button>
                        </td>
                      </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="button" id="add-price-rows"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                    + Tambah Area Sewa
                </button>
            </div>
        </div>

        {{-- Fasilitas --}}
        <div class="mb-10">
          <label class="block font-bold text-gray-700 dark:text-slate-200 mb-1">Fasilitas Umum</label>
          <p class="text-xs text-gray-500 dark:text-slate-400 mb-4 italic">Contoh: parkir, toilet, aula, kursi, sound system, panggung.</p>
          
          <div class="overflow-x-auto">
            <table class="min-w-full border dark:border-slate-700 rounded-lg overflow-hidden">
              <thead>
                <tr class="bg-gray-100 dark:bg-slate-700/50 text-gray-700 dark:text-slate-200">
                  <th class="px-3 py-3 text-left font-semibold text-sm">Nama Fasilitas</th>
                  <th class="px-3 py-3"></th>
                </tr>
              </thead>
              <tbody id="facility-rows">
                <tr class="border-b dark:border-slate-700">
                  <td class="px-3 py-2">
                    <input type="text" name="features[100][facility_name]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Toilet Umum">
                    <input type="hidden" name="features[100][type]" value="facility">
                  </td>
                  <td class="px-3 py-2 text-right">
                    <button type="button" class="remove-facility-row text-red-500 hover:text-red-700 font-semibold" disabled>Hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-4">
            <button type="button" id="add-facility-rows"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all">
                + Tambah Fasilitas
            </button>
          </div>
        </div>

        <div class="flex justify-end mt-8 border-t dark:border-slate-700 pt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-lg font-bold shadow-lg transition-all transform hover:scale-105">
                Simpan Fasilitas & Harga
            </button>
        </div>
    </form>
</div>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    let index = {
      price: 1,
      facility: 101 
    };

    const config = {
      price: {
        tbody: document.getElementById('price-rows'),
        button: document.getElementById('add-price-rows'),
        html: (i) => `
          <td class="px-3 py-2">
            <input type="text" name="features[${i}][bagian]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Pendopo, Area Tengah, Lapangan">
            <input type="hidden" name="features[${i}][type]" value="price">
          </td>
          <td class="px-3 py-2">
            <input type="text" name="features[${i}][luas]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: 20x20 m atau 400 m²">
          </td>
          <td class="px-3 py-2">
            <input type="text" name="features[${i}][price]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50 rupiah-input" placeholder="Contoh: 1.000.000">
          </td>
          <td class="px-3 py-2 text-right">
            <button type="button" class="remove-row text-red-500 hover:text-red-700 font-semibold transition-all">Hapus</button>
          </td>
        `
      },
      facility: {
        tbody: document.getElementById('facility-rows'),
        button: document.getElementById('add-facility-rows'),
        html: (i) => `
          <td class="px-3 py-2">
            <input type="text" name="features[${i}][facility_name]" class="border w-full px-3 py-2 rounded-lg dark:bg-slate-900 dark:border-slate-700 dark:text-slate-50" placeholder="Contoh: Toilet Umum">
            <input type="hidden" name="features[${i}][type]" value="facility">
          </td>
          <td class="px-3 py-2 text-right">
            <button type="button" class="remove-row text-red-500 hover:text-red-700 font-semibold transition-all">Hapus</button>
          </td>
        `
      }
    };

    function addRow(type) {
      const tr = document.createElement('tr');
      tr.className = 'border-b dark:border-slate-700';
      tr.innerHTML = config[type].html(index[type]);
      config[type].tbody.appendChild(tr);
      index[type]++;
      refreshRemoveButtons();
    }

    function refreshRemoveButtons() {
      document.querySelectorAll('.remove-row').forEach((btn) => {
        btn.disabled = false;
        btn.onclick = () => btn.closest('tr').remove();
      });
    }

    config.price.button.addEventListener('click', () => addRow('price'));
    config.facility.button.addEventListener('click', () => addRow('facility'));

    if (config.price.tbody.children.length === 0) addRow('price');
    if (config.facility.tbody.children.length === 0) addRow('facility');

    refreshRemoveButtons();

    // Format rupiah saat mengetik
    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('rupiah-input')) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = formatRupiah(value);
        }
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        let number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    // Bersihkan format titik sebelum submit
    document.getElementById('form-facilities').addEventListener('submit', function() {
        document.querySelectorAll('.rupiah-input').forEach(input => {
            input.value = input.value.replace(/\D/g, '');
        });
    });
  });
</script>

@endsection