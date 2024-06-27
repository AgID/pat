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
        $tmpName = !empty($item['labels']) ? $item['labels'][0]['label'] : $item['name'];
        $link = siteUrl('page/' . $item['id'] . '/' . urlTitle($tmpName)); ?>
    <article>
        <div class="box-risultato">
            <div>
                <span class="categoria"><span class="fas fa-university"></span> Pagina o contenuto</span>
                <h3>
                    <a
                        href="<?php echo $link ?>"><?php echo !empty($tmpName) ? characterLimiter(escapeXss($tmpName),280) : 'N.D.' ?></a>
                </h3>
            </div>
            <div>
                <a href="<?php echo $link ?>" class="pulsante-freccia" aria-label="Leggi di più <?php echo !empty($tmpName) ? escapeXss($tmpName) : 'N.D.' ?>">Leggi di più</a>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<div id="async_pagination_search_result" class="mt-3">
    <?php paginateBootstrap($results); ?>
</div>
<?php endif; ?>