<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS (direct link from CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: Custom CSS -->
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-500">

    <div class="relative grid grid-cols-1 md:grid-cols-2 bg-white/10 backdrop-blur-xl shadow-2xl rounded-3xl overflow-hidden max-w-5xl w-full border border-white/20">

        <!-- Glow Effect -->
        <div class="absolute -top-20 -left-20 w-72 h-72 bg-purple-500 opacity-30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-blue-500 opacity-30 rounded-full blur-3xl"></div>

        <!-- LEFT PANEL -->
        <div class="hidden md:flex flex-col justify-center items-center text-white p-12 bg-gradient-to-br from-indigo-600/80 to-purple-700/80 backdrop-blur-lg">
            <h1 class="text-5xl font-bold mb-4 tracking-wide">Welcome 👋</h1>
            <p class="text-lg opacity-90 text-center max-w-sm leading-relaxed">
                Manage your business smarter with<br>
                <span class="font-semibold text-white">Vyapar POS System</span>
            </p>

            <!-- Extra Design -->
            <div class="mt-10 flex gap-3">
                <span class="w-3 h-3 bg-white/70 rounded-full"></span>
                <span class="w-3 h-3 bg-white/50 rounded-full"></span>
                <span class="w-3 h-3 bg-white/30 rounded-full"></span>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="p-10 w-full bg-white/90 backdrop-blur-xl">

            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Sign In</h2>
                <p class="text-gray-500 mt-2">Welcome back! Please login</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition duration-200 shadow-sm" />
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:outline-none transition duration-200 shadow-sm" />
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-gray-600">
                        <input type="checkbox" name="remember" class="rounded text-indigo-600">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-600 hover:underline">
                            Forgot?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:scale-105 hover:shadow-xl transition duration-300">
                    🚀 Log In
                </button>

            </form>

            <!-- Footer -->
            <p class="text-center text-gray-400 text-sm mt-6">
                © {{ date('Y') }} Vyapar POS
            </p>

        </div>
    </div>

</body>

</html>
