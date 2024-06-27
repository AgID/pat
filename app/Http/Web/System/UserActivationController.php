<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Helpers\ActivityLog;
use Helpers\Obfuscate;
use Model\UsersModel;
use System\Input;
use System\Validator;

class UserActivationController extends \System\BaseController
{
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    public function index()
    {
        $validator = new Validator();
        $validator->label('token')
            ->value(uri()->segment(2))
            ->required()
            ->isNaturalNoZero()
            ->end();

        $validator->label('activation code')
            ->value(Input::get('ak'))
            ->required()
            ->regex('/^[A-Z0-9\:\$]{21,23}$/i')
            ->end();

        if ($validator->isSuccess()) {

            $id = Obfuscate::decode(Input::get('ak'));

            $user = UsersModel::where('active', 0)
                ->where('active_key', '=', uri()->segment(2))
                ->find($id);

            if (!empty($user)) {

                // Activity Log
                $fullName = $user->name;
                $username = $user->username;
                $day = date('d-m-Y');
                $hours = date('H:i');
                $lastVisit = date('Y-n-d H:i:s');

                ActivityLog::create([
                    'user_id' => $id,
                    'action' => "{$fullName} ha attivato il proprio account nel giorno {$day} alle ore {$hours}",
                    'username' => $username,
                    'request_post' => [
                        'post' => Input::post(),
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                ]);

                // Activate users
                $user->active = 1;
                $user->active_key = null;
                $user->last_visit = $lastVisit;
                $user->save();

                // !ToDo
                // Inserire lo script che notifica all'amministratore di sistema l'attivazione di un nuovo utente.

                // Redirect auth..
                session()->setFlash('user_activation', true);
                redirect('/auth');
            }

        } else {
            trace($validator->getErrors());
        }

    }
}