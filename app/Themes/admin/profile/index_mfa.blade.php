{{--  UTENTE index (Paginazione) Form store USER --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row justify-content-center">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">Configura Autenticazione a Due Fattori</div>
            <div class="card-body text-center">
                @if($isEnabled)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> L'autenticazione a due fattori è <strong>abilitata</strong>.
                    </div>

                    @if(!empty($recoveryCodes))
                        <h3>Codici di recupero</h3>
                        <p style="color: #c00;">Salvali in un posto sicuro. Ogni codice è utilizzabile una sola volta.</p>
                        <div style="display: inline-block; text-align: left; background: #f5f5f5; padding: 15px; border-radius: 6px;">
                            @foreach($recoveryCodes as $rc)
                                <code style="display: block; margin: 4px 0;">{{ $rc }}</code>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4">
                        <form method="POST" action="/admin/profile/mfa/disable" onsubmit="return confirm('Sei sicuro di voler disabilitare l\'autenticazione a due fattori?');">
                            <button type="submit" class="btn btn-danger">Disabilita 2FA</button>
                        </form>
                    </div>

                @else
                    <p>Scansiona il QR code con Google Authenticator o Microsoft Authenticator:</p>
                    
                    <div id="qrcode" style="display: inline-block; margin: 20px 0;"></div>
                    
                    <p>
                        Oppure inserisci manualmente il codice:<br>
                        <code style="font-size: 1.1em; letter-spacing: 2px; user-select: all;">
                            {{ $secret }}
                        </code>
                    </p>
    
                    <form method="POST" action="{{ siteUrl('/admin/profile/mfa/store') }}">
                        <div style="margin: 20px 0;">
                            <label for="code">Inserisci il codice a 6 cifre dall'app:</label><br>
                            <input type="text" id="code" name="code"
                                   placeholder="000000"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   required
                                   autocomplete="one-time-code"
                                   inputmode="numeric"
                                   style="font-size: 1.5em; text-align: center; width: 160px;
                                          letter-spacing: 5px; padding: 8px; margin-top: 8px;"
                                   class="form-control d-inline-block">
                        </div>
                        <button type="submit" class="btn btn-primary">Attiva 2FA</button>
                    </form>
                @endif
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
@if(!$isEnabled)
<script src="{{ baseUrl('/assets/common/qrcode/qrcode.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    var otpauthUrl = "{{ $qrUrl }}";

    new QRCode(document.getElementById("qrcode"), {
        text: otpauthUrl,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
@endif
{% endblock %}