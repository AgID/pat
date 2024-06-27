<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use Helpers\FileSystem\Dir;
use Helpers\FileSystem\File;
use Helpers\Security\Acl;
use System\Input;
use System\Log;
use System\Registry;
use System\Security;
use System\Utf8;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('verifyFirstOrLastSlash')) {

    /**
     * Ritorna il site url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function verifyFirstOrLastSlash($uri = null): string
    {
        $segments = defined('CUSTOM_PATH') ? CUSTOM_PATH : '';
        $uri = trim($uri, '/');

        if (strlen($segments) >= 1 && strlen($uri) >= 1) {
            $uri = (substr($segments, -1) !== '/' && $uri[0] !== '/')
                ? $segments . '/' . $uri
                : $segments . $uri;
        }

        return $uri;
    }
}

if (!function_exists('siteUrl')) {

    /**
     * Ritorna il site url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function siteUrl(string $uri = '', $protocol = null): string
    {

        $uri = verifyFirstOrLastSlash($uri);
        $base = new \System\Base();

        return $base->siteUrl($uri, $protocol);
    }
}


if (!function_exists('form_open')) {
    /**
     * @param string    the URI segments of the form destination
     * @param array    a key/value pair of attributes
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function form_open($action = '', $attributes = [], $hidden = [], $csfr_token = null, $reload_all_request = false)
    {

        $base = new \System\Base();
        $uri = new \System\Uri();
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');
        $csfrIsEnable = $config->get('csrf_enable');

        if (!$action) {

            $action = $base->siteUrl($uri->uriString());
        } elseif (preg_match('#{*?}#s', $action)) {

            $action = $action;
        } elseif (strpos($action, '://') === false) {

            $action = verifyFirstOrLastSlash($action);
            $action = $base->siteUrl($action);
        }

        $attributes = stringifyAttributes($attributes);

        if (stripos($attributes, 'method=') === false) {
            $attributes .= ' method="post"';
        }

        if (stripos($attributes, 'accept-charset=') === false) {
            $attributes .= ' accept-charset="' . strtolower(config('charset', null, 'app')) . '"';
        }

        $form = '<form action="' . $action . '"' . $attributes . ">\n";

        if (is_array($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= '<input type="hidden" name="' . $name . '" value="' . htmlEscape($value) . '" />' . "\n";
            }
        }

        if (($csfrIsEnable === true) && ($csfr_token === true || $csfr_token === null)) {

            $form .= csrf_input_token($reload_all_request);
        }

        return $form . "\n";
    }
}

if (!function_exists('getDynamicController')) {
    /**
     * @return
     */
    function getDynamicController()
    {
        $controller = null;
        $uri = uri()->segment(2, 0);
        $institutionId = checkAlternativeInstitutionId();

        $result = \System\Database::table('section_fo')
            ->select(['section_fo.id', 'name', 'controller'])
            ->where('section_fo.id', $uri)
            ->first();

        if (!empty($result)) {

            $section = new \Helpers\Utility\SectionFrontOffice();

            $ancestors = $section->getAncestors($result->id, false, true, null);

            // In caso una pagina dovesse avere più traduzioni(una per il tipo ente e una per Ente), allora si da
            //precedenza a quelle per l'Ente
            $pagine = [];
            foreach ($ancestors as $a) {
                if (empty($pagine[$a->id])) {
                    $pagine[$a->id] = $a;
                }
            }

            $ancestors = [];
            $ancestorsBreadcrumb = [];

            foreach ($pagine as $p) {
                $ancestors[] = $p;
                $ancestorsBreadcrumb[] = (object)['id' => $p->id, 'name' => $p->name];
            }

            Registry::set('__breadcrumbs_front_office', $ancestorsBreadcrumb);

            $controller = "";

            $arrayController = explode('@', $result->controller);
//            \System\Registry::set('__controller_detail', "\Http\Web\Front\\" . $arrayController[0] . '@details');
            Registry::set('__controller_detail', $arrayController[0] . '@details');

            if (count($arrayController) > 1) {

                $controller .= $result->controller;
            } else {

                $controller .= $result->controller . '@index';
            }
        }

        return $controller;
    }
}


if (!function_exists('getDynamicControllerOpendata')) {
    /**
     * @return string|null
     */
    function getDynamicControllerOpendata(): ?string
    {
        $controller = null;
        $uriSegmentId = uri()->segment(3, 0);

        $result = \System\Database::table('section_fo')
            ->select(['id', 'controller_open_data'])
            ->where('id', $uriSegmentId)
            ->first();

        if (!empty($result)) {

            $controller = "\Http\Web\Front\\";
            $arrayController = explode('@', $result->controller_open_data);

            if (count($arrayController) > 1) {

                $controller .= $result->controller_open_data;
            } else {

                $controller .= $result->controller_open_data . '@index';
            }
        }

        return $controller;
    }
}
if (!function_exists('PatOsInstituteId')) {

    /**
     * return institute ID
     *
     * @return Mix bool|int
     */
    function PatOsInstituteId()
    {
        //Controlla se le informazioni dell'Ente sono nel Registro
        if (Registry::exist('pat_os_info_domain')) {
            $patOS_InfoDomain = Registry::get('pat_os_info_domain');
            return $patOS_InfoDomain['id'];
        }

        //Altrimenti le recupera dalla sessione
        if (session()->has('pat_os_domain_info')) {
            $patOS_InfoDomain = session()->get('pat_os_domain_info');
            return $patOS_InfoDomain['id'];
        }

        return false;
    }
}

if (!function_exists('checkAlternativeInstitutionId')) {

    /**
     * Ritorna l'ID dell'ente che si sta gestendo.
     *
     * Se l'utente è super admin ritorna 0 se si stanno gestendo tutti gli enti oppure l'id dell'ente che ha selezionato
     * per la gestione.
     *
     * Se l'utente non è super admin, ritorna l'id dell'ente di appartenenza del dominio in cui si è loggato.
     *
     * @return Mix bool|int
     */
    function checkAlternativeInstitutionId()
    {
        // Prendo l'ID dell'ente che sto gestendo(Importante se si è super Admin)
        $getIdentity = authPatOs()->getIdentity();

        $institutionId = (isSuperAdmin() && (array_key_exists('options', $getIdentity) && array_key_exists('alternative_pat_os_id', $getIdentity['options']) /*&& (int)$getIdentity['options']['alternative_pat_os_id'] !== 0*/))
            ? (int)$getIdentity['options']['alternative_pat_os_id']
            : PatOsInstituteId();

        return $institutionId;
    }
}

