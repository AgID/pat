<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 /* List data-table values, i.e: $_SERVER, $_GET, .... */ ?>
<div class="details">
    <h2 class="details-heading">Environment &amp; details:</h2>

    <div class="data-table-container" id="data-tables">
        <?php foreach ($tables as $label => $data): ?>
            <?php
            $bl=array("Session", 'Server/Request Data', 'Environment Variables', 'Registered Handlers');
            ?>
            <?php if(
                !in_array($label,$bl)
            ): ?>
                <div class="data-table" id="sg-<?php echo $tpl->escape($tpl->slug($label)) ?>">
                    <?php if (!empty($data)): ?>
                        <label><?php echo $tpl->escape($label) ?></label>
                        <table class="data-table">
                            <thead>
                            <tr>
                                <td class="data-table-k">Key</td>
                                <td class="data-table-v">Value</td>
                            </tr>
                            </thead>
                            <?php foreach ($data as $k => $value): ?>
                                <tr>
                                    <td><?php echo $tpl->escape($k) ?></td>
                                    <td><?php echo $tpl->dump($value) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </table>
                    <?php else: ?>
                        <label class="empty"><?php echo $tpl->escape($label) ?></label>
                        <span class="empty">empty</span>
                    <?php endif ?>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</div>
