<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
            /* Add your custom styles here */
        </style>
    @endif
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex flex-col min-h-screen" class="is-preload">
    <header id="header">
        <h1>Eventually</h1>
        <p>A simple template for telling the world when you'll launch<br />
            your next big thing. Brought to you by <a href="http://html5up.net">HTML5 UP</a>.</p>
    </header>
    <form id="signup-form" method="post" action="#">
        <input type="email" name="email" id="email" placeholder="Email Address" />
        <input type="submit" value="Sign Up" />
    </form>
    <footer id="footer">
        <ul class="icons">
            <li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
            <li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
            <li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
            <li><a href="#" class="icon fa-envelope"><span class="label">Email</span></a></li>
        </ul>
        <ul class="copyright">
            <li>&copy; Untitled.</li>
            <li>Credits: <a href="http://html5up.net">HTML5 UP</a></li>
        </ul>
    </footer>
    <script src="{{ asset('assets/js/main.js') }}" >


</body>

</html>
