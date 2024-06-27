<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use \Maer\Config\Config;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Database extends Capsule
{
    public function __construct($datasource = null)
    {
        parent::__construct();

        // Settaggi per la connessione al database
        $config = new Config();
        $config->load(APP_PATH . 'Config/database.php');
        $datasource = ($datasource === null) ? $config->get('db_connect_default') : $datasource;
        $params = $config->get('params.' . $datasource);

        //Connessione al database.
        $this->addConnection((array) $params);
        $this->setEventDispatcher(new Dispatcher(new Container));
        $this->setAsGlobal();
        $this->bootEloquent();


    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function getConnectionInstance()
    {
        return $this->getConnection();

    }

    /**
     * @return void
     */
    public function enableQueryLogging()
    {
        $this->getConnection()->enableQueryLog();
    }

    //*

    public function getQueryLog()
    {
        return $this->getConnection()->getQueryLog();
    }
}
