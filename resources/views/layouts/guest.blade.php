<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="font-[Poppins] min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-500">

@php
$isLogin = request()->routeIs('login');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 bg-white/95 backdrop-blur shadow-2xl rounded-3xl overflow-hidden max-w-5xl w-full">

<!-- LEFT PANEL -->
<div class="hidden md:flex flex-col justify-center items-center text-white p-12 bg-gradient-to-br from-indigo-600 to-purple-700">

<h1 class="text-4xl font-bold mb-4">
{{ $isLogin ? 'Welcome Back 👋' : 'Create Account 🚀' }}
</h1>

<p class="text-lg opacity-90 text-center max-w-sm">
{{ $isLogin
    ? 'Log in to Canva Solution Vyapar POS'
    : 'Sign up to CanvaSolution Vyapar POS and start managing your business with professional tools instantly.' }}
</p>
</div>

<!-- RIGHT PANEL -->
<div class="p-10">

<div class="mb-8 text-center">

<h2 class="text-3xl font-bold text-gray-800">
{{ $isLogin ? 'Sign In' : 'Register' }}
</h2>

<p class="text-gray-500 mt-2">
{{ $isLogin ? 'Enter your credentials to continue' : 'Create your new account' }}
</p>

</div>

{{ $slot }}

</div>

</div>

</body>
</html>
