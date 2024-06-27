<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html lang="{{ config('language',null,'app') }}">
<?php
$shotInstitutionName = patOsInstituteInfo(['short_institution_name'])['short_institution_name'];
$faviconFile = patOsInstituteInfo(['favicon_file'])['favicon_file'] ?? '';
?>
<head>
    <meta charset="{{ CHARSET }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="institute_id" content="{{ PatOsInstituteId() }}">
    <meta name="uid" content="{{  authPatOs()->id() }}">
    {{ csrf_meta() }}
    <meta name="base_url" content="{{xss: baseUrl() }}">
    <meta name="current_url" content="{{xss: currentUrl() }}">
    {{-- Begin favicon --}}
    <!--<link rel="apple-touch-icon" sizes="57x57" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="manifest" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'.$faviconFile) }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile)}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ baseUrl('media/'. $shotInstitutionName . '/assets/images/'. $faviconFile) }}">-->
    <meta name="theme-color" content="#ffffff">
    {{-- End favicon --}}
    <title>{{xss: !empty($title) ? $title : 'Admin Pat OS' }}</title>
    {{ css('css/source_sans_pro_web.css?display=fallback&v='. VERSION_STYLE_CSS,'admin') }}
    {{ css('ionicons-v2.0.1-master/css/ionicons.min.css','common') }}
    {{ css('fontawesome-free-5.15.4-web/css/all.css','common') }}
    {{ css('fontawesome-6/all.min.css','common') }}
    {{ css('icheck-bootstrap/icheck-bootstrap.min.css','common') }}
    {{ css('css/adminlte.min.css','admin') }}
    {{ css('confirm/jquery-confirm.min.css','common') }}
    {{ css('select2/css/select2.min.css','common') }}
    {{ css('select2-bootstrap4-theme/select2-bootstrap4.min.css','common') }}
    {{ css('jquery-ui/jquery-ui.css','common') }}
    {{ css('css/custom.css','admin') }}
    {{-- CSS aggiuntivo --}}
    @yield(css)
    {{-- Init JS --}}
    {{ js('alpine.js','common') }}
    {{-- js('jquery/jquery.min.js','common') --}}
    {{ js('jquery/jquery-3.7.1.min.js','common') }}
    {{ js('bootstrap/js/bootstrap.bundle.min.js','common') }}
    {{ js('js/adminlte.min.js','admin') }}
    {{ js('confirm/jquery-confirm.min.js','common') }}
    {{ js('ajax/jquery.form.min.js','common') }}
    {{ js('select2/js/select2.full.min.js','common') }}
    {{ js('jquery-ui-1.13.2/jquery-ui.js','common') }}
    {{-- js('jquery-ui/jquery-ui-custom.js','common') --}}
    {{ js('autonumeric/autonumeric.js','common') }}
    {{ js('detect-private-mode.js','common') }}
    {{ js('fontawesome-6/all.min.js','common') }}
    {{-- Javascript aggiuntivo --}}
    <script type="text/javascript">
        /**
         * Global CSRF Meta Token
         * @type {string}
         */
        let meta = document.getElementsByTagName('meta');
        const CSRFMetaToken = meta.{{ csrf_token() }}.content;

        /**
         * Global Vars JS
         * @type {string}
         */
        let siteUrl = '{{ siteUrl() }}';
        let currentUrl = '{{ currentUrl() }}';

        /**
         * Parse Json
         */
        function parseJson(data) {
            let response;
            try {
                response = $.parseJSON(data);
            } catch (e) {
                response = data;
            }
            return response;
        }

        /**
         * Verifica se è un intero
         */
        function isInt(value) {
            let x = parseFloat(value);
            return !isNaN(value) && (x | 0) === x;
        }

        /**
         * File size
         */
        function getReadableFileSizeString(fileSizeInBytes, origin = false) {
            let i = -1;
            let originSize = fileSizeInBytes;
            let byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
            do {
                fileSizeInBytes = fileSizeInBytes / 1024;
                i++;
            } while (fileSizeInBytes > 1024);

            if (!origin) {
                return Math.max(fileSizeInBytes, 0.1).toFixed(1).trim() + byteUnits[i];
            } else {

                if (isInt(originSize)) {
                    let j = -1;
                    do {
                        originSize = originSize / 1024;
                        j++;
                    } while (originSize > 1024);
                    return Math.max(originSize, 0.1).toFixed(1).trim() + byteUnits[j];
                } else {

                    return originSize.trim() + byteUnits[i];
                }
            }
        };

        /**
         *
         * @param string
         * @returns {*}
         */
        function stripTags(string = '') {
            return string?.replace(/(<([^>]+)>)/gi, "");
        }

        /**
         * Truncate text
         */
        function truncate(str, max, suffix) {
            return str.length < max ? str : `${str.substr(0, str.substr(0, max - suffix.length).lastIndexOf(' '))}${suffix}`;
        }

        /**
         * Ellipsis text
         */
        function ellipsisify(str, cutoff, remain, ellipsis = '...') {
            const inputType = typeof str;
            if (inputType !== 'string') {
                throw new TypeError(`Expected type of input to be \`string\` but received \`${inputType}\``);
            }

            if (str.length <= cutoff) return str;
            if (!cutoff || cutoff + remain >= str.length) return str;
            if (!remain) return `${str.substr(0, cutoff)}${ellipsis}`;

            return (
                `${str.substr(0, cutoff)}${ellipsis}${str.substr(str.length - remain)}`
            );
        }

        /**
         * Funzione per ulrTItle
         * @param Text
         * @returns {string}
         */
        function convertToSlug(Text) {
            return Text.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }

        /**
         * Check element in array
         */
        function inArray(needle, haystack) {
            let length = haystack.length;
            for (let i = 0; i < length; i++) {
                if (typeof haystack[i] == 'object') {
                    if (arrayCompare(haystack[i], needle)) return true;
                } else {
                    if (haystack[i] == needle) return true;
                }
            }
            return false;
        }

        /**
         * Generatore di password casuali
         */
        function generatePassword(passwordLength) {
            let numberChars = "0123456789";
            let upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            let lowerChars = "abcdefghijklmnopqrstuvwxyz";
            let specialChars = "!(@#$*-";
            let allChars = numberChars + upperChars + lowerChars + specialChars;
            let randPasswordArray = Array(passwordLength);
            randPasswordArray[0] = specialChars;
            randPasswordArray[1] = numberChars;
            randPasswordArray[2] = upperChars;
            randPasswordArray[3] = lowerChars;
            randPasswordArray = randPasswordArray.fill(allChars, 4);
            return shuffleArray(randPasswordArray.map(function (x) {
                return x[Math.floor(Math.random() * x.length)]
            })).join('');
        }

        /**
         * Helper per la generazione di password casuali
         */
        function shuffleArray(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        }

        /**
         * Modale (show) spinner
         */
        function showFullModalSpinner() {
            $('._layer_spinner_notify').show();
            $('._spinner_notify').show();
        }

        /**
         * Modale (hide) spinner
         */
        function hideFullModalSpinner() {
            $('._layer_spinner_notify').hide();
            $('._spinner_notify').hide();
        }

        /**
         * Funzione per prevenire eventuali XSS
         * @param str
         * @returns {string}
         */
        function htmlEncode(str) {
            return String(str).replace(/[^\w. ]/gi, function (c) {
                return '&#' + c.charCodeAt(0) + ';';
            });
        }

        /**
         * Funzione per copiare del testo nel "Clipboard Data"
         * @param text
         * @returns {void}
         */
        function copyToClipboard(text) {
            if (window.clipboardData && window.clipboardData.setData) {
                return clipboardData.setData("Text", text);
            } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                let textarea = document.createElement("textarea");
                textarea.textContent = text;
                textarea.style.position = "fixed";
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    return document.execCommand("copy");
                } catch (ex) {
                    console.warn("Copy to clipboard failed.", ex);
                    return false;
                } finally {
                    document.body.removeChild(textarea);
                }
            }
        }
    </script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- Spinner di caricamento per le pagine --}}
    <div class="col-md-12 text-center align-middle" id="loading-container">
        <div id="loading-l">
            <div class="spinner-border mb-2" role="status"></div>
            <img src="{{baseUrl('assets/admin/img/pat_logo.png')}}"
                 title="<?php echo config('pat_os_title',null,'custom'); ?>">
        </div>
    </div>

    {{-- Testata --}}
    @if(empty($is_box))
        {% include layout/partials/header %}

        {{-- Barra Navigazione sinistra --}}
        {% include layout/partials/left_navbar %}
    @endif
    <div class="content-wrapper" @if(!empty($is_box) && $is_box === true) style="margin-left: 0 !important;" @endif>
        {{-- Breadcrumbs e titotlo --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row <?php echo (empty($is_box)) ? 'mb-4' : ''?>">
                    @if(!empty($titleSection))
                        <div class="col-sm-6">
                            @if(!empty($sectionIcon))
                                <div class="mr-3" style="float: left;padding: 5px;">
                                    {{$sectionIcon}}
                                </div>
                            @endif
                            <div>
                                <h1 class="m-0 text-dark">{{xss: !empty($titleSection) ? $titleSection : 'back Office' }}</h1>
                                <h7 class="text-muted">{{xss: !empty($subTitleSection) ? $subTitleSection : '' }}</h7>
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-6">
                        {{xss: !empty($breadcrumbs) ? $breadcrumbs : '' }}
                    </div>
                </div>
            </div>
        </div>
        {{-- contenuto --}}
        <div class="content text-sm">
            <div class="container-fluid">

                @if(isSuperAdmin(true))
                    @php
                        $alternative = false;
                        $name = null;
                        $patOsName = null;
                        if( session()->has('alternative_pat_os_id') ) {
                            $alternative = true;
                            $name = session()->get('alternative_pat_os_full_name');
                            $patOsName = patOsInstituteInfo(['full_name_institution']);
                        }
                    @endphp
                    @if($alternative && empty($is_box))
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                Attualmente stai gestendo <strong>"{{xss: $name }}"</strong>.
                                <a href="{{ PatOsInstituteId() }}" id="__restore_institute_default_"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-exchange-alt"></i>
                                    Ripristina la normale gestione
                                    <div class="spinner-grow spinner-grow-sm" id="spinner-grow-sm-restore"
                                         role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </a>

                            </div>
                        </div>
                    @endif

                @endif

                {{-- Messaggi di sessioni di tipo Flash per operazioni di CRUD --}}
                @if(sessionHasNotify())
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="alert alert-{{ sessionTypeNotify() }} alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                {{ sessionGetNotify() }}
                            </div>
                        </div>
                    </div>
                @endif


                @if(\System\Registry::exist('session_allowed'))
                    {{-- Sessioni concorrenti --}}
                    <!--<div class="row justify-content-center" id="layer-session-allowed">
                         <div class="col-md-12">
                            <div class="callout callout-danger">
                                <button type="button" class="close close-sess-allowed" data-dismiss="alert" aria-hidden="true">×</button>
                                 <i class="fas fa-info-circle"></i> &nbsp; Avviso: Un altro utente &egrave; gi&agrave; connesso con lo stesso nome utente.
                                <hr />
                                <ul>
                                 @foreach(\System\Registry::get('session_allowed_results') AS $resultConSess)
                        <li>
                             <strong>Accesso:</strong>  in data {{ date('d-m-Y',strtotime($resultConSess['created_at'])) }} alle ore {{ date('h:i',strtotime($resultConSess['created_at'])) }} -
                                          <strong>Sistema operativo:</strong>  {{ $resultConSess['platform'] }} -
                                          <strong>Browser:</strong>  {{ $resultConSess['browser'] }}  -
                                          <strong>Device:</strong>  {{ $resultConSess['device'] }}  -
                                          <strong>Indirizzo IP:</strong>  {{ $resultConSess['ip'] }}  -
                                          <strong>Connessione anonima browser:</strong> {{ ($resultConSess['browser_private_mode']===1) ? 'Si' : 'No' }}.
                                      </li>

                    @endforeach
                    </ul>

                    </div>
                </div>-->
            </div>
            {{-- Chiususra layer sessioni duplicate --}}
            <script type="text/javascript">
                $(document).ready(function () {
                    $('.close-sess-allowed').click(function () {
                        $('#layer-session-allowed').hide();
                    });
                });
            </script>
            @endif
            @if(guard())
                @yield(content)
            @else
                {% include access_denied/index %}
            @endif
        </div>
    </div>
    {{-- Contenuto --}}
</div>
{{-- Right Drawer --}}
{% include layout/partials/right_drawer %}

{{-- Footer --}}
{% include layout/partials/footer %}
</div>
@if(guard())
    @yield(javascript)
@endif

</body>
</html>