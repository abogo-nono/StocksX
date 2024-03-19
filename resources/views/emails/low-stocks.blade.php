<!DOCTYPE html>
<html lang="en" class="">

<head>
    <title>StocksX Low Stocks Alert</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('build/assets/app-qqJ1WtSm.css') }}">
</head>

<body class="text-white">
    <section class="max-w-2xl px-6 py-8 mx-auto bg-white dark:bg-gray-900">
        <header>
            <a href="https://stocksx.test" class="text-2xl font-bold text-pink-500">
                StocksX
            </a>
        </header>

        <main class="mt-8">
            <h2 class="text-gray-700 dark:text-gray-200">Hi {{ $user[0]->name }},</h2>

            <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
                You recived this email because some products need to be restocked <br><span class="font-semibold ">Here is the list</span>.
            </p>

            <ul class="list-disc my-12 ml-5">
                @foreach ($products as $product)
                    <li>{{ $product->name }}: {{ $product->quantity }}</li>
                @endforeach
            </ul>

            <p class="mt-2 leading-loose text-gray-600 dark:text-gray-300">
                You can check it directly in the app by clicking on the bellow button.
            </p><br>
            <a href="{{ route('filament.admin.auth.login') }}"
                class="px-6 py-2 mt-4 text-sm font-medium tracking-wider text-white capitalize transition-colors duration-300 transform bg-pink-600 rounded-lg hover:bg-pink-500 focus:outline-none focus:ring focus:ring-pink-300 focus:ring-opacity-80">
                Login
            </a>

            <p class="mt-8 text-gray-600 dark:text-gray-300">
                Thanks, <br>
                StocksX Support Team
            </p>
        </main>
    </section>
</body>

</html>
