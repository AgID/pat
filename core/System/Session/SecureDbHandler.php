<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Session;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use SessionHandler;
use System\Input;

/**
 * Class SecureDbHandler
 * @package System\Session
 */
class SecureDbHandler extends SessionHandler
{
    /**
     * @var
     */
    private $db;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $sessionId;

    /**
     * @var string
     */
    private $storageData;

    /**
     * @var string
     */
    private $storageExpire;

    /**
     * @var string
     */
    private $storageIpAddress;

    /**
     * SecureDbHandler constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->table = config('table', 'sessions', 'session');
        $this->sessionId = config('session_id', 'id', 'session');
        $this->storageData = config('data', 'data', 'session');
        $this->storageExpire = config('expire', 'expire', 'session');
        $this->storageIpAddress = config('ip_address', 'ip', 'session');

    }

    /**
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function open($save_path, $session_name)
    {
        $this->db = new \System\Database();

        return true;
    }

    /**
     * @param string $id
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function read($id)
    {
        $_table = $this->table;
        $_id = $this->sessionId;
        $_data = $this->storageData;

        $DB = $this->db;

        if (!empty($id) && strlen($id) >= 1) {
            $data = $DB::select("SELECT `{$_data}` FROM `{$_table}` WHERE {$_id} = ?", [
                $id
            ]);

            if (!empty($data[0])) {
                return $data[0]->data;
            }
        }


        return '';
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     * @throws \Exception
     */
    #[\ReturnTypeWillChange]
    public function write($id, $data)
    {
        $_table = $this->table;
        $_id = $this->sessionId;
        $_data = $this->storageData;
        $_Expire = $this->storageExpire;
        $_ip = $this->storageIpAddress;

        $DB = $this->db;

        $DB::statement("REPLACE INTO `{$_table}` VALUES (:{$_id}, :{$_data}, :{$_Expire}, :{$_ip})", [
            'id' => $id,
            'data' => $data,
            'expire' => time(),
            'ip' => \System\Input::ipAddress()
        ]);

        return true;
    }

    /**
     * @param string $id
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        $_table = $this->table;
        $_id = $this->sessionId;

        $DB = $this->db;

        if (!Input::isAjax()) {

            if (!empty($id) && strlen($id) >= 1) {

                @$DB::statement("DELETE FROM `{$_table}` WHERE :{$_id}", [
                    'id' => $id
                ]);

            }

        }

        return true;
    }

    /**
     * @param int $max
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function gc($max)
    {
        $_table = $this->table;
        $_Expire = $this->storageExpire;

        $old = time() - $max;
        $DB = $this->db;

        if (!Input::isAjax()) {

            if (!empty($old) && strlen($old) >= 1) {

                @$DB::statement("DELETE FROM `{$_table}` WHERE `{$_Expire}` < ?", [$old]);

            }
        }

        return true;

    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function close()
    {
        $DB = $this->db;
        $DB::disconnect();

        return true;
    }
}
