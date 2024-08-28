<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <style>
            /* Inline styles for simplicity, consider using CSS classes for larger templates */
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f1f1f1;
            }

            .logo {
                text-align: center;
                margin-bottom: 20px;
            }

            .logo img {
                max-width: 200px;
            }

            .message {
                padding: 20px;
                background-color: #ffffff;
            }

            .message p {
                margin-bottom: 10px;
            }

            .footer {
                text-align: center;
                margin-top: 20px;
            }
        </style>

    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="container">
            <div class="message">
                <p>Dear {{$mailData['email']}},</p>
                <p>ma xac nhan {{$mailData['token']}}</p>
            </div>
        </div>
    </body>
</html>
