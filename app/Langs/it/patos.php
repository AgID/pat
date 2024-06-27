<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [
    'brn_save' => '<i class="far fa-save"></i>&nbsp; Salva',
    'brn_save_spinner' => '<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...',
    'check_password_error' => 'La password non rispecchia i requisiti minimi di sicurezza. I criteri sono:<ul><li>Lunghezza minima 14 caratteri</li><li>Almeno una lettera in maiuscolo</li><li>Almeno un numero</li><li>Almeno uno dei seguenti caratteri speciali: ! ( @ # $ % * - </li></ul>',

    //Success operation
    'success_save_operation' => 'Elemento salvato con successo',
    'success_update_operation' => 'Elemento modificato con successo',
    'success_delete_operation' => 'Elemento eliminato con successo',
    'success_archive_operation' => 'Elemento archiviato con successo',
    'success_restore_operation' => 'Elemento ripristinato con successo',
    'success_save' => '%s salvato con successo',
    'success_save_2' => '%s salvata con successo',
    'success_edit' => '%s modificato con successo',
    'success_edit_2' => '%s modificata con successo',
    'success_delete' => '%s eliminato con successo',
    'success_delete_2' => '%s eliminata con successo',
    'activate_user_message' => 'Operazione avvenuta con successo',

    //URI segment ID errors
    'error_validate_uri_segment_id' => 'Questo %s non esiste oppure non hai i privilegi necessari per modificare questa voce',
    'error_validate_uri_segment_id_2' => 'Questa %s non esiste oppure non hai i privilegi necessari per modificare questa voce',
    'not_exist_obj' => '%s non esistente!',

    'no_permits' => 'Non hai i permessi per compiere quest\'azione',

    //Struttura di appartenenza
    'belong_to_himself' => 'La struttura di appartenenza deve essere diversa dalla struttura stessa',

    //Fiscal code error
    'fiscal_code_error' => 'Campo "Codice fiscale %s"  non valido',
    'fiscal_code_error_generic' => 'Campo "Codice fiscale"  non valido',
    'fiscal_code_exist' => 'Codice fiscale inserito già esistente',
    'fiscal_code_vat_error' => 'Codice fiscale/Partita IVA non valido',
    'foreign_tax_identification_exist' => 'Identificativo fiscale estero inserito già esistente',

    //VAt errors
    'vat_error' => 'Partita IVA non valida',

    //Phone number errors
    'phone_error' => 'Formato numero telefonico non valido',
    'max_phone_length' => 'Lunghezza numero Telefonico superiore alla lunghezza massima consentita',
    'min_phone_length' => 'Lunghezza numero Telefonico inferiore alla lunghezza minima consentita',
    'already_exist_phone' => 'Numero cellulare inserito già esistente',

    //Email errors
    'certified_email_exist' => 'Email certificata inserita già esistente',
    'email_exist' => 'Email inserita già esistente',

    //User errors
    'username_exist' => 'Username inserito già esistente',
    'not_exist_user' => 'Utente non esistente!',
    'password_change_day' => 'Non puoi modificare la password più volte al giorno',
    'last_5_password' => 'Non puoi utilizzare le ultime cinque password inserite precedentemente',
    'error_activate_user' => 'L\'utente che stai provando a modificare non esiste oppure non hai le autorizzazioni necessarie per compiere questa operazione',
    'error_activate_institution' => 'L\'ente che stai provando a modificare non esiste oppure non hai le autorizzazioni necessarie per compiere questa operazione',

    //ACL Profiles errors
    'error_update_is_system' => 'Non hai le autorizzazioni per modificare questo profilo, è di sistema',
    'error_permits_back_office' => 'Errore nel permesso "%s" per la sezione Back Office %s riga %s',
    'error_permits_front_office' => 'Errore nel permesso "%s" per la sezione Front Office "%s" riga %s',

    //Multiple selection errors
    'multiple_selection_errors' => 'Identificativi elementi selezionati non validi',

    'tmp_error' => 'Errore temporaneo, riprovare pi&ugrave; tardi. <br /> Se il problema persiste, contattare il servizio assistenza',
    'error_export_csv_' => 'Non &egrave; stato possibile esportare il documento in formato CSV, perch&egrave; non &egrave; presente nessun "%s" in archivio.',
    'error_export_csv' => 'Non &egrave; stato possibile esportare il documento in formato CSV, perch&egrave; non &egrave; presente nessuna "%s" in archivio.',

    //Errori versioning
    'not_exist_el' => 'L\'elemento selezionato non esiste, oppure non hai i privilegi necessari per modificarlo!',
    'not_exist_versioning' => 'Non esiste la versione selezionata per l\'elemento corrente!',

    //Errori date
    'invalid_end_date' => 'Non è possibile inserire una data di FINE che sia precedente o uguale a quella di inizio!',
    'invalid_end_date_less' => 'Non è possibile inserire una data di FINE che sia precedente a quella di inizio!',
    'invalid_end_date_office' => 'Non è possibile inserire una DATA DI FINE INCARICO che sia precedente o uguale a quella di inizio incarico!',
    'invalid_end_date_activation' => 'Non è possibile inserire una DATA DI FINE ATTIVAZIONE che sia precedente o uguale a quella di inizio!',
    'invalid_end_date_publication' => 'Non è possibile inserire una DATA DI FINE PUBBLICAZIONE che sia precedente o uguale a quella di inizio!',
    'invalid_end_date_publication_2' => 'Non è possibile inserire una DATA DI PUBBLICAZIONE che sia precedente alla data dell\'atto!',
    'invalid_end_date_publication_3' => 'Non è possibile inserire una DATA DI FINE PUBBLICAZIONE che sia precedente alla data di inizio!',
    'invalid_expiration_date' => 'Non è possibile inserire una DATA DI SCADENZA DEL BANDO che sia precedente alla data di pubblicazione!',
    'invalid_expiration_date_2' => 'Non è possibile inserire una DATA DI SCADENZA che sia precedente alla data dell\'atto!',
    'invalid_question_date' => 'Non è possibile inserire una DATA DI SCADENZA PRESENTAZIONI OFFERTE che sia precedente alla data dell\'atto!',
    'invalid_ending_date' => 'Non è possibile inserire una DATA DI TERMINE DEL CONCORSO che sia precedente alla data di pubblicazione!',
    'invalid_guri_date' => 'Non è possibile inserire una DATA DI PUBBLICAZIONE SULLA GURI che sia precedente alla data dell\'atto!',
    'invalid_guue_date' => 'Non è possibile inserire una DATA DI PUBBLICAZIONE SULLA GUUE che sia precedente alla data dell\'atto!',
    'invalid_work_date' => 'Non è possibile inserire una DATA DI FINE LAVORI che sia precedente alla data di inizio lavori!',
    'invalid_contracting_stations_date' => 'Non è possibile inserire una DATA DI PUBBLICAZIONE STAZIONE APPALTANTE che sia precedente alla data dell\'atto!',

    //Errori date archivio
    'invalid_archive_date' => 'Non è possibile inserire una data di FINE PUBBLICAZIONE che sia precedente o uguale a quella di fine attività!',
    'invalid_archive_ending_date' => 'Non è possibile inserire una data di FINE ATTIVITÀ che sia precedente o uguale a quella di inizio attività!',

    'success_cancel_operation' => 'Atto annullato con successo',

    //Errore numero minimo di componenti per un raggruppamento
    'few_components' => 'Non è possibile creare un raggruppamento senza aver selezionato almeno due componenti',


    //Errori date albo
    'albo_end_date' => 'Non è possibile inserire una data di FINE PUBBLICAZIONE che sia precedente o uguale a quella di inizio!',
    'albo_start_date' => 'Non è possibile inserire una data di INIZIO PUBBLICAZIONE che sia precedente alla data dell\'atto!',

    //Errori scp
    'scp_no_notice' => 'Nessuna procedura relativa di tipo "Bando di Gara"',
    'scp_lot_error' => 'Nessun Bando di Gara di riferimento selezionato',
    'scp_too_many_notice' => 'Sono state selezionate pi&ugrave; procedure relative di tipo "Bando di Gara"',
    'scp_invalid_activation_date' => 'Data di pubblicazione sul sito maggiore di quella odierna',
    'scp_no_cf' => 'Codice fiscale del RUP non presente',
    'scp_no_rup_selected' => 'Il personale RUP selezionato non è impostato come tale',
    'scp_is_smartcig' => 'SmartCig o Numero gara ANAC',
    'scp_no_typology' => 'Tipologia atto non selezionata',
    'scp_lot_multicig' => 'Il bando è di tipo MultiCig ma non presenta lotti collegati',
    'scp_awardee_error' => 'Errore nel recupero delle informazioni "Aggiudicatari"',
    'scp_no_info' => 'Errore nel recupero delle informazioni',
    'scp_awardee_group_error' => 'Tipologia del raggruppamento aggiudicatore non selezionata',
];