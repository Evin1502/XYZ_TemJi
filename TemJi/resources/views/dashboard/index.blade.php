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

        <p class="mb-4 text-gray-500">Grafik tren keseluruhan (Filter akan ditambahkan selanjutnya).</p>

        <!-- Container Grafik -->
        <div class="relative h-96 w-full">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <script>
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