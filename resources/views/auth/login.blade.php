<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords" content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">
    <title>Login - Production Downtime Report</title>
    <link href="{{ asset('assets/css/demo1/css2.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css?v=1.1')}}">
    <link rel="shortcut icon" href="{{ asset('assets/images/analyzer-icon.png')}}" />
</head>

<body>
    <div class="main-wrapper">
        <div class="page-wrapper full-page">
            <div class="page-content d-flex align-items-center justify-content-center">
                <div class="row w-100 mx-0 auth-page">
                    <div class="col-md-8 col-xl-6 col-sm-10 mx-auto">
                        <div class="card">
                            <div class="row">
                                <div class="col-sm-5 pe-md-0">
                                    <div class="auth-side-wrapper"></div>
                                </div>
                                <div class="col-sm-7 ps-md-0">
                                    <div class="auth-form-wrapper px-4 py-5">
                                        <a class="noble-ui-logo d-block mb-2">DT<span class="fw-bold">Analyzer</span></a>
                                        <h5 class="text-muted fw-normal mb-4">Welcome to Production Downtime Analyzer</h5>
                                        @if (Session::has('alert-type'))
                                            <div class="alert alert-{{ Session::get('alert-type') }}">
                                                {{ Session::get('alert-message') }}
                                            </div>
                                        @endif
                                        <form method="POST" action="{{ route('login')}}">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="nik">
                                                    <i data-feather="user"></i>
                                                </span>
                                                <div class="form-floating">
                                                    <input type="number" id="nik" name="nik" class="form-control" placeholder="NIK" aria-label="nik" aria-describedby="nik" autocomplete="off">
                                                    <label for="nik" class="form-label">NIK</label>
                                                </div>
                                            </div>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="password">
                                                    <i data-feather="lock"></i>
                                                </span>
                                                <div class="form-floating">
                                                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" aria-label="password" aria-describedby="password">
                                                    <label for="password" class="form-label">Password</label>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/core/core.js')}}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js')}}"></script>
    <script src="{{ asset('assets/js/template.js')}}"></script>
</body>

</html>
