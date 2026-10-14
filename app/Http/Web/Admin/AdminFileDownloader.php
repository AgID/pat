<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\FileSystem\File;
use Model\AttachmentsModel;
use System\Registry;
use System\Validator;

class AdminFileDownloader extends BaseAuthController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('not_acl');
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
            ->value(uri()->segment(4, '-?^'/* @Hack */))
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
                        ->first();

                    if (!empty($query)) {

                        $result = $query->toArray();

                        $filePath = MEDIA_PATH . instituteDir() . DIRECTORY_SEPARATOR . 'object_attachs' . DIRECTORY_SEPARATOR . $result['archive_name'] . DIRECTORY_SEPARATOR . $result['file_name'];

                        if (!File::exists($filePath)) {

                            return ['error' => 1];

                        } else {

                            Registry::set('__back_office_record_download__', [
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

        $result = Registry::get('__back_office_record_download__');

        $robotsIndex = (bool)$result['data']['indexable'];

            downloader(
                $result['file_path'],
                null,
                true,
                $result['data']['client_name'],
                $robotsIndex, // File indicizzabile ?
                true
            );
    }
}
