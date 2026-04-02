<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JobFlow OMS') }}</title>

    <!-- Google Fonts: DM Sans for logic/labels, Merriweather for titles where needed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'DM Sans', sans-serif; }
    </style>

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-800 bg-[#F9FAFB]">

    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-[#0F1B2D]">
                JobFlow <span class="text-[#2D7DD2]">OMS</span>
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Centralized Workspace Registration
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="bg-white py-8 px-4 shadow-sm sm:rounded-lg sm:px-10 border border-slate-200">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>
