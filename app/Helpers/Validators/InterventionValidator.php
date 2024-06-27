<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\InterventionsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Intervento (object_interventions)
 */
class InterventionValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un intervento con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID intervento')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'intervento con l'id passato in input
                $check = InterventionsModel::where('id', uri()->segment(4, 0))
                    ->with('measures:id,object,number,date')
                    ->with('regulations:id,title')
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'intervento ')
                    ];
                }

                //Se esiste l'intervento lo salvo nel registro
                Registry::set('intervention', $check);

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
        return $this->validate($mode);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Nome')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(4, 60)
            ->end();

        $this->validator->label('Descrizione')
            ->value(Input::post('description'))
            ->betweenString(4, 1000)
            ->end();

        if (Input::post('measures')) {
            foreach (explode(',', (string)Input::post('measures')) as $measure) {
                $this->validator->label('Provvedimento correlato ' . $measure)
                    ->value($measure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('regulations')) {
            foreach (Input::post('regulations') as $regulation) {
                $this->validator->label('Regolamento correlato ' . $regulation)
                    ->value($regulation)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Norme derogate e motivazione')
            ->value(Input::post('derogations'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Termini temporali per i provvedimenti straordinari')
            ->value(Input::post('time_limits'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Costo interventi stimato')
            ->value(Input::post('estimated_cost'))
            ->isNumeric()
            ->end();

        $this->validator->label('Costo interventi effettivo')
            ->value(Input::post('effective_cost'))
            ->isNumeric()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $liquidation = InterventionsModel::where('id', Input::post('id'))->first();
                    if (empty($liquidation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Intervento')];
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

            if ($isError === true) {

                $interventions = InterventionsModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($interventions === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $interventions);
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