if (!function_exists('instituteDir')) {

    /**
     * Ritorna il nome della cartella dei media dell'Ente specificato nei parametri, altrimenti dell'ente in cui si è loggati
     *
     * @param $shortName {nome breve dell'ente}
     * @return mixed|null
     */
    function instituteDir($shortName = null)
    {
        $pathName = null;

        //Controlla se è stato passato nei parametri il nome breve dell'ente
        if (!empty($shortName)) {

            $pathName = $shortName;
        } else {

            $getIdentity = authPatOs()->getIdentity();

            // Controllo se l'utente è super admin e ha selezionato un'ente da gestire e non è su tutti gli enti
            if ((isSuperAdmin() && (array_key_exists('options', $getIdentity) && array_key_exists('alternative_pat_os_id', $getIdentity['options']) && (int)$getIdentity['options']['alternative_pat_os_id'] !== 0))) {

                // Recupero il nome breve dell'ente selezionato dal super admin per la gestione
                $pathName = $getIdentity['options']['alternative_pat_os_short_name'];
            } else {

                // Se l'utente non è super admin recupero il nome breve dell'ente in cui è loggato
                if (Registry::exist('pat_os_info_domain')) {
                    $patOS_InfoDomain = Registry::get('pat_os_info_domain');
                    $pathName = $patOS_InfoDomain['short_institution_name'];
                }
            }
        }

        if ($pathName !== null) {

            //Ritorna il nome della cartella dei media dell'Ente
            return $pathName;
        }

        return null;
    }
}

if (!function_exists('createInstituteDirectory')) {

    /**
     * Funzione che crea le cartelle per l'Ente appena creato
     * Cartelle create: cartella_nome_ente
     * Sotto cartelle: assets, object_attachs, file_archive
     * Sotto cartelle della cartella assets: js, css, images
     *
     * Per ogni cartella crea i file: .htaccess e index.html
     *
     * @param null $shortName {nome breve dell'ente}
     * @return bool|null
     * @throws Exception
     */
    function createInstituteDirectory($shortName = null): ?bool
    {
        //Controlla se sono stati passati nei parametri il nome e l'id dell'Ente
        if (empty($shortName)) {

            return false;
        } else {

            //Array con i path delle cartelle da creare per l'Ente appena creato
            $dirs = [
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR, $shortName],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR, 'assets'],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR, 'object_attachs'],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'file_archive' . DIRECTORY_SEPARATOR, 'flie_archive'],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR, 'js'],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR, 'css'],
                [MEDIA_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR, 'images'],
                [TEMP_PATH . DIRECTORY_SEPARATOR . '{institution_dir}' . DIRECTORY_SEPARATOR, $shortName],
            ];

            //Array dei files da creare in tutte le cartelle appena create
            $files = [
                'index.html' => config('tpl_index_html', null, 'app'),
                '.htaccess' => config('tpl_htacces', null, 'app')
            ];

            foreach ($dirs as $dir) {
                //Path della cartella da creare
                $tmpDir = str_replace('{institution_dir}', $shortName, $dir[0]);
                $isCreatedDir = Dir::create($tmpDir, 0777);

                if ($isCreatedDir) {

                    // Cartelle per File manager
                    Helpers\FileSystem\Dir::create($tmpDir . DIRECTORY_SEPARATOR . '.trash');
                    Helpers\FileSystem\Dir::create($tmpDir . DIRECTORY_SEPARATOR . '.trash' . DIRECTORY_SEPARATOR . '.tmb');

                    // Se la cartella è stata creata con successo, creo i file al suo interno
                    foreach ($files as $k => $v) {
                        // Creo il file all'interno della cartella
                        $isCreatedFile = File::writeFile(
                            $tmpDir . $k,
                            $v
                        );

                        //Controllo se il file è stato creato con successo, altrimenti registro l'errore nei log
                        if (!$isCreatedFile) {
                            Log::danger('Attenzione: non è stato possibile creare il file "' . $k . '" nella cartella "' . $dir[1] . '".');
                        }
                    }
                } else {
                    //Controllo se la cartella non è stata creata con successo registro l'errore nei log
                    Log::danger('Attenzione: non è stato possibile creare la cartella "' . $dir[1] . '".');
                }
            }
        }

        return null;
    }
}

if (!function_exists('patOsInstituteInfo')) {

    /**
     * return the information of the institution
     *
     * @param null $args
     * @return Mix string|array|bool
     */
    function patOsInstituteInfo($args = null)
    {
        $found = false;
        $patOS_Info = null;

        //Controlla se le informazioni sono presenti nel Registro
        if (Registry::exist('pat_os_info_domain')) {

            $patOS_Info = Registry::get('pat_os_info_domain');
            $found = true;
        }

        //Se non sono presenti nel registro controlla se sono presenti in sessione
        if (!$found && session()->has('pat_os_domain_info')) {

            $patOS_Info = session()->get('pat_os_domain_info');
        }

        // Se l'argomento non esiste ritorna tutta la matrice.
        if ($args === null) {

            return $patOS_Info;
        }

        // Se l'argomento è un array seleziona più chiavi.
        if (is_array($args) && count($args) >= 1 && !empty($patOS_Info) && is_array($patOS_Info)) {

            $data = [];

            foreach ($args as $a) {

                if (isset($patOS_Info[$a])) {

                    $data[$a] = $patOS_Info[$a];
                }
            }

            return $data;
        }

        // Se l'argomento è una stringa ritorna una chiave solamente.
        if (is_string($args)) {

            return !empty($patOS_Info[$args]) ? $args : false;
        }

        return false;
    }
}


if (!function_exists('patOsConfigMail')) {

    /**
     * Paramentri di configurazione per invio email
     *
     * @param bool $merge
     * @return array
     */
    function patOsConfigMail($merge = false, $configMail = null)
    {

        // Invio Email
        $customInstitution = patOsInstituteInfo([
            'smtp_username',
            'smtp_password',
            'smtp_host',
            'smtp_port',
            'smtp_security',
            'smtp_auth'
        ]);

        if ($merge === true && $configMail === null) {

            if (isset($customInstitution['smtp_username']) && strlen((string)$customInstitution['smtp_username']) < 1) {
                unset($customInstitution['smtp_username']);
            }

            if (isset($customInstitution['smtp_password']) && strlen((string)$customInstitution['smtp_password']) < 1) {
                unset($customInstitution['smtp_password']);
            }

            if (isset($customInstitution['smtp_port']) && strlen((string)$customInstitution['smtp_port']) < 1) {
                unset($customInstitution['smtp_port']);
            }

            if (isset($customInstitution['smtp_security']) && strlen((string)$customInstitution['smtp_security']) < 1) {
                unset($customInstitution['smtp_security']);
            }

            if (isset($customInstitution['smtp_host']) && strlen((string)$customInstitution['smtp_host']) < 1) {
                unset($customInstitution['smtp_host']);
            }

            $configs = arrayMergeRecursiveDistinct(loadConfigMail(), $customInstitution);
        } elseif ($merge === true && is_array($configMail) && count($configMail) >= 1) {

            $configs = arrayMergeRecursiveDistinct(loadConfigMail(), [
                'smtp_host' => $configMail['smtp_host'],
                'smtp_user' => $configMail['smtp_user'],
                'smtp_pass' => $configMail['smtp_pass'],
                'smtp_port' => $configMail['smtp_port'],
                'smtp_crypto' => $configMail['smtp_security']
            ]);
        } else {

            if (!empty($customInstitution)) {

                $configs['protocol'] = 'smtp';
                $configs['smtp_host'] = !empty($customInstitution['smtp_host']) ? $customInstitution['smtp_host'] : null;
                $configs['smtp_user'] = !empty($customInstitution['smtp_username']) ? $customInstitution['smtp_username'] : null;
                $configs['smtp_pass'] = !empty($customInstitution['smtp_password']) ? $customInstitution['smtp_password'] : null;
                $configs['smtp_port'] = !empty($customInstitution['smtp_port']) ? $customInstitution['smtp_port'] : null;
                $configs['smtp_crypto'] = !empty($customInstitution['smtp_security']) ? $customInstitution['smtp_security'] : null;
            } else {

                $configs = loadConfigMail();
            }
        }

        return $configs;
    }
}

