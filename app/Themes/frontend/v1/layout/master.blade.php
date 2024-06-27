<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
@php
 $_js = config('vfo', null, 'app') .'/js/';
 $_css = config('vfo', null, 'app') .'/css/';
 $_institution_info = patOsInstituteInfo();
@endphp
<html lang="{{ config('language',null,'app') }}">
<head>
<meta charset="{{ CHARSET }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{{-- Se l'applicazione è indicizzabile nel motore di ricreca o meno. --}}
@if(empty($_institution_info['indexable']))
<meta name="robots" content="noindex,nofollow">
@else
<meta name="robots" content="all">
@endif
{{-- Meta data dinamici --}}
@if(!empty($_meta_page))
{{$_meta_page}}
@endif

    <meta name="dcterms:language" content="it" />
    <meta name="dcterms:format" content="text/html" />
    <meta name="dcterms:publisher" content="Portale Trasparenza <?php echo $_institution_info['full_name_institution']; ?>" />
    <meta name="description" content="Il portale della Trasparenza conforme al D.Lgs. 33/2013 - Amministrazione Trasparente" />
    <meta name="keywords" content="servizi,uffici,contatti,procedimenti,trasparenza" />
    <meta name="base_url" content="{{ baseUrl() }}">
    <meta name="current_url" content="{{ currentUrl() }}">
    {{-- Favicon pesonalizzata --}}
    {{ getFavicon() }}
    {{ css($_css.'titillium_web.css?v='. VERSION_STYLE_CSS,'frontend') }}
    {{ css('fontawesome-free-5.15.4-web/css/all.css','common') }}
    {{ css('fontawesome-6/all.min.css','common') }}
    {{ css($_css.'bootstrap-italia.min.css','frontend') }}
    {{ css($_css.'swiper-bundle.min.css','frontend') }}
    {{ css($_css.'style.css?v='. VERSION_STYLE_CSS,'frontend') }}
    {{ css($_css.'custom.css?v=' . VERSION_CUSTOM_CSS,'frontend') }}
    {{-- BEGIN: Css --}}
    @yield(css)
    {{-- END: Css --}}

    {{--  Eventuali codici di tracciamento --}}
    @if(!empty($_institution_info['statistics_tracking_code']) && empty($register))
        {{  "\n" . $_institution_info['statistics_tracking_code'] . "\n" }}
    @endif
    <script>
        window.__PUBLIC_PATH__ = '/assets/frontend/v1/bootstrap-italia/dist/fonts'
    </script>
</head>
<body>

{{-- BEGIN: Skip to content --}}
{% include v1/layout/partials/skip_to_content %}
{{-- END: Skip to content --}}


<div id="modals"></div>

{{-- BEGIN: Header --}}
{% include v1/layout/partials/header %}
{{-- END: Header --}}
{{-- BEGIN: Breadcrumbs --}}
@php
    $breadcrumb = getBreadcrumb(
        !empty($bread) ? $bread : null,
        !empty($concatBreadcrumb) ? $concatBreadcrumb : false
    );
@endphp
@if(!empty($breadcrumb))
    <div class="container my-3">
        <nav class="breadcrumb-container" aria-label="breadcrumb">
            {{ $breadcrumb }}
        </nav>
    </div>
@endif

{{-- END: Breadcrumbs --}}
<main id="main">
    {{-- BEGIN: Content --}}
    @yield(content)
    {{-- END: Content --}}

    {{-- BEGIN: Javascript di sistema --}}
    {{ js($_js.'jquery-3.7.1.min.js','frontend') }}
    {{ js($_js.'popper.min.js','frontend') }}
    {{ js($_js.'bootstrap-italia.bundle.min.js','frontend') }}
    {{ js('autonumeric/autonumeric.js','common') }}
    {{ js('fontawesome-6/all.min.js','common') }}

    {{ js($_js.'swiper-bundle.min.js','frontend') }}
    {{ js($_js.'script.js','frontend') }}
    {{ js('download/jquery.fileDownload.js','common') }}
    {{-- END: Javascript di sistema --}}
</main>

