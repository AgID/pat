<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Log;

defined('_FRAMEWORK_') or exit('No direct script access allowed');
/**
 * @package     Pat OS
 * @author      ISWEB S.p.A
 * @copyright   Copyright (c) 2021, ISWEB S.p.A
 * @since       Version 1.0
 * @filesource
 */

if (!function_exists('checkSecurityPassword')) {

    /**
     * Funzione per il controllo sicurezza sulle password
     *
     * @param string $password Password da controllare
     * @param int $minLength Lunghezza minima che la password deve avere
     * @param int $maxLength Lunghezza massima che la password deve avere
     * @return bool
     */
    function checkSecurityPassword(string $password, int $minLength = 14, int $maxLength = 32): bool
    {
        $regex = '/^\S*(?=\S{' . $minLength . ',' . $maxLength . '})(?=(?:.*[!(@#$%*-]){1,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/';
        return (bool)preg_match($regex, $password);
    }
}

if (!function_exists('createDirByUserId')) {

    /**
     * Funzione per creare la directory dei medi associata all'utente.
     * Utilizzata quando viene creato un nuovo utente
     *
     * @param $userId {ID dell'utente}
     * @return void
     * @throws Exception
     */
    function
    createDirByUserId($userId): void
    {
        // Creo la directory associata all'utente per l'upload dei file
        $folderNameByUser = MEDIA_PATH . instituteDir() . '/file_archive/' . config('prefix_user_dir', null, 'app') . $userId;

        // Creo i file al fine di evitare accessi diretti e nella cartella.
        if (!Helpers\FileSystem\Dir::exists($folderNameByUser)) {

            $isDir = Helpers\FileSystem\Dir::create($folderNameByUser);

            if ($isDir === true) {

                // Cartelle per il file manager
                Helpers\FileSystem\Dir::create($folderNameByUser . '/.trash');
                Helpers\FileSystem\Dir::create($folderNameByUser . '/.trash/.tmb');

                $htmlContent = config('app', null, 'tpl_index_html');
                $htaccessContent = config('app', null, 'tpl_htacces');

                $hasIndexFile = Helpers\FileSystem\File::writeFile(
                    $folderNameByUser . '/index.html',
                    $htmlContent
                );

                $hasHtaccessFile = Helpers\FileSystem\File::writeFile(
                    $folderNameByUser . '/.htaccess',
                    $htaccessContent
                );

                if (!$hasIndexFile) {

                    Log::danger('Attenzione: non è stato possibile creare il file "index.html" per l\'utente con ID : ' . $userId);

                }

                if (!$hasHtaccessFile) {

                    Log::danger('Attenzione: non è stato possibile creare il file ".htaccess" per l\'utente con ID : ' . $userId);

                }
            }

        } else {

            Log::danger('Attenzione: non è stato possibile creare la cartella per l\'utente con ID : ' . $userId);

        }
    }
}

if (!function_exists('deleteUserFolder')) {
    /**
     * @description Funzione per l'eliminazione della cartella di un utente
     * @param int $userId Id dell'utente di cui eliminare la cartella
     * @return void
     * @throws Exception
     */
    function deleteUserFolder(int $userId): void
    {
        // Percorso della directory dell'utente da eliminare
        $folderNameByUser = MEDIA_PATH . instituteDir() . '/file_archive/' . config('prefix_user_dir', null, 'app') . $userId;

        //Controllo se la cartella esiste prima di eliminarla
        if (Helpers\FileSystem\Dir::exists($folderNameByUser)) {
            Helpers\FileSystem\Dir::delete($folderNameByUser);
        }
    }
}
