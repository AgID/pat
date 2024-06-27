<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\InstitutionsModel;
use System\BaseController;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Validator;

class UtilityController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     * @url system/list/institutions.html
     * @method GET
     */
    public function institutes()
    {
        $data = [];

        if (isSuperAdmin() === true && Input::isAjax() === true) {

            $institutions = InstitutionsModel::withoutGlobalScopes()
                ->orderBy('full_name_institution', 'ASC')
                ->select('full_name_institution', 'id')
                ->get()
                ->toArray();

            foreach ($institutions as $institution) {

                $data[] = [
                    'id' => (int)$institution['id'],
                    'text' => $institution['full_name_institution']
                ];

            }

            $data[] = [
                'id' => 0,
                'text' => 'Tutti gli Enti'
            ];

        } else {

            echo showError();

        }

        echo json_encode($data);
    }

    /**
     * @throws Exception
     * @url  /system/change/institutions.html
     * @method GET
     */
    public function changeInstitution()
    {
        $json = new JsonResponse();
        $code = $json->success();
        $validator = new Validator();

        if (isSuperAdmin() === true && Input::isAjax() === true) {

            $validator->label('Identificativo instituto')
                ->value(Input::get('id'))
                ->required()
                ->isNatural()
                ->add(function () {
                    $id = (int)Input::get('id');

                    if ($id !== 0) {

                        $institutions = InstitutionsModel::select(['id', 'full_name_institution', 'short_institution_name'])->withoutGlobalScopes()->find(Input::get('id'));
                        if ($institutions === null) {

                            return ['error' => 1];
                        }

                        $id = $institutions->id;
                        Registry::set('__change_institution_full_name', $institutions->full_name_institution);
                        Registry::set('__change_institution_short_name', $institutions->short_institution_name);

                    }

                    Registry::set('__change_institution_id', $id);

                    return null;
                })
                ->end();

            if ($validator->isSuccess()) {

                $id = Registry::get('__change_institution_id');
                $fullName = !Registry::exist('__change_institution_full_name')
                    ? 'Tutti gli enti'
                    : Registry::get('__change_institution_full_name');
                $shortName = !Registry::exist('__change_institution_short_name')
                    ? 'Tutti gli enti'
                    : Registry::get('__change_institution_short_name');

                session()->set('alternative_pat_os_id', $id);
                session()->set('alternative_pat_os_full_name', $fullName);
                session()->set('alternative_pat_os_short_name', $shortName);

                authPatOs()->addStorage([
                    'alternative_pat_os_id' => $id,
                    'alternative_pat_os_full_name' => $fullName,
                    'alternative_pat_os_short_name' => $shortName,
                ]);

                $json->set('message', $id);

            } else {

                $code = $json->bad();
                $json->error('error', $validator->getErrorsHtml());

            }

        } else {

            $code = $json->bad();
            $json->error('error', 'No ajax request');

        }

        $json->setStatusCode($code);
        $json->response();

    }

    public function currentAdministration()
    {
        $json = new JsonResponse();
        $code = $json->success();

        if (isSuperAdmin() === true && Input::isAjax() === true) {

            $instituteInfo = patOsInstituteInfo(['id', 'full_name_institution', 'short_institution_name']);

            session()->set('alternative_pat_os_id', $instituteInfo['id']);
            session()->set('alternative_pat_os_full_name', $instituteInfo['full_name_institution']);
            session()->set('alternative_pat_os_short_name', $instituteInfo['short_institution_name']);

            authPatOs()->addStorage([
                'alternative_pat_os_id' => $instituteInfo['id'],
                'alternative_pat_os_full_name' => $instituteInfo['full_name_institution'],
                'alternative_pat_os_short_name' => $instituteInfo['short_institution_name'],
            ]);

            $json->set('message', [$instituteInfo['id'], $instituteInfo['full_name_institution'], $instituteInfo['short_institution_name']]);

        } else {

            $code = $json->bad();
            $json->error('error', 'No ajax request');

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @throws Exception
     * @url  /system/restore/institution.html
     * @method GET
     */
    public function restoreInstitution()
    {
        $json = new JsonResponse();
        $code = $json->success();

        if (isSuperAdmin() === true && Input::isAjax() === true) {

            session()->kill('alternative_pat_os_id');
            session()->kill('alternative_pat_os_full_name');
            session()->kill('alternative_pat_os_short_name');

            authPatOs()->removeStorage([
                'alternative_pat_os_id',
                'alternative_pat_os_full_name',
                'alternative_pat_os_short_name',
            ]);

        } else {

            $code = $json->bad();
            $json->error('error', 'No ajax request');

        }

        $json->setStatusCode($code);
        $json->response();
    }
}