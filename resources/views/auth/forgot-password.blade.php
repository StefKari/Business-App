<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Business CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Business CRM
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
                </p>
            </div>

            <form class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow" method="POST" action="{{ route('password.email') }}">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded">
                        <p class="text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="appearance-none relative block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Email address"
                           value="{{ old('email') }}">
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Email Password Reset Link
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Back to login
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
