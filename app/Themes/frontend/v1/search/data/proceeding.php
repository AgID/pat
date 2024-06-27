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
            $link = siteUrl('page/98/details/' . $item['id'] . '/' . urlTitle($item['name'])); ?>
    <article>
        <div class="box-risultato">
            <div>
                <!-- Nome dell'archivio -->
                <a class="categoria" href="<?php echo siteUrl('page/8/attivita-e-procedimenti') ?>"><span
                        class="fas fa-university"></span> Procedimenti</a>

                <!-- Nome del procedimento -->
                <h3>
                    <a
                        href="<?php echo $link ?>"><?php echo !empty($item['name']) ? characterLimiter(escapeXss($item['name']),280) : 'N.D.' ?></a>
                </h3>

                <!-- Uffici responsabili del procedimento -->
                <?php if (!empty($item['offices_responsibles'])) : ?>
                <p>
                    <span>Uffici responsabili</span>
                    <?php $i = 0;
                                foreach ($item['offices_responsibles'] as $office) : ?>
                    <a
                        href="<?php echo siteUrl('page/40/details/' . $office['id'] . '/' . urlTitle($office['structure_name'])); ?>">
                        <?php echo escapeXss($office['structure_name']); ?>
                    </a>
                    <?php echo ($i++ < count($item['offices_responsibles']) - 1) ? ', ' : ''; ?>
                    <?php endforeach; ?>
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
                <a href="<?php echo $link ?>" class="pulsante-freccia" aria-label="Leggi di più <?php echo !empty($item['name']) ? escapeXss($item['name']) : 'N.D.' ?>">Leggi di più</a>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<div id="async_pagination_search_result" class="mt-3">
    <?php paginateBootstrap($results); ?>
</div>
<?php endif; ?>