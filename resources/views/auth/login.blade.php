<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ assets('backend/images/favicon.ico') }}">
    <link href="{{assets('backend/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
    <link href="{{assets('backend/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
    <link href="{{assets('backend/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <link href="{{assets('backend/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    <link href="{{assets('backend/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{assets('backend/css/bootstrap-extended.css')}}" rel="stylesheet">
    <link href="{{assets('backend/fonts.googleapis.com/css276c7.css')}}?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet">
    <link href="{{assets('backend/css/app.css')}}" rel="stylesheet">
    <link href="{{assets('backend/css/custom.css')}}" rel="stylesheet">
    <link href="{{ assets('backend/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <title>CNG Showroom - Login</title>

    <style>
        body.login-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Roboto', sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }
        .login-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #fff !important;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .input-group-text {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-left: none !important;
            color: #94a3b8 !important;
            cursor: pointer;
            border-top-right-radius: 8px !important;
            border-bottom-right-radius: 8px !important;
        }
        .input-group .form-control {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .btn-primary-custom {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(45deg, #0b5ed7, #0a58ca);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.4);
        }
        .form-label {
            color: #cbd5e1;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 6px;
        }
        .forgot-link, .signup-link {
            color: #60a5fa;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .forgot-link:hover, .signup-link:hover {
            color: #93c5fd;
            text-decoration: underline;
        }
        .brand-logo {
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }
        .brand-logo:hover {
            transform: scale(1.03);
        }
    </style>
</head>

@php
    $app_name = configs(['app_name']);
    $app_logo = configs(['app_logo']);
@endphp

<body class="login-bg">
<!--wrapper-->
<div class="wrapper w-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card login-card mb-0">
                    <div class="card-body p-4 p-sm-5">
                        <div class="mb-4 text-center">
                            <img src="{{$app_logo}}" alt="{{$app_name ?? 'App Logo'}}" height="70" class="brand-logo mb-3 text-secondary">
                            <h4 class="text-white fw-bold mb-1">Welcome Back!</h4>
                            <p class="text-secondary small mb-0">Sign in to continue to CMS</p>
                        </div>

                        <div class="form-body">
                            <form class="row g-3" method="post" action="{{route('login.submit')}}">
                                {{csrf_field()}}
                                <div class="col-12">
                                    <label for="inputUsername" class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" id="inputUsername" placeholder="Enter your username" required autofocus>
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password" required>
                                        <span class="input-group-text" id="toggleBtn" onclick="togglePassword()">
                                            <i class="bx bx-show fs-5"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-6 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input pointer" name="remember_me" type="checkbox" id="flexSwitchCheckChecked">
                                        <label class="form-check-label text-secondary small pointer ms-1" for="flexSwitchCheckChecked">Remember Me</label>
                                    </div>
                                </div>

{{--                                <div class="col-6 text-end">--}}
{{--                                    <a href="auth-basic-forgot-password.html" class="forgot-link small">Forgot Password?</a>--}}
{{--                                </div>--}}

                                <div class="col-12 mt-4">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary-custom text-white">
                                            Sign In <i class="bx bx-right-arrow-alt ms-1"></i>
                                        </button>
                                    </div>
                                </div>

{{--                                <div class="col-12 mt-4 text-center">--}}
{{--                                    <p class="text-secondary small mb-0">--}}
{{--                                        Registered dealer? <a href="auth-basic-signup.html" class="signup-link fw-medium">Sign Up here</a>--}}
{{--                                    </p>--}}
{{--                                </div>--}}
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{assets('backend/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{assets('backend/libs/feather-icons/feather.min.js') }}"></script>

<script src="{{assets('backend/js/jquery.min.js')}}"></script>
<script src="{{assets('backend/plugins/simplebar/js/simplebar.min.js')}}"></script>
<script src="{{assets('backend/plugins/metismenu/js/metisMenu.min.js')}}"></script>
<script src="{{assets('backend/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
<script src="{{assets('backend/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{assets('backend/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{assets('backend/js/app.js')}}"></script>

<script>
    function togglePassword() {
        const passwordField = document.getElementById("password");
        const toggleBtn = document.getElementById("toggleBtn");

        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleBtn.innerHTML = "<i class='bx bx-hide fs-5'></i>";
        } else {
            passwordField.type = "password";
            toggleBtn.innerHTML = "<i class='bx bx-show fs-5'></i>";
        }
    }
</script>
</body>

</html>
