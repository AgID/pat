<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed');


use System\Event;

Event::add('pre_system', function () {
    $pathFile = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR . 'Isweb' . DIRECTORY_SEPARATOR . 'Plugins.php';

    if (file_exists($pathFile)) {
        require_once $pathFile;

    } else {
        // Comportamento standard dell'applicazione.
        $domainInfo = new \Events\DomainInfo();
        $domainInfo->handle();
    }

    // Sessione
    $session = new \System\Session();
    \System\Registry::set('last_history_url', $session->getFlash('last_history_url'));

    // Init Addons
    \Helpers\Addons::init();

    // Storico navigazione
    $history = new \Events\BrowsingHistory(10);

    // Aggiunge lo storico di navigazione..
    $history->addUrl(currentQueryStringUrl());

});

Event::add('pre_route', function () {
    $session = new \System\Session();
    $session->setFlash('last_history_url', escapeXss(currentQueryStringUrl(), true, false));
});

Event::add('pre_job', function () {
});


/**
 *
 * Evento in PRE System
 * Event::add('pre_system',function (){});
 *
 * Evento in POST System
 * Event::add('post_system',function (){});
 *
 * // PRE Route
 * Event::add('pre_route',function (){});
 *
 * // POST Route
 * Event::add('post_route',function (){});
 *
 * // PRE Route
 * Event::add('pre_controller',function (){});
 *
 * // POST Route
 * Event::add('post_controller',function (){});
 *
 * // PRE Job
 * Event::add('pre_job',function (){});
 *
 * // POST Job
 * Event::add('post_job',function (){});
 *
 */

