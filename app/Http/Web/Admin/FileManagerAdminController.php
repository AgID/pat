<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use elFinder;
use elFinderConnector;
use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\Dir;
use Helpers\FileSystem\File;
use Helpers\Utility\FileUpload;
use System\Log;
use System\Session;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

function access($attr, $path, $data, $volume, $isDir, $relpath)
{
    $basename = basename($path);
    $part = substr($basename, 0, 6);
    if ($part == 'utente' && $isDir && ($attr == 'locked')) {
        return true;
    }
    return $basename[0] === '.'
    && strlen((string) $relpath) !== 1
        ? !($attr == 'read' || $attr == 'write')
        : null;
}

class FileManagerAdminController extends BaseAuthController
{
    public $tracing = [
        'rm',
        'paste',
        'duplicate',
        'rename',
        'mkdir'
    ];

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        elFinder::$netDrivers['ftp'] = 'FTP';

        $getMimes = self::getMimes();
        $uploadMaxSize = FileUpload::maxSize();
        $applicationUploadMaxSize = (int)config('max_size', 0, 'perms_files');

        if ($applicationUploadMaxSize > 0) {
            $applicationUploadMaxSize *= 1024;
            $uploadMaxSize = $uploadMaxSize > 0
                ? min($uploadMaxSize, $applicationUploadMaxSize)
                : $applicationUploadMaxSize;
        }

        $path = $this->getPath();

        $opts = [
            'root_options' => array(
                'defaults' => array('read' => true, 'write' => true, 'locked' => true, 'remove' => true),
            ),
            'bind' => array(
                'mkdir mkfile rename duplicate upload rm paste extract' => function ($cmd, $result, $args, $elfinder, $volume) {

                    foreach ($result as $key => $value) {
                        if (empty($value)) {
                            continue;
                        }
                        $data = [];

                        if (in_array($key, array('error', 'warning'))) {

                            array_push($data, implode(' ', $value));

                        } else {

                            if (is_array($value)) {

                                foreach ($value as $file) {

                                    if (is_array($file)) {

                                        $filepath = ($file['realpath'] ?? $this->getRelativePath($elfinder, $file['hash'], "file_archive"));
                                        array_push($data, $filepath);

                                    }

                                }

                            } else {

                                array_push($data, $value);

                            }

                        }
//Log::print($cmd);
//Log::print($key);
//Log::print($args);
                        $this->notificationOutcome($cmd, $key, $result, $args, $elfinder);

                        $this->log($cmd, $result, $args, $elfinder, $volume);
                    }

                }
            ),
            'roots' => [
                // Items volume
                [
                    'alias' => 'Archivio file',
                    'driver' => 'LocalFileSystem',
                    'path' => $path['path'],
                    'URL' => $path['base_dir'],
                    'trashHash' => 't1_Lw',
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/',
                    'uploadDeny' => [
                        'all'
                    ],
                    'uploadAllow' => $getMimes,
                    'uploadMaxSize' => $uploadMaxSize,
                    'uploadOrder' => [
                        'deny',
                        'allow'
                    ],
                    'accessControl' => function ($attr, $path, $data, $volume, $isDir, $relpath) {

                        $basename = basename($path);
                        $part = substr($basename, 0, 6);

                        if ($part == 'utente' && $isDir && ($attr == 'locked')) {
                            return true;
                        }

                        return $basename[0] === '.'
                        && strlen((string) $relpath) !== 1
                            ? !($attr == 'read' || $attr == 'write')
                            : null;
                    },
                    'attributes' => [
                        [
                            'pattern' => '/\.tmb$/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/\.trash$/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/\.htacces/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/index.html/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        /*[
                            'pattern' => '/utente([0-9])$/',
                            'hidden' => false,
                            'read' => true,
                            'write' => true,
                            'locked' => true
                        ],*/
                        [
                            'pattern' => '/assets$|file_archive$|object_attachs$|imported_attachs$/',
                            'hidden' => false,
                            'read' => true,
                            'write' => true,
                            'locked' => true
                        ],
                        [
                            'pattern' => '/assets(.css$|.images$|.js$|index\.css$)/',
                            'hidden' => false,
                            'read' => true,
                            'write' => true,
                            'locked' => true
                        ],
                        [
                            'pattern' => '/index.css/',
                            'hidden' => false,
                            'read' => true,
                            'write' => true,
                            'locked' => true
                        ]
                    ],
                    'disabled' => 'disabled'
                ],

                [
                    'alias' => 'Cestino',
                    'id' => '1',
                    'driver' => 'Trash',
                    'path' => $path['path_trash'],
                    'URL' => $path['path_trash'],
                    'tmbURL' => $path['thumb_trash'],
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/',
                    'uploadDeny' => [
                        'all'
                    ],
                    'uploadAllow' => $getMimes,
                    'uploadMaxSize' => $uploadMaxSize,
                    'uploadOrder' => [
                        'deny',
                        'allow'
                    ],
                    'accessControl' => 'access',
                    'attributes' => [
                        [
                            'pattern' => '/\.tmb$/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/\.trash$/',
                            'hidden' => true,
                            'read' => true,
                            'write' => false,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/\.htacces/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ],
                        [
                            'pattern' => '/index.html/',
                            'hidden' => true,
                            'read' => true,
                            'write' => true,
                            'locked' => false
                        ]
                    ],
                ],
            ]
        ];

