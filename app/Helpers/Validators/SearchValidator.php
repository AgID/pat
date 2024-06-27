<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use System\Input;
use System\Validator;

/**
 * Validator per l'oggetto Tasso di assenza(object_absence_rates)
 */
class SearchValidator
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
     * Metodo per la validazione dei campi di ricerca del numero dei risultati
     * @param null|bool $t Indica se validare la tipologia
     * @return array
     * @throws Exception
     */
    public function validateInputSearchNumb(bool $t = true): array
    {

        $this->validator->verifyToken();
        $this->validator->label('Id sezione bo')
            ->value(Input::get('sid'))
            ->required()
            ->isInt()
            ->end();

        if ($t) {
            $this->validator->label('Tipologia')
                ->value(Input::get('type'))
                ->required()
                ->end();
        }

        $this->validator->label('Modello')
            ->value(Input::get('model'))
            ->required()
            ->isInt()
            ->add(function () use ($t) {

                $excludeSearchable = config('exclude_searchable_front', null, 'modelConfigs');

                if (!$t && in_array(Input::get('model'), $excludeSearchable)) {
                    return ['error' => 1];
                }

                $config = config(Input::get('model'), null, 'modelConfigs');

                if (empty($config['model'])) {

                    return ['error' => 1];

                }

            }, 'Errore elaborazione modello')
            ->end();

        $this->validator->label('Parola chiave')
            ->value(Input::get('s'))
            ->required()
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];

    }
}
