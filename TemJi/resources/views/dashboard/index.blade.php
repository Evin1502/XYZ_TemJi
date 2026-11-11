<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analitik - TemJi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Analitik TemJi</h1>
            <!-- Tombol Trigger Modal Ekspor -->
            <button onclick="toggleModal(true)" class="bg-emerald-700 hover:bg-emerald-800 text-white font-medium px-4 py-2 rounded-lg flex items-center gap-2 shadow transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d=" "></path></svg>
                Ekspor Laporan
            </button>
        </div>

        <!-- Form Filter Dashboard -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap gap-4 mb-8 bg-gray-50 p-4 rounded border">
            <div>
                <label class="block text-sm font-medium text-gray-700">Wilayah</label>
                <select name="wilayah" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="">Semua Wilayah</option>
                    <option value="1" {{ $wilayah == '1' ? 'selected' : '' }}>Sukapura</option>
                    <option value="2" {{ $wilayah == '2' ? 'selected' : '' }}>Sukabirus</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="kategori" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="">Semua Kategori</option>
                    <option value="1" {{ $kategori == '1' ? 'selected' : '' }}>Minyak Jelantah</option>
                    <option value="2" {{ $kategori == '2' ? 'selected' : '' }}>Botol Plastik</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Periode</label>
                <select name="periode" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>

        <!-- Container Grafik -->
        <div class="relative h-96 w-full">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Modal Pop-up Ekspor Laporan (PBI 17) -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Ekspor laporan</h2>
                    <p class="text-xs text-gray-500">File mengikuti filter yang sedang aktif</p>
                </div>
                <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form method="GET" action="{{ route('admin.dashboard.export') }}">
                <!-- Hidden inputs membawa state filter yang aktif -->
                <input type="hidden" name="wilayah" value="{{ $wilayah }}">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="periode" value="{{ $periode }}">

                <!-- Pilihan Format -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="border rounded-lg p-3 flex items-start gap-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="format" value="csv" checked class="mt-1">
                            <div>
                                <span class="font-bold text-sm block">CSV</span>
                                <span class="text-xs text-gray-500">Untuk diolah di Excel</span>
                            </div>
                        </label>
                        <label class="border rounded-lg p-3 flex items-start gap-3 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="format" value="pdf" class="mt-1">
                            <div>
                                <span class="font-bold text-sm block">PDF</span>
                                <span class="text-xs text-gray-500">Siap cetak, dengan grafik</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tag Filter Aktif -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase">Filter aktif</label>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 border">{{ $wilayah ? ($wilayah == 1 ? 'Sukapura' : 'Sukabirus') : 'Semua wilayah' }}</span>
                        <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 border">{{ $kategori ? ($kategori == 1 ? 'Minyak Jelantah' : 'Botol Plastik') : 'Semua kategori' }}</span>
                        <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 border capitalize">{{ $periode }}</span>
                    </div>
                </div>

                <!-- Opsi Isi Laporan -->
                <div class="mb-6 space-y-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Isi laporan</label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" checked disabled class="rounded text-green-600"> Ringkasan angka utama
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" checked disabled class="rounded text-green-600"> Tren kg terkumpul per {{ $periode }}
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" checked disabled class="rounded text-green-600"> Penjemputan terlaksana
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" onclick="toggleModal(false)" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white rounded-lg text-sm font-medium shadow">Unduh File</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(show) {
            const modal = document.getElementById('exportModal');
            modal.style.display = show ? 'flex' : 'none';
        }

        // Render Chart.js
        const rawData = @json($trendData);
        const labels = rawData.map(item => item.tanggal);
        const dataKg = rawData.map(item => parseFloat(item.total_kg_daur_ulang).toFixed(2));
        const dataPenjemputan = rawData.map(item => item.jumlah_penjemputan);

        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Berat (Kg)',
                        data: dataKg,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderWidth: 2,
                        yAxisID: 'y',
                        fill: true
                    },
                    {
                        label: 'Jumlah Penjemputan',
                        data: dataPenjemputan,
                        type: 'bar',
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Kilogram (Kg)' } },
                    y1: { type: 'linear', position: 'right', title: { display: true, text: 'Jumlah Penjemputan' }, grid: { drawOnChartArea: false } }
                }
            }
        });
    </script>
</body>
</html>