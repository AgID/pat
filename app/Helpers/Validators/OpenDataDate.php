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

class OpenDataDate
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
     * @return array
     * @throws Exception
     */
    public function validateDateAndDiff(): array
    {
        $this->validator->verifyToken();

        $this->validator->label('Inizio intervallo di esportazione')
            ->value(Input::get('skip'))
            ->notIn('-1', 'Non è stato selezionato alcun intervallo di esportazione')
            ->isNatural('Il valore di inizio intervallo deve essere numerico')
            ->end();

        if (Input::get('skip') != -1) {
            $this->validator->label('Fine intervallo di esportazione')
                ->value(Input::get('take'))
                ->isNatural('Il valore di fine intervallo deve essere numerico')
                ->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}