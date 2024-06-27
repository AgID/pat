<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed');

return [

    // File upload
    'upload_userfile_not_set' => 'Unable to find a post variable called userfile.',
    'upload_file_exceeds_limit' => 'The uploaded file exceeds the maximum allowed size in your PHP configuration file.',
    'upload_file_exceeds_form_limit' => 'The uploaded file exceeds the maximum size allowed by the submission form.',
    'upload_file_partial' => 'The file was only partially uploaded.',
    'upload_no_temp_directory' => 'The temporary folder is missing.',
    'upload_unable_to_write_file' => 'The file could not be written to disk.',
    'upload_stopped_by_extension' => 'The file upload was stopped by extension.',
    'upload_no_file_selected' => 'You did not select a file to upload.',
    'upload_invalid_filetype' => 'The filetype you are attempting to upload is not allowed.',
    'upload_invalid_filesize' => 'The file you are attempting to upload is larger than the permitted size.',
    'upload_invalid_dimensions' => 'The image you are attempting to upload doesn\'t fit into the allowed dimensions.',
    'upload_destination_error' => 'A problem was encountered while attempting to move the uploaded file to the final destination.',
    'upload_no_filepath' => 'The upload path does not appear to be valid.',
    'upload_no_file_types' => 'You have not specified any allowed file types.',
    'upload_bad_filename' => 'The file name you submitted already exists on the server.',
    'upload_not_writable' => 'The upload destination folder does not appear to be writable.',


    //------------------------------------------------------------------------------------------------------------------


    // Send Email
    'email_must_be_array' => 'The email validation method must be passed an array.',
    'email_invalid_address' => 'Invalid email address: %s',
    'email_attachment_missing' => 'Unable to locate the following email attachment: %s',
    'email_attachment_unreadable' => 'Unable to open this attachment: %s',
    'email_no_from' => 'Cannot send mail with no "From" header.',
    'email_no_recipients' => 'You must include recipients: To, Cc, or Bcc',
    'email_send_failure_phpmail' => 'Unable to send email using PHP mail(). Your server might not be configured to send mail using this method.',
    'email_send_failure_sendmail' => 'Unable to send email using PHP Sendmail. Your server might not be configured to send mail using this method.',
    'email_send_failure_smtp' => 'Unable to send email using PHP SMTP. Your server might not be configured to send mail using this method.',
    'email_sent' => 'Your message has been successfully sent using the following protocol: %s',
    'email_no_socket' => 'Unable to open a socket to Sendmail. Please check settings.',
    'email_no_hostname' => 'You did not specify a SMTP hostname.',
    'email_smtp_error' => 'The following SMTP error was encountered: %s',
    'email_no_smtp_unpw' => 'Error: You must assign a SMTP username and password.',
    'email_failed_smtp_login' => 'Failed to send AUTH LOGIN command. Error: %s',
    'email_smtp_auth_un' => 'Failed to authenticate username. Error: %s',
    'email_smtp_auth_pw' => 'Failed to authenticate password. Error: %s',
    'email_smtp_data_failure' => 'Unable to send data: %s',
    'email_exit_status' => 'Exit status code: %s',


    //------------------------------------------------------------------------------------------------------------------


    // FTP
    'ftp_no_connection' => 'Unable to locate a valid connection ID. Please make sure you are connected before performing any file routines.',
    'ftp_unable_to_connect' => 'Unable to connect to your FTP server using the supplied hostname.',
    'ftp_unable_to_login' => 'Unable to login to your FTP server. Please check your username and password.',
    'ftp_unable_to_mkdir' => 'Unable to create the directory you have specified.',
    'ftp_unable_to_changedir' => 'Unable to change directories.',
    'ftp_unable_to_chmod' => 'Unable to set file permissions. Please check your path.',
    'ftp_unable_to_upload' => 'Unable to upload the specified file. Please check your path.',
    'ftp_unable_to_download' => 'Unable to download the specified file. Please check your path.',
    'ftp_no_source_file' => 'Unable to locate the source file. Please check your path.',
    'ftp_unable_to_rename' => 'Unable to rename the file.',
    'ftp_unable_to_delete' => 'Unable to delete the file.',
    'ftp_unable_to_move' => 'Unable to move the file. Please make sure the destination directory exists.',
];
