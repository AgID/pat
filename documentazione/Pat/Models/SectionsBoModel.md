## Modello `SectionsBoModel`

Il modello `SectionsBoModel` rappresenta la tabella `section_bo` nel database ed è utilizzato per gestire le sezioni del Back-Office.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'section_bo'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Il modello `SectionsBoModel` eredita i metodi dalla classe base `Model` di Eloquent, che gestisce le operazioni di base per l'interazione con il database. Inoltre, il modello `SectionsBoModel` definisce alcuni metodi personalizzati:

#### `boot()`

Questo metodo viene eseguito durante l'avvio del modello e aggiunge il global scope `DeletedScope`, che applica un filtro per escludere i record eliminati logicamente. Ciò significa che i record contrassegnati come eliminati non verranno restituiti nelle query di default.

#### `sectionFo()`

Questo metodo definisce la relazione "hasOne" con il modello `SectionsFoModel`, che rappresenta le sezioni di front-office correlate. La relazione è basata sulla colonna `section_bo_id` nella tabella `section_bo`. Restituisce un'istanza di `HasOne`.
