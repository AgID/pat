<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Events;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use System\Cookie;
use System\Input;
use System\Registry;
use System\Validator;
use System\Session;

// Models
use Model\InstitutionsModel;

/**
 * Class DomainInfo
 * @package Events
 * @description Classe che identifica il nome del dominio
 */
class DomainInfo
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * DetectDomain constructor.
     */
    public function __construct()
    {
        helper('url');
        $this->session = new Session();
    }

    /**
     * @description Setta il dominio corrente del cloud
     * @throws Exception
     * @return void
     */
    public function handle(): void
    {

        // Get domain DB
        $getInstitution = InstitutionsModel::where('id', '=', 1)
            ->first();

        if ($getInstitution === null) {

            echo show404();
            die();

        }

        // Validation URI
        $validator = new Validator();
        $validator->label('Uri')
            ->isUri($getInstitution['trasparenza_urls']);

        // 404
        if ($validator->isSuccess() === false) {

            echo showError('Attenzione','URL non valido nella configurazione dell\'applicativo');
            die();

        }

        $domain = $this->getDomain($getInstitution['trasparenza_urls']);

        // Load config www
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/custom.php');
        $appendWww = $config->get('www_site_url');

        // SET URL
        define('SITE_URL', $domain['url']);

        // SET URI
        define('SITE_URI', $domain['uri']);

        // SET HOST
        define('HOST', $domain['host']);

        // Query Result
        $institutionInfo = $getInstitution->toArray();

        // Cookie Url
        if (!Cookie::get('pat_os_app_url')) {

            Cookie::set('pat_os_app_url', $domain['url'], 0);

        }

        // Cookie Uri
        if (!Cookie::get('pat_os_app_uri')) {

            Cookie::set('pat_os_app_uri', $domain['uri'], 0);

        }

        // Cookie Host
        if (!Cookie::get('pat_os_app_host')) {

            Cookie::set('pat_os_app_host', $domain['host'], 0);

        }

        // Definisco la sessione di dominio
        if (!$this->session->has('pat_os_domain_info')) {

            $this->session->set('pat_os_domain_info', $institutionInfo);

        }

        // Storage Singleton
        Registry::set('pat_os_info_domain', $institutionInfo);

        // SET domain name
        Registry::set('__pat_os_app_domain_name__', ($appendWww === false) ? $domain['uri'] : $domain['url']);

    }

    /**
     * @description Metodo privato che identifica il nome del dominio
     * @param $url
     * @return array
     */
    private function getDomain($url): array
    {

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        $protocol =  (bool) preg_match('/^https$/', $scheme);
        $server = Input::server();

        return [
            'protocol' => ($protocol === true) ? 'https' : 'http',
            'host' => $host,
            'uri' => $scheme . '://' . $host,
            'url' => $scheme . '://www.' . $host,
            'request_uri' => $server['REQUEST_URI'],
        ];

    }
}