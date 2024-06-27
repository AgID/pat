# Classe per la creazione di template (View)

**Riferimento path sorgente classe upload:** *core/System/View.php*

Il framework mette a disposizione una classe per la creazione di viste.



**Lista dei metodi della classe View per la creazione di template:**

- `create()`
- `display()`
- `render()`
- `partial()`



#### Riferimenti per la classe:

`create($path, $vars = [], $theme = false)`



Questo metodo è utilizzato per la creazione di un template con il path specificato nel parametro `$path`, i dati passati nel secondo parametro `$vars` e con il tema passato nel terzo e ultimo parametro `$theme`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$path** (string) - Template da richiamare<br />**$vars** (array) - I dati da passare alla vista<br />**$theme** (string) - Il tema da utilizzare, se non specificato viene utilizzato quello di default |
| **Ritorno**         | La vista creata                                              |
| **Tipo di ritorno** | Istanza della classe View                                    |

Esempio:

```php
View::create('email/lost_password/index', $data);
```



------

`display()`



Questo metodo permette la visualizzazione del template.

| Settaggi            | Descrizione            |
| ------------------- | ---------------------- |
| **Parametri**       |                        |
| **Ritorno**         | Stampa la vista creata |
| **Tipo di ritorno** | HTML                   |

Esempio:

```php
$view = View::create('email/lost_password/index', $data)
$view->display();

// Mostrera a schermo la vista creata
```



------

`render()`



Questa funzione restituisce il template creato sotto forma di stringa memorizzabile  in una variabile.

| Settaggi            | Descrizione                      |
| ------------------- | -------------------------------- |
| **Parametri**       |                                  |
| **Ritorno**         | La vista creata in una variabile |
| **Tipo di ritorno** | string                           |

Esempio:

```php
$view = View::create('email/lost_password/index', $data)
$template = $view->render();
echo $template;

// Il template viene memorizzato nella variabile $template
```

