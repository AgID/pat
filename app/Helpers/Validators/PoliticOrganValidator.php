<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Http\Web\Admin\PoliticOrganAdminController;
use Model\NormativesModel;
use Model\RelSectionsFoPoliticOrganStructure;
use Model\SectionFoPoliticOrganModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Log;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Normativa (object_normatives)
 */
class PoliticOrganValidator
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
     *  Controlla la validità dell'ID nell'URI segment e se esiste una normativa con quell'ID per l'ente
     *
     * @param $checkOwner Indica se controllare il proprietario del record
     * @param $uriSegment
     * @param $institutionId
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID organo politico')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Se trovo la riga su section_fo_politic_organ prendo quella, altrimenti la creo a partire da section_fo
                $allowedID = [221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 238, 239, 240, 241, 242, 243, 233, 234, 235, 236/*, 244, 245*/];
                if (!in_array(uri()->segment($uriSegment, 0), $allowedID)) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'organo politico ')
                    ];
                }

                //Recupero la normativa con l'id passato in input
                $check = SectionFoPoliticOrganModel::where('section_fo_id', uri()->segment($uriSegment, 0))
                    ->with('structure');
                $check = $check->first();

                if (!empty($check)) {

                    $relationInfo = SectionsFoModel::select(['id'])->where('id', uri()->segment($uriSegment, 0))
                        ->with('management_structure:id,structure_name')
                        ->with('non_management_structure:id,structure_name')
                        ->with('politic_personnel:id,full_name')
                        ->first()
                        ->toArray();

                    $check['management_structure'] = $relationInfo['management_structure'];
                    $check['non_management_structure'] = $relationInfo['non_management_structure'];
                    $check['politic_personnel'] = $relationInfo['politic_personnel'];

                    Registry::set('politic_organ', $check);
                } else {
                    //prendo i dati da section_fo -- qui si entra solo quando definisco nuovi organi politici e non ce li ho configurati
                    $data = [
                        'institution_id' => $institutionId !== null ? $institutionId : checkAlternativeInstitutionId(),
                        'denomination' => '',
                        'skills' => '',
                        'show_personnel' => 1,
                        'section_fo_id' => uri()->segment($uriSegment, 0)
                    ];

                    $sectionInfo = getSections(605);

                    if (!empty($sectionInfo)) {
                        //controllo che l'ente abbia la sezione che sta cercando di modificare (potrebbe essere esclusa, ecc.)
                        $err = true;
                        foreach ($sectionInfo as $sec) {
                            if ($sec['id'] == uri()->segment($uriSegment, 0)) {
                                $data['denomination'] = !empty($sec['label']) ? $sec['label'] : $sec['name'];
                                $err = false;
                            }
                        }
                        if ($err) {
                            return [
                                'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'organo politico ')
                            ];
                        }
                    } else {
                        return [
                            'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'organo politico ')
                        ];
                    }
                    Registry::set('politic_organ', $data);
                }
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
     * @param int|bool $institutionId indica l'id dell'istituto
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert', int|bool $institutionId = false): array
    {
        return $this->validate($mode, $institutionId);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode, int $institutionId): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Denominazione organo')
            ->value(Input::post('denomination'))
            ->required()
            ->maxLength(200)
            ->end();

        $this->validator->label('Motivazione assenza uffici ')
            ->value(Input::post('absence_office_reason'))
            ->maxLength(800)
            ->end();

        $this->validator->label('Competenze')
            ->value(trim((string)Input::post('skills', true)))
            ->required()
            ->betweenString(10, 7500)
            ->end();


        if (!empty(Input::post('management_structure'))) {
            foreach (explode(',', (string) Input::post('management_structure')) as $structure) {
                $this->validator->label('Struttura ' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->add(function () use ($structure, $institutionId) {
                        $query = StructuresModel::where('id', $structure);

                        $result = $query->first();

                        if ($result === null) {
                            return [
                                'error' => 'Identificativo struttura con id ' . $structure . ' non trovato.',
                            ];
                        }

                        return null;
                    })
                    ->end();
            }
        }

        if (!empty(Input::post('non_management_structure'))) {
            foreach (explode(',', (string) Input::post('non_management_structure')) as $structure) {
                $this->validator->label('Struttura ' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->add(function () use ($structure, $institutionId) {
                        $query = StructuresModel::where('id', $structure);

                        $result = $query->first();

                        if ($result === null) {
                            return [
                                'error' => 'Identificativo struttura con id ' . $structure . ' non trovato.',
                            ];
                        }

                        return null;
                    })
                    ->end();
            }
        }

        $this->validator->label("Mostra personale associato all'organo")
            ->value(Input::post('show_personnel'))
            ->required()
            ->in("0,1")
            ->end();


        if (Input::post("show_personnel") == 1) {
            if (!empty(Input::post('personnel'))) {
                foreach (explode(',', (string) Input::post('personnel')) as $person) {
                    $this->validator->label('Personale ' . $person)
                        ->value($person)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
