# Librerie di terze parti utilizzate da PAT

Questo documento elenca le dipendenze dirette dichiarate tramite Composer e le librerie incorporate nel repository che risultano effettivamente utilizzate dall'applicazione.

Per le dipendenze Composer, `composer.json` definisce i vincoli supportati mentre `composer.lock` identifica le versioni risolte e le dipendenze transitive. Le versioni riportate di seguito sono quelle attualmente registrate nel lock file.

## Dipendenze PHP gestite con Composer

### Dipendenze di runtime

| Pacchetto | Vincolo | Versione risolta | Licenza | Utilizzo |
|---|---:|---:|---|---|
| [nesbot/carbon](https://github.com/briannesbitt/Carbon) | `^2.67.0` | `2.73.0` | MIT | Gestione di date e orari |
| [filp/whoops](https://github.com/filp/whoops) | `^2.15.2` | `2.18.5` | MIT | Gestione e rappresentazione degli errori |
| [jenssegers/agent](https://github.com/jenssegers/agent) | `^2.6.4` | `2.6.4` | MIT | Rilevamento di browser e dispositivo |
| [maer/config](https://github.com/magnus-eriksson/config) | `^2.0.1` | `2.0.1` | MIT | Caricamento della configurazione applicativa |
| [illuminate/database](https://github.com/illuminate/database) | `^8.83.27` | `8.83.27` | MIT | ORM e accesso al database |
| [illuminate/events](https://github.com/illuminate/events) | `^8.83.27` | `8.83.27` | MIT | Gestione degli eventi richiesta dai componenti Illuminate |
| [illuminate/pagination](https://github.com/illuminate/pagination) | `^8.83.27` | `8.83.27` | MIT | Paginazione dei risultati |
| [studio-42/elfinder](https://github.com/Studio-42/elFinder) | `^2.1.70` | `2.1.70` | BSD-3-Clause | Gestione dell'archivio file |
| [firebase/php-jwt](https://github.com/firebase/php-jwt) | `^7.0.0` | `7.2.0` | BSD-3-Clause | Gestione dei token JWT usati internamente dall'applicazione |

### Dipendenze di sviluppo

| Pacchetto | Vincolo | Versione risolta | Licenza | Utilizzo |
|---|---:|---:|---|---|
| [phpcompatibility/php-compatibility](https://github.com/PHPCompatibility/PHPCompatibility) | `^9.3` | `9.3.5` | LGPL-3.0-or-later | Controlli statici di compatibilità PHP |

PHPCompatibility utilizza transitivamente [squizlabs/php_codesniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer), registrato nel lock file. Le altre dipendenze transitive non sono elencate come dipendenze dirette: il relativo inventario completo è mantenuto da `composer.lock`.

## Componenti PHP incorporati nel repository

| Componente | Percorso principale | Licenza | Utilizzo |
|---|---|---|---|
| [CodeIgniter 3](https://github.com/bcit-ci/CodeIgniter) | `core/compat/`, `core/Helpers/`, `core/System/` | MIT | Componenti di compatibilità e funzioni di base derivate da CodeIgniter |
| [FuelPHP](https://github.com/fuel/fuel) | `core/System/Arr.php`, `core/System/Response.php` | MIT | Gestione di array e response |
| [Password Hashing API](https://github.com/defuse/password-hashing) | `core/System/Password.php` | BSD-2-Clause | Compatibilità per hashing e verifica delle password |

## Librerie frontend incorporate nel repository

Le librerie seguenti sono distribuite sotto `assets/` e sono caricate dai layout o dalle viste applicative indicate.

| Libreria | Percorso | Licenza | Utilizzo principale |
|---|---|---|---|
| [AdminLTE](https://github.com/ColorlibHQ/AdminLTE) | `assets/admin/` | MIT | Layout del back-office |
| [Bootstrap](https://github.com/twbs/bootstrap) | `assets/common/bootstrap/` | MIT | Componenti grafici del back-office |
| [Bootstrap Italia](https://github.com/italia/bootstrap-italia) | `assets/frontend/` | BSD-3-Clause | Componenti grafici del front-office |
| [Alpine.js](https://github.com/alpinejs/alpine) | `assets/common/alpine.js` | MIT | Interazioni del back-office |
| [jQuery](https://github.com/jquery/jquery) | `assets/common/jquery/` | MIT | Supporto ai componenti legacy e ai plugin frontend |
| [jQuery UI](https://github.com/jquery/jquery-ui) | `assets/common/jquery-ui-1.13.2/`, `assets/common/jquery-ui/` | MIT | Componenti utilizzati dall'archivio file e da viste legacy |
| [jQuery Form](https://github.com/jquery-form/form) | `assets/common/ajax/jquery.form.min.js` | LGPL-2.1 | Invio AJAX dei form |
| [jquery-confirm](https://github.com/craftpip/jquery-confirm) | `assets/common/confirm/` | MIT | Finestre di conferma e messaggi modali |
| [DataTables](https://github.com/DataTables/DataTablesSrc) | `assets/common/datatables/`, `assets/common/datatables-bs4/`, `assets/common/datatables-responsive/`, `assets/common/datatables-buttons/` | MIT | Tabelle paginate e responsive del back-office |
| [CKEditor 4](https://github.com/ckeditor/ckeditor4) | `assets/common/ckeditor4/` | GPL-2.0-or-later / LGPL-2.1-or-later / MPL-1.1 | Editor dei contenuti testuali |
| [autoNumeric](https://github.com/autoNumeric/autoNumeric) | `assets/common/autonumeric/` | MIT | Formattazione e gestione dei campi numerici |
| [Select2](https://github.com/select2/select2) | `assets/common/select2/`, `assets/common/select2-bootstrap4-theme/` | MIT | Campi select avanzati |
| [Tom Select](https://github.com/orchidjs/tom-select) | `assets/common/tom-select/` | Apache-2.0 | Campi select avanzati nei layout applicativi |
| [SweetAlert2](https://github.com/sweetalert2/sweetalert2) | `assets/common/sweetalert2/` | MIT | Messaggi e finestre di dialogo |
| [jsTree](https://github.com/vakata/jstree) | `assets/common/jsTree/` | MIT | Albero delle pagine generiche |
| [jquery-simple-tree-table](https://github.com/kanety/jquery-simple-tree-table) | `assets/common/tree-table/` | MIT | Tabelle gerarchiche dei profili ACL |
| [Leaflet](https://github.com/Leaflet/Leaflet) | `assets/common/leaflet/` | BSD-2-Clause | Mappe nelle strutture organizzative |
| [elFinder](https://github.com/Studio-42/elFinder) | `assets/common/elfinder/` | BSD-3-Clause | Interfaccia dell'archivio file |
| [jQuery DateTimePicker](https://github.com/xdan/datetimepicker) | `assets/common/datetimepicker/` | MIT | Selezione di data e ora nei form che la prevedono |
| [EditArea](https://github.com/gzussa/gwt-editarea) | `assets/common/edit_area/` | LGPL / BSD / Apache | Editor del codice nelle impostazioni dell'ente |
| [QRCode.js](https://github.com/davidshimjs/qrcodejs) | `assets/common/qrcode/` | MIT | Generazione del QR code per la configurazione MFA |
| [Font Awesome](https://github.com/FortAwesome/Font-Awesome) | `assets/common/fontawesome-free-5.15.4-web/`, `assets/common/fontawesome-6/` | CC-BY-4.0 / OFL-1.1 / MIT | Icone dell'interfaccia |
| [Ionicons](https://github.com/ionic-team/ionicons) | `assets/common/ionicons-v2.0.1-master/` | MIT | Icone del back-office |
| [iCheck Bootstrap](https://github.com/bantikyan/icheck-bootstrap) | `assets/common/icheck-bootstrap/` | MIT | Stile di checkbox e radio button |
| [Swiper](https://github.com/nolimits4web/swiper) | `assets/frontend/` | MIT | Componenti scorrevoli del front-office |
| [Popper](https://github.com/floating-ui/floating-ui) | `assets/frontend/` | MIT | Posizionamento dei componenti Bootstrap Italia |
| [jquery.fileDownload](https://github.com/johnculviner/jquery.fileDownload) | `assets/common/download/` | MIT | Gestione dei download avviati dal browser |
| [detectIncognito](https://github.com/Joe12387/detectIncognito) | `assets/common/detect-private-mode.js` | MIT | Rilevamento della navigazione privata nella pagina di autenticazione |

Per ogni voce dell'inventario è presente una cartella dedicata in `documentazione/Third-party-libraries/`, contenente il relativo `readme.md` e il file `LICENSE`. Le licenze e gli avvisi di copyright inclusi con gli asset restano inoltre nei rispettivi file e directory. Questo inventario non considera librerie semplicemente presenti in vecchia documentazione o in cartelle non richiamate dal codice applicativo.
