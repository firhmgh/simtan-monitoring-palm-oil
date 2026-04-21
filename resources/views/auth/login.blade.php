<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Monitoring PTPN IV</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-blue-50 flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl mb-4 mx-auto">
                <i data-lucide="leaf" class="w-10 h-10 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Sistem Informasi Monitoring Areal Tanaman
            </h1>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">

            <h2 class="text-xl font-bold text-gray-900 mb-6">Login ke Dashboard</h2>

            <!-- Menampilkan pesan sukses (misal dari redirect lain) -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <i data-lucide="user"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input id="username" name="username" type="text" value="{{ old('username') }}"
                            placeholder="contoh: adminptpn4 atau firahmagh485"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('username') border-red-500 @enderror"
                            required autocomplete="username" autofocus>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <i data-lucide="lock"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input id="password" name="password" type="password" placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            required autocomplete="current-password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium py-3 rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg shadow-emerald-500/30 disabled:opacity-60">
                    Login
                </button>
            </form>

            <!-- Demo Credentials (untuk development saja – hapus di production) -->
            <div class="mt-6 p-4 bg-emerald-50 rounded-lg text-center">
                <p class="text-xs text-emerald-800 font-medium mb-2">Demo (development only):</p>
                <p class="text-xs text-emerald-700">Username: gitaddpir</p>
                <p class="text-xs text-emerald-700">Password: password123</p>
            </div>

        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">
                © {{ date('Y') }} SIMTAN. All rights reserved.
            </p>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>

</body>

</html>
