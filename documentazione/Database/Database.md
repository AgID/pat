# Database Pat

**Riferimento path file dump:**  *tools/sql/dump pat.sql*

In questo documento viene descritto il database del software Pat.

Vengono descritte nel dettaglio tutte le tabelle con i loro campi, le relazioni e le classi che ne descrivono il Modello.



#### Lista delle tabelle del database:

1. `acl_profiles`
2. `activity_log`
3. `attachments`
4. `attachment_cats`
5. `attempts`
6. `configs`
7. `content_section_fo`
8. `contraent_choice`
9. `institutions`

------

#### Descrizione tabelle:

1.  **acl_profiles**

Tabella per i **Profili ACL**.

Classe per il modello: **AclProfilesModel**

Classe controller: **AclUsersProfileAdminController**

In relazione molti a molti con le tabelle section_fo e section_bo tramite la tabella permits.



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del profilo ACL                                           |
| institution_id | int           | Id dell'ente di appartenenza del profilo ACL                 |
| is_system      | tinyint(bool) | Indica se il profilo ACL è un profilo di sistema o meno      |
| versioning     | int           | Indica se il profilo ha il permesso per la gestione del versioning degli oggetti |
| lock_user      | tinyint(bool) | Indica se il profilo ha il permesso per il blocco/sblocco degli utenti |
| advanced       | tinyint(bool) | Indica se il profilo ha il permesso per la modifica avanzata del profilo utente |
| export_csv     | tinyint(bool) | Indica se il profilo ha il permesso per l'esportazione dei dati degli oggetti in formato CSV |
| name           | varchar       | Nome del profilo ACL                                         |
| description    | text          | Descrizione del profilo ACL                                  |
| deleted        | int           | Indica se il profilo ACL è stato eliminato (valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data di creazione del profilo                             |
| updated_at     | datetime      | La data di ultima modifica del profilo                       |
| deleted_at     | datetime      | La data di eliminazione del profilo                          |



------

2.  **activity_log**

Tabella per i **Log delle attività**.

Classe per il modello: **ActivityLogModel**

Classe controller: **ActivityLogAdminController**

Relazioni:

- molti a uno con users
- molti a uno con institutions



| Colonna        | Tipo     | Descrizione                                                  |
| -------------- | -------- | ------------------------------------------------------------ |
| id             | int      | Id del record                                                |
| institution_id | int      | Id dell'ente associato all'attività                          |
| user_id        | int      | Id dell'utente che ha eseguito l'attività                    |
| is_superadmin  | int      | Indica se l'attività è stata eseguita da un utente super admin (valore a 1) |
| client_info    | text     | Informazioni sul client da cui è stata eseguita l'attività   |
| ip_address     | varchar  | Indirizzo IP da cui è stata eseguita l'attività              |
| action         | varchar  | Nome dell'attività eseguita                                  |
| description    | text     | Descrizione dettagliata dell'attività eseguita               |
| uri            | varchar  | Uri dell'attività eseguita                                   |
| referer        | varchar  | Sezione su cui è stata eseguita l'attività                   |
| request_post   | text     | Tutte le informazioni presenti nella richiesta POST          |
| request_get    | text     | Tutte le informazioni presenti nella richiesta GET           |
| request_file   | text     | Tutte le informazioni presenti nei file nella richiesta      |
| created_at     | datetime | La data di creazione del profilo                             |
| updated_at     | datetime | La data di ultima modifica del profilo                       |
| deleted_at     | datetime | La data di eliminazione del profilo                          |



------

3.  **attachments**

Tabella per la gestione degli **Allegati**.

Classe per il modello: **AttachmentsModel**

Classe controller: **--**

Relazioni: --



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del record                                                |
| institution_id | int           | Id dell'ente a cui è associato il file allegato              |
| file_name      | varchar       | Nome del file allegato                                       |
| file_type      | varchar       | Tipo del file allegato                                       |
| file_path      | varchar       | Percorso in cui è il file allegato                           |
| full_path      | varchar       | Percorso completo in cui è il file allegato                  |
| raw_name       | varchar       | Nome del file senza estensione                               |
| origin_name    | varchar       | Descrizione dettagliata dell'attività eseguita               |
| client_name    | varchar       | Uri dell'attività eseguita                                   |
| file_ext       | varchar       | Estensione del file allegato                                 |
| file_size      | varchar       | Dimensione del file allegato                                 |
| is_image       | tinyint(bool) | Indica se il file allegato è un'immagine o meno              |
| image_width    | varchar       | Se il file allegato è un' immagine, ne indica la larghezza   |
| image_height   | varchar       | Se il file allegato è un' immagine, ne indica l'altezza      |
| image_type     | varchar       | Se il file allegato è un' immagine, ne indica il tipo        |
| image_size_str | varchar       | --                                                           |
| indexable      | tinyint(bool) | Indica se l'allegato è indicizzabile o meno dai motori di ricerca |
| created_at     | datetime      | La data di inserimento del file allegato                     |
| updated_at     | datetime      | La data di ultima modifica del file allegato                 |
| deleted_at     | datetime      | La data di eliminazione del file allegato                    |



------

4. **attachment_cats**

Tabella per le categorie di allegati.

Classe per il modello: **CategoriesModel**

Classe controller: **--**

Relazioni: 

- uno a molti con attachments



| Colonna        | Tipo     | Descrizione                                 |
| -------------- | -------- | ------------------------------------------- |
| id             | int      | Id della categoria per i file allegati      |
| institution_id | int      | Id dell'ente a cui è associata la categoria |
| parent_id      | int      | Indica la categoria "genitore"              |
| path           | varchar  | Indica il percorso della categoria          |
| name           | varchar  | Nome della categoria                        |
| created_at     | datetime | La data di creazione della categoria        |
| updated_at     | datetime | La data di ultima modifica della categoria  |
| deleted_at     | datetime | La data di eliminazione della categoria     |



------

5.  **attempts**

Tabella utilizzata per la gestione dei tentativi falliti di accesso da parte di un determinato indirizzo IP.

Classe per il modello: **AttemptsModel**

Classe controller: **--**



| Colonna     | Tipo     | Descrizione                                                  |
| ----------- | -------- | ------------------------------------------------------------ |
| id          | int      | Id del record                                                |
| ip          | varchar  | Indirizzo IP da cui è stato effettuato il tentativo di accesso fallito |
| client_info | varchar  | Contiene le informazioni del client da cui è stato fatto il tentativo di accesso fallito |
| created_at  | datetime | La data in cui è stato fatto il tentativo di accesso fallito |
| updated_at  | datetime | --                                                           |
| deleted_at  | datetime | La data di eliminazione del tentativo di accesso fallito     |



------

6.  **configs**

Tabella utilizzata per la gestione delle configurazioni del Pat

Classe per il modello: **ConfigsModel**

Classe controller: **--**



| Colonna        | Tipo     | Descrizione                                      |
| -------------- | -------- | ------------------------------------------------ |
| id             | int      | Id del record                                    |
| institution_id | int      | ID dell'ente a cui è associata la configurazione |
| opt_key        | varchar  | Chiave identificativa della configurazione       |
| opt_value      | varchar  | Valore della configurazione                      |
| opt_group      | varchar  | --                                               |
| created_at     | datetime | La data in cui è stata creata la configurazione  |
| updated_at     | datetime | La data di ultima modifica della configurazione  |
| deleted_at     | datetime | La data di eliminazione della configurazione     |



------

7.  **content_section_fo**

Tabella utilizzata per la gestione dei contenuti(paragrafi) delle pagine di front-office.

Classe per il modello: **ContentSectionFoModel**

Classe controller: **--**

Relazioni: 

- molti a uno con section_fo



| Colonna        | Tipo     | Descrizione                                                  |
| -------------- | -------- | ------------------------------------------------------------ |
| id             | int      | Id del record                                                |
| institution_id | int      | ID dell'ente a cui è associato il paragrafo della pagina di front-office |
| section_fo_id  | int      | ID della pagina di front-office in cui inserire il paragrafo |
| user_id        | int      | ID dell'utente che ha creato il paragrafo                    |
| name           | varchar  | Nome del paragrafo                                           |
| sort           | int      | Valore utilizzato per gestire l'ordine di visualizzazione del paragrafo all'interno della pagina |
| content        | text     | Contenuto del paragrafo da mostrare nella pagina di front-office |
| created_at     | datetime | La data in cui è stata creato il paragrafo                   |
| updated_at     | datetime | La data di ultima modifica del paragrafo                     |
| deleted_at     | datetime | La data di eliminazione del paragrafo                        |



------

8. **contraent_choice**

Tabella utilizzata per la gestione della scelta del contraente nelle selcet dei form.

Classe per il modello: **ContentSectionFoModel**

Classe controller: **--**



| Colonna | Tipo          | Descrizione                                  |
| ------- | ------------- | -------------------------------------------- |
| id      | int           | Id del record                                |
| name    | varchar       | Nome della scelta del contraente             |
| deleted | tinyint(bool) | Indica se il record è stato eliminato o meno |



------

9. **institutions**

Tabella utilizzata per la gestione degli **Enti** della trasparenza. (etrasp_enti su vecchio PAT)

Classe per il modello: **InstitutionsModel**

Classe controller: 

- **InstitutionAdminController** (per utenti normali)
- **InstitutionAdminController** (per utente super admin)



| Colonna                      | Tipo          | Descrizione                                                  |
| ---------------------------- | ------------- | ------------------------------------------------------------ |
| id                           | int           | Id dell'ente                                                 |
| id_creator                   | int           | ID dell'utente che creato l'ente                             |
| institution_type_id          | int           | ID della tipologia di ente                                   |
| state                        | int           | --                                                           |
| full_name_institution        | varchar       | Nome completo dell'ente                                      |
| short_institution_name       | varchar       | Nome breve dell'ente                                         |
| vat                          | varchar       | Indica la Partita Iva dell'ente                              |
| email_address                | varchar       | Indica l'indirizzo email dell'ente                           |
| certified_email_address      | varchar       | Indica l'indirizzo email certificato dell'ente               |
| institutional_website_name   | varchar       | Nome portale istituzionale dell'ente                         |
| institutional_website_url    | varchar       | URL portale istituzionale dell'ente                          |
| top_level_institution_name   | varchar       | Nome dell'ente di appartenenza                               |
| top_level_institution_url    | varchar       | URL del portale istituzionale dell'ente di appartenenza      |
| welcome_text                 | varchar       | Testo iniziale homapage dell'ente                            |
| footer_text                  | varchar       | Testo da inserire nel footer                                 |
| accessibility_text           | varchar       | Testo dell'accessibilità                                     |
| address_street               | varchar       | Strada indirizzo dell'ente                                   |
| address_zip_code             | varchar       | Codice postale indirizzo dell'ente                           |
| address_city                 | varchar       | Città indirizzo dell'ente                                    |
| address_province             | varchar       | Provincia indirizzo dell'ente                                |
| phone                        | varchar       | Recapito telefonico dell'ente                                |
| two_factors_identification   | tinyint(bool) | Indica se è attiva l'autenticazione a due fattori per l'ente |
| trasparenza_logo_file        | varchar       | Logo della trasparenza dell'ente                             |
| activation_date              | datetime      | Data di attivazione dell'ente                                |
| expiration_date              | datetime      | Data di scadenza dell'ente                                   |
| cancellation                 | ??            | ??                                                           |
| trasparenza_urls             | varchar       | URL della trasparenza dell'ente                              |
| bulletin_board_url           | varchar       | URL albo pretorio dell'ente                                  |
| simple_logo_file             | varchar       | Logo dell'ente                                               |
| custom_css                   | varchar       | File css personalizzato dell'ente                            |
| favicon_file                 | varchar       | Favicon dell'ente                                            |
| opendata_channel             | ??            | ??                                                           |
| show_update_date             | tinyint(bool) | Indica se mostrare la data di ultimo aggiornamento dei contenuti |
| statistic_snippet_code ??    | varchar       | Snippet di codice per il monitoraggio delle statistiche dell'ente |
| google_maps_api_key ??       | varchar       | Chiave per l'API di Google maps per l'ente                   |
| indexable                    | tinyint(bool) | Indica se il portale dell'ente è indicizzabile o meno dai motori di ricerca |
| support                      | tinyint(bool) | Supporto ente su vecchio PAT ??                              |
| show_regulation_in_structure | tinyint(bool) | Indica se mostrare o meno la norma associata alla struttura organizzativa dell'ente |
| tabular_display_org_ind_pol  | tinyint(bool) | Indica se attivare o meno la visualizzazione tabellare degli Organi di indirizzo politico |
| max_users                    | int           | Numero massimo di utenti attivabili per l'ente               |
| client_code                  | varchar       | Codice cliente dell'ente                                     |
| smtp_username                | varchar       | Username per server SMTP utilizzato dall'ente                |
| smtp_pec_username            | varchar       | Username per server SMTP per PEC utilizzato dall'ente        |
| smtp_password                | varchar       | Password per server SMTP utilizzato dall'ente                |
| smtp_pec_password            | varchar       | Password per server SMTP per PEC utilizzato dall'ente        |
| smtp_host                    | varchar       | Indirizzo server SMPT utilizzato dall'ente                   |
| smtp_pec_host                | varchar       | Indirizzo server SMPT per PEC utilizzato dall'ente           |
| smtp_port                    | varchar       | Porta server SMPT utilizzato dall'ente                       |
| smtp_pec_port                | varchar       | Porta server SMPT per PEC utilizzato dall'ente               |
| smtp_security                | varchar       | Indica il protocollo di sicurezza da utilizzare per SMPT     |
| smtp_pec_security            | varchar       | Indica il protocollo di sicurezza da utilizzare per SMPT per PEC |
| smtp_auth                    | varchar       | Indica se utilizzare o meno l'autenticazione per il server SMPT |
| show_smtp_auth               | varchar       | Indica se utilizzare o meno l'autenticazione per il server SMPT per PEC |
| smtp_test_email              | varchar       | Indirizzo email per test del server SMPT                     |
| smtp_pec_auth                | varchar       | Indica se utilizzare o meno l'autenticazione per il server SMPT per PEC |
| email_notifications          | varchar       | Email mittente per le notifiche                              |
| email_pec_notifications      | varchar       | Email mittente per le notifiche PEC                          |
| publication_responsible      | varchar       | Responsabile del procedimento di pubblicazione dell'ente     |
| privacy_url                  | varchar       | URL della privacy dell'ente                                  |
| private_token                | ??            | ??                                                           |
| last_visit_time_limit        | varchar       | Tempo di disattivazione dell'ente dall'ultimo accesso        |
| personnel_roles              | text          | Ruoli aggiuntivi per il personale dell'ente                  |
| active                       | tinyint(bool) | Indica se l'ente è attivo(valore a 1) o meno(valore a 0)     |
| deleted                      | tinyint(bool) | Indica se l'ente è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at                   | datetime      | La data in cui è stata creato l'ente                         |
| updated_at                   | datetime      | La data di ultima modifica dell'ente                         |
| deleted_at                   | datetime      | La data di eliminazione dell'ente                            |



------

10. **institution_type**

Tabella utilizzata per la gestione delle tipologie di enti. (oggetto_etrasp_tipoentisemplice su vecchio PAT)

Classe per il modello: **InstitutionTypeModel**

Classe controller: **--**

Relazioni:

- uno a molti con institutions



| Colonna                   | Tipo          | Descrizione                                        |
| ------------------------- | ------------- | -------------------------------------------------- |
| id                        | int           | Id della tipologia ente                            |
| owner_id                  | int           | ID dell'utente che ha creato la tipologia di ente  |
| label_institution_type_id | int           | Relazione con la tabella label_institution_type    |
| name                      | varchar       | Nome della tipologia di ente                       |
| state                     | tinyint(bool) | ??                                                 |
| workflow_state            | varchar       | Sato del workflow                                  |
| type_name                 | ??            | ??                                                 |
| created_at                | datetime      | La data in cui è stata creato la tipologia di ente |
| updated_at                | datetime      | La data di ultima modifica della tipologia di ente |
| deleted_at                | datetime      | La data di eliminazione della tipologia di ente    |



------

11. **io_app**

Tabella utilizzata per la gestione dell'app IO. (app_io su vecchio PAT)

Classe per il modello: **IoAppModel**

Classe controller: **--**



| Colonna        | Tipo     | Descrizione                             |
| -------------- | -------- | --------------------------------------- |
| id             | int      | Id del record                           |
| owner_id       | int      | ID dell'utente che ha creato il record  |
| institution_id | int      | ID dell'ente associato al record        |
| name           | varchar  | ??                                      |
| description    | text     | ??                                      |
| department     | varchar  |                                         |
| type_name      | ??       | ??                                      |
| token          | varchar  | ??                                      |
| base_url       | varchar  | ??                                      |
| object_id      | int      | ??                                      |
| privacy        | text     | ??                                      |
| created_at     | datetime | La data in cui è stata creato il record |
| updated_at     | datetime | La data di ultima modifica del record   |
| deleted_at     | datetime | La data di eliminazione del record      |



------

12.  **io_app_notifications**

Tabella utilizzata per la gestione delle notifiche dell'app IO. ( app_io_notifiche su vecchio PAT)

Classe per il modello: **IoAppNotificationsModel**

Classe controller: **IoAppNotificationAdminController**



| Colonna            | Tipo     | Descrizione                             |
| ------------------ | -------- | --------------------------------------- |
| id                 | int      | Id del record                           |
| owner_id           | int      | ID dell'utente che ha creato il record  |
| institution_id     | int      | ID dell'ente associato al record        |
| io_app_id          | int      | ??                                      |
| object_id          | int      | ??                                      |
| document_id        | int      |                                         |
| name               | varchar  | ??                                      |
| description        | text     | ??                                      |
| content            | text     | ??                                      |
| log                | text     | ??                                      |
| sent_notifications | varchar  | ??                                      |
| created_at         | datetime | La data in cui è stata creato il record |
| updated_at         | datetime | La data di ultima modifica del record   |
| deleted_at         | datetime | La data di eliminazione del record      |



------

13.  **io_app_subscribers**

Tabella utilizzata per la gestione degli iscritti all'app IO. (app_io_iscritti su vecchio PAT)

Classe per il modello: **IoAppSubscribersModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                             |
| -------------- | ------------- | --------------------------------------- |
| id             | int           | Id del record                           |
| institution_id | int           | ID dell'ente associato al record        |
| io_app_id      | int           | ??                                      |
| cf             | varchar       | ??                                      |
| privacy        | tinyint(bool) | ??                                      |
| created_at     | datetime      | La data in cui è stata creato il record |
| updated_at     | datetime      | La data di ultima modifica del record   |
| deleted_at     | datetime      | La data di eliminazione del record      |



------

14.  **io_log**

Tabella utilizzata per la gestione dei log  dell'app IO. (app_io_iscritti su vecchio PAT)

Classe per il modello: **IoLogModel**

Classe controller: **--**



| Colonna        | Tipo     | Descrizione                             |
| -------------- | -------- | --------------------------------------- |
| id             | int      | Id del record                           |
| institution_id | int      | ID dell'ente associato al record        |
| start_date     | datetime | ??                                      |
| sent           | int      | ??                                      |
| totals         | int      | ??                                      |
| end_date       | datetime | ??                                      |
| errors         | text     | ??                                      |
| created_at     | datetime | La data in cui è stata creato il record |
| updated_at     | datetime | La data di ultima modifica del record   |
| deleted_at     | datetime | La data di eliminazione del record      |



------

15.  **label_institution_type**

Tabella utilizzata per le traduzioni.

Classe per il modello: **LabelInstitutionTypeModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                             |
| -------------- | ------------- | --------------------------------------- |
| id             | int           | Id del record                           |
| state          | tinyint(bool) | ??                                      |
| workflow_state | varchar       | ??                                      |
| name           | varchar       | ??                                      |
| description    | varchar       | ??                                      |
| created_at     | datetime      | La data in cui è stata creato il record |
| updated_at     | datetime      | La data di ultima modifica del record   |
| deleted_at     | datetime      | La data di eliminazione del record      |



------

16.  **meta_acl_profiles**

Tabella astratta per contenere eventuali valori aggiuntivi non previsti nella tabella standard.

Classe per il modello: **MetaAclProfilesModel**

Classe controller: **--**



| Colonna         | Tipo     | Descrizione                             |
| --------------- | -------- | --------------------------------------- |
| id              | int      | Id del record                           |
| acl_profiles_id | int      | ID del profilo ACL associato            |
| meta_key        | varchar  | Chiave identificativa del meta dato     |
| meta_label      | varchar  | Label del meta dato                     |
| meta_value      | varchar  | Valore del meta dato                    |
| meta_group      | varchar  | Gruppo del meta dato                    |
| meta_icon       | varchar  | Eventuale icona per il meta dato        |
| created_at      | datetime | La data in cui è stata creato il record |
| updated_at      | datetime | La data di ultima modifica del record   |
| deleted_at      | datetime | La data di eliminazione del record      |



------

17.  **meta_institutions**

Tabella astratta per contenere eventuali valori aggiuntivi non previsti nella tabella standard.

Classe per il modello: **MetaInstitutionsModel**

Classe controller: **--**



| Colonna        | Tipo     | Descrizione                             |
| -------------- | -------- | --------------------------------------- |
| id             | int      | Id del record                           |
| institution_id | int      | ID dell'ente associato                  |
| meta_key       | varchar  | Chiave identificativa del meta dato     |
| meta_label     | varchar  | Label del meta dato                     |
| meta_value     | varchar  | Valore del meta dato                    |
| meta_group     | varchar  | Gruppo del meta dato                    |
| meta_icon      | varchar  | Eventuale icona per il meta dato        |
| created_at     | datetime | La data in cui è stata creato il record |
| updated_at     | datetime | La data di ultima modifica del record   |
| deleted_at     | datetime | La data di eliminazione del record      |



------

18.  **meta_object_notices_acts**

Tabella astratta per contenere eventuali valori aggiuntivi non previsti nella tabella standard.

Classe per il modello: **MetaObjectNoticesActsModel**

Classe controller: **--**



| Colonna                | Tipo     | Descrizione                             |
| ---------------------- | -------- | --------------------------------------- |
| id                     | int      | Id del record                           |
| object_notices_acts_id | int      | ID dell'atto amministrativo associato   |
| meta_key               | varchar  | Chiave identificativa del meta dato     |
| meta_label             | varchar  | Label del meta dato                     |
| meta_value             | varchar  | Valore del meta dato                    |
| meta_group             | varchar  | Gruppo del meta dato                    |
| meta_icon              | varchar  | Eventuale icona per il meta dato        |
| created_at             | datetime | La data in cui è stata creato il record |
| updated_at             | datetime | La data di ultima modifica del record   |
| deleted_at             | datetime | La data di eliminazione del record      |



------

19.  **meta_sections**

Tabella astratta per contenere eventuali valori aggiuntivi non previsti nella tabella standard.

Classe per il modello: **MetaSectionsModel**

Classe controller: **--**



| Colonna     | Tipo     | Descrizione                             |
| ----------- | -------- | --------------------------------------- |
| id          | int      | Id del record                           |
| sections_id | int      | ID della sezione associata              |
| meta_key    | varchar  | Chiave identificativa del meta dato     |
| meta_label  | varchar  | Label del meta dato                     |
| meta_value  | varchar  | Valore del meta dato                    |
| meta_group  | varchar  | Gruppo del meta dato                    |
| meta_icon   | varchar  | Eventuale icona per il meta dato        |
| created_at  | datetime | La data in cui è stata creato il record |
| updated_at  | datetime | La data di ultima modifica del record   |
| deleted_at  | datetime | La data di eliminazione del record      |



------

20. **meta_users**

Tabella astratta per contenere eventuali valori aggiuntivi non previsti nella tabella standard.

Classe per il modello: **MetaUsersModel**

Classe controller: **--**



| Colonna    | Tipo     | Descrizione                             |
| ---------- | -------- | --------------------------------------- |
| id         | int      | Id del record                           |
| users_id   | int      | ID dell'utente associato                |
| meta_key   | varchar  | Chiave identificativa del meta dato     |
| meta_label | varchar  | Label del meta dato                     |
| meta_value | varchar  | Valore del meta dato                    |
| meta_group | varchar  | Gruppo del meta dato                    |
| meta_icon  | varchar  | Eventuale icona per il meta dato        |
| created_at | datetime | La data in cui è stata creato il record |
| updated_at | datetime | La data di ultima modifica del record   |
| deleted_at | datetime | La data di eliminazione del record      |



------

21.  **object_absence_rates**

Tabella utilizzata per la gestione dei **Tassi di assenza**. (oggetto_tassi_assenza su vecchio PAT)

Classe per il modello: **AbsenceRatesModel**

Classe controller: **AbsenceRatesAdminController**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del tasso di assenza                                      |
| institution_id       | int           | ID dell'ente a cui è associato il tasso di assenza           |
| owner_id             | int           | ID dell'utente che ha creato il tasso di assenza             |
| object_structures_id | int           | ID della struttura organizzativa relativa al tasso di assenza |
| state                | tinyint(bool) | ??                                                           |
| workflow_state       | varchar       | ??                                                           |
| structure_name       | varchar       | ??                                                           |
| month                | varchar       | Periodo del tasso di assenza                                 |
| year                 | int           | Anno di riferimento del tasso di assenza                     |
| presence_percentage  | decimal       | Percentuale di presenze                                      |
| total_absence        | decimal       | Assenze totali relative al tasso di assenza                  |
| absence_illness      | decimal       | Assenze per malattia relative al tasso di assenza            |
| illness_days         | decimal       | Giorni di malattia relativi al tasso di assenza              |
| deleted              | tinyint(bool) | Indica se il tasso di assenza è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il tasso di assenza            |
| updated_at           | datetime      | La data di ultima modifica del tasso di assenza              |
| deleted_at           | datetime      | La data di eliminazione del tasso di assenza                 |



------

22.  **object_ac_directives**

Tabella utilizzata per la gestione delle **Direttive anticorruzione**.  (oggetto_ac_direttive su vecchio PAT)

Classe per il modello: **AcDirectivesModel**

Classe controller: **AcDirectiveAdminController**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id della direttiva anticorruzione                            |
| institution_id       | int           | ID dell'ente a cui è associata la direttiva anticorruzione   |
| owner_id             | int           | ID dell'utente che ha creato la direttiva anticorruzione     |
| object_structures_id | int           | ID della struttura organizzativa relativa al tasso di assenza |
| object_personnel_id  | int           | ID del personale                                             |
| state                | tinyint(bool) | ??                                                           |
| workflow_state       | varchar       | ??                                                           |
| number               | varchar       | ??                                                           |
| object               | varchar       | ??                                                           |
| date                 | datetime      | ??                                                           |
| content              | text          | ??                                                           |
| deleted              | tinyint(bool) | Indica se la direttiva è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at           | datetime      | La data in cui è stata creata la direttiva anticorruzione    |
| updated_at           | datetime      | La data di ultima modifica della direttiva anticorruzione    |
| deleted_at           | datetime      | La data di eliminazione della direttiva anticorruzione       |



------

23.  **object_ac_measures**

Tabella utilizzata per la gestione delle **Misure anticorruzione**.  (oggetto_ac_misure su vecchio PAT)

Classe per il modello: **AcMeasuresModel**

Classe controller: **AcMeasureAdminController**



| Colonna             | Tipo          | Descrizione                                                  |
| ------------------- | ------------- | ------------------------------------------------------------ |
| id                  | int           | Id della misura anticorruzione                               |
| institution_id      | int           | ID dell'ente a cui è associata la misura anticorruzione      |
| owner_id            | int           | ID dell'utente che ha creato la misura anticorruzione        |
| object_personnel_id | int           | ID del personale associato alla misura anticorruzione        |
| state               | tinyint(bool) | ??                                                           |
| workflow_state      | varchar       | ??                                                           |
| goal                | varchar       | Obbiettivi??                                                 |
| times               | text          | ??                                                           |
| markers             | text          | Indicatori??                                                 |
| verification_mode   | text          | ??                                                           |
| check_periodicity   | text          | ??                                                           |
| prevention_level    | text          | ??                                                           |
| deleted             | tinyint(bool) | Indica se la misura è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at          | datetime      | La data in cui è stata creata la misura anticorruzione       |
| updated_at          | datetime      | La data di ultima modifica della misura anticorruzione       |
| deleted_at          | datetime      | La data di eliminazione della misura anticorruzione          |



------

24. **object_ac_plan**

Tabella utilizzata per la gestione del **Piano anticorruzione**.  (oggetto_ac_piano su vecchio PAT)

Classe per il modello: **AcPlanModel**

Classe controller: **AcPlanAdminController**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del piano anticorruzione                                  |
| institution_id          | int           | ID dell'ente a cui è associato il piano anticorruzione       |
| owner_id                | int           | ID dell'utente che ha creato il piano anticorruzione         |
| premise                 | text          | ??                                                           |
| state                   | tinyint(bool) | ??                                                           |
| workflow_state          | varchar       | ??                                                           |
| pa_description          | text          | Descrizione del processo di adozione                         |
| pa_measures_id          | int           | Provvedimento organo politico                                |
| pa_si_description       | text          | campo pa_si_descrizione su vecchio PAT.                      |
| pa_se_description       | text          | Campo pa_se_descrizione su vecchio PAT. INDIVIDUAZIONE DEGLI ATTORI ESTERNI ALL'AMMINISTRAZIONE CHE HANNO PARTECIPATO ALLA PREDISPOSIZIONE DEL PIANO NONCHÈ DEI CANALI E DEGLI STRUMENTI DI PARTECIPAZIONE |
| pa_description_channels | text          | campo pa_canali_descrizione su vecchio PAT                   |
| gr_description          | text          | campo gr_descrizione su vecchio PAT. Gestione del rischio    |
| gr_areas_description    | varchar       | campo gr_aree_descrizione su vecchio PAT                     |
| gr_valutation_mode      | text          | campo gr_metodo_valutazione su vecchio PAT.                  |
| mo_ptpc_description     | text          | campo mo_ptpc_descrizione su vecchio PAT. PIANO TRIENNALE DI PREVENZIONE DELLA CORRUZIONE - ADOZIONE DEL P.T.P.C. |
| mo_mpc_description      | text          | campo mo_mpc_descrizione su vecchio PAT. MODELLI DI PREVENZIONE DELLA CORRUZIONE |
| mo_at_description       | text          | campo mo_at_descrizione su vecchio PAT. ADEMPIMENTI DI TRASPARENZA |
| mo_cc_description       | text          | campo mo_cc_descrizione su vecchio PAT.CODICE DI COMPORTAMENTO |
| mo_rp_description       | text          | campo mo_rp_descrizione su vecchio PAT.ROTAZIONE DEL PERSONALE |
| mo_oaci_description     | text          | campo mo_oaci_descrizione su vecchio PAT.OBBLIGO DI ASTENSIONE IN CASO DI CONFLITTO DI INTERESSE |
| mo_cai_description      | text          | campo mo_cai_descrizione su vecchio PAT.CONFERIMENTO E AUTORIZZAZIONE INCARICHI |
| mo_iid_description      | text          | campo mo_iid_descrizione su vecchio PAT.INCONFERIBILITÀ PER INCARICHI DIRIGENZIALI |
| mo_ippd_description     | text          | campo mo_ippd_descrizione su vecchio PAT.INCOMPATIBILITÀ PER PARTICOLARI POSIZIONI DIRIGENZIALI |
| mo_ascs_description     | text          | campo mo_ascs_descrizione su vecchio PAT.ATTIVITÀ SUCCESSIVE ALLA CESSAZIONE DAL SERVIZIO |
| mo_fcau_description     | text          | campo mo_fcau_descrizione su vecchio PAT.FORMAZIONE DI COMMISSIONI, ASSEGNAZIONE AGLI UFFICI, CONFERIMENTO DI INCARICHI IN CASO DI CONDANNA PER DELITTI CONTRO LA P.A. |
| mo_dpsi_description     | text          | campo mo_dpsi_descrizione su vecchio PAT. TUTELA DEL DIPENDENTE PUBBLICO CHE SEGNALA GLI ILLECITI |
| mo_fdp_description      | text          | campo mo_fdp_descrizione su vecchio PAT.FORMAZIONE DEL PERSONALE |
| mo_pia_description      | text          | campo mo_pia_descrizione su vecchio PAT.PATTI DI INTEGRITÀ NEGLI AFFIDAMENTI |
| mo_asrsc_descritpion    | text          | campo mo_asrsc_descrizione su vecchio PAT.AZIONE DI SENSIBILIZZAZIONE E RAPPORTO CON LA SOCIETÀ CIVILE |
| mo_mtp_description      | text          | campo mo_mtp_descrizione su vecchio PAT.MONITORAGGIO TEMPI PROCEDIMENTALI |
| mo_mrase_description    | text          | campo mo_mrase_descrizione su vecchio PAT.MONITORAGGIO DEI RAPPORTI AMMINISTRAZIONE/SOGGETTI ESTERNI |
| ptt_intro_description   | text          | campo ptt_intro_descrizione su vecchio PAT. Piano triennale della trasparenza |
| ptt_pn_description      | text          | campo ptt_pn_descrizione su vecchio PAT.LE PRINCIPALI NOVITÀ |
| peap_os_description     | text          | campo peap_os_descrizione su vecchio PAT. PROCEDIMENTO DI ELABORAZIONE E ADOZIONE DEL PROGRAMMA |
| peap_cp_description     | text          | campo peap_cp_descrizione su vecchio PAT. Collegamenti con il Piano della performance o con analoghi strumenti di programmazione previsti da normative di settore |
| peap_mcs_description    | text          | campo peap_mcs_descrizione su vecchio PAT.Modalità di coinvolgimento degli stakeholder e i risultati di tale coinvolgimento |
| peap_tma_description    | text          | campo peap_tma_descrizione su vecchio PAT.Termini e le modalità di adozione del Programma da parte degli organi di vertice |
| ict_isc_description     | text          | campo ict_isc_descrizione su vecchio PAT. INIZIATIVE DI COMUNICAZIONE DELLA TRASPARENZA |
| ict_ora_description     | text          | campo ict_ora_descrizione su vecchio PAT. Organizzazione e risultati attesi delle Giornate della trasparenza |
| pap_idr_description     | text          | campo pap_idr_descrizione su vecchio PAT.PROCESSO DI ATTUAZIONE DEL PROGRAMMA |
| pap_idrp_description    | text          | campo pap_idrp_descrizione su vecchio PAT. Individuazione dei dirigenti responsabili della pubblicazione e dell'aggiornamento dei dati |
| ap_iert_description     | text          | campo pap_iert_descrizione su vecchio PAT. Individuazione di eventuali referenti per la trasparenza e specificazione delle modalità di coordinamento con il Responsabile della trasparenza |
| pap_mo_description      | text          | campo pap_mo_descrizione su vecchio PAT. Misure organizzative volte ad assicurare la regolarità e la tempestività dei flussi informativi |
| pap_mo_description      | text          | campo pap_mmv_descrizione su vecchio PAT. Misure di monitoraggio e di vigilanza sull'attuazione degli obblighi di trasparenza a supporto dell'attività di controllo dell'adempimento da parte del responsabile della trasparenza |
| pap_mmv_description     | text          | campo pap_str_descrizione su vecchio PAT. Strumenti e tecniche di rilevazione dell'effettivo utilizzo dei dati da parte degli utenti della sezione "Amministrazione Trasparente" |
| pap_mae_description     | text          | campo pap_mae_descrizione su vecchio PAT. Misure per assicurare l'efficacia dell’istituto dell'accesso civico |
| pap_de_description      | text          | campo pap_de_descrizione su vecchio PAT. DATI ULTERIORI      |
| ccp_description         | text          | campo ccp_descrizione su vecchio PAT. Coordinamento con il ciclo delle performance |
| intro_form_description  | text          | campo form_intro_descrizione su vecchio PAT.                 |
| form_sef_description    | text          | campo form_sef_descrizione su vecchio PAT. INDIVIDUAZIONE DEI SOGGETTI CHE EROGANO LA FORMAZIONE IN TEMA DI ANTICORRUZIONE |
| form_cfa_description    | text          | campo form_cfa_descrizione su vecchio PAT. INDICAZIONE DEI CONTENUTI DELLA FORMAZIONE IN TEMA DI ANTICORRUZIONE |
| form_cse_description    | text          | campo form_cse_descrizione su vecchio PAT. INDICAZIONE DI CANALI E STRUMENTI DI EROGAZIONE DELLA FORMAZIONE IN TEMA DI ANTICORRUZIONE |
| form_qog_description    | text          | campo form_qog_descrizione su vecchio PAT.QUANTIFICAZIONE DI ORE/GIORNATE DEDICATE ALLA FORMAZIONE IN TEMA DI ANTICORRUZIONE |
| cca_ai_description      | text          | campo cca_ai_descrizione su vecchio PAT. Codici di comportamento adottati |
| cca_imd_description     | text          | campo cca_imd_descrizione su vecchio PAT. INDICAZIONE DEI MECCANISMI DI DENUNCIA DELLE VIOLAZIONI DEL CODICE DI COMPORTAMENTO |
| cca_iuc_description     | text          | campo cca_iuc_descrizione su vecchio PAT. INDICAZIONE DELL'UFFICIO COMPETENTE A EMANARE PARERI SULLA APPLICAZIONE DEL CODICE DI COMPORTAMENTO |
| ai_description          | text          | campo ai_descrizione su vecchio PAT. altre iniziative.       |
| se_description          | text          | campo se_descrizione su vecchio PAT. se_descrizione          |
| deleted                 | tinyint(bool) | Indica se la misura è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at              | datetime      | La data in cui è stata creata la misura anticorruzione       |
| updated_at              | datetime      | La data di ultima modifica della misura anticorruzione       |
| deleted_at              | datetime      | La data di eliminazione della misura anticorruzione          |



------

25.  **object_ac_risk**

Tabella utilizzata per la gestione degli oggetti **Rischi anticorruzione**.  (oggetto_ac_rischi su vecchio PAT)

Classe per il modello: **AcRisksModel**

Classe controller: **AcRiskAreaAdminController**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del rischio anticorruzione                                |
| institution_id        | int           | ID dell'ente a cui è associato il rischio anticorruzione     |
| owner_id              | int           | ID dell'utente che ha creato il rischio anticorruzione       |
| object_structures_id  | int           | ID della struttura organizzativa associata al rischio anticorruzione |
| object_personnel_id   | int           | ID del personale associato al rischio anticorruzione         |
| object_proceedings_id | int           | ID del procedimento associato al rischio anticorruzione      |
| state                 | tinyint(bool) | ??                                                           |
| workflow_state        | varchar       | ??                                                           |
| risk_description      | text          | Descrizione del rischio                                      |
| probability_indexes   | varchar       | ??                                                           |
| impact_indexes        | varchar       | ??                                                           |
| risk_classiflication  | varchar       | ??                                                           |
| risk_indication       | varchar       | ??                                                           |
| deleted               | tinyint(bool) | Indica se il rischio è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at            | datetime      | La data in cui è stata creato il rischio anticorruzione      |
| updated_at            | datetime      | La data di ultima modifica del rischio anticorruzione        |
| deleted_at            | datetime      | La data di eliminazione del rischio anticorruzione           |



------

26. **object_ac_rotation**

Tabella utilizzata per la gestione degli oggetti **Rischi anticorruzione**.  (oggetto_ac_rotazione su vecchio PAT)

Classe per il modello: **AcRotationModel**

Classe controller: **AcRotationAdminController**



| Colonna             | Tipo          | Descrizione                                                  |
| ------------------- | ------------- | ------------------------------------------------------------ |
| id                  | int           | Id della rotazione anticorruzione                            |
| institution_id      | int           | ID dell'ente a cui è associata la rotazione anticorruzione   |
| owner_id            | int           | ID dell'utente che ha creato la rotazione anticorruzione     |
| object_personnel_id | int           | ID del personale associato al rischio anticorruzione         |
| state               | tinyint(bool) | ??                                                           |
| workflow_state      | varchar       | ??                                                           |
| description         | text          | Descrizione della rotazione anticorruzione                   |
| deleted             | tinyint(bool) | Indica se la rotazione anticorruzione è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at          | datetime      | La data in cui è stata creato la rotazione anticorruzione    |
| updated_at          | datetime      | La data di ultima modifica della rotazione anticorruzione    |
| deleted_at          | datetime      | La data di eliminazione della rotazione anticorruzione       |



------

27. **object_admin_news_trasparenza**

Tabella utilizzata per la gestione delle news admin della trasparenza(nel back-end).  (oggetto_etrasp_news_admin su vecchio PAT)

Classe per il modello: **AdminNewsTrasparenzaModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                           |
| -------------- | ------------- | ------------------------------------- |
| id             | int           | Id della news                         |
| owner_id       | int           | ID dell'utente che ha creato la news  |
| state          | tinyint(bool) | ??                                    |
| workflow_state | varchar       | ??                                    |
| title          | varchar       | Titolo della news                     |
| close          | tinyint(bool) | ??                                    |
| image          | int           |                                       |
| date           | datetime      | Data della news                       |
| content        | text          | Contenuto della news                  |
| info_link      | varchar       | Link ??                               |
| created_at     | datetime      | La data in cui è stata creata la news |
| updated_at     | datetime      | La data di ultima modifica della news |
| deleted_at     | datetime      | La data di eliminazione della news    |



------

28. **object_anti_corruption_external**

Tabella utilizzata per la gestione degli **Esterni anticorruzione**.  (oggetto_ac_esterni_anticorruzione su vecchio PAT)

Classe per il modello: **AntiCorruptionExternalModel**

Classe controller: **AntiCorruptionExternalAdminController**



| Colonna        | Tipo          | Descrizione                             |
| -------------- | ------------- | --------------------------------------- |
| id             | int           | Id della news                           |
| owner_id       | int           | ID dell'utente che ha creato il recrod  |
| institution_id | int           | ID dell'ente associato al record        |
| state          | tinyint(bool) | ??                                      |
| workflow_state | varchar       | ??                                      |
| name           | varchar       | ??                                      |
| fiscal_code    | varchar       | ??                                      |
| role           | varchar       | ??                                      |
| notes          | text          | ??                                      |
| created_at     | datetime      | La data in cui è stata creato il record |
| updated_at     | datetime      | La data di ultima modifica del record   |
| deleted_at     | datetime      | La data di eliminazione del record      |



------

29. **object_assignments**

Tabella utilizzata per la gestione degli **Incarichi e consulenze**.  (oggetto_incarichi su vecchio PAT)

Classe per il modello: **AssignmentsModel**

Classe controller: **AssignmentAdminController**



| Colonna                            | Tipo          | Descrizione                                                  |
| ---------------------------------- | ------------- | ------------------------------------------------------------ |
| id                                 | int           | Id dell'incarico                                             |
| owner_id                           | int           | ID dell'utente che ha creato l'incarico                      |
| institution_id                     | int           | ID dell'ente associato all'incarico                          |
| object_structures_id               | int           | ID della struttura organizzativa associata all'incarico      |
| related_assignment_id              | int           | ID dell'incarico relativo associato all'incarico(per liqiudazioni) |
| state                              | tinyint(bool) | ??                                                           |
| workflow_state                     | varchar       | ??                                                           |
| typology                           | varchar       | Tipologia dell'incarico(assignment, liquidation)             |
| type                               | varchar       | Tipo dell'incarico(Incarico, Liquidazione)                   |
| consulting_type                    | varchar       | Tipo consulenza dell'incarico                                |
| name                               | varchar       | Nome dell'incarico                                           |
| object                             | varchar       | Oggetto dell'incarico                                        |
| assignment_type                    | varchar       | Tipo di incarico(solo per incarichi)                         |
| assignment_start                   | datetime      | Data di inizio dell'incarico                                 |
| assignment_end                     | datetime      | Data di fine dell'incarico                                   |
| end_of_assignment_not_avaiable     | tinyint(bool) | Indica se è disponibile (valore 0) o meno(valore 1) la data di fine dell'incarico |
| end_of_assignment_not_avaiable_txt | text          | Note relative all'indisponibilità della data di fine incarico |
| compensation                       | varchar       | Compenso dell'incarico(per incarico)                         |
| compensation_provided              | varchar       | Compenso erogato(per liquidazioni)                           |
| compensation_provided_date         | datetime      | Data di erogazione del compenso(per liquidazioni)            |
| liquidation_date                   | datetime      | Data della liquidazione                                      |
| liquidation_year                   | int           | Anno della liquidazione                                      |
| variable_compensation              | text          | Componenti variabili del compenso(solo per incarichi)        |
| notes                              | text          | Note dell'incarico                                           |
| deleted                            | tinyint(bool) | Indica se l'incarico è stato eliminato(valore 1) o meno(valore 0) |
| acts_extremes                      | text          | Estremi atto di conferimento dell'incarico                   |
| attachments_id                     | int           | ID dell'allegato associato all'incarico                      |
| assignment_reason                  | varchar       | Ragione dell'incarico                                        |
| created_at                         | datetime      | La data in cui è stata creata la news                        |
| updated_at                         | datetime      | La data di ultima modifica della news                        |
| deleted_at                         | datetime      | La data di eliminazione della news                           |



------

30. **object_avcp_url**

Tabella utilizzata per la gestione degli **URL per Anac**.  (oggetto_url_avcp su vecchio PAT)

Classe per il modello: **AvcpUrlModel**

Classe controller: **AvcpAdminController**



| Colonna                        | Tipo          | Descrizione                                                  |
| ------------------------------ | ------------- | ------------------------------------------------------------ |
| id                             | int           | Id dell'URL per Anac                                         |
| owner_id                       | int           | ID dell'utente che ha creato l'URL per Anac                  |
| institution_id                 | int           | ID dell'ente associato all'URL per Anac                      |
| object_contracting_stations_id | int           | campo id_stazione su vecchio PAT. Relazione con tabella object_contracting_stations |
| state                          | tinyint(bool) |                                                              |
| workflow_state                 | varchar       | ??                                                           |
| year                           | int           | Anno di riferimento dell'URL per Anac                        |
| url                            | varchar       | URL per Anac                                                 |
| deleted                        | tinyint(bool) | Indica se l'URL per Anac è stato eliminato(valore 1) o meno (valore 0) |
| __bloccato                     | varchar       | ??                                                           |
| __personalizzato               | varchar       | ??                                                           |
| __dimensione_file              | varchar       | ??                                                           |
| __tipoxml                      | varchar       | ??                                                           |
| created_at                     | datetime      | La data in cui è stata creato l'URL per Anac                 |
| updated_at                     | datetime      | La data di ultima modifica dell'URL per Anac                 |
| deleted_at                     | datetime      | La data di eliminazione dell'URL per Anac                    |



------

31. **object_balance_sheets**

Tabella utilizzata per la gestione dei **Bilanci**.  (oggetto_bilanci su vecchio PAT)

Classe per il modello: **BalanceSheetsModel**

Classe controller: **BalanceAdminController**



| Colonna           | Tipo          | Descrizione                                                  |
| ----------------- | ------------- | ------------------------------------------------------------ |
| id                | int           | Id del bilancio                                              |
| owner_id          | int           | ID dell'utente che ha creato il bilancio                     |
| institution_id    | int           | ID dell'ente associato al bilancio                           |
| state             | tinyint(bool) |                                                              |
| workflow_state    | varchar       | ??                                                           |
| name              | varchar       | Nome assegnato al bilancio                                   |
| typology          | varchar       | Tipologia del bilancio                                       |
| year              | int           | Anno di riferimento del bilancio                             |
| description       | varchar       | Descrizione del bilancio                                     |
| publication_state | int           | ??                                                           |
| deleted           | tinyint(bool) | Indica se il bilancio è stato eliminato(valore 1) o meno (valore 0) |
| attachments_id    | int           | ID dell'allegato associato al bilancio                       |
| created_at        | datetime      | La data in cui è stata creato il bilancio                    |
| updated_at        | datetime      | La data di ultima modifica del bilancio                      |
| deleted_at        | datetime      | La data di eliminazione del bilancio                         |



------

32. **object_charges**

Tabella utilizzata per la gestione degli **Oneri informativi e obblighi**.  (oggetto_oneri su vecchio PAT)

Classe per il modello: **ChargesModel**

Classe controller: **ChargeAdminController**



| Colonna           | Tipo          | Descrizione                                                  |
| ----------------- | ------------- | ------------------------------------------------------------ |
| id                | int           | Id del bilancio                                              |
| owner_id          | int           | ID dell'utente che ha creato l'onere informativo             |
| institution_id    | int           | ID dell'ente associato all'onere informativo                 |
| normative_id      | int           | ID della normativa associata all'onere                       |
| attachments_id    | int           | ID dell'allegato associato all'onere                         |
| state             | tinyint(bool) |                                                              |
| workflow_state    | varchar       | ??                                                           |
| type              | varchar       | Tipo dell'onere informativo                                  |
| citizen           | tinyint(bool) | Indica se è per i cittadini(valore 1) o meno (valore 0)      |
| companies         | tinyint(bool) | Indica se è per le imprese(valore 1) o meno (valore 0)       |
| title             | varchar       | Denominazione o titolo dell'onere                            |
| expiration_date   | datetime      | Data di scadenza dell'onere                                  |
| description       | varchar       | Descrizione o contenuto dell'onere                           |
| info_url          | varchar       | Url per maggiori informazioni                                |
| publication_state | varchar       | ??                                                           |
| deleted           | tinyint(bool) | Indica se l'onere è stato eliminato(valore 1) o meno (valore 0) |
| attachments_id    | int           | ID dell'allegato associato al bilancio                       |
| created_at        | datetime      | La data in cui è stata creato il bilancio                    |
| updated_at        | datetime      | La data di ultima modifica del bilancio                      |
| deleted_at        | datetime      | La data di eliminazione del bilancio                         |



------

33. **object_civic_access**

Tabella utilizzata per la gestione dell' **Accesso Civico**.  (oggetto_accesso_civico su vecchio PAT)

Classe per il modello: **CivicAccessModel**

Classe controller: **CivicAccessAdminController**



| Colonna                   | Tipo          | Descrizione                                                  |
| ------------------------- | ------------- | ------------------------------------------------------------ |
| id                        | int           | Id dell'accesso civico                                       |
| owner_id                  | int           | ID dell'utente che ha creato l'accesso civico                |
| institution_id            | int           | ID dell'ente associato all'accesso civico                    |
| manager_id                | int           | ID dell'utente che gestisce l'accesso civico                 |
| object_structures_id      | int           | ID della struttura organizzativa associata all'accesso civico |
| state                     | tinyint(bool) |                                                              |
| workflow_state            | varchar       | ??                                                           |
| request_code              | varchar       | ??                                                           |
| request_date              | datetime      | Data??                                                       |
| request_mode              | varchar       | ??                                                           |
| tipology                  | varchar       | ??                                                           |
| object                    | varchar       | ??                                                           |
| request                   | varchar       | ??                                                           |
| practice_state            | varchar       | ??                                                           |
| link                      | varchar       | ??                                                           |
| protocol_number           | varchar       | ??                                                           |
| protocol_date             | datetime      | ??                                                           |
| review_date               | datetime      | ??                                                           |
| review_request            | varchar       | ??                                                           |
| register_object           | varchar       | ??                                                           |
| result                    | varchar       | ??                                                           |
| result_date               | datetime      | ??                                                           |
| register_result           | varchar       | ??                                                           |
| requesting_company_name   | varchar       | ??                                                           |
| requesting_fiscal_code    | varchar       | ??                                                           |
| requesting_email          | varchar       | ??                                                           |
| requesting_phone          | varchar       | ??                                                           |
| requesting_details        | varchar       | ??                                                           |
| counterparty_company_name | varchar       | ??                                                           |
| counterparty_fiscal_code  | varchar       | ??                                                           |
| counterparty_email        | varchar       | ??                                                           |
| counterparty_phone        | varchar       | ??                                                           |
| counterparty_details      | varchar       | ??                                                           |
| attachments_id            | int           | ID allegato associato all'accesso civico                     |
| deleted                   | tinyint(bool) | Indica se il record è stato eliminato(valore 1) o meno (valore 0) |
| attachments_id            | int           | ID dell'allegato associato al bilancio                       |
| created_at                | datetime      | La data in cui è stata creato il record                      |
| updated_at                | datetime      | La data di ultima modifica del record                        |
| deleted_at                | datetime      | La data di eliminazione del record                           |



------

34. **object_civic_access_comunication**

Tabella utilizzata per la gestione della  **Comunicazione Accesso Civico**.  (oggetto_accesso_civico_com su vecchio PAT)

Classe per il modello: **CivicAccessComunicationModel**

Classe controller: **--**



| Colonna                | Tipo          | Descrizione                                                  |
| ---------------------- | ------------- | ------------------------------------------------------------ |
| id                     | int           | Id del record                                                |
| object_civic_access_id | int           | ID dell'accesso civico associato                             |
| institution_id         | int           | ID dell'ente associato al record                             |
| state                  | tinyint(bool) |                                                              |
| workflow_state         | varchar       | ??                                                           |
| type                   | varchar       | ??                                                           |
| date                   | datetime      | ??                                                           |
| recipient              | varchar       | ??                                                           |
| object                 | varchar       | ??                                                           |
| content                | varchar       | ??                                                           |
| deleted                | tinyint(bool) | Indica se il record è stato eliminato(valore 1) o meno (valore 0) |
| created_at             | datetime      | La data in cui è stata creato il record                      |
| updated_at             | datetime      | La data di ultima modifica del record                        |
| deleted_at             | datetime      | La data di eliminazione del record                           |



------

35. **object_commissions**

Tabella utilizzata per la gestione delle  **Commissioni e gruppi consiliari**.  (oggetto_commissioni su vecchio PAT)

Classe per il modello: **CommissionsModel**

Classe controller: **CommissionAdminController**



| Colonna         | Tipo          | Descrizione                                                  |
| --------------- | ------------- | ------------------------------------------------------------ |
| id              | int           | Id della commissione                                         |
| owner_id        | int           | ID dell'utente che ha creato la commissione                  |
| institution_id  | int           | ID dell'ente associato alla commissione                      |
| president_id    | int           | ID del personale presidente della commissione o gruppo       |
| attachments_id  | int           | ID dell'allegato associato alla commissione o gruppo         |
| state           | tinyint(bool) | ??                                                           |
| workflow_state  | varchar       | ??                                                           |
| name            | varchar       | Nome della commissione                                       |
| typology        | varchar       | Indica se è una commissione o un gruppo consiliare           |
| description     | varchar       | Descrizione della commissione o del gruppo                   |
| image           | varchar       | Immagine da assegnare alla commissione o gruppo              |
| phone           | varchar       | Recapito telefonico fisso della commissione o gruppo         |
| fax             | varchar       | Fax della commissione o gruppo                               |
| address         | varchar       | Indirizzo della commissione o gruppo                         |
| email           | varchar       | Recapito email della commissione o gruppo                    |
| order           | int           | Utilizzato per l'ordine di visualizzazione                   |
| archive         | tinyint(bool) | ??                                                           |
| activation_date | datetime      | Data "attiva dal" della commissione o gruppo                 |
| expiration_date | datetime      | Data "fino al" della commissione o gruppo                    |
| deleted         | tinyint(bool) | Indica se la commissione o gruppo è stata eliminata(valore 1) o meno (valore 0) |
| created_at      | datetime      | La data in cui è stata creata la commissione o gruppo        |
| updated_at      | datetime      | La data di ultima modifica della commissione o gruppo        |
| deleted_at      | datetime      | La data di eliminazione della commissione o gruppo           |



------

36. **object_company**

Tabella utilizzata per la gestione degli  **Enti e società controllate**.  (oggetto_commissioni su vecchio PAT)

Classe per il modello: **CompanyModel**

Classe controller: **CompanyAdminController**



| Colonna                  | Tipo          | Descrizione                                                  |
| ------------------------ | ------------- | ------------------------------------------------------------ |
| id                       | int           | Id della società                                             |
| owner_id                 | int           | ID dell'utente che ha creato la società                      |
| institution_id           | int           | ID dell'ente associato alla società                          |
| attachments_id           | int           | ID dell'allegato associato alla società                      |
| state                    | tinyint(bool) | ??                                                           |
| workflow_state           | varchar       | ??                                                           |
| company_name             | varchar       | Nome della società                                           |
| typology                 | varchar       | Tipologia della società                                      |
| description              | text          | Descrizione della società                                    |
| partecipation_measure    | varchar       | Misura di partecipazione della società                       |
| duration                 | varchar       | Durata dell'impegno                                          |
| year_charges             | text          | Oneri complessivi annuali della società                      |
| treatment_assignment     | text          | Incarichi amministrativi e relativo trattamento economico della società |
| website_url              | varchar       | Url del sito web della società                               |
| balance                  | text          | Risultati di bilancio (ultimi 3 mesi) della società          |
| inconferability_dec_link | varchar       | Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell'incarico (link) |
| incompatibility_dec_link | varchar       | Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell'incarico (link) |
| deleted                  | tinyint(bool) | Indica se la società è stata eliminata(valore 1) o meno (valore 0) |
| created_at               | datetime      | La data in cui è stata creata la società                     |
| updated_at               | datetime      | La data di ultima modifica della società                     |
| deleted_at               | datetime      | La data di eliminazione della società                        |



------

37. **object_contest**

Tabella utilizzata per la gestione dei  **Bandi di concorso**.  (oggetto_concorsi su vecchio PAT)

Classe per il modello: **ContestModel**

Classe controller: **ContestAdminController**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del bando di concorso                                     |
| owner_id             | int           | ID dell'utente che ha creato il bando di concorso            |
| institution_id       | int           | ID dell'ente associato al bando di concorso                  |
| attachments_id       | int           | ID dell'allegato associato al bando di concorso              |
| related_contest_id   | int           | ID del concorso o avviso relativo                            |
| object_structures_id | int           | Ufficio di riferimento del bando di concorso                 |
| state                | tinyint(bool) | ??                                                           |
| workflow_state       | varchar       | ??                                                           |
| typology             | varchar       | Tipologia del bando (concorso, avviso o esito)               |
| object               | varchar       | Oggetto del bando di concorso                                |
| province_office      | varchar       | Provincia sede di prova del bando di concorso                |
| city_office          | varchar       | Comune sede di prova del bando di concorso                   |
| office_address       | varchar       | Indirizzo sede di prova del bando di concorso                |
| activation_date      | datetime      | Data di pubblicazione del bando di concorso                  |
| expiration_date      | datetime      | Data di scadenza del bando di concorso                       |
| expiration_time      | varchar       | Orario di scadenza del bando di concorso                     |
| expected_expenditure | varchar       | Eventuale spesa prevista dal bando di concorso               |
| expenditures_made    | varchar       | Spese effettuate per il bando di concorso                    |
| heired_employees     | int           | Numero di dipendenti assunti dal bando di concorso           |
| description          | varchar       | Descrizione del bando di concorso                            |
| test_calendar        | varchar       | Calendario delle prove del bando di concorso                 |
| evaluation_criteria  | varchar       | Criteri di valutazione del bando di concorso                 |
| traces_written_tests | varchar       | Tracce prove scritte del bando di concorso                   |
| publication_state    | varchar       | Stato di pubblicazione del bando di concorso                 |
| deleted              | tinyint(bool) | Indica se il bando di concorso è stato eliminato(valore 1) o meno (valore 0) |
| created_at           | datetime      | La data in cui è stata creato il bando di concorso           |
| updated_at           | datetime      | La data di ultima modifica del bando di concorso             |
| deleted_at           | datetime      | La data di eliminazione del bando di concorso                |



------

38. **object_contests_acts**

Tabella utilizzata per la gestione dei  **Bandi di Gare e Contratti**.  (oggetto_gare_atti (bandi) su vecchio PAT)

Classe per il modello: **ContestsActAdminController**

Classe controller: **ContestsActAdminController**



| Colonna                        | Tipo          | Descrizione                                                  |
| ------------------------------ | ------------- | ------------------------------------------------------------ |
| id                             | int           | Id della bando di gara                                       |
| owner_id                       | int           | ID dell'utente che ha creato il bando di gara                |
| institution_id                 | int           | ID dell'ente associato al bando di gara                      |
| attachments_id                 | int           | ID dell'allegato associato al bando di gara                  |
| object_personnel_id            | int           | ID del personale(RUP) relativo al bando di gara              |
| object_structures_id           | int           | ID della struttura(ufficio) relativa al bando di gara, relazione con le strutture organizzative |
| object_contracting_stations_id | int           | ID della stazione associata al bando di gara                 |
| relative_procedure_id          | int           | ID della procedura relativa al bando di gara                 |
| relative_notice_id             | int           | Bando di gara relativo al lotto                              |
| notice_id                      | int           | Bando relativo all'esito di gara                             |
| state                          | tinyint(bool) | ??                                                           |
| workflow_state                 | varchar       | ??                                                           |
| type                           | varchar       | Tipo del bando di gara (Delibera a contrarre o atto equivalente, Bando di gara, Lotto, Esito di gara, Avviso, Esito/Affidamento, Liquidazione) |
| typology                       | varchar       | Tipologia del bando di gara(deliberation, notice, lot, result, alert, foster, liquidation) |
| object                         | varchar       | Oggetto del bando di gara                                    |
| cig                            | varchar       | Codice identificativo gara(CIG) del bando di gara            |
| contract                       | varchar       | Tipo contratto del bando di gara(Lavoro, Servizi, Forniture) |
| adjudicator_name               | varchar       | Nome dell'amministrazione aggiudicatrice del bando di gara   |
| adjudicator_data               | varchar       | Codice fiscale amministrazione aggiudicatrice del bando di gara |
| administration_type            | varchar       | Tipo di amministrazione aggiudicataria del bando             |
| province_office                | varchar       | Provincia sede di gara del bando di gara                     |
| municipality_office            | varchar       | Comune sede di gara del bando di gara                        |
| office_address                 | varchar       | Indirizzo sede di gara del bando di gara                     |
| istat_office                   | varchar       | Codice Istat sede di gara del bando di gara                  |
| nuts_office                    | varchar       | Codice nuts sede di gara del bando di gara                   |
| no_amount                      | varchar       | Indica se il bando di gara è senza importo(valore 1) o con importo(valore 0) |
| asta_base_value                | varchar       | Importo dell'appalto (al netto dell'IVA) del bando di gara   |
| anac_year                      | int           | Anno Anac di riferimento del bando di gara                   |
| act_date                       | datetime      | Data dell'atto relativa al bando di gara                     |
| activation_date                | datetime      | Data di pubblicazione sul sito del bando di gara             |
| expiration_date                | datetime      | Data di scadenza per la presentazione delle offerte per il bando di gara |
| guue_date                      | datetime      | Data di pubblicazione del bando di gara sulla G.U.U.E.       |
| guri_date                      | datetime      | Data di pubblicazione del bando di gara sulla G.U.R.I.       |
| cpv_code                       | varchar       | Codice CPV del bando di gara                                 |
| codice_scp                     | varchar       | Codice SCP del bando di gara                                 |
| url_scp                        | varchar       | URL di Pubblicazione su www.serviziocontrattipubblici.it del bando di gara |
| details                        | text          | Dettagli sul bando di gara                                   |
| contraent_choice               | varchar       | Tipologia scelta del contraente per il bando di gara         |
| typology_result                | varchar       | Tipologia esito per il bando di gara(tipo esito/affidamento) |
| award_amount_value             | varchar       | Valore Importo di aggiudicazione (al lordo degli oneri di sicurezza e al netto dell'IVA) del bando di gara(tipo esito/affidamento) |
| amount_liquidated              | varchar       | Valore Importo liquidato (al netto dell'IVA) del bando di gara(liquidazione) |
| publication_date_type          | varchar       | Tipologia data di pubblicazione del bando di gara(esiti/affidamenti) |
| work_start_date                | datetime      | Data inizio lavori del bando di gara(esito/affidamento)      |
| work_end_date                  | datetime      | Data di fine lavori del bando di gara(esito/affidamento)     |
| deleted                        | tinyint(bool) | Indica se il bando di gara è stato eliminato(valore 1) o meno (valore 0) |
| created_at                     | datetime      | La data in cui è stata creato il bando di gara               |
| updated_at                     | datetime      | La data di ultima modifica del bando di gara                 |
| deleted_at                     | datetime      | La data di eliminazione del bando di gara                    |



------

39. **object_contracting_stations**

Tabella utilizzata per la gestione delle  **Stazioni Appaltanti**.  (oggetto_stazioni_appaltanti su vecchio PAT)

Classe per il modello: **ContractingStationsModel**

Classe controller: **ContractingStationAdminController**



| Colonna                           | Tipo          | Descrizione                                                  |
| --------------------------------- | ------------- | ------------------------------------------------------------ |
| id                                | int           | Id della stazione appaltante                                 |
| owner_id                          | int           | ID dell'utente che ha creato la stazione appaltante          |
| institution_id                    | int           | ID dell'ente associato alla stazione appaltante              |
| attachments_id                    | int           | ID dell'allegato associato alla stazione appaltante          |
| state                             | tinyint(bool) | ??                                                           |
| workflow_state                    | varchar       | ??                                                           |
| adjudicator_name                  | varchar       | Nome dell'amministrazione aggiudicataria                     |
| contracting_authority_fiscal_code | varchar       | Codice fiscale ??                                            |
| administration_type               | varchar       | Tipo amministrazione                                         |
| province_office                   | varchar       | Provincia sede                                               |
| municipality_office               | varchar       | Comune sede                                                  |
| office_address                    | varchar       | Indirizzo sede                                               |
| deleted                           | tinyint(bool) | Indica se la stazione appaltante è stata eliminata(valore 1) o meno (valore 0) |
| created_at                        | datetime      | La data in cui è stata creata la stazione appaltante         |
| updated_at                        | datetime      | La data di ultima modifica della stazione appaltante         |
| deleted_at                        | datetime      | La data di eliminazione della stazione appaltante            |



------

40. **object_elections**

Tabella utilizzata per la gestione delle  **Elezioni Trasparenti**.  (oggetto_elezioni su vecchio PAT)

Classe per il modello: **ElectionsModel**

Classe controller: **ElectionAdminController**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id dell'elezione                                             |
| owner_id       | int           | ID dell'utente che ha creato l'elezione                      |
| institution_id | int           | ID dell'ente associato all'elezione                          |
| attachments_id | int           | ID dell'allegato associato all'elezione                      |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| name           | varchar       | Nome assegnato all'elezione                                  |
| date           | datetime      | Data dell'elezione                                           |
| deleted        | tinyint(bool) | Indica se l'elezione è stata eliminata(valore 1) o meno (valore 0) |
| created_at     | datetime      | La data in cui è stata creata l'elezione                     |
| updated_at     | datetime      | La data di ultima modifica dell'elezione                     |
| deleted_at     | datetime      | La data di eliminazione dell'elezione                        |



------

41. **object_election_candidates**

Tabella utilizzata per la gestione delle  **Candidati per le elezioni**.  (oggetto_elezioni_candidati su vecchio PAT)

Classe per il modello: **ElectionCandidatesModel**

Classe controller: **ElectionCandidateAdminController**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del candidato                                             |
| owner_id       | int           | ID dell'utente che ha creato il candidato                    |
| institution_id | int           | ID dell'ente associato al candidato                          |
| attachments_id | int           | ID dell'allegato associato al candidato                      |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| name           | varchar       | Nome del candidato                                           |
| order          | int           | Utilizzato per l'ordine di visualizzazione del candidato     |
| deleted        | tinyint(bool) | Indica se il candidato è stato eliminato(valore 1) o meno (valore 0) |
| created_at     | datetime      | La data in cui è stato creato il candidato                   |
| updated_at     | datetime      | La data di ultima modifica del candidato                     |
| deleted_at     | datetime      | La data di eliminazione del candidato                        |



------

42. **object_election_lists**

Tabella utilizzata per la gestione delle  **Liste dell'elezione**.  (oggetto_elezioni_liste su vecchio PAT)

Classe per il modello: **ElectionListsModel**

Classe controller: **ElectionListAdminController**



| Colonna                       | Tipo          | Descrizione                                                  |
| ----------------------------- | ------------- | ------------------------------------------------------------ |
| id                            | int           | Id della lista                                               |
| owner_id                      | int           | ID dell'utente che ha creato la lista                        |
| institution_id                | int           | ID dell'ente associato alla lista                            |
| attachments_id                | int           | ID dell'allegato associato alla lista                        |
| object_election_candidates_id | int           | ID del candidato associato alla lista                        |
| state                         | tinyint(bool) | ??                                                           |
| workflow_state                | varchar       | ??                                                           |
| name                          | varchar       | Nome della lista                                             |
| order                         | int           | Utilizzato per l'ordine di visualizzazione della lista       |
| deleted                       | tinyint(bool) | Indica se la lista è stata eliminata(valore 1) o meno (valore 0) |
| created_at                    | datetime      | La data in cui è stato creata la lista                       |
| updated_at                    | datetime      | La data di ultima modifica della lista                       |
| deleted_at                    | datetime      | La data di eliminazione della lista                          |



------

43. **objcet_grants**

Tabella utilizzata per la gestione delle  **Sovvenzioni e vantaggi economici**.  (oggetto_sovvenzioni su vecchio PAT)

Classe per il modello: **GrantsModel**

Classe controller: **GrantAdminController**



| Colonna                  | Tipo          | Descrizione                                                  |
| ------------------------ | ------------- | ------------------------------------------------------------ |
| id                       | int           | Id della sovvenzione                                         |
| owner_id                 | int           | ID dell'utente che ha creato la sovvenzione                  |
| institution_id           | int           | ID dell'ente associato alla sovvenzione                      |
| attachments_id           | int           | ID dell'allegato associato alla sovvenzione                  |
| object_structures_id     | int           | ID della struttura organizzativa responsabile della sovvenzione |
| object_regulations_id    | int           | ID del regolamento alla base dell'attribuzione della sovvenzione |
| grant_id                 | int           | ID della sovvenzione relativa(per le liquidazioni)           |
| state                    | tinyint(bool) | ??                                                           |
| workflow_state           | varchar       | ??                                                           |
| beneficiary_name         | varchar       | Nominativo del beneficiario della sovvenzione                |
| fiscal_data_not_avaiable | tinyint(bool) | Indica se i dati fiscali non sono disponibili(valore 1) o si(valore 0) |
| fiscal_data              | text          | Dati fiscali della sovvenzione                               |
| object                   | varchar       | Oggetto della sovvenzione                                    |
| typology                 | varchar       | Tipologia della sovvenzione(Sovvenzione, Liquidazione)       |
| type                     | varchar       | Tipo della sovvenzione(grant, liquidation)                   |
| concession_act_date      | datetime      | Data atto di concessione della sovvenzione                   |
| start_date               | datetime      | Data inizio della sovvenzione                                |
| end_date                 | datetime      | Data fine della sovvenzione                                  |
| concession_amount        | varchar       | Importo atto di concessione della sovvenzione                |
| detection_mode           | text          | Modalità seguita per l'individuazione del beneficiario della sovvenzione |
| omissis                  | tinyint(bool) | Omissis(privacy)                                             |
| reference_date           | datetime      | Data di riferimento (per liquidazioni)                       |
| compensation_paid        | varchar       | Importo del vantaggio economico corrisposto (per le liquidazioni) |
| compensation_paid_date   | int           | Anno di liquidazione(per liquidazioni)                       |
| notes                    | text          | Note sulla sovvenzione                                       |
| deleted                  | tinyint(bool) | Indica se la sovvenzione è stata eliminata(valore 1) o meno (valore 0) |
| created_at               | datetime      | La data in cui è stata creata la sovvenzione                 |
| updated_at               | datetime      | La data di ultima modifica della sovvenzione                 |
| deleted_at               | datetime      | La data di eliminazione della sovvenzione                    |



------

44. **object_help_adminui_trasparenza**

Tabella utilizzata per la gestione **??**.  (oggetto_etrasp_help_adminui su vecchio PAT)

Classe per il modello: **--**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                             |
| -------------- | ------------- | --------------------------------------- |
| id             | int           | Id del record                           |
| owner_id       | int           | ID dell'utente che ha creato il record  |
| state          | tinyint(bool) | ??                                      |
| workflow_state | varchar       | ??                                      |
| menu           | varchar       | ??                                      |
| secondary_menu | varchar       | ??                                      |
| action         | varchar       | ??                                      |
| title          | varchar       | ??                                      |
| content_type   | varchar       | ??                                      |
| content        | text          | ??                                      |
| created_at     | datetime      | La data in cui è stato creato il record |
| updated_at     | datetime      | La data di ultima modifica del record   |
| deleted_at     | datetime      | La data di eliminazione del record      |



------

45. **object_interventions**

Tabella utilizzata per la gestione delle  **Interventi straordinari e di emergenza**.  (oggetto_interventi su vecchio PAT)

Classe per il modello: **InterventionsModel**

Classe controller: **InterventionAdminController**



| Colonna           | Tipo          | Descrizione                                                  |
| ----------------- | ------------- | ------------------------------------------------------------ |
| id                | int           | Id dell'intervento                                           |
| owner_id          | int           | ID dell'utente che ha creato l'intervento                    |
| institution_id    | int           | ID dell'ente associato all'intervento                        |
| state             | tinyint(bool) | ??                                                           |
| workflow_state    | varchar       | ??                                                           |
| description       | varchar       | Descrizione dell'intervento                                  |
| derogations       | varchar       | Norme derogate e motivazione dell'intervento                 |
| time_limits       | datetime      | Termini temporali per i provvedimenti straordinari           |
| estimated_cost    | varchar       | Costo interventi stimato                                     |
| effective_cost    | varchar       | Costo interventi effettivo                                   |
| publication_state | varchar       | Sato di pubblicazione dell'intervento                        |
| deleted           | tinyint(bool) | Indica se l'intervento è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at        | datetime      | La data in cui è stato creato l'intervento                   |
| updated_at        | datetime      | La data di ultima modifica dell'intervento                   |
| deleted_at        | datetime      | La data di eliminazione dell'intervento                      |



------

46. **object_lease_canons**

Tabella utilizzata per la gestione delle  **Canoni di locazione**.  (oggetto_interventi su vecchio PAT)

Classe per il modello: **LeaseCanonsModel**

Classe controller: **CanonAdminController**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del canone di locazione                                   |
| owner_id             | int           | ID dell'utente che ha creato il canone di locazione          |
| institution_id       | int           | ID dell'ente associato al canone di locazione                |
| object_structures_id | int           | ID della struttura organizzativa scelta come ufficio referente per il contratto associata al canone |
| attachments_id       | int           | ID dell'allegato associato al canone di locazione            |
| state                | tinyint(bool) | ??                                                           |
| canon_type           | varchar       | Tipo del canone                                              |
| beneficiary          | varchar       | Informazioni sul beneficiario del canone di locazione        |
| fiscal_code          | varchar       | Partita iva o codice fiscale del beneficiario del canone di locazione |
| amount               | decimal       | Importo del canone di locazione                              |
| contract_statements  | varchar       | Estremi del contratto del canone di locazione                |
| start_date           | datetime      | Data inizio                                                  |
| end_date             | datetime      | Data fine                                                    |
| notes                | text          | Note sul canone di locazione                                 |
| deleted              | tinyint(bool) | Indica se il canone è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at           | datetime      | La data in cui è stato creato il canone di locazione         |
| updated_at           | datetime      | La data di ultima modifica del canone di locazione           |
| deleted_at           | datetime      | La data di eliminazione del canone di locazione              |



------

47. **object_mayoral_candidates_elections**

Tabella utilizzata per la gestione delle  **Candidati Sindaci**.  (oggetto_elezioni_candidati_sindaci su vecchio PAT)

Classe per il modello: **MayoralCandidatesElectionsModel**

Classe controller: **MayoralCandidateAdminController**



| Colonna             | Tipo          | Descrizione                                                  |
| ------------------- | ------------- | ------------------------------------------------------------ |
| id                  | int           | Id del candidato sindaco                                     |
| owner_id            | int           | ID dell'utente che ha creato il candidato sindaco            |
| institution_id      | int           | ID dell'ente associato al candidato sindaco                  |
| object_elections_id | int           | ID delle elezioni di appartenenza del candidato sindaco      |
| attachments_id      | int           | ID dell'allegato associato al candidato sindaco              |
| state               | tinyint(bool) | ??                                                           |
| workflow_state      | varchar       | ??                                                           |
| name                | varchar       | Nome del candidato sindaco                                   |
| order               | int           | Utilizzato per l'ordine di visualizzazione del candidato sindaco |
| deleted             | tinyint(bool) | Indica se il candidato sindaco è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at          | datetime      | La data in cui è stato creato il candidato sindaco           |
| updated_at          | datetime      | La data di ultima modifica del candidato sindaco             |
| deleted_at          | datetime      | La data di eliminazione del candidato sindaco                |



------

48. **object_measures**

Tabella utilizzata per la gestione delle  **Provvedimenti Amministrativi**.  (oggetto_provvedimenti su vecchio PAT)

Classe per il modello: **MeasuresModel**

Classe controller: **MeasureAdminController**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del provvedimento                                         |
| owner_id                | int           | ID dell'utente che ha creato il provvedimento                |
| institution_id          | int           | ID dell'ente associato al provvedimento                      |
| object_contests_acts_id | int           | ID della procedura relativa al provvedimento                 |
| attachments_id          | int           | ID dell'allegato associato al provvedimento                  |
| state                   | tinyint(bool) | ??                                                           |
| workflow_state          | varchar       | ??                                                           |
| number                  | varchar       | Numero del provvedimento                                     |
| object                  | varchar       | Oggetto del provvedimento                                    |
| type                    | varchar       | Tipologia del provvedimento                                  |
| date                    | datetime      | Data del provvedimento                                       |
| content                 | varchar       | ??                                                           |
| expense                 | varchar       | ??                                                           |
| extremes                | varchar       | ??                                                           |
| choice_of_contractor    | varchar       | ??                                                           |
| notes                   | varchar       | Note relative al provvedimento                               |
| publication_state       | varchar       | Stato di pubblicazione del provvedimento                     |
| deleted                 | tinyint(bool) | Indica se il provvedimento è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at              | datetime      | La data in cui è stato creato il provvedimento               |
| updated_at              | datetime      | La data di ultima modifica del provvedimento                 |
| deleted_at              | datetime      | La data di eliminazione del provvedimento                    |



------

49. **object_modules_regulations**

Tabella utilizzata per la gestione della  **Modulistica**.  (oggetto_modulistica_regolamenti su vecchio PAT)

Classe per il modello: **ModulesRegulationsModel**

Classe controller: **ModuleAdminController**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id della modulistica                                         |
| owner_id       | int           | ID dell'utente che ha creato la modulistica                  |
| institution_id | int           | ID dell'ente associato alla modulistica                      |
| attachments_id | int           | ID dell'allegato associato alla modulistica                  |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| typology       | varchar       | Tipologia della modulistica                                  |
| title          | varchar       | Titolo della modulistica                                     |
| description    | varchar       | Descrizione della modulistica                                |
| order          | int           | Utilizzato per l'ordine di visualizzazione della modulistica |
| deleted        | tinyint(bool) | Indica se la modulistica è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data in cui è stato creata la modulistica                 |
| updated_at     | datetime      | La data di ultima modifica della modulistica                 |
| deleted_at     | datetime      | La data di eliminazione della modulistica                    |



------

50. **object_municipality**

Tabella utilizzata per la gestione dei  **Comuni**.  (etrasp_comuni su vecchio PAT)

Classe per il modello: **MunicipalityModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del comune                                                |
| owner_id       | int           | ID dell'utente che ha creato il comune                       |
| institution_id | int           | ID dell'ente associato il comune                             |
| municipality   | varchar       | ??                                                           |
| province       | varchar       | Provincia del comune                                         |
| code           | varchar       | ??                                                           |
| deleted        | tinyint(bool) | Indica se il comune è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data in cui è stato creato il comune                      |
| updated_at     | datetime      | La data di ultima modifica del comune                        |
| deleted_at     | datetime      | La data di eliminazione del comune                           |



------

51. **object_news_notices**

Tabella utilizzata per la gestione delle  **News e Avvisi**.  (oggetto_news_avvisi su vecchio PAT)

Classe per il modello: **NewsNoticesModel**

Classe controller: **NewsNoticeAdminController**



| Colonna          | Tipo          | Descrizione                                                  |
| ---------------- | ------------- | ------------------------------------------------------------ |
| id               | int           | Id dell'avviso/news                                          |
| owner_id         | int           | ID dell'utente che ha creato l'avviso/news                   |
| institution_id   | int           | ID dell'ente associato all'avviso/news                       |
| attachments_id   | int           | ID dell'allegato associato all'avviso/news                   |
| state            | tinyint(bool) | ??                                                           |
| workflow_state   | varchar       | ??                                                           |
| news_date        | datetime      | Data avviso/news                                             |
| start_date       | datetime      | Data di inizio pubblicazione dell'avviso/news                |
| end_date         | datetime      | Data di fine pubblicazione dell'avviso/news                  |
| title            | varchar       | Titolo dell'avviso/news                                      |
| typology         | varchar       | Tipologia dell'avviso/news(Avviso, News)                     |
| evidence         | tinyint(bool) | Pubblica in evidenza                                         |
| public_in_notice | tinyint(bool) | Pubblica in bandi gare e contratti                           |
| content          | text          | Contenuto dell'avviso/news                                   |
| deleted          | tinyint(bool) | Indica se l'avviso/news è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at       | datetime      | La data in cui è stato creato l'avviso/news                  |
| updated_at       | datetime      | La data di ultima modifica dell'avviso/news                  |
| deleted_at       | datetime      | La data di eliminazione dell'avviso/news                     |



------

52. **object_normatives**

Tabella utilizzata per la gestione delle  **Normative**.  (oggetto_normativa su vecchio PAT)

Classe per il modello: **NormativesModel**

Classe controller: **NormativeAdminController**



| Colonna         | Tipo          | Descrizione                                                  |
| --------------- | ------------- | ------------------------------------------------------------ |
| id              | int           | Id della normativa                                           |
| owner_id        | int           | ID dell'utente che ha creato la normativa                    |
| institution_id  | int           | ID dell'ente associato alla normativa                        |
| attachments_id  | int           | ID dell'allegato associato alla normativa                    |
| state           | tinyint(bool) | ??                                                           |
| workflow_state  | varchar       | ??                                                           |
| name            | varchar       | Titolo della normativa                                       |
| issue_date      | datetime      | Data promulgazione della normativa                           |
| act_type        | varchar       | Tipologia atto della normativa                               |
| number          | varchar       | Numero della normativa                                       |
| protocol        | int           | Numero del protocollo della normativa                        |
| normative_link  | varchar       | Link della normativa                                         |
| normative_topic | varchar       | Argomento della normativa                                    |
| description     | varchar       | Descrizione della normativa                                  |
| deleted         | tinyint(bool) | Indica se la normativa è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at      | datetime      | La data in cui è stata creata la normativa                   |
| updated_at      | datetime      | La data di ultima modifica della normativa                   |
| deleted_at      | datetime      | La data di eliminazione della normativa                      |



------

53.  **object_normative_references**

Tabella utilizzata per la gestione delle  **??**.  (oggetto_etrasp_norma su vecchio PAT)

Classe per il modello: **NormativeReferencesModel**

Classe controller: **--**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del record                                                |
| owner_id              | int           | ID dell'utente che ha creato il record                       |
| state                 | tinyint(bool) | ??                                                           |
| workflow_state        | varchar       | ??                                                           |
| normative             | varchar       | ??                                                           |
| article_number        | varchar       | ??                                                           |
| paragraphs            | varchar       | ??                                                           |
| institutions_typology | varchar       | Tipologia ente associato                                     |
| normative_text        | varchar       | ??                                                           |
| notes                 | varchar       | ??                                                           |
| deleted               | tinyint(bool) | Indica se il record è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at            | datetime      | La data in cui è stata creata la normativa                   |
| updated_at            | datetime      | La data di ultima modifica della normativa                   |
| deleted_at            | datetime      | La data di eliminazione della normativa                      |



------

54. **object_notices_acts**

Tabella utilizzata per la gestione delle  **Atti delle amministrazioni**.  (oggetto_bandi_atti su vecchio PAT)

Classe per il modello: **NoticesActsModel**

Classe controller: **NoticesActAdminController**



| Colonna                  | Tipo          | Descrizione                                                  |
| ------------------------ | ------------- | ------------------------------------------------------------ |
| id                       | int           | Id dell'atto                                                 |
| owner_id                 | int           | ID dell'utente che ha creato l'atto                          |
| institution_id           | int           | ID dell'ente associato all'atto                              |
| attachments_id           | int           | ID dell'allegato associato all'atto                          |
| objects_contests_acts_id | int           | Procedura relativa all'atto(relazione con bandi di gara)     |
| state                    | tinyint(bool) | ??                                                           |
| workflow_state           | varchar       | ??                                                           |
| object                   | varchar       | Oggetto dell'atto                                            |
| date                     | datetime      | Data dell'atto                                               |
| details                  | varchar       | Note sull'atto                                               |
| protocol                 | int           | Numero del protocollo della normativa                        |
| deleted                  | tinyint(bool) | Indica se l'atto è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at               | datetime      | La data in cui è stata creato l'atto                         |
| updated_at               | datetime      | La data di ultima modifica dell'atto                         |
| deleted_at               | datetime      | La data di eliminazione dell'atto                            |



------

55. **object_notices_for_qualification_requirements**

Tabella utilizzata per la gestione dei  **Requisiti di qualificazione dei bandi di gara**.  (oggetto_bandi_requisiti_qualificazione su vecchio PAT)

Classe per il modello: **NoticesForQualificationRequirementsModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del requisito                                             |
| owner_id       | int           | ID dell'utente che ha creato il requisito                    |
| institution_id | int           | ID dell'ente associato al requisito                          |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| code           | varchar       | Codice del requisito                                         |
| denomination   | varchar       | Denominazione del requisito                                  |
| deleted        | tinyint(bool) | Indica se il requisito è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data in cui è stata creato il requisito                   |
| updated_at     | datetime      | La data di ultima modifica del requisito                     |
| deleted_at     | datetime      | La data di eliminazione del requisito                        |



------

56. **object_other_contents**

Tabella utilizzata per la gestione dei  **Altri contenuti**.  (oggetto_altri_contenuti su vecchio PAT)

Classe per il modello: **OtherContentsModel**

Classe controller: **--**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del record                                                |
| owner_id       | int           | ID dell'utente che ha creato il record                       |
| institution_id | int           | ID dell'ente associato al record                             |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| title          | varchar       | ??                                                           |
| content        | text          | ??                                                           |
| deleted        | tinyint(bool) | Indica se il record è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data in cui è stata creato il record                      |
| updated_at     | datetime      | La data di ultima modifica del record                        |
| deleted_at     | datetime      | La data di eliminazione del record                           |



------

57. **object_personnel**

Tabella utilizzata per la gestione del  **Personale**.  (oggetto_riferimenti su vecchio PAT)

Classe per il modello: **PersonnelModel**

Classe controller: **PersonnelAdminController**



| Colonna                | Tipo          | Descrizione                                                  |
| ---------------------- | ------------- | ------------------------------------------------------------ |
| id                     | int           | Id del personale                                             |
| owner_id               | int           | ID dell'utente che ha creato il personale                    |
| institution_id         | int           | ID dell'ente di appartenenza del personale                   |
| attachments_id         | int           | ID dell'allegato associato al personale                      |
| role_id                | int           | ID del ruolo assegnato al personale                          |
| state                  | tinyint(bool) | ??                                                           |
| workflow_state         | varchar       | ??                                                           |
| title                  | varchar       | Titolo di studio del personale                               |
| referent               | varchar       | ?????                                                        |
| full_name              | varchar       | Nome completo del personale                                  |
| fiscal_code            | varchar       | Codice fiscale del personale                                 |
| qualification          | varchar       | ???????????                                                  |
| determined_term        | tinyint(bool) | Indica se il personale ha un contratto a tempo determinato(valore 1) o meno(valore 0) |
| political_role         | varchar       | Incarico di stampo politico del personale                    |
| commissions            | varchar       | ??????????                                                   |
| political_organ        | varchar       | Organi politici-amministrativi del personale                 |
| delegation             | tinyint(bool) | ????????????                                                 |
| delegation_text        | varchar       | ?????????                                                    |
| photo                  | varchar       | Foto da allegare al personale                                |
| phone                  | varchar       | Recapito telefonico fisso del personale                      |
| mobile_phone           | varchar       | Recapito telefonico mobile del personale                     |
| fax                    | varchar       | Recapito fax del personale                                   |
| not_avaiable_email     | tinyint(bool) | Indica se l'indirizzo email del personale non è disponibile (valore a 1) o se si(valore a 0) |
| not_avaiable_email_txt | varchar       | Note sull'indirizzo email non disponibile del personale      |
| email                  | varchar       | Indirizzo email del personale                                |
| certified_email        | varchar       | Indirizzo email certificato del personale                    |
| details_conferment_act | varchar       | ?????????                                                    |
| notes                  | varchar       | Note sul personale                                           |
| compensations          | varchar       | Compensi connessi all'assunzione della carica del personale  |
| trips_import           | varchar       | Importi di viaggi di servizio e missioni del personale       |
| other_assignments      | varchar       | Altri incarichi con oneri a carico della finanza pubblica e relativi compensi del personale |
| personnel_lists        | tinyint(bool) | Utilizza negli elenchi del personale                         |
| in_office_since        | datetime      | Data di inizio incarico del personale                        |
| in_office_until        | datetime      | Data di fine incarico del personale                          |
| priority               | int           | Utilizzato per l'ordine di visualizzazione del personale     |
| other_info             | varchar       | Altre informazioni del personale                             |
| information_archive    | varchar       | Archivio informazioni del personale???                       |
| on_leave               | tinyint(bool) | ????????                                                     |
| extremes_of_conference | text          | Estremi atto di nomina o proclamazione del personale         |
| deleted                | tinyint(bool) | Indica se il personale è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at             | datetime      | La data in cui è stata creato il personale                   |
| updated_at             | datetime      | La data di ultima modifica del personale                     |
| deleted_at             | datetime      | La data di eliminazione del personale                        |



------

58.  **object_proceedings**

Tabella utilizzata per la gestione dei  **Procedimenti**.  (oggetto_procedimenti su vecchio PAT)

Classe per il modello: **ProceedingsModel**

Classe controller: **ProceedingAdminController**



| Colonna                      | Tipo          | Descrizione                                                  |
| ---------------------------- | ------------- | ------------------------------------------------------------ |
| id                           | int           | Id del procedimento                                          |
| owner_id                     | int           | ID dell'utente che ha creato il procedimento                 |
| institution_id               | int           | ID dell'ente associato al procedimento                       |
| attachments_id               | int           | ID dell'allegato associato al procedimento                   |
| state                        | tinyint(bool) | ??                                                           |
| workflow_state               | varchar       | ??                                                           |
| name                         | varchar       | Nome del procedimento                                        |
| contact                      | varchar       | Visualizzazione del Chi Contattare                           |
| description                  | varchar       | Descrizione del procedimento                                 |
| costs                        | varchar       | Costi e modalità di pagamento                                |
| silence_consent              | tinyint(bool) | Conclusione tramite silenzio assenso(valore 1) o meno (valore 0) |
| declaration                  | tinyint(bool) | Conclusione tramite dichiarazione dell'interessato (valore 1) o meno(valore 0) |
| regulation                   | varchar       | Riferimenti normativi (altro) del procedimento               |
| url_service                  | varchar       | Url per il servizio online relativo al procedimento          |
| deadline                     | varchar       | Termine di conclusione del procedimento                      |
| protection_instruments       | varchar       | Strumenti di tutela del procedimento                         |
| service_avaiable             | tinyint(bool) | Disponibilità del servizio online                            |
| publication_state            | varchar       | Stato di pubblicazione del procedimento                      |
| service_time                 | varchar       | Tempi previsti per attivazione del servizio online           |
| public_monitoring_proceeding | tinyint(bool) | ????????????                                                 |
| deleted                      | tinyint(bool) | Indica se il procedimento è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at                   | datetime      | La data in cui è stata creato il procedimento                |
| updated_at                   | datetime      | La data di ultima modifica del procedimento                  |
| deleted_at                   | datetime      | La data di eliminazione del procedimento                     |



------

59. **object_programming_acts**

Tabella utilizzata per la gestione degli  **Atti di programmazione**.  (oggetto_atti_programmazione su vecchio PAT)

Classe per il modello: **ProgrammingActsModel**

Classe controller: **ProgrammingActAdminController**



| Colonna                | Tipo          | Descrizione                                                  |
| ---------------------- | ------------- | ------------------------------------------------------------ |
| id                     | int           | Id dell'atto di programmazione                               |
| owner_id               | int           | ID dell'utente che ha creato l'atto                          |
| institution_id         | int           | ID dell'ente associato all'atto                              |
| attachments_id         | int           | ID dell'allegato associato all'atto                          |
| state                  | tinyint(bool) | ??                                                           |
| workflow_state         | varchar       | ??                                                           |
| object                 | varchar       | Oggetto dell'atto di programmazione                          |
| date                   | varchar       | Data dell'atto di programmazione                             |
| act_type               | varchar       | Tipo dell'atto di programmazione                             |
| public_in_public_works | tinyint(bool) | Indica se pubblicare l'atto in Opere pubbliche-Documenti di programmazione(valore a 1) o meno (valore a 0) |
| description            | text          | Descrizione dell'atto di programmazione                      |
| deleted                | tinyint(bool) | Indica se l'atto di programmazione è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at             | datetime      | La data in cui è stata creato l'atto di programmazione       |
| updated_at             | datetime      | La data di ultima modifica dell'atto di programmazione       |
| deleted_at             | datetime      | La data di eliminazione del procedimento                     |



------

60. **object_real_estate_asset**

Tabella utilizzata per la gestione del  **Patrimonio Immobiliare**.  (oggetto_patrimonio_immobiliare su vecchio PAT)

Classe per il modello: **RealEstateAssetModel**

Classe controller: **RealEstateAssetAdminController**



| Colonna            | Tipo          | Descrizione                                                  |
| ------------------ | ------------- | ------------------------------------------------------------ |
| id                 | int           | Id dell'immobile                                             |
| owner_id           | int           | ID dell'utente che ha creato l'immobile                      |
| institution_id     | int           | ID dell'ente associato all'immobile                          |
| attachments_id     | int           | ID dell'allegato associato all'immobile                      |
| name               | varchar       | Nome dell'immobile                                           |
| address            | varchar       | Indirizzo dell'immobile                                      |
| gross_surface      | varchar       | Superficie lorda (mq) dell'immobile                          |
| discovered_surface | varchar       | Superficie scoperta (mq) dell'immobile                       |
| sheet              | varchar       | Foglio dell'immobile                                         |
| particle           | varchar       | Particella dell'immobile                                     |
| subaltern          | varchar       | Subalterno dell'immobile                                     |
| description        | text          | Descrizione e note dell'immobile                             |
| deleted            | tinyint(bool) | Indica se l'immobile è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at         | datetime      | La data in cui è stata creato l'immobile                     |
| updated_at         | datetime      | La data di ultima modifica dell'immobile                     |
| deleted_at         | datetime      | La data di eliminazione dell'immobile                        |



------

61. **object_regulations**

Tabella utilizzata per la gestione dei  **Regolamenti e documentazione**.  (oggetto_regolamenti su vecchio PAT)

Classe per il modello: **RegulationsModel**

Classe controller: **RegulationAdminController**



| Colonna            | Tipo          | Descrizione                                                  |
| ------------------ | ------------- | ------------------------------------------------------------ |
| id                 | int           | Id del regolamento/documentazione                            |
| owner_id           | int           | ID dell'utente che ha creato il regolamento/documentazione   |
| institution_id     | int           | ID dell'ente associato al regolamento/documentazione         |
| attachments_id     | int           | ID dell'allegato associato al regolamento/documentazione     |
| description        | varchar       | Descrizione del regolamento/documentazione                   |
| state              | tinyint(bool) | ??                                                           |
| workflow_state     | varchar       | ??                                                           |
| title              | varchar       | Nome del regolamento/documentazione                          |
| issue_date         | datetime      | Data emissione del regolamento/documentazione                |
| number             | varchar       | Numero regolamento/documentazione                            |
| protocol           | int           | Numero di protocollo del regolamento/documentazione          |
| order              | int           | Utilizzato per l'ordine di visualizzazione del regolamento/documentazione |
| typology           | varchar       | Tipo del regolamento/documentazione                          |
| publication_status | varchar       | Stato di pubblicazione del regolamento/documentazione        |
| deleted            | tinyint(bool) | Indica se il regolamento/documentazione è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at         | datetime      | La data in cui è stata creato il regolamento/documentazione  |
| updated_at         | datetime      | La data di ultima modifica il regolamento/documentazione     |
| deleted_at         | datetime      | La data di eliminazione del regolamento/documentazione       |



------

62. **object_relief_checks**

Tabella utilizzata per la gestione dei  **Controlli e rilievi**.  (oggetto_controlli_rilievi su vecchio PAT)

Classe per il modello: **ReliefChecksModel**

Classe controller: **ReliefCheckAdminController**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del controllo/rilievo                                     |
| owner_id             | int           | ID dell'utente che ha creato il controllo/rilievo            |
| institution_id       | int           | ID dell'ente associato al controllo/rilievo                  |
| attachments_id       | int           | ID dell'allegato associato al controllo/rilievo              |
| objcet_structures_id | int           | ID della struttura organizzativa(ufficio) associata al controllo/rilievo |
| state                | tinyint(bool) | ??                                                           |
| workflow_state       | varchar       | ??                                                           |
| object               | varchar       | Oggetto del controllo/rilievo                                |
| date                 | datetime      | Data del controllo/rilievo                                   |
| description          | text          | Descrizione del controllo/rilievo                            |
| deleted              | tinyint(bool) | Indica se il controllo/rilievo è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il controllo/rilievo           |
| updated_at           | datetime      | La data di ultima modifica il controllo/rilievo              |
| deleted_at           | datetime      | La data di eliminazione del controllo/rilievo                |



------

63. **object_structures**

Tabella utilizzata per la gestione dei  **Strutture Organizzative**.  (oggetto_uffici su vecchio PAT)

Classe per il modello: **StructuresModel**

Classe controller: **StructureAdminController**



| Colonna                    | Tipo          | Descrizione                                                  |
| -------------------------- | ------------- | ------------------------------------------------------------ |
| id                         | int           | Id della struttura organizzativa                             |
| owner_id                   | int           | ID dell'utente che ha creato la struttura organizzativa      |
| institution_id             | int           | ID dell'ente associato alla struttura organizzativa          |
| attachments_id             | int           | ID dell'allegato associato alla struttura organizzativa      |
| structure_of_belonging_id  | int           | ID della struttura organizzativa di appartenenza             |
| state                      | tinyint(bool) | ??                                                           |
| workflow_state             | varchar       | ??                                                           |
| structure_name             | varchar       | Nome della struttura organizzativa                           |
| responsible_not_available  | tinyint(bool) | Indica se il responsabile della struttura non è disponibile(valore a 1) o si (valore a 0) |
| referent_not_available_txt | varchar       | Note sul responsabile non disponibile della struttura organizzativa |
| ad_interim                 | tinyint(bool) | Ad interim(valore 1 ) o no(valore 0)                         |
| reference_email            | varchar       | Indirizzo email                                              |
| email_not_available        | tinyint(bool) | Indirizzo email non disponibile(valore 1) o si (valore 0)    |
| email_not_available_txt    | varchar       | Note email non disponibile                                   |
| certified_email            | varchar       | Indirizzo email certificata                                  |
| phone                      | varchar       | Recapito telefonico della struttura organizzativa            |
| fax                        | varchar       | Recapito fax della struttura organizzativa                   |
| description                | varchar       | Descrizione della struttura organizzativa                    |
| articulation               | tinyint(bool) | Utilizza in Articolazione degli Uffici                       |
| headquarter_structure      | tinyint(bool) | ???????????                                                  |
| address_detail             | varchar       | Dettaglio indirizzo della struttura organizzativa (Compilare solo se l'indirizzo non è correttamente censito su Maps) |
| based_structure            | tinynit(bool) | Indica se è una struttura con sede(valore 1) o meno (valore 0) |
| address                    | varchar       | Indirizzo della struttura organizzativa                      |
| lat                        | varchar       | Latitudine della posizione della sede della struttura        |
| lon                        | varchar       | Longitudine della posizione della sede della struttura       |
| timetables                 | varchar       | Orario al pubblico della struttura                           |
| order                      | int           | Utilizzato per l'ordinamento della visualizzazione della struttura |
| description                | text          | Descrizione del controllo/rilievo                            |
| deleted                    | tinyint(bool) | Indica se la struttura organizzativa è stata eliminata(valore a 1) o meno(valore a 0) |
| created_at                 | datetime      | La data in cui è stata creato  la struttura organizzativa    |
| updated_at                 | datetime      | La data di ultima modifica  della struttura organizzativa    |
| deleted_at                 | datetime      | La data di eliminazione della struttura organizzativa        |



------

64. **object_supplie_list**

Tabella utilizzata per la gestione dei  **Elenco partecipanti/aggiudicatari**.  (oggetto_elenco_fornitori su vecchio PAT)

Classe per il modello: **SupplieListModel**

Classe controller: **SupplierAdminController**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del fornitore                                             |
| owner_id       | int           | ID dell'utente che ha creato il fornitore                    |
| institution_id | int           | ID dell'ente associato al fornitore                          |
| attachments_id | int           | ID dell'allegato associato al fornitore                      |
| state          | tinyint(bool) | ??                                                           |
| workflow_state | varchar       | ??                                                           |
| typology       | varchar       | Tipologia del fornitore(singolo, raggruppamento)             |
| name           | varchar       | Nominativo del fornitore                                     |
| vat            | varchar       | Codice fiscale del fornitore                                 |
|                |               |                                                              |
|                |               |                                                              |
|                |               |                                                              |
|                |               |                                                              |
|                |               |                                                              |
| deleted        | tinyint(bool) | Indica se il controllo/rilievo è stato eliminato(valore a 1) o meno(valore a 0) |
| created_at     | datetime      | La data in cui è stata creato il controllo/rilievo           |
| updated_at     | datetime      | La data di ultima modifica il controllo/rilievo              |
| deleted_at     | datetime      | La data di eliminazione del controllo/rilievo                |



------

65.  **password_history**

Tabella utilizzata per la gestione della **ripetizione di vecchie password** da parte dell'utente

Classe per il modello: **PasswordHistoryModel**



| Colonna    | Tipo     | Descrizione                             |
| ---------- | -------- | --------------------------------------- |
| id         | int      | Id della password                       |
| user_id    | int      | ID dell'utente associato alla password  |
| password   | varchar  | La password                             |
| created_at | datetime | La data in cui è stata creato il record |
| updated_at | datetime | La data di ultima modifica del record   |
| deleted_at | datetime | La data di eliminazione del record      |



------

66. **permits**

Tabella utilizzata per la gestione dei  **Permessi degli utenti sulle varie sezioni** (è la tabella di relazione tra acl_profiles e sections)

Classe per il modello: **PermitsModel**



| Colonna            | Tipo          | Descrizione                                                  |
| ------------------ | ------------- | ------------------------------------------------------------ |
| id                 | int           | Id del permesso                                              |
| acl_profiles_id    | int           | ID del profilo ACL associato al permesso                     |
| sections_bo_id     | int           | ID della sezione di back-office associata al permesso        |
| sections_fo_id     | int           | ID della sezione di front-office associata al permesso       |
| institution_id     | int           | ID dell'ente associato al permesso                           |
| create             | tinyint(bool) | Indica se l'utente ha il permesso di creazione sulla sezione (valore 1) o meno (valore 0) |
| read               | tinyint(bool) | Indica se l'utente ha il permesso di lettura sulla sezione (valore 1) o meno (valore 0) |
| update             | tinyint(bool) | Indica se l'utente ha il permesso di modifica sulla sezione (valore 1) o meno (valore 0) |
| delete             | tinyint(bool) | Indica se l'utente ha il permesso di eliminazione sulla sezione (valore 1) o meno (valore 0) |
| send_notify_app_io | tinyint(bool) | Indica se l'utente ha il permesso di inviare notifiche push app IO sulla sezione (valore 1) o meno (valore 0) |
| created_at         | datetime      | La data in cui è stata creato il permesso                    |
| updated_at         | datetime      | La data di ultima modifica del permesso                      |
| deleted_at         | datetime      | La data di eliminazione del permesso                         |



------

67. **recovery_password**

Tabella utilizzata per la gestione del  **Recupero password** (è la tabella di relazione tra acl_profiles e sections)

Classe per il modello: **RecoveryPassword**



| Colonna        | Tipo     | Descrizione                                           |
| -------------- | -------- | ----------------------------------------------------- |
| id             | int      | Id del record                                         |
| user_id        | int      | ID dell'utente che deve recuperare la password        |
| institution_id | int      | ID dell'ente associato al record                      |
| token          | varchar  | Token utilizzato per il processo di recupero password |
| created_at     | datetime | La data in cui è stata creato il record               |
| updated_at     | datetime | La data di ultima modifica del record                 |
| deleted_at     | datetime | La data di eliminazione del record                    |



------

68. **rel_ac_plan_personnel**

Tabella di **relazione tra object_personnel  e object_ac_plan**, utilizzata per gestire gli attori interni ed esterni, i dirigenti. 

(Campi pa_si_soggetti, pa_se_soggetti, peap_dirigenti, pap_idr_dirigenti, pap_idrp_dirigenti, pap_iert_referenti, form_personale su tabella oggetto_ac_piano su vecchio PAT.)

Classe per il modello: **RelAcPlanPersonnellModel**



| Colonna             | Tipo     | Descrizione                                                  |
| ------------------- | -------- | ------------------------------------------------------------ |
| id                  | int      | Id del record                                                |
| object_ac_plan_id   | int      | ID del piano anticorruzione                                  |
| object_personnel_id | int      | ID del personale                                             |
| typology            | varchar  | Ruolo del personale nel piano anticorruzione (attori interni ed esterni, dirigenti) |
| created_at          | datetime | La data in cui è stata creato il record                      |
| updated_at          | datetime | La data di ultima modifica del record                        |
| deleted_at          | datetime | La data di eliminazione del record                           |



------

69. **rel_ac_plan_regulations**

Tabella di **relazione tra object_regulations e object_ac_plan**, utilizzata per gestire i regolamenti in coordinamento con il ciclo delle performance. 

(Campo ccp_regolamenti, cca_ai_regolamenti sulla tabella oggetto_ac_piano su vecchio PAT)

Classe per il modello: **RelAcPlanRegulationsModel**



| Colonna             | Tipo     | Descrizione                             |
| ------------------- | -------- | --------------------------------------- |
| id                  | int      | Id del record                           |
| object_ac_plan_id   | int      | ID del piano anticorruzione             |
| object_personnel_id | int      | ID del regolamento                      |
| created_at          | datetime | La data in cui è stata creato il record |
| updated_at          | datetime | La data di ultima modifica del record   |
| deleted_at          | datetime | La data di eliminazione del record      |



------

70. **rel_ac_plan_risks**

Tabella di **relazione tra object_risks e object_ac_plan**, utilizzata per gestire le aree di rischio.

(campo gr_aree_aree su vecchio PAT sulla tabella oggetto_ac_piano)

Classe per il modello: **RelAcPlanRisksModel**



| Colonna            | Tipo     | Descrizione                             |
| ------------------ | -------- | --------------------------------------- |
| id                 | int      | Id del record                           |
| object_ac_plan_id  | int      | ID del piano anticorruzione             |
| object_ac_risks_id | int      | ID dell'area di rischio                 |
| created_at         | datetime | La data in cui è stata creato il record |
| updated_at         | datetime | La data di ultima modifica del record   |
| deleted_at         | datetime | La data di eliminazione del record      |



------

71. **rel_acl_plan_structures**

Tabella di **relazione tra object_structures e object_ac_plan**,  utilizzata per gestire le strutture organizzative coinvolte per l'individuazione dei contenuti del programma associate al piano anticorruzione.

(Campo pa_si_uffici, peap_uffici, cca_iuc_uffici su tabella oggetto_ac_piano su vecchio PAT)

Classe per il modello: **RelAcPlanStructuresModel**



| Colonna              | Tipo     | Descrizione                             |
| -------------------- | -------- | --------------------------------------- |
| id                   | int      | Id del record                           |
| object_ac_plan_id    | int      | ID del piano anticorruzione             |
| object_structures_id | int      | ID della struttura                      |
| created_at           | datetime | La data in cui è stata creato il record |
| updated_at           | datetime | La data di ultima modifica del record   |
| deleted_at           | datetime | La data di eliminazione del record      |



------

72.  **rel_ac_rotation_proceedings**

Tabella di **relazione tra object_proceedings e object_ac_rotation**,  utilizzata per le rotazioni dei procedimenti.

Classe per il modello: **RelAcRotationProceedingsModel**



| Colonna               | Tipo     | Descrizione                             |
| --------------------- | -------- | --------------------------------------- |
| id                    | int      | Id del record                           |
| object_ac_rotation_id | int      | ID della rotazione                      |
| object_proceedings_id | int      | ID del procedimento                     |
| created_at            | datetime | La data in cui è stata creato il record |
| updated_at            | datetime | La data di ultima modifica del record   |
| deleted_at            | datetime | La data di eliminazione del record      |



------

73. **rel_ac_rotation_structures**

Tabella di **relazione tra object_structures e object_ac_rotation**,  utilizzata per gestire le rotazioni degli uffici.

Classe per il modello: **RelAcRotationStructuresModel**



| Colonna               | Tipo     | Descrizione                             |
| --------------------- | -------- | --------------------------------------- |
| id                    | int      | Id del record                           |
| object_ac_rotation_id | int      | ID della rotazione                      |
| object_structures_id  | int      | ID della struttura                      |
| created_at            | datetime | La data in cui è stata creato il record |
| updated_at            | datetime | La data di ultima modifica del record   |
| deleted_at            | datetime | La data di eliminazione del record      |



------

74. **rel_assignments_measures**

Tabella di **relazione tra object_assignments e object_measures**,  utilizzata per gestire i provvedimenti associati agli incarichi.

Classe per il modello: **RelAssignmentsMeasuresModel**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del record                                                |
| object_assignments_id | int           | ID dell'incarico                                             |
| object_measures_id    | int           | ID del provvedimento                                         |
| deleted               | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at            | datetime      | La data in cui è stata creato il record                      |
| updated_at            | datetime      | La data di ultima modifica del record                        |
| deleted_at            | datetime      | La data di eliminazione del record                           |



------

75.  **rel_assignments_notices_acts**

Tabella di **relazione tra object_assignments e object_notices_acts**,  utilizzata per gestire gli atti delle amministrazioni associati agli incarichi.

Classe per il modello: **RelAssignmentsNoticesActsModel**



| Colonna                | Tipo          | Descrizione                                                  |
| ---------------------- | ------------- | ------------------------------------------------------------ |
| id                     | int           | Id del record                                                |
| object_assignments_id  | int           | ID dell'incarico                                             |
| object_notices_acts_id | int           | ID dell'atto amministrativo                                  |
| deleted                | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at             | datetime      | La data in cui è stata creato il record                      |
| updated_at             | datetime      | La data di ultima modifica del record                        |
| deleted_at             | datetime      | La data di eliminazione del record                           |



------

76.  **rel_categories_attachments**

Tabella di **relazione tra object_categories e object_attachments**,  utilizzata per gestire le categorie degli allegati.

Classe per il modello: **RelCategoriesAttachmentsModel**



| Colonna        | Tipo          | Descrizione                                                  |
| -------------- | ------------- | ------------------------------------------------------------ |
| id             | int           | Id del record                                                |
| attachments_id | int           | ID dell'allegato                                             |
| categories_id  | int           | ID della categoria                                           |
| institution_id | int           | ID dell'ente                                                 |
| archive        | varchar       | ????                                                         |
| id_archive     | int           | ID dell'archivio                                             |
| deleted        | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at     | datetime      | La data in cui è stata creato il record                      |
| updated_at     | datetime      | La data di ultima modifica del record                        |
| deleted_at     | datetime      | La data di eliminazione del record                           |



------

77. **rel_charges_measures**

Tabella di **relazione tra object_charges e object_measures**,  utilizzata per gestire i provvedimenti associati agli oneri.

Classe per il modello: **RelChargesMeasuresModel**



| Colonna            | Tipo          | Descrizione                                                  |
| ------------------ | ------------- | ------------------------------------------------------------ |
| id                 | int           | Id del record                                                |
| object_charges_id  | int           | ID dell'onere                                                |
| object_measures_id | int           | ID del provvedimento                                         |
| deleted            | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at         | datetime      | La data in cui è stata creato il record                      |
| updated_at         | datetime      | La data di ultima modifica del record                        |
| deleted_at         | datetime      | La data di eliminazione del record                           |



------

78.  **rel_charges_normatives**

Tabella di **relazione tra object_charges e object_normatives**,  utilizzata per gestire i riferimenti normativi associati agli oneri.

Classe per il modello: **RelChargesNormativesModel**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del record                                                |
| object_charges_id    | int           | ID dell'onere                                                |
| object_normatives_id | int           | ID della normativa                                           |
| deleted              | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il record                      |
| updated_at           | datetime      | La data di ultima modifica del record                        |
| deleted_at           | datetime      | La data di eliminazione del record                           |



------

79.  **rel_charges_proceedings**

Tabella di **relazione tra object_charges e object_proceedings**,  utilizzata per gestire i  procedimenti associati agli oneri.

Classe per il modello: **RelChargesProceedingsModel**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del record                                                |
| object_charges_id    | int           | ID dell'onere                                                |
| object_normatives_id | int           | ID del procedimento                                          |
| deleted              | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il record                      |
| updated_at           | datetime      | La data di ultima modifica del record                        |
| deleted_at           | datetime      | La data di eliminazione del record                           |



------

80.  **rel_charges_regulations**

Tabella di **relazione tra object_charges e object_regulations**,  utilizzata per gestire i  regolamenti associati agli oneri.

Classe per il modello: **RelChargesRegulationsModel**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del record                                                |
| object_charges_id     | int           | ID dell'onere                                                |
| object_regulations_id | int           | ID del regolamento                                           |
| deleted               | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at            | datetime      | La data in cui è stata creato il record                      |
| updated_at            | datetime      | La data di ultima modifica del record                        |
| deleted_at            | datetime      | La data di eliminazione del record                           |



------

81.  **rel_commissions_personnel**

Tabella di **relazione tra object_commissions e object_personnel_id**,  utilizzata per gestire presidente, vicepresidente, segretari e membri delle commissioni, specificandolo nel campo typology.

Classe per il modello: **RelCommissionsPersonnelModel**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del record                                                |
| object_commissions_id | int           | ID della commissione                                         |
| object_personnel_id   | int           | ID del personale                                             |
| typology              | varchar       | Indica il ruolo del personale nella commissione(presidente, vicepresidente, segretario o membro) |
| deleted               | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at            | datetime      | La data in cui è stata creato il record                      |
| updated_at            | datetime      | La data di ultima modifica del record                        |
| deleted_at            | datetime      | La data di eliminazione del record                           |



------

82.  **rel_contests_acts_contests_acts**

Tabella di **relazione tra object_contests_acts con se stessa**,  utilizzata per gestire le varie tipologie di altre procedure associate ai vari tipi di bando.

Classe per il modello: **RelContestsActsContestsActsModel**



| Colonna                  | Tipo          | Descrizione                                                  |
| ------------------------ | ------------- | ------------------------------------------------------------ |
| id                       | int           | Id del record                                                |
| object_contests_acts_id  | int           | ID del bando                                                 |
| object_contests_acts_id1 | int           | ID dell'altra procedura                                      |
| typology                 | varchar       | Indica la tipologia del bando associato (es: altre_procedure, bando_collegato ecc...) |
| deleted                  | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at               | datetime      | La data in cui è stata creato il record                      |
| updated_at               | datetime      | La data di ultima modifica del record                        |
| deleted_at               | datetime      | La data di eliminazione del record                           |



------

83.  **rel_contests_acts_supplie_list**

Tabella di **relazione tra object_contests_acts e object_supplie_list**,  utilizzata per gestire  le tipologie di fornitori associati ai vari bandi.

Classe per il modello: **RelContestsActsSupplieListModel**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del record                                                |
| object_contests_acts_id | int           | ID del bando                                                 |
| object_supplie_list_id  | int           | ID del fornitore                                             |
| typology                | varchar       | Indica la tipologia(il ruolo del fornitore: partecipante o aggiudicatario) |
| deleted                 | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at              | datetime      | La data in cui è stata creato il record                      |
| updated_at              | datetime      | La data di ultima modifica del record                        |
| deleted_at              | datetime      | La data di eliminazione del record                           |



------

84.  **rel_contests_act_requirements** 

Tabella di **relazione tra object_contests_acts e object_notices_for_qualification_requirements**,  utilizzata per gestire  i requisiti di qualificazione associati ai bandi di gara.

Classe per il modello: **RelContestsActRequirementsModel**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del record                                                |
| object_contests_acts_id | int           | ID del bando                                                 |
| object_requirement_id   | int           | ID del requisito di qualificazione                           |
| deleted                 | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at              | datetime      | La data in cui è stata creato il record                      |
| updated_at              | datetime      | La data di ultima modifica del record                        |
| deleted_at              | datetime      | La data di eliminazione del record                           |



------

85.  **rel_contest_assignments**

Tabella di **relazione tra object_contest e object_assignments**,  utilizzata per gestire la commissione giudicatrice incarichi dei bandi di concorso.

Classe per il modello: **RelContestAssignmentsModel**



| Colonna               | Tipo          | Descrizione                                                  |
| --------------------- | ------------- | ------------------------------------------------------------ |
| id                    | int           | Id del record                                                |
| object_contest_id     | int           | ID del bando di concorso                                     |
| object_assignments_id | int           | ID dell'incarico                                             |
| deleted               | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at            | datetime      | La data in cui è stata creato il record                      |
| updated_at            | datetime      | La data di ultima modifica del record                        |
| deleted_at            | datetime      | La data di eliminazione del record                           |



------

86.  **rel_grants_normatives**

Tabella di **relazione tra object_grants e object_normatives**,  utilizzata per gestire le normative associate alla sovvenzione.

Classe per il modello: **RelGrantsNormativesModel**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del record                                                |
| object_grants_id     | int           | ID della sovvenzione                                         |
| object_normatives_id | int           | ID della normativa                                           |
| deleted              | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il record                      |
| updated_at           | datetime      | La data di ultima modifica del record                        |
| deleted_at           | datetime      | La data di eliminazione del record                           |



------

87.  **rel_grants_personnel**

Tabella di **relazione tra object_grants e object_personnel**,  utilizzata per gestire i responsabili delle sovvenzioni.

Classe per il modello: **RelGrantsPersonnelModel**



| Colonna              | Tipo          | Descrizione                                                  |
| -------------------- | ------------- | ------------------------------------------------------------ |
| id                   | int           | Id del record                                                |
| object_grants_id     | int           | ID della sovvenzione                                         |
| object_normatives_id | int           | ID del personale                                             |
| deleted              | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at           | datetime      | La data in cui è stata creato il record                      |
| updated_at           | datetime      | La data di ultima modifica del record                        |
| deleted_at           | datetime      | La data di eliminazione del record                           |



------

88.   **rel_institution_type_sections_labeling**

Tabella di **relazione tra institution_type e sections**,  utilizzata per gestire le traduzioni.

Classe per il modello: **RelInstitutionTypeSectionsLabelingModel**



| Colonna             | Tipo          | Descrizione                                                  |
| ------------------- | ------------- | ------------------------------------------------------------ |
| id                  | int           | Id del record                                                |
| institution_type_id | int           | ID della sovvenzione                                         |
| sections_id         | int           | ID del personale                                             |
| label               | varchar       | Etichetta della sezione per la traduzione                    |
| deleted             | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at          | datetime      | La data in cui è stata creato il record                      |
| updated_at          | datetime      | La data di ultima modifica del record                        |
| deleted_at          | datetime      | La data di eliminazione del record                           |



------

89.  **rel_interventions_measures**

Tabella di **relazione tra object_interventions e object_measures**,  utilizzata per gestire i provvedimenti associati all'intervento.

Classe per il modello: **RelInterventionsMeasuresModel**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del record                                                |
| object_interventions_id | int           | ID dell'intervento                                           |
| object_measures_id      | int           | ID del provvedimento                                         |
| deleted                 | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at              | datetime      | La data in cui è stata creato il record                      |
| updated_at              | datetime      | La data di ultima modifica del record                        |
| deleted_at              | datetime      | La data di eliminazione del record                           |



------

90.  **rel_interventions_regulations**

Tabella di **relazione tra object_interventions e object_regulations**,  utilizzata per gestire i regolamenti associati agli interventi.

Classe per il modello: **RelInterventionsRegulationsModel**



| Colonna                 | Tipo          | Descrizione                                                  |
| ----------------------- | ------------- | ------------------------------------------------------------ |
| id                      | int           | Id del record                                                |
| object_interventions_id | int           | ID dell'intervento                                           |
| object_regulations_id   | int           | ID del regolamento                                           |
| deleted                 | tinyint(bool) | Indica se il record di relazione è stato eliminato(valore a 1) o meno (valore a 0) |
| created_at              | datetime      | La data in cui è stata creato il record                      |
| updated_at              | datetime      | La data di ultima modifica del record                        |
| deleted_at              | datetime      | La data di eliminazione del record                           |



------

91. ​     **rel_lease_canons_real_estate_asset**

