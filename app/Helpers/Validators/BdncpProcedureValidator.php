<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\BdncpProcedureModel;
use System\Input;
use System\Registry;
use System\Validator;

class BdncpProcedureValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una soluzione con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID atto/documento')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero il bilancio con l'id passato in input
                $check = BdncpProcedureModel::select()
                    ->with('commission:id,assignment_start,name,object')
                    ->with('board:id,assignment_start,name,object')
                    ->with('relative_bdncp_procedure:id,object,cig')
                    ->find(uri()->segment($uriSegment, 0));

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'procedura ')
                    ];
                }

                //Se esiste il bilancio lo salvo nel registro
                Registry::set('bdncp-procedure', $check);

                return null;
            })
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }


    /**
     *  Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param string $typology Indica se è un avviso o meno
     * @param int|bool $institutionId L'identificativo dell'instituto
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert', string $typology = '', int|bool $institutionId = false): array
    {
        if ($typology === 'alert') {
            return $this->validateAlert($mode, $institutionId);
        } else {
            return $this->validate($mode, $institutionId);
        }
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param bool $institutionId
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode = 'insert', int|bool $institutionId = false): array
    {

        $validateCigAndLink = true;

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object', false, false, false, ['strip']))
            ->required()
            ->betweenString(2, 1000)
            ->end();


            $this->validator->label('Tipologia errata')
                ->value(Input::post('_typology'))
                ->required()
                ->in('procedure')
                ->end();



            $this->validator->label('Cig')
                ->value(trim(Input::post('cig', true, true, true, ['strip'])))
                ->required()
                ->exactLength(10)
                ->isAlphaNum('CIG non valido')
                ->regex('/^([A-Za-z0-9]{10})?$/', 'CIG non valido')
                ->add(function () use ($mode, $institutionId) {

                    //Controllo che non sia già presente una procedura con il CIG inserito
                    $query = BdncpProcedureModel::where('cig', strtoupper(trim(Input::post('cig'))));

                    //Escludo dal controllo i CIG 0000000000
                    $query->where('typology', '=', 'procedure');
                    $query->where('cig', '!=', '0000000000');

                    //In update inserisco il controllo anche sull'id
                    if ($mode === 'update') {
                        $query->where('id', '!=', Input::post('id'));
                    }

                    $check = $query->first();

                    //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                    //mostro un messaggio di errore
                    if ($check !== null) {

                        return [
                            'error' => 'Non è possibile inserire la procedura in quanto il CIG è già censito sulla piattaforma'
                        ];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Link alla Banca Dati Nazionale Contratti Pubblici (BDNCP)')
                ->value(trim(Input::post('bdncp_link', true, true, true, ['strip'])))
                ->isUrl(null, true)
                ->betweenString(2, 191)
                ->end();


        $this->validator->label('Stai pubblicando un Dibattito pubblico?')
            ->value(Input::post('public_debate_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando un Documenti di gara?')
            ->value(Input::post('notice_documents_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando la Composizione delle commissioni giudicatrici e CV dei componenti?')
            ->value(Input::post('judging_commission_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati?')
            ->value(Input::post('equal_opportunities_af_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando una Procedura di affidamento dei servizi pubblici locali?')
            ->value(Input::post('local_public_services_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando la Composizione del Collegio consultivo tecnico?')
            ->value(Input::post('advisory_board_technical_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati?')
            ->value(Input::post('equal_opportunities_es_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando un Contratto gratuito e forme speciali di partenariato?')
            ->value(Input::post('free_contract_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando un Atto o documento relativo agli affidamenti di somma urgenza?')
            ->value(Input::post('emergency_foster_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Stai pubblicando una Procedura di affidamento?')
            ->value(Input::post('foster_procedure_check'))
            ->isNatural()
            ->in('0,1')
            ->end();

        $this->validator->label('Note dibattito pubblico')
            ->value(Input::post('public_debate_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note documenti di gara')
            ->value(Input::post('notice_documents_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note composizione delle commissioni giudicatrici')
            ->value(Input::post('judging_commission_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati')
            ->value(Input::post('equal_opportunities_af_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note procedura di affidamento dei servizi pubblici locali')
            ->value(Input::post('local_public_services_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note composizione del Collegio consultivo tecnico')
            ->value(Input::post('advisory_board_technical_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati')
            ->value(Input::post('equal_opportunities_es_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note contratto gratuito e forme speciali di partenariato')
            ->value(Input::post('free_contract_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note affidamenti di somma urgenza')
            ->value(Input::post('emergency_foster_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Note procedura di affidamento')
            ->value(Input::post('foster_procedure_notes'))
            ->betweenString(2, 10000)
            ->end();

        $this->validateNotes();

        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($institutionId, $id) {

                    $query = BdncpProcedureModel::where('id', $id);

                    $regulation = $query->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bandi di gara e contratti dal 01/01/2024')];
                    }
                })
                ->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml(),
            'errorFields' => $this->validator->getErrorField()
        ];
    }

    /**
     *  Metodo per la validazione dei campi del form di un avviso
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int|bool $institutionId
     * @return array
     * @throws Exception
     */
    protected function validateAlert(string $mode = 'insert', int|bool $institutionId = false): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validateObject();

        $this->validateNotes();

        $this->validator->label('Avviso')
            ->value(Input::post('_typology'))
            ->required()
            ->in('alert')
            ->end();

        $this->validator->label('Data dell\'avviso')
            ->value(Input::post('alert_date'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Procedura relativa (dal 01/01/2024)')
            ->value(Input::post('object_bdncp_procedure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = BdncpProcedureModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bandi di gara e contratti dal 01/01/2024')];
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
     * Metodo per la validazione dell'Oggetto
     * @return void
     * @throws Exception
     */
    private function validateObject(): void
    {
        $this->validator->label('Oggetto')
            ->value(trim(Input::post('object')))
            ->required()
            ->betweenString(2, 255)
            ->end();
    }

    /**
     * Metodo per la validazione delle Note
     * @return void
     * @throws Exception
     */
    private function validateNotes(): void
    {
        $this->validator->label('Note')
            ->value(trim(Input::post('notes', true, true, true, ['strip'])))
            ->betweenString(2, 1000)
            ->end();
    }

    /**
     * Validatore dell'eliminazione multipla
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

                //Recupero le procedure selezionate
                $documents = BdncpProcedureModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($documents === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $documents);
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
