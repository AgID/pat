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
                            <th style="width:35%;">NOME INTERVENTO</th>
                            <th style="width:30%;">PROVVEDIMENTI CORRELATI</th>
                            <th style="width:10%;">REGOLAMENTI</th>
                            <th style="width:10%;">TERMINI TEMPORALI PER I PROVVEDIMENTI STRAORDINARI</th>
                            <th style="width:10%;">COSTO INTERVENTI STIMATO</th>
                            <th style="width:10%;">COSTO INTERVENTI EFFETTIVO</th>
                            <th style="width:10%;">CREATO DA</th>
                            <th style="width:10%;">ULTIMA MODIFICA</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):

                            if (!empty($r['updated_at'])) {
                                $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                                    . date('d-m-Y H:i:s', strtotime($r['updated_at'])) .
                                    '</small>';
                            } else {
                                $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                            }

                            if (!empty($r['measures']) && is_array($r['measures'])) {

                                $tmpMeasures = Arr::pluck($r['measures'], 'object');
                                $measures = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($measure) {
                                            return ('<small class="badge badge-primary">' . escapeXss($measure) . '</small>');
                                        }, $tmpMeasures)));

                            } else {

                                $measures = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            if (!empty($r['regulations']) && is_array($r['regulations'])) {

                                $tmpRegulations = Arr::pluck($r['regulations'], 'title');
                                $regulations = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($regulation) {
                                            return ('<small class="badge badge-primary">' . escapeXss($regulation) . '</small>');
                                        }, $tmpRegulations)));

                            } else {

                                $regulations = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            $timeLimits = !empty($r['time_limits'])
                                ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                                    date('d-m-Y', strtotime($r['time_limits'])) .
                                    '</small>')
                                : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            ?>
                            <tr>
                                <td>
                                    <?php echo !empty($r['name']) ? $r['name'] : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo $measures ?>
                                </td>
                                <td>
                                    <?php echo $regulations; ?>
                                </td>
                                <td>
                                    <?php echo $timeLimits; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['estimated_cost'])
                                        ? '<small class="badge badge-success">' . $r['estimated_cost'] . ' &euro; </small>'
                                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>'; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['effective_cost'])
                                        ? '<small class="badge badge-success">' . $r['effective_cost'] . ' &euro; </small>'
                                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>'; ?>
                                </td>
                                <td>
                                    <?php echo createdByCheckDeleted(@$r['created_by']['name'], @$r['created_by']['deleted']) ?>
                                </td>

                                <td>
                                    <?php echo $updateAt; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/interventions/edit/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:35%;">NOME INTERVENTO</th>
                            <th style="width:30%;">PROVVEDIMENTI CORRELATI</th>
                            <th style="width:10%;">REGOLAMENTI</th>
                            <th style="width:10%;">TERMINI TEMPORALI PER I PROVVEDIMENTI STRAORDINARI</th>
                            <th style="width:10%;">COSTO INTERVENTI STIMATO</th>
                            <th style="width:10%;">COSTO INTERVENTI EFFETTIVO</th>
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
            Nessun occorrenza trovata nella sezione <strong>"Interventi straordinari e di emergenza"</strong>
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

