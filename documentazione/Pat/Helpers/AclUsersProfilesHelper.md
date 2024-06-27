## Funzione `recursiveAddRowCsvAclUserProfile`

La funzione `recursiveAddRowCsvAclUserProfile` genera in modo ricorsivo le righe di un file CSV per la gestione dei profili ACL.

### Parametri

* `$object`: L'istanza della classe preposta alla generazione del file CSV.
* `$data`: L'array di dati per il log delle attività.
* `$permits`: (Opzionale) Un array di permessi.

### Valore di ritorno

La funzione non restituisce alcun valore.

---

## Funzione `badgeAclUserProfile`

La funzione `badgeAclUserProfile` genera uno snippet HTML per visualizzare un badge che indica lo stato di un profilo ACL.

### Parametri

* `$string`: (Opzionale) La stringa da utilizzare per determinare lo stato del profilo ACL.

### Valore di ritorno

La funzione restituisce uno snippet HTML che rappresenta un badge che indica lo stato del profilo ACL.

---

## Funzione `treeTableACL`

La funzione `treeTableACL` crea righe per tabelle di tipo tree per la gestione dei profili ACL.

### Parametri

* `$tree`: L'array che rappresenta la struttura ad albero dei profili ACL.
* `$permits`: (Opzionale) Un array di permessi.

### Valore di ritorno

La funzione restituisce una stringa che rappresenta le righe HTML per la tabella di tipo tree.

---

## Funzione `treeTableReadOnlyACL`

La funzione `treeTableReadOnlyACL` crea righe per tabelle di tipo tree in modalità di sola lettura per la gestione dei profili ACL.

### Parametri

* `$tree`: L'array che rappresenta la struttura ad albero dei profili ACL.
* `$permits`: (Opzionale) Un array di permessi.

### Valore di ritorno

La funzione restituisce una stringa che rappresenta le righe HTML per la tabella di tipo tree in modalità di sola lettura.
