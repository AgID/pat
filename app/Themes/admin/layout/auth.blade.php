<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="{{ config('language',null,'app') }}">
<head>
    <meta charset="{{ CHARSET }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{ csrf_meta() }}
    <title>@yield(title)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{ css('fontawesome-free-5.15.4-web/css/all.css','common') }}
    {{ css('ionicons-v2.0.1-master/css/ionicons.min.css','common') }}
    {{ css('icheck-bootstrap/icheck-bootstrap.min.css','common') }}
    {{ css('css/adminlte.min.css','admin') }}
    {{ css('css/custom.css','admin') }}
    {{ css('css/source_sans_pro_web.css?display=fallback&v='. VERSION_STYLE_CSS,'admin') }}
    {{-- Begin favicon --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ baseUrl('assets/admin/favicon/apple-icon-57x57.png')  }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ baseUrl('assets/admin/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ baseUrl('assets/admin/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ baseUrl('assets/admin/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ baseUrl('assets/admin/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ baseUrl('assets/admin/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ baseUrl('assets/admin/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ baseUrl('assets/admin/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ baseUrl('assets/admin/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"
          href="{{ baseUrl('assets/admin/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ baseUrl('assets/admin/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ baseUrl('assets/admin/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ baseUrl('assets/admin/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ baseUrl('assets/admin/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ baseUrl('assets/admin/favicon/ms-icon-144x144.png') }}">
    @yield(css)
    {{ js('detect-private-mode.js','common') }}
    <script type="text/javascript">
        /**
         * Global CSRF Meta Token
         * @type {string}
         */
        let meta = document.getElementsByTagName('meta');
        const CSRFMetaToken = meta.{{ csrf_token() }}.content;
    </script>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{ siteUrl() }}" title="<?php echo config('pat_os_title',null,'custom'); ?>">
            {{ img(['src'=>'assets/admin/img/pat_logo_nero.png','alt'=>config('pat_os_title',null,'custom')]) }}
        </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            @yield(content)
        </div>
    </div>
</div>
{{ js('jquery/jquery.min.js','common') }}
{{ js('bootstrap/js/bootstrap.bundle.min.js','common') }}
{{ js('js/adminlte.min.js','admin') }}
@yield(javascript)
</body>
</html>