if (!function_exists('arrayMergeRecursiveDistinct')) {

    /**
     * Merge di due array multidimensionali
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    function arrayMergeRecursiveDistinct($array1, $array2)
    {
        static $level = 0;
        $merged = [];
        if (!empty($array2["mergeWithParent"]) || $level == 0) {

            $merged = $array1;
        }

        if (!empty($array2) && is_array($array2)) {
            foreach ($array2 as $key => &$value) {

                if (is_numeric($key)) {

                    $merged[] = $value;
                } else {

                    $merged[$key] = $value;
                }

                if (
                    is_array($value) && isset($array1[$key]) && is_array($array1[$key])
                ) {

                    $level++;
                    $merged[$key] = arrayMergeRecursiveDistinct($array1[$key], $value);
                    $level--;
                }
            }
        }

        unset($merged["mergeWithParent"]);
        return $merged;
    }
}

if (!function_exists('authPatOs')) {

    /**
     * Funzione autenticazione custom Pat OS
     *
     * @return Auth
     */
    function authPatOs()
    {
        $auth = new \System\Auth(\Helpers\AuthPatOS::class);
        return $auth;
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Funzione che verifica se l'utente in sessione e' un super admin (Amministratore della piattaforma)
     *
     * @param $alsoCheckAdmin
     * @return bool
     */
    function isSuperAdmin($alsoCheckAdmin = false)
    {
        if (!is_cli()) {
            $auth = authPatOs();

            if ($auth->hasIdentity()) {

                $userInfo = $auth->getIdentity();

                if (!empty($alsoCheckAdmin)) {
                    return ((int)$userInfo['super_admin'] !== 0) || !empty($userInfo['admin']) ? true : false;
                } else {
                    return ((int)$userInfo['super_admin'] !== 0) ? true : false;
                }

            }

        }

        return false;
    }
}

if (!function_exists('isAdmin')) {

    /**
     * Funzione che verifica se l'utente in sessione e' un admin (Amministratore della piattaforma)
     *
     * @return bool
     */
    function isAdmin()
    {
        $auth = authPatOs();

        if ($auth->hasIdentity()) {

            $userInfo = $auth->getIdentity();

            return (in_array((int)$userInfo['super_admin'], [0, 1])) && ((int)$userInfo['admin'] !== 0) ? true : false;
        }

        return false;
    }
}

if (!function_exists('allowCORS')) {

    /**
     * Abilitazione CORS
     *
     * @return null
     */
    function allowCORS()
    {
        //        header("Access-Control-Allow-Origin: *");
        //        header("Access-Control-Expose-Headers: *");
        //        header("Access-Control-Allow-Headers: *");
        //        header("Access-Control-Request-Headers: Content-Type, x-requested-with");
        //        header("Access-Control-Allow-Credentials: true");
        //        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    }
}

if (!function_exists('br2nl')) {

    /**
     * Trasforma <br/> in new line
     *
     * @param $string
     * @return array|string|string[]|null
     */
    function br2nl($string)
    {
        $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
        return preg_replace("=&lt;br */?&gt;=i", "\n", $string);
    }
}

if (!function_exists('toFloat')) {

    /**
     * Trasforma il valore passato nel tipo float
     * Utilizzata per gli importi
     *
     * @param $num
     * @return float
     */
    function toFloat($num)
    {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", '', $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", '', substr((string)$num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", '', substr((string)$num, $sep + 1, strlen($num)))
        );
    }
}


if (!function_exists('resolveStringBearer')) {

    /**
     * Nell'autenticazione con JWT ritorna il token dagli headers
     *
     * @param $bearer
     * @return null|string
     */
    function resolveStringBearer($bearer)
    {
        if (!empty($bearer) && strlen((string)$bearer) >= 6) {

            return trim(preg_replace('/Bearer|Basic/', '', $bearer));
        }

        return null;
    }
}

if (!function_exists('translateMonth')) {

    /**
     * Funzione che dato un numero restituisce il nome del mese corrispondente.
     *
     * @param $month
     * @return string
     */
    function translateMonth($month = null): string
    {

        $getMonth = '';

        if (!empty($month)) {

            if (is_numeric($month)) {

                if ((int)strlen((string)$month) === 1) {

                    $month = '0' . $month;
                }

                switch ($month) {

                    case '01':
                        $getMonth = 'Gennaio';
                        break;

                    case '02':
                        $getMonth = 'Febbraio';
                        break;

                    case '03':
                        $getMonth = 'Marzo';
                        break;

                    case '04':
                        $getMonth = 'Aprile';
                        break;

                    case '05':
                        $getMonth = 'Maggio';
                        break;

                    case '06':
                        $getMonth = 'Giugno';
                        break;

                    case '07':
                        $getMonth = 'Luglio';
                        break;

                    case '08':
                        $getMonth = 'Agosto';
                        break;

                    case '09':
                        $getMonth = 'Settembre';
                        break;

                    case '10':
                        $getMonth = 'Ottobre';
                        break;

                    case '11':
                        $getMonth = 'Novembre';
                        break;

                    default:
                        $getMonth = 'Dicembre';
                }
            } else {

                $getMonth = $month;
            }
        }

        return $getMonth;
    }
}


if (!function_exists('iconBtn')) {

    /**
     * Funzione per la creazione dell'icona di un pulsante
     *
     * @param string $name Testo da visualizzare nel pulsante
     * @param string $id   id dell'icona da mostrare nel bottone
     * @param string $ico  icona da mostrare nel bottone
     * @return string
     */
    function iconBtn($name = 'Salva', $id = "icon-save", $ico = 'fa-save')
    {
        return '<i id="' . $id . '" class="far ' . $ico . '"></i> ' . $name;
    }
}

if (!function_exists('endKeyOnlyPagination')) {
    /**
     * Paginazione Bootstrap
     *
     * @param $pages
     */
    function endKeyOnlyPagination($pages)
    {
        end($pages);
        return key($pages);
    }
}

if (!function_exists('paginateBootstrap')) {

    /**
     * Paginazione Bootstrap 4
     *
     * @param $elements
     * @return string
     * @throws Exception
     */
    function paginateBootstrap($elements, $rendering = 'display')
    {
        $data['paginator'] = $elements;
        return \System\View::create('pagination_bootstrap', $data)->$rendering();
    }
}

if (!function_exists('form_editor')) {
    /**
     * Funzione che creare un campo di input di tipo CKEDITOR
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_editor($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
        );

        if (!is_array($data) or !isset($data['value'])) {

            $val = $value;
        } else {

            $val = $data['value'];
            unset($data['value']);
        }

        return '<textarea ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " >" . $val . "</textarea>\n";
    }
}

if (!function_exists('sessionSetNotify')) {

    /**
     * Funzione che permette di settare una notifica con il messaggio
     *
     * @param string|null $message
     * @param string      $type    success | warning | info | danger
     */
    function sessionSetNotify(string $message = null, string $type = 'success'): void
    {
        $acceptType = ['success', 'warning', 'info', 'danger'];

        $type = in_array($type, $acceptType) ? $type : 'info';

        $session = new \System\Session();

        $session->setFlash('___has_notify___', true);
        $session->setFlash('___msg_notify___', $message);
        $session->setFlash('___type_notify___', $type);
    }
}

if (!function_exists('sessionHasNotify')) {

    /**
     * Funzione che ritorna true se c'è una notifica in sessione, false altrimenti
     *
     * @return bool
     */
    function sessionHasNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___has_notify___') ? true : false;
    }
}

if (!function_exists('sessionGetNotify')) {

    /**
     * Funzione che ritorna il messaggio della notifica
     *
     * @return string|null
     */
    function sessionGetNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___msg_notify___');
    }
}

