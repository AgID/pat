<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ActivityLogModel;
use System\Agent;
use System\Input;

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

        $arrayValues = [
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : $getIdentity['id'] ?? null,
            'institution_id' => !empty($data['institution_id']) ? $data['institution_id'] : checkAlternativeInstitutionId(),
            'action' => !empty($data['action']) ? $data['action'] : null,
            'action_type' => !empty($data['action_type']) ? $data['action_type'] : null,
            'description' => !empty($data['description']) ? $data['description'] : null,
            'client_info' => self::compress(self::userAgent()),
            'request_post' => !empty($data['request_post']) ? self::compress($data['request_post']) : null,
            'request_get' => !empty($data['request_get']) ? self::compress($data['request_get']) : null,
            'request_file' => !empty($data['request_file']) ? self::compress($data['request_get']) : null,
            'ip_address' => ($isRegisterIp === true) ? Input::ipAddress() : null,
            'uri' => !empty($data['uri']) ? $data['uri'] : currentQueryStringUrl(),
            'referer' => Input::server('HTTP_REFERER') ?? @$_SESSION['flash']['last_history_url'],
            'is_superadmin' => !isSuperAdmin() ? 0 : 1,
            'object_id' => !empty($data['object_id']) ? $data['object_id'] : null,
            'record_id' => !empty($data['record_id']) ? $data['record_id'] : null,
            'area' => !empty($data['area']) ? $data['area'] : null,
            'platform' => !empty($data['platform']) ? $data['platform'] : 'pat',
        ];

        ActivityLogModel::create($arrayValues);

        return null;
    }

    /**
     * @return S
     */
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
