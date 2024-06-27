{{--  Dashboard principale area riservata --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="callout callout-success">
            <h5>Benvenuto <strong>{{ checkDecrypt($identity['name']) }}!</strong></h5>
            <p>Hai effettuato l'accesso in data <strong>{{ $identity['options']['last_date_access'] }}</strong> alle ore
                <strong>{{ $identity['options']['last_hour_access'] }}</strong>.</p>
        </div>
    </div>
</div>

<div class="row">
    {{-- Shortcut  --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                    <i class="fas fa-fist-raised"></i>{{ nbs(2) }}
                    Shortcut
                </h3>
            </div>
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        @foreach(getSectionPagesBackOffice() AS $section)
                            <div class="col-md-12 mt-3 mb-2">
                                <strong class="">{{ $section['icon'] }}  {{ $section['name'] }}</strong>
                            </div>
                            @if(!empty($section['children']))
                                <div class="col-md-12 pb-2" style="border-bottom:1px solid #f2f2f2">
                                    @foreach($section['children'] AS $children)
                                        @if($children['id'] != 52)
                                            <a class="btn btn-default btn-flat btn-xs mr-1 mb-2"
                                               href="{{ siteUrl( removeDotHtml($children['url'])) }}">
                                                {{ $children['name'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
@if($session_allowed!==null)
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'ATTENZIONE',
                autohide: true,
                delay: 15000,
                body: '{{ $session_allowed }}'
            });
        });
    </script>
@endif
{% endblock %}