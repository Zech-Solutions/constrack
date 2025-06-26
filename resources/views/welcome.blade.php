<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constrack</title>
    <!-- Tailwind -->
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <!-- Alpine -->
    <script type="module" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine-ie11.min.js" defer></script>
    <!-- AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <!-- Custom style -->
    <link rel="stylesheet" href="{{ asset('css/constract.css') }}">
    <!-- Poppins font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
</head>

<body class="antialiased">

    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            class="fixed z-50 px-6 py-3 text-white transform -translate-x-1/2 bg-green-500 rounded shadow-lg top-4 left-1/2">
            {{ session('success') }}
        </div>
    @endif
    @livewireStyles
    <div x-data="{ open: false }" class="w-full text-gray-700 bg-cream">
        <div class="flex flex-col max-w-screen-xl px-8 mx-auto md:items-center md:justify-between md:flex-row">
            <div class="flex flex-row items-center justify-between py-6">
                <div class="relative md:mt-8">
                    <a href="#"
                        class="relative z-50 text-lg font-bold tracking-widest text-gray-900 rounded-lg focus:outline-none focus:shadow-outline">Constrack</a>
                    <svg class="absolute z-40 h-11 -top-2 -left-3" viewBox="0 0 79 79" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M35.2574 2.24264C37.6005 -0.100501 41.3995 -0.100505 43.7426 2.24264L76.7574 35.2574C79.1005 37.6005 79.1005 41.3995 76.7574 43.7426L43.7426 76.7574C41.3995 79.1005 37.6005 79.1005 35.2574 76.7574L2.24264 43.7426C-0.100501 41.3995 -0.100505 37.6005 2.24264 35.2574L35.2574 2.24264Z"
                            fill="#65DAFF" />
                    </svg>
                </div>
            </div>
            <nav :class="{ 'transform md:transform-none': !open, 'h-full': open }"
                class="flex flex-col flex-grow h-0 pb-4 duration-300 origin-top scale-y-0 md:h-auto md:items-center md:pb-0 md:flex md:justify-end md:flex-row">
                {{-- <a class="px-10 py-3 mt-2 text-sm text-center text-gray-800 bg-white rounded-full md:mt-8 md:ml-4"
                    href="#">Login</a> --}}
                <a href="{{ route('signup.page') }}"
                    class="z-50 flex items-center gap-2 px-10 py-3 mt-2 text-sm text-center text-white bg-yellow-500 rounded-full md:mt-8 md:ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path d="M10 10a4 4 0 100-8 4 4 0 000 8zM2 18a8 8 0 0116 0H2z" />
                    </svg>
                    Sign Up
                </a>

            </nav>
        </div>
    </div>
    <div class="bg-cream">
        <div class="flex flex-col items-start max-w-screen-xl px-8 mx-auto lg:flex-row">
            <div
                class="flex flex-col items-start justify-center w-full mb-5 text-center lg:w-6/12 lg:pt-24 lg:text-left md:mb-0">
                <h1 class="my-4 text-5xl font-bold leading-tight text-darken">
                    <span class="text-yellow-500" id="typewriter-prefix">Constrack</span>
                    <span id="typewriter" class="typewriter-cursor"></span>
                </h1>
                <p data-aos="fade-down" data-aos-delay="300" class="mb-8 text-lg leading-normal">
                    Welcome to Constrack — your all-in-one construction project management tool. Plan, track, and manage
                    your projects efficiently with ease and precision.
                </p>

                <div class="items-center justify-center w-full md:flex lg:justify-start md:space-x-5">
                    <a href="{{ route('signup.page') }}"
                        class="py-4 text-xl font-bold text-white transition duration-300 ease-in-out transform bg-yellow-500 rounded-full lg:mx-0 px-9 focus:outline-none hover:scale-110">
                        Sign up
                    </a>
                </div>
            </div>
            <div class="relative w-full lg:w-6/12 lg:-mt-10" id="girl">
                <img data-aos="fade-up" data-aos-once="true" class="w-10/12 mx-auto 2xl:-mb-20"
                    src="{{ asset('man.png') }}" alt="bg" />
            </div>
        </div>
        {{-- <div class="relative z-40 text-white -mt-14 sm:-mt-24 lg:-mt-36">
            <svg class="xl:h-40 xl:w-full" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M600,112.77C268.63,112.77,0,65.52,0,7.23V120H1200V7.23C1200,65.52,931.37,112.77,600,112.77Z"
                    fill="currentColor"></path>
            </svg>
        </div> --}}
    </div>

    <footer class="mt-8 text-white bg-gray-900">
        <div class="flex flex-col items-center justify-between px-6 py-6 mx-auto max-w-7xl md:flex-row">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <h1 class="relative z-50 pr-5 text-xl font-bold">Constrack</h1>
                    <svg class="absolute z-40 w-11 h-11 -top-2 -left-3" viewBox="0 0 79 79" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M35.9645 2.94975C37.9171 0.997129 41.0829 0.997127 43.0355 2.94975L76.0502 35.9645C78.0029 37.9171 78.0029 41.0829 76.0503 43.0355L43.0355 76.0502C41.0829 78.0029 37.9171 78.0029 35.9645 76.0503L2.94975 43.0355C0.997129 41.0829 0.997127 37.9171 2.94975 35.9645L35.9645 2.94975Z"
                            stroke="#26C1F2" stroke-width="2" />
                    </svg>
                </div>
                <span class="pl-4 text-sm font-semibold border-l border-gray-500">Construction Management</span>
            </div>

            <div class="mt-4 text-sm text-center text-gray-400 md:mt-0">
                <p>All rights reserved &copy; 2025</p>
                <p>By <span class="font-semibold text-white">Bacoders</span></p>
            </div>
        </div>
    </footer>


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script>
        const typewriter = document.getElementById('typewriter');

        const sentences = [
            ' is now much easier',
            ' is powerful and smart',
            ' makes everything smoother',
            ' helps you build better'
        ];

        let sentenceIndex = 0;
        let charIndex = 0;

        function typeSentence() {
            const currentSentence = sentences[sentenceIndex];
            if (charIndex < currentSentence.length) {
                typewriter.textContent += currentSentence.charAt(charIndex);
                charIndex++;
                setTimeout(typeSentence, 50);
            } else {
                setTimeout(() => {
                    typewriter.textContent = '';
                    charIndex = 0;
                    sentenceIndex = (sentenceIndex + 1) % sentences.length;
                    typeSentence();
                }, 1500);
            }
        }

        typeSentence();
    </script>



</body>

</html>
