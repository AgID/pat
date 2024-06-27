<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Traits;

use Illuminate\Database\Eloquent\Builder;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


trait SearchableTrait
{

    /**
     * @description Funzione che controlla se la stringa da cerare è una email e in caso toglie i caratteri speciali
     *
     * @param string $term Stringa da cercare
     * @return string
     */
    protected function fullTextWildcards(string $term): string
    {
        if ($this->hasMail($term)) {

            $reservedSymbols = ['-', '@', '+', '<', '>', '(', ')', '~'];
            $term = str_replace($reservedSymbols, '', $term);
            $words = explode(' ', $term);

            foreach ($words as $key => $word) {
                if (strlen($word) >= 3) {
                    $words[$key] = "'*" . $word . "* (" . $word . ")'";
                }
            }

            $searchTerm = implode(' ', $words);


        } else {

            $searchTerm = $term;
        }

        return $searchTerm;
    }

    /**
     * @description Funzione che controlla se la stringa è una email
     *
     * @param string $term Stringa da controllare
     * @return bool
     */
    private function hasMail(string $term = ''): bool
    {
        if ((bool)filter_var($term, FILTER_VALIDATE_EMAIL) === true ||
            (bool)preg_match('/^([a-z0-9_.-]+)@([\da-z.-]+)$/', $term) === true
        ) {

            return true;

        }

        return false;
    }

    /**
     * @description Funzione che genera la query di ricerca in base alla stringa inserita dall'utente e ai campi dei vari modelli
     * @param Builder         $query         Query da effettuare
     * @param string|null     $term          La stringa da cercare, inserita dall'utente
     * @param bool|array|null $select        Se passato, indica i campi su cui effettuare la ricerca altrimenti prende i campi dichiarati nel modello
     * @param array|null      $fieldWhereHas Campi di ricerca nelle tabelle di relazione
     * @param bool|null       $frontOffice   Indica se la ricerca è
     *                                       nel front-office
     * @param array|null      $dateField     Campi di ricerca di tipo data
     * @param array           $modelScope    Eventuali scope aggiuntivi del modello
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term, bool|array|null $select = false, array|null $fieldWhereHas = null, bool|null $frontOffice = false, array|null $dateField = null, array $modelScope = []): Builder
    {
        //Controllo se la stringa da cercare non è vuota e che sia di almeno 3 caratteri
        if (!empty($term) && strlen($term) >= 3) {


            //Pulisco la stringa di eventuali spazi bianchi superflui e la divido utilizzando il carattere "white space" come separatore
            $terms = explode(' ', preg_replace('/\s+/', ' ', trim($term)));

            //In caso di ricerca nelle select2 o nel front-office, non usa i campi nei modelli ma quelli passati nei parametri
            if (is_array($select)) {
                $this->searchable = $select;
            }

            if(!empty($fieldWhereHas)) {
                $this->searchableWhereHas = $fieldWhereHas;
            } elseif($frontOffice) {
                $this->searchableWhereHas = null;
            }

            //Campi di tipo data
            if(!empty($dateField)) {
                $this->searchableDateFields = $dateField;
            } elseif($frontOffice) {
                $this->searchableDateFields = null;
            }

            //Ciclo le parale che compongono la stringa da cercare
            foreach ($terms as $t) {

                if(strlen($t) >= 3) {
                    //Pulisco il termine da eventuali caratteri speciali
                    $t = $this->fullTextWildcards($t);

                    $i = 0;

                    // Ciclo sui campi del modello su cui effettuare la ricerca
                    foreach ($this->searchable as $column) {

                        if (!empty($this->encrypted) && in_array($column, $this->encrypted)) {

                            $term = checkEncrypt($term);

                        }


//                if ($hasMail === true || count($terms) >= 1) {

                        if ($i === 0) {

                            $query->where($column, 'like', '%' . ($t) . '%')
                                ->orWhere($column, 'like', ($t) . '%')
                                ->orWhere($column, 'like', '%' . ($t))
                                ->orWhere($column, '=', ($t));

                        } else {

                            $query->orWhere($column, 'like', '%' . ($t) . '%')
                                ->orWhere($column, 'like', ($t) . '%')
                                ->orWhere($column, 'like', '%' . ($t))
                                ->orWhere($column, '=', ($t));

                        }

//                } else { //Servono le tabelle in myIsam
//
//                    if ($i === 0) {
//
//                        $query->whereRaw("MATCH ({$column}) AGAINST ( ? IN BOOLEAN MODE)",  ($term));
//
//                    } else {
//
//                        $query->orWhereRaw("MATCH ({$column}) AGAINST ( ? IN BOOLEAN MODE)",  ($term));
//
//                    }
//                }

                        $i++;
                    }

                    //Eventuali scope locali aggiuntivi
                    if(!empty($modelScope)) {
                        foreach ($modelScope as $scope) {
                            $query->$scope($t);
                        }
                    }

                    if(!empty($this->searchableDateFields)) {
                        foreach ($this->searchableDateFields as $dateField) {
                            $dateValue = str_replace('/', '-', $t);
                            $dateValue = date('Y-m-d H:i:s', strtotime($dateValue));

                            $query->orWhere($dateField, 'like', '%' . ($dateValue) . '%')
                                ->orWhere($dateField, 'like', ($dateValue) . '%')
                                ->orWhere($dateField, 'like', '%' . ($dateValue))
                                ->orWhere($dateField, '=', ($dateValue));
                        }
                    }

                    //Campi di ricerca nelle tabelle di relazione
                    if (!empty($this->searchableWhereHas)) {
                        foreach ($this->searchableWhereHas as $k => $column) {

                            $query->orWhereHas($k, function ($query) use ($t, $column, $k) {
                                if(!empty($column['related_table'])) {

                                    $query->join($column['table'].' as '.$column['as'], $column['as'].'.id', '=', $column['related_table'].$column['local_key'], 'left outer')
                                        ->where($column['as'].'.deleted', '=', 0);

                                    $column['table'] = $column['as'];
                                }

                                if(!empty($column['table'] )) {
                                    $column['table'] .= '.';
                                } else {
                                    $column['table'] = '';
                                }

                                foreach ($column['field'] as $field) {
                                    $query->where($column['table'] . $field, 'like', '%' . ($t) . '%')
                                        ->orWhere($column['table'] . $field, 'like', ($t) . '%')
                                        ->orWhere($column['table'] . $field, 'like', '%' . ($t))
                                        ->orWhere($column['table'] . $field, '=', ($t));
                                }

                                if(!empty($column['whereAs'])) {
                                    foreach ($column['whereAs'] as $whereAs){
                                        $query->orWhereHas($whereAs['table'], function ($query) use ($t, $whereAs) {
                                            $query->where(/*$column['table'] . */$whereAs['field'], 'like', '%' . ($t) . '%')
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], 'like', ($t) . '%')
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], 'like', '%' . ($t))
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], '=', ($t));
                                        });
                                    }

                                }

                                if(!empty($column['groupby'] )) {
                                    $query->groupBy($column['groupby']);
                                }
                            });
                        }
                    }
                }
            }
        }

        return $query;
    }
}
