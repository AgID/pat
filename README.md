<div align="center">
 <a href="https://developers.italia.it/it/software/agid-agid-pat.html"><img src="./src/app/grafica/pat_logo_trasp_dialog.png"></a>
</div>

<div align="center">
  <a href="https://github.com/AgID/pat/blob/master/eupl_v1.2_it.pdf"><img src="https://it.wikipedia.org/wiki/European_Union_Public_Licence#/media/File:Eupl.png" alt="Licenza EUPL v1.2"></a>
</div>

# PAT - Portale Amministrazione Trasparente

La soluzione applicativa per adempiere in modo semplice ed efficace alle disposizioni del d.lgs. 33/2013 (Amministrazione Trasparente)

## Descrizione
PAT (Portale Amministrazione Trasparente) è la soluzione applicativa
dedicata alla gestione e alla pubblicazione delle informazioni richieste
dal quadro normativo che permette agli enti pubblici di ogni ordine e
grado di adempiere agli **OBBLIGHI DI PUBBLICITÀ’, TRASPARENZA E
DIFFUSIONE DELLE INFORMAZIONI ** in conformità alle disposizioni del
Decreto Legislativo del 14 marzo 2013, n.33 (Amministrazione Trasparente),
in attuazione della Legge 6 novembre 2012, n. 190 (Anticorruzione).
PAT è disponibile anche con una configurazione dedicata per le Società e
gli Enti pubblici in conformità alla delibera ANAC n. 1134 del 08/11/2017.

## Note sulla release

 * ./database 
   struttura di configurazione del DB PAT

 * ./docs
   documentazione varia sull'installazione, l'utilizzo e l'integrazione della piattaforma PAT.

 * ./install
   materiale per l'installazione della piattaforma PAT

 * ./src
   sorgenti e struttura di cartelle della piattaforma PAT

 * ./utility
   script utili per test o esecuzione esterna di funzioni

## Requisiti del sistema

 * [Apache 2.2 o maggiore] (http://httpd.apache.org/)
 * [PHP 7.x] (http://www.php.net/)
 * [MySQL 8.0 o maggiore] (http://www.mysql.com/) - in alternativa MariaDB 10.0 o maggiore (https://mariadb.org/)
 * [ISWEB 3.2 o maggiore] (http://www.isweb.it/)
 

## What's New
Release 2.0.2 - 06/08/2021

Nuove funzioni:
 - Inserita configurabilità del menu di condivisione social nel template front-office standard
 - Nuove funzioni di importazioni massive per dati excel con affidamenti e liquidazioni congiunti
 - Nuova gestione delle esclusione di funzioni dal menu backoffice, con possibilità di personalizzare singole voci 
 - Nuovo archivio dedicato agli avvisi generici pubblicabili in homepage o in altre sezioni del template front-office standard, configurabile ed attivabile da modulo
 - Nuovo archivio "Interventi straordinari e di emergenza", con archvio backoffice dedicato e visualizzazione tabellare come da normativa per il front office.
 - Nuovo layer DB compatibile PHP 7.3

Modifiche:
 - Funzioni avanzate contenuti di pagine generiche, con predisposizione per le azioni di annidamento multiplo aree di contenuto 
 - Migliorata la gestione degli header HTML per i contenuti non trovati
 - Modificata la gestione del passaggio dati nella selezione dei forniroi nell'archivio bandi di gara, al fine di consentire selezioni particolarmente numerose di partecipanti e/o aggiudicatari.
 - Sostituzione header di pubblicazione degli allegati per apertura inline in browser (configurabile da modulo)
 - Corretto un problema sull'importo totale dei bandi di gara in backoffice nel caso di presenza di più lotti
 - Gestione diretta del Logo di progetto nella configurazione avanzata
 - Fix del problema di apertura di nuovi contenuti libere sul menu di navigazione nella colonna di template front-office standard
 - Fix per apertura di collegamenti da dentro una navigazione modale in back-office
 - Fix per modulo AC sul problema della compilazione del campo "obiettivi" che impediva il corretto salvataggio
 - Possibilità di configurare il controllo di obbligatorietà sugli allegati dell'archivio personale in base alla presenza di una o più determinate etichette di file
 - Estensione delle funzioni di reset dei form di ricerca su tutti i campi disponibili nel template template front-office standard
 - Impedimento per l'autocomplete dei campi di login utente a livello browser con attributo autocomplete="off"
 - Upgrade componente Simple HTML DOM 
 - Bugfix vari



## Licenza

PAT - Portale Amministrazione Trasparente
Copyright AgID Agenzia per l'Italia Digitale

Concesso in licenza a norma dell'EUPL(la "Licenza"), versione 1.2;

Non è possibile utilizzare l'opera salvo nel rispetto
della Licenza.

È possibile ottenere una copia della Licenza al seguente
indirizzo: https://joinup.ec.europa.eu/software/page/eupl

Salvo diversamente indicato dalla legge applicabile o
concordato per iscritto, il software distribuito secondo
i termini della Licenza è distribuito "TAL QUALE",
SENZA GARANZIE O CONDIZIONI DI ALCUN TIPO,
esplicite o implicite.

Si veda la Licenza per la lingua specifica che disciplina
le autorizzazioni e le limitazioni secondo i termini della
Licenza.
