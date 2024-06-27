<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Classe che implementa nella query il filtro di estrazione dei dati dell'Ente in base al tipo di utenza in sessione.
 */
class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!is_cli()) {
            if (!isSuperAdmin()) {

                $builder->where($model->getTable() . '.institution_id', PatOsInstituteId());

            } else {

                // Get Globale per filtrare l'ID ente nelle paginazioni dimnamiche
                if (!empty(Input::get('eid'))) {

                    $validator = new Validator();
                    $validator->label('Identificativo ente')
                        ->value(Input::get('eid'))
                        ->isInt();

                    if ($validator->isSuccess()) {

                        $builder->where($model->getTable() . '.institution_id', (int)Input::get('eid'));

                    }

                } else {

                    // Customizzazioni filtraggio enti
                    $getIdentity = authPatOs()->getIdentity();

                    $institutionId = checkAlternativeInstitutionId();

                    if (isset($getIdentity['options']['alternative_pat_os_id'])) {

                        if ((int)$getIdentity['options']['alternative_pat_os_id'] !== 0) {

                            $institutionId = $getIdentity['options']['alternative_pat_os_id'];

                            $builder->where($model->getTable() . '.institution_id', $institutionId);
                        }
                    }


                }

            }

        }
    }
}