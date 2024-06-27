<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<?php if ($position == 'header') : ?>
    <li class="nav-item active">
        <a class="nav-link" href="<?php echo $result['url'] ?>" data-focus-mouse="false" title="Vai su <?php echo escapeXss($result['title']) ?>">
            <span><?php echo escapeXss($result['title']) ?></span>
            <!-- <span class="sr-only">pagina attuale</span> -->
        </a>
    </li>
<?php else : ?>
    <li>
        <a href="<?php echo escapeXss($result['url'],true) ?>" title="Vai su <?php echo escapeXss($result['title']) ?>">
            <?php echo escapeXss($result['title']) ?>
        </a>
    </li>
<?php endif ?>