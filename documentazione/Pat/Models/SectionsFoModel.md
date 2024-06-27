## Modello `SectionsFoModel`

Il modello `SectionsFoModel` rappresenta la tabella `section_fo` nel database ed è utilizzato per la gestione delle sezioni del Front-Office.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'section_fo'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Il modello `SectionsFoModel` utilizza il trait `SearchableTrait` e definisce alcuni metodi personalizzati:

#### `boot()`

Questo metodo viene eseguito durante l'avvio del modello.

#### `scopeInstitution($query)`

Questo metodo è uno scope locale per filtrare i dati in base all'ente. Applica un filtro alle sezioni del sistema (`is_system = 1`) o alle sezioni appartenenti all'ente corrente (`institution_id` corrispondente all'ente corrente).

#### `normatives()`

Questo metodo definisce la relazione "belongsToMany" con il modello `NormativeReferencesModel`, che rappresenta i riferimenti normativi. La relazione è basata sulla tabella di collegamento `rel_normative_references_sections_fo`.

#### `institution()`

Questo metodo definisce la relazione "belongsTo" con il modello `InstitutionsModel`, che rappresenta l'ente di appartenenza. La relazione è basata sulla colonna `institution_id` nella tabella `section_fo`.

#### `parent()`

Questo metodo definisce la relazione "belongsTo" con il modello `SectionsFoModel`, che rappresenta la sezione padre. La relazione è basata sulla colonna `parent` nella tabella `section_fo`.

#### `created_by()`

Questo metodo definisce la relazione "belongsTo" con il modello `UsersModel`, che rappresenta l'utente che ha creato la pagina generica. La relazione è basata sulla colonna `owner_id` nella tabella `section_fo`.

#### `contents()`

Questo metodo definisce la relazione "hasMany" con il modello `ContentSectionFoModel`, che rappresenta i paragrafi della sezione. La relazione è basata sulla colonna `section_fo_id` nella tabella `content_section_fo`.

#### `labels()`

Questo metodo definisce la relazione "hasMany" con il modello `RelInstitutionTypeSectionsLabelingModel`, che rappresenta le etichette delle sezioni in base al tipo di ente. La relazione è basata sulla colonna `sections_id` nella tabella `rel_institution_type_sections_labeling`.

#### `scopeContentFilter($query, $term)`

Questo metodo è uno scope locale per filtrare i dati relativi ai paragrafi in base a una stringa di ricerca nel contenuto di un paragrafo.

#### `scopeLabelFilter($query, $institutionTypeId)`

Questo metodo è uno scope locale per filtrare i dati in base al tipo di ente. Applica un filtro alle traduzioni delle sezioni in base al tipo di ente.
