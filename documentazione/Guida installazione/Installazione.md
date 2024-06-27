#### REQUISITI MINIMI PER IL FUNZIONAMENTO DELL'APPLICATIVO 

- Versione PHP testate: 8.0, 8.1;
- BCMath PHP Extension;
- Ctype PHP Extension;
- Fileinfo PHP extension;
- JSON PHP Extension;
- Mbstring PHP Extension;
- OpenSSL PHP Extension;
- PDO PHP Extension;
- Tokenizer PHP Extension;
- XML PHP Extension;
- Aver installato il gestore di pacchetti composer https://getcomposer.org.



#### PROCEDURA PER L'INSTALLAZIONE

- Caricare la cartella estratta dall'archivio scaricato da GitHub contenente il codice sorgente del PAT nella cartella del vostro vhost;

- Posizionarsi all'interno della cartella estratta e da terminale lanciare il seguente comando "composer install"  per installare ed aggiornare le dipendenze;

- Importare il dump sql del database che si trova nella directory /documentazione/Guida installazione/pat.sql

- Dopo aver importato il dump del database, sostituire nella colonna denominata "trasparenza_urls" della tabella "institutions" tutti i nomi di dominio raggiungibili dal PAT con quelli da voi desiderati;

- Configurare i parametri di connessione al database nel file .env contenuto nella cartella App come riportato di seguito:

  

  ```php
  # DB CONFIG
  DB_USE=default
  DB_CONNECTION=mysql
  DB_HOST=localhost
  DB_DATABASE=pat
  DB_USERNAME=root
  DB_PASSWORD=root
  ```
  
  
  
  - Valorizzare la chiave "DB_HOST" con il nome dell'hostname
  
  - Valorizzare la chiave "DB_DATABASE" con il nome del database da voi scelto
  
  - Valorizzare la chiave "DB_USERNAME" con la username di connessione al database
  
  - Valorizzare la chiave "DB_PASSWORD" con la password di connessione al database

    

- Cambiare il valore dei vari token con una stringa random con lunghezza minima di 32 caratteri come riportato di seguito:

  

  ```php
  #Token JWT
  TOKEN_JWT=jhdbfiurfdsg6rfughrkjnhfv34tyui7
  
  #Token session
  TOKEN_SESSION_KEY=fjnoishoigf8787rgfhirhgfiNGg6ytr
  TOKEN_FRINGE_PRINT=bfdih76876hfrghfahjgerdxcsdd4ert
  
  #Auth Pat
  AUTH_KEY=DFGH5rtngiokuthfiofRGTF9879ffgh6
  ```

  

  - Valorizzare le chiavi "TOKEN_JWT", "TOKEN_SESSION_KEY", "TOKEN_FRINGE_PRINT" e "AUTH_KEY" con una stringa random di almeno 32 caratteri a vostra scelta.

    

Se la procedura di installazione è andata a buon fine è ora possibile navigare nel PAT.

Per accedere all'area riservata dall'homepage del PAT, cliccare sul link ipertestuale in alto a destra  "Accedi all'area personale" ed inserire le seguenti credenziali:

- Username: **demoadmin**
- Password: ****fJKbjq9pL3kSEuAF@****