if (!function_exists('sessionTypeNotify')) {

    /**
     * Funzione che ritorna il tipo della notifica
     *
     * @return string|null
     */
    function sessionTypeNotify()
    {
        $session = new \System\Session();

        return $session->getFlash('___type_notify___');
    }
}


if (!function_exists('filesUploaded')) {

    /**
     * Funzione per l'upload dei files
     *
     * @return bool
     */
    function filesUploaded($field = 'userfile'): bool
    {
        if (empty($_FILES)) {
            return false;
        }

        $files = @$_FILES[$field]['tmp_name'];

        if (empty($files)) {
            return false;
        }

        if (is_array($files) == false) {
            if (is_uploaded_file($files) == true || file_exists($files) == true) {
                return true;
            }
        } else {

            foreach ($files as $field_title => $temp_name) {
                if (!empty($temp_name) && (is_uploaded_file($temp_name) == true || file_exists($temp_name) == true)) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('getIdentity')) {

    /**
     * Funzione che restituisce tutte le informazioni dell'utente in sessione, oppure solo i campi passati nel parametro
     *
     * @param null $data
     * @return array|null
     */
    function getIdentity($data = null)
    {
        $identity = authPatOs()->getIdentity();

        if (!empty($identity[$data])) {

            return $identity[$data];
        } else if (!empty($identity['options'][$data])) {

            return $identity['options'][$data];
        }

        return null;
    }
}

if (!function_exists('avatar')) {

    /**
     * Funzione che restituisce l'avatar con l'immagine del profilo dell'utente
     *
     * @return string
     * @throws Exception
     */
    function avatar()
    {
        $identity = authPatOs()->getIdentity(['profile_image']);

        $avatar = (!empty($identity['profile_image']) && $identity !== null && $identity['profile_image'] !== '')
            ? 'media/' . instituteDir() . '/assets/images/' . $identity['profile_image']
            : 'assets/admin/img/avatar.png';

        return baseUrl($avatar);
    }
}

if (!function_exists('isIe11')) {

    /**
     * Funzione che controlla se il browser in uso è Internet Explorer 11
     *
     * @return bool
     */
    function isIe11()
    {
        $agent = new Jenssegers\Agent\Agent();
        $browser = $agent->browser();
        $version = $agent->version($browser);

        if ($version == '11.0') {

            return true;
        }

        return false;
    }
}

if (!function_exists('guard')) {

    /**
     * Funzione che verifica se un determinato utente ha il privilegio di accedere a una determinata
     * pagina.
     *
     * @return bool
     */
    function guard(): bool
    {
        $guard = false;

        if (!Registry::exist('___patos___profiles___not_run__')) {

            if (Registry::exist('___patos___profiles___') && Registry::exist('___patos___profiles___method__')) {

                $profiles = Registry::get('___patos___profiles___');
                $methodInRoute = Registry::get('___patos___profiles___method__');

                foreach ($methodInRoute as $method) {
                    if (!empty($profiles[$method])) {

                        $guard = true;
                        break;
                    }
                }
            }
        } else {

            $guard = true;
        }


        return $guard;
    }
}

if (!function_exists('getSectionPagesBackOffice')) {

    /**
     * Funzione che restituisce il menù laterale di sinistra e la sua alberatura
     *
     * @return array
     */
    function getSectionPagesBackOffice()
    {
        $sections = new \System\Hierarchy();
        return $sections->getGroupedChildren();
    }
}

if (!function_exists('removeDotHtml')) {

    /**
     * Funzione che rimuove ".html" o ".htm" da una stringa
     *
     * @param null $str
     * @return string
     */
    function removeDotHtml($str = null)
    {
        return str_replace(['.html', '.htm'], ['', ''], $str);
    }
}

if (!function_exists('setUpperCaseRowTable')) {

    /**
     * Funzione che converte tutti i caratteri di una riga di una tabella in caratteri maiuscoli
     *
     * @param bool $mode
     * @param $string
     * @param bool $strong
     * @return string
     */
    function setUpperCaseRowTable($string, $mode = false, $strong = false)
    {
        $strongOpen = $strong === true ? '<strong>' : '';
        $strongClose = $strong === true ? '</strong>' : '';
        return ($mode === true)
            ? $strongOpen . mb_strtoupper($string, CHARSET) . $strongClose
            : $strongOpen . $string . $strongClose;
    }
}

if (!function_exists('btnSave')) {

    /**
     * Funzione che crea un pulsante per il salvataggio dei dati
     *
     * @param string $id
     * @param bool   $title
     * @return string
     * @throws Exception
     */
    function btnSave($id = "btn_save", $title = false)
    {
        $title = ($title == false) ? __('brn_save', null, 'patos') : $title;
        $html = '<button name="send" type="submit" id="' . $id . '" class="btn btn-outline-primary">';
        $html .= $title;
        $html .= '</button>';
        $html .= nbs(2);
        $html .= '<span></span>';

        return $html;
    }
}

if (!function_exists('searchArrayByField')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param null   $value
     * @param array  $data
     * @param string $field
     * @return array|null
     */
    function searchArrayByField($value = null, $data = [], $field = '')
    {
        return array_search($value, array_column($data, $field));
    }
}

if (!function_exists('multiSearch')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param array $array
     * @param array $pairs
     * @return array|null
     */
    function multiSearch(array $array, array $pairs)
    {
        $found = array();
        foreach ($array as $aKey => $aVal) {

            $coincidences = 0;
            foreach ($pairs as $pKey => $pVal) {

                if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {

                    $coincidences++;
                }
            }

            if ($coincidences == count($pairs)) {

                $found[$aKey] = $aVal;
            }
        }

        return $found;
    }
}

if (!function_exists('convertDateToDatabase')) {

    /**
     * Funzione che converte una data nel formato datetime per essere salvata sul database
     *
     * @param $dateTime
     * @return false|string
     */
    function convertDateToDatabase($dateTime)
    {
        $dateTime = explode('-', $dateTime);

        return date($dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2]);
    }
}

if (!function_exists('convertDateToImport')) {

    /**
     * Funzione che converte una data nel formato d-m-Y
     *
     * @param $dateTime
     * @return false|string
     */
    function convertDateToImport($dateTime)
    {
        $dateTime = explode('/', $dateTime);

        return date($dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2]);
    }
}

if (!function_exists('convertDateToForm')) {

    /**
     * Funzione che converte una data nel formato timestamp accettato dal form
     *
     * @param $timeStamp
     * @return array
     */
    function convertDateToForm($timeStamp)
    {

        $data = [
            'date' => null,
            'hours' => null
        ];

        if (!empty($timeStamp)) {

            $temp = explode(' ', $timeStamp);

            $dateTime = explode('-', $temp[0]);
            $hours = explode(':', $temp[1]);

            if (!empty($dateTime) && is_array($dateTime)) {

                $data['date'] = $dateTime[0] . '-' . $dateTime[1] . '-' . $dateTime[2];
            }

            if (!empty($hours) && is_array($hours)) {

                $data['hours'] = $hours[0] . ':' . $hours[1];
            }
        }

        return $data;
    }
}

if (!function_exists('setDefaultData')) {

    /**
     * Funzione che ritorna un parametro predefinito in base al valore passato
     *
     * @param null   $data
     * @param null   $default
     * @param string $expected
     * @return mixed|null
     */
    function setDefaultData($data = null, $default = null, $expected = [null, 0, false])
    {

        if (in_array($data, $expected, true)) {

            return $default;
        }

        return $data;
    }
}

if (!function_exists('setOrderDatatable')) {

    /**
     * Funzione che imposta la colonna su cui effettuare l'ordinamento nel datatable
     *
     * @param null   $columnName
     * @param array  $orderable
     * @param string $default
     * @return mixed|null
     */
    function setOrderDatatable($columnName = null, array $orderable = [], string $default = '')
    {
        return !empty($columnName) && is_int($columnName) && array_key_exists($columnName, $orderable)
            ? $orderable[$columnName]
            : $default;
    }
}

if (!function_exists('getAclVersioning')) {

    /**
     * Funzione che restituisce il permesso di versioning che ha l'utente
     *
     * @return bool
     */
    function getAclVersioning()
    {
        return Acl::getVersioning();
    }
}

if (!function_exists('getAclArchiving')) {

    /**
     * Funzione che restituisce il permesso di versioning che ha l'utente
     *
     * @return bool
     */
    function getAclArchiving()
    {
        return Acl::getArchiving();
    }
}

if (!function_exists('getAclLockUser')) {

    /**
     * Funzione che restituisce il permesso di blocco/sblocco degli utenti che ha l'utente
     *
     * @return bool
     */
    function getAclLockUser()
    {
        return Acl::getLockUser();
    }
}

if (!function_exists('getAclModifyProfile')) {

    /**
     * Funzione che restituisce il permesso di modifica avanzata del profilo che ha l'utente
     *
     * @return bool
     */
    function getAclModifyProfile()
    {
        return Acl::getModifyProfile();
    }
}

if (!function_exists('getAclExportCsv')) {

    /**
     * Funzione che restituisce i permessi di export dei dati in CSV dell'utente
     *
     * @return bool
     */
    function getAclExportCsv()
    {
        return Acl::getExportCsv();
    }
}

if (!function_exists('getAclDelete')) {

    /**
     * Funzione che restituisce i permessi di eliminazione dell'utente su una sezione
     *
     * @return bool
     */
    function getAclDelete()
    {
        if (Registry::exist('___patos___profiles___')) {

            $keys = Registry::get('___patos___profiles___');
            return (bool)$keys['delete'];
        }

        return false;
    }
}

if (!function_exists('getAclAdd')) {

    /**
     * Funzione che restituisce i permessi di inserimento dell'utente su una sezione
     *
     * @return bool
     */
    function getAclAdd()
    {
        if (Registry::exist('___patos___profiles___')) {

            $keys = Registry::get('___patos___profiles___');
            return (bool)$keys['create'];
        }

        return false;
    }
}

if (!function_exists('createdByCheckDeleted')) {

    /**
     * Funzione che mostra nel datatable se un utente è stato cancellato
     *
     * @param string|null $name    Nome dell'utente cifrato
     * @param int|null    $deleted Indica se l'utente è stato eliminato
     *                             o meno
     * @return string
     */
    function createdByCheckDeleted(string|null $name = null, int|null $deleted = 0): string
    {
        $html = '';

        if ($deleted === 1) {

            $html .= '<span class="badge badge-danger" aria-label="' . checkDecrypt($name) . ': Utente eliminato" data-toggle="tooltip" data-placement="top" data-original-title="Utente Eliminato">';
        } else {

            $html .= '<span class="badge badge-success">';
        }

        if ($name !== null) {

            $html .= escapeXss(checkDecrypt($name));
        } else {
            $html .= 'Utente non presente';
        }

        if ($deleted === 1) {

            $html .= nbs(2) . '<i class="fas fa-user-times"></i></span>';
        } else {

            $html .= nbs(2) . '</span>';
        }

        return $html;
    }
}

if (!function_exists('instituteNameSelected')) {
    /**
     * Funzione che ritorna il nome dell'ente selezionato
     *
     * @return string
     */
    function instituteNameSelected()
    {
        $fullName = patOsInstituteInfo(['full_name_institution']);
        $patSsFullName = session()->has('alternative_pat_os_full_name') ? session()->get('alternative_pat_os_full_name') : $fullName['full_name_institution'];
        return 'data-placeholder="' . $patSsFullName . '" placeholder="' . $patSsFullName . '" class="form-control form-control-sm select2-_cinst _cinst" id="_cinst" style="width:200px; font-size:90%"';
    }
}

if (!function_exists('wordLimiter')) {
    /**
     * Limits a string to X number of words.
     *
     * @param string $str
     * @param int    $limit
     * @param string $end_char the end character
     * @return string
     */
    function wordLimiter($str, $limit = 100, $end_char = '&#8230;')
    {
        if (trim($str) === '') {

            return $str;
        }

        preg_match('/^\s*+(?:\S++\s*+){1,' . (int)$limit . '}/', $str, $matches);

        if (strlen((string)$str) === strlen((string)$matches[0])) {

            $end_char = '';
        }

        return rtrim($matches[0]) . $end_char;
    }
}


if (!function_exists('characterLimiter')) {
    /**
     * Limita le parole data una stringa
     * @param $str
     * @param $n
     * @param $endChar
     * @return array|mixed|string|string[]|void|null
     */
    function characterLimiter($str, $n = 500, $endChar = '&#8230;')
    {
        if (mb_strlen((string)$str) < $n) {
            return $str;
        }

        $str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $str));

        if (mb_strlen((string)$str) <= $n) {
            return $str;
        }

        $out = '';
        foreach (explode(' ', trim($str)) as $val) {
            $out .= $val . ' ';

            if (mb_strlen($out) >= $n) {
                $out = trim($out);
                return (mb_strlen($out) === mb_strlen((string)$str)) ? $out : $out . $endChar;
            }
        }
    }
}

