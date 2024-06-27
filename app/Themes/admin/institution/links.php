<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<?php if (!empty($results)): ?>
    <ul class="list-group">
        <?php foreach ($results as $r): ?>
            <li class="list-group-item">

                <div class="float-left">
                    <a href="<?php echo $r['url'] ?>" data-toggle="tooltip" data-placement="top"
                       data-original-title="<?php echo $r['url'] ?>" title="<?php echo $r['url'] ?>" target="_blank">
                        <?php echo $r['title'] ?>
                    </a>
                </div>

                <div class="float-right">
                    <div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">
                        <div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">
                            <div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">
                                <div class="btn-group" role="group" aria-label="Azioni" style="color: #fff !important;">

                                    <a href="<?php echo $r['id'] ?>" id="up_<?php echo $r['id'] ?>" title=""
                                       class="btn btn-sm btn-primary a_up blank" data-sort="<?php echo $r['sort'] ?>"
                                       data-reload="<?php echo $type ?>">
                                        <i class="fas fa-chevron-up"></i>
                                    </a>

                                    <a href="<?php echo $r['id'] ?>" id="down_<?php echo $r['id'] ?>" title=""
                                       class="btn btn-sm btn-primary a_down blank" data-sort="<?php echo $r['sort'] ?>"
                                       data-reload="<?php echo $type ?>" >
                                        <i class="fas fa-chevron-down"></i>
                                    </a>

                                    <a href="<?php echo $r['id'] ?>" id="edit_<?php echo $r['id'] ?>" title=""
                                       class="btn btn-sm btn-primary a_edit blank" data-reload="<?php echo $type ?>" >
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="<?php echo $r['id'] ?>" id="delete_<?php echo $r['id'] ?>" title=""
                                       class="btn btn-sm btn-danger a_delete blank" data-reload="<?php echo $type ?>" >
                                        <i class="fas fa-trash"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </li>
        <?php endforeach; ?>
    </ul>
    <i class="fas fa-spinner-third"></i>
<?php else : ?>
    <p>Nessuna voce inserito nell'archivio digitale</p>
<?php endif; ?>