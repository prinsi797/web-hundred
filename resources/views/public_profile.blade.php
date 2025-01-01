<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    if ($type == 'invite') {
        $title = 'Join HUNDRED Today';
        $description = ucwords($user->name) . ' has invited you to get strong with them on H U N D R E D 💪';
        $og_url = route('user.profile.public', ['username' => $user->username]);
        $og_image = asset('assets/images/feature-image.png');
    } else {
        $title = 'Join HUNDRED Today';
        $description = ucwords($user->name) . ' has invited you to get strong with them on H U N D R E D 💪';
        $og_url = route('user.profile.public', ['username' => $user->username]);
        $og_image = asset('assets/images/feature-image.png');
    }
    ?>
    {{-- test data --}}
    <title>{{ $title }}</title>

    <!-- Fonts -->
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;600&display=swap" rel="stylesheet"> --}}
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Tera:wght@100..900&display=swap" rel="stylesheet">
    <meta property="og:SITE_NAME" content="Hundred" />
    <meta property="og:type" content="website" />

    <meta property="og:image" content="{{ $og_image }}" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:url" content="{{ $og_url }}" />
    <meta property="og:description" content="{{ $description }}" />

    <style>
        body {
            background-color: black;
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        /* .title {
                font-size: 3rem;
                font-weight: 600;
                margin-bottom: 20px;
            } */
        .center-text {
            font-weight: 1059px;
            Top: 308px;
            font-size: 3rem;
            font-family: 'Lexend Tera';
        }

        .additional-text {
            font-size: 1rem;
            font-weight: 707px;
            margin-bottom: 50px;
            /* font-weight: bold; */
            /* font-family: 'SF Pro'; */
        }

        .additional2-text {
            font-size: 1.5rem;
            font-family: 'Lexend Tera';
            font-weight: 297px;
            /* font-weight: bold;  */
            margin-bottom: 50px;
        }

        .botom-text {
            font-weight: 184px;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .image {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 209px;
        }

        @media only screen and (max-width: 600px) {
            .center-text {
                font-size: 2rem;
            }
        }
    </style>

</head>
{{-- <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ route('admin.dashboard') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    {{ config('app.name') }}
                </div>
            </div>
        </div>
    </body> --}}

<body>
    <div class="content">
        <div class="additional-text">
            Get stronger with your friends 💪
        </div>
        <div class="center-text">
            H U N D R E D
        </div>
        <div class="additional2-text">
            lb club
        </div>
        <div class="botom-text">
            Join our beta
        </div>
        <a href="https://apps.apple.com/us/app/hundred-lift-with-friends/id6499122783" target="_blank">
            <div class="image">
                <img src="{{ asset('assets/images/app-store.png') }}">
            </div>
        </a>

    </div>
</body>

</html>
