<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row">
    <?php if (!empty($results['data'])): ?>
        <?php
        foreach ($results['data'] as $profile):
            $imgProfile = !empty($profile['photo'])
                ? 'media/' . instituteDir() . '/assets/images/' . $profile['photo']
                : 'assets/admin/img/avatar.png';
            ?>
            <div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                <div class="card bg-light d-flex flex-fill">

                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7">
                                <h2 class="lead">
                                    <b>
                                        <?php if (!empty($profile['title'])): ?>
                                            <?php echo ucfirst($profile['title']) ?>
                                        <?php endif; ?>
                                        <?php echo escapeXss($profile['full_name']) ?>
                                    </b>
                                </h2>
                                <p class="text-muted text-sm">
                                    <b>Ruolo:</b>
                                    <?php if (!empty($profile['role']['name'])): ?>
                                        <?php echo $profile['role']['name'] ?>
                                    <?php else: ?>
                                        N.D.
                                    <?php endif; ?>
                                </p>
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small">
                                            <span class="fa-li">
                                                <i class="far fa-envelope"></i>
                                            </span> E-mail:
                                        <?php if (!empty($profile['email'])): ?>
                                            <?php echo $profile['email'] ?>
                                        <?php else: ?>
                                            N.D.
                                        <?php endif; ?>
                                    </li>
                                    <li class="small">
                                            <span class="fa-li">
                                                 <i class="far fa-envelope"></i>
                                            </span> P.E.C:
                                        <?php if (!empty($profile['certified_email'])): ?>
                                            <?php echo $profile['certified_email'] ?>
                                        <?php else: ?>
                                            N.D.
                                        <?php endif; ?>
                                    </li>
                                    <li class="small">
                                            <span class="fa-li">
                                                <i class="fas fa-lg fa-phone"></i>
                                            </span> Telefono
                                        <?php if (!empty($profile['phone'])): ?>
                                            <?php echo $profile['phone'] ?>
                                        <?php else: ?>
                                            N.D.
                                        <?php endif; ?>
                                    </li>
                                    <li class="small">
                                            <span class="fa-li">
                                                <i class="fas fa-lg fa-phone"></i>
                                            </span> Cellulare
                                        <?php if (!empty($profile['mobile_phone'])): ?>
                                            <?php echo $profile['mobile_phone'] ?>
                                        <?php else: ?>
                                            N.D.
                                        <?php endif; ?>
                                    </li>
                                    <li class="small">
                                            <span class="fa-li">
                                                <i class="fas fa-mobile-alt"></i>
                                            </span> Fax
                                        <?php if (!empty($profile['fax'])): ?>
                                            <?php echo $profile['fax'] ?>
                                        <?php else: ?>
                                            N.D.
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-5 text-center">
                                <img src="<?php echo baseUrl($imgProfile) ?>"
                                     alt="<?php echo $profile['full_name'] ?>"
                                     class="img-circle img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right">
                            <a href="<?php echo siteUrl('admin/personnel/edit/' . $profile['id']) ?>"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-user"></i> Visualizza profilo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="mt-2 col-lg-12 text-center">
            Nessun occorrenza trovata nella sezione <strong>"Personale"</strong>
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
