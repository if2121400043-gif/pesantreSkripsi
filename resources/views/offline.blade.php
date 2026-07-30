<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koneksi Terputus — PP Nurul Furqon</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest" defer></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Safe Inline CSS Fallback when Offline --}}
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
        }
        * {
            box-sizing: border-box;
        }
        .offline-card {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            padding: 2rem;
            text-align: center;
            max-width: 28rem;
            width: 100%;
        }
        .offline-icon-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            border-radius: 9999px;
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
            margin-bottom: 1.5rem;
        }
        .offline-title {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
        }
        .offline-text {
            font-size: 0.875rem;
            line-height: 1.5rem;
            color: #64748b;
            margin: 0 0 2rem 0;
        }
        .offline-btn-retry {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background-color: #047857;
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.875rem;
        }
        .offline-btn-retry:hover {
            background-color: #065f46;
        }
        .offline-link-home {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            margin-top: 1rem;
            transition: color 0.2s;
        }
        .offline-link-home:hover {
            color: #334155;
        }
    </style>
</head>
<body class="min-h-screen bg-surface-50 flex items-center justify-center p-4">
    <div class="offline-card max-w-md w-full bg-white rounded-2xl shadow-xl border border-surface-200 p-8 text-center animate-fade-in-up">
        {{-- Branding Icon --}}
        <div class="offline-icon-container inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 mb-6 shadow-sm">
            <i data-lucide="wifi-off" class="w-10 h-10"></i>
        </div>

        {{-- Main Message --}}
        <h1 class="offline-title text-2xl font-bold text-surface-900 font-heading mb-2">Koneksi Internet Terputus</h1>
        <p class="offline-text text-surface-500 text-sm leading-relaxed mb-8">
            Halaman ini tidak dapat dimuat karena perangkat Anda tidak terhubung ke internet. Periksa koneksi data atau Wi-Fi Anda dan coba lagi.
        </p>

        {{-- Actions --}}
        <div class="space-y-3">
            <button onclick="window.location.reload()" class="offline-btn-retry w-full btn-primary flex items-center justify-center gap-2 py-3 bg-emerald-700 hover:bg-emerald-800 transition-colors shadow-md">
                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                <span>Coba Lagi</span>
            </button>
            <a href="/" class="offline-link-home block w-full text-center text-sm font-semibold text-surface-500 hover:text-surface-700 transition-colors py-2">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
