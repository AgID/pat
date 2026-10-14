<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ActivityLogModel;
use System\Input;
use System\Agent;

class ActivityLog
{
    /**
     * Registra nel database le attività quotidiane svolte nel Pat OS
     *
     * @param array $data         Dati per il log delle
     *                            attività
     * @param bool  $isRegisterIp Indica se registrare o meno l'indirizzo IP
     * @return null
     * @throws Exception
     */
    public static function create(array $data, bool $isRegisterIp = false)
    {
        // Dati per registrazione ActivityLogs
        $fields = ['id'];

        $getIdentity = authPatOs()->getIdentity($fields);

        //Introdotto perche per richieste di spostamento cartelle/file in archivio file
        //In questi casi l'uri supera ampiamente i 1000 caratteri del campo sul DB
        $uri = !empty($data['uri']) ? $data['uri'] : currentQueryStringUrl();
        if(strlen($uri) > 980 ){
            $uri = substr($uri, 0, 980);
        }

        $arrayValues = [
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : $getIdentity['id'] ?? null,
            'institution_id' => !empty($data['institution_id']) ? $data['institution_id'] : checkAlternativeInstitutionId(),
            'action' => !empty($data['action']) ? $data['action'] : null,
            'action_type' => !empty($data['action_type']) ? $data['action_type'] : null,
            'description' => !empty($data['description']) ? $data['description'] : null,
            'client_info' => self::compress(self::userAgent()),
            'ip_address' => ($isRegisterIp === true) ? Input::ipAddress() : null,
            'uri' => $uri,
            'referer' => Input::server('HTTP_REFERER') ?? @$_SESSION['flash']['last_history_url'],
            'object_id' => !empty($data['object_id']) ? $data['object_id'] : null,
            'record_id' => !empty($data['record_id']) ? $data['record_id'] : null,
            'parent_id' => !empty($data['parent_id']) ? $data['parent_id'] : null,
            'area' => !empty($data['area']) ? $data['area'] : null,
            'platform' => !empty($data['platform']) ? $data['platform'] : 'pat',
            'origin' => !empty($data['origin']) ? $data['origin'] : 'web',
        ];

        $activityLog = ActivityLogModel::create($arrayValues);

        return $activityLog->id ?? null;
    }

    public static function userAgent()
    {

        $agent = new Agent();

        $device = $agent->device();
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);

        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);

        return $device . ' | ' . $browser . ' (' . $browserVersion . ') | ' . $platform . '(' . $platformVersion . ')';
    }

    /**
     * @param null $string
     * @param int  $level
     * @return false|S
     */
    public static function compress($string = null, $level = 9)
    {
        return base64_encode(gzcompress(serialize($string), 9));
    }

    /**
     * @param null $string
     * @return null
     */
    public static function unCompress($string = null)
    {

        if (!empty($string)) {

            var_dump(unserialize(gzuncompress(base64_decode($string))));

        }

        return null;
    }
}
