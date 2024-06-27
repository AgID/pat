<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

if (!defined('_FRAMEWORK_')) exit('No direct script access allowed');

return [

    /**
     * Breadcrumbs senza prefisso nel costruttore della classe System\Breadcrumbs()
     */
    'base_url' => 'admin/dashboard',
    'ico_home' => '<i class="fas fa-home"></i>',
    'crumb_divider' => '',
    'tag_open' => '<ol class="breadcrumb float-sm-right">',
    'tag_close' => '</ol>',
    'crumb_open' => '<li class="breadcrumb-item">',
    'crumb_last_open' => '<li class="breadcrumb-item active">',
    'crumb_close' => '</li>',

    /**
     * Breadcrumbs con prefisso "back_office" nel costruttore della classe System\Breadcrumbs()
     */
    'back_office' => [
        'base_url' => '',
        'ico_home' => '',
        'crumb_divider' => '',
        'tag_open' => '<ol class="breadcrumb">',
        'tag_close' => '</ol>',
        'crumb_open' => '<li>',
        'crumb_last_open' => '<li class="active">',
        'crumb_close' => '</li>',
    ],

    /**
     * Breadcrumbs con prefisso "front_office" nel costruttore della classe System\Breadcrumbs()
     */
    'front_office' => [
        'base_url' => '',
        'ico_home' => '',
        'crumb_divider' => '',
        'tag_open' => '<ol class="breadcrumb">',
        'tag_close' => '</ol>',
        'crumb_open' => '<li>',
        'crumb_last_open' => '<li class="active">',
        'crumb_close' => '</li>',
    ],

    /**
     * Breadcrumbs con prefisso "custom" nel costruttore della classe System\Breadcrumbs()
     */
    'custom' => [
        'base_url' => '',
        'ico_home' => '',
        'crumb_divider' => '',
        'tag_open' => '',
        'tag_close' => '',
        'crumb_open' => '',
        'crumb_last_open' => '',
        'crumb_close' => '',
    ]

    // ... Altre customizzazioni con prefisso nella classe System\Breadcrumbs()..
];
