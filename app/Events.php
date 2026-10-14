<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');


use System\Event;

Event::add('pre_system', function () {

    $domainInfo = new \Events\DomainInfo();
    $domainInfo->handle();

    // Sessione
    $session = session();
    \System\Registry::set('last_history_url', $session->getFlash('last_history_url'));

    // Origine della navigazione verso i form di creazione/modifica (chiave __frm_history_t,
    // letta da referenceOriginForRegenerateToken()). Saltata per le richieste frammento
    // (paginazione AJAX, vedi renderFrontFragment() in FrontOffice/Utility.php): un cambio
    // pagina di una tabella non e' una navigazione da tracciare. Stessa condizione di
    // isFrontFragmentRequest(), duplicata qui perche' quell'helper non e' ancora caricato a
    // questo punto di pre_system.
    if (empty($_SERVER['HTTP_X_PATOS_FRAGMENT']) || !\System\Input::isAjax()) {
        $history = new \Events\BrowsingHistory();
        $history->addUrl(currentQueryStringUrl());
    }

    // Headers Sicurezza
    $headers = new \Events\HeadersSecurity();
    $headers->handle();
});

Event::add('pre_route', function () {
    $session = session();
    $session->setFlash('last_history_url', escapeXss(currentQueryStringUrl(), true, false));
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
