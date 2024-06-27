<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Arr;

defined('_FRAMEWORK_') or exit('No direct script access allowed');
?>

<div class="row">
    <?php if (!empty($results['data'])): ?>
        <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch flex-column">

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:35%;">SOGGETTO BENEFICIARIO</th>
                            <th style="width:30%;">OGGETTO</th>
                            <th style="width:10%;">TIPOLOGIA</th>
                            <th style="width:10%;">STRUTTURA ORGANIZZATIVA</th>
                            <th style="width:10%;">RESPONSABILE</th>
                            <th style="width:10%;">CREATO DA</th>
                            <th style="width:10%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):

                            if (!empty($r['personnel']) && is_array($r['personnel'])) {

                                $tmpResponsibles = Arr::pluck($r['personnel'], 'full_name');
                                $responsibles = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($responsible) {
                                            return ('<small class="badge badge-primary">' . escapeXss($responsible) . '</small>');
                                        }, $tmpResponsibles)));

                            } else {

                                $responsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

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
                                    <?php echo !empty($r['beneficiary_name']) ? escapeXss($r['beneficiary_name']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['object']) ? escapeXss(ucfirst($r['object'])) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['typology']) ? $r['typology'] : 'N.D.'; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['structure']['structure_name']) ? escapeXss($r['structure']['structure_name']) : 'N.D.'; ?>
                                </td>
                                <td>
                                    <?php echo $responsibles; ?>
                                </td>
                                <td>
                                    <?php echo createdByCheckDeleted(@$r['created_by']['name'], @$r['created_by']['deleted']) ?>
                                </td>

                                <td>
                                    <?php echo $updateAt; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/grant/edit-' . $r['type'] . '/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:35%;">SOGGETTO BENEFICIARIO</th>
                            <th style="width:30%;">OGGETTO</th>
                            <th style="width:10%;">TIPOLOGIA</th>
                            <th style="width:10%;">STRUTTURA ORGANIZZATIVA</th>
                            <th style="width:10%;">RESPONSABILE</th>
                            <th style="width:10%;">CREATO DA</th>
                            <th style="width:10%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mt-2 col-lg-12 text-center">
            Nessun occorrenza trovata nella sezione <strong>"Sovvenzioni e vantaggi"</strong>
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

