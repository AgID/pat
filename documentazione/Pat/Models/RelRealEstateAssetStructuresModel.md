## Modello `RelRealEstateAssetStructuresModel`

Il modello `RelRealEstateAssetStructuresModel` rappresenta la tabella `rel_real_estate_asset_structures` e gestisce la relazione tra le tabelle `object_real_estate_asset` e `object_structures`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_real_estate_asset_structures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelRealEstateAssetStructuresModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent, che gestisce la relazione molti-a-molti tra le tabelle.
