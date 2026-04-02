<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-500">

    <div class="relative grid grid-cols-1 md:grid-cols-2 bg-white/10 backdrop-blur-xl shadow-2xl rounded-3xl overflow-hidden max-w-5xl w-full border border-white/20">

        <!-- Glow -->
        <div class="absolute -top-20 -left-20 w-72 h-72 bg-purple-500 opacity-30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-blue-500 opacity-30 rounded-full blur-3xl"></div>

        <!-- LEFT PANEL -->
        <div class="hidden md:flex flex-col justify-center items-center text-white p-12 bg-gradient-to-br from-indigo-600/80 to-purple-700/80 backdrop-blur-lg">
            <h1 class="text-5xl font-bold mb-4">Join Us 🚀</h1>
            <p class="text-lg opacity-90 text-center max-w-sm">
                Create your account in<br>
                <span class="font-semibold">Vyapar POS System</span>
            </p>
        </div>

        <!-- RIGHT PANEL -->
        <div class="p-10 w-full bg-white/90 backdrop-blur-xl">

            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">Create Account</h2>
                <p class="text-gray-500 mt-2">Start your journey with us</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" />
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" />
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm" />
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-purple-500 focus:outline-none shadow-sm" />
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:scale-105 transition">
                    ✨ Register
                </button>

                <!-- Login Link -->
                <p class="text-center text-sm text-gray-500 mt-4">
                    Already registered?
                    <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">
                        Login
                    </a>
                </p>

            </form>

        </div>
    </div>

</body>
</html>
