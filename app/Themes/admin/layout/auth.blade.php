<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
@php
$institutionInfoArray = patOsInstituteInfo(['short_institution_name', 'full_name_institution']);
$shotInstitutionName = $institutionInfoArray['short_institution_name'];
$fullInstitutionName = $institutionInfoArray['full_name_institution'];
$faviconFile = patOsInstituteInfo(['favicon_file'])['favicon_file'] ?? '';
@endphp
<html lang="{{ config('language',null,'app') }}">
<head>
    <meta charset="{{ CHARSET }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{ csrf_meta() }}
    <title>Login trasparenza - {{$fullInstitutionName}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link rel="manifest" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'.$faviconFile) }}">
    <link rel="apple-touch-icon" sizes="any" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="icon" type="image/png" sizes="any" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile)}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <meta name="theme-color" content="#ffffff">
    {{ css('fontawesome-free-5.15.4-web/css/all.css','common') }}
    {{ css('ionicons-v2.0.1-master/css/ionicons.min.css','common') }}
    {{ css('icheck-bootstrap/icheck-bootstrap.min.css','common') }}
    {{ css('css/adminlte.min.css','admin') }}
    {{ css('css/custom.css','admin') }}
    @php
        if(file_exists('media/'. $shotInstitutionName . '/assets/css/admin.css')){
            echo '<link href="'.baseUrl('media/'. $shotInstitutionName . '/assets/css/admin.css').'" rel="stylesheet" type="text/css" />';
        }
    @endphp
    {{ css('css/source_sans_pro_web.css?display=fallback&v='. VERSION_STYLE_CSS,'admin') }}
    @yield(css)
    {{ js('detect-private-mode.js','common') }}
    <script type="text/javascript">
        /**
         * Global BASE URL
         * @type {string}
         */
        const APP_BASE_URL = '{{ rtrim(preg_replace('/\.html$/', '', siteUrl()), '/') . '/' }}';

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
    <div class="login-ente">
        <img class="logo-ente"
                @php
                    $logo = patOsInstituteInfo(['simple_logo_file'])['simple_logo_file'] ?? null;
                    if (!empty($logo) && file_exists('media/' . $shotInstitutionName . '/assets/images/' . $logo)) {
                            echo !empty($logo)
                            ? 'src="' . baseUrl('media/' . $shotInstitutionName . '/assets/images/' . $logo) . '"alt="logo dell\'ente"'
                            : '';
                    } else {
                        echo '';
                    }
                @endphp
        >
        @php
            $app = !empty(patOsInstituteInfo(['app'])) ? patOsInstituteInfo(['app'])['app']['app_id'] : 0;
        @endphp
            <a class="logo-patos" href="{{ siteUrl() }}" title="<?php echo config('pat_os_title',null,'custom'); ?>">
                {{ img(['src'=>'assets/admin/img/pat_logo_nero.png','alt'=>config('pat_os_title',null,'custom')]) }}
            </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            @yield(content)
        </div>
    </div>
    @if($app == 1 and !empty($loginSap))
        <div class="wrapper-logo-patos-auth">
            <a class="logo-patos-auth" href="{{ siteUrl() }}" title="<?php echo config('pat_os_title',null,'custom'); ?>">
                {{ img(['src'=>'assets/admin/img/pat_logo_nero.png','alt'=>config('pat_os_title',null,'custom')]) }}
            </a>
        </div>

    @endif

</div>
{{ js('jquery/jquery-3.7.1.min.js','common') }}
{{ js('bootstrap/js/bootstrap.bundle.min.js','common') }}
{{ js('js/adminlte.min.js','admin') }}
@yield(javascript)
</body>
</html>
