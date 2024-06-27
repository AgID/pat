<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\NewsNoticesModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto News e Avviso (object_news_notices)
 */
class NewsNoticeValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una news/avviso con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID news')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la news con l'id passato in input
                $check = NewsNoticesModel::where('id', uri()->segment(4, 0))
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'news/avviso ')
                    ];
                }

                //Se esiste la news la salvo nel registro
                Registry::set('news_notice', $check);

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

        $this->validator->label('Titolo notizia')
            ->value(Input::post('title'))
            ->required()
            ->betweenString(4, 120)
            ->end();

        $this->validator->label('Tipologia')
            ->value(Input::post('typology'))
            ->required()
            ->in('avviso,news')
            ->end();

        $this->validator->label('Data notizia')
            ->value(Input::post('news_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data inizio pubblicazione')
            ->value(Input::post('start_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data fine pubblicazione')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('end_date') < Input::post('start_date')) {

                    return ['error' => __('invalid_end_date_publication_3', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Contenuto')
            ->value(Input::post('content'))
            ->betweenString(4, 1000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $measure = NewsNoticesModel::where('id', Input::post('id'))->first();
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

            if ($isError === true) {

                $news = NewsNoticesModel::select(['id', 'title'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($news === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $news);
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