if (!function_exists('shortInstitutionName')) {

    /**
     * Funzione che restituisce il nome breve dell'ente
     *
     * @param string $str Nome completo dell'Ente
     * @return string
     */
    function shortInstitutionName($str = null)
    {
        helper('url');

        return urlTitle(wordLimiter($str, 3, ''), '_', true);
    }
}

if (!function_exists('moveFileInDirMedia')) {

    /**
     * Funzione che sposta il file nella cartella media dell'Ente quando viene creato
     *
     * @param string $fileName     Nome del file da spostare
     * @param string $instituteDir Nome della cartella media dell'ente
     * @return void
     */
    function moveFileInDirMedia($fileName = null, $instituteDir = null)
    {
        $from = MEDIA_PATH . '/' . $fileName;
        $to = MEDIA_PATH . '/' . $instituteDir . '/' . $fileName;
        File::copy($from, $to);
        File::delete($from);
    }
}

if (!function_exists('write_file')) {
    /**
     *
     * Funzione che permette di scrivere i dati nel file specificato nel percorso.
     * Se il file non esiste lo crea.
     *
     * @param string $path File path
     * @param string $data Data to write
     * @param string $mode fopen() mode (default: 'wb' write in binary mode)
     * @return    bool
     */
    function write_file($path, $data, $mode = 'wb')
    {
        if (!$fp = @fopen($path, $mode)) {
            return false;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen((string)$data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr((string)$data, $written))) === false) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }
}

