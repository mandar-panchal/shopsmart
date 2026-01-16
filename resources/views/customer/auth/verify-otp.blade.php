<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Verify OTP
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter the 6-digit code sent to your email
                </p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('customer.verify.otp.submit') }}" method="POST">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700">OTP Code</label>
                    <input id="otp" name="otp" type="text" required maxlength="6" pattern="[0-9]{6}"
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 rounded-md text-center text-2xl tracking-widest focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="000000" autofocus>
                    <p class="mt-1 text-xs text-gray-500">Enter the 6-digit code</p>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verify & Login
                    </button>
                </div>
            </form>

            <form action="{{ route('customer.resend.otp') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Didn't receive OTP? Resend
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('customer.login') }}" class="text-sm text-gray-600 hover:text-gray-500">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>