<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use System\Input;

class FileArchiveAdminController extends BaseAuthController
{

    private bool $hasPerms = true;

    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('not_acl');

        if (!isSuperAdmin()) {

            $getIdentity = authPatOs()->getIdentity();
            $permFileArchive = (int)$getIdentity['options']['file_archive'];

            if ($permFileArchive === 1) {

                $this->hasPerms = false;

            }

        }

    }

    /**
     * @throws Exception
     * @url /admin/file-archive.html
     * @method GET
     * @return void
     */
    public function index(): void
    {
        //Setto il metodo della rotta
        //$this->acl->setRoute('read');


        $baseDir = baseUrl('/media/');
        $path = MEDIA_PATH;

        if (!isSuperAdmin() || checkAlternativeInstitutionId() !== 0) {

            $path = MEDIA_PATH . instituteDir();
            $baseDir .= instituteDir();

            $getIdentity = authPatOs()->getIdentity();
            $permFileArchive = (int)$getIdentity['options']['file_archive'];

            if ($permFileArchive === 1) {
                $path = null;
                $baseDir = null;
            }

            if ($permFileArchive === 2) {
                $path .= '/file_archive/utente' . $getIdentity['id'];
                $baseDir .= '/file_archive/utente' . $getIdentity['id'];
            }

            if ($permFileArchive === 3) {
                $path .= '/file_archive/';
                $baseDir .= '/file_archive/';
            }

        }

        $this->breadcrumb->push('Archivio file', '/');
        $data = [];

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Allegati';
        $data['subTitleSection'] = 'GESTIONE DEI FILE ALLEGATI PER I CONTENUTI DELLE PAGINE';
        $data['sectionIcon'] = '<i class="far fa-edit fa-3x"></i>';

        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['formAction'] = '/admin/file-archive';
        $data['formSettings'] = [
            'name' => 'form_file-archive',
            'id' => 'form_file-archive',
            'class' => 'form_file-archive',
        ];
        $data['perms'] = $this->hasPerms;

        $view = (Input::get('f') == 1) ? 'file_archive/elfinder' : 'file_archive/index';

        render($view, $data, 'admin');

    }

    /**
     * @throws Exception
     * @url /admin/file-manager.html
     * @method GET
     * @return void
     */
    public function openModal(): void
    {
        //Setto il metodo della rotta
        //$this->acl->setRoute('read');

        render('file_archive/elfinder', [], 'admin');
    }

    /**
     * @throws Exception
     * @url /admin/file-archive/create.html
     * @method GET
     * @return void
     */
    public function create(): void
    {
        //Setto il metodo della rotta
        //$this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('Archivio file', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Archivio file';
        render('file-archive/file-archive', $data, 'admin');
    }
}
