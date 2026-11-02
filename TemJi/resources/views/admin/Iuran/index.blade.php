<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penetapan Nominal Iuran per Wilayah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Tetapkan Nominal Iuran per Wilayah</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <form action="{{ route('admin.iuran.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Wilayah</label>
                    <input type="text" name="wilayah" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" placeholder="Contoh: Sukapura" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nominal Iuran per Bulan (Rp)</label>
                    <input type="number" name="nominal" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500" placeholder="Contoh: 20000" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Berlaku Sejak</label>
                    <input type="date" name="berlaku_sejak" class="w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">
                    Simpan Nominal
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b">
                        <th class="p-3 font-semibold text-gray-700">Wilayah</th>
                        <th class="p-3 font-semibold text-gray-700">Nominal / Bulan</th>
                        <th class="p-3 font-semibold text-gray-700">Berlaku Sejak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listIuran as $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $item->wilayah }}</td>
                            <td class="p-3">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="p-3">{{ $item->berlaku_sejak ? Carbon\Carbon::parse($item->berlaku_sejak)->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-3 text-center text-gray-500">Belum ada data iuran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>