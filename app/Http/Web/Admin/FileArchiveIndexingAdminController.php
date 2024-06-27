<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class FileArchiveIndexingAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/file-archive-indexing.html
     * @method GET
     */
    public function index(): void
    {
        $this->breadcrumb->push('Archivio file - indicizzazione', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Esclusioni indicizzazione allegati';
        $data['formAction'] = '/admin/file-archive-indexing';
        $data['formSettings'] = [
            'name' => 'form_file-archive-indexing',
            'id' => 'form_file-archive-indexing',
            'class' => 'form_file-archive-indexing',
        ];

        render('file_archive_indexing/index', $data, 'admin');
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/file-archive-indexing/create.html
     * @method GET
     */
    public function create(): void
    {
        $this->breadcrumb->push('Archivio file - indicizzazione', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Archivio file - indicizzazione';
        render('file-archive-indexing/file-archive-indexing', $data, 'admin');
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/file-archive-indexing/edit.html
     * @method GET
     */
    public function edit(): void
    {
        $this->breadcrumb->push('Archivio file - indicizzazione', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Archivio file - indicizzazione';
        render('file-archive-indexing/file-archive-indexing', $data, 'admin');
    }
}
