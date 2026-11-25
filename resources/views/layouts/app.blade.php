<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM-PKL') - Teknik Pemesinan SMK</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white">

    <div class="flex flex-1 min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 hidden md:flex">
            <div>
                <!-- App Logo & Branding -->
                <div class="p-5 border-b border-slate-800/80 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-gears text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold tracking-tight text-white leading-tight">SIM-PKL TP</h1>
                        <p class="text-xs text-blue-400 font-medium">Teknik Pemesinan SMK</p>
                    </div>
                </div>

                <!-- User Quick Info -->
                @auth
                <div class="px-5 py-3 mx-3 my-3 bg-slate-800/60 rounded-xl border border-slate-700/50 flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-sm text-blue-400 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-semibold text-slate-200 truncate">{{ Auth::user()->name }}</div>
                        <div class="flex items-center space-x-1.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[11px] uppercase tracking-wider font-semibold text-slate-400">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Menu Navigation -->
                <nav class="px-3 py-2 space-y-1 text-sm font-medium">
                    <div class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Navigasi Utama</div>

                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-gauge-high w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('bengkel.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('bengkel.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-industry w-5 text-center"></i>
                        <span>Daftar Bengkel PKL</span>
                    </a>

                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'guru']))
                        <div class="pt-4 px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Verifikasi & Laporan</div>

                        <a href="{{ route('pendaftaran.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('pendaftaran.index') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-clipboard-check w-5 text-center"></i>
                                <span>Konfirmasi PKL</span>
                            </div>
                            @php
                                $pendingCount = \App\Models\PendaftaranPkl::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('pendaftaran.belum-memilih') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('pendaftaran.belum-memilih') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-user-xmark w-5 text-center"></i>
                                <span>Belum Memilih PKL</span>
                            </div>
                        </a>

                        <div class="pt-4 px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Manajemen Pengguna</div>

                        <a href="{{ route('user.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('user.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-users-gear w-5 text-center"></i>
                            <span>Akun Siswa & Guru</span>
                        </a>
                    @endif

                    <div class="pt-4 px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengaturan</div>

                    <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all {{ request()->routeIs('profile') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-id-badge w-5 text-center"></i>
                        <span>Profil & Password</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Logout Action -->
            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-lg text-sm font-medium text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition-all">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-950">
            <!-- Top Navbar -->
            <header class="glass-header border-b border-slate-800 sticky top-0 z-30 px-4 sm:px-6 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger -->
                    <button onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="md:hidden text-slate-300 hover:text-white p-2 rounded-lg bg-slate-800 border border-slate-700">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs text-slate-400 hidden sm:block">Praktek Kerja Lapangan (PKL) - Konsentrasi Keahlian Teknik Pemesinan</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @auth
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-semibold text-slate-200">{{ Auth::user()->name }}</span>
                        <span class="text-[11px] text-blue-400">{{ Auth::user()->nisn_nip ? 'NISN/NIP: ' . Auth::user()->nisn_nip : Auth::user()->email }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-blue-600/30 border border-blue-500/50 flex items-center justify-center text-blue-300 font-bold text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Mobile Sidebar Drawer -->
            <div id="mobile-sidebar" class="hidden md:hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm" onclick="this.classList.add('hidden')">
                <div class="w-64 bg-slate-900 h-full p-4 border-r border-slate-800 flex flex-col justify-between" onclick="event.stopPropagation()">
                    <div>
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                            <span class="font-bold text-white flex items-center space-x-2">
                                <i class="fa-solid fa-gears text-blue-500"></i>
                                <span>SIM-PKL TP</span>
                            </span>
                            <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')" class="text-slate-400 hover:text-white">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <nav class="mt-4 space-y-1 text-sm font-medium">
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Dashboard</a>
                            <a href="{{ route('bengkel.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Daftar Bengkel PKL</a>
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'guru']))
                                <a href="{{ route('pendaftaran.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Konfirmasi PKL</a>
                                <a href="{{ route('pendaftaran.belum-memilih') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Belum Memilih PKL</a>
                                <a href="{{ route('user.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Akun Siswa & Guru</a>
                            @endif
                            <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Profil & Password</a>
                        </nav>
                    </div>
                    <div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-center py-2 text-rose-400 bg-rose-500/10 rounded-lg">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notification Messages -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-start space-x-3 shadow-lg shadow-emerald-950/20">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5 text-lg"></i>
                        <div class="flex-1 font-medium text-sm">{{ session('success') }}</div>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 flex items-start space-x-3 shadow-lg shadow-rose-950/20">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 mt-0.5 text-lg"></i>
                        <div class="flex-1 text-sm">
                            <p class="font-semibold">{{ session('error') ?? 'Terjadi kesalahan pada input:' }}</p>
                            @if($errors->any())
                                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-rose-200">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-800/80 px-6 py-4 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} SIM-PKL Teknik Pemesinan SMK. Sistem Informasi Manajemen Praktek Kerja Lapangan Industri.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
