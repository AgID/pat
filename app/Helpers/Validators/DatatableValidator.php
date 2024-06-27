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

class DatatableValidator
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
     * Controlla dei $_POST nella generazione dei dati per la paginazione di datatable.
     *
     * @return array
     * @throws Exception
     */
    public function validate(): array
    {
        $order = Input::get('order');
        $search = Input::get('search');

        $this->validator->label('Start')
            ->value(Input::get('start'))
            ->required()
            ->isNatural()
            ->end();

        $this->validator->label('Length')
            ->value(Input::get('length'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('Draw')
            ->value(Input::get('draw'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('Ordine colonna')
            ->value(!empty($order[0]['column']) ? $order[0]['column'] : null)
            ->required()
            ->isNatural()
            ->end();

        $this->validator->label('Ordine valore')
            ->value(!empty($order[0]['dir']) ? $order[0]['dir'] : null)
            ->required()
            ->in('asc,desc,ASC,DESC')
            ->end();

        $this->validator->label('Valore ricerca')
            ->value(!empty($search['value']) ? $search['value'] : null)
            ->add(function () use ($search) {
                // !empty($search['value']) ? $search['value'] : null;
                if (empty($search['value'])) {
                    return [
                        'error' => 1,
                    ];
                }

                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
