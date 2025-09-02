<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Darkheim Web Development - Crafting exceptional digital experiences through innovative web development solutions">
        <meta name="keywords" content="web development, laravel, vue.js, responsive design, digital solutions">
        <meta name="author" content="Darkheim Web Development">

        <title>Darkheim Web Development - Innovative Digital Solutions</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app">
            <!-- Header Navigation -->
            <darkheim-header></darkheim-header>

            <!-- Hero Section -->
            <darkheim-hero></darkheim-hero>

            <!-- Services Section -->
            <darkheim-services></darkheim-services>

            <!-- Portfolio Section -->
            <darkheim-portfolio></darkheim-portfolio>

            <!-- About Section -->
            <darkheim-about></darkheim-about>

            <!-- Contact Section -->
            <darkheim-contact></darkheim-contact>

            <!-- Footer -->
            <darkheim-footer></darkheim-footer>
        </div>
    </body>
</html>