        // run elFinder
        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();

    }

    private function getPath()
    {
        $baseDir = baseUrl('/media/');
        $path = MEDIA_PATH;
        $pathTrash = './media/';
        $urlTrash = $baseDir;
        $thumbTrash = $baseDir;

        // Limito il percorso dei file all'ente corrente.
        // Aggiungo la cartella dell'ente al percorso
        $path = MEDIA_PATH . instituteDir();
        $baseDir .= instituteDir();
        $pathTrash .= instituteDir();
        $urlTrash .= instituteDir();
        $thumbTrash .= instituteDir();

        $getIdentity = authPatOs()->getIdentity();
        $permFileArchive = (int) $getIdentity['options']['file_archive'];

        // Accesso alla cartella dell'ente previsto dal permesso di archivio.
        if ($permFileArchive === 0) {
            $path .= '/';
            $baseDir .= '/';
            $pathTrash .= '/.trash/';
            $urlTrash .= '/.trash/';
            $thumbTrash .= '/.trash/.tmb/';
        }

        // Se l'utente non ha nessun permesso
        if ($permFileArchive === 1) {
            $path = null;
            $baseDir = null;
            $pathTrash = null;
        }

        // Se l'utente ha i permessi solo sui prorpri file
        if ($permFileArchive === 2) {
            $path .= '/file_archive/utente' . $getIdentity['id'] . '/';
            $baseDir .= '/file_archive/utente' . $getIdentity['id'] . '/';
            $pathTrash .= '/file_archive/utente' . $getIdentity['id'] . '/.trash/';
            $urlTrash .= '/file_archive/utente' . $getIdentity['id'] . '/.trash/';
            $thumbTrash .= '/file_archive/utente' . $getIdentity['id'] . '/.trash/.tmb/';
        }

        // Se l'utente ha i permessi su i file di tutti gli utenti
        if ($permFileArchive === 3) {
            $path .= '/file_archive/';
            $baseDir .= '/file_archive/';
            $pathTrash .= '/file_archive/.trash/';
            $urlTrash .= '/file_archive/.trash/';
            $thumbTrash .= '/file_archive/.trash/.tmb/';
        }

        return [
            'path' => $path,
            'base_dir' => $baseDir,
            'path_trash' => $pathTrash,
            'url_trash' => $urlTrash,
            'thumb_trash' => $thumbTrash,
        ];
    }

    private function rootDir()
    {

        baseUrl('/media/');
    }

    /**
     * Restituisce i formati accettati dal file manager.
     * La mappa completa viene riutilizzata dalla pagina dell'archivio per mostrare
     * all'utente MIME ed estensioni senza duplicare la configurazione di elFinder.
     *
     * @param bool $includeExtensions Se true mantiene le estensioni come chiavi della mappa
     * @return array
     */
    public static function getMimes(bool $includeExtensions = false): array
    {
        $mts = [];
        $tmpMimetypes = (array)config('file_manager_allowed_mimes', [], 'perms_files');

        if ($includeExtensions) {
            return $tmpMimetypes;
        }

        foreach ($tmpMimetypes as $k => $v) {
            $mts[] = $v;
        }

        return $mts;
    }

    /**
     * Notifiche operazioni file manager.
     * @param $cmd
     * @param $key
     * @param $result
     * @param $args
     * @param $elfinder
     * @return void
     * @throws Exception
     */
    private function notificationOutcome($cmd = null, $key = null, $result = null, $args = null, $elfinder = null)
    {
        $baseFolder = 'file_archive';

        //Mappatura comandi -> chiave di risultato
        $commandResultMap = [
            'upload'    => 'added',
            'mkdir'     => 'added',
            'mkfile'    => 'added',
            'rename'    => 'added',
            'copy'      => 'added',
            'move'      => 'added',
            'paste'     => 'changed',
            'duplicate' => 'added',
            'rm'        => 'removed',
            'archive'   => 'added',
            'extract'   => 'added'
        ];

        if (!isset($commandResultMap[$cmd])) {
            return;
        }

        if($key != $commandResultMap[$cmd]) {
            return;
        }

        $getIdentity = authPatOs()->getIdentity();
        $session = new Session();

        $azioni = [
            'upload' => 'Caricamento file',
            'mkdir'  => 'Creazione cartella',
            'mkfile' => 'Creazione file vuoto',
            'rename' => 'Rinomina',
            'paste'  => 'Copia/Taglia & Incolla',
            'rm'     => 'Eliminazione',
            'duplicate' => 'Duplicazione',
            'move'   => 'Spostamento',
            'copy'   => 'Copia',
            'archive'=> 'Creazione archivio',
            'extract'=> 'Estrazione da archivio'
        ];
        $azione = $azioni[$cmd] ?? ucfirst($cmd);

        $items = [];

        // Costruisci descrizione dinamica
        $descrizione = '';
        switch ($cmd) {

            case 'upload':
                //Considera soltanto i file, scarta directory perché viene scritto il log di creazione della directory basta quello
                $files = array_filter($result['added'], function($f){
                    return ! (isset($f['mime']) && $f['mime'] === 'directory');
                });

                //Registro il solo upload dei file
                foreach ($files as $file) {
                    $rel = $this->getRelativePath($elfinder, $file['hash'], $baseFolder);
                    $descrizione .= sprintf(
                        'Caricato "%s" in "%s"; ',
                        $file['name'],
                        dirname($rel)
                    );
                }
                break;

            case 'mkdir':
                //Prendo l’hash della cartella appena creata
                $newHash = $result['added'][0]['hash'] ?? null;

                if ($newHash) {
                    //Calcolo il percorso relativo a partire dal nuovo hash
                    $rel = $this->getRelativePath($elfinder, $newHash, $baseFolder);
                } else {
                    //se per qualche motivo non ci fosse $result['added']
                    $parentRel = $this->getRelativePath($elfinder, $args['target'], $baseFolder);
                    $rel = rtrim($parentRel, '/') . '/' . ($args['name'] ?? '');
                }

                //Estraggo il nome della cartella e cartella padre
                $folderName = basename($rel);
                $parentDir  = dirname($rel);

                //Se c'è trash nel nome della cartella o della cartella padre non scrivo il log (scrivo solo quello di eliminazione)
                if (
                    str_contains($parentDir, '.trash') || $parentDir === '.trash' || str_contains($folderName, '.trash')  //destinazioni
                    || $folderName === "Archivio file" //origini
                )  {
                    return;
                }

                //Composizione descrizione del log
                $descrizione = sprintf(
                    'Creata cartella "%s" in "%s"',
                    $folderName,
                    $parentDir === '.' ? '' : $parentDir
                );
                break;


            case 'mkfile':
                $targetDir = $this->getRelativePath($elfinder,$args['target'], $baseFolder);
                $descrizione = sprintf(
                    'Creato file "%s" in "%s"',
                    $args['name'],
                    $targetDir
                );
                break;

            case 'rename':
                // Prendo il vecchio e il nuovo item dal result
                $oldItem = isset($result['removed'][0]) ? $result['removed'][0] : null;
                $newItem = isset($result['added'][0])   ? $result['added'][0]   : null;

                // Estraggo i nomi
                $oldName = $oldItem['name'] ?? '';                // nome prima della rinomina
                $newName = $newItem['name'] ?? $args['name'] ?? ''; // nome dopo

                // Path relativo basato sul nuovo hash (se esiste), altrimenti sul target
                if ($newItem && isset($newItem['hash'])) {
                    $rel = $this->getRelativePath($elfinder, $newItem['hash'], $baseFolder);
                } else {
                    $rel = $this->getRelativePath($elfinder, $args['target'], $baseFolder);
                }

                $items[$azione] = ['old'=>$oldName,'new'=>$newName,'rel'=>$rel];

                // Composizione descrizione del log
                $descrizione = sprintf(
                    'Rinominato "%s" in "%s" (path: %s)',
                    $oldName,
                    $newName,
                    $rel
                );
                break;


            case 'rm':
                //Viene chiamato solo quando elimino nel cestino.
                //L'eliminazione normale effettua il move
                foreach ($result['removed'] as $hash) {

                    $hash = is_array($hash) ? $hash['hash'] : $hash;
                    $path = $this->getRelativePath($elfinder,$hash, $baseFolder);
                    if(empty($path)){
                        $path = "Archivio file/Cestino".$this->_hashDecode($hash);
                    }
                    $descrizione .= sprintf('Rimosso "%s"; ', $path);
                }
                break;

            case 'paste':
                //Determino se è cut (taglia) o copy (copia)
                $isCut  = !empty($args['cut']);
                $azione = $isCut ? 'Spostamento' : 'Copia';

                //Cartella di destinazione
                $dstHash = $args['dst'] ?? $args['target'];
                $dstRel  = $this->getRelativePath($elfinder, $dstHash, $baseFolder);

                // Decido da dove prendere gli hash origine
                if ($isCut && !empty($result['removed'])) {
                    $origItems = $result['removed'];
                } else {
                    $origItems = $args['targets'] ?? [];
                }

                $destinazioni = [];
                $origini = [];

                //Ciclo sugli elementi origine
                foreach ($origItems as $idx => $item) {
                    // estraggo sempre la stringa di hash
                    $origHash = is_array($item) && isset($item['hash'])
                        ? $item['hash']
                        : $item;

                    // calcolo il percorso relativo
                    $origRel = $this->getRelativePath($elfinder, $origHash, $baseFolder);

                    if(empty($origRel)) {
                        //Aggiungo "Archivio file che il contenitore di tutto
                        $trash = str_starts_with($origHash, "t1_") ? "/Cestino" : "";
                        if($trash){
                            $azione = "Ripristino";
                        }
                        $origRel = "Archivio file".$trash. $this->_hashDecode($origHash);
                    }

                    $origini[] = $origRel;

                    // cerco il corrispondente hash in 'added'
                    if (isset($result['added'][$idx]['hash'])) {
                        $newHash = $result['added'][$idx]['hash'];
                        $newRel  = $this->getRelativePath($elfinder, $newHash, $baseFolder);
                    } else {
                        //stesso nome dentro dst
                        $newRel = $dstRel . '/' . basename($origRel);
                    }
                    $destinazioni[] = $newRel;
                }

                if($azione == 'Spostamento'){
                    $items[$azione] = ['ori'=>$origini,'dest'=>$destinazioni];
                }

                $pairs = [];
                foreach ($origini as $i => $o) {

                    if (str_contains($destinazioni[$i], '.trash/') || $destinazioni[$i] === '.trash') {
                        // Descrivo ogni file eliminato
                        $deleted = !empty($args['targets']) ? $args['targets'] : $args['cut'];
                        $pairs = [];
//                        Log::print($args);
                        foreach ($deleted as $item) {
                            // estraggo l’hash se necessario
                            $h = is_array($item) && isset($item['hash']) ? $item['hash'] : $item;
                            $rel = $this->getRelativePath($elfinder, $h, $baseFolder);
                            if(empty($rel)){
                                //Aggiungo "Archivio file che il contenitore di tutto
                                $rel = "Archivio file".$this->_hashDecode($h);
                            }
                            //Descrizione log eliminazione
                            $pairs[] = sprintf('Eliminato "%s"', $rel);
                        }

                        $azione      = 'Eliminazione';

                        break;
                    }

                    $pairs[] = sprintf(
                        '"%s" da "%s" a "%s"',
                        basename($o),
                        $o,
                        $destinazioni[$i]
                    );
                }

                $descrizione = implode('; ', $pairs);

                break;

            case 'archive':
            case 'extract':
                $archiveDetails = $cmd == 'archive' ? " archivio" : "";
                if(isset($result['added']) && !empty($result['added'][0]['hash'])){
                    $filepath = $this->getRelativePath($elfinder,$result['added'][0]['hash'], $baseFolder);
                    $descrizione = sprintf('Creato'.$archiveDetails.' "%s"', $filepath);
                }
                break;

            case 'duplicate':

                if(!empty($result['added'])){
                    foreach ($result['added'] as $key => $duplicated){
                        $file = $this->getRelativePath($elfinder,$duplicated['hash'], $baseFolder);
                        $descrizione .= ($duplicated['mime'] == 'directory' ? "Nuova cartella: " : "Nuovo file: \"").$file."\"<br>";
                        //file di partenza dal quale si duplica
                        //$originFile = $this->getRelativePath($elfinder,$args['targets'][$key], $baseFolder);
                    }
                }

                break;

            default:
                // Generico
                $descrizione = 'Args: '. json_encode($args);
        }

        // Registro l'azione nella tabella Activity LOG.
        if ($azione !== null && $descrizione !== null) {

            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => $azione,
                'description' => $descrizione,
                'object_id' => 45,
            ]);

        }


        // Se la scrittura nel filesystem è abilitata il log, lo scrivo anche sul disco
