# Class CronJob



**Riferimento path sorgente classe CronJob:** *core/System/CronJob.php*



Questa libreria ha la funzione di pianificare dei Cronjobs da lanciare tramite CLI di php. 

**Processo generale:**

Il lacio di uno scritp pianificato implica il seguente provesso generale:

- Configurare il demone crontab lanciando il file app/job.php tramite CLI di php e settando l'invio ogni minuto;
- Creare e salvare la classe da lanciare nella cartella app/Jobs/;
- Nel file app/Job.php instanziare una classe CronJob ed un metodo atto alla schedulazione della libreria appena creata nel punto precedente;
- Processare la classe CronJob con il medodo run.



**Esempio pratico processo di cronjob:** 

```php
$cronJob = new \System\CronJob(false);

$cronJob->monthly('00:00', new \Jobs\JobPackAgeDemo())
  ->everyMinute(new \Jobs\JobPackAgeDemo())
  ->minutes([11,21,31], new \Jobs\JobPackAgeDemo())
  ->run();
```

Nello snippet precedente viene instanziata la libreria CronJob, passandogli come parametro di ingresso un valore booleano, se il valore è true la librerie scrive un log con la data di inizializzazione della classe, in caso contrario la libreria viene inizializzata normalmente senza scrivere nessun log nel file system. Successivamente vengono invocati tre metodi ("monthly", "everyMinute" e "minutes"). Questi hanno la funzione di pianificare la data ed il package da lanciare,  mentre il metodo run avvia l'analisi degli orari settati dai primi tre metodi  e se il parametri impostato soddisfa il criterio di analisi nella classe vengono lanciate le librerie settate.

**Lista dei metodi della classe:**

`$job = new CronJob();`

- `$job ->sundays()`
- `$job->mondays()`
- `$job->tuesdays()`
- `$job->wednesdays()`
- `$job->thursdays()`
- `$job->fridays()`
- `$job->saturdays()`
- `$job->daily()`
- `$job->hourly()`
- `$job->monthly()`
- `$job->months()`
- `$job->days()`
- `$job->minutes()`
- `$job->everyMinute()`

#### Riferimenti della classe.

`$job = new CronJob($log);`

Nella variabile job viene instanziata la classe CronJob

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$log **(bool) - Se impostato a true ogni volta che viene instanziata la classe scrive un log all'interno della cartalle app/Logs |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | void                                                         |



------

`$job->sundays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni Domenica ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->mondays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni lunedì ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->mondays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni martedì ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->wednesdays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni mercoledì ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->thursdays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni giovedì ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->fridays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni venerdì ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->saturdays($hourMinute = '00:00', $package = false);`

Esegue lo script ogni Sabato ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`$job->daily($hourMinute = '00:00', $package = false);`

Esegue lo script tutti i giorni ad una determinata ora. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`hourly($minute = '00', $package = false);`

Esegue lo script all'inizio di ogni ora e al minuto specificato. Default 00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$minute**(string) - Setta i minuti  per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`monthly($hourMinute = '00:00', $package = false);`

Esegue lo script all'inizio di ogni ora e al minuto specificato. Default 00:00

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$hourMinute**(string) - Setta l'ora ed i minuti per il quale lo script deve essere lanciato<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |



------

`months($days = null, $package = false);`

Lo script viene eseguito tutti i mesi e con dei giorni prestabiliti.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$days**(array) - Matrice con il numero dei giorni per i quali deve essere lanciato lo script<br />**$package**(object) - La classe da lanciare se il criterio di interrogazione viene soddisfatto. |
| **Ritorno**         | This                                                         |
| **Tipo di ritorno** | Object                                                       |

