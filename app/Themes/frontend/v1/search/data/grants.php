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

        if ($item['type'] == 'grant') {
            $link = siteUrl('page/11/details/' . $item['id'] . '/' . urlTitle($item['object'] ?? ''));
        } else if ($item['type'] == 'liquidation') {
            $link = siteUrl('page/155/details/' . $item['id'] . '/' . urlTitle($item['relative_grant']['object'] ?? ''));
        }
        ?>
    <article>
        <div class="box-risultato">
            <div>
                <!-- Nome archivio -->
                <a class="categoria" href="<?php
                if ($item['type'] == 'grants') {
                    echo siteUrl('page/11/sovvenzioni-contributi-sussidi-vantaggi-economici');
                } else if ($item['type'] == 'liquidation')
                    echo siteUrl('page/155/pagamenti-di-sovvenzioni-contributi-sussidi-vantaggi-economici');
                ?>"><span class="fas fa-university"></span>
                    <?php echo escapeXss($item['typology']); ?></a>

                <!-- Oggetto della sovvenzione struttura -->
                <h3>
                    <?php $object = !empty($item['relative_grant']['object'])
                            ? escapeXss($item['relative_grant']['object'])
                            : escapeXss($item['object']); ?>
                    <a href="<?php echo $link ?>"><?php echo characterLimiter($object,280); ?></a>
                </h3>

                <!-- Struttura -->
                <?php if (!empty($item['structure'])) : ?>
                <p>
                    <span>Struttura responsabile:</span>
                    <a
                        href="<?php echo siteUrl('page/40/details/' . $item['object_structures_id'] . '/' . urlTitle($item['structure']['structure_name'])); ?>">
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
                <a href="<?php echo $link ?>" class="pulsante-freccia" aria-label="Leggi di più <?php echo $object; ?></a>">Leggi di più</a>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<div id="async_pagination_search_result" class="mt-3">
    <?php paginateBootstrap($results); ?>
</div>
<?php endif; ?>