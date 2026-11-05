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
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Dashboard Analitik TemJi</h1>

        <!-- Form Filter -->
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

    <script>
        const rawData = @json($trendData);
        
        // Memisahkan data untuk label (sumbu x) dan dataset (sumbu y)
        const labels = rawData.map(item => item.tanggal);
        const dataKg = rawData.map(item => parseFloat(item.total_kg_daur_ulang).toFixed(2));
        const dataPenjemputan = rawData.map(item => item.jumlah_penjemputan);

        const ctx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Berat (Kg)',
                        data: dataKg,
                        borderColor: 'rgb(34, 197, 94)', // Green-500
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderWidth: 2,
                        yAxisID: 'y',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Jumlah Penjemputan',
                        data: dataPenjemputan,
                        type: 'bar',
                        backgroundColor: 'rgba(59, 130, 246, 0.6)', // Blue-500
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Kilogram (Kg)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Jumlah Penjemputan' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    </script>
</body>
</html>