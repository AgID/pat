<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

use Exception;
use Model\ContentSectionFoModel;
use Model\InstitutionsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Registry;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class GenericPageValidator
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
     * @description Funzione per la validazione dei dati nell'operazione di ordinamento delle pagine e/o paragrafi
     * @param string   $type          Indica se si sta ordinando un paragrafo o una sezione
     * @param int|null $institutionId ID dell'ente
     * @return array
     * @throws Exception
     */
    public function validateSort(string $type = 'section', int $institutionId = null): array
    {
        if ($type === 'section') {

            $this->validator->label('identificativo')
                ->value(Input::get('id'))
                ->required()
                ->isInt()
                ->add(function () use ($institutionId) {
                    $query = SectionsFoModel::select(['id'])
                        ->institution()
                        ->where('id', Input::get('id'))
                        ->where('is_system', 0)
                        ->first();

                    if (empty($query)) {
                        return [
                            'error' => 1
                        ];
                    }

                    return null;
                }, 'Non hai i permessi per operare su questa voce')
                ->end();
        } elseif ($type == 'paragraph') {

            $this->validator->label('Identificativo')
                ->value(Input::get('id'))
                ->required()
                ->isInt()
                ->add(function () use ($institutionId) {
                    $query = ContentSectionFoModel::select(['id'])
                        ->institution()
                        ->where('id', Input::get('id'))
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

        $this->validator->label('Identificativo Ente')
            ->value(Input::get('institution_id'))
            ->required()
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


        $this->validator->label('Direzione Ordinamento')
            ->value(Input::get('dir'))
            ->required()
            ->in('up,down')
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Funzione che controlla se la pagina che si sta cercando di modificare esiste o meno
     * @return array
     * @throws Exception
     */
    public function validateContentsPageBySectionFoId(): array
    {
        $this->validator->label('Identificativo pagina')
            ->value(Input::get('id'))
            ->required()
            ->isInt()
            ->add(function () {
                $query = SectionsFoModel::select(['id'])
                    ->institution()
                    ->first();

                if (empty($query)) {
                    return [
                        'error' => 1
                    ];
                }

                return null;
            }, 'Non hai i permessi per operare su questa voce')
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /** @noinspection PhpInconsistentReturnPointsInspection */
    /**
     * @description Funzione che valida i dati per il salvataggio di una nuova pagina
     * @param bool $isSystem Indica se la sezione è di sistema o meno
     * @return array
     * @throws Exception
     */
    public function validateStorageSection(bool $isSystem = false): array
    {

        $this->validator->verifyToken()
            ->end();


        if (!$isSystem) {


            $this->validator->label('nome sezione')
                ->value(Input::post('name'))
                ->required()
                ->minLength(2)
                ->maxLength(255)
                ->end();

            $this->validator->label('Parole chiave')
                ->value(Input::post('meta_keywords'))
                ->maxLength(255)
                ->end();

            $this->validator->label('descrizione')
                ->value(Input::post('meta_descriptions'))
                ->maxLength(255)
                ->end();
        }


        $this->validator->label('tipologia di storage')
            ->value(Input::post('mode'))
            ->required()
            ->in('insert,update,duplicate')
            ->end();

        if (Input::post('mode') === 'insert') {

            $this->validator->label('parent di riferimento')
                ->value(Input::post('parent_id'))
                ->required()
                ->isInt()
                ->end();

            //$getIdentity = authPatOs()->getIdentity();

            /*$this->validator->label('nominativo ente')
                ->value(@$getIdentity['options']['alternative_pat_os_id'])
                ->add(function () {

                    $args = func_get_args();

                    if (empty($args[1])) {

                        return ['error' => 1];

                    }

                }, 'Devi selezionare prima un ente di riferimento per inserire una nuova pagina personalizzata.')
                ->end();*/
        }

        if (Input::post('mode') === 'duplicate') {

            $this->validator->label('parent di riferimento')
                ->value(Input::post('select_tree'))
                ->required()
                ->isInt()
                ->end();
        }

        $this->validator->label('identificativo sezione')
            ->value(Input::post('section_id'))
            ->add(function () {

                if (Input::post('mode') === 'update' &&
                    Input::post('section_id') === null
                ) {
                    return ['error' => 1];
                }
            }, 'ID di sezione richiesto')
            ->isInt()
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Funzione che verifica se la pagina esiste tramite l'id in get
     * @param null $parentId Id della pagina padre
     * @return array
     * @throws Exception
     */
    public function validateSectionId($parentId = null): array
    {

        $id = (!empty($parentId)) ? $parentId : Input::get('id');

        $this->validator->label('Identificativo')
            ->value($id)
            ->required()
            ->isInt()
            ->add(function () use ($id) {

                $query = SectionsFoModel::select(['id'])
                    ->institution()
                    ->where('id', $id)
                    ->first();

                if (empty($query)) {
                    return [
                        'error' => 1
                    ];
                }

                return null;
            }, 'Non hai i permessi per operare su questa voce')
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Funzione che verifica se esiste il paragrafo con l'id passato in get
     * @return array
     * @throws Exception
     */
    public function validateParagraphId(): array
    {

        $this->validator->label('Identificativo paragrafo')
            ->value(Input::get('id'))
            ->required()
            ->isInt()
            ->add(function () {

                $query = ContentSectionFoModel::where('id', Input::get('id'))
                    ->institution()
                    ->first();

                if (empty($query)) {
                    return [
                        'error' => 1
                    ];
                }

                //Se esiste il tasso di assenza, lo salvo nel registro
                Registry::set('paragraph', $query);

                return null;
            }, 'Non hai i permessi per operare su questa voce')
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Funzione che valida i dati per lo storage di un paragrafo
     * @param bool $update Indica se è un'operazione di Update
     * @return array
     * @throws Exception
     */
    public function validateParagraph(bool $update = false): array
    {
        $this->validator->label('sezione')
            ->value(Input::post('section_id'))
            ->required()
            ->isInt()
            ->add(function () {

                $query = SectionsFoModel::select(['id'])
                    ->institution()
                    ->where('id', Input::post('section_id'))
                    ->first();

                if (empty($query)) {
                    return [
                        'error' => 1
                    ];
                }

                return null;
            }, 'Non hai i permessi per operare su questa sezione')
            ->end();

        $this->validator->label('Mode')
            ->value(Input::post('mode'))
            ->required()
            ->in('insert,update,duplicate')
            ->end();

        $this->validator->label('sezione')
            ->value(Input::post('parent_id'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('titolo')
            ->value(Input::post('title'))
            ->minLength(1)
            ->maxLength(255)
            ->end();

        $this->validator->label('contenuto')
            ->value(Input::post('content'))
            ->required()
            ->minLength(1)
            ->maxLength(100000)
            ->end();

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

        /* if (Input::post('mode') === 'insert') {

            $getIdentity = authPatOs()->getIdentity();

            $this->validator->label('nominativo ente')
                ->value(@$getIdentity['options']['alternative_pat_os_id'])
                ->add(function () {

                    $args = func_get_args();

                    if (empty($args[1])) {

                        return ['error' => 1];

                    }

                }, 'Devi selezionare prima un ente di riferimento per inserire una nuova pagina personalizzata.')
                ->end();
        }*/

        // Update
        if ($update) {

            $this->validator->label('Id paragrafo')
                ->value(Input::post('paragraph_id'))
                ->required()
                ->isInt()
                ->add(function () {
                    ContentSectionFoModel::select(['id'])
                        ->institution()
                        ->where('id', Input::post('paragraph_id'))
                        ->first();

                    if (empty($query)) {
                        return [
                            'error' => 1
                        ];
                    }

                    return null;
                }, 'Non hai i permessi per operare su questo paragrafo')
                ->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * @description Metodo per la validazione dei dati in input per i richiami nelle sezioni
     *
     * @return array
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function validateRecalls(): array
    {
        if (Input::get('id')) {
            $this->validator->label('Identificativo')
                ->value(Input::get('id'))
                ->isNaturalNoZero()
                ->isInt()
                ->end();
        }

        if (Input::get('model')) {
            $this->validator->label('Modello')
                ->value(Input::get('model'))
                ->required()
                ->isInt()
                ->in('1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39')
                ->end();
        }


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
