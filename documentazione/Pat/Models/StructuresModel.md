## Modello `StructuresModel`

Il modello `StructuresModel` rappresenta la tabella `object_structures` nel database ed è utilizzato per gestire le strutture organizzative.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_structures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Il modello `StructuresModel` utilizza il trait `SearchableTrait` e definisce alcuni metodi personalizzati:

#### `__construct(array $attributes = [])`

Il costruttore della classe `StructuresModel` aggiunge la ricerca per l'ente.

#### `boot()`

Questo metodo viene eseguito durante l'avvio del modello. Aggiunge il global scope `InstitutionScope`, che applica un filtro in base all'ente di appartenenza.

#### `institution()`

Questo metodo definisce la relazione "belongsTo" con il modello `InstitutionsModel`, che rappresenta l'ente di appartenenza. La relazione è basata sulla colonna `institution_id` nella tabella `object_structures`.

#### `responsibles()`

Questo metodo definisce la relazione "belongsToMany" con il modello `PersonnelModel`, che rappresenta il personale responsabile per la struttura. La relazione è basata sulla tabella di collegamento `rel_personnel_for_structures` e filtra i record con la tipologia `'responsible'`.

#### `referents()`

Questo metodo definisce la relazione "belongsToMany" con il modello `PersonnelModel`, che rappresenta il personale referente per la struttura. La relazione è basata sulla tabella di collegamento `rel_personnel_for_structures` e filtra i record con la tipologia `'referent'` e senza archiviazione (`'archived' = 0`).

#### `personnel()`

Questo metodo definisce la relazione "belongsToMany" con il modello `PersonnelModel`, che rappresenta tutto il personale in relazione con la struttura.

#### `assets()`

Questo metodo definisce la relazione "belongsToMany" con il modello `RealEstateAssetModel`, che rappresenta tutto il patrimonio immobiliare in relazione con la struttura.

#### `structure_of_belonging()`

Questo metodo definisce la relazione "belongsTo" con il modello `StructuresModel`, che rappresenta la struttura di appartenenza.

#### `sub_structures()`

Questo metodo definisce la relazione "hasMany" con il modello `StructuresModel`, che rappresenta le strutture che appartengono alla struttura corrente.

#### `meta()`

Questo metodo definisce la relazione "hasMany" con il modello `MetaStructuresModel`, che rappresenta i dati meta associati alla struttura.

#### `created_by()`

Questo metodo definisce la relazione "belongsTo" con il modello `UsersModel`, che rappresenta l'utente che ha creato la struttura.

#### `to_contact()`

Questo metodo definisce la relazione "belongsToMany" con il modello `PersonnelModel`, che rappresenta il personale da contattare per la struttura. La relazione è basata sulla tabella di collegamento `rel_personnel_for_structures` e filtra i record con la tipologia `'toContact'`.

#### `regulations()`

Questo metodo definisce la relazione "belongsToMany" con il modello `RegulationsModel`, che rappresenta i regolamenti e i documenti validi per la struttura.

#### `measures()`

Questo metodo definisce la relazione "belongsToMany" con il modello `MeasuresModel`, che rappresenta i provvedimenti associati alla struttura.

#### `proceedings()`

Questo metodo definisce la relazione "belongsToMany" con il modello `ProceedingsModel`, che rappresenta i procedimenti associati alla struttura.

#### `normatives()`

Questo metodo definisce la relazione "belongsToMany" con il modello `NormativesModel`, che rappresenta i riferimenti normativi associati alla struttura.

#### `allNormatives()`

Questo metodo definisce la relazione "belongsToMany" con il modello `NormativesModel`, che rappresenta tutte le normative associate alla struttura, indipendentemente dalla tipologia.

#### `valid_normatives()`

Il metodo `valid_normatives()` definisce la relazione "belongsToMany" con il modello `NormativesModel`. Rappresenta le normative valide per la struttura.

#### `attachs()`

Il metodo `attachs()` definisce la relazione "hasMany" con il modello `AttachmentsModel`. Rappresenta tutti gli allegati non nascosti associati alla struttura per il front-office.

#### `all_attachs()`

Il metodo `all_attachs()` definisce la relazione "hasMany" con il modello `AttachmentsModel`. Rappresenta tutti gli allegati associati alla struttura per il back-office.

#### `scopeFilter($query, $structure, $responsible)`

Lo scope `scopeFilter()` è un metodo locale che filtra i dati delle strutture organizzative in base a un'opzione di struttura e un responsabile specifici.

#### `scopeStructureFilterDataTable($query, $structures, $responsibles)`

Lo scope `scopeStructureFilterDataTable()` è un metodo locale che filtra i dati delle strutture organizzative per l'utilizzo con DataTables.