if (!function_exists('loadElfinderJs')) {

    /**
     * Paginazione del file-manager elFinder
     *
     * @param $elements
     * @throws Exception
     */
    function loadElfinderJs($elements = null)
    {

        // header("X-Frame-Options: sameorigin");
        $data[] = $elements;
        $data['isCkEditor'] = (bool)empty($elements['isCkEditor']) ? false : true;
        $data['base_url'] = baseUrl('admin/sys/filemanager');
        $data['my_folder'] = isSuperAdmin()
            ? MEDIA_PATH . instituteDir() . '/' . authPatOs()->id()
            : MEDIA_PATH;

        return \System\View::create('elfinder_javascript', $data)->display();
    }
}


if (!function_exists('treeSelectOptionValue')) {
    /**
     * Creazione del tag option per la select
     *
     * @param $data
     */
    function treeSelectOptionValue($tree, $parentId = null, $institutionId = null)
    {
        $eId = !empty($institutionId) ? $institutionId : checkAlternativeInstitutionId();

        $html = '';
        foreach ($tree as $item) {

            if ((int)$item['is_system'] === 1 || (int)$eId == (int)$item['institution_id']) {

                $customPage = ($item['is_system'] === 0 && $eId === $item['institution_id'])
                    ? '[p] '
                    : '';

                $selected = (int)$item['id'] === (int)$parentId ? ' selected ' : '';

                $html .= '<option value="' . $item['id'] . '" ' . $selected . '>';

                $html .= '|' . str_repeat("_", $item['deep']) . '/ ' . $customPage . $item['name'];

                $html .= "</option>";

                if ($item['children']) {

                    $html .= treeSelectOptionValue(
                        $item['children'],
                        $parentId,
                        $eId
                    );
                }
            }
        }

        return $html;
    }
}

/**
 * Funzione che restituisce un array di ID
 *
 * @param object $obj
 * @return array|null
 */
if (!function_exists('extractIds')) {
    function extractIds($obj)
    {
        $result = [];

        if (isset($obj['id'])) {
            $result[] = $obj['id'];
        }

        if (isset($obj['children'])) {
            return array_merge($result, ...array_map('extractIds', $obj['children']));
        }

        return $result;
    }
}

if (!function_exists('getLasHistoryUrl')) {

    /**
     * Funzione che cerca un riga all'interno di un array multidimensionale
     *
     * @param null   $value
     * @param array  $data
     * @param string $field
     * @return array|null
     */
    function getLasHistoryUrl()
    {
        // $session = new System\Session();
        // return $session->getFlash('last_history_url');
        return Registry::get('last_history_url');
    }
}

if (!function_exists('fileToBase64')) {

    /**
     * Funzione che converte un file in Base 64.
     * @param $file
     * @return string
     */
    function fileToBase64($file)
    {
        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $type = $fInfo->file($file);
        return 'data:' . $type . ';base64,' . base64_encode(File::getContent($file));
    }
}

if (!function_exists('floatvalue')) {

    /**
     * Funzione per la formattazione degli importi nel formato italiano
     * @param $val
     * @return float
     */
    function floatvalue($val)
    {
        $val = str_replace(",", ".", $val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }
}

if (!function_exists('forceDownloadDynamic')) {

    function forceDownloadDynamic($templateString = '', $filename = '', $contentType = 'text/html', $printDoc = false)
    {

        $now = gmdate("D, d M Y H:i:s");

        header('Pragma: private');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: " . $now . " GMT");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen((string)$templateString));
        header('Connection: close');

        echo $templateString;
    }
}
if (!function_exists('downloader')) {

    function downloader($filename = '', $data = '', $setMime = false, $alias = null, $robotsIndex = false, $printDoc = false)
    {

        if ($filename === '' or $data === '') {
            return;
        } elseif ($data === null) {

            if (is_array($filename)) {
                if (count($filename) !== 1) {
                    return;
                }

                reset($filename);
                $filepath = key($filename);
                $filename = current($filename);

                if (is_int($filepath)) {
                    return;
                }
            } else {

                $filepath = $filename;
                $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
                $filename = end($filename);
            }

            if (!@is_file($filepath) or ($filesize = @filesize($filepath)) === false) {

                return;
            }
        } else {

            $filesize = strlen((string)$data);
        }

        if ($filesize == 0) {
            echo show404('ATTENZIONE', 'File non presente');
            die();
        }

        $mime = 'application/octet-stream';

        $x = explode('.', $filename);
        $extension = end($x);

        if ($setMime === true) {
            if (count($x) === 1 or $extension === '') {
                return;
            }

            $mimes = &getMimes();

            if (isset($mimes[$extension])) {
                $mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
            }
        }

        if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {

            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode('.', $x);
        }

        if (ob_get_level() !== 0 && @ob_end_clean() === false) {

            @ob_clean();
        }

        $utf8 = new Utf8(null);
        $charset = strtoupper(CHARSET);

        if ($alias != null) {

            $utf8Filename = ($charset !== 'UTF-8')
                ? $utf8->convertToUtf8($alias, $charset)
                : $alias;
        } else {

            $utf8Filename = ($charset !== 'UTF-8')
                ? $utf8->convertToUtf8($filename, $charset)
                : $filename;
        }

        isset($utf8Filename[0]) && $utf8Filename = " filename*=UTF-8''" . rawurlencode($utf8Filename);

        $contentDisposition = 'attachment';

        if ($printDoc === true) {

            $contentTypeForPrintDoc = [
                'application/pdf',
                'application/x-pdf',
                'application/acrobat',
                'applications/vnd.pdf',
                'text/pdf',
                'text/x-pdf',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/x-tiff',
                'image/svg+xml',
            ];

            $contentDisposition = in_array($mime, $contentTypeForPrintDoc)
                ? 'inline'
                : 'attachment';
        }

        header('Content-Type: ' . $mime);
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $filesize);
        header('Cache-Control: private, no-transform, no-store, must-revalidate');
        header('Content-Disposition: ' . $contentDisposition . '; filename="' . $filename . '";' . $utf8Filename);

        if ($robotsIndex) {
            header("X-Robots-Tag: noindex, nofollow", true);
        }

        if ($data !== null) {
            exit($data);
        }

        if (@readfile($filepath) === false) {
            return;
        }
    }
}


