<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ProceedingsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Procedimento (object_proceedings)
 */
class ProceedingValidator
{

    public $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste un procedimento con quell'ID per l'ente
     *
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null)
    {
        $this->validator->label('ID procedimento')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il procedimento con l'id passato in input
                $check = ProceedingsModel::where('id', uri()->segment(4, 0))
                    ->with(['responsibles' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'title', 'email', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['measure_responsibles' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['substitute_responsibles' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['to_contacts' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['offices_responsibles' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with(['other_structures' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with('normatives:id')
                    ->with(['monitoring_datas' => function ($query) {
                        $query->select(['*']);
                    }])
                    ->first();
                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'procedimento ')
                    ];
                }

                //Se esiste il procedimento lo salvo nel registro
                Registry::set('proceeding', $check);

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
     * @return array
     * @throws Exception
     */
    public function check($mode = 'insert')
    {
        if ($mode == 'archiving') {
            return $this->archivingValidate();
        } else {
            return $this->validate($mode);
        }
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @return array
     * @throws Exception
     */
    protected function validate($mode = 'insert')
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Nome del procedimento')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(5, 255)
            ->end();

        if (Input::post('responsibles')) {
            foreach (explode(',', (string)Input::post('responsibles')) as $responsible) {
                $this->validator->label('Responsabile di procedimento' . $responsible)
                    ->value($responsible)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('measure_responsibles')) {
            foreach (explode(',', (string)Input::post('measure_responsibles')) as $measureResponsible) {
                $this->validator->label('Responsabile di provvedimento' . $measureResponsible)
                    ->value($measureResponsible)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }


        if(Input::post('substitute_responsibles')) {
            foreach (explode(',', (string)Input::post('substitute_responsibles')) as $substituteResponsible) {
                $this->validator->label('Responsabile sostitutivo' . $substituteResponsible)
                    ->value($substituteResponsible)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('offices_responsibles')) {
            foreach (explode(',', (string)Input::post('offices_responsibles')) as $officesResponsible) {
                $this->validator->label('Ufficio responsabile' . $officesResponsible)
                    ->value($officesResponsible)
                    ->required()
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        } else {

            $this->validator->label('Ufficio responsabile')
                ->value(Input::post('offices_responsibles'))
                ->required()
                ->end();
        }

        if (Input::post('to_contacts')) {
            foreach (explode(',', (string)Input::post('to_contacts')) as $toContact) {
                $this->validator->label('Personale da contattare' . $toContact)
                    ->value($toContact)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Visualizzazione del Chi Contattare')
            ->value(Input::post('contact'))
            ->in('1,2,3,4')
            ->end();

        if (Input::post('other_offices')) {
            foreach (explode(',', (string)Input::post('other_offices')) as $structure) {
                $this->validator->label('Altra struttura' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Descrizione del procedimento')
            ->value(Input::post('description'))
            ->betweenString(5, 10000)
            ->end();

        $this->validator->label('Costi e modalità di pagamento')
            ->value(Input::post('costs'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Conclusione tramite silenzio assenso')
            ->value(Input::post('silence_consent'))
            ->in('0,1')
            ->end();

        $this->validator->label('Conclusione tramite dichiarazione dell\'interessato')
            ->value(Input::post('declaration'))
            ->in('0,1')
            ->end();

        if (Input::post('normatives')) {
            foreach (Input::post('normatives') as $normative) {
                $this->validator->label('Riferimento normativo' . $normative)
                    ->value($normative)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Riferimenti normativi (altro)')
            ->value(Input::post('regulation'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Termine di conclusione')
            ->value(Input::post('deadline'))
            ->betweenString(5, 150)
            ->end();

        $this->validator->label('Strumenti di tutela')
            ->value(Input::post('protection_instruments'))
            ->betweenString(5, 150)
            ->end();

        $this->validator->label('Disponibilità del servizio online')
            ->value(Input::post('service_available'))
            ->in('0,1')
            ->end();

        if (Input::post('service_available') === '1') {

            $this->validator->label('Url per il servizio online relativo')
                ->value(Input::post('url_service'))
                ->required()
                ->isUrl(null, true)
                ->betweenString(4, 191)
                ->end();
        } else {

            $this->validator->label('Tempi previsti per attivazione del servizio online')
                ->value(Input::post('service_time'))
                ->required()
                ->betweenString(2, 150)
                ->end();
        }

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $user = ProceedingsModel::where('id', Input::post('id'))->first();
                    if (empty($user)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Procedimento')];
                    }
                })
                ->end();
        }

        $this->validateMonitoring();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo per la validazione dei campi del form per l'archiviazione
     *
     * @return array
     * @throws Exception
     */
    protected function archivingValidate()
    {
        $this->validator->label('Item Id')
            ->value(Input::post('itemId'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->add(function () {
                $user = ProceedingsModel::where('id', Input::post('itemId'))->first();
                if (empty($user)) {
                    return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Procedimento')];
                }
            })
            ->end();

        $this->validator->label('Data di fine pubblicazione in archivio')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    protected function validateMonitoring()
    {
        if (Input::post('_monitoring')) {

            $proceedingMonitoringData = objectToArray(json_decode(Input::post('_monitoring')));
            $i = 1;
            foreach ($proceedingMonitoringData as $monitoring) {
                $this->validator->label('Anno - Monitoraggio riga ' . $i)
                    ->value($monitoring['year'])
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();

                $this->validator->label('Numero procedimenti conclusi nell\'anno: - Monitoraggio ' . $i)
                    ->value($monitoring['year_concluded_proceedings'])
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();

                $this->validator->label('Numero giorni medi di conclusione nell\'anno ' . $i)
                    ->value($monitoring['conclusion_days'])
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();

                $this->validator->label('Percentuale procedimenti conclusi nei termini al termine dell\'anno ' . $i)
                    ->value($monitoring['percentage_year_concluded_proceedings'])
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();

                $i++;
            }
        }
    }

    /**
     * Validatore dell'eliminazione multipla
     *
     * @return array
     * @throws Exception
     */
    public function multipleSelection()
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

                $proceeding = ProceedingsModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($proceeding === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $proceeding);
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
