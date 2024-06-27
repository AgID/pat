<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');
$numPages = (int)ceil($paginator['total'] / $paginator['per_page']);
?>

<?php if ($paginator['total'] > 0):?>
    <nav class="mt-2">

        <div class="mb-5 stile-pagination">
            <div>
                <ul class="pagination" >

                    <?php if (empty($paginator['first_page_url'])): ?>
                        <li class="page-item disabled" aria-disabled="true" aria-label="Prima pagina">
                            <span class="page-link" aria-hidden="true">&lsaquo;&lsaquo;</span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $paginator['first_page_url']; ?>" rel="prev"
                               aria-label="Prima pagina">&lsaquo;&lsaquo;</a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $i = 0;
                    $endKey = endKeyOnlyPagination($paginator['links']);
                    foreach ($paginator['links'] as $link): ?>

                        <?php if ($link['label'] == $paginator['current_page']): ?>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link"><?php echo $link['label'] ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo !empty($link['url']) ? $link['url'] : '#!'; ?>">
                                    <?php if ($link['label'] === null && $i === 0): ?>
                                        &lsaquo;
                                    <?php elseif ($link['label'] === null && $i === $endKey): ?>
                                        &rsaquo;
                                    <?php else: ?>
                                        <?php echo $link['label']; ?>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php $i++; endforeach; ?>

                    <?php if ($paginator['last_page_url']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $paginator['last_page_url'] ?>" rel="next"
                               aria-label="Ultima pagina">&rsaquo;&rsaquo;
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled" aria-disabled="true" aria-label="Ultima pagina">
                            <span class="page-link" aria-hidden="true">&rsaquo;&rsaquo;</span>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>

            <div>
                Pagina <strong><?php echo $paginator['current_page'] ?></strong> di <strong><?php echo $numPages ?></strong>
                &nbsp; - &nbsp;
                Totale records <strong><?php echo $paginator['total'] ?></strong>
            </div>

        </div>

    </nav>
<?php endif; ?>
