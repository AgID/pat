## Modello `RelMeasuresStructuresModel`

Il modello `RelMeasuresStructuresModel` rappresenta la tabella `rel_measures_structures` e rappresenta la relazione tra le tabelle `object_measures` e `object_structures`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_measures_structures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
where(string $column, mixed $value) : void
```

Questo metodo consente di effettuare una query per filtrare i risultati in base a una colonna della tabella `rel_measures_structures` e un valore specificato.
