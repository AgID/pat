<?php

/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\AclProfilesModel;
use System\Input;
use System\Registry;
use System\Validator;

class AclUserProfileValidator
{
    public Validator $validator;

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * @param null $mode Indica l'operazione che si sta eseguendo
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($mode = null): array
    {
        $this->validator->label('Id Profilo ACL')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($mode) {

                $check = AclProfilesModel::select(['id', 'institution_id'])
                    ->where('id', uri()->segment(4, 0));

                if (!empty($mode) && !isSuperAdmin(true)) {
                    $check = $check->where('is_system', 0);
                }

                $check = $check->first();

                if ($check == null) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'profilo ')
                    ];
                }

                //Se esiste il profilo, lo salvo nel registro
                Registry::set('temp_profile', $check);

                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Effettua lo storage di un nuovo profilo
     * @param string $mode Tipologia di operazione
     * @return array
     * @throws Exception
     * @noinspection PhpConditionAlreadyCheckedInspection
     */
    public function storage(string $mode = 'insert'): array
    {
        $profileACL = [];

        // Update Data...
        if ($mode !== 'insert') {

            $this->validator->label('identificativo profilo acl')
                ->value(Input::post('id'))
                ->required()
                ->isNaturalNoZero()
                ->add(function () {

                    $profile = AclProfilesModel::select(['id']);

                    if (isSuperAdmin(true)) {
                        $profile->where('is_system', '=', 0);
                    }

                    $profile->find(Input::post('id'));

                    if ($profile === null) {

                        return [
                            'error' => __('error_update_is_system', null, 'patos')
                        ];
                    }

                    return null;
                })
                ->end();
        }

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Titolo')
            ->value(Input::post('title'))
            ->required()
            ->betweenString(3, 255)
            ->end();

        $this->validator->label('Descrizione')
            ->value(Input::post('description'))
            ->required()
            ->betweenString(3, 500)
            ->end();

        $this->validator->label('Permesso delle operazioni di blocco/sblocco utenti')
            ->value(Input::post('lock_user'))
            ->in('0,1')
            ->end();

        // Validazione Sezioni Back Office
        if (!empty($_POST['acl'])) {

            $i = 1;

            foreach ($_POST['acl'] as $key => $item) {

                $sectionName = !empty($item['name']) ? '"' . $item['name'] . '"' : 'riga' . $i;

                if (!empty($item['add'])) {

                    $this->validator->label('AGGIUNGI')
                        ->value($item['add'])
                        ->in('1', sprintf(__('error_permits_back_office', null, 'patos'), 'AGGIUNGI', $sectionName, $i))
                        ->end();
                }

                if (!empty($item['read'])) {

                    $this->validator->label('LEGGI')
                        ->value($item['read'])
                        ->in('1', sprintf(__('error_permits_back_office', null, 'patos'), 'LEGGI', $sectionName, $i))
                        ->end();
                }

                if (!empty($item['modify'])) {

                    $this->validator->label('MODIFICA')
                        ->value($item['modify'])
                        ->in('1', sprintf(__('error_permits_back_office', null, 'patos'), 'MODIFICA', $sectionName, $i))
                        ->end();
                }

                if (!empty($item['delete'])) {

                    $this->validator->label('CANCELLA')
                        ->value($item['delete'])
                        ->in('1', sprintf(__('error_permits_back_office', null, 'patos'), 'CANCELLA', $sectionName, $i))
                        ->end();
                }

                if (!empty($item['app_io'])) {

                    $this->validator->label('APP IO')
                        ->value($item['app_io'])
                        ->in('1', sprintf(__('error_permits_back_office', null, 'patos'), 'APP IO', $sectionName, $i))
                        ->end();
                }

                if (!empty($item['add']) || !empty($item['read']) || !empty($item['modify']) || !empty($item['delete']) || !empty($item['adv']) || !empty($item['app_io'])) {

                    unset($_POST['acl'][$i]);
                    $profileACL[$key] = $item;
                }

                $i++;
            }
        }

        // Validazione sezioni Front Office
        if (!empty($_POST['section_fo'])) {

            $i = 1;

            foreach ($_POST['section_fo'] as $key => $value) {

                $sectionNameFrontOffice = !empty($_POST['name_fo'][$key]) ? $_POST['name_fo'][$key] : 'riga' . $i;

                if (!empty($value)) {

                    $this->validator->label('MODIFICA')
                        ->value($value)
                        ->isInt(sprintf(__('error_permits_front_office', null, 'patos'), 'MODIFICA', $sectionNameFrontOffice, $i))
                        ->end();
                }

                $i++;
            }
        }

        Registry::set('aclProfilePost', $profileACL);

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore per le operazioni multiple(Delete)
     *
     * @param bool $lockUnlock Indica se l'operazione è di blocco/sblocco utenti
     * @return array
     * @throws Exception
     */
    public function multipleSelection(bool $lockUnlock = true): array
    {
        $this->validator->label('Identificativi elementi');
        $this->validator->value(Input::get('ids'));
        $this->validator->required();
        $this->validator->regex('/^[0-9,]+$/', __('multiple_selection_errors', null, 'patos'));
        $this->validator->add(function () {

            $ids = explode(',', Input::get('ids'));
            $isError = false;

            if (!empty($ids) && is_array($ids)) {
                $isError = true;
            }

            if ($isError === true) {

                //Recupero i profili selezionati da eliminare
                $profilesAcl = AclProfilesModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($profilesAcl === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $profilesAcl);
                }
            }

            if (!$isError) {

                return ['error' => __('no_permits', null, 'patos')];
            }

            return null;
        });

        $this->validator->end();

        if ($lockUnlock) {
            $this->validator->label('azione');
            $this->validator->value(Input::get('action'));
            $this->validator->required();
            $this->validator->in('lock,unlock');
            $this->validator->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
