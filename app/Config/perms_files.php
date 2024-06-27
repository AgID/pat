<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [
    'upload_path' => './media/'. instituteDir() . '/object_attachs/',
    'set_dir_institution_upload_file' => true,
    'file_ext_tolower' => true,
    'encrypt_name' => true,
    'remove_spaces' => true,
    'max_size' => 0, /* 0=infinito */
    'allowed_types' => 'p7m|csv|psd|pdf|ai|eps|xls|ppt|pptx|wbxml|wmlc|dvi|gtar|gz|gzip|tar|tgz|z|zip|xht|rar|mid|midi|mpga|mp2|mp3|aif|aiff|aifc|ram|rm|rpm|ra|rv|wav|bmp|gif|jpeg|jpg|jpe|jp2|j2k|jpf|jpg2|jpx|jpm|mj2|mjp2|png|tiff|tif|heic|heif|css|js|txt|text|rtx|rtf|xml|xsl|mpeg|mpg|mpe|qt|mov|avi|movie|doc|docx|dot|dotx|xlsx|word|xl|eml|json|pem|p10|p12|p7a|p7c|p7m|p7r|p7s|crt|crl|der|kdb|pgp|gpg|sst|csr|rsa|cer|3g2|3gp|mp4|m4a|f4v|flv|webm|aac|m4u|m3u|xspf|vlc|wmv|au|ac3|flac|ogg|kmz|kml|ics|ical|7z|7zip|cdr|wma|svg|vcf|srt|vtt|ico|odc|otc|odf|otf|odg|otg|odi|oti|odp|otp|ods|ots|odt|odm|ott|oth'
];
