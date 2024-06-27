## Modello `RelAttachmentArchiveLabelModel`

Il modello `RelAttachmentArchiveLabelModel` rappresenta la tabella `rel_attachment_label_archive` e fornisce funzionalità per la gestione delle relazioni tra le tabelle `attachment_label` e `archive`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_attachment_label_archive'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
label(): HasOne
```

Il metodo `label` definisce la relazione con il modello `AttachmentLabelModel`, rappresentante l'etichetta dell'allegato. La relazione è di tipo uno-a-uno.
