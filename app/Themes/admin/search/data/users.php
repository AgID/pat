<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Arr;

defined('_FRAMEWORK_') or exit('No direct script access allowed');
?>

<div class="row">
    <?php if (!empty($results['data'])): ?>
        <div class="col-12 col-sm-12 col-md-12 d-flex align-items-stretch flex-column">

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:35%;">NOME UTENTE</th>
                            <th style="width:30%;">USERNAME</th>
                            <th style="width:10%;">PROFILI ACL</th>
                            <th style="width:10%;">INDIRIZZO EMAIL</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($results['data'] as $r):

                            if (!empty($r['profiles']) && is_array($r['profiles'])) {

                                $tmpProfiles = Arr::pluck($r['profiles'], 'name');
                                $profiles = str_replace(',', nbs(2), implode(',',
                                    array_map(
                                        function ($profile) {
                                            return ('<small class="badge badge-primary">' . escapeXss($profile) . '</small>');
                                        }, $tmpProfiles)));

                            } else {

                                $profiles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

                            }

                            $dataToggle = ($r['active'] === 0)
                                ? 'Non attivo'
                                : 'Attivo';

                            $colorGrey = ($r['active'] === 0)
                                ? 'grey'
                                : 'lightgrey';

                            $icon = ($r['active'] === 0)
                                ? 'lock'
                                : 'lock-open';

                            ?>
                            <tr>
                                <td>
                                    <?php echo '<i data-toggle="tooltip" data-placement="top" data-original-title="Utente ' . $dataToggle . '" 
                    class="fas fa-' . $icon . ' fa-sm" style="color: ' . $colorGrey . '"></i> &nbsp; ' . $r['name']; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['username']) ? escapeXss(ucfirst($r['username'])) : 'N.D.' ?>
                                </td>
                                <td>
                                    <?php echo $profiles; ?>
                                </td>
                                <td>
                                    <?php echo !empty($r['email']) ? $r['email'] : 'N.D.'; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo siteUrl('admin/user/edit/' . $r['id']); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th style="width:35%;">NOME UTENTE</th>
                            <th style="width:30%;">USERNAME</th>
                            <th style="width:10%;">PROFILI ACL</th>
                            <th style="width:10%;">INDIRIZZO EMAIL</th>
                            <th class="text-center" style="width:5%;">AZIONI</th>
                        </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="mt-2 col-lg-12 text-center">
            Nessun occorrenza trovata nella sezione <strong>"Utenti"</strong>
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

