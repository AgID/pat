<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <?php if (!empty($results['data'])): ?>
        <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch flex-column">

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:25%;">NOME DEL PROCEDIMENTO</th>
                            <th style="width:20%;">UFFICI RESPONSABILI</th>
                            <th style="width:20%;">RESPONSABILI PROCEDIMENTO</th>
                            <th style="width:15%;">CREATO DA</th>
                            <th style="width:15%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):

                            if (!empty($r['responsibles']) && is_array($r['responsibles'])) {

                                $tmpResponsibles = System\Arr::pluck($r['responsibles'], 'full_name');
                                $responsibles = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($personnel) {
                                            return ('<small class="badge badge-primary">' . escapeXss($personnel) . '</small>');
                                        }, $tmpResponsibles)));

                            } else {

                                $responsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            if (!empty($r['offices_responsibles']) && is_array($r['offices_responsibles'])) {

                                $tmpOfficeResponsibles = System\Arr::pluck($r['offices_responsibles'], 'structure_name');
                                $officeResponsibles = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($office) {
                                            return ('<small class="badge badge-primary">' . escapeXss($office) . '</small>');
                                        }, $tmpOfficeResponsibles)));

                            } else {

                                $officeResponsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            if (!empty($r['updated_at'])) {
                                $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                                    . date('d-m-Y H:i:s', strtotime($r['updated_at'])) .
                                    '</small>';
                            } else {
                                $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                            }
                            ?>

                            <tr>
                                <td>
                                    <?php echo !empty($r['name']) ? $r['name'] : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo $officeResponsibles ?>
                                </td>
                                <td>
                                    <?php echo $responsibles ?>
                                </td>
                                <td>
                                    <?php echo createdByCheckDeleted(@$r['created_by']['name'], @$r['created_by']['deleted']) ?>
                                </td>

                                <td>
                                    <?php echo $updateAt; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/proceeding/edit/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:25%;">NOME DEL PROCEDIMENTO</th>
                            <th style="width:20%;">UFFICI RESPONSABILI</th>
                            <th style="width:20%;">RESPONSABILI PROCEDIMENTO</th>
                            <th style="width:15%;">CREATO DA</th>
                            <th style="width:15%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mt-2 col-lg-12 text-center">
            Nessun occorrenza trovata nella sezione <strong>"Procedimenti dell'Ente"</strong>
        </div>
    <?php endif ?>
</div>
<div class="row justify-content-md-center">
    <div class="col-lg-12">
        <div id="async_pagination_search_result">
            <?php echo $pagination ?>
        </div>
    </div>
</div>

