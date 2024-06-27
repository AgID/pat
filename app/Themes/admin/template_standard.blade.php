{{--  Template standard principale area riservata --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row">
    <div class="col-md-12">
        <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-warning">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="../dist/img/user7-128x128.jpg" alt="User Avatar">
                </div>
                <h3 class="widget-user-username">Nadia Carmichael</h3>
                <h5 class="widget-user-desc">Lead Developer</h5>
            </div>
            <div class="card-footer p-0">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Projects <span class="float-right badge bg-primary">31</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Tasks <span class="float-right badge bg-info">5</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Completed Projects <span class="float-right badge bg-success">12</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            Followers <span class="float-right badge bg-danger">842</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
