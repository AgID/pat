<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\CompanyModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Enti e società controllate (object_company)
 */
class CompanyValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una società con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID società')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                // Recupero la società con l'id passato in input
                $check = CompanyModel::where('id', uri()->segment(4, 0))
                    ->with(['representatives' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }]);

                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'società ')
                    ];
                }

                // Se esiste la società la salvo nel registro
                Registry::set('company', $check);

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
                $user = CompanyModel::where('id', Input::post('itemId'))->first();
                if (empty($user)) {
                    return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Società')];
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

        $this->validator->label('Ragione sociale')
            ->value(Input::post('company_name'))
            ->required()
            ->betweenString(5, 191)
            ->end();

        $this->validator->label('Tipo')
            ->value(Input::post('typology'))
            ->required()
            ->in('ente pubblico vigilato,societa partecipata,ente di diritto privato controllato')
            ->end();

        $this->validator->label('Misura di partecipazione')
            ->value(Input::post('participation_measure'))
            ->betweenString(2, 45)
            ->end();

        $this->validator->label('Durata dell\'impegno')
            ->value(Input::post('duration'))
            ->betweenString(5, 45)
            ->end();

        $this->validator->label('Oneri complessivi (annuale)')
            ->value(Input::post('year_charges'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Descrizione delle attività')
            ->value(Input::post('description'))
            ->betweenString(5, 5000)
            ->end();


        if (Input::post('representatives')) {
            foreach (explode(',', (string)Input::post('representatives')) as $representative) {
                $this->validator->label('Rappresentante ' . $representative)
                    ->value($representative)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Incarichi amministrativi e relativo trattamento economico')
            ->value(Input::post('treatment_assignments'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Url sito web')
            ->value(Input::post('website_url'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        $this->validator->label('Risultati di bilancio (ultimi 3 anni)')
            ->value(Input::post('balance'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell\'incarico (link)')
            ->value(Input::post('inconferability_dec_link'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        $this->validator->label('Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell\'incarico (link)')
            ->value(Input::post('incompatibility_dec_link'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $company = CompanyModel::where('id', Input::post('id'))->first();
                    if (empty($company)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Società')];
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

                $company = CompanyModel::select(['id', 'company_name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($company === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $company);
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
