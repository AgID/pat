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
                            <th style="width:15%;">OGGETTO</th>
                            <th style="width:10%;">TIPO</th>
                            <th style="width:5%;">CIG</th>
                            <th style="width:10%;">IMP. LIQUIDO</th>
                            <th style="width:15%;">STRUTTURA</th>
                            <th style="width:10%;">ATTIVATO DA</th>
                            <th style="width:10%;">AGGIUDICATARI</th>
                            <th style="width:10%;">CREATO DA</th>
                            <th style="width:10%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):

                            // Aggiudicatari
                            if (!empty($r['awardees']) && is_array($r['awardees'])) {

                                $tmpSuppliers = System\Arr::pluck($r['awardees'], 'name');
                                $awardees = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($number) {
                                            return ('<small class="badge badge-primary">' . escapeXss($number) . '</small>');
                                        }, $tmpSuppliers)));

                            } else {

                                $awardees = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            // Data di pubblicazione
                            $workStartDate = !empty($r['work_start_date'])
                                ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                                    date('d-m-Y', strtotime($r['work_start_date'])) .
                                    '</small>')
                                : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                            if (!empty($r['updated_at'])) {
                                $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                                    . date('d-m-Y H:i:s', strtotime($r['updated_at'])) .
                                    '</small>';
                            } else {
                                $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                            }

                            // Cig
                            if (in_array($r['typology'], ['result', 'alert'])) {
                                $tmpCig = !empty($r['relative_notice'])
                                    ? $r['relative_notice']['cig']
                                    : null;
                            } elseif ($r['typology'] == 'notice' && !empty($r['relative_lots'])) {
                                $tmpCig = (!empty($r['relative_lots']) ? implode(', ', \System\Arr::pluck($r['relative_lots'], 'cig')) : null);
                            } else {
                                $tmpCig = !empty($r['cig']) ? $r['cig'] : null;
                            }
                            ?>

                            <tr>
                                <td>
                                    <?php echo !empty($r['object']) ? escapeXss($r['object']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['type']) ? ucfirst($r['type']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($tmpCig) ? $tmpCig : 'N.D.'; ?>
                                </td>
                                <td>
                                    <?php echo
                                    !empty($r['amount_liquidated'])
                                        ? '<small class="badge badge-success">' . \Helpers\S::currency($r['amount_liquidated'], 2, ',', '.') . ' &euro; </small>'
                                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>'
                                    ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['structure']['structure_name']) ? escapeXss($r['structure']['structure_name']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo $workStartDate ?>
                                </td>
                                <td>
                                    <?php echo $awardees ?>
                                </td>
                                <td>
                                    <?php echo createdByCheckDeleted(@$r['created_by']['name'], @$r['created_by']['deleted']) ?>
                                </td>

                                <td>
                                    <?php echo $updateAt; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/contests-act/edit-' . $r['typology'] . '/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:15%;">OGGETTO</th>
                            <th style="width:10%;">TIPO</th>
                            <th style="width:5%;">CIG</th>
                            <th style="width:10%;">IMP. LIQUIDO</th>
                            <th style="width:15%;">STRUTTURA</th>
                            <th style="width:10%;">ATTIVATO DA</th>
                            <th style="width:10%;">AGGIUDICATARI</th>
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
            Nessun occorrenza trovata nella sezione <strong>"Bilanci"</strong>
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

