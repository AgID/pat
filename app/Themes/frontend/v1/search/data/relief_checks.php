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
            $link = siteUrl('page/14/details/' . $item['id'] . '/' . urlTitle($item['object'])); ?>
    <article>
        <div class="box-risultato">
            <div>
                <!-- Nome archivio -->
                <a class="categoria"
                    href="<?php echo siteUrl('page/14/controlli-e-rilievi-sullamministrazione') ?>"><span
                        class="fas fa-university"></span> Controlli e rilievi sull'amministrazione</a>

                <!-- Oggetto del controllo -->
                <h3>
                    <a
                        href="<?php echo $link ?>"><?php echo !empty($item['object']) ? escapeXss($item['object']) : 'N.D.' ?></a>
                </h3>
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
                <a href="<?php echo $link ?>" class="pulsante-freccia" aria-label="Leggi di più <?php echo !empty($item['object']) ? escapeXss($item['object']) : 'N.D.' ?>">Leggi di più</a>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<div id="async_pagination_search_result" class="mt-3">
    <?php paginateBootstrap($results); ?>
</div>
<?php endif; ?>