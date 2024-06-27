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
    && strlen((string)$relpath) !== 1
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

        $getMimes = $this->getMimes();
        $path = $this->getPath();

        $opts = [
            //'debug' => true,
            'root_options' => array(
                'defaults' => array('read' => true, 'write' => true, 'locked' => true, 'remove' => true),
            ),
            'bind' => array(
                'mkdir mkfile rename duplicate upload rm paste' => function ($cmd, $result, $args, $elfinder) {

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

                                        $filepath = (isset($file['realpath']) ? $file['realpath'] : $elfinder->realpath($file['hash']));
                                        array_push($data, $filepath);

                                    }

                                }

                            } else {

                                array_push($data, $value);

                            }

                        }

                        $this->notificationOutcome($cmd, $key, $data, $args, $elfinder);

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
                        && strlen((string)$relpath) !== 1
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

        // Se l'utente non è super admin o se lo è ma sta gestendo un determinato ente
        if (!isSuperAdmin() || checkAlternativeInstitutionId() !== 0) {

            // Aggiungo la cartella dell'ente al percorso
            $path = MEDIA_PATH . instituteDir();
            $baseDir .= instituteDir();
            $pathTrash .= instituteDir();
            $urlTrash .= instituteDir();
            $thumbTrash .= instituteDir();

            $getIdentity = authPatOs()->getIdentity();
            $permFileArchive = (int)$getIdentity['options']['file_archive'];

            // Se l'utente è super amdin
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

        } else { // Se l'utente è super admin e sta gestendo tutti gli enti
            $path .= '/';
            $baseDir .= '/';
            $pathTrash .= '/.trash/';
            $urlTrash .= '/.trash/';
            $thumbTrash .= '/.trash/.tmb/';
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

    private function getMimes()
    {
        $mts = [];
        $tmpMimetypes = [
            'ai' => 'application/postscript',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pdf' => 'application/pdf',
            'p7m' => 'application/pkcs7-mime',
            'xml' => 'application/xml',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ott' => 'application/vnd.oasis.opendocument.text-template',
            'oth' => 'application/vnd.oasis.opendocument.text-web',
            'odm' => 'application/vnd.oasis.opendocument.text-master',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'otg' => 'application/vnd.oasis.opendocument.graphics-template',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'otp' => 'application/vnd.oasis.opendocument.presentation-template',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odi' => 'application/vnd.oasis.opendocument.image',
            'oxt' => 'application/vnd.openofficeorg.extension',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
            'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
            'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
            'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
            'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
            'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
            'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
            'gz' => 'application/x-gzip',
            'bz' => 'application/x-bzip2',
            'xz' => 'application/x-xz',
            'zip' => 'application/zip',
            'zip2' => 'application/x-zip-compressed',
            'rtf2' => 'application/rtf',
            'rar' => 'application/x-rar',
            'tar' => 'application/x-tar',
            '7z' => 'application/x-7z-compressed',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'rtf' => 'text/rtf',
            'log' => 'text/plain',
            'html' => 'text/html',
            'bmp' => 'image/x-ms-bmp',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'tif' => 'image/tiff',
            'tga' => 'image/x-targa',
            'psd' => 'image/vnd.adobe.photoshop',
            'xbm' => 'image/xbm',
            'pxm' => 'image/pxm'
        ];

        foreach ($tmpMimetypes as $k => $v) {
            $mts[] = $v;
        }

        return $mts;
    }

    /**
     * Notifiche operazioni file manager.
     * @param $cmd
     * @param $key
     * @param $otherData
     * @param $args
     * @param $elfinder
     * @return void
     * @throws Exception
     */
    private function notificationOutcome($cmd = null, $key = null, $otherData = null, $args = null, $elfinder = null)
    {
        $action = null;
        $description = null;
        $operation = null;
        $naming = false;
        $idDir = false;
        $isFile = false;

        $getIdentity = authPatOs()->getIdentity();
        $session = new Session();
        $getTempCmd = $session->getTemp($cmd . '_' . $getIdentity['id']);

        if (empty($getTempCmd) || (!empty($getTempCmd['ts']) && $getTempCmd['ts'] < time())) {

            $session->setTemp($cmd . '_' . $getIdentity['id'], [
                'user_id' => $getIdentity['id'],
                'ts' => !empty($value['ts']) ? $value['ts'] : null,
                'name' => !empty($value['name']) ? $value['name'] : null,
            ], 1);

            if (!empty($otherData[0])) {

                // Un file?
                if (File::exists($otherData[0])) {

                    $idDir = true;
                    $naming = 'file';
                    $operation = 'un ' . $naming;

                }

                // Un Cartella ?
                if (Dir::exists($otherData[0])) {

                    $isFile = true;
                    $naming = 'cartella';
                    $operation = 'una ' . $naming;
                }

            }

            // Creazione di una nuoca cartella
            if ($cmd == 'mkdir' && $key == 'added') {

                $action = 'Creazione cartella "' . $args['name'] . '" nella gestione degli allegati';
                $description = 'L\'utente ' . $getIdentity['name'] . ' ha creato una nuova cartella denominata "' . $args['name'] . '" nella gestione degli allegati';

            }

            // Rinomina un file
            if ($cmd == 'rename' && $key == 'added') {

                $action = 'Rinominato ' . $operation . ' denominato "' . $args['name'] . '" nella gestione degli allegati';
                $description = 'L\'utente ' . $getIdentity['name'] . ' ha rinominato ' . $operation . ' denominata " ' . $args['name'] . '" nella gestione degli allegati';

            }

            // Spostamento di un file
            if ($cmd == 'paste' && $key == 'changed') {

                $action = 'Spostato ' . $operation . ' denominato "' . $args['name'] . '" nella gestione degli allegati';
                $description = 'L\'utente ' . $getIdentity['name'] . ' ha spostato ' . $operation . ' denominata " ' . $args['name'] . '" nella gestione degli allegati';

            }

            // Aggiunta di un file
            if ($cmd == 'upload' && $key == 'added') {

                $action = 'Caricato ' . $operation . ' denominato "' . $args['name'] . '" nella gestione degli allegati';
                $description = 'L\'utente ' . $getIdentity['name'] . ' ha caricato ' . $operation . ' denominata " ' . $args['name'] . '" nella gestione degli allegati';

            }

            // Rimozione di un file
            if ($cmd == 'upload' && $key == 'removed') {

                $action = 'Eliminato ' . $operation . ' denominato "' . $args['name'] . '" nella gestione degli allegati';
                $description = 'L\'utente ' . $getIdentity['name'] . ' ha eliminato ' . $operation . ' denominata " ' . $args['name'] . '" nella gestione degli allegati';

                $this->addTrackData($key, $args['name']);
            }
        }

        // Registro l'azione nella tabella Activity LOG.
        if ($action !== null && $description !== null && $operation !== null) {

            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => $action,
                'description' => $description,
                'request_post' => [
                    'cmd' => @$cmd,
                    'data' => @$otherData,
                    'args' => @$args,
                ]
            ]);

        }

        // Se la scrittura nel filesystem è abilitata il log, lo scrivo anche sul disco
        if (config('write_file_manager_filesystem', null, 'app') === true) {

            $log = '[ User ID: ' . $getIdentity['id'] . ' ] ';
            $log .= '[ User name: ' . $getIdentity['name'] . ' ] ';
            $log .= '[ Institution ID: ' . PatOsInstituteId() . ' ] ';
            $log .= '[ Cmd: ' . @$cmd . ' ] ';
            $log .= '[ Data: ' . @serialize($otherData) . ' ] ';
            $log .= '[ Args: ' . @serialize($args) . ' ] ';
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
}
