<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-PKL Teknik Pemesinan SMK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-blue-600 selection:text-white">
    
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(37,99,235,0.1),transparent_50%)]"></div>

    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden relative z-10 p-6 sm:p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl mx-auto flex items-center justify-center text-white shadow-xl shadow-blue-600/30 mb-4">
                <i class="fa-solid fa-gears text-2xl"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">SIM-PKL TEKNIK PEMESINAN</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem Informasi Pendaftaran Tempat Praktek Kerja Lapangan SMK</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Username / Email / NISN / NIP</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all"
                           placeholder="Contoh: siswa1, admin, atau 0054321001">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-blue-600">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full mt-2 py-3 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all text-sm flex items-center justify-center space-x-2">
                <span>Masuk ke Sistem</span>
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>

        <!-- Quick Demo Accounts Helper -->
        <div class="mt-8 pt-6 border-t border-slate-800/80">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Demo Akses Cepat (Password: <span class="text-blue-400">password123</span>)</p>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <button onclick="fillDemo('admin')" class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-center border border-slate-700/50 transition-all">
                    <div class="font-bold text-blue-400">Admin</div>
                    <div class="text-[10px] text-slate-400">admin</div>
                </button>
                <button onclick="fillDemo('guru')" class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-center border border-slate-700/50 transition-all">
                    <div class="font-bold text-indigo-400">Guru</div>
                    <div class="text-[10px] text-slate-400">guru</div>
                </button>
                <button onclick="fillDemo('siswa1')" class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-center border border-slate-700/50 transition-all">
                    <div class="font-bold text-emerald-400">Siswa</div>
                    <div class="text-[10px] text-slate-400">siswa1</div>
                </button>
            </div>
        </div>

    </div>

    <script>
        function fillDemo(username) {
            document.querySelector('input[name="login"]').value = username;
            document.querySelector('input[name="password"]').value = 'password123';
        }
    </script>
</body>
</html>
