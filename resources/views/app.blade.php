<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php($basePath = config('app.base_path') ? '/'.config('app.base_path') : '')
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="DepAIDE - Department of Education Portal for Assisting ICT Diagnosis and Enhancement">
        <meta name="base-path" content="{{ $basePath }}">
        <title>{{ config('app.name', 'DepAIDE') }}</title>
        <link rel="icon" href="{{ $basePath }}/favicon.ico" sizes="any">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
            <title>{{ config('app.name', 'DepAIDE') }}</title>
        </x-inertia::head>
    </head>
    <body style="--depaide-building-bg: url('{{ $basePath }}/images/deped-building-bg.png');">
        <x-inertia::app />
    </body>
</html>
