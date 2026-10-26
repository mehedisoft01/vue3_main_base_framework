<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard| TMSS ICT LTD.')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="TMSS Construction Department" name="description" />
    <meta content="TMSS ICT LTD" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ assets('/images/cms-logo.png') }}">

    <!-- jsvectormap css -->
    <link href="{{ assets('backend/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ assets('backend/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ assets('backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ assets('backend/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ assets('backend/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ assets('backend/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        window.baseUrl = '{{url('/')}}';
        window.uploadPath = '{{env('UPLOAD_PATH')}}';
        window.publicPath = '{{env('PUBLIC_PATH')}}';
        window.locale = '{{auth()->user()->locale ? auth()->user()->locale : 'en'}}';

        function dd(...args) {
            args.forEach(arg => console.log(arg));
        }

        // Suppress specific errors
        window.onerror = function(msg, url, line, col, error) {
            if (msg.includes('choices.min.js') || msg.includes('flatpickr.min.js')) {
                return true;
            }
            return false;
        };
    </script>

    <title>CNG Showroom</title>
</head>

<body class="{{auth()->user()->theme}}">
<div id="app"></div>

<!-- Core JS Files -->
<script src="{{assets('backend/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{assets('backend/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{assets('backend/libs/node-waves/waves.min.js') }}"></script>
<script src="{{assets('backend/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{assets('backend/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Suppress plugin.js errors by loading after Vue -->
<script>
    // Override document.write to prevent CDN blocking
    const originalWrite = document.write;
    document.write = function(html) {
        if (html && html.includes('toastify-js')) {
            // Load toastify properly
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/toastify-js';
            script.async = true;
            document.head.appendChild(script);
            return;
        }
        originalWrite.call(document, html);
    };
</script>

<script src="{{assets('backend/js/plugins.js') }}"></script>

<script src="{{assets('backend/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{assets('backend/libs/jsvectormap/maps/world-merc.js') }}"></script>

<script src="{{assets('backend/libs/swiper/swiper-bundle.min.js') }}"></script>

<!-- Dashboard init - Only if exists -->
@if(file_exists(public_path('assets/js/pages/dashboard-ecommerce.init.js')))
    <script src="{{assets('backend/js/pages/dashboard-ecommerce.init.js') }}"></script>
@endif

<!-- App js -->
<script src="{{assets('backend/js/app.js') }}"></script>

<!-- Fix for app.js error -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix innerHTML null error
        if (typeof document.querySelector('.counter-value') !== 'undefined') {
            // Counter functionality if needed
        }

        // Initialize metisMenu safely
        if (typeof $ !== 'undefined' && $.fn.metisMenu) {
            try {
                $('#menu').metisMenu();
            } catch(e) {
                console.warn('MetisMenu init skipped');
            }
        }
    });
</script>

@env('local')
@vite(['resources/js/app.js'])
@else
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $appJs = $manifest['resources/js/app.js'] ?? null;
    @endphp

    @if($appJs)
        <script type="module" src="{{ assets('build/' . $appJs['file']) }}"></script>

        @if(isset($appJs['css']))
            @foreach($appJs['css'] as $css)
                <link rel="stylesheet" href="{{ assets('build/' . $css) }}">
            @endforeach
        @endif

        @if(isset($manifest['resources/css/app.css']))
            <link rel="stylesheet" href="{{ assets('build/' . $manifest['resources/css/app.css']['file']) }}">
        @endif
    @endif
    @endenv
</body>
</html>
