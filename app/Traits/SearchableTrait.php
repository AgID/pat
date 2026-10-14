<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Traits;

use Illuminate\Database\Eloquent\Builder;
use System\Log;

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
                if (strlen($word) >= 4) {
                    $words[$key] = "'*" . $word . "* (" . $word . ")'";
                }
            }

            $searchTerm = implode(' ', $words);


        } else {

            $searchTerm = $term;
        }

        return \System\Utf8::normalize($this->convertAccentedCharacters($searchTerm));
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

    private function normalizeQuery(string $q, int $minLenQuery = 4, $exactSearch = false): array
    {
        // normalizza in minuscolo e rimuove accenti
        $q = mb_strtolower($q, 'UTF-8');
        $q = str_replace(
            ['à','è','é','ì','ò','ù'],
            ['a','e','e','i','o','u'],
            $q
        );

        if($exactSearch){
            //non effettua lo split, ritorna il testo esatto inserito
            return [trim($q)];
        }

        // split su spazi e punteggiatura
        $parts = preg_split('/\s+/', trim($q));

        // lista minimale di stopwords italiane (puoi ampliarla a piacere)
        $stopwords = [
            'a','ad','al','allo','ai','agli','all','agl',
            'alla','alle','con','col','coi','da','dal','dallo',
            'dai','dagli','dalla','dalle','di','del','dello',
            'dei','degli','della','delle','in','nel','nello',
            'nei','negli','nella','nelle','su','sul','sullo',
            'sui','sugli','sulla','sulle','per','tra','fra',
            'il','lo','la','i','gli','le','un','uno','una',
            'ma','ed','se','non','più','anche','come','dove',
            'che','chi','cui','quando','quale','quali','quello',
            'quella','questo','questa','questi','queste','loro',
            'mio','mia','tuo','tua','suo','sua','nostro','nostra',
            'vostro','vostra','miei','mie','tuoi','tue','suoi','sue',
            'nostri','nostre','vostri','vostre','io','tu','lui','lei',
            'noi','voi','loro','mi','ti','ci','vi','ne','si'
        ];

        $maxTokens = 8;

        // filtra: lunghezza minima e non stopword
        $tokens = array_values(array_filter($parts, function($w) use ($minLenQuery, $stopwords) {
            return mb_strlen($w, 'UTF-8') >= $minLenQuery && !in_array($w, $stopwords, true);
        }));

        // tronca se troppi token
        return array_slice($tokens, 0, $maxTokens);
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
    public function scopeSearch(Builder $query, ?string $term, bool|array|null $select = false, array|null $fieldWhereHas = null, bool|null $frontOffice = false, array|null $dateField = null, array $modelScope = [], int|bool $exactSearch = 0): Builder
    {
        //Controllo se la stringa da cercare non è vuota e che sia di almeno 3 caratteri
        $minLenTerm = 4;
        if(!$frontOffice) {
            $minLenTerm = 3;
        }

        if (!empty($term) && strlen($term) >= $minLenTerm) {

            $terms = $this->normalizeQuery($term, $minLenTerm, $exactSearch);

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

                //Introdotto perché gli apostrofi venivano convertiti in &#039 ecc. e cercare frasi che li contenevano dava 0 risultati.
                //Non rimuovere strip tags.
                $t = strip_tags(html_entity_decode($t));

                // $minLenght = $frontOffice ? 4 : 4;
                $minLenght = $minLenTerm;
                if(strlen($t) >= $minLenght) {
                    //Pulisco il termine da eventuali caratteri speciali
                    $t = $this->fullTextWildcards($t);
                    $tRelationShip = $t;

                    $i = 0;

                    // Ciclo sui campi del modello su cui effettuare la ricerca
                    foreach ($this->searchable as $column) {

                        if (!empty($this->encrypted) && in_array($column, $this->encrypted)) {
                            $t = checkEncrypt($term);
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
                            $query->$scope($tRelationShip);
                        }
                    }

                    if(!empty($this->searchableDateFields)) {
                        foreach ($this->searchableDateFields as $dateField) {
                            $dateValue = str_replace('/', '-', $tRelationShip);
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

                            $query->orWhereHas($k, function ($query) use ($tRelationShip, $column, $k) {
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
                                    $query->where($column['table'] . $field, 'like', '%' . ($tRelationShip) . '%')
                                        ->orWhere($column['table'] . $field, 'like', ($tRelationShip) . '%')
                                        ->orWhere($column['table'] . $field, 'like', '%' . ($tRelationShip))
                                        ->orWhere($column['table'] . $field, '=', ($tRelationShip));
                                }

                                if(!empty($column['whereAs'])) {
                                    foreach ($column['whereAs'] as $whereAs){
                                        $query->orWhereHas($whereAs['table'], function ($query) use ($tRelationShip, $whereAs) {
                                            $query->where(/*$column['table'] . */$whereAs['field'], 'like', '%' . ($tRelationShip) . '%')
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], 'like', ($tRelationShip) . '%')
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], 'like', '%' . ($tRelationShip))
                                                ->orWhere(/*$column['table'] . */$whereAs['field'], '=', ($tRelationShip));
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

    private function convertAccentedCharacters(string $str) : string
    {
        $foreignCharacters = [
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
            '/Б/' => 'B',
            '/б/' => 'b',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/Д|Δ/' => 'D',
            '/д|δ/' => 'd',
            '/Ð|Ď|Đ/' => 'Dj',
            '/ð|ď|đ/' => 'dj',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
            '/Ф/' => 'F',
            '/ф/' => 'f',
            '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
            '/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
            '/Ĥ|Ħ/' => 'H',
            '/ĥ|ħ/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Θ/' => 'TH',
            '/θ/' => 'th',
            '/Ķ|Κ|К/' => 'K',
            '/ķ|κ|к/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
            '/М/' => 'M',
            '/м/' => 'm',
            '/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
            '/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
            '/П/' => 'P',
            '/п/' => 'p',
            '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
            '/ŕ|ŗ|ř|ρ|р/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
            '/Ț|Ţ|Ť|Ŧ|Τ|Т/' => 'T',
            '/ț|ţ|ť|ŧ|τ|т/' => 't',
            '/Þ|þ/' => 'th',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
            '/Ƴ|Ɏ|Ỵ|Ẏ|Ӳ|Ӯ|Ў|Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
            '/ẙ|ʏ|ƴ|ɏ|ỵ|ẏ|ӳ|ӯ|ў|ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
            '/В/' => 'V',
            '/в/' => 'v',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Φ/' => 'F',
            '/φ/' => 'f',
            '/Χ/' => 'CH',
            '/χ/' => 'ch',
            '/Ź|Ż|Ž|Ζ|З/' => 'Z',
            '/ź|ż|ž|ζ|з/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/' => 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/Ξ/' => 'KS',
            '/ξ/' => 'ks',
            '/Π/' => 'P',
            '/π/' => 'p',
            '/Β/' => 'V',
            '/β/' => 'v',
            '/Μ/' => 'M',
            '/μ/' => 'm',
            '/Ψ/' => 'PS',
            '/ψ/' => 'ps',
            '/Ё/' => 'Yo',
            '/ё/' => 'yo',
            '/Є/' => 'Ye',
            '/є/' => 'ye',
            '/Ї/' => 'Yi',
            '/Ж/' => 'Zh',
            '/ж/' => 'zh',
            '/Х/' => 'Kh',
            '/х/' => 'kh',
            '/Ц/' => 'Ts',
            '/ц/' => 'ts',
            '/Ч/' => 'Ch',
            '/ч/' => 'ch',
            '/Ш/' => 'Sh',
            '/ш/' => 'sh',
            '/Щ/' => 'Shch',
            '/щ/' => 'shch',
            '/Ъ|ъ|Ь|ь/' => '',
            '/Ю/' => 'Yu',
            '/ю/' => 'yu',
            '/Я/' => 'Ya',
            '/я/' => 'ya'
        ];

        $from = array_keys($foreignCharacters);
        $to = array_values($foreignCharacters);

        return preg_replace($from, $to, $str);
    }
}
