## Modello `NoticesForQualificationRequirementsModel`

Il modello `NoticesForQualificationRequirementsModel` rappresenta la tabella `object_notices_for_qualification_requirements` e fornisce funzionalità per la gestione dei requisiti di qualificazione.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_notices_for_qualification_requirements'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array dei nomi delle colonne su cui effettuare la ricerca.

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
getFullDescriptionAttribute(): string
```

Il metodo `getFullDescriptionAttribute`  restituisce la descrizione completa del requisito di qualificazione, concatenando il codice e la denominazione.
