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
                            <th style="width:30%;">STRUTTURA</th>
                            <th style="width:15%;">ANNO</th>
                            <th style="width:20%;">PERIODO</th>
                            <th style="width:15%;">CREATO DA</th>
                            <th style="width:15%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $periods = config('absenceRatesPeriod', null, 'app');

                        foreach ($results['data'] as $r):

                            if (!empty($r['month'])) {
                                $period = str_replace(',', ', ', implode(',',
                                    array_map(
                                        function ($number) use($periods) {
                                            return ('<small class="badge badge-primary">' . $periods[$number] . '</small>');
                                        }, explode(',', $r['month']))));
                            } else {
                                $period = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>';
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
                                    <?php echo !empty($r['structure']) ? escapeXss($r['structure']['structure_name']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['year']) ? $r['year'] : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo $period ?>
                                </td>
                                <td>
                                    <?php echo createdByCheckDeleted(@$r['created_by']['name'], @$r['created_by']['deleted']) ?>
                                </td>

                                <td>
                                    <?php echo $updateAt; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/absence-rates/edit/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th style="width:30%;">STRUTTURA</th>
                                <th style="width:15%;">ANNO</th>
                                <th style="width:20%;">PERIODO</th>
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
            Nessun occorrenza trovata nella sezione <strong>"Tassi di assenza"</strong>
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

