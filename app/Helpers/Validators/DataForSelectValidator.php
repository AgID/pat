<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

use Exception;
use Model\InstitutionsModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class DataForSelectValidator
{
    public Validator $validator;

    /**
     * @description Costruttore
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla dei dati passati in Input nella generazione dei dati per le selct2
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     */
    public function validate(string $mode = 'all'): array
    {
        if ($mode === 'selected') {

            if (Input::get('id')) {
                foreach (explode(',', Input::get('id')) as $id) {
                    $this->validator->label('Id ' . $id)
                        ->required()
                        ->value($id)
                        ->isInt()
                        ->end();
                }
            }

        } else {

            if (Input::post('id')) {
                foreach (explode(',', Input::get('id')) as $id) {
                    $this->validator->label('Id ' . $id)
                        ->value($id)
                        ->isInt()
                        ->end();
                }
            }
        }

        if (Input::get('per_page')) {
            $this->validator->label('numero di record per pagina')
                ->value(Input::get('per_page'))
                ->isInt()
                ->end();
        }

        if (Input::get('institution_id')) {

            $this->validator->label('Identificativo Ente')
                ->value(Input::get('institution_id'))
                ->isInt()
                ->add(function () {
                    $query = InstitutionsModel::where('id', Input::get('institution_id'))
                        ->first();

                    if (empty($query)) {
                        return [
                            'error' => 1
                        ];
                    }

                    return null;

                }, 'Non hai i permessi per operare su questa voce')
                ->end();
        }

        $this->validator->label('Modello')
            ->value(Input::get('model'))
            ->required()
            ->isInt()
            ->in('1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49')
            ->end();

        $this->validator->label('Campo')
            ->value(Input::get('field'))
            ->add(function () {

                $isValid = (bool)filter_var(
                    Input::get('field'),
                    FILTER_VALIDATE_REGEXP,
                    [
                        'options' => [
                            'regexp' => "/^[a-zA-Z_]+$/"
                        ]
                    ]);

                if (!$isValid) {
                    return [
                        'error' => 1,
                    ];
                }

                return null;

            }, 'Il paramento passato non è valido')
            ->maxLength(20)
            ->end();

        $this->validator->label('Search')
            ->value(Input::get('searchTerm'))
            ->add(function () {
                return Input::get('searchTerm');
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
