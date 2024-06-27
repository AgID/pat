<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<?php if (!empty($results['data'])) : ?>
    <div class="wrapper-risultati">
        <?php
        foreach ($results['data'] as $item) :
            $link = siteUrl('page/66/details/' . $item['id'] . '/' . 'tassi-index');
            ?>
            <article>
                <div class="box-risultato">
                    <div>
                        <a class="categoria" href="<?php echo siteUrl('page/66/tassi-di-assenza') ?>"><span
                                    class="fas fa-university"></span> Tassi di assenza</a>
                        <?php if (!empty($item['year'])) : ?>
                            <p>
                                <span>Anno:</span> <span class="grigio"><?php echo escapeXss($item['year']); ?></span>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($item['month'])) :
                            $i = 0;
                            $months = explode(',', $item['month']);
                            $len = count($months) - 1;
                            $periods = config('absenceRatesPeriod', null, 'app');
                            ?>
                            <p>
                                <span>Periodo:</span>
                                <span class="grigio">
                                    <?php foreach ($months as $month) : ?>
                                        <?php echo escapeXss($periods[$month]); ?>
                                        <?php if ($i++ < $len) echo ', '; ?>
                                    <?php endforeach; ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($item['structure'])) : ?>
                            <p>
                                <span>Struttura:</span>
                                <a class="grigio"
                                   href="<?php echo siteUrl('page/40/details/' . $item['structure']['id'] . '/' . urlTitle($item['structure']['structure_name'])) ?>">
                                    <?php echo escapeXss($item['structure']['structure_name']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <!-- Data ultima modifica -->
                        <?php if ($institution_info && (!empty($item['updated_at']) || !empty($item['created_at']))) :
                            $date = !empty($item['updated_at']) ? $item['updated_at'] : $item['created_at'];
                            ?>
                            <div style="font-size:.8rem;">
                                Data ultima modifica:<br><?php echo date('d-m-Y', strtotime($date)); ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?php echo $link ?>" class="pulsante-freccia" aria-label="Leggi di più <?php echo escapeXss($item['structure']['structure_name']); ?>">Leggi di più</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div id="async_pagination_search_result" class="mt-3">
        <?php paginateBootstrap($results); ?>
    </div>
<?php endif; ?>