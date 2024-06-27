<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\CommissionsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Commissioni e gruppi consiliari (object_commissions)
 */
class CommissionValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una commissione con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID commissione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la commissione con l'id passato in input
                $check = CommissionsModel::select()
                    ->where('id', uri()->segment(4, 0))
                    ->with(['president' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'role_id', 'title', 'role.name']);
                        $query->join('role', 'role.id', '=', 'object_personnel.role_id', 'left outer');
                    }])
                    ->with(['vicepresidents' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'title', 'email', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id', 'left outer');
                    }])
                    ->with(['secretaries' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id', 'left outer');
                    }])
                    ->with(['substitutes' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id', 'left outer');
                    }])
                    ->with(['members' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id', 'left outer');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'commissione ')
                    ];
                }

                //Se esiste la commissione la salvo nel registro
                Registry::set('commission', $check);

                return null;
            })
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert'): array
    {
        if ($mode == 'archiving') {
            return $this->archivingValidate();
        } else {
            return $this->validate($mode);
        }
    }

    /**
     * Metodo per la validazione dei campi del form per l'archiviazione
     *
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function archivingValidate(): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Item Id')
            ->value(Input::post('itemId'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->add(function () {
                $user = CommissionsModel::where('id', Input::post('itemId'))->first();
                if (empty($user)) {
                    return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Commissione')];
                }
            })
            ->end();

        $this->validator->label('Attiva dal')
            ->value(Input::post('active_from'))
            ->isDate('Y-m-d')
            ->end();


        $this->validator->label('Attiva fino al')
            ->value(Input::post('active_to'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('active_to') < Input::post('active_from')) {

                    return ['error' => __('invalid_archive_ending_date', null, 'patos')];

                }
                return null;
            })
            ->end();


        $this->validator->label('Data di fine pubblicazione in archivio')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('end_date') <= Input::post('active_to')) {

                    return ['error' => __('invalid_archive_date', null, 'patos')];

                }
                return null;
            })
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->label('Nome')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(4, 60)
            ->end();

        $this->validator->label('Tipo')
            ->value(Input::post('typology'))
            ->required()
            ->in('commissione,gruppo consiliare')
            ->end();

        $this->validator->label('Presidente o capogruppo')
            ->value(Input::post('president_id'))
            ->required()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Testo di descrizione')
            ->value(Input::post('description'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Recapito email')
            ->value(Input::post('email'))
            ->isEmail()
            ->betweenString(2, 40)
            ->end();

        $this->validator->label('Recapito telefonico fisso')
            ->value(Input::post('phone'))
            ->maxLength(17, __('max_phone_length', null, 'patos'))
            ->minLength(3, __('min_phone_length', null, 'patos'))
            ->regex('/^([0-9().\-+ ]){3,17}$/', __('phone_error', null, 'patos'))
            ->end();

        $this->validator->label('Recapito fax')
            ->value(Input::post('fax'))
            ->betweenString(3, 30)
            ->end();

        $this->validator->label('Indirizzo')
            ->value(Input::post('address'))
            ->betweenString(5, 80)
            ->end();

        $this->validator->label('Attiva dal')
            ->value(Input::post('activation_date'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Attiva fino al')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_date') <= Input::post('activation_date')) {

                    return ['error' => __('invalid_end_date_activation', null, 'patos')];

                }
                return null;
            })
            ->end();

        if (filesUploaded('img')) {
            $this->validator->label('Immagine da visualizzare')
                ->file(Input::files('img'))
                ->required()
                ->allowed(['png', 'jpeg', 'jpg', 'gif'])
                ->maxSize('5MB')
                ->minSize('1KB')
                ->allowedDimensions('50', '1024', '50', '1024')
                ->end();
        }

        $this->validator->label('Ordine di visualizzazione')
            ->value(Input::post('order'))
            ->isNaturalNoZero()
            ->end();

        if (Input::post('vice_presidents')) {
            foreach (explode(',', (string)Input::post('vice_presidents')) as $vice) {
                $this->validator->label('Vicepresidente ' . $vice)
                    ->value($vice)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('secretaries')) {
            foreach (explode(',', (string)Input::post('secretaries')) as $secretarie) {
                $this->validator->label('Segretario ' . $secretarie)
                    ->value($secretarie)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('substitutes')) {
            foreach (explode(',', (string)Input::post('substitutes')) as $substitute) {
                $this->validator->label('Membro supplente ' . $substitute)
                    ->value($substitute)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('members')) {
            foreach (explode(',', (string)Input::post('members')) as $member) {
                $this->validator->label('Membro ' . $member)
                    ->value($member)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $commission = CommissionsModel::where('id', Input::post('id'))->first();
                    if (empty($commission)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Commissione/Gruppo consiliare')];
                    }
                })
                ->end();
        }


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore dell'eliminazione multipla
     *
     * @return array
     * @throws Exception
     */
    public function multipleSelection(): array
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

            if ($isError) {

                $commission = CommissionsModel::select(['id', 'name', 'image'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($commission === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $commission);
                }
            }

            if (!$isError) {

                return ['error' => __('no_permits', null, 'patos')];
            }

            return null;
        });

        $this->validator->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
