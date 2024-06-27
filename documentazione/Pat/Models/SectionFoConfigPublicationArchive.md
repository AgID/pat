## `SectionFoConfigPublicationArchive`

Il modello `SectionFoConfigPublicationArchive` rappresenta la tabella `section_fo_config_publication_archive` nel database ed è utilizzato per la gestione della pubblicazione degli archivi nelle sezioni del front-office.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'section_fo_config_publication_archive'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Il modello `SectionFoConfigPublicationArchive` eredita i metodi dalla classe base `Model` di Eloquent, che gestisce le operazioni di base per l'interazione con il database. Inoltre, il modello `SectionFoConfigPublicationArchive` definisce alcuni metodi personalizzati:

#### `boot()`

Questo metodo viene eseguito durante l'avvio del modello e aggiunge il global scope `DeletedScope`, che applica un filtro per escludere i record eliminati logicamente. Questo significa che i record contrassegnati come eliminati non verranno restituiti nelle query di default. È possibile disabilitare il global scope temporaneamente utilizzando il metodo `withoutGlobalScope`.

#### `section()`

Questo metodo definisce la relazione "belongsTo" con il modello `SectionsFoModel`, che rappresenta le sezioni di front-office. La relazione è basata sulla colonna `section_fo_id` nella tabella `section_fo_config_publication_archive`. Restituisce un'istanza di `BelongsTo`.

#### `scopeLabelFilter($query, $institutionTypeId)`

Questo metodo è uno scope locale che filtra le query in base all'eventuale traduzione delle sezioni in base al tipo di ente. Prende due argomenti:

* `$query`: L'oggetto di query Builder.
* `$institutionTypeId`: L'ID del tipo di ente per il filtro.