if (!function_exists('checkGet')) {

    function checkGet($data = [])
    {

        foreach (\System\Input::get($data) as $key => $value) {

            if ($value != null) {

                return true;

                continue;
            }
        }
        return false;
    }
}
if (!function_exists('isArrayMultidimensional')) {
    function isArrayMultidimensional($array)
    {
        return is_array($array[array_key_first($array)]);
    }
}

if (!function_exists('checkRecordOwner')) {
    /**
     * Metodo che controlla se l'utente loggato è il creatore del record con l'id passato nel parametro
     * @param int|null $recordOwnerId Id utente
     * @param int|null $userId        identificativo dell'utente da prendere come riferimento
     * @return bool
     */
    function checkRecordOwner(int $recordOwnerId = null, int $userId = null): bool
    {
        if ($userId === null) {
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);
            if (!empty($recordOwnerId)) {
                return $getIdentity['id'] == $recordOwnerId;
            }
        } else {
            return $userId === $recordOwnerId;
        }

        return false;
    }
}

if (!function_exists('sanitizeArray')) {
    /**
     * Funzione di sanificazione per la concat nelle query
     * @param $array dei campi da sanificare
     * @return string
     */
    function sanitizeArray($array)
    {
        $validFields = ['object', 'name', 'code', 'denomination', 'title', 'object_structures.structure_name'];
        $data = null;
        if (!empty($array) && is_array($array)) {
            foreach ($array as $arr) {
                $item = !empty($arr)
                    ? preg_replace("/[^A-Za-z_.]/", '', removeInvisibleCharacters($arr))
                    : '';

                if (in_array($item, $validFields)) {
                    $data[] = $item;
                }
            }
        }

        return $data;
    }
}

if (!function_exists('checkEncrypt')) {

    /**
     * Funzione controlla ed avvia eventualmente la crittografica statica della stringa passata
     * @param $data campo avviare il controlloo della crittografia
     * @return string
     */
    function checkEncrypt($data = '')
    {

        if (strlen((string)$data) >= 1) {

            return (!empty(_env('ENCRYPTION_DB_DATA')) ? \Helpers\Security\Crypto::encrypt((string)$data) : (string)$data);
        }

        return null;
    }
}

if (!function_exists('checkDecrypt')) {

    /**
     * Funzione controlla e avvia eventualmente la decrittografica statica della stringa passata
     * @param string $data campo avviare il controllo della decrittografia
     * @return string|null
     */
    function checkDecrypt(string $data = ''): ?string
    {

        if (strlen($data) >= 1) {

            return (!empty(_env('ENCRYPTION_DB_DATA')) ? \Helpers\Security\Crypto::verify($data) : $data);
        }

        return null;
    }
}

if (!function_exists('insertMeta')) {
    /**
     * Funzione per l'inserimento dei dati nelle tabelle META
     * @param $insertId
     * @param $institutionId
     * @param $model_name
     * @param $value
     * @param $newColumnName
     * @param $MetaGroup
     * @return void
     */
    function insertMeta($insertId, $institutionId, $model_name, $value, $newColumnName, $metaGroup = null)
    {
        $model = '\\Model\\' . $model_name;

        if (!empty($value) or (empty($value) and is_bool($value))) {

            $insertMetaID = $model::create([
                'institution_id' => $institutionId,
                'reference_id' => $insertId,
                //                        'meta_label' => serialize([
                //                            'key' => $agent->platform()
                //                        ]),
                'meta_key' => $newColumnName,
                'meta_value' => $value,
                'meta_group' => $metaGroup,
            ]);
        }
    }
}

