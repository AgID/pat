<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ChargesModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Onere informativo e obbligo (object_charges)
 */
class ChargeValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un onere/obbligo con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID onere')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'onere con l'id passato in input
                $check = ChargesModel::where('id', uri()->segment(4, 0))
                    ->with('proceedings:id,name')
                    ->with('measures:id,object,date,number')
                    ->with('regulations:id,title')
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'onere o obbligo ')
                    ];
                }

                //Se esiste l'onere lo salvo nel registro
                Registry::set('charge', $check);

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

        $this->validator->label('Tipologia')
            ->value(Input::post('type'))
            ->required()
            ->in('onere,obbligo')
            ->end();

        $this->validator->label('Per Cittadini')
            ->value(Input::post('citizen'))
            ->in('0,1')
            ->end();

        $this->validator->label('Per Imprese')
            ->value(Input::post('companies'))
            ->in('0,1')
            ->end();

        $this->validator->label('Denominazione o titolo')
            ->value(Input::post('title'))
            ->required()
            ->betweenString(4, 120)
            ->end();

        $this->validator->label('Data di scadenza')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->end();

        if (Input::post('proceedings')) {
            foreach (Input::post('proceedings') as $proceeding) {
                $this->validator->label('Procedimento associato ' . $proceeding)
                    ->value($proceeding)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('measures')) {
            foreach (explode(',', (string)Input::post('measures')) as $measure) {
                $this->validator->label('Provvedimento associato ' . $measure)
                    ->value($measure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('regulations')) {
            foreach (Input::post('regulations') as $regulation) {
                $this->validator->label('Regolamento associato ' . $regulation)
                    ->value($regulation)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Riferimenti normativi')
            ->value(Input::post('normative_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Contenuto')
            ->value(Input::post('description'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('URL per maggiori informazioni')
            ->value(Input::post('info_url'))
            ->isUrl(null, true)
            ->betweenString(4, 1000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $measure = ChargesModel::where('id', Input::post('id'))->first();
                    if (empty($measure)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Onere/Obbligo')];
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

                $charges = ChargesModel::select(['id', 'title'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($charges === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $charges);
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
