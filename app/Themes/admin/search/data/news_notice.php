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
                            <th style="width:35%;">TITOLO</th>
                            <th style="width:30%;">TIPOLOGIA</th>
                            <th style="width:10%;">DATA VISUALIZZATA</th>
                            <th style="width:10%;">SEZIONI DI PUBBLICAZIONE</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):
                            ?>
                            <tr>
                                <td>
                                    <?php echo !empty($r['title']) ? escapeXss($r['title']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['typology']) ? ucfirst($r['typology']) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['news_date']) ? $r['news_date'] : 'N.D.'; ?>
                                </td>
                                <td>
                                    <?php echo '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>'; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/news-notice/edit/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:35%;">TITOLO</th>
                            <th style="width:30%;">TIPOLOGIA</th>
                            <th style="width:10%;">DATA VISUALIZZATA</th>
                            <th style="width:10%;">SEZIONI DI PUBBLICAZIONE</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mt-2 col-lg-12 text-center">
            Nessun occorrenza trovata nella sezione <strong>"News ed avvisi"</strong>
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

