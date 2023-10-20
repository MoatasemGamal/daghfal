<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/profile">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="text-bg-dark d-flex flex-column justify-content-center align-items-center" style="height: calc(100vh - 56px);
background-image: url('/assets/images/palestine.png');
background-size: contain;
background-position: center center;
background-repeat: no-repeat;
">
        @yield('content')
    </div>

    <p class="h1 p-2 position-fixed bottom-0 left-0 text-light">#standing_with_palestine</p>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>