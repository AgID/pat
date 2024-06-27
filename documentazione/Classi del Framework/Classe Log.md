# Classe per la scrittura dei log sul File System

**Riferimento path sorgente classe Log:** *core/System/Log.php*

La Classe Log integrata nel  Framework, consente di scrivere messaggi nei file di registro. Questa funzionalità premette di scrivere tre livelli di log:

- **Messaggi di pericolo (danger)** :  Scrive un messaggio di pericolo nei file di registro.
- **Messaggi di avvertimento (warning).** Scrive un messaggio di avvertimento nei file di registro.
- **Messaggi informativi (info)** . Scrive un messaggio informativo nei file di registro,

I files di registro vengono salvati nella directory "app/Logs", con estenzione *.php, questo per evitare un accesso diretto ai logs da utenti mal intenzionati.

Tutti i file (log) di registro, vengono raggruppati in base alla data della scrittura del file,  il tipo di livello associato ed ordinati in modo decrescente, in base alla data di scrittura.

I logs vengono salvati nel File System nel seguente formato: nome etichetta livello (**danger** oppure **warning** oppure **info** ), data di scrittura del log in formato (**aaaa**-**mm**-**dd**), come nel seguente esempio:
`[LIVELLO] - 2020-08-31.php`



#### Riferimenti della classe.

`\System\Log::info([$log = '']);`

```php
// Scrittura di un log di tipo informativo 
\System\Log::info([$log = '']);
```

| Settaggi            | Descrizione                                         |
| ------------------- | --------------------------------------------------- |
| **Parametri**       | **$log** (mix) – valore da scrivere sul file log    |
| **Ritorno**         | Istanza alla classe Log  (metodo di concatenamento) |
| **Tipo di ritorno** | Log                                                 |

Esempio:

```php
// Scrittura di un log di tipo informatico 
// Data di scrittura 31-08-2020

// Primo file log scritto
\System\Log::info('Testo PRIMO file log scritto');

// Secondo file log scritto
\System\Log::info('Testo SECONDO file log scritto');

/*
I File logs verrano scritti in app/config/[INFO] - 2020-09-01.php
i logs verranno impaginati in questo modo.
*/
[INFO - 2020-09-01 07:47:10]: Testo PRIMO file log scritto
[INFO - 2020-09-01 07:47:10]: Testo SECONDO file log scritto

```



------

`\System\Log::warning([$log = '']);`

```php
// Scrittura di un log di tipo informativo 
\System\Log::warning([$log = '']);
```

| Settaggi            | Descrizione                                         |
| ------------------- | --------------------------------------------------- |
| **Parametri**       | **$log** (mix) – valore da scrivere sul file log    |
| **Ritorno**         | Istanza alla classe Log  (metodo di concatenamento) |
| **Tipo di ritorno** | Log                                                 |

Esempio:

```php
// Scrittura di un log di tipo informatico 
// Data di scrittura 01-09-2020

// Primo file log scritto
\System\Log::warning('Testo PRIMO file log scritto');

// Secondo file log scritto
\System\Log::warning('Testo SECONDO file log scritto');

/*
I File logs verrano scritti in app/config/[WARNING] - 2020-09-01.php
i logs verranno impaginati in questo modo.
*/
[WARNING - 2020-09-01 07:51:48]: Testo PRIMO file log scritto
[WARNING - 2020-09-01 07:51:48]: Testo SECONDO file log scritto

```



------

`\System\Log::danger([$log = '']);`

```php
// Scrittura di un log di tipo informativo 
\System\Log::danger([$log = '']);
```

| Settaggi            | Descrizione                                         |
| ------------------- | --------------------------------------------------- |
| **Parametri**       | **$log** (mix) – valore da scrivere sul file log    |
| **Ritorno**         | Istanza alla classe Log  (metodo di concatenamento) |
| **Tipo di ritorno** | Log                                                 |

Esempio:

```php
// Scrittura di un log di tipo informatico 
// Data di scrittura 01-09-2020

// Primo file log scritto
\System\Log::danger('Testo PRIMO file log scritto');

// Secondo file log scritto
\System\Log::danger('Testo SECONDO file log scritto');

/*
I File logs verrano scritti in app/config/[DANGER] - 2020-09-01.php
i logs verranno impaginati in questo modo.
*/
[DANGER - 2020-09-01 07:55:09]: Testo PRIMO file log scritto
[DANGER - 2020-09-01 07:55:09]: Testo SECONDO file log scritto

```

