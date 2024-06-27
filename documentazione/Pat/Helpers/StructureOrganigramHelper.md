## Funzione `treeHtmlStructures`

La funzione `treeHtmlStructures` crea il codice HTML degli elementi per la struttura ad albero dell'organigramma delle strutture.

### Parametri

* `$tree`: Un array delle strutture.

### Valore di ritorno

La funzione restituisce una stringa che rappresenta il codice HTML degli elementi per la struttura ad albero.

### Eccezioni

La funzione può generare un'eccezione di tipo `Exception`.

```
// Esempio di utilizzo della funzione treeHtmlStructures
$tree = [
    [
        'structure_of_belonging_id' => null,
        'structure_name' => 'Struttura 1',
        'id' => 1,
        'children' => [
            [
                'structure_of_belonging_id' => 1,
                'structure_name' => 'Struttura 1.1',
                'id' => 2,
                'children' => [
                    [
                        'structure_of_belonging_id' => 2,
                        'structure_name' => 'Struttura 1.1.1',
                        'id' => 3,
                        'children' => []
                    ]
                ]
            ],
            [
                'structure_of_belonging_id' => 1,
                'structure_name' => 'Struttura 1.2',
                'id' => 4,
                'children' => []
            ]
        ]
    ],
    [
        'structure_of_belonging_id' => null,
        'structure_name' => 'Struttura 2',
        'id' => 5,
        'children' => []
    ]
];

$html = treeHtmlStructures($tree);
echo $html;
```

## Funzione `treeStructures`

La funzione `treeStructures` crea la struttura ad albero delle strutture per l'organigramma, basandosi sulla relazione parent_id (structure_of_belonging_id).

### Parametri

* `$organigram`: Un array o una collezione di strutture.

### Valore di ritorno

La funzione restituisce un array che rappresenta la struttura ad albero delle strutture.
