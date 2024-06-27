<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">

    $(document).ready(function () {

        setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 500);

        $(function () {
            $("body").tooltip({selector: '[data-toggle=tooltip]'});
        });

        function notifyErrors(object) {

            let html = '<ul class="async-error">';

            for (let key in object) {

                if (typeof object[key] === 'string') {

                    html += '<li>' + object[key] + '.</li>';

                } else {

                    let files = object[key];

                    let $i = 1;
                    for (let key2 in files) {

                        html += "<li>L'estensione dell'allegato " + $i + ' non &egrave; permessa.</li>';

                        $i++;
                    }
                }

            }

            html += '</ul>';

            Swal.fire({
                title: 'Attenzione',
                html: html,
                icon: 'error',
                confirmButtonText: 'Chiudi'
            });
        }

        function notifySuccess(objectString) {

            let html = '<ul class="async-success">';

            if (typeof objectString === 'string') {

                html += '<li>' + objectString + '</li>';

            } else {

                for (let key in object) {

                    html += '<li>' + object[key] + '</li>';

                }

            }

            html += '</ul>';

            Swal.fire({
                title: 'Complimenti',
                html: html,
                icon: 'success',
                confirmButtonText: 'Chiudi'
            });
        }

        function parseJson(data) {

            let response;

            try {

                response = $.parseJSON(data);

            } catch (e) {

                response = data;

            }

            return data;
        }

        function formatCurrency(amount) {
            let amountParsed = parseFloat(amount);
            return amountParsed.toLocaleString('it-IT', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
                style: 'currency',
                currency: 'EUR'
            });
        }
    });
</script>