//        if (config('write_file_manager_filesystem', null, 'app') === true) {
//
//            $log = '[ User ID: ' . $getIdentity['id'] . ' ] ';
//            $log .= '[ User name: ' . checkDecrypt($getIdentity['name']) . ' ] ';
//            $log .= '[ Institution ID: ' . PatOsInstituteId() . ' ] ';
//            $log .= '[ Cmd: ' . @$cmd . ' ] ';
//            $log .= '[ Data: ' . @serialize($result) . ' ] ';
//            $log .= '[ Args: ' . @serialize($args) . ' ] ';
//        }


    }

    /**
     * Create log record
     *
     * @param string   $cmd      command name
     * @param array    $result   command result
     * @param array    $args     command arguments from client
     * @param elFinder $elfinder elFinder instance
     * @param volume   $volume   elFinder current volume instance
     * @return void|true
     * @author Dmitry (dio) Levashov
     **/
    public function log($cmd, $result, $args, $elfinder, $volume)
    {
        $log = $cmd . ' [' . date('d.m.Y H:s') . "]\n";

        if (!empty($result['error'])) {
            $log .= "\tERROR: " . implode(' ', $result['error']) . "\n";
        }

        if (!empty($result['warning'])) {
            $log .= "\tWARNING: " . implode(' ', $result['warning']) . "\n";
        }

        if (!empty($result['removed'])) {
            foreach ($result['removed'] as $file) {
                // removed file contain additional field "realpath"
                $log .= "\tREMOVED: " . $file['realpath'] . "\n";
            }
        }

        if (!empty($result['added'])) {
            foreach ($result['added'] as $file) {
                $log .= "\tADDED: " . $elfinder->realpath($file['hash']) . "\n";
            }
        }

        if (!empty($result['changed'])) {
            foreach ($result['changed'] as $file) {
                $log .= "\tCHANGED: " . $elfinder->realpath($file['hash']) . "\n";
            }
        }

        $this->write($log);
    }

    /**
     * Write log into file
     *
     * @param string $log log record
     * @return void
     * @author Dmitry (dio) Levashov
     **/
    protected function write(string $log): void
    {
        $getIdentity = authPatOs()->getIdentity();

        $path = '.' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'log_elfinder' . DIRECTORY_SEPARATOR . checkAlternativeInstitutionId() . DIRECTORY_SEPARATOR . 'user_' . $getIdentity['id'] . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (($fp = @fopen($path . DIRECTORY_SEPARATOR . date('Y-m-d') . '_logElfinder.txt', 'a'))) {
            fwrite($fp, $log . "\n");
            fclose($fp);
        }
    }

    /**
     * Fix Patch
     * @param $type
     * @param $from
     * @param $to
     * @return void
     */
    private function addTrackData($type = null, $from = null, $to = null)
    {
        $getIdentity = authPatOs()->getIdentity();
        $institutionId = PatOsInstituteId();
    }

    /**
     * Decodifica l'hash del percorso del file modificato/cancellato
     */
    private function _hashDecode($hash)
    {
        // Trova la posizione del carattere '_'
        $pos = strpos($hash, '_');
        // Se non viene trovato '_', prende l'intera stringa, altrimenti la parte successiva al carattere '_'
        if ($pos === false) {
            $encPath = $hash;
        } else {
            $encPath = substr($hash, $pos + 1);
        }
        // Sostituisce i caratteri: '-' diventa '+', '_' diventa '/' e '.' diventa '='
        $encPath = str_replace(
            array('-', '_', '.'),
            array('+', '/', '='),
            $encPath
        );
        // Decodifica la stringa Base64 e aggiunge uno slash iniziale
        return '/' . base64_decode($encPath);
    }

    /**
     * Restituisce il percorso “relativo” a partire da una cartella specifica.
     *
     * @param elFinder $elfinder   Istanza di elFinder, per chiamare realpath()
     * @param string   $hash       Hash dell’item di cui vogliamo il
     *                             path
     * @param string   $baseFolder Nome della cartella da cui far partire il path (es. "media")
     * @return string  Percorso relativo (es. "media/subdir/file.txt") o path completo se la cartella non c’è
     */
    function getRelativePath(elFinder $elfinder, string $hash, string $baseFolder): string
    {
        $fullPath = $elfinder->realpath($hash);
        // cercare “/media/” (o "\media\" su Windows)
        $needle = DIRECTORY_SEPARATOR . $baseFolder . DIRECTORY_SEPARATOR;
        $pos    = strpos($fullPath, $needle);

        if ($pos !== false) {
            // +1 per rimuovere il leading DIRECTORY_SEPARATOR
            $rel = substr($fullPath, $pos + 1);
        } else {
            $rel = $fullPath;
        }


        // se non trova la cartella base, restituisce il path completo
        $rel =  str_replace($baseFolder, "Archivio file", $rel);
        return str_replace(['\\', DIRECTORY_SEPARATOR], '/', $rel);
    }
}
