<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * @description
 *
 * @param $file
 * @return array|null
 */

class getInfoFile
{
    private $fileUrl = null;
    private $fileInfo = array(
        'file_name',
        'file_type',
        'file_path',
        'full_path',
        'raw_name',
        'orig_name',
        'client_name',
        'file_ext',
        'file_size',
        'is_image',
        'image_width',
        'image_height',
        'image_type',
        'image_size_str'
    );

    public $imageType = [
        0 => 'UNKNOWN',
        1 => 'GIF',
        2 => 'JPEG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF_II',
        8 => 'TIFF_MM',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM',
        17 => 'ICO',
        18 => 'COUNT'
    ];

    public function __construct($file = null)
    {
        $data = null;

        if ($file != null && file_exists($file)) {

            $mimeContentType = mime_content_type($file);
            $contentTypeIsImage = $this->isImage($mimeContentType);
            $fileName = basename($file);
            $imageWidth = null;
            $imageHeight = null;
            $imageType = null;
            $imageSizeString = null;
            $ext = $this->getExtension($file);
            $fullPath = str_replace('\\', '/', realpath($file));
            $rawName = substr($fileName, 0, -strlen($ext));

            if ($contentTypeIsImage['is_image']) {

                $imageSize = getimagesize($file);
                $imageWidth = $imageSize[0];
                $imageHeight = $imageSize[1];
                $imageType = strtolower($this->imageType[$imageSize[2]]);
                $imageSizeString = $imageSize[3];
            }

            $data['file_name'] = $fileName;
            $data['file_type'] = $contentTypeIsImage['file_type'];
            $data['file_path'] = str_replace($rawName . $ext, '', $fullPath);
            $data['full_path'] = $fullPath;
            $data['raw_name'] = $rawName;
            $data['orig_name'] = $fileName;
            $data['client_name'] = $fileName;
            $data['file_ext'] = $ext;
            $data['file_size'] = filesize($file);
            $data['is_image'] = $contentTypeIsImage['is_image'];
            $data['image_width'] = $imageWidth;
            $data['image_height'] = $imageHeight;
            $data['image_type'] = $imageType;
            $data['image_size_str'] = $imageSizeString;

            $this->fileUrl = $file;
            $this->fileInfo = $data;
        }

    }

    public function getUrlFile() {
        return $this->fileUrl;
    }

    public function getInfoFile() {
        return $this->fileInfo;
    }

    public function getExtension($filename)
    {
        $x = explode('.', $filename);

        if (count($x) === 1) {
            return null;
        }
        return '.' . strtolower(end($x));
    }

    public function isImage($filetype)
    {

        $pngMimes = [
            'image/x-png',
        ];

        $jpegMimes = [
            'image/jpg',
            'image/jpe',
            'image/jpeg',
            'image/pjpeg',
        ];

        if (in_array($filetype, $pngMimes)) {

            $filetype = 'image/png';
        } elseif (in_array($filetype, $jpegMimes)) {

            $filetype = 'image/jpeg';
        }

        $imgMimes = [
            'image/gif',
            'image/jpeg', 'image/png'
        ];

        return [
            'file_type' => $filetype,
            'is_image' => (bool)in_array($filetype, $imgMimes, TRUE),
        ];
    }

    public function fileInfo($file, $returnedValues = ['name', 'server_path', 'size', 'date'])
    {
        if (!file_exists($file)) {
            return FALSE;
        }

        if (is_string($returnedValues)) {
            $returnedValues = explode(',', $returnedValues);
        }

        foreach ($returnedValues as $key) {

            switch ($key) {
                case 'name':
                    $fileinfo['name'] = basename($file);
                    break;
                case 'server_path':
                    $fileinfo['server_path'] = $file;
                    break;
                case 'size':
                    $fileinfo['size'] = filesize($file);
                    break;
                case 'date':
                    $fileinfo['date'] = filemtime($file);
                    break;
                case 'fileperms':
                    $fileinfo['fileperms'] = fileperms($file);
                    break;
            }
        }

        return $fileinfo;
    }
}