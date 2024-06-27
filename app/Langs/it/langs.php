<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    // File upload
    'upload_userfile_not_set' => 'Impossibile trovare nel post una variabile chiamata userfile.',
    'upload_file_exceeds_limit' => 'Il file supera le dimensioni massime consentite nel file di configurazione del PHP.',
    'upload_file_exceeds_form_limit' => 'Il file supera le dimensioni massime consentite dall\'invio del form.',
    'upload_file_partial' => 'Il file è stato ricevuto solo parzialmente.',
    'upload_no_temp_directory' => 'La cartella temporanea è mancante.',
    'upload_unable_to_write_file' => 'Il file non può essere scritto su disco.',
    'upload_stopped_by_extension' => 'L\'upload del file è stato interrotto dall\'estensione.',
    'upload_no_file_selected' => 'Non è stato selezionato nessun file da caricare.',
    'upload_invalid_filetype' => 'Il tipo di file che si sta cercando di caricare non è consentito.',
    'upload_invalid_filesize' => 'La dimensione del file che si sta cercando di caricare supera il limite consentito.',
    'upload_invalid_dimensions' => 'L\'immagine che si sta cercando di caricare supera il limite massimo di altezza e larghezza.',
    'upload_destination_error' => 'Si è verificato un problema durante il tentativo di spostamento del file alla sua destinazione finale.',
    'upload_no_filepath' => 'Il percorso di upload non è valido.',
    'upload_no_file_types' => 'Non sono stati specificati i tipi di file permessi.',
    'upload_bad_filename' => 'Il nome del file che è stato inviato è già presente sul server.',
    'upload_not_writable' => 'La cartella di upload non è scrivibile.',
    'upload_not_reading_file' => 'Errore durante la lettura del file temporaneo.',
    'upload_not_lock_file_destination' => 'Impossibile bloccare il file di destinazione.',


    //------------------------------------------------------------------------------------------------------------------


    // Send Email
    'email_must_be_array' => 'Il metodo di validazione delle email deve essere inviato come array.',
    'email_invalid_address' => 'Indirizzo email non valido: %s',
    'email_attachment_missing' => 'Impossibile trovare il seguente allegato dell\'email: %s',
    'email_attachment_unreadable' => 'Impossibile aprire il seguente alleato: %s',
    'email_no_from' => 'Impossibile inviare l\'email senza il campo header "Da".',
    'email_no_recipients' => 'E\' necessario includere le informazioni: A, Cc, or Ccn',
    'email_send_failure_phpmail' => 'Impossibile inviare una mail utilizzando la funzione PHP mail(). Il server sembra non essere configurato per inviare mail utilizzando questo metodo.',
    'email_send_failure_sendmail' => 'Impossibile inviare una mail utilizzando la funzione Sendmail(). Il server sembra non essere configurato per inviare mail utilizzando questo metodo.',
    'email_send_failure_smtp' => 'Impossibile inviare una mail utilizzando la funzione PHP SMTP. Il server sembra non essere configurato per inviare mail utilizzando questo metodo.',
    'email_sent' => 'Il tuo messaggio è stato inviato con successo utilizzando il seguente protocollo: %s',
    'email_no_socket' => 'Impossibile aprire un socket con Sendmail. Controllare i settaggi.',
    'email_no_hostname' => 'Non è stato specificato un hostname SMTP.',
    'email_smtp_error' => 'E\' stato riscontrato il seguente errore SMTP: %s',
    'email_no_smtp_unpw' => 'Errore: occorre assegnare un SMTP username e password.',
    'email_failed_smtp_login' => 'Invio del comando AUTH LOGIN fallito. Errore: %s',
    'email_smtp_auth_un' => 'Autenticazione dell\'username fallita. Errore: %s',
    'email_smtp_auth_pw' => 'Autenticazione della password fallita. Errore: %s',
    'email_smtp_data_failure' => 'Impossibile inviare i dati: %s',
    'email_exit_status' => 'Codice di status di uscita: %s',


    //------------------------------------------------------------------------------------------------------------------


    // FTP
    'ftp_no_connection' => 'Impossibile trovare un ID di connessione valido. Assicurati di essere connesso prima di eseguire una qualsiasi operazione sui files.',
    'ftp_unable_to_connect' => 'Impossibile connettersi al server FTP utilizzando l\'hostname indicato.',
    'ftp_unable_to_login' => 'Impossibile effettuare il login al server FTP. Controlla che username e password siano corretti.',
    'ftp_unable_to_mkdir' => 'Impossibile creare la directory specificata.',
    'ftp_unable_to_changedir' => 'Impossibile cambiare directory.',
    'ftp_unable_to_chmod' => 'Impossibile impostare i permessi del file. Controlla che il percorso del file sia corretto.',
    'ftp_unable_to_upload' => 'Impossibile eseguire l\'upload del file specificato. Controlla che il percorso del file sia corretto.',
    'ftp_unable_to_download' => 'Impossibile eseguire il download del file specificato. Controlla che il percorso del file sia corretto.',
    'ftp_no_source_file' => 'Impossibile localizzare il file sorgente. Controlla che il percorso del file sia corretto',
    'ftp_unable_to_rename' => 'Impossibile rinominare il file.',
    'ftp_unable_to_delete' => 'Impossibile cancellare il file.',
    'ftp_unable_to_move' => 'Impossibile spostare il file. Assicurati che la directory di destinazione esista.',


    //------------------------------------------------------------------------------------------------------------------


    // Validator
    'validator_required' => 'Campo "%s" obbligatorio.',
    'validator_allowed' => 'Il tipo di file "%s" che si sta tendando di caricare non è consentito.',
    'validator_invalid_max_filesize' => 'La dimensione del file "%s" che si sta cercando di caricare supera il limite massimo consentito. Dimesione massima %s.',
    'validator_invalid_min_filesize' => 'La dimensione del file "%s" che si sta cercando di caricare non supera il limite minimo consentito. Dimesione minima %s.',
    'validator_invalid_dimensions' => 'L\'immagine che si sta cercando di caricare supera il limite massimo di altezza e larghezza.',
    'validator_min' => 'Valore campo "%s" inferiore al valore minimo.',
    'validator_max' => 'Valore campo "%s" superiore al valore massimo.',
    'validator_exact' => 'La lunghezza del campo "%s" deve essere pari a "%s.',
    'validator_natural' => 'Il campo "%s" deve contenere un numero.',
    'validator_natural_no_zero' => 'Il campo "%s" deve contenere un numero maggiore di zero.',
    'validator_in_list' => 'Il campo "%s" non è valido.',
    'validator_not_in_list' => 'Il campo "%s" non può contenere il seguente valore %s.',
    'validator_integer' => 'Il campo "%s" può contenere solo un numero intero.',
    'validator_decimal' => 'Il campo "%s" deve contenere un numero decimale.',
    'validator_alpha' => 'Il campo "%s" può contenere solo caratteri alfabetici.',
    'validator_alpha_numeric' => 'Il campo "%s" può contenere solo caratteri alfa-numerici.',
    'validator_url' => 'Il campo "%s" deve contenere un URL valido.',
    'validator_uri' => 'Il campo "%s" deve contenere un URI valido.',
    'validator_bool' => 'Il campo "%s" deve contenere un valore vero o falso.',
    'validator_email' => 'Il campo "%s" deve contenere un indirizzo email valido.',
    'validator_date' => 'La data nel campo "%s" non è valida.',
    'validator_credit_card' => 'Il campo "%s" deve contenere un numero di carta di credito valida.',
    'validator_base' => 'Il campo "%s" deve contenere un formato in Base 64.',
    'validator_alpha_dash' => 'Il campo "%s" può contenere solo caratteri alfa-numerici, underscore ("_") e punti.',
    'validator_alpha_num_spaces' => 'Il campo "%s" può contenere solo caratteri alfa-numerici e di spaziatura.',
    'validator_mac_address' => 'Il campo "%s" non è un mac address valido',
    'validator_accept' => 'Non hai accettato le condizioni nel campo "%s".',
    'validator_between_string' => 'Il campo "%s" deve rientrare nella lunghezza minima di "%s" e massima di "%s" caratteri.',
    'validator_ip' => 'Il campo "%s" deve contenere un indirizzo IP valido.',
    'validator_regex' => 'Il campo "%s" non è nel formato corretto',
    'validator_differs' => 'Il campo "%s" deve essere diverso dal campo "%s".',
    'validator_match' => 'Il campo "%s" deve essere uguale al campo "%s".',
    'validator_greater_than' => 'Il campo "%s" deve contenere un numero maggiore di "%s".',
    'validator_greater_than_or_equal' => 'Il campo "%s" deve contenere un numero maggiore o uguale a "%s".',
    'validator_less_than' => 'Il campo "%s" deve contenere un numero inferiore a "%s".',
    'validator_less_than_or_equal' => 'Il campo "%s" deve contenere un numero inferiore o uguale a "%s".',
    'validator_is_numeric' => 'Il campo "%s" deve contenere un valore numerico',
    'validator_token' => 'Token non valido',
];