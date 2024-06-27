<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\FileSystem\File;
use Model\AttachmentsModel;
use System\Registry;
use System\Validator;

class FileDownloader extends BaseFrontController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Funzione per il download degli allegati in front-office
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $active = true;
        $validator = new Validator();
        //$validator->verifyToken();
        $validator->label('Il parametro di ingresso non è valido')
            ->value(uri()->segment(2, '-?^'/* @Hack */))
            ->required()
            ->isAlphaNum()
            ->add(function () {

                $arguments = func_get_args();
                $id = $arguments[1];

                if ($id == 0) {

                    return ['error' => 1];

                } else {

                    $query = AttachmentsModel::whereNull('deleted_at')
                        ->where('id', $id)
                        ->where('active', 1)
                        ->first();

                    if (!empty($query)) {

                        $result = $query->toArray();

                        $filePath = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $result['archive_name'] . DIRECTORY_SEPARATOR . $result['file_name'];

                        if (!File::exists($filePath)) {

                            return ['error' => 1];

                        } else {

                            Registry::set('__front_office_record_download__', [
                                'data' => $result,
                                'file_path' => $filePath
                            ]);

                        }

                    } else {

                        return ['error' => 1];

                    }

                }

                return null;

            }, 'parametro non valido')
            ->end();


        if (!$validator->isSuccess()) {

            echo showError('ATTENZIONE', 'File non presente');
            die();

        }

        $result = Registry::get('__front_office_record_download__');

        $robotsIndex = (bool)$result['data']['indexable'];

        //Modello dell'elemento a cui è associato l'allegato
        $archive = config($result['data']['archive_name'], null, 'archiveConfig') ?? null;

        $archive = $archive['model'];

        //Se l'allegato è pubblico lo mostro/scarico
        if ($active) {
            // Avvio il download del file...
            downloader(
                $result['file_path'],
                null,
                true,
                $result['data']['client_name'],
                $robotsIndex, // File indicizzabile ?
                true
            );
        } else {
            header("X-Robots-Tag: noindex, nofollow");
            echo showError('ATTENZIONE', 'File non presente');
            die();
        }
    }
}
