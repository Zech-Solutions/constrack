<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Constract Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-0 m-0">
    <div class="flex w-screen h-screen overflow-hidden">
        <div class="flex-1 bg-center bg-cover" style="background-image: url('{{ asset('4.jpg') }}');">
        </div>

        <div class="flex items-center justify-center flex-1 overflow-hidden bg-white">
            <div class="w-full h-full max-w-xl px-10 py-10 overflow-y-auto">

                <div class="mb-10 text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Constrack</h1>
                    <p class="mt-2 text-gray-500">Sign up into your account</p>
                </div>
                @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                        class="p-4 mb-4 text-green-800 bg-green-100 border border-green-200 rounded">
                        {{ session('success') }}
                    </div>
                @endif


                <form class="space-y-6" method="POST" action="{{ route('signup.store') }}">
                    @csrf
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Business Name</label>
                            <input name="name" type="text" required placeholder="Your Business Name"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Business Email</label>
                            <input name="email" type="email" required placeholder="business@example.com"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <input name="contact_number" type="text" placeholder="09123456789"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <!-- Domain Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Preferred Domain</label>
                            <div class="flex">
                                <input name="domain_name" type="text" required pattern="^[a-zA-Z0-9\-]+$"
                                    placeholder="yourstore"
                                    class="w-full px-4 py-2 text-gray-900 bg-gray-100 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <span
                                    class="inline-flex items-center px-4 text-sm text-gray-700 bg-gray-200 select-none rounded-r-md">
                                    .constrack.com
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Only letters, numbers, and hyphens allowed. No spaces.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <input name="owner_firstname" type="text" required placeholder="First Name"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input name="owner_middlename" type="text" placeholder="Middle Name"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input name="owner_lastname" type="text" required placeholder="Last Name"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input name="owner_email" type="email" required placeholder="owner@example.com"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Business Address</label>
                            <textarea name="address" required rows="3" placeholder="123 Main St, City, Province"
                                class="w-full px-4 py-2 text-gray-900 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="flex items-start mt-4">
                        <input id="terms" name="terms" type="checkbox" required
                            class="w-4 h-4 mt-1 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="terms" class="ml-2 text-sm text-gray-700">
                            I agree to the
                            <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a>
                        </label>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full px-4 py-3 text-sm font-semibold text-white transition bg-blue-600 hover:bg-blue-700">
                            Submit Pre-Registration
                        </button>
                    </div>
                    <footer class="mt-8 text-xs text-center text-gray-400">
                        &copy; {{ date('Y') }} Constrack. All rights reserved.
                    </footer>

                </form>
            </div>
        </div>
    </div>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