{{-- BEGIN: Footer --}}
{% include v1/layout/partials/footer %}
{{-- END: Footer --}}
{% include v1/layout/partials/credits_footer %}
<script type="text/javascript">
    $(document).ready(function () {

        $('#salto_blocchi').removeAttr('style');

        // Per i campi importo
        $('.a-num-class').autoNumeric({aSep: '.', aDec: ',', vMax: '999999999999.99'});

        // Per i tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // Script per l'indice della pagina dove è presente
        // Begin
        let indexElements = $('.anchor');
        let html = '';
        $.each(indexElements, function () {
            html += '<li><a href="#' + this.id + '">' + this.textContent + '</a></li>';
        });
        $('#lista-anchor').append(html);
        let topMenu = $("#lista-anchor"),
            topMenuHeight = 90,
            menuItems = topMenu.find("a"),
            scrollItems = menuItems.map(function () {
                let item = $($(this).attr("href"));
                if (item.length) {
                    return item;
                }
            });
        $(window).scroll(function () {
            let fromTop = $(this).scrollTop() + topMenuHeight;
            let cur = scrollItems.map(function () {
                if ($(this).offset().top < fromTop)
                    return this;
            });
            cur = cur[cur.length - 1];
            let id = cur && cur.length ? cur[0].id : "";
            menuItems
                .parent().removeClass("active")
                .end().filter("[href='#" + id + "']").parent().addClass("active");
        });
        $('#__reset_form').click((e) => {
            e.preventDefault();
            window.location.href = $('#__redirect_url').val();
        });
        // End

        $('.open-data-download-btn').on('click', (e) => {
            e.preventDefault();
            $('#loading-download-open-data').hide();
            $('#success-download-open-data').hide();
            $('#error-download-open-data').hide();
            $('#form-generate-opendata').show();
            $('#modalOpenData').modal({
                backdrop: 'static',
                keyboard: false
            });
        })

        @if(!empty($linkDownloadOpenData))
        {{-- Download Async Open data--}}
        $('.open-data-download-btn').on('click', (e) => {
            e.preventDefault();

            let dataParams = {
                'filter': false,
                '{{ config('csrf_token_name',null,'app') }}': $("input[name='{{ config('csrf_token_name',null,'app') }}']").val()
            };

            @if(!empty($filterUsed))
                dataParams['filter'] = true;
            @foreach($searchFieldsOpenData as $name => $value)
                    @if(!empty($value))
                dataParams['{{ $name }}'] = '{{ $value }}';
            @endif
            @endforeach
            @endif


            let issueType = '';
            @if(!empty($doubleIssue))
                issueType = $(e.target).attr('data-type');
            dataParams['issueType'] = issueType;
            @endif


            let interval = '';
            let flagNoRangeSelected = false;
            @if(isset($rangeOpenData,$instances['total']) && $instances['total'] >= ($rangeOpenData+1))

            let rangeSelect = null;

            @if(!empty($doubleIssue))
                rangeSelect = document.getElementById('open_data_range_id_' + issueType);
            @else
                rangeSelect = document.getElementById('open_data_range_id');
            @endif

                interval = rangeSelect.options[rangeSelect.selectedIndex].value;
            flagNoRangeSelected = true;


            @endif
            if (interval !== undefined && interval !== '') {
                let splitInterval = interval.split('-');
                dataParams['skip'] = splitInterval[0] - 1;
                dataParams['take'] = splitInterval[1];
            } else {
                if (flagNoRangeSelected) {
                    dataParams['skip'] = -1;
                } else {
                    dataParams['skip'] = 0;
                    dataParams['take'] = 1000000;
                }
            }

            $.fileDownload('{{ siteUrl('download/open-data/'.$linkDownloadOpenData) }}', {
                httpMethod: "GET",
                data: dataParams,
                prepareCallback: (url) => {
                    //console.log('prepareCallback');
                    $('#loading-download-open-data').show();
                },

                successCallback: (url) => {
                    //console.log('successCallback');
                    $('#loading-download-open-data').hide();
                    $('#success-download-open-data').show();

                    setTimeout(() => {
                        $('#modalOpenData').modal('hide');
                    }, 2000)
                },

                abortCallback: (url) => {
                    //console.log('abortCallback');
                    $('#modalOpenData').modal('hide');
                    $('#loading-download-open-data').hide();
                },

                failCallback: (responseHtml, url, error) => {
                    //console.log('failCallback');
                    $('#loading-download-open-data').hide();
                    $('#error-download-open-data-text').empty().html(jQuery(responseHtml).text());
                    $('#error-download-open-data').show();

                    setTimeout(() => {
                        $('#modalOpenData').modal('hide');
                    }, 5000)
                }
            });
            return false;
        });
        @endif

    });

</script>
{{-- BEGIN: Javascript --}}
@yield(javascript)
{{-- END: Javascript --}}
</body>
</html>

