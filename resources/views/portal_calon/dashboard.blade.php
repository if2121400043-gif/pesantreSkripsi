<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Calon Santri</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f4f5f6; /* Very light grayish background matching wireframe */
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-4xl bg-[#dfdfdf] rounded-lg shadow-sm overflow-hidden border border-gray-200">
        {{-- Header --}}
        <div class="bg-[#3b3a40] text-white px-6 py-3 flex justify-between items-center m-2 rounded-md">
            <h2 class="text-lg font-medium">Biodata Peserta Didik</h2>
            <button class="text-sm text-gray-300 hover:text-white transition-colors">Edit</button>
        </div>

        {{-- Content --}}
        <div class="px-8 py-6 text-[#5b5b5b] text-sm space-y-3">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">No. KK</div>
                <div class="md:col-span-2">{{ $calonSantri->no_kk ?? '-' }}</div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">N I K</div>
                <div class="md:col-span-2">{{ $calonSantri->nik ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Nama Lengkap</div>
                <div class="md:col-span-2">{{ $calonSantri->nama_lengkap ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Tempat, Tanggal Lahir</div>
                <div class="md:col-span-2">
                    {{ $calonSantri->tempat_lahir ?? '-' }}, 
                    {{ $calonSantri->tanggal_lahir ? \Carbon\Carbon::parse($calonSantri->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Usia</div>
                <div class="md:col-span-2">
                    @if($calonSantri->tanggal_lahir)
                        {{ \Carbon\Carbon::parse($calonSantri->tanggal_lahir)->age }} Tahun
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Jenis Kelamin</div>
                <div class="md:col-span-2">
                    {{ ($calonSantri->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : (($calonSantri->jenis_kelamin ?? '') === 'P' ? 'Perempuan' : '-') }}
                </div>
            </div>

            <hr class="border-[#b3b3b3] my-4 w-2/3">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Pendidikan Terakhir</div>
                <div class="md:col-span-2">{{ $calonSantri->asal_sekolah ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Phone1</div>
                <div class="md:col-span-2">{{ $calonSantri->telepon_wali ?? '-' }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Phone2</div>
                <div class="md:col-span-2">-</div>
            </div>

            <hr class="border-[#b3b3b3] my-4 w-2/3">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="font-medium">Alamat</div>
                <div class="md:col-span-2">{{ $calonSantri->alamat ?? '-' }}</div>
            </div>

        </div>

        <div class="mt-4 px-8 pb-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium underline">
                    Logout
                </button>
            </form>
        </div>
    </div>

</body>
</html>