if (!function_exists('referenceOriginForRegenerateToken')) {
    /**
     * Funzione che verifica e
     * @param int    $num         Numero di segmento
     * @param string $segmentName Segment name
     * @return bool
     * @throws Exception
     */
    function referenceOriginForRegenerateToken(int $num = 3, string $segmentName = 'create-box.html'): bool
    {
        $ref = session()->get('__frm_history_t');

        if (!empty($ref)) {

            $segments = explode('/', parse_url($ref['url'], PHP_URL_PATH));

            if (!empty($segments[$num]) && rtrim($segments[$num], '.html') === rtrim($segmentName, '.html')) {
                session()->kill('__frm_history_t');
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('getFullName')) {
    /**
     * Funzione che ritorna il nome completo unendo nome e cognome
     * @param $str1
     * @param $str2
     * @return string
     */
    function getFullName($str1, $str2): string
    {
        return $str1 . ' ' . $str2;
    }
}

if (!function_exists('setDataTableData')) {
    /**
     * @description Funzione che setta i dati per il funzionamento dei datatable nel back-office
     * @param $data       array
     * @param $columnName string Nome della colonna su cui effettuare l'ordinamento
     * @return void
     */
    function setDataTableData(array &$data, string $columnName): void
    {
        $data['draw'] = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
        $data['start'] = !empty(Input::get("start")) ? (int)Input::get("start", true) : 0;
        $data['rowPerPage'] = !empty(Input::get("length")) ? Input::get("length", true) : 25;

        $security = new Security();

        $columnIndexArr = Input::get('order', true);
        $columnNameArr = Input::get('columns', true);
        $orderArr = Input::get('order', true);
        $searchArr = !empty($_GET['search']) ? $security->xssClean(removeInvisibleCharacters($_GET['search'])) : null;

        $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
        $data['columnName'] = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : $columnName;
        $data['columnSortOrder'] = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
        $data['searchValue'] = !empty($searchArr['value']) ? trim($searchArr['value']) : null;
    }
}

if (!function_exists('urlWithoutWww')) {
    /**
     * @description Funzione che analizza una stringa e ritorna un array con e senza il www
     * @param $url string la stringa das analizzare
     * @return void
     */
    function urlWithoutWww(string $url): array
    {
        $urlWithoutWww = preg_replace('/www\./', '', $url);

        if (!preg_match("/^https?:\/\/www\./", $url)) {
            $url = preg_replace('/https?:\/\//', 'http://www.', $url);
        }

        return [
            $urlWithoutWww,
            $url
        ];
    }
}

if (!function_exists('addWwwToUrl')) {
    /**
     * @description Funzione che analizza una stringa e verifica se esiste il www. Se non esiste glielo inserisce
     * @param $url
     * @return mixed|string
     */
    function addWwwToUrl($url)
    {
        if (strpos($url, 'http://') === 0) {
            $prefix = 'http://';
            $url = substr($url, 7);
        } elseif (strpos($url, 'https://') === 0) {
            $prefix = 'https://';
            $url = substr($url, 8);
        } else {
            return $url;
        }

        if (strpos($url, 'www.') !== 0) {
            $url = 'www.' . $url;
        }

        return $prefix . $url;
    }
}


if (!function_exists('duplicateAttach')) {
    /**
     * @description Funzione che duplica gli allegati dopo la duplicazione di un oggetto
     * @param int|null  $id          Id dell'elemento appena inserito di cui duplicare gli allegati
     * @param string    $archiveName Nome dell'archivio dell'elemento duplicato
     * @param int|null  $originId    Id dell'allegato che si deve duplicare
     * @param int       $sort        Ordinamento dell'allegato
     * @param Utf8|null $utf8        Classe per la pulizia del nome dell'allegato
     * @return void
     */
    function duplicateAttach(int $id = null, string $archiveName = '', int $originId = null, int $sort = 1, Utf8 $utf8 = null, null|string $bdncpCat = ''): void
    {

        if (!empty($originId)) {

            //Id degli allegati da duplicare
            $attachIds = Input::post('attach_id' . $bdncpCat) ?? [];

            $attachIds = array_diff($attachIds, ['null']);

            //Recupero gli allegati originali che devo duplicare
            $originAttach = optional(\Model\AttachmentsModel::where('id', $originId)
                ->first())
                ->toArray();

            if (!empty($originAttach)) {

                //Path in cui duplicare gli allegati
                $uploadPath = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $archiveName . DIRECTORY_SEPARATOR;

                $ext = $originAttach['file_ext'];
                $tmpName = md5(uniqid(mt_rand()));

                //Duplico il file fisico
                if (File::copy($originAttach['full_path'], $uploadPath . $tmpName . $ext)) {

                    $_POST['temp_label_attach'] = !empty($_POST['label_attach' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['label_attach' . $bdncpCat][$sort]), true, false))
                        : null;
                    $_POST['temp_publish'] = !empty($_POST['publish']) && (!empty($_POST['publish' . $bdncpCat][$sort]) || $_POST['publish' . $bdncpCat][$sort] == 0)
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['publish' . $bdncpCat][$sort]), true, false))
                        : 1;
                    $_POST['temp_omissis'] = !empty($_POST['omissis']) && !empty($_POST['omissis' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['omissis' . $bdncpCat][$sort]), true, false))
                        : 0;
                    $_POST['temp_bdncp_cat'] = !empty($_POST['bdncp_cat' . $bdncpCat]) && !empty($_POST['bdncp_cat' . $bdncpCat][$sort])
                        ? strip_tags(escapeXss(removeInvisibleCharacters($_POST['bdncp_cat' . $bdncpCat][$sort]), true, false))
                        : 0;
                    $_POST['temp_category'] = !empty($_POST['category'][$sort])
                        ? $_POST['category'][$sort]
                        : 1;


                    //Aggiorno il record del file nel db con il nuovo nome utilizzato per salvarlo sul file system
                    // e le altre relative informazioni
                    \Model\AttachmentsModel::create([
                        'file_name' => $tmpName . $ext,
                        'raw_name' => $tmpName,
                        'file_path' => $uploadPath,
                        'full_path' => $uploadPath . $tmpName . $ext,
                        'fingerprint' => hash_file('sha256', $uploadPath . $tmpName . $ext),
                        'file_size' => $originAttach['file_size'],
                        'archive_id' => $id,
                        // Insert ID
                        'archive_name' => $archiveName,
                        'sort' => $sort + 1,
                        'institution_id' => checkAlternativeInstitutionId(),
                        'cat_id' => (int)Input::post('temp_category'),
                        'file_type' => $originAttach['file_type'],
                        'orig_name' => $utf8->cleanString(urlTitle($originAttach['orig_name'])),
                        'client_name' => $originAttach['client_name'],
                        'file_ext' => $originAttach['file_ext'],
                        'is_image' => $originAttach['is_image'],
                        'image_width' => $originAttach['image_width'],
                        'image_height' => $originAttach['image_height'],
                        'image_type' => $originAttach['image_type'],
                        'image_size_str' => $originAttach['image_size_str'],
                        'label' => strip_tags(Input::post('temp_label_attach', true)),
                        'bdncp_cat' => strip_tags(Input::post('temp_bdncp_cat', true)),
                        'active' => (int)strip_tags((string)Input::post('temp_publish', true)),
                        'indexable' => (int)strip_tags((string)Input::post('temp_omissis', true)),
                    ]);
                }

            }
        }
    }
}

if (!function_exists('toSql')) {
    /**
     * Ritorna la query SQL costruita (utile solo per scopi di debug)
     * @param string $sql
     * @param array  $bindings
     * @return string
     */
    function toSql(string $sql = '', array $bindings = []): string
    {
        $data = '';
        if (strlen($sql) >= 1 && count($bindings) >= 1) {
            $data = vsprintf(str_replace('?', '%s', $sql), $bindings);;
        }

        return $data;
    }
}


if (!function_exists('getAclProfileInfo')) {

    /**
     * Funzione che restituisce il permesso di che ha l'utente riguardo name
     *
     * @param string $name Nome del permesso generale che si vuole controllare
     * @return bool
     */
    function getAclProfileInfo(string $name = ''): bool
    {
        return Acl::getAclProfileInfo($name);
    }
}

if (!function_exists('removePhoto')) {
    /**
     * @description Funzione per la cancellazione della foto
     * @param string|null $fileName Nome del file da cancellare
     * @return void
     */
    function removePhoto(?string $fileName = ''): void
    {
        $path = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;

        // Controllo se il file allegato esiste prima di eliminarlo
        if (!empty($fileName) && File::exists($path . $fileName)) {
            File::delete($path . $fileName);
        }

    }
}
if (!function_exists('isSerialized')) {
    function isSerialized($value, &$result = null)
    {
        if (!is_string($value)) {
            return false;
        }

        set_error_handler(function () {
        }, E_NOTICE);

        $result = unserialize($value);
        restore_error_handler();

        return $result !== false || $value === 'b:0;';
    }
}

if (!function_exists('convertDateForCsv')) {

    /**
     * Funzione che converte una data nel formato datetime per essere salvata sul database
     *
     * @param $date  {data da convertire}
     * @param bool $hours {true se si vuole restituire anche ora:minuti:secondi}
     * @return null|string
     */
    function convertDateForCsv($date = null, bool $hours = false): null|string
    {
        if (!empty($date)) {
            $explodeDate = explode(' ', $date);
            $data = explode('-', $explodeDate[0]);

            if ($hours) {
                return $data[2] . '/' . $data[1] . '/' . $data[0] . ' ' . $explodeDate[1];
            } else {
                return $data[2] . '/' . $data[1] . '/' . $data[0];
            }
        }
        return null;
    }
}


if (!function_exists('generateSeparator')) {

    /**
     * Funzione che genera il separatore presente nei vari form
     *
     * @param $name {titolo del separatore}
     * @param $icon {icona}
     * @return string
     */
    function generateSeparator(string $name = '', string $icon = ''): string
    {
        if ($icon != '') {
            return '<div class="custom-separator"><h5><i class="' . $icon . '"></i> ' . $name . '</h5></div>';
        } else {
            return '<div class="custom-separator"><h5>' . $name . '</h5></div>';
        }
    }
}