<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Begin Modale --}}
<div class="modal fade zoom-in" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Aggiungi nuovo elemento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body " id="modal-form">
                <div class="justify-content-center mt-5 pt-5" id="spinner">
                    <div class="col-md-12 text-center">
                        <div class="spinner-border" role="status"></div>
                        <div>Caricamento...</div>
                    </div>
                </div>
                <iframe src="" id="new-obj-box" width="100%" height="600px" frameborder="0"
                        style="display: none;"></iframe>
            </div>
        </div>
    </div>
</div>
{{-- End Modale --}}


<style type="text/css">
    #spinner {
        display: flex;
    }

    .modal.fade .modal-dialog {
        -webkit-transform: translate(0, 0);
        transform: translate(0, 0);
    }

    .zoom-in {
        transform: scale(0) !important;
        opacity: 0;
        -webkit-transition: .25s all 0s;
        -moz-transition: .25s all 0s;
        -ms-transition: .25s all 0s;
        -o-transition: .25s all 0s;
        transition: .25s all 0s;
        display: block !important;
    }

    .zoom-in.show {
        opacity: 1;
        transform: scale(1) !important;
        transform: none;
    }


</style>