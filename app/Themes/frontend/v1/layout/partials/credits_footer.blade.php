<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */
?>
@if(empty($register))
    <div class="text-center py-3 section-credits">
        <div class="container">

                <span href="https://developers.italia.it/it/software/agid-agid-pat"
                      title="Vai alla pagina su Developers Italia">PAT
            - Portale Amministrazione Trasparente </span>
                (pubblicata su Developers Italia) - <a
                        href="https://www.isweb.it/pagina642_amministrazione-trasparente-di-agid-servizi-annessi-al-riuso.html"
                        title="Vai alla pagina servixi di ISWEB">Servizi
                    di supporto al riuso erogati da ISWEB S.p.A.</a>

        </div>

    </div>
@else
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let barraAdmin = document.getElementById("barra-admin-bottom");
        if (barraAdmin) {
            let barraAdminHeight = barraAdmin.offsetHeight
            document.body.style.marginBottom = barraAdminHeight + "px";
        }
    });
</script>
