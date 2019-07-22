

-- Dump della struttura di tabella pat.etrasp_enti
DROP TABLE IF EXISTS `etrasp_enti`;
CREATE TABLE IF NOT EXISTS `etrasp_enti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `data_creazione` int(11) unsigned NOT NULL,
  `id_creatore` int(11) unsigned NOT NULL,
  `data_attivazione` int(11) unsigned NOT NULL,
  `data_scadenza` int(11) unsigned DEFAULT NULL,
  `nome_completo_ente` varchar(255) COLLATE latin1_bin NOT NULL,
  `nome_breve_ente` varchar(30) COLLATE latin1_bin NOT NULL,
  `tipo_ente` int(11) unsigned DEFAULT NULL,
  `url_etrasparenza` varchar(150) COLLATE latin1_bin NOT NULL,
  `url_sitoistituzionale` varchar(150) COLLATE latin1_bin NOT NULL,
  `url_albopretorio` varchar(150) COLLATE latin1_bin NOT NULL,
  `url_cmscollegato` varchar(50) COLLATE latin1_bin DEFAULT NULL,
  `url_social_facebook` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `url_social_twitter` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `url_social_youtube` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `url_social_google` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `url_social_flickr` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `file_logo_semplice` varchar(255) COLLATE latin1_bin NOT NULL,
  `file_logo_etrasp` varchar(255) COLLATE latin1_bin NOT NULL,
  `cookie_dominio` varchar(80) COLLATE latin1_bin NOT NULL DEFAULT '',
  `cookie_nome` varchar(50) COLLATE latin1_bin NOT NULL DEFAULT '',
  `canale_opendata` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `oggetto_provvedimenti` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_bandi_gara` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_incarichi` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_sovvenzioni` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_normativa` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_concorsi` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `oggetto_tabelle_avcp` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `mostra_data_aggiornamento` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `personale_ruoli` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_amm_agg` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_cod_fisc` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_tipo_amm` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_prov` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_comune` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `bandi_gara_indirizzo` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '',
  `indirizzo_via` varchar(100) COLLATE latin1_bin NOT NULL,
  `indirizzo_cap` varchar(10) COLLATE latin1_bin NOT NULL,
  `indirizzo_comune` varchar(50) COLLATE latin1_bin NOT NULL,
  `indirizzo_provincia` varchar(50) COLLATE latin1_bin NOT NULL,
  `telefono` varchar(50) COLLATE latin1_bin NOT NULL,
  `email` varchar(150) COLLATE latin1_bin NOT NULL,
  `email_certificata` varchar(150) COLLATE latin1_bin NOT NULL,
  `responsabile_pubblicazione` varchar(150) COLLATE latin1_bin NOT NULL DEFAULT '',
  `p_iva` varchar(100) COLLATE latin1_bin NOT NULL DEFAULT '',
  `testo_welcome` text COLLATE latin1_bin,
  `testo_footer` text COLLATE latin1_bin,
  `modulo_webservice` tinyint(3) NOT NULL DEFAULT '0',
  `aggiorna_avcp` tinyint(3) NOT NULL DEFAULT '1',
  `google_analitycs` varchar(500) COLLATE latin1_bin DEFAULT NULL,
  `file_organigramma` varchar(255) COLLATE latin1_bin DEFAULT NULL,
  `id_ente_albo` int(11) DEFAULT NULL,
  `url_etrasparenza_multidominio` varchar(255) COLLATE latin1_bin DEFAULT NULL,
  `chiave_webservice` varchar(255) COLLATE latin1_bin DEFAULT NULL,
  `indicizzabile` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.etrasp_moduli
DROP TABLE IF EXISTS `etrasp_moduli`;
CREATE TABLE IF NOT EXISTS `etrasp_moduli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ente` int(11) DEFAULT NULL,
  `data_attivazione` int(11) DEFAULT NULL,
  `modulo` varchar(255) DEFAULT NULL,
  `attivo` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.etrasp_paragrafi
DROP TABLE IF EXISTS `etrasp_paragrafi`;
CREATE TABLE IF NOT EXISTS `etrasp_paragrafi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_sezione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` int(11) unsigned NOT NULL DEFAULT '0',
  `stato` tinyint(1) NOT NULL DEFAULT '1',
  `id_proprietario` int(11) unsigned NOT NULL DEFAULT '0',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(125) NOT NULL DEFAULT '',
  `contenuto` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.etrasp_ruoli
DROP TABLE IF EXISTS `etrasp_ruoli`;
CREATE TABLE IF NOT EXISTS `etrasp_ruoli` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_ente` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(125) NOT NULL DEFAULT '',
  `descrizione` longtext,
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `utenti` tinyint(1) unsigned NOT NULL,
  `ruoli` tinyint(1) unsigned NOT NULL,
  `archiviomedia` tinyint(1) unsigned NOT NULL,
  `gestione_workflow` tinyint(1) unsigned DEFAULT NULL,
  `strutture` text NOT NULL,
  `personale` text NOT NULL,
  `commissioni` text NOT NULL,
  `societa` text NOT NULL,
  `procedimenti` text NOT NULL,
  `regolamenti` text NOT NULL,
  `modulistica` text NOT NULL,
  `normativa` text NOT NULL,
  `bilanci` text NOT NULL,
  `fornitori` text NOT NULL,
  `bandigara` text NOT NULL,
  `avcp` text NOT NULL,
  `bandiconcorso` text NOT NULL,
  `sovvenzioni` text NOT NULL,
  `incarichi` text NOT NULL,
  `provvedimenti` text NOT NULL,
  `oneri` text NOT NULL,
  `contenuti` text NOT NULL,
  `speciali` text NOT NULL,
  `ealbo_import` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetti_notifiche_push
DROP TABLE IF EXISTS `oggetti_notifiche_push`;
CREATE TABLE IF NOT EXISTS `oggetti_notifiche_push` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ente` int(10) unsigned NOT NULL,
  `id_oggetto` int(10) unsigned NOT NULL,
  `id_documento` int(10) unsigned NOT NULL,
  `data` int(10) unsigned DEFAULT NULL,
  `testo_notifica_push` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetti_notifiche_push_devices
DROP TABLE IF EXISTS `oggetti_notifiche_push_devices`;
CREATE TABLE IF NOT EXISTS `oggetti_notifiche_push_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(500) DEFAULT NULL,
  `tokid` varchar(500) DEFAULT NULL,
  `regid` varchar(500) DEFAULT NULL,
  `type` varchar(50) DEFAULT '',
  `app_version` varchar(80) DEFAULT '',
  `id_ente` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetti_strutture_webservice
DROP TABLE IF EXISTS `oggetti_strutture_webservice`;
CREATE TABLE IF NOT EXISTS `oggetti_strutture_webservice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `struttura` text NOT NULL,
  `id_oggetto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.

CREATE TABLE `oggetto_allegati` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`stato` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`stato_workflow` VARCHAR(50) NOT NULL DEFAULT 'finale',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`permessi_lettura` ENUM('R','H','N/A') NOT NULL DEFAULT 'N/A',
	`tipo_proprietari_lettura` ENUM('tutti','utente','gruppo') NULL DEFAULT 'tutti',
	`id_proprietari_lettura` VARCHAR(255) NOT NULL DEFAULT '-1',
	`permessi_admin` ENUM('R','H','N/A') NOT NULL DEFAULT 'N/A',
	`tipo_proprietari_admin` ENUM('tutti','utente','gruppo') NULL DEFAULT 'tutti',
	`id_proprietari_admin` VARCHAR(255) NOT NULL DEFAULT '-1',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`ultima_modifica` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_sezione` MEDIUMINT(8) NOT NULL DEFAULT '-1',
	`id_lingua` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`numero_letture` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`template` VARCHAR(24) NOT NULL DEFAULT 'istanza',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`id_oggetto` MEDIUMINT(9) NULL DEFAULT '0',
	`id_documento` MEDIUMINT(9) NULL DEFAULT '0',
	`__id_allegato_istanza` VARCHAR(255) NULL DEFAULT '',
	`__temporaneo` VARCHAR(255) NULL DEFAULT '1',
	`nome` VARCHAR(255) NULL DEFAULT '',
	`file_allegato` TEXT NULL,
	`ordine` MEDIUMINT(9) NULL DEFAULT '1',
	`omissis` MEDIUMINT(9) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `stato` (`stato`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `ultima_modifica` (`ultima_modifica`),
	INDEX `id_sezione` (`id_sezione`),
	INDEX `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
CREATE TABLE `oggetto_allegati_backup` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_riferimento` INT(11) NOT NULL DEFAULT '0',
	`pubblicata` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`bozza` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`id_oggetto` MEDIUMINT(9) NULL DEFAULT '0',
	`id_documento` MEDIUMINT(9) NULL DEFAULT '0',
	`__id_allegato_istanza` VARCHAR(255) NULL DEFAULT '',
	`__temporaneo` VARCHAR(255) NULL DEFAULT '1',
	`nome` VARCHAR(255) NULL DEFAULT '',
	`file_allegato` TEXT NULL,
	`ordine` MEDIUMINT(9) NULL DEFAULT '1',
	`omissis` MEDIUMINT(9) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
CREATE TABLE `oggetto_allegati_workflow` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_riferimento` INT(11) NOT NULL DEFAULT '0',
	`pubblicata` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`bozza` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`id_oggetto` MEDIUMINT(9) NULL DEFAULT '0',
	`id_documento` MEDIUMINT(9) NULL DEFAULT '0',
	`__id_allegato_istanza` VARCHAR(255) NULL DEFAULT '',
	`__temporaneo` VARCHAR(255) NULL DEFAULT '1',
	`nome` VARCHAR(255) NULL DEFAULT '',
	`file_allegato` TEXT NULL,
	`ordine` MEDIUMINT(9) NULL DEFAULT '1',
	`omissis` MEDIUMINT(9) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


CREATE TABLE `oggetto_altri_contenuti` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`stato` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`stato_workflow` VARCHAR(50) NOT NULL DEFAULT 'finale',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`permessi_lettura` ENUM('R','H','N/A') NOT NULL DEFAULT 'N/A',
	`tipo_proprietari_lettura` ENUM('tutti','utente','gruppo') NULL DEFAULT 'tutti',
	`id_proprietari_lettura` VARCHAR(255) NOT NULL DEFAULT '-1',
	`permessi_admin` ENUM('R','H','N/A') NOT NULL DEFAULT 'N/A',
	`tipo_proprietari_admin` ENUM('tutti','utente','gruppo') NULL DEFAULT 'tutti',
	`id_proprietari_admin` VARCHAR(255) NOT NULL DEFAULT '-1',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`ultima_modifica` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_sezione` MEDIUMINT(8) NOT NULL DEFAULT '-1',
	`id_lingua` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`numero_letture` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	`template` VARCHAR(24) NOT NULL DEFAULT 'istanza',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`titolo` VARCHAR(255) NULL DEFAULT '',
	`contenuto` TEXT NULL,
	PRIMARY KEY (`id`),
	INDEX `stato` (`stato`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `ultima_modifica` (`ultima_modifica`),
	INDEX `id_sezione` (`id_sezione`),
	INDEX `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
CREATE TABLE `oggetto_altri_contenuti_backup` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_riferimento` INT(11) NOT NULL DEFAULT '0',
	`pubblicata` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`bozza` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`titolo` VARCHAR(255) NULL DEFAULT '',
	`contenuto` TEXT NULL,
	PRIMARY KEY (`id`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
CREATE TABLE `oggetto_altri_contenuti_workflow` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_riferimento` INT(11) NOT NULL DEFAULT '0',
	`pubblicata` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`bozza` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`id_proprietario` INT(11) NOT NULL DEFAULT '0',
	`data_creazione` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`id_ente` MEDIUMINT(9) NULL DEFAULT '0',
	`titolo` VARCHAR(255) NULL DEFAULT '',
	`contenuto` TEXT NULL,
	PRIMARY KEY (`id`),
	INDEX `data_creazione` (`data_creazione`),
	INDEX `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


-- Dump della struttura di tabella pat.oggetto_bandi_requisiti_qualificazione
DROP TABLE IF EXISTS `oggetto_bandi_requisiti_qualificazione`;
CREATE TABLE IF NOT EXISTS `oggetto_bandi_requisiti_qualificazione` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `codice` varchar(255) DEFAULT '',
  `denominazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_bandi_requisiti_qualificazione_backup
DROP TABLE IF EXISTS `oggetto_bandi_requisiti_qualificazione_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_bandi_requisiti_qualificazione_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `codice` varchar(255) DEFAULT '',
  `denominazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_bandi_requisiti_qualificazione_workflow
DROP TABLE IF EXISTS `oggetto_bandi_requisiti_qualificazione_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_bandi_requisiti_qualificazione_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `codice` varchar(255) DEFAULT '',
  `denominazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_banner
DROP TABLE IF EXISTS `oggetto_banner`;
CREATE TABLE IF NOT EXISTS `oggetto_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo` varchar(255) DEFAULT '',
  `descrizione` varchar(255) DEFAULT '',
  `visualizzato` tinyint(1) DEFAULT '1',
  `sezioni` varchar(255) DEFAULT '',
  `colonna` varchar(255) DEFAULT 'colonna destra',
  `destinazione` varchar(125) DEFAULT '',
  `immagine` text,
  `bordo` varchar(255) DEFAULT '',
  `priorita` mediumint(9) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_banner_backup
DROP TABLE IF EXISTS `oggetto_banner_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_banner_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `descrizione` varchar(255) DEFAULT '',
  `visualizzato` tinyint(1) DEFAULT '1',
  `sezioni` varchar(255) DEFAULT '',
  `colonna` varchar(255) DEFAULT 'colonna destra',
  `destinazione` varchar(125) DEFAULT '',
  `immagine` text,
  `bordo` varchar(255) DEFAULT '',
  `priorita` mediumint(9) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_banner_workflow
DROP TABLE IF EXISTS `oggetto_banner_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_banner_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `descrizione` varchar(255) DEFAULT '',
  `visualizzato` tinyint(1) DEFAULT '1',
  `sezioni` varchar(255) DEFAULT '',
  `colonna` varchar(255) DEFAULT 'colonna destra',
  `destinazione` varchar(125) DEFAULT '',
  `immagine` text,
  `bordo` varchar(255) DEFAULT '',
  `priorita` mediumint(9) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_bilanci
DROP TABLE IF EXISTS `oggetto_bilanci`;
CREATE TABLE IF NOT EXISTS `oggetto_bilanci` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `anno` varchar(255) DEFAULT '',
  `descrizione` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `stato` (`stato`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_bilanci_backup
DROP TABLE IF EXISTS `oggetto_bilanci_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_bilanci_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `anno` varchar(255) DEFAULT '',
  `descrizione` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_bilanci_workflow
DROP TABLE IF EXISTS `oggetto_bilanci_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_bilanci_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `anno` varchar(255) DEFAULT '',
  `descrizione` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_commissioni
DROP TABLE IF EXISTS `oggetto_commissioni`;
CREATE TABLE IF NOT EXISTS `oggetto_commissioni` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `presidente` int(11) unsigned DEFAULT '0',
  `vicepresidente` varchar(255) DEFAULT '',
  `segretari` varchar(255) DEFAULT '',
  `membro` int(11) unsigned DEFAULT '0',
  `descrizione` text,
  `membri` varchar(255) DEFAULT '',
  `immagine` text,
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `ordine` mediumint(9) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '0',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_commissioni_backup
DROP TABLE IF EXISTS `oggetto_commissioni_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_commissioni_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `presidente` int(11) unsigned DEFAULT '0',
  `vicepresidente` varchar(255) DEFAULT '',
  `segretari` varchar(255) DEFAULT '',
  `membro` int(11) unsigned DEFAULT '0',
  `descrizione` text,
  `membri` varchar(255) DEFAULT '',
  `immagine` text,
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `ordine` mediumint(9) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '0',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_commissioni_workflow
DROP TABLE IF EXISTS `oggetto_commissioni_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_commissioni_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `presidente` int(11) unsigned DEFAULT '0',
  `vicepresidente` varchar(255) DEFAULT '',
  `segretari` varchar(255) DEFAULT '',
  `membro` int(11) unsigned DEFAULT '0',
  `descrizione` text,
  `membri` varchar(255) DEFAULT '',
  `immagine` text,
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `ordine` mediumint(9) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '0',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_concorsi
DROP TABLE IF EXISTS `oggetto_concorsi`;
CREATE TABLE IF NOT EXISTS `oggetto_concorsi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `tipologia` varchar(255) DEFAULT 'concorsi',
  `id_ente` mediumint(9) DEFAULT '0',
  `oggetto` varchar(500) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(255) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `orario_scadenza` varchar(255) DEFAULT '',
  `spesa_prevista` varchar(255) DEFAULT '',
  `spese_fatte` varchar(255) DEFAULT '',
  `dipendenti_assunti` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `descrizione` text,
  `calendario_prove` text,
  `concorso_collegato` int(11) unsigned DEFAULT '0',
  `allegato1` text,
  `allegato2` text,
  `alelgato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_lingua` (`id_lingua`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_concorsi_backup
DROP TABLE IF EXISTS `oggetto_concorsi_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_concorsi_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `tipologia` varchar(255) DEFAULT 'concorsi',
  `id_ente` mediumint(9) DEFAULT '0',
  `oggetto` varchar(255) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(255) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `orario_scadenza` varchar(255) DEFAULT '',
  `spesa_prevista` varchar(255) DEFAULT '',
  `spese_fatte` varchar(255) DEFAULT '',
  `dipendenti_assunti` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `descrizione` text,
  `calendario_prove` text,
  `concorso_collegato` int(11) unsigned DEFAULT '0',
  `allegato1` text,
  `allegato2` text,
  `alelgato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_concorsi_workflow
DROP TABLE IF EXISTS `oggetto_concorsi_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_concorsi_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `tipologia` varchar(255) DEFAULT 'concorsi',
  `id_ente` mediumint(9) DEFAULT '0',
  `oggetto` varchar(255) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(255) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `orario_scadenza` varchar(255) DEFAULT '',
  `spesa_prevista` varchar(255) DEFAULT '',
  `spese_fatte` varchar(255) DEFAULT '',
  `dipendenti_assunti` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `descrizione` text,
  `calendario_prove` text,
  `concorso_collegato` int(11) unsigned DEFAULT '0',
  `allegato1` text,
  `allegato2` text,
  `alelgato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_contatti
DROP TABLE IF EXISTS `oggetto_contatti`;
CREATE TABLE IF NOT EXISTS `oggetto_contatti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `struttura` int(11) unsigned DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `cognome` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `oggetto` text,
  `richiesta` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_contatti_backup
DROP TABLE IF EXISTS `oggetto_contatti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_contatti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `struttura` int(11) unsigned DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `cognome` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `oggetto` text,
  `richiesta` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_contatti_workflow
DROP TABLE IF EXISTS `oggetto_contatti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_contatti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `struttura` int(11) unsigned DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `cognome` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `oggetto` text,
  `richiesta` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_elenco_fornitori
DROP TABLE IF EXISTS `oggetto_elenco_fornitori`;
CREATE TABLE IF NOT EXISTS `oggetto_elenco_fornitori` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `nominativo` varchar(255) DEFAULT '',
  `codice_fiscale` varchar(255) DEFAULT '',
  `fiscale_estero` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `nominativo_raggruppamento` varchar(255) DEFAULT '',
  `mandante` varchar(255) DEFAULT '',
  `mandataria` varchar(255) DEFAULT '',
  `associata` varchar(255) DEFAULT '',
  `capogruppo` varchar(255) DEFAULT '',
  `consorziata` varchar(255) DEFAULT '',
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_elenco_fornitori_backup
DROP TABLE IF EXISTS `oggetto_elenco_fornitori_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_elenco_fornitori_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `nominativo` varchar(255) DEFAULT '',
  `codice_fiscale` varchar(255) DEFAULT '',
  `fiscale_estero` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `nominativo_raggruppamento` varchar(255) DEFAULT '',
  `mandante` varchar(255) DEFAULT '',
  `mandataria` varchar(255) DEFAULT '',
  `associata` varchar(255) DEFAULT '',
  `capogruppo` varchar(255) DEFAULT '',
  `consorziata` varchar(255) DEFAULT '',
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_elenco_fornitori_duplicate
DROP TABLE IF EXISTS `oggetto_elenco_fornitori_duplicate`;
CREATE TABLE IF NOT EXISTS `oggetto_elenco_fornitori_duplicate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `codice_fiscale` varchar(255) DEFAULT '',
  `fiscale_estero` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_elenco_fornitori_duplicati
DROP TABLE IF EXISTS `oggetto_elenco_fornitori_duplicati`;
CREATE TABLE IF NOT EXISTS `oggetto_elenco_fornitori_duplicati` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `codice_fiscale` varchar(255) DEFAULT '',
  `fiscale_estero` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_elenco_fornitori_workflow
DROP TABLE IF EXISTS `oggetto_elenco_fornitori_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_elenco_fornitori_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `nominativo` varchar(255) DEFAULT '',
  `codice_fiscale` varchar(255) DEFAULT '',
  `fiscale_estero` varchar(255) DEFAULT '',
  `indirizzo` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `nominativo_raggruppamento` varchar(255) DEFAULT '',
  `mandante` varchar(255) DEFAULT '',
  `mandataria` varchar(255) DEFAULT '',
  `associata` varchar(255) DEFAULT '',
  `capogruppo` varchar(255) DEFAULT '',
  `consorziata` varchar(255) DEFAULT '',
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help
DROP TABLE IF EXISTS `oggetto_etrasp_help`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) COLLATE latin1_bin NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) COLLATE latin1_bin NOT NULL DEFAULT 'istanza',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `tipo_enti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `testo_html` text COLLATE latin1_bin,
  `operazioni` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) COLLATE latin1_bin DEFAULT '',
  `frequenza_agg` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help_adminui
DROP TABLE IF EXISTS `oggetto_etrasp_help_adminui`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help_adminui` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `titolo` varchar(255) DEFAULT '',
  `tipo_cont` varchar(255) DEFAULT '',
  `contenuto` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help_adminui_backup
DROP TABLE IF EXISTS `oggetto_etrasp_help_adminui_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help_adminui_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `titolo` varchar(255) DEFAULT '',
  `tipo_cont` varchar(255) DEFAULT '',
  `contenuto` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help_adminui_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_help_adminui_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help_adminui_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `titolo` varchar(255) DEFAULT '',
  `tipo_cont` varchar(255) DEFAULT '',
  `contenuto` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help_backup
DROP TABLE IF EXISTS `oggetto_etrasp_help_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `tipo_enti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `testo_html` text COLLATE latin1_bin,
  `operazioni` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) COLLATE latin1_bin DEFAULT '',
  `frequenza_agg` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_help_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_help_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_help_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `tipo_enti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `testo_html` text CHARACTER SET latin1 COLLATE latin1_bin,
  `operazioni` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `frequenza_agg` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_modello
DROP TABLE IF EXISTS `oggetto_etrasp_modello`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_modello` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) COLLATE latin1_bin NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) COLLATE latin1_bin NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `html_generico` mediumtext COLLATE latin1_bin,
  `modulistica_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `modulistica` varchar(255) COLLATE latin1_bin DEFAULT '',
  `modulistica_opz` text COLLATE latin1_bin,
  `normativa_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `normativa` varchar(255) COLLATE latin1_bin DEFAULT '',
  `normativa_opz` text COLLATE latin1_bin,
  `referenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `referenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `referenti_opz` text COLLATE latin1_bin,
  `regolamenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `regolamenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `regolamenti_opz` text COLLATE latin1_bin,
  `procedimenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `procedimenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `procedimenti_opz` text COLLATE latin1_bin,
  `provvedimenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `provvedimenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `provvedimenti_opz` text COLLATE latin1_bin,
  `strutture_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `strutture` varchar(255) COLLATE latin1_bin DEFAULT '',
  `strutture_opz` text COLLATE latin1_bin,
  `incarichi` varchar(255) COLLATE latin1_bin DEFAULT '',
  `incarichi_opz` text COLLATE latin1_bin,
  `incarichi_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_modello_backup
DROP TABLE IF EXISTS `oggetto_etrasp_modello_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_modello_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `html_generico` text COLLATE latin1_bin,
  `modulistica_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `modulistica` varchar(255) COLLATE latin1_bin DEFAULT '',
  `modulistica_opz` text COLLATE latin1_bin,
  `normativa_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `normativa` varchar(255) COLLATE latin1_bin DEFAULT '',
  `normativa_opz` text COLLATE latin1_bin,
  `referenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `referenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `referenti_opz` text COLLATE latin1_bin,
  `regolamenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `regolamenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `regolamenti_opz` text COLLATE latin1_bin,
  `procedimenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `procedimenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `procedimenti_opz` text COLLATE latin1_bin,
  `provvedimenti_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `provvedimenti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `provvedimenti_opz` text COLLATE latin1_bin,
  `strutture_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  `strutture` varchar(255) COLLATE latin1_bin DEFAULT '',
  `strutture_opz` text COLLATE latin1_bin,
  `incarichi` varchar(255) COLLATE latin1_bin DEFAULT '',
  `incarichi_opz` text COLLATE latin1_bin,
  `incarichi_tit` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_modello_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_modello_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_modello_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `id_sezione_etrasp` int(11) unsigned DEFAULT '0',
  `html_generico` text CHARACTER SET latin1 COLLATE latin1_bin,
  `modulistica_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `modulistica` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `modulistica_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `normativa_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `normativa` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `normativa_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `referenti_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `referenti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `referenti_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `regolamenti_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `regolamenti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `regolamenti_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `procedimenti_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `procedimenti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `procedimenti_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `provvedimenti_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `provvedimenti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `provvedimenti_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `strutture_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `strutture` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `strutture_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `incarichi` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `incarichi_opz` text CHARACTER SET latin1 COLLATE latin1_bin,
  `incarichi_tit` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_news_admin
DROP TABLE IF EXISTS `oggetto_etrasp_news_admin`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_news_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo` varchar(255) DEFAULT '',
  `chiusura` tinyint(1) DEFAULT '1',
  `immagine` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `link_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_news_admin_backup
DROP TABLE IF EXISTS `oggetto_etrasp_news_admin_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_news_admin_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `chiusura` tinyint(1) DEFAULT '1',
  `immagine` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `link_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_news_admin_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_news_admin_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_news_admin_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `chiusura` tinyint(1) DEFAULT '1',
  `immagine` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `link_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_norma
DROP TABLE IF EXISTS `oggetto_etrasp_norma`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_norma` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) COLLATE latin1_bin NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') COLLATE latin1_bin NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') COLLATE latin1_bin DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) COLLATE latin1_bin NOT NULL DEFAULT 'istanza',
  `norma` varchar(255) COLLATE latin1_bin DEFAULT '',
  `num_art` varchar(255) COLLATE latin1_bin DEFAULT '',
  `commi` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_enti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `testo_norma` text COLLATE latin1_bin,
  `altre_note` text COLLATE latin1_bin,
  `sezioni` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_norma_backup
DROP TABLE IF EXISTS `oggetto_etrasp_norma_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_norma_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `norma` varchar(255) COLLATE latin1_bin DEFAULT '',
  `num_art` varchar(255) COLLATE latin1_bin DEFAULT '',
  `commi` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_enti` varchar(255) COLLATE latin1_bin DEFAULT '',
  `testo_norma` text COLLATE latin1_bin,
  `altre_note` text COLLATE latin1_bin,
  `sezioni` varchar(255) COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_norma_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_norma_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_norma_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `norma` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `num_art` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `commi` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `tipo_enti` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `testo_norma` text CHARACTER SET latin1 COLLATE latin1_bin,
  `altre_note` text CHARACTER SET latin1 COLLATE latin1_bin,
  `sezioni` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  `tipo_cont` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_notifiche
DROP TABLE IF EXISTS `oggetto_etrasp_notifiche`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_notifiche` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `secondiview` mediumint(9) DEFAULT '8',
  `testo` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_notifiche_backup
DROP TABLE IF EXISTS `oggetto_etrasp_notifiche_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_notifiche_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `secondiview` mediumint(9) DEFAULT '8',
  `testo` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_notifiche_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_notifiche_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_notifiche_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `menu` varchar(255) DEFAULT '',
  `menusec` varchar(255) DEFAULT '',
  `azione` varchar(255) DEFAULT '',
  `secondiview` mediumint(9) DEFAULT '8',
  `testo` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipocontenuti
DROP TABLE IF EXISTS `oggetto_etrasp_tipocontenuti`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipocontenuti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `nome` varchar(255) DEFAULT '',
  `nome_breve` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipocontenuti_backup
DROP TABLE IF EXISTS `oggetto_etrasp_tipocontenuti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipocontenuti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `nome_breve` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipocontenuti_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_tipocontenuti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipocontenuti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `nome_breve` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoenti
DROP TABLE IF EXISTS `oggetto_etrasp_tipoenti`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `nome` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoentisemplice
DROP TABLE IF EXISTS `oggetto_etrasp_tipoentisemplice`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoentisemplice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `nome_tipo` varchar(255) DEFAULT '',
  `tipo_ente` varchar(255) DEFAULT '',
  `sezioni_esclusione` varchar(255) DEFAULT '',
  `org_commissario` varchar(255) DEFAULT '',
  `org_sub_commissario` varchar(255) DEFAULT '',
  `org_sindaco` varchar(255) DEFAULT '',
  `org_vicesindaco` varchar(255) DEFAULT '',
  `org_giunta` varchar(255) DEFAULT '',
  `org_presidente` varchar(255) DEFAULT '',
  `org_consiglio` varchar(255) DEFAULT '',
  `org_direzione` varchar(255) DEFAULT '',
  `org_segretario` varchar(255) DEFAULT '',
  `org_commissioni` varchar(255) DEFAULT '',
  `org_ass_sindaci` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoentisemplice_backup
DROP TABLE IF EXISTS `oggetto_etrasp_tipoentisemplice_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoentisemplice_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome_tipo` varchar(255) DEFAULT '',
  `tipo_ente` varchar(255) DEFAULT '',
  `sezioni_esclusione` varchar(255) DEFAULT '',
  `org_commissario` varchar(255) DEFAULT '',
  `org_sub_commissario` varchar(255) DEFAULT '',
  `org_sindaco` varchar(255) DEFAULT '',
  `org_vicesindaco` varchar(255) DEFAULT '',
  `org_giunta` varchar(255) DEFAULT '',
  `org_presidente` varchar(255) DEFAULT '',
  `org_consiglio` varchar(255) DEFAULT '',
  `org_direzione` varchar(255) DEFAULT '',
  `org_segretario` varchar(255) DEFAULT '',
  `org_commissioni` varchar(255) DEFAULT '',
  `org_ass_sindaci` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoentisemplice_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_tipoentisemplice_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoentisemplice_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome_tipo` varchar(255) DEFAULT '',
  `tipo_ente` varchar(255) DEFAULT '',
  `sezioni_esclusione` varchar(255) DEFAULT '',
  `org_commissario` varchar(255) DEFAULT '',
  `org_sub_commissario` varchar(255) DEFAULT '',
  `org_sindaco` varchar(255) DEFAULT '',
  `org_vicesindaco` varchar(255) DEFAULT '',
  `org_giunta` varchar(255) DEFAULT '',
  `org_presidente` varchar(255) DEFAULT '',
  `org_consiglio` varchar(255) DEFAULT '',
  `org_direzione` varchar(255) DEFAULT '',
  `org_segretario` varchar(255) DEFAULT '',
  `org_commissioni` varchar(255) DEFAULT '',
  `org_ass_sindaci` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoenti_backup
DROP TABLE IF EXISTS `oggetto_etrasp_tipoenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_tipoenti_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_tipoenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_tipoenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `descrizione` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `utenti` varchar(255) DEFAULT '',
  `composizione_workflow` text,
  `id_stati` text,
  `id_utenti_intermedi` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_workflow_backup
DROP TABLE IF EXISTS `oggetto_etrasp_workflow_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_workflow_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `utenti` varchar(255) DEFAULT '',
  `composizione_workflow` text,
  `id_stati` text,
  `id_utenti_intermedi` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_etrasp_workflow_workflow
DROP TABLE IF EXISTS `oggetto_etrasp_workflow_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_etrasp_workflow_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `id_oggetto` varchar(255) DEFAULT '',
  `utenti` varchar(255) DEFAULT '',
  `composizione_workflow` text,
  `id_stati` text,
  `id_utenti_intermedi` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_eventi
DROP TABLE IF EXISTS `oggetto_eventi`;
CREATE TABLE IF NOT EXISTS `oggetto_eventi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `evento` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `luogo_evento` varchar(255) DEFAULT '',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `presentazione` varchar(255) DEFAULT '',
  `dettagli_evento` text,
  `allegato` varchar(255) DEFAULT '',
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_eventi_backup
DROP TABLE IF EXISTS `oggetto_eventi_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_eventi_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `evento` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `luogo_evento` varchar(255) DEFAULT '',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `presentazione` varchar(255) DEFAULT '',
  `dettagli_evento` text,
  `allegato` varchar(255) DEFAULT '',
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_eventi_workflow
DROP TABLE IF EXISTS `oggetto_eventi_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_eventi_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `evento` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `luogo_evento` varchar(255) DEFAULT '',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `presentazione` varchar(255) DEFAULT '',
  `dettagli_evento` text,
  `allegato` varchar(255) DEFAULT '',
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_faq
DROP TABLE IF EXISTS `oggetto_faq`;
CREATE TABLE IF NOT EXISTS `oggetto_faq` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `domanda` varchar(255) DEFAULT '',
  `risposta` text,
  `procedimento` varchar(255) DEFAULT '',
  `argomenti` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_faq_backup
DROP TABLE IF EXISTS `oggetto_faq_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_faq_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `domanda` varchar(255) DEFAULT '',
  `risposta` text,
  `procedimento` varchar(255) DEFAULT '',
  `argomenti` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_faq_workflow
DROP TABLE IF EXISTS `oggetto_faq_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_faq_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `domanda` varchar(255) DEFAULT '',
  `risposta` text,
  `procedimento` varchar(255) DEFAULT '',
  `argomenti` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_gare_atti
DROP TABLE IF EXISTS `oggetto_gare_atti`;
CREATE TABLE IF NOT EXISTS `oggetto_gare_atti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `contratto` varchar(255) DEFAULT '',
  `denominazione_aggiudicatrice` varchar(255) DEFAULT '',
  `dati_aggiudicatrice` varchar(255) DEFAULT '',
  `tipo_amministrazione` varchar(255) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(255) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `senza_importo` varchar(255) DEFAULT '',
  `valore_base_asta` varchar(255) DEFAULT '',
  `valore_importo_aggiudicazione` varchar(255) DEFAULT '',
  `importo_liquidato` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `data_scadenza_esito` bigint(15) DEFAULT '0',
  `data_inizio_lavori` bigint(15) DEFAULT '0',
  `data_lavori_fine` bigint(15) DEFAULT '0',
  `requisiti_qualificazione` int(11) unsigned DEFAULT '0',
  `codice_cpv` varchar(255) DEFAULT '',
  `codice_scp` varchar(255) DEFAULT '',
  `url_scp` varchar(255) DEFAULT '',
  `cig` varchar(255) DEFAULT '',
  `bando_collegato` int(11) unsigned DEFAULT '0',
  `id_record_cig_principale` int(11) unsigned DEFAULT '0',
  `altre_procedure` varchar(255) DEFAULT '',
  `oggetto` varchar(500) DEFAULT '',
  `dettagli` text,
  `scelta_contraente` varchar(255) DEFAULT '',
  `note_scelta` text,
  `elenco_partecipanti` text,
  `elenco_aggiudicatari` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `allegato21` text,
  `allegato22` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_beneficiario` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_lingua` (`id_lingua`),
  KEY `stato` (`stato`),
  KEY `id_sezione` (`id_sezione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `data_attivazione` (`data_attivazione`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_gare_atti_backup
DROP TABLE IF EXISTS `oggetto_gare_atti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_gare_atti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `contratto` varchar(255) DEFAULT '',
  `denominazione_aggiudicatrice` varchar(255) DEFAULT '',
  `dati_aggiudicatrice` varchar(255) DEFAULT '',
  `tipo_amministrazione` varchar(255) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(10) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `senza_importo` varchar(255) DEFAULT '',
  `valore_base_asta` varchar(255) DEFAULT '',
  `valore_importo_aggiudicazione` varchar(255) DEFAULT '',
  `importo_liquidato` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `data_scadenza_esito` bigint(15) DEFAULT '0',
  `data_inizio_lavori` bigint(15) DEFAULT '0',
  `data_lavori_fine` bigint(15) DEFAULT '0',
  `requisiti_qualificazione` int(11) unsigned DEFAULT '0',
  `codice_cpv` varchar(255) DEFAULT '',
  `codice_scp` varchar(255) DEFAULT '',
  `url_scp` varchar(255) DEFAULT '',
  `cig` varchar(255) DEFAULT '',
  `bando_collegato` int(11) unsigned DEFAULT '0',
  `id_record_cig_principale` int(11) unsigned DEFAULT '0',
  `altre_procedure` varchar(255) DEFAULT '',
  `oggetto` varchar(255) DEFAULT '',
  `dettagli` text,
  `scelta_contraente` varchar(255) DEFAULT '',
  `note_scelta` text,
  `elenco_partecipanti` varchar(255) DEFAULT '',
  `elenco_aggiudicatari` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `allegato21` text,
  `allegato22` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_beneficiario` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_gare_atti_workflow
DROP TABLE IF EXISTS `oggetto_gare_atti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_gare_atti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipologia` varchar(255) DEFAULT '',
  `contratto` varchar(255) DEFAULT '',
  `denominazione_aggiudicatrice` varchar(255) DEFAULT '',
  `dati_aggiudicatrice` varchar(255) DEFAULT '',
  `tipo_amministrazione` varchar(255) DEFAULT '',
  `sede_provincia` varchar(255) DEFAULT '',
  `sede_comune` varchar(10) DEFAULT '',
  `sede_indirizzo` varchar(255) DEFAULT '',
  `struttura` varchar(255) DEFAULT '',
  `senza_importo` varchar(255) DEFAULT '',
  `valore_base_asta` varchar(255) DEFAULT '',
  `valore_importo_aggiudicazione` varchar(255) DEFAULT '',
  `importo_liquidato` varchar(255) DEFAULT '',
  `data_attivazione` bigint(15) DEFAULT '0',
  `data_scadenza` bigint(15) DEFAULT '0',
  `data_scadenza_esito` bigint(15) DEFAULT '0',
  `data_inizio_lavori` bigint(15) DEFAULT '0',
  `data_lavori_fine` bigint(15) DEFAULT '0',
  `requisiti_qualificazione` int(11) unsigned DEFAULT '0',
  `codice_cpv` varchar(255) DEFAULT '',
  `codice_scp` varchar(255) DEFAULT '',
  `url_scp` varchar(255) DEFAULT '',
  `cig` varchar(255) DEFAULT '',
  `bando_collegato` int(11) unsigned DEFAULT '0',
  `id_record_cig_principale` int(11) unsigned DEFAULT '0',
  `altre_procedure` varchar(255) DEFAULT '',
  `oggetto` varchar(255) DEFAULT '',
  `dettagli` text,
  `scelta_contraente` varchar(255) DEFAULT '',
  `note_scelta` text,
  `elenco_partecipanti` varchar(255) DEFAULT '',
  `elenco_aggiudicatari` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `allegato9` text,
  `allegato10` text,
  `allegato11` text,
  `allegato12` text,
  `allegato13` text,
  `allegato14` text,
  `allegato15` text,
  `allegato16` text,
  `allegato17` text,
  `allegato18` text,
  `allegato19` text,
  `allegato20` text,
  `allegato21` text,
  `allegato22` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_beneficiario` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini
DROP TABLE IF EXISTS `oggetto_immagini`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo_imm` varchar(255) DEFAULT '',
  `evento` int(11) unsigned DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` varchar(255) DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini_backup
DROP TABLE IF EXISTS `oggetto_immagini_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo_imm` varchar(255) DEFAULT '',
  `evento` int(11) unsigned DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` varchar(255) DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini_testata
DROP TABLE IF EXISTS `oggetto_immagini_testata`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini_testata` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `nome` varchar(255) DEFAULT '',
  `immagine` text,
  `visualizzazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini_testata_backup
DROP TABLE IF EXISTS `oggetto_immagini_testata_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini_testata_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `immagine` text,
  `visualizzazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini_testata_workflow
DROP TABLE IF EXISTS `oggetto_immagini_testata_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini_testata_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `immagine` text,
  `visualizzazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_immagini_workflow
DROP TABLE IF EXISTS `oggetto_immagini_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_immagini_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo_imm` varchar(255) DEFAULT '',
  `evento` int(11) unsigned DEFAULT '0',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` varchar(255) DEFAULT '',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_incarichi
DROP TABLE IF EXISTS `oggetto_incarichi`;
CREATE TABLE IF NOT EXISTS `oggetto_incarichi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `oggetto` varchar(255) DEFAULT '',
  `tipo_incarico` varchar(255) DEFAULT '',
  `dirigente` tinyint(1) DEFAULT '1',
  `struttura` int(11) unsigned DEFAULT '0',
  `inizio_incarico` bigint(15) DEFAULT '0',
  `fine_incarico` bigint(15) DEFAULT '0',
  `compenso` text,
  `compenso_erogato` varchar(255) DEFAULT '',
  `compenso_variabile` text,
  `note` text,
  `estremi_atti` text,
  `file_atto` text,
  `modo_individuazione` text,
  `progetto` text,
  `cv_soggetto` text,
  `verifica_conflitto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_incarichi_backup
DROP TABLE IF EXISTS `oggetto_incarichi_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_incarichi_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `oggetto` varchar(255) DEFAULT '',
  `tipo_incarico` varchar(255) DEFAULT '',
  `dirigente` tinyint(1) DEFAULT '1',
  `struttura` int(11) unsigned DEFAULT '0',
  `inizio_incarico` bigint(15) DEFAULT '0',
  `fine_incarico` bigint(15) DEFAULT '0',
  `compenso` text,
  `compenso_erogato` varchar(255) DEFAULT '',
  `compenso_variabile` text,
  `note` text,
  `estremi_atti` text,
  `file_atto` text,
  `modo_individuazione` text,
  `progetto` text,
  `cv_soggetto` text,
  `verifica_conflitto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_incarichi_workflow
DROP TABLE IF EXISTS `oggetto_incarichi_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_incarichi_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `oggetto` varchar(255) DEFAULT '',
  `tipo_incarico` varchar(255) DEFAULT '',
  `dirigente` tinyint(1) DEFAULT '1',
  `struttura` int(11) unsigned DEFAULT '0',
  `inizio_incarico` bigint(15) DEFAULT '0',
  `fine_incarico` bigint(15) DEFAULT '0',
  `compenso` text,
  `compenso_erogato` varchar(255) DEFAULT '',
  `compenso_variabile` text,
  `note` text,
  `estremi_atti` text,
  `file_atto` text,
  `modo_individuazione` text,
  `progetto` text,
  `cv_soggetto` text,
  `verifica_conflitto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_link
DROP TABLE IF EXISTS `oggetto_link`;
CREATE TABLE IF NOT EXISTS `oggetto_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo` varchar(255) DEFAULT '',
  `indirizzo` varchar(125) DEFAULT '',
  `note` text,
  `area` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_link_backup
DROP TABLE IF EXISTS `oggetto_link_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_link_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `indirizzo` varchar(125) DEFAULT '',
  `note` text,
  `area` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_link_workflow
DROP TABLE IF EXISTS `oggetto_link_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_link_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `indirizzo` varchar(125) DEFAULT '',
  `note` text,
  `area` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_menu_banner
DROP TABLE IF EXISTS `oggetto_menu_banner`;
CREATE TABLE IF NOT EXISTS `oggetto_menu_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `nome` text,
  `usa_titolo` varchar(255) DEFAULT 'no',
  `tooltip` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_menu_banner_backup
DROP TABLE IF EXISTS `oggetto_menu_banner_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_menu_banner_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` text,
  `usa_titolo` varchar(255) DEFAULT 'no',
  `tooltip` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_menu_banner_workflow
DROP TABLE IF EXISTS `oggetto_menu_banner_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_menu_banner_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` text,
  `usa_titolo` varchar(255) DEFAULT 'no',
  `tooltip` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_modulistica_regolamenti
DROP TABLE IF EXISTS `oggetto_modulistica_regolamenti`;
CREATE TABLE IF NOT EXISTS `oggetto_modulistica_regolamenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '2',
  `titolo` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_1` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_modulistica_regolamenti_backup
DROP TABLE IF EXISTS `oggetto_modulistica_regolamenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_modulistica_regolamenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '2',
  `titolo` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_1` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_modulistica_regolamenti_workflow
DROP TABLE IF EXISTS `oggetto_modulistica_regolamenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_modulistica_regolamenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '2',
  `titolo` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_1` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_normativa
DROP TABLE IF EXISTS `oggetto_normativa`;
CREATE TABLE IF NOT EXISTS `oggetto_normativa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '2',
  `nome` varchar(255) DEFAULT '',
  `uffici` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `desc_cont` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_normativa_backup
DROP TABLE IF EXISTS `oggetto_normativa_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_normativa_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '2',
  `nome` varchar(255) DEFAULT '',
  `uffici` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `desc_cont` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_normativa_workflow
DROP TABLE IF EXISTS `oggetto_normativa_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_normativa_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '2',
  `nome` varchar(255) DEFAULT '',
  `uffici` varchar(255) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `desc_cont` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_notizie
DROP TABLE IF EXISTS `oggetto_notizie`;
CREATE TABLE IF NOT EXISTS `oggetto_notizie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `primopiano` tinyint(1) DEFAULT '1',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `contenuto` text,
  `allegato` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_notizie_backup
DROP TABLE IF EXISTS `oggetto_notizie_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_notizie_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `primopiano` tinyint(1) DEFAULT '1',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `contenuto` text,
  `allegato` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_notizie_workflow
DROP TABLE IF EXISTS `oggetto_notizie_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_notizie_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `data_inizio` bigint(15) DEFAULT '0',
  `data_fine` bigint(15) DEFAULT '0',
  `primopiano` tinyint(1) DEFAULT '1',
  `argomenti` varchar(255) DEFAULT '',
  `immagine` int(11) unsigned DEFAULT '0',
  `contenuto` text,
  `allegato` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `maggiori_info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_oneri
DROP TABLE IF EXISTS `oggetto_oneri`;
CREATE TABLE IF NOT EXISTS `oggetto_oneri` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipo` varchar(255) DEFAULT '',
  `cittadini` tinyint(1) DEFAULT '0',
  `imprese` tinyint(1) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `descrizione` text,
  `procedimenti` varchar(255) DEFAULT '',
  `provvedimenti` varchar(255) DEFAULT '',
  `normativa` varchar(255) DEFAULT '',
  `regolamenti` varchar(255) DEFAULT '',
  `info` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_oneri_backup
DROP TABLE IF EXISTS `oggetto_oneri_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_oneri_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipo` varchar(255) DEFAULT '',
  `cittadini` tinyint(1) DEFAULT '0',
  `imprese` tinyint(1) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `descrizione` text,
  `procedimenti` varchar(255) DEFAULT '',
  `provvedimenti` varchar(255) DEFAULT '',
  `normativa` varchar(255) DEFAULT '',
  `regolamenti` varchar(255) DEFAULT '',
  `info` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_oneri_workflow
DROP TABLE IF EXISTS `oggetto_oneri_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_oneri_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tipo` varchar(255) DEFAULT '',
  `cittadini` tinyint(1) DEFAULT '0',
  `imprese` tinyint(1) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `descrizione` text,
  `procedimenti` varchar(255) DEFAULT '',
  `provvedimenti` varchar(255) DEFAULT '',
  `normativa` varchar(255) DEFAULT '',
  `regolamenti` varchar(255) DEFAULT '',
  `info` varchar(255) DEFAULT '',
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_paragrafo
DROP TABLE IF EXISTS `oggetto_paragrafo`;
CREATE TABLE IF NOT EXISTS `oggetto_paragrafo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) NOT NULL DEFAULT '1',
  `id_proprietario` int(11) unsigned NOT NULL DEFAULT '0',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(125) NOT NULL DEFAULT '',
  `contenuto` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_paragrafo_backup
DROP TABLE IF EXISTS `oggetto_paragrafo_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_paragrafo_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) unsigned NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) unsigned NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(125) NOT NULL DEFAULT '',
  `contenuto` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_paragrafo_workflow
DROP TABLE IF EXISTS `oggetto_paragrafo_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_paragrafo_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) unsigned NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) unsigned NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `nome` varchar(125) NOT NULL DEFAULT '',
  `contenuto` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_primopiano
DROP TABLE IF EXISTS `oggetto_primopiano`;
CREATE TABLE IF NOT EXISTS `oggetto_primopiano` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `titolo` varchar(255) DEFAULT '',
  `sottotitolo` varchar(255) DEFAULT '',
  `descrizione` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  `apertura_link` varchar(255) DEFAULT 'stessa finestra',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_primopiano_backup
DROP TABLE IF EXISTS `oggetto_primopiano_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_primopiano_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `sottotitolo` varchar(255) DEFAULT '',
  `descrizione` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  `apertura_link` varchar(255) DEFAULT 'stessa finestra',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_primopiano_workflow
DROP TABLE IF EXISTS `oggetto_primopiano_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_primopiano_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `sottotitolo` varchar(255) DEFAULT '',
  `descrizione` text,
  `immagine` text,
  `destinazione` varchar(255) DEFAULT '',
  `apertura_link` varchar(255) DEFAULT 'stessa finestra',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_procedimenti
DROP TABLE IF EXISTS `oggetto_procedimenti`;
CREATE TABLE IF NOT EXISTS `oggetto_procedimenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `referente_proc` varchar(255) DEFAULT '',
  `referente_prov` varchar(255) DEFAULT '',
  `resp_sost` varchar(255) DEFAULT '',
  `ufficio_def` varchar(255) DEFAULT '',
  `personale_proc` varchar(255) DEFAULT '',
  `contattare` varchar(255) DEFAULT 'struttura-referenti',
  `ufficio` varchar(255) DEFAULT '',
  `descrizione` text,
  `costi` text,
  `silenzio_assenso` tinyint(1) DEFAULT '1',
  `dichiarazione` tinyint(1) DEFAULT '1',
  `normativa` text,
  `norme` varchar(255) DEFAULT '',
  `termine` varchar(255) DEFAULT '',
  `area` varchar(255) DEFAULT '',
  `link_servizio` varchar(255) DEFAULT '',
  `tempi_servizio` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_resp_proc` varchar(255) DEFAULT '',
  `txt_resp_prov` varchar(255) DEFAULT '',
  `txt_resp_sost` varchar(255) DEFAULT '',
  `txt_struttura` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_procedimenti_backup
DROP TABLE IF EXISTS `oggetto_procedimenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_procedimenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `referente_proc` varchar(255) DEFAULT '',
  `referente_prov` varchar(255) DEFAULT '',
  `resp_sost` varchar(255) DEFAULT '',
  `ufficio_def` varchar(255) DEFAULT '',
  `personale_proc` varchar(255) DEFAULT '',
  `contattare` varchar(255) DEFAULT 'struttura-referenti',
  `ufficio` varchar(255) DEFAULT '',
  `descrizione` text,
  `costi` text,
  `silenzio_assenso` tinyint(1) DEFAULT '1',
  `dichiarazione` tinyint(1) DEFAULT '1',
  `normativa` text,
  `norme` varchar(255) DEFAULT '',
  `termine` varchar(255) DEFAULT '',
  `area` varchar(255) DEFAULT '',
  `link_servizio` varchar(255) DEFAULT '',
  `tempi_servizio` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_resp_proc` varchar(255) DEFAULT '',
  `txt_resp_prov` varchar(255) DEFAULT '',
  `txt_resp_sost` varchar(255) DEFAULT '',
  `txt_struttura` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_procedimenti_pre_az
DROP TABLE IF EXISTS `oggetto_procedimenti_pre_az`;
CREATE TABLE IF NOT EXISTS `oggetto_procedimenti_pre_az` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `referente_proc` varchar(255) DEFAULT '',
  `referente_prov` varchar(255) DEFAULT '',
  `resp_sost` varchar(255) DEFAULT '',
  `ufficio_def` varchar(255) DEFAULT '',
  `personale_proc` varchar(255) DEFAULT '',
  `contattare` varchar(255) DEFAULT 'struttura-referenti',
  `ufficio` varchar(255) DEFAULT '',
  `descrizione` text,
  `costi` text,
  `normativa` text,
  `norme` varchar(255) DEFAULT '',
  `termine` varchar(255) DEFAULT '',
  `area` varchar(255) DEFAULT '',
  `link_servizio` varchar(255) DEFAULT '',
  `tempi_servizio` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_resp_proc` varchar(255) DEFAULT '',
  `txt_resp_provv` varchar(255) DEFAULT '',
  `txt_resp_sost` varchar(255) DEFAULT '',
  `txt_ufficio` varchar(255) DEFAULT '',
  `txt_uffici_altri` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_procedimenti_workflow
DROP TABLE IF EXISTS `oggetto_procedimenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_procedimenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome` varchar(255) DEFAULT '',
  `referente_proc` varchar(255) DEFAULT '',
  `referente_prov` varchar(255) DEFAULT '',
  `resp_sost` varchar(255) DEFAULT '',
  `ufficio_def` varchar(255) DEFAULT '',
  `personale_proc` varchar(255) DEFAULT '',
  `contattare` varchar(255) DEFAULT 'struttura-referenti',
  `ufficio` varchar(255) DEFAULT '',
  `descrizione` text,
  `costi` text,
  `silenzio_assenso` tinyint(1) DEFAULT '1',
  `dichiarazione` tinyint(1) DEFAULT '1',
  `normativa` text,
  `norme` varchar(255) DEFAULT '',
  `termine` varchar(255) DEFAULT '',
  `area` varchar(255) DEFAULT '',
  `link_servizio` varchar(255) DEFAULT '',
  `tempi_servizio` text,
  `id_ori` varchar(255) DEFAULT '',
  `txt_resp_proc` varchar(255) DEFAULT '',
  `txt_resp_prov` varchar(255) DEFAULT '',
  `txt_resp_sost` varchar(255) DEFAULT '',
  `txt_struttura` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_provvedimenti
DROP TABLE IF EXISTS `oggetto_provvedimenti`;
CREATE TABLE IF NOT EXISTS `oggetto_provvedimenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `numero` varchar(255) DEFAULT '',
  `oggetto` varchar(500) DEFAULT '',
  `tipo_articolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `spesa` text,
  `estremi` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_provvedimenti_backup
DROP TABLE IF EXISTS `oggetto_provvedimenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_provvedimenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `numero` varchar(255) DEFAULT '',
  `oggetto` varchar(500) DEFAULT '',
  `tipo_articolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `spesa` text,
  `estremi` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_provvedimenti_workflow
DROP TABLE IF EXISTS `oggetto_provvedimenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_provvedimenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `numero` varchar(255) DEFAULT '',
  `oggetto` varchar(500) DEFAULT '',
  `tipo_articolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` int(11) unsigned DEFAULT '0',
  `data` bigint(15) DEFAULT '0',
  `contenuto` text,
  `spesa` text,
  `estremi` text,
  `allegato1` text,
  `allegato2` text,
  `allegato3` text,
  `allegato4` text,
  `allegato5` text,
  `allegato6` text,
  `allegato7` text,
  `allegato8` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_regolamenti
DROP TABLE IF EXISTS `oggetto_regolamenti`;
CREATE TABLE IF NOT EXISTS `oggetto_regolamenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT 'normale',
  `strutture` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_2` text,
  `allegato_3` text,
  `allegato_4` text,
  `allegato_5` text,
  `allegato_6` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_regolamenti_backup
DROP TABLE IF EXISTS `oggetto_regolamenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_regolamenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT 'normale',
  `strutture` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_2` text,
  `allegato_3` text,
  `allegato_4` text,
  `allegato_5` text,
  `allegato_6` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_regolamenti_workflow
DROP TABLE IF EXISTS `oggetto_regolamenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_regolamenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `titolo` varchar(255) DEFAULT '',
  `tipo` varchar(255) DEFAULT 'normale',
  `strutture` varchar(255) DEFAULT '',
  `procedimenti` varchar(255) DEFAULT '',
  `allegato` varchar(255) DEFAULT '',
  `allegato_2` text,
  `allegato_3` text,
  `allegato_4` text,
  `allegato_5` text,
  `allegato_6` text,
  `descrizione_mod` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_riferimenti
DROP TABLE IF EXISTS `oggetto_riferimenti`;
CREATE TABLE IF NOT EXISTS `oggetto_riferimenti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `tit` varchar(255) DEFAULT '',
  `referente` varchar(255) DEFAULT '',
  `ruolo` varchar(255) DEFAULT 'Dipendente',
  `incarico` varchar(255) DEFAULT '',
  `determinato` tinyint(1) DEFAULT '1',
  `uffici` varchar(255) DEFAULT '',
  `ruolo_politico` varchar(255) DEFAULT '',
  `organo` varchar(255) DEFAULT '',
  `delega` tinyint(1) DEFAULT '0',
  `testo_delega` text,
  `commissioni` varchar(255) DEFAULT '',
  `allegato_nomina` text,
  `foto` text,
  `telefono` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `email_cert` varchar(255) DEFAULT '',
  `curriculum` text,
  `atto_conferimento` text,
  `retribuzione` text,
  `retribuzione1` text,
  `retribuzione2` text,
  `altre_cariche` text,
  `patrimonio` text,
  `patrimonio1` text,
  `patrimonio2` text,
  `note` text,
  `compensi` text,
  `importi_viaggi` text,
  `altri_incarichi` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  `vis_elenchi` tinyint(1) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '1',
  `carica_inizio` bigint(15) DEFAULT '0',
  `carica_fine` bigint(15) DEFAULT '0',
  `priorita` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `altre_info` text,
  `archivio_informazioni` text,
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_lingua` (`id_lingua`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_riferimenti_backup
DROP TABLE IF EXISTS `oggetto_riferimenti_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_riferimenti_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tit` varchar(255) DEFAULT '',
  `referente` varchar(255) DEFAULT '',
  `ruolo` varchar(255) DEFAULT 'Dipendente',
  `incarico` varchar(255) DEFAULT '',
  `determinato` tinyint(1) DEFAULT '1',
  `uffici` varchar(255) DEFAULT '',
  `ruolo_politico` varchar(255) DEFAULT '',
  `organo` varchar(255) DEFAULT '',
  `delega` tinyint(1) DEFAULT '0',
  `testo_delega` text,
  `commissioni` varchar(255) DEFAULT '',
  `allegato_nomina` text,
  `foto` text,
  `telefono` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `email_cert` varchar(255) DEFAULT '',
  `curriculum` text,
  `atto_conferimento` text,
  `retribuzione` text,
  `retribuzione1` text,
  `retribuzione2` text,
  `altre_cariche` text,
  `patrimonio` text,
  `patrimonio1` text,
  `patrimonio2` text,
  `note` text,
  `compensi` text,
  `importi_viaggi` text,
  `altri_incarichi` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  `vis_elenchi` tinyint(1) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '1',
  `carica_inizio` bigint(15) DEFAULT '0',
  `carica_fine` bigint(15) DEFAULT '0',
  `priorita` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `altre_info` text,
  `archivio_informazioni` text,
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_riferimenti_rapallo
DROP TABLE IF EXISTS `oggetto_riferimenti_rapallo`;
CREATE TABLE IF NOT EXISTS `oggetto_riferimenti_rapallo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `tit` varchar(255) DEFAULT '',
  `referente` varchar(255) DEFAULT '',
  `ruolo` varchar(255) DEFAULT 'Dipendente',
  `incarico` int(11) unsigned DEFAULT '0',
  `determinato` tinyint(1) DEFAULT '1',
  `uffici` varchar(255) DEFAULT '',
  `ruolo_politico` varchar(255) DEFAULT '',
  `organo` varchar(255) DEFAULT '',
  `commissioni` varchar(255) DEFAULT '',
  `allegato_nomina` text,
  `foto` text,
  `telefono` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `email_cert` varchar(255) DEFAULT '',
  `curriculum` text,
  `retribuzione` text,
  `altre_cariche` text,
  `patrimonio` text,
  `note` longtext,
  `vis_elenchi` tinyint(1) DEFAULT '1',
  `priorita` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_ente` (`id_ente`),
  KEY `id_lingua` (`id_lingua`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_riferimenti_workflow
DROP TABLE IF EXISTS `oggetto_riferimenti_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_riferimenti_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `tit` varchar(255) DEFAULT '',
  `referente` varchar(255) DEFAULT '',
  `ruolo` varchar(255) DEFAULT 'Dipendente',
  `incarico` varchar(255) DEFAULT '',
  `determinato` tinyint(1) DEFAULT '1',
  `uffici` varchar(255) DEFAULT '',
  `ruolo_politico` varchar(255) DEFAULT '',
  `organo` varchar(255) DEFAULT '',
  `delega` tinyint(1) DEFAULT '0',
  `testo_delega` text,
  `commissioni` varchar(255) DEFAULT '',
  `allegato_nomina` text,
  `foto` text,
  `telefono` varchar(255) DEFAULT '',
  `mobile` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `email_cert` varchar(255) DEFAULT '',
  `curriculum` text,
  `atto_conferimento` text,
  `retribuzione` text,
  `retribuzione1` text,
  `retribuzione2` text,
  `altre_cariche` text,
  `patrimonio` text,
  `patrimonio1` text,
  `patrimonio2` text,
  `note` text,
  `compensi` text,
  `importi_viaggi` text,
  `altri_incarichi` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  `vis_elenchi` tinyint(1) DEFAULT '1',
  `archivio` tinyint(1) DEFAULT '1',
  `carica_inizio` bigint(15) DEFAULT '0',
  `carica_fine` bigint(15) DEFAULT '0',
  `priorita` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `altre_info` text,
  `archivio_informazioni` text,
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_societa
DROP TABLE IF EXISTS `oggetto_societa`;
CREATE TABLE IF NOT EXISTS `oggetto_societa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `ragione` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `descrizione` text,
  `misura` varchar(255) DEFAULT '',
  `durata` varchar(255) DEFAULT '',
  `oneri_anno` text,
  `rappresentanti` varchar(255) DEFAULT '',
  `incarichi_trattamento` text,
  `indirizzo_web` varchar(255) DEFAULT '',
  `bilancio` text,
  `bilancio_allegato` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_societa_backup
DROP TABLE IF EXISTS `oggetto_societa_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_societa_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `ragione` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `descrizione` text,
  `misura` varchar(255) DEFAULT '',
  `durata` varchar(255) DEFAULT '',
  `oneri_anno` text,
  `rappresentanti` varchar(255) DEFAULT '',
  `incarichi_trattamento` text,
  `indirizzo_web` varchar(255) DEFAULT '',
  `bilancio` text,
  `bilancio_allegato` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_societa_workflow
DROP TABLE IF EXISTS `oggetto_societa_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_societa_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `ragione` varchar(255) DEFAULT '',
  `tipologia` varchar(255) DEFAULT '',
  `descrizione` text,
  `misura` varchar(255) DEFAULT '',
  `durata` varchar(255) DEFAULT '',
  `oneri_anno` text,
  `rappresentanti` varchar(255) DEFAULT '',
  `incarichi_trattamento` text,
  `indirizzo_web` varchar(255) DEFAULT '',
  `bilancio` text,
  `bilancio_allegato` text,
  `dic_inconferibilita` text,
  `dic_incompatibilita` text,
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_sovvenzioni
DROP TABLE IF EXISTS `oggetto_sovvenzioni`;
CREATE TABLE IF NOT EXISTS `oggetto_sovvenzioni` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `dati_fiscali` text,
  `oggetto` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `compenso` varchar(500) DEFAULT '',
  `normativa` int(11) unsigned DEFAULT '0',
  `regolamento` int(11) unsigned DEFAULT '0',
  `note` text,
  `modo_individuazione` text,
  `file_atto` text,
  `progetto` text,
  `cv_soggetto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_sovvenzioni_backup
DROP TABLE IF EXISTS `oggetto_sovvenzioni_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_sovvenzioni_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `dati_fiscali` text,
  `oggetto` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `compenso` varchar(255) DEFAULT '',
  `normativa` int(11) unsigned DEFAULT '0',
  `regolamento` int(11) unsigned DEFAULT '0',
  `note` text,
  `modo_individuazione` text,
  `file_atto` text,
  `progetto` text,
  `cv_soggetto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_sovvenzioni_workflow
DROP TABLE IF EXISTS `oggetto_sovvenzioni_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_sovvenzioni_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nominativo` varchar(255) DEFAULT '',
  `dati_fiscali` text,
  `oggetto` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `responsabile` varchar(255) DEFAULT '',
  `data` bigint(15) DEFAULT '0',
  `compenso` varchar(255) DEFAULT '',
  `normativa` int(11) unsigned DEFAULT '0',
  `regolamento` int(11) unsigned DEFAULT '0',
  `note` text,
  `modo_individuazione` text,
  `file_atto` text,
  `progetto` text,
  `cv_soggetto` text,
  `id_ori` varchar(255) DEFAULT '',
  `id_atto_albo` mediumint(9) DEFAULT '0',
  `stato_pubblicazione` varchar(255) DEFAULT '100',
  `omissis` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_uffici
DROP TABLE IF EXISTS `oggetto_uffici`;
CREATE TABLE IF NOT EXISTS `oggetto_uffici` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome_ufficio` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `referente` int(11) unsigned DEFAULT '0',
  `referenti_contatti` varchar(255) DEFAULT '',
  `email_riferimento` varchar(255) DEFAULT '',
  `email_certificate` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `desc_att` text,
  `articolazione` tinyint(1) DEFAULT '1',
  `pres_sede` varchar(255) DEFAULT '',
  `sede` text,
  `dett_indirizzo` varchar(255) DEFAULT '',
  `orari` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `prop_agg` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `id_proprietario` (`id_proprietario`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`),
  KEY `id_ente` (`id_ente`),
  KEY `struttura` (`struttura`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_uffici_backup
DROP TABLE IF EXISTS `oggetto_uffici_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_uffici_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome_ufficio` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `referente` int(11) unsigned DEFAULT '0',
  `referenti_contatti` varchar(255) DEFAULT '',
  `email_riferimento` varchar(255) DEFAULT '',
  `email_certificate` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `desc_att` text,
  `articolazione` tinyint(1) DEFAULT '1',
  `pres_sede` varchar(255) DEFAULT '',
  `sede` text,
  `dett_indirizzo` varchar(255) DEFAULT '',
  `orari` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `prop_agg` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_uffici_workflow
DROP TABLE IF EXISTS `oggetto_uffici_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_uffici_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `nome_ufficio` varchar(255) DEFAULT '',
  `struttura` int(11) unsigned DEFAULT '0',
  `referente` int(11) unsigned DEFAULT '0',
  `referenti_contatti` varchar(255) DEFAULT '',
  `email_riferimento` varchar(255) DEFAULT '',
  `email_certificate` varchar(255) DEFAULT '',
  `telefono` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `desc_att` text,
  `articolazione` tinyint(1) DEFAULT '1',
  `pres_sede` varchar(255) DEFAULT '',
  `sede` text,
  `dett_indirizzo` varchar(255) DEFAULT '',
  `orari` text,
  `ordine` mediumint(9) DEFAULT '1',
  `id_ori` varchar(255) DEFAULT '',
  `proprieta_txt` varchar(255) DEFAULT '',
  `prop_agg` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_url_avcp
DROP TABLE IF EXISTS `oggetto_url_avcp`;
CREATE TABLE IF NOT EXISTS `oggetto_url_avcp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stato` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stato_workflow` varchar(50) NOT NULL DEFAULT 'finale',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `permessi_lettura` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_lettura` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_lettura` varchar(255) NOT NULL DEFAULT '-1',
  `permessi_admin` enum('R','H','N/A') NOT NULL DEFAULT 'N/A',
  `tipo_proprietari_admin` enum('tutti','utente','gruppo') DEFAULT 'tutti',
  `id_proprietari_admin` varchar(255) NOT NULL DEFAULT '-1',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `ultima_modifica` int(11) unsigned NOT NULL DEFAULT '0',
  `id_sezione` mediumint(8) NOT NULL DEFAULT '-1',
  `id_lingua` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `numero_letture` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `template` varchar(24) NOT NULL DEFAULT 'istanza',
  `id_ente` mediumint(9) DEFAULT '0',
  `anno` mediumint(9) DEFAULT '0',
  `url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `stato` (`stato`),
  KEY `data_creazione` (`data_creazione`),
  KEY `ultima_modifica` (`ultima_modifica`),
  KEY `id_sezione` (`id_sezione`),
  KEY `id_lingua` (`id_lingua`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_url_avcp_backup
DROP TABLE IF EXISTS `oggetto_url_avcp_backup`;
CREATE TABLE IF NOT EXISTS `oggetto_url_avcp_backup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `anno` mediumint(9) DEFAULT '0',
  `url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.oggetto_url_avcp_workflow
DROP TABLE IF EXISTS `oggetto_url_avcp_workflow`;
CREATE TABLE IF NOT EXISTS `oggetto_url_avcp_workflow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_riferimento` int(11) NOT NULL DEFAULT '0',
  `pubblicata` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bozza` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_proprietario` int(11) NOT NULL DEFAULT '0',
  `data_creazione` int(11) unsigned NOT NULL DEFAULT '0',
  `id_ente` mediumint(9) DEFAULT '0',
  `anno` mediumint(9) DEFAULT '0',
  `url` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `data_creazione` (`data_creazione`),
  KEY `id_riferimento` (`id_riferimento`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.sessioni
DROP TABLE IF EXISTS `sessioni`;
CREATE TABLE IF NOT EXISTS `sessioni` (
  `sessione_id` char(32) NOT NULL DEFAULT '',
  `sessione_userid` int(11) NOT NULL DEFAULT '0',
  `sessione_inizio` int(11) unsigned NOT NULL DEFAULT '0',
  `sessione_time` int(11) unsigned NOT NULL DEFAULT '0',
  `sessione_ip` char(8) NOT NULL DEFAULT '0',
  `sessione_pagina` int(11) DEFAULT '0',
  `sessione_loggato` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sessione_idlingua` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `sessione_idcss` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `sessione_admingrafica` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sessione_admininfo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sessione_basedati` mediumint(8) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

-- L’esportazione dei dati non era selezionata.


-- Dump della struttura di tabella pat.utenti
DROP TABLE IF EXISTS `utenti`;
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` mediumint(8) NOT NULL DEFAULT '0',
  `id_ente_admin` int(11) unsigned NOT NULL DEFAULT '0',
  `acl` varchar(255) NOT NULL DEFAULT '',
  `editor_avanzato` tinyint(1) NOT NULL DEFAULT '0',
  `attivo` tinyint(1) DEFAULT '1',
  `nome` varchar(255) DEFAULT NULL,
  `username` varchar(25) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `refresh_password` int(11) NOT NULL DEFAULT '0',
  `utente_sessione_time` int(11) NOT NULL DEFAULT '0',
  `utente_sessione_pagina` smallint(5) NOT NULL DEFAULT '0',
  `ultima_visita` int(11) NOT NULL DEFAULT '0',
  `data_registrazione` int(11) NOT NULL DEFAULT '0',
  `permessi` tinyint(4) DEFAULT '0',
  `admin_accessibile` tinyint(1) NOT NULL DEFAULT '0',
  `id_profilo_permessi` tinyint(3) unsigned DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `cellulare` varchar(25) DEFAULT '',
  `nuova_password` varchar(32) DEFAULT NULL,
  `actkey` varchar(32) DEFAULT NULL,
  `msg_non_letti` tinyint(3) unsigned DEFAULT NULL,
  `admin_skin` varchar(20) NOT NULL DEFAULT 'classic',
  `admin_interfaccia` enum('scomparsa','visualizzata') NOT NULL DEFAULT 'scomparsa',
  `admin_avvisi` enum('nascondi','avvisi','visualizza') NOT NULL DEFAULT 'nascondi',
  `istatus` int(11) unsigned NOT NULL DEFAULT '0',
  `dtmlastvisited` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;


-- Dump dei dati della tabella pat.categorie_immagini: 15 rows
DELETE FROM `categorie_immagini`;
/*!40000 ALTER TABLE `categorie_immagini` DISABLE KEYS */;
INSERT INTO `categorie_immagini` (`id`, `nome`, `descrizione`, `id_riferimento`, `id_proprietario`, `permessi`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `priorita`) VALUES
	(1, 'Immagini Template', 'Categoria di immagini per la costruzione dei template di progetto', 0, 0, 'H', 'N/A', 'tutti', '-1', 0, 0, 1),
	(2, 'Icone varie', 'Icone per vari utilizzi contenutistici e grafici', 0, 0, 'R', 'N/A', 'tutti', '-1', 0, 0, 1),
	(3, 'Icone semantiche', 'Icone rappresentative per funzioni e informazioni', 2, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(4, 'Icone grafiche', 'Icone non rappresentative per scopi puramente grafici', 2, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(5, 'Sfondi elementi (sfumature)', '', 1, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(6, 'Banner', '', 1, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(7, 'Altre icone', '', 2, 0, 'R', 'N/A', 'tutti', '-1', 0, 0, 1),
	(8, 'Icone grandi', '', 7, 0, 'R', 'N/A', 'tutti', '-1', 0, 0, 1),
	(9, 'Banner Desktop', '', 7, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(47, 'Immagini per Sezioni', '', 0, 1, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(11, 'Icone titoli', '', 1, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(12, 'Icone campi oggetto', '', 1, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(13, 'Mail e contatti', '', 12, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(14, 'Freccie', '', 12, 0, 'N/A', 'N/A', 'tutti', '-1', 0, 0, 1),
	(71, 'PAT - Immagini template', 'Immagini ed altre risorse usate nella struttura di PAT', 0, 0, 'H', 'N/A', 'tutti', '-1', 0, 0, 8);
/*!40000 ALTER TABLE `categorie_immagini` ENABLE KEYS */;

-- Dump dei dati della tabella pat.configurazione: 267 rows
DELETE FROM `configurazione`;
/*!40000 ALTER TABLE `configurazione` DISABLE KEYS */;
INSERT INTO `configurazione` (`nome`, `valore`, `amministrazione`, `descrizione`) VALUES
	('autenticazione', 'proprietario', 0, NULL),
	('autologin', '0', 0, NULL),
	('avvisa_autorizzati', '1', 0, NULL),
	('avvisa_autorizzati_oggetti', '1', 0, NULL),
	('avvisa_newsletter', '1', 0, NULL),
	('avvisa_utenti_bloccati', '0', 0, NULL),
	('avvisa_utenti_modificati', '0', 0, NULL),
	('backup_massimi', '10', 0, NULL),
	('cache_media', '1', 0, NULL),
	('cache_pdf', '1', 0, NULL),
	('cache_sezioni', '0', 0, NULL),
	('cache_sezioni_validita', '900', 0, NULL),
	('cache_stile', '0', 0, NULL),
	('capienza_msgbox', '1', 0, NULL),
	('captcha_community', '1', 0, NULL),
	('captcha_contatto', '1', 0, NULL),
	('captcha_utility', '0', 0, NULL),
	('censura', '', 0, NULL),
	('codice_google', '', 0, NULL),
	('commenti_in_elenco', '0', 0, NULL),
	('commenti_lettura', '0', 0, NULL),
	('commenti_moderati', '0', 0, NULL),
	('commenti_scrittura', '0', 0, NULL),
	('community_campo_vis', 'username', 0, NULL),
	('community_admin_obj', '0', 0, NULL),
	('compressione_dati', '0', 0, NULL),
	('contenuti_bozza', 'mostra', 0, NULL),
	('cookie_dominio', 'www.dominiopat.it', 0, NULL),
	('cookie_nome', 'pat_cookie', 0, NULL),
	('cookie_percorso', '/', 0, NULL),
	('cookie_sicurezza', '0', 0, NULL),
	('data_scadenza_default', '2556143356', 0, NULL),
	('descrizione_sito', 'Il portale della Trasparenza conforme al D.Lgs. 33/2013 - "Amministrazione Trasparente"', 0, NULL),
	('download_sicuri', '0', 0, NULL),
	('dtd', 'xhtml', 0, NULL),
	('durata_sessione', '7200', 0, NULL),
	('editor', 'editor_ck3', 0, NULL),
	('flash_object', '0', 0, NULL),
	('googlemaps_default', '1_ROADMAP_8_0_0|indirizzo=Roma, Roma, Lazio, Italia}titolomarker=}icona=803}htmlEditor=', 0, NULL),
	('keywords', 'servizi,uffici,contatti,procedimenti,trasparenza', 0, NULL),
	('googlemaps_static', 'http://maps.google.com/maps/api/staticmap?', 0, NULL),
	('ldap_attr_admin', '', 0, NULL),
	('ldap_attr_user', '', 0, NULL),
	('ldap_bind_pass', '', 0, NULL),
	('ldap_bind_user', '', 0, NULL),
	('ldap_cerca_sub', '', 0, NULL),
	('ldap_contesti', '', 0, NULL),
	('ldap_contesti_admin', '', 0, NULL),
	('ldap_contesto_admin', '', 0, NULL),
	('ldap_contesto_user', '', 0, NULL),
	('ldap_indirizzo', 'ldap://localhost/', 0, NULL),
	('ldap_objectclass', 'objectClass=*', 0, NULL),
	('ldap_profilo_admin', '0', 0, NULL),
	('ldap_profilo_user', '0', 0, NULL),
	('ldap_versione', '3', 0, NULL),
	('licenza_software', 'full', 0, NULL),
	('limita_mappasito', '0', 0, NULL),
	('limite_amministratori', '0', 0, NULL),
	('limite_contenuti', '0', 0, NULL),
	('limite_pagine_elenco', '12', 0, NULL),
	('limite_ricerca', '20', 0, NULL),
	('login_visibile', '0', 0, NULL),
	('mail_sito', 'noreply@dominiopat.it', 0, NULL),
	('mobile_larghezza', '320', 0, NULL),
	('modalita_privacy', '0', 0, NULL),
	('modulo_areariservata', '1', 0, NULL),
	('modulo_centrocomunicazioni', '1', 0, NULL),
	('modulo_community', '0', 0, NULL),
	('modulo_googlemaps', '1', 0, NULL),
	('modulo_ldap', '0', 0, NULL),
	('modulo_mobile', '0', 0, NULL),
	('modulo_multilingua', '0', 0, NULL),
	('modulo_multisito', '0', 0, NULL),
	('modulo_multitemplate', '1', 0, NULL),
	('modulo_newsletter', '0', 0, NULL),
	('modulo_oggetti', '0', 0, NULL),
	('modulo_pdf', '0', 0, NULL),
	('modulo_registrazione', '0', 0, NULL),
	('modulo_rss', '1', 0, NULL),
	('modulo_sms', '1', 0, NULL),
	('modulo_sondaggi', '0', 0, NULL),
	('modulo_statistiche', '1', 0, NULL),
	('modulo_urp', '1', 0, NULL),
	('modulo_versioning', '1', 0, NULL),
	('modulo_webapplication', '1', 0, NULL),
	('modulo_workflow', '1', 0, NULL),
	('motori_ricerca', '1', 0, NULL),
	('mouse_destro_attivo', '1', 0, NULL),
	('nome_paragrafo_automatico', 'Contenuto di sezione', 0, NULL),
	('nome_sito', 'Portale Amministrazione Trasparente', 0, NULL),
	('numero_istanze_blog', '10', 0, NULL),
	('numero_istanze_preview_default', ',', 0, NULL),
	('obj_link_automatico', '1', 0, NULL),
	('oggetti_default', ',', 0, NULL),
	('oggetti_preview_default', ',', 0, NULL),
	('oggetti_riservati_visibili', '0', 0, NULL),
	('pagina_accesso', 'navigazione', 0, NULL),
	('paragrafi_autorizzazione', 'redattore', 0, NULL),
	('paragrafi_versioning', '1', 0, NULL),
	('paragrafo_automatico', '0', 0, NULL),
	('periodo_cancellazione', '2', 0, NULL),
	('player_video', 'integrato', 0, NULL),
	('player_video_autoplay', 'false', 0, NULL),
	('pop3_indirizzo', '', 0, NULL),
	('pop3_mailbox', 'INBOX', 0, NULL),
	('pop3_porta', '110', 0, NULL),
	('pop3_tipo', 'pop3', 0, NULL),
	('privacy_contatto', '', 0, NULL),
	('privacy_registrazione', '', 0, NULL),
	('proprieta_oggetti_default', ',', 0, NULL),
	('registrazione_attiva', '0', 0, NULL),
	('registrazione_gruppoutenti', '0', 0, NULL),
	('registrazione_gruppoutentinewsletter', '0', 0, NULL),
	('registrazione_txt', '', 0, NULL),
	('ricerca_tipo', 'contenuto', 0, NULL),
	('richiesta_attivazione', '0', 0, NULL),
	('risposte_citazioni', '2', 0, NULL),
	('rss_automatici', '1', 0, NULL),
	('smtp_host', 'localhost', 0, NULL),
	('smtp_password', '', 0, NULL),
	('smtp_username', '', 0, NULL),
	('sondaggio_stile_commenti', '0', 0, NULL),
	('sondaggio_stile_correzioni', '0', 0, NULL),
	('sondaggio_stile_domande', '0', 0, NULL),
	('sondaggio_stile_etichette', '0', 0, NULL),
	('sondaggio_stile_risposte', '0', 0, NULL),
	('sondaggio_stile_valutazioni', '0', 0, NULL),
	('sondaggio_tipo', 'avanzato', 0, NULL),
	('stile_paragrafo_automatico', '18', 0, NULL),
	('template_404', '0', 0, NULL),
	('template_default', '0', 0, NULL),
	('template_msg', '0', 0, NULL),
	('template_ricerca', '0', 0, NULL),
	('template_rss', '0', 0, NULL),
	('template_utente', '0', 0, NULL),
	('templegg_bgcol', '#000000', 0, NULL),
	('templegg_linkcol', '#FFFF99', 0, NULL),
	('templegg_txtcol', '#FFFFFF', 0, NULL),
	('ultima_cancellazione', '', 0, NULL),
	('url_parlanti', '1', 0, NULL),
	('usa_smtp', '0', 0, NULL),
	('usa_tags_sezioni', '1', 0, NULL),
	('usa_utenti_personalizzati', '0', 0, NULL),
	('utente_commenti', '0', 0, NULL),
	('utente_registrazioni', '0', 0, NULL),
	('utenti_campi_personalizzati', 'numero_telefono{', 0, NULL),
	('utenti_campi_personalizzati_tipo', 'text{', 0, NULL),
	('utilizza_gd_copyright', '0', 0, NULL),
	('utilizza_gd_copyright_tipo', 'all', 0, NULL),
	('utilizza_gd_formato', 'jpeg', 0, NULL),
	('utilizza_gd_resize', '1', 0, NULL),
	('utilizza_tidy', '1', 0, NULL),
	('versione_software', '3.5', 0, NULL),
	('excel_export', 'excel', 0, NULL),
	('excel_campi', 'data_creazione', 0, NULL),
	('excel_charset', 'cp1252', 0, NULL),
	('modulo_urpegweb', '0', 0, NULL),
	('modulo_ici', '0', 0, NULL),
	('forzatura_sistema', '0', 0, NULL),
	('modulo_commerce', '0', 0, NULL),
	('sondaggi_usanativa', '1', 0, NULL),
	('sondaggi_numeradomande', '0', 0, NULL),
	('modulo_ids', '0', 0, NULL),
	('modulo_ids_azione', 'log', 0, NULL),
	('modulo_ids_block', '0', 0, NULL),
	('modulo_ids_mail', 'administrator@dominiopat.it', 0, NULL),
	('title_doc', '0', 0, NULL),
	('captcha_community_stile', '0', 0, NULL),
	('captcha_contatto_stile', '0', 0, NULL),
	('captcha_utility_stile', '0', 0, NULL),
	('community_interfaccia_stile', '0', 0, NULL),
	('community_commento_stile', '0', 0, NULL),
	('community_risposte_stile', '0', 0, NULL),
	('community_numcommenti_rev', '0', 0, NULL),
	('community_intcommenti_pos', 'prima', 0, NULL),
	('community_inscommento', 'mai', 0, NULL),
	('modulo_webservice_client', '0', 0, NULL),
	('modulo_webservice_server', '0', 0, NULL),
	('id_server_tags', '0', 0, NULL),
	('modulo_backup', '0', 0, NULL),
	('media_lightbox_title', '1', 0, NULL),
	('template_commerce', '0', 0, NULL),
	('commerce_paypal_account', '', 0, NULL),
	('commerce_includi_iva', '1', 0, NULL),
	('tipo_calendario', 'tigra', 0, NULL),
	('msg_riservato', 'Non sei autorizzato ad accedere al contenuto di questa sezione', 0, NULL),
	('msg_nascosto', 'Sezione attualmente non disponibile', 0, NULL),
	('commerce_accessolibero', '0', 0, NULL),
	('msg_registrazione', 'La pagina o funzione richiesta, richiede la registrazione del tuo utente nel sistema. <a href="index.php?azione=registrazione">Accedi alla pagina di registrazione</a>', 0, NULL),
	('modulo_youtube', '0', 0, NULL),
	('url_auth_youtube', 'https://www.google.com/youtube/accounts/ClientLogin', 0, NULL),
	('username_youtube', '', 0, NULL),
	('password_youtube', '', 0, NULL),
	('key_youtube', '', 0, NULL),
	('token_youtube', 'http://gdata.youtube.com/action/GetUploadToken', 0, NULL),
	('link_youtube', 'http://www.youtube.com/v/', 0, NULL),
	('linkesterno_youtube', 'http://www.youtube.com/watch?v=', 0, NULL),
	('secondi_durata_cache_youtube', '0', 0, NULL),
	('cache_amm_youtube', 'false', 0, NULL),
	('visualizza_profilo', 'sempre', 0, NULL),
	('modulo_upload_multiplo', '0', 0, NULL),
	('sms_provider', 'tol', 0, NULL),
	('sms_mittente', '', 0, NULL),
	('sms_host', '', 0, NULL),
	('sms_username', '', 0, NULL),
	('sms_password', '', 0, NULL),
	('sms_limite_caratteri', '160', 0, NULL),
	('sms_user_id', '', 0, NULL),
	('sms_host_utente', '', 0, NULL),
	('sms_username_utente', '', 0, NULL),
	('sms_password_utente', '', 0, NULL),
	('sms_qty_utente', 'h,ll,n|alta,bassa,notifica', 0, NULL),
	('sviluppo_multilingua', '0', 0, NULL),
	('utilizzo_h2', 'descrizione', 0, NULL),
	('utilizzo_h1', 'nome_sezione', 0, NULL),
	('msg_non_pubblicata', 'Sezione attualmente non pubblicata', 0, NULL),
	('forza_link', '0', 0, NULL),
	('utilizza_gd_metodo', 'simpleimage', 0, NULL),
	('campo_data_ricerca', 'default', 0, NULL),
	('visibilita_sito', 'pubblicato', 0, NULL),
	('redirect_sito', '', 0, NULL),
	('html_redirect', '', 0, NULL),
	('file_redirect', '', 0, NULL),
	('googlemaps_segnaposti', '0', 0, NULL),
	('modulo_aggregatore_rss', '1', 0, NULL),
	('root_template', '0', 0, NULL),
	('scadenza_password', '7776000', 0, NULL),
	('id_dominio', '0', 0, NULL),
	('commerce_valore_iva', '20', 0, NULL),
	('commerce_spese_spedizione', '0', 0, NULL),
	('commerce_usa_carrello', '1', 0, NULL),
	('commerce_id_testata', '0', 0, NULL),
	('commerce_id_logo', '0', 0, NULL),
	('commerce_sfondo_colore', '#FFFFFF', 0, NULL),
	('commerce_altro_colore', '#FFFFFF', 0, NULL),
	('commerce_bordo_colore', '#CCCCCC', 0, NULL),
	('commerce_paypal_test', '0', 0, NULL),
	('commerce_mail', '', 0, NULL),
	('commerce_pagamenti', 'paypal', 0, NULL),
	('modulo_valutazione_contenuti', '0', 0, NULL),
	('default_permetti_valutazione', '0', 0, NULL),
	('default_consultazione_valutazioni', '0', 0, NULL),
	('tipologia_valutazioni', '', 0, NULL),
	('frequenza_invio_valutazioni', '', 0, NULL),
	('data_ultimo_invio_valutazioni', '', 0, NULL),
	('mail_report_valutazioni', '', 0, NULL),
	('mail_reparto_tecnico', 'administrator@dominiopat.it', 0, NULL),
	('stili_editor_template', '0', 0, NULL),
	('url_gmaps_api_geocoder', 'http://maps.googleapis.com/maps/api/geocode/xml', 0, NULL),
	('memorizza_navigazione_utenti', '0', 0, NULL),
	('immagineEditorSX', '', 0, NULL),
	('immagineEditorDX', '', 0, NULL),
	('immagineEditorBordoNero', '', 0, NULL),
	('immagineEditorNoBordo', '', 0, NULL),
	('modulo_chat', '0', 0, NULL),
	('responsabile_pubb', '', 0, NULL),
	('rights', '', 0, NULL),
	('header_output_immagini', '1', 0, NULL),
	('visualizza_preferiti', 'mai', 0, NULL),
	('modulo_open_data', '1', 0, NULL),
	('open_data_stile_link', '177', 0, NULL),
	('open_data_img', '0', 0, NULL),
	('open_data_testo', 'Per questa informazione, sono disponibili i dati in formato aperto.', 0, NULL),
	('open_data_posizione', 'sotto', 0, NULL),
	('calendario_tooltip', '0', 0, NULL),
	('dataUltimoAggiornamentoAVCP', '1422079201', 0, NULL),
	('versione_pat', '1.0', 0, NULL);
/*!40000 ALTER TABLE `configurazione` ENABLE KEYS */;

-- Dump dei dati della tabella pat.cookie_personalizzati: 1 rows
DELETE FROM `cookie_personalizzati`;
/*!40000 ALTER TABLE `cookie_personalizzati` DISABLE KEYS */;
INSERT INTO `cookie_personalizzati` (`id`, `nome`, `cerca`, `nome_variabile`, `codice_php`, `valore_default`, `is_array`, `tipo_dato`, `tipo_errato`) VALUES
	(1, 'id_ente', 'get_post', 'id_ente', '', '0', 'no', 'qualsiasi', 'valore_default');
/*!40000 ALTER TABLE `cookie_personalizzati` ENABLE KEYS */;

-- Dump dei dati della tabella pat.etrasp_enti: 1 rows
DELETE FROM `etrasp_enti`;
/*!40000 ALTER TABLE `etrasp_enti` DISABLE KEYS */;
INSERT INTO `etrasp_enti` (`id`, `stato`, `data_creazione`, `id_creatore`, `data_attivazione`, `data_scadenza`, `nome_completo_ente`, `nome_breve_ente`, `tipo_ente`, `url_etrasparenza`, `url_sitoistituzionale`, `url_albopretorio`, `url_cmscollegato`, `url_social_facebook`, `url_social_twitter`, `url_social_youtube`, `url_social_google`, `url_social_flickr`, `file_logo_semplice`, `file_logo_etrasp`, `cookie_dominio`, `cookie_nome`, `canale_opendata`, `oggetto_provvedimenti`, `oggetto_bandi_gara`, `oggetto_incarichi`, `oggetto_sovvenzioni`, `oggetto_normativa`, `oggetto_concorsi`, `oggetto_tabelle_avcp`, `mostra_data_aggiornamento`, `personale_ruoli`, `bandi_gara_amm_agg`, `bandi_gara_cod_fisc`, `bandi_gara_tipo_amm`, `bandi_gara_prov`, `bandi_gara_comune`, `bandi_gara_indirizzo`, `indirizzo_via`, `indirizzo_cap`, `indirizzo_comune`, `indirizzo_provincia`, `telefono`, `email`, `email_certificata`, `responsabile_pubblicazione`, `p_iva`, `testo_welcome`, `testo_footer`, `modulo_webservice`, `aggiorna_avcp`, `google_analitycs`, `file_organigramma`, `id_ente_albo`, `url_etrasparenza_multidominio`, `chiave_webservice`, `indicizzabile`) VALUES
	(1, 1, 0, 0, 0, 0, 'Comune da impostare', 'da impostare', 6, 'http://www.dominiopat.it', 'http://www.dominiosito.it', 'http://albo.dominiosito.it', NULL, '', '', '', '', '', '14771221170O__Orapallo.jpg', '131841336201O__Ologo.gif', 'esempio.dominiopat.it', 'trasp_esempio', 0, 1, 1, 1, 1, 1, 1, 1, 1, '', '', '', '', '', '', '', 'Via Esempio , 1', '00001', 'Esempio', 'ES', '+39.0123.456789', 'mod@esempio.it', 'pec@pec.comune.esempio.it', 'Da inserire', '00000000', '', '', 0, 1, '', '', 3, 'http://alias.dominiopat.it,http://alias2.dominiopat.it', 'www.dominiopat.it', 0);
/*!40000 ALTER TABLE `etrasp_enti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.etrasp_moduli: 0 rows
DELETE FROM `etrasp_moduli`;
/*!40000 ALTER TABLE `etrasp_moduli` DISABLE KEYS */;
/*!40000 ALTER TABLE `etrasp_moduli` ENABLE KEYS */;

-- Dump dei dati della tabella pat.etrasp_paragrafi: 0 rows
DELETE FROM `etrasp_paragrafi`;
/*!40000 ALTER TABLE `etrasp_paragrafi` DISABLE KEYS */;
/*!40000 ALTER TABLE `etrasp_paragrafi` ENABLE KEYS */;

-- Dump dei dati della tabella pat.etrasp_ruoli: 12 rows
DELETE FROM `etrasp_ruoli`;
/*!40000 ALTER TABLE `etrasp_ruoli` DISABLE KEYS */;
INSERT INTO `etrasp_ruoli` (`id`, `id_ente`, `nome`, `descrizione`, `admin`, `utenti`, `ruoli`, `archiviomedia`, `gestione_workflow`, `strutture`, `personale`, `commissioni`, `societa`, `procedimenti`, `regolamenti`, `modulistica`, `normativa`, `bilanci`, `fornitori`, `bandigara`, `avcp`, `bandiconcorso`, `sovvenzioni`, `incarichi`, `provvedimenti`, `oneri`, `contenuti`, `speciali`, `ealbo_import`) VALUES
	(3, 0, 'Amministratore completo di PAT', 'Ruolo di utenti che possono gestire qualunque caratteristica e qualunque informazione del portale dedicato alla Trasparenza', 1, 1, 1, 1, NULL, 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:101:{i:711;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:43;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:747;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:768;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:774;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:804;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:712;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:701;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:709;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:710;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:25;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:65;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:19;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:61;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:765;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:713;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:748;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:50;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:51;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:68;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:749;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:54;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:59;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:609;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:63;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:53;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:640;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:641;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:806;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:807;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:714;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:44;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:715;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:56;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:57;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:716;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:778;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:779;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:780;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:717;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:718;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:64;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:719;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:720;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:21;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:721;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:22;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:722;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:723;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:724;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:725;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:726;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:727;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:566;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:787;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:636;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:788;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:790;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:789;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:803;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:799;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:728;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:48;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:729;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:802;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:730;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:731;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:732;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:733;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:734;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:735;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:736;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:737;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:632;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:62;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:738;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:800;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:46;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:739;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:740;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:775;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:776;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:777;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:741;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:781;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:782;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:783;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:784;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:785;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:786;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:742;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:743;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:744;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:745;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:746;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:769;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:770;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:771;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:772;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}}', '', 0),
	(180, 0, 'Profilo A PAT - Gestore Bandi Gare e contratti', 'Ruolo di gestore delle pubblicazioni riguardanti Gare e procedure', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:787;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:636;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:788;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:790;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:789;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(181, 0, 'Profilo B PAT - Gestore Organizzazione ente', 'Ruolo di gestore delle informazioni inerenti all\\\\\\\' organizzazione dell\\\\\\\'ente', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:701;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:65;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:19;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:61;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:765;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:713;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:748;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:50;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:51;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:68;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:749;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:54;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:59;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:609;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:63;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:53;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:798;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";s:1:"1";}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:721;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:22;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:722;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:723;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(182, 0, 'Profilo C PAT - Gestore Provvedimenti amministrativi', 'Ruolo di gestore delle pubblicazioni relative ai provvedimenti amministrativi', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:725;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:726;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(183, 0, 'Profilo D PAT - Gestore Bandi di Concorso', 'Ruolo di gestore delle informazioni relative ai bandi di concorso', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:640;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:641;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(184, 0, 'Profilo E PAT - Gestore Modulistica', 'Ruolo di gestore della modulistica dell\\\\\\\'ente', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(185, 0, 'Profilo F PAT - Gestore Regolamenti Statuti e Codici', 'Ruolo di gestore delle pubblicazioni inerenti Regolamenti, statuto comunale e codici', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(186, 0, 'Profilo G PAT - Gestore Bilanci', 'Ruolo di gestore delle pubblicazioni relative ai bilanci', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:731;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:732;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(187, 0, 'Profilo H PAT - Gestore Normativa', 'Ruolo di gestore delle pubblicazioni inerenti la Normativa', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(188, 0, 'Profilo I PAT - Gestore Sovvenzioni e vantaggi economici', 'Ruolo di gestore delle pubblicazioni inerenti Sovvenzioni, contributi, sussidi e vantaggi economici', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:48;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:729;a:4:{s:8:"modifica";s:1:"1";s:8:"workflow";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(189, 0, 'Profilo L PAT - Gestore Oneri informativi ed obblighi', 'Ruolo di gestore delle pubblicazioni relative ad oneri informativi ed obblighi', 0, 0, 0, 2, NULL, 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}', 'a:8:{s:8:"workflow";s:1:"1";s:7:"lettura";s:1:"1";s:9:"creazione";s:1:"1";s:8:"modifica";s:1:"1";s:13:"cancellazione";s:1:"1";s:5:"stato";s:1:"1";s:8:"permessi";s:1:"1";s:8:"avanzate";s:1:"1";}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0),
	(336, 0, 'Amministratore Workflow', 'Gestione del workflow', 0, 0, 0, 0, 1, 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:9:{s:8:"workflow";i:0;s:7:"lettura";i:0;s:9:"creazione";i:0;s:8:"modifica";i:0;s:13:"cancellazione";i:0;s:5:"stato";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;s:14:"notifiche_push";i:0;}', 'a:101:{i:711;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:43;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:747;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:768;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:774;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:804;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:712;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:701;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:709;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:710;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:25;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:65;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:19;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:61;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:765;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:713;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:748;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:50;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:51;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:68;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:749;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:54;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:59;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:609;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:63;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:53;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:798;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:639;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:640;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:641;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:806;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:807;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:714;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:44;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:715;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:56;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:57;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:716;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:778;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:779;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:780;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:717;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:718;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:64;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:719;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:720;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:21;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:721;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:22;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:722;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:723;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:724;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:725;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:726;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:727;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:566;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:787;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:636;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:788;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:790;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:789;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:803;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:799;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:728;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:48;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:729;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:802;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:730;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:731;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:732;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:733;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:734;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:735;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:736;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:737;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:632;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:62;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:738;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:800;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:46;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:739;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:740;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:775;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:776;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:777;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:741;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:781;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:782;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:783;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:784;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:785;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:786;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:742;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:743;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:744;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:745;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:746;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:769;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:770;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:771;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}i:772;a:4:{s:8:"modifica";i:0;s:8:"workflow";i:0;s:8:"permessi";i:0;s:8:"avanzate";i:0;}}', '', 0);
/*!40000 ALTER TABLE `etrasp_ruoli` ENABLE KEYS */;

-- Dump dei dati della tabella pat.menu: 5 rows
DELETE FROM `menu`;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`id`, `nome`, `tipologia`, `titolo_link`, `id_sezione`, `id_criterio`, `id_oggetto`, `id_sezioni`, `ramificabile`, `includi_categorie`, `attiva_percorso`, `inizio`, `limite`, `aperto`, `stile_avanzato`, `template`) VALUES
	(1, 'Organizzazione', 'automatico', 1, 712, 0, 0, '', 1, 1, 1, 1, 0, 'no', '0-0-0-0-0', 'menu_normale'),
	(2, 'Trasparenza', 'automatico', 1, 18, 0, 0, '', 1, 0, 1, 1, 0, 'no', '0-0-0-0-0', 'menu_normale'),
	(3, 'Tools', 'automatico', 0, 3, 0, 0, '', 0, 0, 1, 1, 0, 'no', '0-0-0-0-0', 'menu_normale'),
	(4, 'Menu Navigazione Rapida', 'automatico', 0, 562, 0, 0, '', 0, 0, 0, 1, 0, 'no', '0-0-0-0-0', 'menu_normale'),
	(5, 'Attività e procedimenti', 'automatico', 1, 21, 0, 0, '', 1, 0, 1, 1, 0, 'no', '0-0-0-0-0', 'menu_normale');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetti: 41 rows
DELETE FROM `oggetti`;
/*!40000 ALTER TABLE `oggetti` DISABLE KEYS */;
INSERT INTO `oggetti` (`id`, `nome`, `nomedb`, `descrizione`, `tabella`, `template_default`, `commenti`, `id_proprietario`, `tipo_proprietari_admin`, `id_proprietari_admin`, `proprieta`, `oggetto_sistema`, `obj_db_dati`, `richiesta_autorizzazione`, `versioning`, `upload_multiplo`, `ricercabile`, `link_automatico`, `link_stile`, `forza_nativa`, `lettura_completa_stile`, `amministrazione`, `interfaccia`, `id_categoria`, `mail_associata`, `int_admin`, `int_speciale`, `int_speciale_campo`, `num_default`, `num_admin`, `ordine_default`, `ordine_default_admin`, `senso_ordine_default`, `senso_ordine_default_admin`, `campo_default`, `campi_default_alternativi`, `campo_data_ricerca`, `criterio_ricerca`, `struttura_tipo`, `struttura_etichette`, `struttura_valori`, `struttura_proprieta`, `struttura_default`, `struttura_maxchar`, `struttura_ordinamento`, `campi_ricerca`, `campi_ricerca_titoli`, `campi_ricerca_proprieta`, `campi_ricerca_stile`, `campi_ricerca_admin`, `campi_ricerca_admin_titoli`, `campi_ricerca_admin_proprieta`, `campi_richiami`, `campi_richiami_titoli`, `campi_richiami_proprieta`, `campi_richiami_stile`, `campi_elenco`, `campi_elenco_titoli`, `campi_elenco_proprieta`, `campi_elenco_stile`, `campi_admin`, `campi_admin_titoli`, `campi_admin_proprieta`, `campi_admin_stile`, `campi_review`, `campi_review_titoli`, `campi_review_proprieta`, `campi_review_stile`, `campi_webapp`, `campi_webapp_titoli`, `campi_webapp_proprieta`, `campi_webapp_stile`, `id_server_ws`, `id_oggetto_ws`, `permetti_valutazione`, `consultazione_valutazioni`) VALUES
	(1, 'Elemento contenuto', 'paragrafo', 'Oggetto paragrafo. Permette la pubblicazione di elementi aree testo e paragrafi html.', 'oggetto_paragrafo', 'N/A', 0, 0, 'proprietario', '0', 'core', 0, '', 'nessuna', 0, 0, 1, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'nome', 'ultima_modifica', '', '', 'nome', 'id', '', 0, '*text{editor{', 'Titolo{Contenuto{', '{{', '0{0{', '{{', '255{{', '1{0{', 'nome,contenuto', NULL, NULL, NULL, 'nome,contenuto', NULL, NULL, '', '', '', '', '', '', '', '', 'nome{contenuto', 'Titolo del contenuto{anteprima', '{anteprima|120', '0{0', '', '', '', '', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL),
	(2, 'Come fare per', 'aree_tematiche', 'Oggetto informativo per il raggruppamento di varie informazioni in aree tematiche dedicate al come fare per', 'oggetto_aree_tematiche', 'informativa', 0, 0, 'gruppo', ',5,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 0, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'alfabetica', 'area', '20', '20', 'area', 'area', '', '', 'area', 'id', '', 0, '*numerico{*text{textarea{editor{camposezione_multi{', 'Identificativo Ente{Area tematica{Descrizione breve{Descrizione{Sezioni consigliate{', '{{{{{', '{{{{{', '0{{{{{', '{{{{{', '1{1{0{0{0{', 'area', 'Area tematica(*): }', 'normale||', '0', 'area,lista_sezioni', 'Area tematica}{Sezioni consigliate}', 'normale||{normale||', 'area', '}', 'linkreview|normale|', '0', '', '', '', '', 'area{lista_sezioni', 'Area tematica}{Sezioni consigliate}', 'normale||{normale||', 'undefined{undefined', 'area{descrizione{id{lista_sezioni', '}{}{Risorse consigliate}{}', 'normale||{normale||{soloetichetta||}criterio sezioneEsiste($istanzaOggetto[lista_sezioni]){linklista||', '24{114{86{134', '', '', '', '', 0, 0, 0, 0),
	(3, 'Personale ente', 'riferimenti', 'Oggetto per la pubblicazione dei referenti e dei riferimenti degli uffici dell\'ente. Utilizzato anche dalla sezione Operazione Trasparenza', 'oggetto_riferimenti', 'informativa', 0, 0, 'gruppo', ',15,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'alfabetica', 'referente', '10', '10', 'referente', 'ultima_modifica', '', 'DESC', 'referente', 'id', '', 0, '*numerico{select{*text{select{campoggetto_multi{casella{campoggetto_multi{text{text{casella{editor{text{file{filemedia{text{text{text{*email{email{file{file{file{file{file{file{file{file{file{editor{editor{editor{editor{file{file{casella{casella{data_calendario{data_calendario{*numerico{text{text{editor{editor{casella{', 'Identificativo ente{Titolo{Nome referente (cognome-nome){Ruolo{Riferimento incarico amministrativo di vertice{Contratto tempo determinato{Uffici di appartenenza{Incarico di stampo politico{Organo politico-amministrativo{Con delega{Delega a{Commissioni di appartenenza{Atto di nomina o proclamazione{Foto referente{Telefono fisso{Telefono mobile{Fax{Email{Email certificata{Curriculum{Atto di conferimento{Retribuzione{Retribuzione anni precedenti{Retribuzione anni precedenti{Dati su altre cariche{Dichiarazione patrimoniale{Dichiarazione patrimoniale anni precedenti{Dichiarazione patrimoniale anni precedenti{Note{Compensi connessi alla carica{Importi di viaggi di servizi e missioni{Altri incarichi con oneri a carico della finanza pubblica e relativi compensi{Dichiarazione insussistenza cause inconferibilità{Dichiarazione insussistenza cause incompatibilità{Visualizza negli elenchi{Visualizza in archivio storico{In carica da{In carica fino a{Priorita di visualizzazione{Id Originale{Proprieta generali{Altre informazioni{Archivio informazioni{Omissis{', '{,Arch.,Avv.,Dott.,Dott.ssa,Ing.,Geom.,Prof.,Prof.ssa,Rag.,Per.{{Dipendente,P.O.,Funzionario,Dirigente,Incaricato politico{4{{13{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{', '0{{{Dipendente{{1{{{{0{{{{{{{{{{{{{{{{{{{{{{{{{1{1{gg/mm/aaaa{gg/mm/aaaa{1{{{{{0{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{', '1{1{1{1{2{1{0{2{0{2{2{1{2{2{0{0{0{0{0{0{2{2{0{0{0{0{0{0{2{2{2{2{2{2{1{2{2{2{2{2{0{2{2{2{', 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', '', '', '', 'foto{referente{ruolo{organo{commissioni{ruolo_politico{note{id{telefono{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{id{determinato{allegato_nomina{curriculum{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{incarico{altre_cariche{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita{altre_info', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{Ruolo:  <strong>}</strong>{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{Commissioni di appartenenza: }{Incarico di stampo politico: }{}{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono fisso: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Referente per le strutture</div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Provvedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{Altri dati}{Contratto tempo determinato: }{Atto di nomina o proclamazione: }{Curriculum: }{Dati sulla retribuzione }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali:  }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Dati su altre cariche: }{<div style="clear:both"></div>}{<strong>Compensi connessi alla carica</strong>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }{<div class="campoOggetto86">Altre informazioni</div>}', 'normale||{soloetichetta||{normale||{soloetichettadv||{normale||{normale||{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichetta||{normale||{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){soloetichettadv||incarico}!= \'\'{linkcompleto||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{normale||', '126{171{114{0{0{0{80{0{86{77{77{114{114{114{0{0{0{0{0{0{86{0{48{48{48{48{48{48{48{48{0{48{0{80{80{80{48{48{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', 'referente{ruolo{ruolo_politico{uffici{archivio{id_proprietario{ultima_modifica', 'Nome}{Ruolo}{Incarico di stampo politico}{Uffici di appartenenza}{In archivio storico}{Creato da}{Data di ultima modifica}', 'normale||{normale||{normale||{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined{undefined{undefined', 'foto{referente{carica_inizio{carica_fine{ruolo{organo{commissioni{ruolo_politico{ruolo_politico{testo_delega{note{id{telefono{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{incarico{id{determinato{allegato_nomina{curriculum{atto_conferimento{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{altre_cariche{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita{altre_info{archivio_informazioni{id{ultima_modifica', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{In carica da: <strong>}</strong>{In carica fino a: <strong>}</strong>{Ruolo:  <strong>}</strong>{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{Commissioni di appartenenza: }{Incarico di stampo politico: }{Organo di controllo (art.20 d.lgs 30 giugno 2011, n.123): }{<div class="campoOggetto86"> Consigliere con delega a: </div> }{}{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono fisso: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Referente per le strutture</div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Procedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Altri dati}{Contratto tempo determinato: }{Atto di nomina o proclamazione: }{Curriculum: }{Atto di conferimento: }{Dati sulla retribuzione: }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali: }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{Dati su altre cariche: }{<div style="clear:both"></div>}{<div class="campoOggetto86">Compensi connessi alla carica</div>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }{<div class="campoOggetto86">Altre informazioni</div>}{<div class="campoOggetto86">Archivio informazioni</div>}{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{soloetichetta||{formatodata|d-m-Y|{formatodata|d-m-Y|{normale||}criterio !moduloAttivo(\'agid\'){soloetichettadv||{normale||{normale||}criterio !moduloAttivo(\'agid\'){normale||}criterio moduloAttivo(\'agid\'){normale||delega}== 1{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichettadv||incarico}!= \'\'{soloetichetta||{normale||ruolo}!= \'Incaricato politico\'{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (moduloAttivo(\'privacy\')  AND (!$istanzaOggetto[omissis] AND !$istanzaOggetto[archivio])){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (moduloAttivo(\'privacy\')  AND (!$istanzaOggetto[omissis] AND !$istanzaOggetto[archivio])){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (moduloAttivo(\'privacy\')  AND (!$istanzaOggetto[omissis] AND !$istanzaOggetto[archivio])){linkcompleto||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{normale||{normale||{soloetichetta||{formatodata|d-m-Y|', '126{171{114{114{114{0{0{0{0{0{80{0{86{77{77{67{114{114{0{0{0{0{0{0{0{86{0{48{48{48{48{48{48{48{48{48{48{0{80{80{80{48{48{0{0{0{0', '', '', '', '', 0, 0, 0, 0),
	(4, 'Incarichi e consulenze', 'incarichi', 'Archivio di Incarichi e Consulenze', 'oggetto_incarichi', 'informativa', 0, 0, 'gruppo', ',10,', 'informativa', 0, '', 'nessuna', 0, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'inizio_incarico', 'inizio_incarico', 'DESC', 'DESC', 'nominativo', 'id', '', 157, '*numerico{*text{*text{select{casella{campoggetto{data_calendario{data_calendario{*textarea{text{textarea{editor{editor{*file{editor{file{file{file{text{numerico{select{', 'Identificativo Ente{Nominativo{Oggetto{Tipo di incarico{Incarico amministrativo di vertice o dirigenziale{Struttura organizzativa responsabile{Inizio incarico{Fine incarico{Compenso{Compenso erogato{Componenti variabili del compenso{Note (incarichi, cariche, altre attività){Estremi atti di conferimento{Atto di conferimento{Modalità seguita per l\'individuazione{Progetto selezionato{Curriculum del soggetto incaricato{Attestazione della verifica sul conflitto d\'interessi{Id Originale{ID atto eAlbo{Stato pubblicazione{', '{{{incarichi dipendenti interni,incarichi dipendenti altra amministrazione,incarichi dipendenti esterni{{13{{{{{{{{{{{{{{{100,40{', '{{{incarichi retribuiti e non retribuiti dei propri dipendenti,incarichi retribuiti e non retribuiti altra amministrazione,incarichi retribuiti e non retribuiti affidati a soggetti esterni{{{0{0{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '0{{{{1{{0{gg/mm/aaaa{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{', '1{2{2{1{1{1{2{2{0{2{0{2{0{0{2{2{2{2{2{2{2{', 'nominativo,oggetto,inizio_incarico,tipo_incarico,struttura', 'Nominativo}{Oggetto}{Inizio incarico}{Tipo di incarico}{Ufficio}', 'normale||{normale||{normale||{normale||{inputsearch||', '71{71{71{71{71', 'nominativo,oggetto,inizio_incarico,compenso', '', '', 'oggetto{nominativo{struttura{inizio_incarico', '}{Nominativo: }{Struttura organizzativa: }{Inizio incarico: }', 'linkreview|normale|{normale||{link||{formatodata|d-m-Y|', '25{114{0{114', 'oggetto{nominativo{inizio_incarico{fine_incarico', '}Oggetto{}Nominativo{}Inizio incarico{}Fine incarico', 'linkreview|normale|{normale||{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0{0', 'nominativo{oggetto{tipo_incarico{id_proprietario{ultima_modifica', 'Nominativo}{Oggetto}{Tipologia}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', 'oggetto{inizio_incarico{fine_incarico{tipo_incarico{nominativo{struttura{compenso{compenso_erogato{compenso_variabile{dirigente{modo_individuazione{estremi_atti{note{id{progetto{cv_soggetto{file_atto{verifica_conflitto{id{ultima_modifica', '<span style="font-weight:bold;">}</span>{Inizio incarico: <strong>}</strong> {- Fine incarico: <strong>}</strong>{Tipo di incarico: }{Nominativo: }{Struttura organizzativa: }{Compenso: }{Compenso erogato: }{Componenti variabili del compenso: }{Incarico amministrativo di vertice o dirigenziale: }{<div class="campoOggetto86">Modalità seguita per l\'individuazione</div>}{<div class="campoOggetto86">Estremi atto di conferimento</div>}{<div class="campoOggetto86">Note</div>}{<div class="campoOggetto86">Allegati</div>}{scarica il progetto selezionato: }{scarica il curriculum del soggetto:}{scarica atto di conferimento: }{scarica attestazione della verifica sul conflitto d\'interessi: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{formatodata|d-m-Y|{formatodata|d-m-Y|{normale||{normale||{link||{normale||{normale||{normale||{normale||{normale||{normale||{normale||{soloetichetta||}criterio $istanzaOggetto[progetto] != \'\' OR $istanzaOggetto[cv_soggetto] != \'\'  OR $istanzaOggetto[file_atto] != \'\' {linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{67{67{0{0{0{0{0{0{0{0{0{0{0{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(5, 'Modulistica', 'modulistica_regolamenti', 'Oggetto informativo per la gestione di un archivio di modustica dell\'ente', 'oggetto_modulistica_regolamenti', 'informativa', 0, 0, 'gruppo', ',12,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '10', '10', 'ordine', 'ultima_modifica', '', 'DESC', 'titolo', 'id', '', 0, '*numerico{*text{campoggetto_multi{*file{file{editor{*numerico{text{text{', 'Identificativo Ente{Titolo{Procedimenti associati{Allegato{Allegato 1{Descrizione{Ordine di visualizzazione{Id Originale{Proprieta generali{', '{{16{{{{{{{', '{{{{{{{{{', '2{{{{{{1{{{', '{{{{{{{{{', '1{1{0{0{2{0{1{0{0{', 'titolo', 'Nome }', 'normale||', '71', 'titolo,procedimenti', 'undefined{Titolo}', 'undefined{normale||', 'titolo{descrizione_mod{id', '}{}{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}', 'linkreview|normale|{anteprima|120|{soloetichetta||}criterio $_GET[id_doc] > 0', '137{138{0', 'titolo{procedimenti{descrizione_mod{id', '}{Procedimenti associati: }{}{dettagli}', 'linkreview|normale|{link||{anteprima|240|{etichettalink||', '137{138{138{47', 'titolo{procedimenti{allegato{id_proprietario{ultima_modifica', 'Titolo}{Procedimenti associati}{Allegato}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{link||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', 'titolo{procedimenti{descrizione_mod{allegato{allegato_1{id{ultima_modifica', '}{Procedimenti associati: }{}{Scarica il modulo: }{Scarica il modulo: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{normale||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{114{114{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(6, 'Eventi in agenda', 'eventi', 'Oggetto con interfaccia calendario, per la creazione di un\'agenda eventi del portale. Collegato con l\'oggetto photogallery', 'oggetto_eventi', 'informativa', 0, 0, 'gruppo', ',8,', 'informativa', 0, '', 'nessuna', 1, 0, 0, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'calendario', 'data_inizio', '20', '20', 'data_inizio', 'data_inizio', 'DESC', 'DESC', 'evento', 'id', 'data_inizio', 0, '*text{immagine{text{*data_calendario{data_calendario{*tags{textarea{editor{file{linkinterno{', 'Nome evento{Immagine{Luogo evento{Data inizio evento{Data fine evento{Argomenti (tags) associati{Testo di presentazione{Dettagli evento{Allegato{Maggiori informazioni{', '{{{{{{{{{{', '{{0{{{{{{{{', '{{{0{gg/mm/aaaa{{{{{{', '{{{{{{{{{{', '1{0{2{2{2{0{2{0{0{0{', 'evento,luogo_evento,id,data_inizio,data_inizio,data_fine', 'Nome evento: }{Luogo: }{&nbsp;}{<strong>Periodo evento: </strong>}{dal: }{al: }', 'normale||{normale||{soloetichetta||{soloetichetta||{normaledatamag||{normaledatamin||', '71{71{0{71{71{71', 'evento,luogo_evento,data_inizio,data_fine,dettagli_evento', '', '', 'evento{data_inizio{data_fine{luogo_evento{id', '}{Dal }{ al }{Luogo: }{<? $configurazione[\'pres_news\']=true;?>}', 'linkreview|normale|{formatodata|d-m-Y|{formatodata|d-m-Y|{normale||{soloetichettadv||', '25{67{67{0{0', 'evento{immagine{luogo_evento{data_inizio{data_inizio{data_fine{presentazione', '}{}{}{ - il <strong>}</strong>{- dal <strong>}</strong>{ al <strong>}</strong>{}', 'linkreview|normale|{linkreview|normale|{normale||{formatodata|d-m-Y|data_fine}== \'\'{formatodata|d-m-Y|data_fine}!= \'\'{formatodata|d-m-Y|{normale||', '25{27{67{67{67{67{114', 'evento{luogo_evento{data_inizio{data_fine', 'Evento:}{Luogo evento: }{Data inizio evento: }{Data fine evento: }', 'linkreview||{normale||{formatodata|D d M, Y|{formatodata|D d M, Y|', 'undefined{undefined{undefined{undefined', 'evento{data_inizio{data_fine{data_inizio{luogo_evento{immagine{dettagli_evento{id{maggiori_info{allegato{id', '}{Evento dal <strong>}</strong>{ al <strong>}</strong>{Evento del <strong>}</strong>{<strong>}</strong>{}{}{&nbsp;}{Maggiori informazioni: }{<strong>scarica allegato a questo evento</strong>}{<br /><br />}', 'normale||{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] == \'\' OR $istanzaOggetto[data_inizio] == $istanzaOggetto[data_fine]{normale||{normale||{normale||{soloetichetta||{link||{linketicompleto||{soloetichetta||', '24{67{67{114{25{118{67{0{128{48{0', '', '', '', '', 0, 0, 0, 0),
	(7, 'Banner', 'banner', 'Oggetto informativo per gestione di un\'area banner', 'oggetto_banner', 'informativa', 0, 0, 'gruppo', ',4,', 'informativa', 0, '', 'nessuna', 0, 0, 0, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'priorita', 'ultima_modifica', '', 'DESC', 'titolo', 'id', '', 0, '*text{textarea{casella{camposezione_multi{select{*linkinterno{*filemedia{select{*numerico{', 'Titolo del banner{Descrizione Breve{Visualizza sempre{Visualizza in sezioni{Visualizza in zona{Link di destinazione{File immagine (larghezza max 180px){Usa bordo su immagine{Ordine di visualizzazione{', '{{{{colonna destra,colonna sinistra{{{no,si{{', '0{0{0{0{0{0{{{0{', '{{1{{colonna destra{{{{1{', '{{{{{{{{{', '2{2{2{2{2{2{2{2{2{', '', '', '', '', 'titolo,descrizione', '', '', 'immagine{immagine', '}{}', 'linkcampo|destinazione|bordo}== \'si\'{linkcampo|destinazione|bordo}!= \'si\'', '161{160', '', '', '', '', 'titolo{immagine{sezioni{visualizzato{priorita', 'Titolo{File}{Visualizzato in sezioni{Sempre{Priorita', 'normale||{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(8, 'Link utili', 'link', 'Oggetto informativo per la creazione di un archivio categorizzato di links utili ai navigatori', 'oggetto_link', 'informativa', 0, 0, 'gruppo', ',11,', 'informativa', 0, '', 'nessuna', 0, 0, 1, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'titolo', 'ultima_modifica', '', 'DESC', 'titolo', 'id', '', 0, '*text{*linkinterno{textarea{*campoggetto_multi{', 'Titolo{Indirizzo{Note{Area Tematica{', '{{{2{', '{{{{', '{{{{', '{{{{', '1{0{0{0{', '', '', '', '', 'titolo,indirizzo,note', '', '', 'titolo', '}', 'linkcampo|indirizzo|', '87', 'titolo{area{note', '}Risorsa{}Area Tematica{}Note', 'linkcampo|indirizzo|{link||{normale||', '25{114{114', 'titolo{indirizzo{note{id_proprietario{ultima_modifica', 'Titolo}{Indirizzo}{Note}{Creato da}{Ultima modifica}', 'linkreview|normale|{link||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(9, 'Galleria immagini', 'immagini', 'Oggetto con proprietà lightbox per la visualizzazione di un archivio di foto con riferimento agli eventi dell\'ente', 'oggetto_immagini', 'informativa', 0, 0, 'gruppo', ',9,', 'informativa', 0, '', 'nessuna', 0, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'gallery_lightbox', 'immagine', '12', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'titolo_imm', 'id', '', 0, '*text{campoggetto{*tags{*filemedia{editor{', 'Titolo immagine{Evento in agenda relativo{Argomenti (tags) associati{File immagine{Note sull\'immagine{', '{6{{{{', '0{{{0{0{', '{{{{{', '{{{{{', '1{2{0{0{0{', '', '', '', '', 'titolo_imm,note', '', '', 'immagine{id', '}{<? $configurazione[\'pres_imm\']=true;?>}', 'linkreview|normale|{soloetichettadv||', '89{0', 'immagine', '}', 'linkreview|normale|', '60', 'titolo_imm{immagine{note', 'Titolo}{Immagine}{Note sull\'immagine: }', 'normale||{normale||{anteprima|120|', 'undefined{undefined{undefined', 'titolo_imm{immagine{note{id{evento', '}{}{<br />}{Questa immagine è associata all\'evento}{<div style="margin: 10px 5px;">}</div>', 'normale||{normale||{normale||{soloetichetta||evento}!= 0{oggettorichiamo||', '24{55{114{86{0', '', '', '', '', 0, 0, 0, 0),
	(10, 'Notizie e Comunicati', 'notizie', 'Oggetto informativo per la presentazione delle Notizie e Comunicati del Comune', 'oggetto_notizie', 'informativa', 0, 0, 'gruppo', ',13,', 'informativa', 0, '', 'nessuna', 1, 0, 0, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data', 'data', 'DESC', 'DESC', 'titolo', 'id', '', 0, '*text{*data_calendario{*data_calendario{data_calendario{casella{*tags{*immagine{editor{file{file{file{linkinterno{', 'Titolo{Data{Data di inizio pubblicazione{Data di fine pubblicazione{Pubblicazione in primo piano{Argomenti (tags) associati{Immagine Associata{Contenuto{Allegato{Allegato{Allegato{Maggiori informazioni{', '{{{{{{{{{{{{', '{{{{{{{0{0{{{{', '{0{0{gg/mm/aaaa{1{{{{{{{{', '{{{{{{{{{{{{', '1{1{0{0{2{0{0{2{2{2{2{2{', 'titolo,contenuto', '', '', '', 'data,titolo,contenuto', '', '', 'data{titolo{id', '}{}{<? $configurazione[\'pres_eve\']=true;?>}', 'formatodata|d-m-Y|{linkreview|normale|{soloetichettadv||', '32{25{0', 'titolo{immagine{data{contenuto', '}{}{<strong>}</strong> -{}', 'linkreview|normale|{linkreview|normale|{formatodata|d-m-Y|{anteprima|400|', '25{27{67{67', 'titolo{immagine{data', 'Titolo: }{Immagine Associata(*): }{Data: }', 'linkreview||{normale||{formatodata|D d M, Y|', 'undefined{undefined{undefined', 'titolo{immagine{data{contenuto{id{maggiori_info{allegato{allegato1{allegato2{id', '}{}{<strong>}</strong> -{}{&nbsp;}{Maggiori informazioni: <strong>}</strong>{<strong>}</strong>{<strong>}</strong>{<strong>}</strong>{<br /><br />}', 'normale||{normale||{formatodata|d-m-Y|{normale||{soloetichetta||{link||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||', '24{118{127{67{0{128{48{48{48{0', '', '', '\n			\n			\n			\n			', '', 0, 0, 0, 0),
	(11, 'Bandi di Gara', 'gare_atti', 'Oggetto informativo per la gestione dell\'archivio dei bandi del Comune', 'oggetto_gare_atti', 'informativa', 0, 0, 'gruppo', ',2,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'data_creazione', 'DESC', 'DESC', 'oggetto', 'oggetto', '', 0, 'numerico{select{select{text{text{select{select{comuni{text{campoggetto_multi{select{text{text{text{data_calendario{data_calendario{data_calendario{data_calendario{data_calendario{campoggetto{text{text{linksolo{text{campoggetto{campoggetto{campoggetto_multi{text{editor{select{editor{campoggetto_multi{campoggetto_multi{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{text{text{numerico{select{', 'Identificativo Ente{Tipo{Contratto{Denominazione dell\'Amministrazione aggiudicatrice{Codice fiscale dell\'Amministrazione aggiudicatrice{Tipo di Amministrazione{Sede di gara - Provincia{Sede di gara - Comune{Sede di gara - Indirizzo{Struttura organizzativa proponente{Senza importo{Valore Importo a base asta{Valore Importo di aggiudicazione{Valore Importo liquidato{Data di pubblicazione{Data di scadenza del bando{Data di scadenza della pubblicazione dell\'esito{Data di effettivo inizio dei lavori o forniture{Data di ultimazione dei lavori o forniture{Requisiti di qualificazione{Codice CPV{Codice SCP{URL di Pubblicazione su www.serviziocontrattipubblici.it{Codice CIG{Bando di gara relativo (se il presente è avviso o esito){Record principale (cig multipli){Altre procedure{Oggetto{Maggiori Dettagli{Procedura di scelta del contraente{Note aggiuntive sulla scelta del contraente{Partecipanti alla gara{Aggiudicatari della gara{Allegato 1{Allegato 2{Allegato 3{Allegato 4{Allegato 5{Allegato 6{Allegato 7{Allegato 8{Allegato 9{Allegato 10{Allegato 11{Allegato 12{Allegato 13{Allegato 14{Allegato 15{Allegato 16{Allegato 17{Allegato 18{Allegato 19{Allegato 20{Allegato 21{Allegato 22{Id Originale{Beneficiario (testuale){ID atto eAlbo{Stato pubblicazione{', '{bandi ed inviti,esiti,delibere e determine a contrarre,affidamenti,avvisi pubblici,somme liquidate{lavori,servizi,forniture{{{,Organi istituzionali,Ministeri,Organi giurisdizionali e avvocatura,Amministrazioni indipendenti,Regioni,Aziende speciali regionalizzate,Province,Aziende speciali provincializzate,Comuni,Enti di previdenza e prevenzione,Enti preposti ad attività sportive,Enti scientifici di ricerca e sperimentazione,Enti di promozione culturale e artistica,Aziende speciali municipalizzate,Istituti autonomi case popolari,Aziende del servizio sanitario nazionale,Autorità di bacino,Comunità montane,Enti di bonifica e di sviluppo agricolo,Consorzi di industrializzazione,Consorzi autonomi di regioni province e comuni,Consorzi enti ed autorità portuali,Università ed altri enti,Istituzioni europee,Istituti bancari e finanziari,Enti ed istituti religiosi,Concessionari e imprese di gestione reti e infrastrutture,Associazioni di imprese,Imprese a partecipazione pubblica,Consorzi di imprese,Imprese ed altri soggetti privati non in forma associata,Associazioni di categoria e organizzazioni sindacali,Camere di commercio,Soggetti esterni,Provveditorato regionale alle opere pubbliche,Organismi di diritto pubblico,Altri soggetti pubblici e privati{,AG,AL,AN,AO,AR,AP,AT,AV,BA,BT,BL,BN,BG,BI,BO,BZ,BS,BR,CA,CL,CB,CI,CE,CT,CZ,CH,CO,CS,CR,KR,CN,EN,FM,FE,FI,FG,FC,FR,GE,GO,GR,IM,IS,SP,AQ,LT,LE,LC,LI,LO,LU,MC,MN,MS,MT,ME,MI,MO,MB,NA,NO,NU,OT,OR,PD,PA,PR,PV,PG,PU,PE,PC,PI,PT,PN,PZ,PO,RG,RA,RC,RE,RI,RN,RM,RO,SA,VS,SS,SV,SI,SR,SO,TA,TE,TR,TO,OG,TP,TN,TV,TS,UD,VA,VE,VB,VC,VR,VV,VI,VT{2{{13{SI,NO{{{{{{{{{21{{{{{11{11{11{{{01-PROCEDURA APERTA,02-PROCEDURA RISTRETTA,03-PROCEDURA NEGOZIATA PREVIA PUBBLICAZIONE DEL BANDO,04-PROCEDURA NEGOZIATA SENZA PREVIA PUBBLICAZIONE DEL BANDO,05-DIALOGO COMPETITIVO,06-PROCEDURA NEGOZIATA SENZA PREVIA INDIZIONE DI GARA ART. 221 D.LGS. 163/2006,07-SISTEMA DINAMICO DI ACQUISIZIONE,08-AFFIDAMENTO IN ECONOMIA - COTTIMO FIDUCIARIO,14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006,17-AFFIDAMENTO DIRETTO EX ART. 5 DELLA LEGGE N.381/91,21-PROCEDURA RISTRETTA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA,22-PROCEDURA NEGOZIATA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA,23-AFFIDAMENTO IN ECONOMIA - AFFIDAMENTO DIRETTO,24-AFFIDAMENTO DIRETTO A SOCIETA\\\\\\\' IN HOUSE,25-AFFIDAMENTO DIRETTO A SOCIETA\\\\\\\' RAGGRUPPATE/CONSORZIATE O CONTROLLATE NELLE CONCESSIONI DI LL.PP,26-AFFIDAMENTO DIRETTO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE,27-CONFRONTO COMPETITIVO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE,28-PROCEDURA AI SENSI DEI REGOLAMENTI DEGLI ORGANI COSTITUZIONALI{{41{41{{{{{{{{{{{{{{{{{{{{{{{{{{100,40{', '{{Lavori,Servizi,Forniture{{{{selezionare,AG,AL,AN,AO,AR,AP,AT,AV,BA,BT,BL,BN,BG,BI,BO,BZ,BS,BR,CA,CL,CB,CI,CE,CT,CZ,CH,CO,CS,CR,KR,CN,EN,FM,FE,FI,FG,FC,FR,GE,GO,GR,IM,IS,SP,AQ,LT,LE,LC,LI,LO,LU,MC,MN,MS,MT,ME,MI,MO,MB,NA,NO,NU,OT,OR,PD,PA,PR,PV,PG,PU,PE,PC,PI,PT,PN,PZ,PO,RG,RA,RC,RE,RI,RN,RM,RO,SA,VS,SS,SV,SI,SR,SO,TA,TE,TR,TO,OG,TP,TN,TV,TS,UD,VA,VE,VB,VC,VR,VV,VI,VT{{{{Si,No{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '0{{{{{{{{{{{{{{0{0{0{gg/mm/aaaa{gg/mm/aaaa{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{', '1{2{2{2{0{2{2{2{2{2{2{2{0{1{2{2{2{1{1{2{2{2{2{2{2{2{2{2{2{1{0{0{2{2{2{2{2{2{2{2{2{0{0{0{0{0{0{0{0{0{0{0{2{0{0{2{0{2{2{', 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', '<? selectRicercaBandi(); ?>}{Oggetto: }{Data di Attivazione: }{Data di Scadenza: }{Maggiori Dettagli: }', 'soloetichettadv||{normale||{normale||{normale||{normale||', '71{71{71{71{71', 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', 'Tipologia: }{Oggetto: }{Data di Attivazione: }{Data di Scadenza: }{Maggiori Dettagli: }', 'normale||{normale||{normale||{normale||{normale||', 'data_scadenza{id{oggetto', 'Data di Scadenza: <strong>}</strong>{<div></div>}{<strong style="color:#780000">}</strong>', 'formatodata|D d M, Y|{soloetichetta||{linkreview|normale|', '67{0{25', 'oggetto{data_attivazione{data_scadenza', '}Oggetto{}Data di pubblicazione{}Data di scadenza', 'linkreview|normale|{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0', 'oggetto{tipologia{cig{importo_liquidato{struttura{data_attivazione{elenco_aggiudicatari{id_proprietario{ultima_modifica', 'Oggetto}{Tipo}{CIG}{Importo liquidato}{Struttura}{Attivo da}{Aggiudicatari}{Creato da}{Modificato}', 'linkreview|normale|{normale||{normale||{normale||{normale||{formatodata|d-m-Y|{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined{undefined{normale{undefined', 'oggetto{struttura{data_attivazione{data_scadenza{scelta_contraente{bando_collegato{altre_procedure{id{cig{note_scelta{dettagli{id{id{valore_importo_aggiudicazione{valore_base_asta{importo_liquidato{id{elenco_partecipanti{elenco_aggiudicatari{id{allegato1{allegato2{allegato3{allegato4{allegato5{allegato6{allegato7{allegato8{allegato9{allegato10{allegato11{allegato12{allegato13{allegato14{allegato15{allegato16{allegato17{allegato18{allegato19{allegato20{allegato21{allegato22{id{ultima_modifica', '}{Struttura proponente: }{Data di Pubblicazione: <strong>}</strong>{Data di Scadenza: <strong>}</strong>{Procedura di scelta del contraente: }{Procedura relativa: }{Altre procedure: }{Altre procedure di riferimento}{Codice CIG: }{Note aggiuntive sulla scelta del contraente: }{}{<? visualizzaTabellaIndicizzazione($istanzaOggetto); ?>}{<div class="campoOggetto86">Importi </div>}{Importo di aggiudicazione: }{Importo a base asta: }{Valore importo liquidato: }{<? elencoCigMultipli($istanzaOggetto); ?>\r<? totaleSommeLiquidate($istanzaOggetto); ?>}{<? echo visualizzaFornitori($istanzaOggetto, $istanzaOggetto[elenco_partecipanti], \'partecipanti\'); ?>}{<? echo visualizzaFornitori($istanzaOggetto, $istanzaOggetto[elenco_aggiudicatari], \'aggiudicatari\'); ?>}{<div class="campoOggetto86">Allegati</div>}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{formatodata|D d M, Y|{formatodata|D d M, Y|{normale||{link||{linklista||{oggettolistacamposearch|11£altre_procedure|{normale||{normale||{normale||{soloetichettadv||{soloetichetta||}criterio $istanzaOggetto[valore_base_asta] != \'\' OR $istanzaOggetto[valore_importo_aggiudicazione] != \'\' OR $istanzaOggetto[importo_liquidato] != \'\' {normale||{normale||{normale||{soloetichettadv||{soloetichettadv||{soloetichettadv||{soloetichetta||}criterio $istanzaOggetto[allegato1] != \'\' or $istanzaOggetto[allegato2] != \'\' or$istanzaOggetto[allegato3] != \'\'{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|{oggettoetisearch|11£altre_procedure|', '24{114{114{114{114{114{114{114{114{114{80{0{0{114{114{114{114{0{0{0{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(27, 'Normativa', 'normativa', 'Oggetto PAT dedicato alla normativa dell\'ente. ', 'oggetto_normativa', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 1, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'nome', 'id', '', 0, '*numerico{*text{*campoggetto_multi{linksolo{editor{file{file{file{file{text{', 'Identificativo Ente{Nome normativa{Strutture organizzative di riferimento{Link norma su portale Normattiva{Descrizione o contenuto norma{File allegato 1{File allegato 2{File allegato 3{File allegato 4{Id Originale{', '{{13{{{{{{{{', '{{{{{{{{{{', '2{{{{{{{{{{', '{{{{{{{{{{', '0{1{0{0{0{0{0{0{0{0{', 'nome,uffici', 'Normativa}{Uffici}', 'normale||{normalecond|id_ente = ".$idEnte."|', '71{71', 'nome,uffici', 'Nome normativa}{Strutture organizzative}', 'normale||{normale||', 'nome{desc_cont{id', '}{}{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}', 'linkreview|normale|{anteprima|120|{soloetichetta||}criterio $_GET[id_doc] > 0', '137{138{0', 'nome{uffici{desc_cont{id', '}{Strutture organizzative di riferimento: }{}{dettagli}', 'linkreview|normale|{link||{anteprima|240|{etichettalink||', '137{138{138{47', 'nome{uffici{link{id_proprietario{ultima_modifica', 'Nome}{Strutture organizzative di riferimento}{Link norma Normattiva}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', 'nome{uffici{desc_cont{link{allegato1{allegato2{allegato3{allegato4{id{ultima_modifica', '}{Strutture organizzative di riferimento: }{}{<strong>Link norma su portale Normattiva: </strong>}{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{normale||{link||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{114{80{128{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(12, 'Domande e risposte (FAQ)', 'faq', 'Oggetto informativo per la creazione di un archivio di domande e risposte utili alla navigazione', 'oggetto_faq', 'informativa', 0, 0, 'gruppo', ',7,', 'informativa', 0, '', 'nessuna', 1, 0, 0, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'domanda', 'id', 'data_creazione', 0, '*textarea{*editor{campoggetto_multi{*tags{', 'Domanda{Risposta{Procedimenti associati{Argomenti (tags) associati{', '{{16{{', '{{{{', '{{{{', '{{{{', '2{0{0{0{', 'domanda,risposta', '}{}', 'normale||{normale||', '0{0', 'domanda,risposta,procedimento,argomenti', 'Domanda}{Risposta}{Procedimenti associati}{Argomenti correlati}', 'normale||{normale||{normale||{normalecond||', 'domanda{id{id{risposta{procedimento{procedimento{id', '<a href=\\"Javascript:apriLayer(\'faq_".$istanzaOggetto[\'id\']."\')\\">".$istanzaOggetto[\'domanda\']."</a>}{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}{<div style="display:none;" id="faq_<? echo $istanzaOggetto[\'id\']; ?>">}{}{Valida per}{}{</div>}', 'soloetichetta||{soloetichetta||}criterio $_GET[id_doc] > 0{soloetichettadv||{normale||{soloetichetta||{linklista||{soloetichettadv||', '25{0{0{0{81{134{0', 'domanda{risposta', '<a href=\\"Javascript:mostraNascondiElemento(\'domanda$istanzaOggetto[id]\');void(0);\\">}</a>{<div style=\\"display: none;\\" id=\\"domanda$istanzaOggetto[id]\\">}</div>', 'normale||{normale||', '25{80', 'domanda{procedimento{argomenti', 'Domanda}{Procedimenti associati}{Argomenti}', 'normale||{normale||{normale||', 'undefined{undefined{undefined', 'domanda{procedimento{risposta', '}{Procedimenti associati: }{}', 'normale||{link||{normale||', '24{114{80', '', '', '', '', 0, 0, 0, 0),
	(13, 'Strutture organizzative', 'uffici', 'Oggetto informativo per la gestione delle informazioni sull\'organizzazione dell\'ente', 'oggetto_uffici', 'informativa', 0, 0, 'gruppo', ',17,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'ordine', 'struttura', '', '', 'nome_ufficio', 'id', '', 0, '*numerico{*text{campoggetto{*campoggetto{campoggetto_multi{email{email{text{text{*editor{casella{select{gmaps{text{textarea{*numerico{text{text{text{', 'Identificativo ente{Nome struttura{Struttura di appartenenza{Responsabile{Referenti nei contatti{Posta elettronica{Email certificate{Telefono{Fax{Descrizione attività{Visualizza in Articolazione degli uffici{Presenza Sede{Dati sulla sede{Dettaglio Indirizzo{Orari al pubblico{Ordine di Visualizzazione{Id Originale{Proprieta generali{Proprieta generali aggiuntive{', '{{13{3{3{{{{{{{no,si{1{{{{{{{', '{{{{{{{{{{{{{{{{{{{', '0{{{{{{{{{{1{{{{{1{{{{', '{{{{{{{{{{{{{{{{{{{', '1{1{1{2{0{0{0{0{0{0{1{1{0{2{0{1{0{0{0{', 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'nome_ufficio,email_riferimento', '}{}', 'normale||{normale||', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|}criterio $istanzaOggetto[\'pres_sede\'] == \'si\'{linkreview|normale|{gmaps_testo||}criterio $istanzaOggetto[\'pres_sede\'] == \'si\'{link||{normale||{link||', '101{171{114{114{114{114', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '0{103', 'nome_ufficio{struttura{referente{id_proprietario{ultima_modifica', 'Ufficio}{Struttura di appartenenza}{Responsabile}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', 'nome_ufficio{referente{struttura{desc_att{id{id{email_certificate{email_riferimento{telefono{fax{sede{dett_indirizzo{referenti_contatti{id{orari{orari{id{id{id{id{id{sede{id{id{ultima_modifica', '}{Responsabile: }{Struttura organizzativa di appartenenza: }{}{<div class="campoOggetto86"> Strutture organizzative in quest\'area </div>}{Contatti}{Email <strong>certificate</strong>: }{Email normali: }{Telefono: }{Fax: }{Indirizzo: }{Indirizzo: }{<div class="campoOggetto86"> Personale da contattare </div>}{<div class="campoOggetto86"> In questa struttura </div>}{Orari al pubblico}{}{<div class="campoOggetto86"> Regolamenti di questa struttura </div>}{<div class="campoOggetto86"> Normativa </div>}{<div class="campoOggetto86"> Procedimenti gestiti da questa struttura </div>}{<div class="nascondiMappaApp">}{Come raggiungerci}{}{</div>}{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{link||{normale||desc_att}!= \'\'{oggettolistacamposearch|13£struttura|{soloetichetta||{link||{link||{normale||{normale||{gmaps_testo||}criterio $istanzaOggetto[pres_sede]==\'si\' AND $istanzaOggetto[dett_indirizzo]==\'\'{normale||}criterio $istanzaOggetto[pres_sede]==\'si\' AND $istanzaOggetto[dett_indirizzo]!=\'\'{link||{oggettolistacamposearch|3£uffici|{soloetichetta||orari}!= \'\'{normale||{oggettorichiamocamposearch|19£strutture|{oggettorichiamocamposearch|27£uffici|{oggettorichiamocamposearch|16£ufficio_def|{soloetichettadv||{soloetichetta||pres_sede}== \'si\'{gmaps_completo|nome_ufficio|pres_sede}== \'si\'{soloetichettadv||{soloetichetta||{formatodata|d-m-Y|', '24{78{79{80{0{86{77{77{114{114{114{114{0{0{86{114{0{0{0{0{86{100{0{0{0', '', '', '', '', 0, 0, 0, 0),
	(16, 'Procedimenti', 'procedimenti', 'Oggetto informativo per la gestione dell\'archivio procedimenti del Comune', 'oggetto_procedimenti', 'informativa', 0, 0, 'gruppo', ',14,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'nome', 'nome', '', '', 'nome', 'id', '', 0, '*numerico{*text{campoggetto_multi{campoggetto_multi{campoggetto_multi{*campoggetto_multi{campoggetto_multi{select{campoggetto_multi{*editor{editor{casella{casella{editor{campoggetto_multi{text{campoggetto_multi{linkinterno{textarea{text{text{text{text{text{numerico{select{', 'Identificativo Ente{Nome del procedimento{Responsabile/i di procedimento{Responsabile/i di provvedimento{Responsabile/i sostitutivo{Struttura di riferimento (chi contattare){Personale di riferimento (chi contattare){Visualizza nel Chi Contattare{Altre strutture organizzative associate{Descrizione del procedimento{Costi e modalità di pagamento{Conclusione tramite silenzio assenso{Conclusione tramite dichiarazione dell\'interessato{Riferimenti normativi (testuale){Riferimenti normativi (diretti){Termine di conclusione{Aree tematiche associate{Link per servizio online{Tempi previsti per attivazione servizio online{Id Originale{txt_resp_proc{txt_resp_prov{txt_resp_sost{txt_struttura{ID atto eAlbo{Stato pubblicazione{', '{{3{3{3{13{3{struttura-referenti,referenti-struttura,struttura,referenti{13{{{{{{27{{2{{{{{{{{{100,40{', '{{{{{{{prima la struttura poi i referenti,prima i referenti poi la stuttura,visualizza solo la struttura di riferimento,visualizza solo i referenti di riferimento{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '{{{{{{{struttura-referenti{{{{1{1{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{{{{{{', '1{1{1{1{0{1{0{1{0{0{0{1{1{0{0{2{2{2{0{2{2{2{2{2{2{2{', 'nome,ufficio_def', 'Nome procedimento}{Struttura di riferimento}', 'normale||{inputsearch||', '71{71', 'nome,ufficio_def,ufficio,descrizione,area', 'Nome del procedimento(*): }{Struttura di riferimento (chi contattare)(*): }{Altre strutture organizzative associate: }{Descrizione del procedimento(*): }{Aree tematiche associate(*): }', 'normale||{normale||{normale||{normale||{normale||', 'nome{descrizione{id', '}{}{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}', 'linkreview|normale|{anteprima|120|{soloetichetta||}criterio $_GET[id_doc] > 0', '137{138{0', 'nome{descrizione{area', '}Procedimento{}Descrizione{}Area tematica', 'linkreview|normale|{normale||{link||', '25{82{0', 'nome{referente_proc{ufficio_def{id_proprietario{ultima_modifica', 'Nome del procedimento}{Responsabili procedimento}{Strutture di riferimento}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', 'nome{referente_proc{referente_prov{resp_sost{descrizione{descrizione{id{ufficio_def{personale_proc{ufficio_def{ufficio{ufficio{termine{silenzio_assenso{dichiarazione{termine{costi{costi{id{id{id{normativa{normativa{norme{norme{id{area{area{id{link_servizio{tempi_servizio{ultima_modifica', '}{Responsabile di procedimento: }{Responsabile di provvedimento: }{Responsabile sostitutivo: }{Descrizione}{}{Chi contattare}{}{Personale da contattare: }{}{Altre strutture che si occupano del procedimento}{}{Termine di conclusione}{Conclusione tramite silenzio assenso: }{Conclusione tramite dichiarazione dell\'interessato: }{}{Costi per l\'utenza}{}{<div class="campoOggetto86"> Modulistica per il procedimento </div>}{<div class="campoOggetto86"> Regolamenti per il procedimento </div>}{<div class="campoOggetto86"> Domande frequenti </div>}{Riferimenti normativi}{}{Riferimenti normativi}{}{<div class="campoOggetto86"> Oneri informativi </div>}{Aree tematiche associate}{}{Servizio online}{}{Tempi previsti per attivazione servizio online: }{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{link||{link||{soloetichetta||{normale||{soloetichetta||ufficio_def}!= 0{oggettorichiamo||}criterio $istanzaOggetto[\'contattare\'] != \'referenti\' AND $istanzaOggetto[\'contattare\'] != \'referenti-struttura\' {link||}criterio $istanzaOggetto[\'contattare\'] != \'struttura\'{oggettorichiamo||}criterio $istanzaOggetto[\'contattare\'] == \'referenti-struttura\' {soloetichetta||ufficio}!= \'\'{linklista||{soloetichetta||termine}!= \'\'{normale||{normale||{normale||{soloetichetta||costi}!= \'\'{normale||{oggettorichiamocamposearch|5£procedimenti|{oggettorichiamocamposearch|19£procedimenti|{oggettorichiamocamposearch|12£procedimento|{soloetichetta||}criterio $istanzaOggetto[normativa] != \'\' AND $istanzaOggetto[norme] == \'\' {normale||}criterio $istanzaOggetto[normativa] != \'\' AND $istanzaOggetto[norme] == \'\' {soloetichetta||norme}!= \'\'{oggettorichiamo||{oggettolistacamposearch|30£procedimenti|{soloetichetta||area}!= \'\'{linklista||{soloetichetta||}criterio $istanzaOggetto[tempi_servizio] != \'\' OR $istanzaOggetto[link_servizio] != \'\' {link||{normale||{formatodata|d-m-Y|', '24{78{78{78{86{80{86{0{0{0{86{0{86{0{0{114{86{114{0{0{0{86{114{86{0{134{86{0{86{128{0{0', '', '', '', '', 0, 0, 0, 0),
	(19, 'Regolamenti e documentazione', 'regolamenti', 'Oggetto informativo per la gestione di un archivio di regolamenti dell\'ente', 'oggetto_regolamenti', 'informativa', 0, 0, 'gruppo', ',16,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '10', '10', 'ordine', 'ultima_modifica', '', 'DESC', 'titolo', 'id', '', 0, '*numerico{*text{select{campoggetto_multi{campoggetto_multi{*file{file{file{file{file{file{editor{*numerico{text{numerico{select{', 'Identificativo Ente{Titolo{Tipologia (sistema){Strutture organizzative associate{Procedimenti associati{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Descrizione{Ordine di visualizzazione{Id Originale{ID atto eAlbo{Stato pubblicazione{', '{{regolamento,statuto,codice{13{16{{{{{{{{{{{100,40{', '{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '0{{normale{{{{{{{{{{1{{0{100{', '{{{{{{{{{{{{{{{{', '1{1{0{0{2{0{2{2{0{0{0{0{1{2{2{2{', 'titolo,descrizione_mod', 'Nome }{Descrizione: }', 'normale||{normale||', '71{71', 'titolo,strutture,procedimenti', '{Strutture organizzative}{Procedimenti}', '{normale||{normale||', 'titolo{descrizione_mod{id', '}{}{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}', 'linkreview|normale|{anteprima|120|{soloetichetta||}criterio $_GET[id_doc] > 0', '137{138{0', 'titolo{procedimenti{descrizione_mod{id', '}{Procedimenti associati: }{}{dettagli}', 'linkreview|normale|{link||{anteprima|240|{etichettalink||', '137{138{138{47', 'titolo{strutture{tipo{procedimenti{id_proprietario{ultima_modifica', 'Nome}{Strutture organizzative}{Tipo}{Procedimenti}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{normale||{formatodata|d-m-Y|', 'undefined{normale{undefined{undefined{undefined{undefined', 'titolo{procedimenti{strutture{id{descrizione_mod{allegato{allegato_2{allegato_3{allegato_4{allegato_5{allegato_6{id{ultima_modifica', '}{Procedimenti associati: }{Strutture organizzative associate: }{<div class="campoOggetto86"> Oneri informativi relativi </div>}{}{Scarica: }{Scarica: }{Scarica: }{Scarica: }{Scarica: }{Scarica: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{link||{link||{oggettolistacamposearch|30£regolamenti|{normale||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{114{114{134{80{48{48{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(20, 'Contatti dal sito', 'contatti', 'Oggetto informativo per la pubblicazione di un form webapplication di contatto', 'oggetto_contatti', 'informativa', 0, 0, 'gruppo', ',6,', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 46, 1, 0, 'webapplication libera', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'email', 'id', '', 0, '*campoggetto{*text{*text{*email{textarea{*textarea{', 'Struttura da contattare{Nome{Cognome{Indirizzo email{Oggetto della richiesta{Richiesta{', '13{{{{{{', '{{{{{{', '{{{{{{', '{{{{{{', '1{1{1{2{2{0{', '', '', '', '', 'data_creazione,struttura,nome,cognome,email', 'Data di creazione}{Struttura da contattare}{Nome}{Cognome}{Indirizzo email}', 'normale||{normale||{normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'data_creazione{struttura{nome{cognome{email', 'Data di creazione}{Struttura da contattare}{Nome}{Cognome}{Indirizzo email}', 'formatodata|D d M, Y|{normale||{linkreview|normale|{linkreview|normale|{link||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', 'struttura{numero_letture{nome{cognome{email{oggetto{richiesta{nome', 'Struttura da contattare}{<label for="struttura"><strong>(*)</strong> Struttura da contattare</label>\r<input class="stileForm110" type="text" name="struttura" id="struttura" value="" readonly="readonly">}{Nome}{Cognome}{Indirizzo email}{Oggetto della richiesta}{Richiesta}{<input id="email_dest" name="email_dest" type="hidden" />}', 'normalecond|struttura=0|}criterio 1 == 0{soloetichetta||{normale||{normale||{normale||{normale||{normale||{soloetichetta||', '109{109{109{109{109{112{112{109', 0, 0, 0, 0),
	(21, 'Bandi di Gara - Requisiti di qualificazione', 'bandi_requisiti_qualificazione', '', 'oggetto_bandi_requisiti_qualificazione', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 1, '', 'nessuna', 0, 0, 0, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'nessuna', 'normale', '', '20', '20', 'codice', 'codice', '', '', 'codice', 'id', '', 0, '*text{*text{', 'Codice{Denominazione{', '{{', '{{', '{{', '{{', '2{2{', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'codice{denominazione', 'Codice}{Denominazione}', 'normale||{normale||', 'undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(22, 'Bandi di concorso', 'concorsi', 'Oggetto informativo PAT per la gestione dei bandi di concorso in pubblicazione.', 'oggetto_concorsi', 'informativa', 0, 0, 'gruppo', ',3,', 'informativa', 0, '', 'nessuna', 1, 0, 1, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_attivazione', 'data_attivazione', 'DESC', 'DESC', 'oggetto', 'id', '', 0, 'select{*numerico{text{select{text{text{data_calendario{data_calendario{*text{text{text{text{campoggetto_multi{editor{editor{campoggetto{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{text{numerico{select{', 'Tipo{Identificativo Ente{Oggetto{Sede Prova - Provincia{Sede Prova - Comune{Sede Prova - Indirizzo{Data di attivazione{Data di scadenza{Orario scadeza{Eventuale spesa prevista{Spese effettuate{Numero dipendenti assunti{Ufficio di riferimento{Descrizione{Calendario delle Prove{Bando di Concorso relativo (se il presente è avviso o esito){Allegato 1{Allegato 2{Allegato 3{Allegato 4{Allegato 5{Allegato 6{Allegato 7{Allegato 8{Allegato 9{Allegato 10{Allegato 11{Allegato 12{Allegato 13{Allegato 14{Allegato 15{Allegato 16{Allegato 17{Allegato 18{Allegato 19{Allegato 20{Id Originale{ID atto eAlbo{Stato pubblicazione{', 'concorsi,avvisi,esiti{{{,AG,AL,AN,AO,AR,AP,AT,AV,BA,BT,BL,BN,BG,BI,BO,BZ,BS,BR,CA,CL,CB,CI,CE,CT,CZ,CH,CO,CS,CR,KR,CN,EN,FM,FE,FI,FG,FC,FR,GE,GO,GR,IM,IS,SP,AQ,LT,LE,LC,LI,LO,LU,MC,MN,MS,MT,ME,MI,MO,MB,NA,NO,NU,OT,OR,PD,PA,PR,PV,PG,PU,PE,PC,PI,PT,PN,PZ,PO,RG,RA,RC,RE,RI,RN,RM,RO,SA,VS,SS,SV,SI,SR,SO,TA,TE,TR,TO,OG,TP,TN,TV,TS,UD,VA,VE,VB,VC,VR,VV,VI,VT{{{{{{{{{13{{{22{{{{{{{{{{{{{{{{{{{{{{{100,40{', '{{0{,AG,AL,AN,AO,AR,AP,AT,AV,BA,BT,BL,BN,BG,BI,BO,BZ,BS,BR,CA,CL,CB,CI,CE,CT,CZ,CH,CO,CS,CR,KR,CN,EN,FM,FE,FI,FG,FC,FR,GE,GO,GR,IM,IS,SP,AQ,LT,LE,LC,LI,LO,LU,MC,MN,MS,MT,ME,MI,MO,MB,NA,NO,NU,OT,OR,PD,PA,PR,PV,PG,PU,PE,PC,PI,PT,PN,PZ,PO,RG,RA,RC,RE,RI,RN,RM,RO,SA,VS,SS,SV,SI,SR,SO,TA,TE,TR,TO,OG,TP,TN,TV,TS,UD,VA,VE,VB,VC,VR,VV,VI,VT{{{{{{{{{{0{{{{{{{{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', 'concorsi{0{{{{{0{0{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{{', '2{1{2{2{2{2{2{2{1{0{0{0{2{2{2{2{2{2{2{2{2{2{2{2{0{0{0{0{0{0{0{0{0{0{0{0{2{2{2{', 'oggetto,data_attivazione,data_attivazione', 'Oggetto}{Data di Pubblicazione - dal}{Data di Pubblicazione - al}', 'normale||{normaledatamag||{normaledatamin||', '71{71{71', 'oggetto,descrizione', '', '', 'tipologia{data_scadenza{orario_scadenza{oggetto', '<strong>}</strong>{ - Data di scadenza: <strong>}</strong>{ - Orario scadenza: <strong>}</strong>{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{normale||{linkreview|normale|', '67{67{67{25', 'oggetto{tipologia{data_attivazione{data_scadenza{id', '}Oggetto{}Tipologia{}Data di pubblicazione{}Data di scadenza{dettagli}', 'linkreview|normale|{normale||{formatodata|d-m-Y|{formatodata|d-m-Y|{etichettalink||', '25{0{114{114{114', 'oggetto{tipologia{data_attivazione{data_scadenza{id_proprietario{ultima_modifica', 'Oggetto}{Tipo}{Data di pubblicazione}{Data di scadenza}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{formatodata|d-m-Y|{formatodata|d-m-Y|{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined', 'oggetto{tipologia{data_attivazione{data_scadenza{orario_scadenza{struttura{id{sede_provincia{sede_comune{sede_indirizzo{calendario_prove{dipendenti_assunti{spesa_prevista{spese_fatte{id{descrizione{id{allegato1{allegato2{alelgato3{allegato4{allegato5{allegato6{allegato7{allegato8{allegato9{allegato10{allegato11{allegato12{allegato13{allegato14{allegato15{allegato16{allegato17{allegato18{allegato19{allegato20{id{ultima_modifica', '}{}{Data di pubblicazione: <strong>}</strong>{Data di scadenza: <strong>}</strong>{Orario scadenza: <strong>}</strong>{Ufficio di riferimento: }{Sede di prova}{ - Provincia: }{ - Comune: }{ - Indirizzo: }{<div class="campoOggetto86">Calendario delle Prove</div>}{Numero dipendenti assunti: <strong>}</strong>{Eventuale spesa prevista: <strong>}</strong>{Spese effettuate: <strong>}</strong>{<div style="border-top:1px solid #C9C9C9;margin:10px 0px;"></div>}{}{<div style="clear:both;"></div>}{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Sacrica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{Scarica allegato: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{normale||{formatodata|d-m-Y|{formatodata|d-m-Y|tipologia}!= \'esiti\'{normale||tipologia}== \'concorsi\'{link||}criterio $istanzaOggetto[struttura]{soloetichetta||}criterio $istanzaOggetto[sede_comune] OR $istanzaOggetto[sede_indirizzo] OR $istanzaOggetto[sede_provincia]{normale||}criterio $istanzaOggetto[sede_provincia]{normale||}criterio $istanzaOggetto[sede_comune]{normale||}criterio $istanzaOggetto[sede_provincia]{normale||{normale||{normale||{normale||{soloetichetta||{normale||{soloetichetta||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{79{114{114{114{114{86{0{0{0{80{114{114{114{0{114{0{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(23, 'Immagini sfondo testata', 'immagini_testata', 'Oggetto informativo semplice per la gestione di sfondi nella testata del template', 'oggetto_immagini_testata', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'nome', 'id', '', 0, '*text{*filemedia{select{', 'Nome{File immagine{Visualizzato{', '{{sempre,solo in home page,solo in navigazione{', '{{{', '{{{', '{{{', '1{0{1{', '', '', '', '', 'nome,visualizzazione', 'Nome}{Visualizzazione}', 'normale||{normale||', '', '', '', '', '', '', '', '', 'nome{immagine{visualizzazione', 'Nome}{File immagine}{Visualizzazione per}', 'normale||{normale||{normale||', 'undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(24, 'Menu banner homepage', 'menu_banner', 'Oggetto informativo per la gestione di un\'area banner di scorciatoie per le sezioni del sito', 'oggetto_menu_banner', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'nome', 'id', '', 0, '*editor{select{*textarea{*filemedia{*linkinterno{', 'Nome{Visualizza barra titolo{Testo del tooltip{File immagine{Link di destinazione{', '{no,si{{{{', '{{{{{', '{no{{{{', '{{{{{', '1{1{0{0{0{', '', '', '', '', 'nome,destinazione', 'Nome}{Link}', 'normale||{normale||', 'id', '<? // pubblico banner, verificando se è pari o dispari\rif($numRiga % 2 == \'0\') $stileTemp = "float:left;margin:0px 0px 10px 0px;padding:2px;";\rif($numRiga % 2 != \'0\') $stileTemp = "float:right;margin:0px 0px 10px 0px;padding:2px;";\recho "<div style=\\"".$stileTemp."\\"><a class=\\"elementoTip\\" href=\\"".$istanzaOggetto[\'destinazione\']."\\" title=\\"".$istanzaOggetto[\'tooltip\']."\\">";\recho "<img class=\\"rollBanner\\" style=\\"border:1px solid #dcdcdc;box-shadow: 0 1px 3px #CDCDCD;\\" src=\\"".$server_url."moduli/output_media.php?file=oggetto_menu_banner/".$istanzaOggetto[\'immagine\']."\\" alt=\\"".$istanzaOggetto[\'nome\']."\\" />";\recho "</a></div>";\r?>}', 'soloetichettadv||', '0', '', '', '', '', 'nome{immagine{destinazione', 'Nome}{File immagine}{Link di destinazione}', 'normale||{normale||{link||', 'undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(25, 'Primi piani', 'primopiano', 'Oggetto informativo per i primipiani dinamici nello slider della testata in home', 'oggetto_primopiano', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'titolo', 'id', '', 0, '*text{*text{*textarea{*filemedia{linkinterno{select{', 'Titolo{Sottotitolo{Descrizione breve{Immagine di sfondo (982x260px){Link di destinazione{Apri il link in{', '{{{{{stessa finestra,nuova finestra{', '{{{{{{', '{{{{{stessa finestra{', '80{80{{{{{', '1{0{0{0{0{1{', '', '', '', '', 'titolo,sottotitolo,descrizione,destinazione', 'Titolo}{Sottotitolo}{Descrizione breve}{Link di destinazione}', 'normale||{normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'titolo{immagine{sottotitolo{data_creazione', 'Titolo}{Immagine}{Sottotitolo}{Aggiunto il}', 'normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(26, 'Appuntamenti', 'appuntamenti', '', 'oggetto_appuntamenti', 'informativa', 0, 0, 'gruppo', ',1,', 'informativa', 0, '', 'nessuna', 0, 0, 0, 1, 0, 1, 0, 'webapplication libera', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'nome_completo', 'id', '', 0, '*text{*text{text{text{*email{*text{textarea{data_calendario{ora{ora{', 'Cognome e nome{Telefono{Altro recapito telefonico{Indirizzo{Email{Motivazione appuntamento{Note{Data appuntamento{Inizio appuntamento{Fine appuntamento{', '{{{{{{{{0{0{', '{{{{{{{{{{', '{{{{{{{gg/mm/aaaa{{{', '{{{{{{{{{{', '2{2{2{2{2{2{2{2{2{2{', '', '', '', '', 'nome_completo,email,motivo,note', 'Cognome e nome(*): }{Email(*): }{Motivazione appuntamento(*): }{Note: }', 'normale||{normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'nome_completo{motivo{data_appuntamento{ora_inizio_appuntamento{ora_fine_appuntamento', 'Cognome e nome}{Motivazione appuntamento}{Data appuntamento}{Inizio appuntamento}{Fine appuntamento}', 'normale||{normale||{formatodata|d-m-Y G:i|{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', 'nome_completo{telefono1{telefono2{indirizzo{email{motivo{note', 'Cognome e nome}{Telefono}{Altro recapito telefonico}{Indirizzo}{Email}{Motivazione appuntamento}{Note}', 'normale||{normale||{normale||{normale||{normale||{normale||{normale||', '109{109{109{109{109{109{109', 0, 0, 0, 0),
	(28, 'Provvedimenti', 'provvedimenti', 'Oggetto PAT per le gestione dei provvedimenti dell\'ente', 'oggetto_provvedimenti', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 1, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', '', '', 'oggetto', 'id', '', 0, '*numerico{text{*text{select{select{campoggetto{campoggetto{*data_calendario{editor{textarea{textarea{file{file{file{file{file{file{file{file{text{numerico{select{', 'Identificativo Ente{Numero del Provvedimento{Oggetto{Tipo articolo{Tipologia{Struttura responsabile{Funzionario responsabile{Data{Contenuto{Eventuale spesa prevista{Estremi documenti principali{File allegato 1{File allegato 2{File allegato 3{File allegato 4{File allegato 5{File allegato 6{File allegato 7{File allegato 8{Id Originale{ID atto eAlbo{Stato pubblicazione{', '{{{art. 23 - c.1 - lett. a) del d.lgs. n. 33/2013,art. 23 - c.1 - lett. b) del d.lgs. n. 33/2013,art. 23 - c.1 - lett. c) del d.lgs. n. 33/2013,art. 23 - c.1 - lett. d) del d.lgs. n. 33/2013{provvedimento dirigenziale,provvedimento organo politico{13{3{{{{{{{{{{{{{{{100,40{', '{{{{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '{{{{{{{0{{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{{', '1{2{1{2{1{1{1{1{2{0{0{0{0{2{2{2{2{2{2{2{2{2{', 'numero,oggetto,tipo_articolo,data,struttura,responsabile,contenuto', 'Numero}{Oggetto}{Tipologia art. 23 }{Data}{<? selectRicercaStruttura(\'struttura_mcrt_14\'); ?>}{<? selectRicercaResponsabile(\'responsabile_mcrt_15\'); ?>}{Contenuto}', 'normale||{normale||{normale||}criterio moduloAttivo(\'agid\'){normale||{soloetichettadv||{soloetichettadv||{normale||', '71{71{71{71{0{0{71', 'oggetto,data', 'Oggetto}{Data}', 'normale||{normale||', 'oggetto{tipo{struttura{data{id', '}{Tipologia: }{Struttura responsabile: }{Data: }{<br />}', 'linkreview|normale|{normale||{link||{formatodata|d-m-Y|{soloetichetta||', '25{114{114{114{0', 'oggetto{tipo{struttura{responsabile{data', '}Oggetto{}Tipologia{}Struttura{}Responsabile{}Data', 'linkreview|normale|{normale||{link||{link||{formatodata|d-m-Y|', '25{0{0{0{0', 'oggetto{numero{tipo{struttura{data{id_proprietario{ultima_modifica', 'Oggetto}{Numero}{Tipologia}{Struttura responsabile: }{Data}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y|{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined{undefined', 'oggetto{tipo{tipo_articolo{numero{struttura{responsabile{data{id{contenuto{spesa{estremi{estremi{allegato1{allegato2{allegato3{allegato4{allegato5{allegato6{allegato7{allegato8{id{ultima_modifica', '}{Tipologia: }{}{Provvedimento numero: }{Struttura responsabile: }{Responsabile del provvedimento: }{Data del provvedimento: }{<div class="campoOggetto86"> Oneri informativi relativi </div>}{}{Eventuale spesa prevista: }{Estremi documenti principali}{}{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{normale||{normale||{normale||{normale||{link||{formatodata|d-m-Y|{oggettolistacamposearch|30£provvedimenti|{normale||{normale||{soloetichetta||estremi}!= \'\'{normale||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '171{79{79{79{79{79{79{134{80{114{86{138{48{48{48{48{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(29, 'Bilanci', 'bilanci', '', 'oggetto_bilanci', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 1, 1, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'nome', 'nome', '', '', 'nome', 'id', '', 0, '*numerico{*text{*select{*text{editor{file{file{file{file{file{file{file{file{file{file{file{file{file{file{file{text{numerico{select{', 'Identificativo ente{Nome{Tipologia{Anno{Descrizione{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Allegato{Id Originale{ID atto eAlbo{Stato pubblicazione{', '{{,bilancio preventivo,bilancio consuntivo,piano indicatori e risultati{{{{{{{{{{{{{{{{{{{{100,40{', '{{{{{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '{{{{{{{{{{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{{{{{', '1{1{1{1{2{2{2{2{2{2{2{2{2{2{2{0{0{2{2{0{2{2{2{', '', '', '', '', 'nome,descrizione,allegato1,allegato2,allegato3', 'Nome: }{Descrizione: }{Allegato: }{Allegato: }{Allegato: }', 'normale||{normale||{normale||{normale||{normale||', '', '', '', '', 'nome{anno{tipologia{descrizione{id', '}Nome{}Anno{}Tipologia{}Descrizione{dettagli}', 'linkreview|normale|{normale||{normale||{anteprima|80|{etichettalink||', '25{0{0{67{25', 'nome{tipologia{anno{allegato1{id_proprietario{ultima_modifica', 'Nome}{Tipologia}{Anno}{Allegato}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{link||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined', 'nome{tipologia{anno{descrizione{allegato1{allegato2{allegato3{allegato4{allegato5{allegato6{allegato7{allegato8{allegato9{allegato10{allegato11{allegato12{allegato13{allegato14{allegato15{id{ultima_modifica', '}{Tipologia: <strong>}</strong>{Anno: <strong>}</strong>{}{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{Allegato: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{171{171{82{48{48{48{48{48{48{48{48{48{48{48{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(30, 'Oneri informativi e obblighi amministrativi', 'oneri', 'Oggetto PAT per la gestione degli oneri informativi', 'oggetto_oneri', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'titolo', 'id', '', 0, '*numerico{*select{casella{casella{*text{data_calendario{editor{campoggetto_multi{campoggetto_multi{campoggetto_multi{campoggetto_multi{linkinterno{file{file{file{file{text{numerico{select{', 'Identificativo Ente{Tipo{Per Cittadini{Per Imprese{Denominazione{Data di scadenza{Descrizione{Procedimenti associati{Provvedimenti associati{Riferimenti normativi{Regolamenti o altra documentazione associata{Maggiori informazioni{File allegato 1{File allegato 2{File allegato 3{File allegato 4{Id Originale{ID atto eAlbo{Stato pubblicazione{', '{,onere,obbligo{{{{{{16{28{27{19{{{{{{{{100,40{', '{{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{', '0{{0{0{{gg/mm/aaaa{{{{{{{{{{{{0{100{', '{{{{{{{{{{{{{{{{{{{', '1{1{2{2{1{2{0{2{2{2{2{0{2{0{0{0{2{2{2{', '', '', '', '', 'titolo,procedimenti,provvedimenti,regolamenti', 'Titolo}{Procedimenti}{Provvedimenti}{Regolamenti}', 'normale||{normale||{normale||{normale||', '', '', '', '', 'titolo{cittadini{imprese{data', '}Denominazione{}Per Cittadini{}Per Imprese{}Data di scadenza', 'linkreview|normale|{normale||{normale||{formatodata|d-m-Y|', '0{0{0{0', 'titolo{tipo{procedimenti{regolamenti{id_proprietario{ultima_modifica', 'Titolo}{Tipo}{Procedimenti}{Regolamenti}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined', 'titolo{cittadini{imprese{data{procedimenti{provvedimenti{regolamenti{normativa{descrizione{info{allegato1{allegato2{allegato3{allegato4{id{ultima_modifica', '}{Rivolto a: <strong>Cittadini</strong>}{Rivolto a: <strong>Imprese</strong>}{Data di scadenza: <strong>}</strong>{<div class="campoOggetto86"> Procedimenti relativi </div>}{<div class="campoOggetto86"> Provvedimenti associati </div>}{<div class="campoOggetto86">Regolamenti o altra documentazione</div>}{<div class="campoOggetto86"> Riferimenti normativi </div>}{}{Maggiori informazioni: }{<strong>Scarica allegato</strong>}{<strong>Scarica allegato</strong>}{<strong>Scarica allegato</strong>}{<strong>Scarica allegato</strong>}{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{soloetichetta||cittadini}== 1{soloetichetta||imprese}== 1{formatodata|d-m-Y|{linklista||{linklista||{linklista||{linklista||{normale||{link||{linketicompleto||{linketicompleto||{linketicompleto||{linketicompleto||{soloetichetta||{formatodata|d-m-Y|', '22{0{0{0{134{134{134{134{80{128{48{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(31, 'PAT - Riferimenti normativi', 'etrasp_norma', 'Oggetto di sistema PAT per gestire i riferimenti normativi delle pagine di sistema', 'oggetto_etrasp_norma', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'num_art', 'id', '', 0, '*text{*text{text{campoggetto_multi{*editor{editor{*camposezione_multi{campoggetto_multi{', 'Norma{Numero articolo{Commi{Ambito di applicazione (nessuno per tutti){Testo della norma{Altre note{Sezioni associate{Tipo di contenuti{', '{{{39{{{{37{', '{{{{{{{{', '{{{{{{{{', '{{{{{{{{', '1{1{1{1{0{0{0{2{', '', '', '', '', 'num_art,commi,sezioni', 'Num articolo}{Commi}{Sezioni associate}', 'normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'norma{sezioni{num_art{commi{tipo_cont', 'Norma}{Sezioni associate}{Num art}{Commi}{Tipo di contenuti}', 'normale||{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(33, 'PAT - Modello di contenuto', 'etrasp_modello', 'Oggetto di sistema PAT per la creazione di  un modello di contenuto "ad oggetto" all\'interno del portale', 'oggetto_etrasp_modello', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', '', 'DESC', 'id_sezione_etrasp', 'id', '', 0, '*numerico{*camposezione{editor{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{text{campoggetto_multi{textarea{campoggetto_multi{textarea{text{', 'Identificativo Ente{Identificativo Sezione{Contenuto html generico{Modulistica associata (Titolo){Modulistica associata{Modulistica associata (opzioni){Normativa associata(Titolo){Normativa associata{Normativa associata (opzioni){Personale associato (Titolo){Personale associato{Personale associato (opzioni){Regolamenti e documenti associati (Titolo){Regolamenti e documenti associati{Regolamenti e documenti associati (opzioni){Procedimenti associati (titolo){Procedimenti associati{Procedimenti associati (opzioni){Provvedimenti associati (Titolo){Provvedimenti associati{Provvedimenti associati (opzioni){Strutture organizzative associate (Titolo){Strutture organizzative associate{Strutture organizzative associate (opzioni){Incarichi associati{Incarichi associati (opzioni){Incarichi associati (titolo){', '{{{{5{{{27{{{3{{{19{{{16{{{28{{{13{{4{{{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{', '0{{{{{{{{{{{{{{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{{{{{{{{{{{{{{', '1{1{2{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{0{', 'html_generico', 'Contenuto html generico: }', 'normale||', '0', 'html_generico', 'Contenuto html generico: }', 'normale||', 'modulistica_tit{modulistica{normativa_tit{normativa{referenti_tit{referenti{regolamenti_tit{regolamenti{procedimenti_tit{procedimenti{provvedimenti_tit{provvedimenti{strutture_tit{strutture{incarichi_tit{incarichi', '}{}{}{}{}{}{}{}{}{}{}{}{}{}{}{}', 'normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||{normale||{oggettorichiamo||', '86{0{86{0{86{0{86{0{86{0{86{0{86{0{86{0', '', '', '', '', 'id_ente{id_sezione_etrasp{id_proprietario', 'ID Ente}{Sezione}{Creato da}', 'normale||{normale||{normale||', 'undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(34, 'PAT - Help online Contenuti', 'etrasp_help', 'Oggetto di sistema PAT per la gestione dei testi di help e di presentazione delle pagine di gestione dei contenuti', 'oggetto_etrasp_help', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'id_sezione_etrasp', 'id', '', 0, '*camposezione{campoggetto_multi{editor{select{campoggetto_multi{text{', 'Identificativo Sezione{Ambito di applicazione (nessuno per tutti){Contenuto{Operazioni consigliate{Tipo di contenuti{Frequenza di aggiornamento {', '{39{{,editare contenuto di sezione,non editare contenuto di sezione,sezione snodo,sezione ospitante oggetti{37{{', '{{{{{{', '{{{{{{', '{{{{{{', '1{1{2{1{1{2{', '', '', '', '', 'id_sezione_etrasp,testo_html', 'Sezione}{Contenuto}', 'normale||{normale||', '', '', '', '', '', '', '', '', 'id_sezione_etrasp{operazioni{testo_html{tipo_cont', 'Sezione}{Operazioni consigliate}{Contenuto}{Tipo di contenuti}', 'normale||{normale||{anteprima|60|{normale||', 'undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(35, 'PAT - Help online Admin UI', 'etrasp_help_adminui', 'Oggetto informativo PAT per la gestione degli help funzionali e di interfaccia', 'oggetto_etrasp_help_adminui', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'titolo', 'id', '', 0, '*select{*select{*select{*text{*campoggetto_multi{*editor{', 'Variabile Menu{Variabile Menu Secondario{Variabile Azione{Titolo della guida{Tipo di contenuti{Contenuto della guida{', ',desktop,enti,utenti,contenuti,organizzazione,documentazione,pubblicazioni,configurazione{,nessuna,strutture,personale,procedimenti,regolamenti,modulistica,normativa,bandigara,bandiconcorso,incarichi,provvedimenti,oneri,avanzata,wizard,societa{,nessuna,aggiungi,modifica{{37{{', '{{{{{{', '{{{{{{', '{{{{{{', '2{2{2{1{0{0{', '', '', '', '', 'menu,menusec,azione,titolo', 'Menu}{Menu Secondario}{Azione}{Titolo}', 'normale||{normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'titolo{menu{menusec{azione{tipo_cont', 'Titolo}{Menu}{Menu Secondario}{Azione}{Tipo di contenuti}', 'normale||{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(36, 'PAT - News ePOLIS', 'etrasp_news_admin', 'Oggetto informativo PAT per la gestione delle novità dal mondo ePOLIS che visualizza l\'utente Admin', 'oggetto_etrasp_news_admin', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', '', 'DESC', 'titolo', 'id', '', 0, '*text{casella{immagine{*data_calendario{*editor{linkinterno{', 'Titolo notizia{Pulsante chiudi{Immagine associata{Data notizia{Contenuto notizia{Link maggiori informazioni{', '{{{{{{', '{{{{{{', '{1{{0{{{', '{{{{{{', '1{2{0{1{0{0{', '', '', '', '', 'titolo,data', 'Titolo}{Data}', 'normale||{normale||', '', '', '', '', '', '', '', '', 'titolo{immagine{data', 'Titolo notizia}{Immagine}{Data notizia}', 'normale||{normale||{formatodata|d-m-Y|', 'undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(37, 'PAT - Tipo di contenuti', 'etrasp_tipocontenuti', 'Oggetto di sistema PAT per la catalogazione del tipo di contenuti', 'oggetto_etrasp_tipocontenuti', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'nome', 'nome', '', '', 'nome', 'id', '', 0, '*text{*text{*text{editor{', 'Nome{Nome breve (azione){Identificativo oggetto{Descrizione{', '{{{{', '{{{{', '{{{{', '{{{{', '1{0{1{0{', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'nome{id_oggetto{nome_breve{descrizione', 'Nome}{Id oggetto}{Nome breve }{Descrizione}', 'normale||{normale||{normale||{anteprima|120|', 'undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(38, 'Sovvenzioni e vantaggi economici', 'sovvenzioni', 'Oggetto informativo per la gestione di sovvenzioni sussidi e vantaggi economici dell\'ente', 'oggetto_sovvenzioni', 'informativa', 0, 0, 'proprietario', '', 'informativa', 0, '', 'nessuna', 1, 0, 1, 2, 46, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data', 'ultima_modifica', 'DESC', 'DESC', 'oggetto', 'id', '', 0, '*numerico{*text{*textarea{*text{*campoggetto{*campoggetto_multi{*data_calendario{*text{*campoggetto{campoggetto{editor{*editor{*file{file{file{text{numerico{select{casella{', 'Identificativo Ente{Nominativo beneficiario{Dati fiscali{Oggetto{Struttura organizzativa responsabile{Dirigente o funzionario responsabile{Data di riferimento{Importo{Norma o titolo alla base dell\'attribuzione{Regolamento alla base dell\'attribuzione{Note{Modalità seguita per l\'individuazione{Atto di concessione{Progetto selezionato{Curriculum del soggetto beneficiario{Id Originale{ID atto eAlbo{Stato pubblicazione{Omissis{', '{{{{13{3{{{27{19{{{{{{{{100,40{{', '{{{{{{{{{{{{{{{{{pubblicato,importato da eAlbo{{', '0{{{{{{0{{{{{{{{{{0{100{0{', '{{{{{{{{{{{{{{{{{{{', '1{2{0{2{1{1{2{2{0{0{2{2{0{2{2{2{2{2{2{', 'nominativo,struttura,responsabile', 'Beneficiario}{Struttura responsabile}{Responsabile}', 'normale||{inputsearch||{normalecond|id_ente = ".$idEnte."|', '71{71{71', 'nominativo,struttura,responsabile,data', 'Beneficiario}{Struttura}{Dirigente}{Data}', 'normale||{normale||{normale||{normale||', 'oggetto{nominativo{nominativo{compenso{note', '}{ | }{<hr />}{Compenso: }Euro{Note: }', 'normale||{normale||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){soloetichetta||{normale||{normale||', '74{undefined{0{79{56', 'oggetto{nominativo{struttura{data', '}Oggetto{<?\rstampaBeneficiario($istanzaOggetto);\r?>}Nominativo{}Struttura Organizzativa{}Data{}Struttura organizzativa responsabile {}Data', 'linkreview|normale|{soloetichettadv||{link||{formatodata|d-m-Y|', '25{0{0{0', 'nominativo{oggetto{struttura{responsabile{data{id_proprietario{ultima_modifica', 'Soggetto beneficiario}{Oggetto}{Struttura organizzativa}{Responsabile}{Data}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y|{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined{undefined{undefined', 'oggetto{nominativo{dati_fiscali{normativa{regolamento{struttura{responsabile{compenso{data{note{modo_individuazione{id{progetto{cv_soggetto{file_atto{id{ultima_modifica', '<span style="font-weight:bold;">}</span>{Nominativo: }{Dati fiscali: }{Normativa alla base dell\'attribuzione: }{Regolamento alla base dell\'attribuzione: }{Struttura organizzativa responsabile: }{Dirigente o funzionario responsabile: }{Compenso: }{Data: }{<div class="campoOggetto86">Note</div>}{<div class="campoOggetto86">Modalità seguita per l\'individuazione</div>}{<div class="campoOggetto86">ALLEGATI</div>}{Progetto selezionato: }{Curriculum del soggetto incaricato: }{Atto di concessione: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{normale||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){normale||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){link||{link||{link||{link||{normale||{formatodata|d-m-Y|{normale||{normale||{soloetichetta||}criterio $istanzaOggetto[file_atto] != \'\' OR $istanzaOggetto[progetto] != \'\' OR $istanzaOggetto[cv_soggetto] != \'\' {linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||{soloetichetta||{formatodata|d-m-Y|', '24{0{0{0{0{0{0{0{0{0{0{0{48{48{48{0{0', '', '', '', '', 0, 0, 0, 0),
	(39, 'PAT - Tipologia enti', 'etrasp_tipoenti', 'Oggetto informativo PAT per la classificazione del tipo di ente', 'oggetto_etrasp_tipoenti', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'nome', 'nome', '', '', 'nome', 'id', '', 0, '*text{editor{', 'Nome della tipologia{Descrizione{', '{{', '{{', '{{', '{{', '1{0{', '', '', '', '', 'nome', 'Nome della tipologia(*): }', 'normale||', '', '', '', '', '', '', '', '', 'nome', 'Nome della tipologia(*): }', 'normale||', 'undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(40, 'PAT - Notifiche Growl', 'etrasp_notifiche', 'Oggetto informativo con proprietà PAT per la gestione di notifiche Growl aggiuntive da visualizzare nell\'ambiente amministrativo', 'oggetto_etrasp_notifiche', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca semplice', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'testo', 'id', '', 0, 'select{select{select{*numerico{*editor{', 'Visualizza nella funzione{Visualizza nella funzione specifica{Visualizza durante azione{Tempo di visualizzazione (in secondi){Testo notifica{', ',desktop,enti,utenti,ruoli,organizzazione,documentazione,pubblicazioni,contenuti,configurazione{,strutture,personale,procedimenti,regolamenti,modulistica,normativa,bilanci,bandigara,bandiconcorso,sovvenzioni,incarichi,provvedimenti,oneri,normali,avanzata{,lista,aggiungi,modifica,importa{{{', 'tutte le funzioni,desktop utente,gestione enti,gestione utenti,gestione ruoli,menu organizzazione,menu documentazione,menu pubblicazioni,menu contenuti,menu configurazione{nessuna funzione specifica,strutture organizzative ,personale ente,procedimenti,regolamenti,modulistica,normativa,bilanci,bandi gare e contratti,bandi di concorso,sovvenzioni,incarichi e consulenze,provvedimenti,oneri informativi,pagine generiche,configurazione avanzata{sempre,visualizzazione lista elementi,interfaccia di aggiunta,interfaccia di modifica,interfaccia di importazione{{{', '{{{8{{', '{{{{{', '1{1{1{1{0{', '', '', '', '', 'menu,menusec,azione,testo', 'Visualizza nella funzione: }{Visualizza nella funzione specifica: }{Visualizza durante azione: }{Testo notifica(*): }', 'normale||{normale||{normale||{normale||', '', '', '', '', '', '', '', '', 'testo{menu{menusec{azione{secondiview', 'Notifica}{Funzione}{Funzione specifica}{Azione}{Tempo}', 'normale||{normale||{normale||{normale||{normale||', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(41, 'Elenco fornitori', 'elenco_fornitori', 'Oggetto informativo per la gestione dell\'albo fornitori da collegare a tutte le informazioni dell\'ente che lo richiedono', 'oggetto_elenco_fornitori', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', 'DESC', 'DESC', 'nominativo', 'id', '', 0, '*numerico{select{*text{*text{text{text{text{text{email{text{campoggetto_multi{campoggetto_multi{campoggetto_multi{campoggetto_multi{campoggetto_multi{text{', 'Identificativo Ente{Tipologia{Nominativo e ragione sociale{Codice fiscale{Identificativo fiscale estero{Recapiti - indirizzo{Recapiti - telefono{Recapiti - fax{Recapiti - email{Nominativo raggruppamento{Mandante{Mandataria{Associata{Capogruppo{Consorziata{ID originale{', '{fornitore singolo,raggruppamento{{{{{{{{{41{41{41{41{41{{', '{{{{{{{{{{{{{{{{', '0{{{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{{{', '1{2{1{0{0{0{2{2{0{2{2{2{2{2{2{2{', '', '', '', '', 'id_ente,nominativo', 'Identificativo Ente}{Nominativo e ragione sociale}', 'normale||{normale||', '', '', '', '', '', '', '', '', 'nominativo{codice_fiscale{id_proprietario{ultima_modifica', 'Nominativo e ragione sociale}{Codice fiscale}{Creato da}{Ultima modifica}', 'normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined', 'nominativo{codice_fiscale{fiscale_estero{indirizzo{id{ultima_modifica', '}{Codice fiscale: }{Identificativo fiscale estero: }{Indirizzo: }{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'normale||{normale||{normale||{normale||{soloetichetta||{formatodata|d-m-Y|', '24{114{114{114{0{0', '', '', '', '', 0, 0, 0, 0),
	(42, 'PAT - Tipologia enti (semplice)', 'etrasp_tipoentisemplice', 'Oggetto informativo dedicato ad una classificazione semplificata del tipo di ente', 'oggetto_etrasp_tipoentisemplice', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'nome_tipo', 'nome_tipo', '', '', 'nome_tipo', 'id', '', 0, 'text{*campoggetto_multi{camposezione_multi{text{text{text{text{text{text{text{text{text{text{text{', 'Nome della tipologia{Tipo di enti (normativo){Sezioni e pagine da escludere{Traduzioni Organi politici - Commissario prefettizio{Traduzioni Organi politici - Sub Commissario prefettizio{Traduzioni Organi politici - Sindaco{Traduzioni Organi politici - Vicesindaco{Traduzioni Organi politici - Giunta ed assessori{Traduzioni Organi politici - Presidente Consiglio Comunale{Traduzioni Organi politici - Consiglio Comunale{Traduzioni Organi politici - Direzione Generale{Traduzioni Organi politici - Segretario Generale{Traduzioni Organi politici - Commissioni{Traduzioni Organi politici - Assemblea dei Sindaci{', '{39{{{{{{{{{{{{{', '{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{', '1{1{0{0{0{0{0{0{0{0{0{0{0{0{', '', '', '', '', 'nome_tipo,tipo_ente', 'Nome della tipologia}{Tipo di enti (normativo)}', 'normale||{normale||', '', '', '', '', '', '', '', '', 'nome_tipo{tipo_ente', 'Nome della tipologia}{Tipo di enti (normativo)}', 'normale||{normale||', 'undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(43, 'Commissioni e gruppi consiliari', 'commissioni', 'Oggetto PAT per la gestione delle commissioni e di archivi consiliari del personale politico dell\'ente', 'oggetto_commissioni', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 1, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'nessuna', 'normale', '', '20', '20', 'nome', 'ultima_modifica', '', 'DESC', 'nome', 'id', '', 0, 'numerico{*text{select{campoggetto{campoggetto_multi{campoggetto_multi{campoggetto{editor{campoggetto_multi{filemedia{text{text{text{email{numerico{casella{data_calendario{data_calendario{', 'Id Ente{Nome{Tipologia{Presidente o capogruppo{Vicepresidente{Segretari{Membro supplente{Descrizione{Membri{Immagine associata{Recapiti - telefono{Recapiti - fax{Recapiti - indirizzo{Recapiti - email{Ordine{Visualizza in archivio storico{Attiva dal giorno{Attiva fino al giorno{', '{{commissione,gruppo consiliare{3{3{3{3{{3{{{{{{{{{{', '{{{{{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{1{{gg/mm/aaaa{gg/mm/aaaa{', '{{{{{{{{{{{{{{{{{{', '1{1{1{2{2{2{2{0{0{2{2{2{2{0{1{2{2{2{', '', '', '', '', '', '', '', 'immagine{nome{indirizzo{email{telefono{fax{descrizione{id{presidente{vicepresidente{segretari{membri{data_attivazione{data_scadenza', '}{}{Indirizzo: }{Email: }{Telefono: }{Fax: }{}{<span style="font-size:82%;">Membri della commissione</span>}{Presidente: }{Vicepresidente: }{Segretari: }{}{Attiva dal }{Attiva fino al }', 'linkreview|normale|{normale||{normale||{link||{normale||{normale||{normale||{soloetichetta||}criterio $istanzaOggetto[\'segretari\'] != \'\' OR $istanzaOggetto[\'membri\'] != \'\'{link||{link||{link||{linklista||{formatodata|d-m-Y|{formatodata|d-m-Y|', '126{171{114{77{114{114{0{86{114{114{114{0{0{0', '', '', '', '', 'nome{tipologia{presidente{id_proprietario{ultima_modifica', 'Nome}{Tipologia}{Presidente o capogruppo}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{normale{undefined', 'immagine{nome{indirizzo{email{telefono{fax{descrizione{id{id{presidente{vicepresidente{segretari{membri{id{ultima_modifica', '}{}{Indirizzo: }{Email: }{Telefono: }{Fax: }{}{<span style="font-size:82%;">Membri della commissione</span>}{<span style="font-size:82%;">Membri del Gruppo Consiliare</span>}{Presidente:  }{Vicepresidente: }{Segretari: }{}{<div style="clear:both"></div>}{<div id="dataAggiornamento">Contenuto aggiornato al }</div>', 'linkreview|normale|{normale||{normale||{link||{normale||{normale||{normale||{soloetichetta||}criterio ($istanzaOggetto[\'segretari\'] != \'\' OR $istanzaOggetto[\'membri\'] != \'\') AND $istanzaOggetto[\'tipologia\']==\'commissione\'{soloetichetta||}criterio ($istanzaOggetto[\'segretari\'] != \'\' OR $istanzaOggetto[\'membri\'] != \'\') AND $istanzaOggetto[\'tipologia\']==\'gruppo consiliare\'{link||{link||{link||{linklista||{soloetichetta||{formatodata|d-m-Y|', '126{171{114{77{114{114{0{86{86{114{114{114{0{0{0', '', '', '', '', 0, 0, 0, 0),
	(44, 'Enti controllati', 'societa', 'Oggetto PAT per la gestione delle società partecipate dell\'ente', 'oggetto_societa', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 0, 0, 1, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'nessuna', 'normale', '', '20', '20', 'ragione', 'ultima_modifica', '', 'DESC', 'ragione', 'id', '', 0, 'numerico{*text{*select{editor{text{text{editor{campoggetto_multi{editor{linksolo{editor{file{file{file{', 'Id Ente{Ragione sociale{Tipologia{Descrizione attività{Misura di partecipazione{Durata dell\'impegno{Oneri complessivi per anno{Rappresentanti negli organi di governo{Incarichi amministrativi e relativo trattamento economico{Indirizzo portale web{Risultati di bilancio (ultimi 3 anni){Risultati di bilancio - allegato{Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell\'incarico{Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell\'incarico{', '{{ente pubblico vigilato,societa partecipata,ente di diritto privato controllato{{{{{3{{{{{{{', '{{ente pubblico vigilato,società partecipata,ente di diritto privato controllato{{{{{{{{{{{{', '{{{{{{{{{{{{{{', '{{{{{{{{{{{{{{', '1{1{1{0{0{0{2{0{0{0{2{2{2{2{', '', '', '', '', '', '', '', 'ragione{indirizzo_web{descrizione{id{misura{durata{oneri_anno{incarichi_trattamento{rappresentanti{bilancio{bilancio_allegato{dic_inconferibilita{dic_incompatibilita', '}{Sito web: }{}{<span style="font-size:82%;">Partecipazione dell\'ente</span>}{Misura di partecipazione: }{Durata dell\'impegno: }{Oneri complessivi: }{<div class="campoOggetto86"><span style="font-size:82%;">Incarichi amministrativi e relativo trattamento economico</span></div>}{<div class="campoOggetto86"><span style="font-size:82%;">Rappresentati negli organi di governo</span></div>}{<div class="campoOggetto86"><span style="font-size:82%;">Risultati di bilancio</span></div>}{Scarica allegato: }{Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell\'incarico: }{Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell\'incarico: }', 'normale||{link||{normale||{soloetichetta||{normale||{normale||{normale||{normale||{linklista||{normale||{linkcompleto||{linkcompleto||{linkcompleto||', '171{128{0{86{114{67{114{0{0{0{48{48{48', '', '', '', '', 'ragione{tipologia{indirizzo_web{id_proprietario{ultima_modifica', 'Ragione sociale}{Tipologia}{Indirizzo sito}{Creato da}{Ultima modifica}', 'linkreview|normale|{normale||{link||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(45, 'URL per AVCP', 'url_avcp', '', 'oggetto_url_avcp', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'ricerca completa', 'normale', '', '20', '20', 'data_creazione', 'ultima_modifica', '', '', 'url', 'id', '', 0, '*numerico{*numerico{*linksolo{', 'Identificativo Ente{Anno di riferimento{URL{', '{{{', '{{{', '0{{{', '{{{', '1{2{2{', '', '', '', '', 'id_ente,anno', 'Identificativo Ente:}{Anno di riferimento:}', 'normale||{normale||', '', '', '', '', 'anno{url', '}Anno di riferimento{}Dati in formato aperto', 'normale||{link||', '0{0', 'anno{url{id_proprietario{ultima_modifica', 'Anno di riferimento}{URL}{Creato da}{Ultima modifica}', 'normale||{normale||{normale||{formatodata|d-m-Y G:i|', 'undefined{undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0),
	(46, 'PAT - Workflow di pubblicazione', 'etrasp_workflow', '', 'oggetto_etrasp_workflow', 'informativa', 0, 0, 'proprietario', '0', 'informativa', 0, '', 'nessuna', 1, 0, 0, 1, 0, 1, 0, 'classica', 'normale', 0, '', 'nessuna', 'normale', '', '20', '20', 'nome', 'nome', '', '', 'nome', 'id', '', 0, '*numerico{*text{*select{campoutente_multi{*editor{editor{editor{', 'Identificativo Ente{Nome{Oggetto{Utenti iniziali{Composizione workflow{ID Stati{ID Utenti intermedi{', '{{29,22,11,43,41,44,4,5,27,30,3,16,28,19,38,13{{{{{', '{{Bilanci,Bandi di Concorso,Bandi Gare e Contratti,Commissioni e gruppi consiliari,Elenco fornitori,Enti e società controllate,Incarichi e consulenze,Modulistica,Normativa,Oneri informativi e obblighi,Personale,Procedimenti,Provvedimenti Amministrativi,Regolamenti statuti e codici,Sovvenzioni e vantaggi economici,Strutture organizzative{{{{{', '0{{{{{{{', '{{{{{{{', '2{2{2{2{2{2{2{', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'nome{id_oggetto{utenti', 'Nome}{Archivio}{Utenti iniziali}', 'normale||{normale||{normale||', 'undefined{undefined{undefined', '', '', '', '', '', '', '', '', 0, 0, 0, 0);
/*!40000 ALTER TABLE `oggetti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetti_campi_aree: 53 rows
DELETE FROM `oggetti_campi_aree`;
/*!40000 ALTER TABLE `oggetti_campi_aree` DISABLE KEYS */;
INSERT INTO `oggetti_campi_aree` (`id_regola_oggetti`, `campi_ricerca`, `campi_ricerca_titoli`, `campi_ricerca_proprieta`, `campi_ricerca_stile`, `campi_richiami`, `campi_richiami_titoli`, `campi_richiami_proprieta`, `campi_richiami_stile`, `campi_elenco`, `campi_elenco_titoli`, `campi_elenco_proprieta`, `campi_elenco_stile`, `campi_webapp`, `campi_webapp_titoli`, `campi_webapp_proprieta`, `campi_webapp_stile`) VALUES
	(1, 'titolo,contenuto', '', '', '', 'titolo{immagine{data{contenuto', '}{}{<strong>}</strong> - {}', 'linkreview|normale|{normale||{formatodata|d-m-Y|{anteprima|300|', '25{21{67{67', 'immagine{data{titolo{contenuto', '}{<strong>}</strong>{}{}', 'linkreview|normale|{formatodata|d-m-Y|{linkreview|normale|{anteprima|250|', '21{0{22{0', '', '', '\n			\n			\n			\n			', ''),
	(8, 'titolo,contenuto', '', '', '', 'id{titolo{immagine{data{contenuto{id{id', '<div class="oggetto59">}{}{}{<strong>}</strong> -{}{</div>}{<? include("personalizzazioni/template/oggetti/istanza2colonne.php"); ?>}', 'soloetichettadv||{linkreview|normale|{normale||{formatodata|d-m-Y|{anteprima|200|{soloetichettadv||{soloetichettadv||', '0{25{126{67{67{0{0', 'immagine{data{titolo{contenuto', '}{<strong>}</strong>{}{}', 'linkreview|normale|{formatodata|d-m-Y|{linkreview|normale|{anteprima|250|', '21{0{22{0', '', '', '\n			\n			\n			\n			', ''),
	(20, 'referente,ufficio', 'Nome}{Ufficio}', 'normale||{normalecond||', '148{148', 'foto{referente{email{telefono', '}{<strong style="font-size:124%">}</strong>{}{Telefono: }', 'normale||{linkreview||{link||{normale||', '135{0{85{86', 'referente{ruolo{telefono{mobile{email', '}Referente{}Ruolo{}Telefono fisso{}Telefono mobile{}Indirizzo Email', 'linkreview|normale|{normale||{normale||{normale||{link||', '25{0{0{0{0', '', '', '', ''),
	(26, 'nome,area', 'Nome procedimento}{Area tematica}', 'normale||{normalecond||', '71{71', '', '', '', '', 'nome{descrizione{ufficio_def', '}Procedimento{}Descrizione{}Struttura di riferimento', 'linkreview|normale|{anteprima|360|{link||', '25{114{0', '', '', '', ''),
	(32, 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', 'Tipologia: }{Oggetto: }{Data di Attivazione: <br />}{Data di Scadenza: <br />}{Maggiori Dettagli: }', 'normale||{normale||{normale||{normale||{normale||', '71{71{71{71{71', 'oggetto{tipologia{data_scadenza', '}{<strong>}</strong>{ - Data di Scadenza: <strong>}</strong>', 'linkreview|normale|{normale||{formatodata|D d M, Y|', '23{67{67', 'oggetto{tipologia{data_attivazione{data_scadenza', '}Oggetto{}Tipologia{}Data di inizio{}Data di scadenza', 'linkreview|normale|{normale||{formatodata|d-m-Y|{formatodata|D d M, Y|', '25{0{0{0', '', '', '', ''),
	(37, 'titolo,contenuto', '', '', '', 'data{titolo{immagine{contenuto', '<strong>}</strong> - {<strong style="color:#780000">}</strong>{}{}', 'formatodata|d-m-Y|{linkreview|normale|{linkreview|normale|{anteprima|120|', '67{147{126{114', 'immagine{data{titolo{contenuto', '}{<strong>}</strong>{}{}', 'linkreview|normale|{formatodata|d-m-Y|{linkreview|normale|{anteprima|250|', '21{0{22{0', '', '', '\n			\n			\n			\n			', ''),
	(38, '', '', '', '', 'area{descrizione', '}{}', 'linkreview|normale|{anteprima|240|', '137{138', '', '', '', '', '', '', '', ''),
	(12, 'evento,luogo_evento,id,data_inizio,data_inizio,data_fine', 'Nome evento: }{Luogo: }{&nbsp;}{Periodo evento: }{dal: }{al: }', 'normale||{normale||{soloetichetta||{soloetichetta||{normaledatamag||{normaledatamin||', '71{71{79{25{67{67', 'evento{immagine{data_inizio{data_inizio{data_fine{luogo_evento', '}{}{Data: }{Dal }{ al }{Luogo: }', 'linkreview|normale|{linkreview|normale|{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] == \'\' OR $istanzaOggetto[data_inizio] == $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{normale||', '25{126{114{67{67{0', 'evento{immagine{luogo_evento{data_inizio{data_inizio{data_fine{presentazione', '}{}{}{ - il <strong>}</strong>{- dal <strong>}</strong>{ al <strong>}</strong>{}', 'linkreview|normale|{linkreview|normale|{normale||{formatodata|d-m-Y|data_fine}== \'\'{formatodata|d-m-Y|data_fine}!= \'\'{formatodata|d-m-Y|{normale||', '22{21{67{67{67{67{0', '', '', '', ''),
	(7, '', '', '', '', 'immagine', '}', 'linkreview|normale|', '54', 'immagine', '}', 'linkreview|normale|', '60', '', '', '', ''),
	(10, '', '', '', '', 'immagine', '}', 'linkreview|normale|', '54', 'immagine', '}', 'linkreview|normale|', '60', '', '', '', ''),
	(43, '', '', '', '', 'nome{descrizione', '}{}', 'linkreview|normale|{normale||', '23{82', 'nome{descrizione', '}{}', 'linkreview||{normale||', '30{0', '', '', '', ''),
	(44, '', '', '', '', 'nome{descrizione', '<strong>}</strong>{}', 'linkreview|normale|{normale||', '23{82', 'nome{descrizione', '}{}', 'linkreview||{normale||', '30{0', '', '', '', ''),
	(53, 'nome_ufficio,servizio', 'Nome ufficio}{Servizio di appartenenza}', 'normale||{normalecond||', '71{71', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '136{103', 'nome_ufficio{referente{id{id', '}{Responsabile: }{In quest\'area}{}', 'linkreview|normale|{link||{soloetichetta||{oggettocamposearch|13£struttura|', '22{78{86{81', '', '', '', ''),
	(58, 'nome,area,ufficio', 'Nome procedimento}{Area tematica}{Procedimento della struttura }', 'normale||{normalecond||{compilatocond||', '71{71{71', 'id{nome{descrizione{id', '";$configurazione["procedimentii_caricati"]="0"; $fintaVar="}{}{}{";$configurazione["procedimenti_caricati"].=",".$istanzaOggetto["id"];$fintaVar="}', 'soloetichetta||}criterio !is_array($configurazione["procedimenti_caricati"]) {linkreview|normale|{anteprima|120|{soloetichetta||', '0{137{138{0', 'nome{descrizione{area', '}Procedimento{}Descrizione{}Area tematica', 'linkreview|normale|{normale||{link||', '25{82{0', '', '', '', ''),
	(61, 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'sede{nome_ufficio{sede{servizio{telefono{email_riferimento', '}{}{}{Servizio di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{23{0{0{0{0', 'nome_ufficio{email_certificate{email_riferimento{telefono', '}Struttura organizzativa{}Email certificate{}Email normali{}Telefono', 'linkreview|normale|{link||{link||{normale||', '25{0{0{0', '', '', '', ''),
	(69, 'referente,ufficio', 'Nome}{Ufficio}', 'normale||{normalecond||', '148{148', 'foto{referente{email{telefono', '}{<strong style="font-size:124%">}</strong>{}{Telefono: }', 'normale||{linkreview||{link||{normale||', '135{0{85{86', 'referente{ruolo_politico{email', '}Nome{}Incarico{}Indirizzo email', 'linkreview|normale|{normale||{link||', '25{0{0', '', '', '', ''),
	(83, 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'nome_ufficio', '<?\r$email = $istanzaOggetto[\'email_riferimento\'];\rif ($email == \'\') $email = $istanzaOggetto[\'email_certificate\'];\rif ($email == \'\') $email = $configurazione[\'mail_sito\'];\r?>\r<a href="javascript:document.getElementById(\'struttura\').value=\'<? echo addslashes($istanzaOggetto[\'nome_ufficio\']); ?>\';document.getElementById(\'email_dest\').value=\'<? echo $email; ?>\';void(0);"><? echo $istanzaOggetto[\'nome_ufficio\']; ?></a>}', 'soloetichettadv||', '23', 'nome_ufficio{id', '<strong>}</strong>{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '104{103', '', '', '', ''),
	(55, 'evento,luogo_evento,id,data_inizio,data_inizio,data_fine', 'Nome evento: }{Luogo: }{&nbsp;}{<strong>Periodo evento: </strong>}{dal: }{al: }', 'normale||{normale||{soloetichetta||{soloetichetta||{normaledatamag||{normaledatamin||', '71{71{0{71{67{67', 'id{evento{data_inizio{data_inizio{data_fine{luogo_evento{id{id', '<div class="oggetto59">}{}{Data: }{Dal }{ al }{Luogo: }{</div>}{<? include("personalizzazioni/template/oggetti/istanza2colonne.php"); ?>}', 'soloetichettadv||{linkreview|normale|{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] == \'\' OR $istanzaOggetto[data_inizio] == $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{normale||{soloetichettadv||{soloetichettadv||', '0{25{114{67{67{114{0{0', 'immagine{evento{luogo_evento{data_inizio{data_inizio{data_fine{presentazione', '}{}{}{ - il <strong>}</strong>{- dal <strong>}</strong>{ al <strong>}</strong>{}', 'linkreview|normale|{linkreview|normale|{normale||{formatodata|d-m-Y|data_fine}== \'\'{formatodata|d-m-Y|data_fine}!= \'\'{formatodata|d-m-Y|{normale||', '27{23{67{67{67{67{0', '', '', '', ''),
	(56, '', '', '', '', 'id{immagine{id{id', '<div class="oggetto130">\r<div class="inner_oggetto130">}{}{</div>\r</div>}{<? include("personalizzazioni/template/oggetti/istanza2colonne.php"); ?>}', 'soloetichettadv||{linkreview|normale|{soloetichettadv||{soloetichettadv||', '0{131{0{0', 'immagine', '}', 'linkreview|normale|', '60', '', '', '', ''),
	(13, 'evento,luogo_evento,id,data_inizio,data_inizio,data_fine', 'Nome evento: }{Luogo: }{&nbsp;}{<strong>Periodo evento: </strong>}{dal: }{al: }', 'normale||{normale||{soloetichetta||{soloetichetta||{normaledatamag||{normaledatamin||', '71{71{0{71{71{71', 'evento{immagine{data_inizio{data_inizio{data_fine{luogo_evento', '}{}{Data: }{Dal }{ al }{Luogo: }', 'linkreview|normale|{linkreview|normale|{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] == \'\' OR $istanzaOggetto[data_inizio] == $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{formatodata|d-m-Y|}criterio $istanzaOggetto[data_fine] != \'\' AND $istanzaOggetto[data_inizio] != $istanzaOggetto[data_fine]{normale||', '25{126{114{67{67{0', 'immagine{evento{luogo_evento{data_inizio{data_inizio{data_fine{presentazione', '}{}{}{ - il <strong>}</strong>{- dal <strong>}</strong>{ al <strong>}</strong>{}', 'linkreview|normale|{linkreview|normale|{normale||{formatodata|d-m-Y|data_fine}== \'\'{formatodata|d-m-Y|data_fine}!= \'\'{formatodata|d-m-Y|{normale||', '27{23{67{67{67{67{0', '', '', '', ''),
	(50, 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{25{114{114{114{114', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '136{103', '', '', '', ''),
	(2, 'area', 'Area tematica(*): }', 'normale||', '0', 'id{area{id', '<? \r$stile= "display:block;";\rif (isset($configurazione[\'pres_news\']) or isset($configurazione[\'pres_eve\']) and isset($configurazione[\'pres_imm\'])) $stile= "display:none;";\rif (!$numRiga) echo "<div style=\\"".$stile."\\" id=\\"comeFare\\">"; \r?>\r<div>}{<div class="review93">}</div>{</div>\r<? if (count($listaDocumenti) == ($numRiga+1)) echo "</div>"; ?>}', 'soloetichettadv||{linkreview|normale|{soloetichettadv||', '0{0{0', '', '', '', '', '', '', '', ''),
	(99, '', '', '', '', 'id', '<? // pubblico banner con modalità intera a sfondo immagine\r$stileTitolo = "stileTitolo16";\r$stileBox = "pannelloBoxDefault";\r?>\r\r<div class="<? echo $stileBox; ?>" style="margin-bottom:10px;">\r	<? if ($istanzaOggetto[\'usa_titolo\']==\'si\') echo "<div class=\\"".$stileTitolo."\\">".$istanzaOggetto[\'nome\']."</div>"; ?>\r	<a href="<? echo $istanzaOggetto[\'destinazione\']; ?>">\r		<span style="display:block;margin:4px 2px 1px 2px;min-height:116px;background:url(<? echo $server_url."moduli/output_media.php?file=oggetto_menu_banner/".$istanzaOggetto[\'immagine\']; ?>) bottom no-repeat;">\r			<? if ($istanzaOggetto[\'tooltip\']!= \'\') echo "<span onmouseover=\\"rollBanner(\'".$istanzaOggetto[\'id\']."\',\'in\');\\" onmouseout=\\"rollBanner(\'".$istanzaOggetto[\'id\']."\',\'out\');\\" class=\\"rollBannerHome\\" id=\\"sfondoBan".$istanzaOggetto[\'id\']."\\" style=\\"height:116px;display:none;background:url(".$server_url."moduli/output_immagine.php?id=691) repeat;\\">".$istanzaOggetto[\'tooltip\']."</span>"; ?>\r		</span>\r	</a>\r</div>}', 'soloetichettadv||', '0', '', '', '', '', '', '', '', ''),
	(117, 'oggetto,data', 'Oggetto}{Data}', 'normale||{normale||', '71{71', '', '', '', '', 'numero{oggetto{struttura{data', '}Numero{}Oggetto{}Struttura{}Data', 'normale||{linkreview|normale|{link||{formatodata|d-m-Y|', '0{25{0{0', '', '', '', ''),
	(118, 'oggetto,data', 'Oggetto}{Data}', 'normale||{normale||', '71{71', '', '', '', '', 'numero{oggetto{struttura{data', '}Numero{}Oggetto{}Struttura {}Data', 'normale||{linkreview|normale|{link||{formatodata|d-m-Y|', '0{25{0{0', '', '', '', ''),
	(125, 'titolo,contenuto', '', '', '', 'immagine{titolo{contenuto', '}{}{}', 'linkreview|normale|{linkreview|normale|{anteprima|120|', '169{171{0', 'titolo{immagine{data{contenuto', '}{}{<strong>}</strong> -{}', 'linkreview|normale|{linkreview|normale|{formatodata|d-m-Y|{anteprima|400|', '25{27{67{67', '', '', '\n			\n			\n			\n			', ''),
	(126, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio di appartenenza}', 'normale||{normale||{compilatocond||', '71{71{71', 'foto{referente{email{telefono{incarico', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{}{Telefono: }{Riferimento incarico amministrativo di vertice: }', 'normale||{etichettalink||{link||{normale||{link||', '27{25{77{0{0', 'referente{uffici{id', '}Nome{}Uffici di appartenenza{}Referente per', 'linkreview|normale|{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(130, 'nome_ufficio,referente', 'Nome ufficio}{Responsabile}', 'normale||{normalecond|id_ente = ".$idEnte."|', '71{71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{25{114{114{114{114', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '0{103', '', '', '', ''),
	(103, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio di appartenenza}', 'normale||{normale||{normalecond|id_ente = ".$idEnte."|', '71{71{71', 'foto{referente{carica_inizio{carica_fine{organo{ruolo_politico{id{id{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{incarico{id{allegato_nomina{curriculum{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{altre_cariche{id{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita{altre_info{note', '}{".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."}{In carica da: <strong> }</strong>{In carica fino a: <strong>}</strong>{<div>\rOrgano politico-amministrativo: <? echo traduciOrgani($istanzaOggetto[\'organo\']); ?>\r</div>}{Incarico di stampo politico: }{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Strutture organizzative di appartenenza </div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Procedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Altri dati}{Atto di nomina o proclamazione: }{Curriculum: }{Dati sulla retribuzione: }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali: }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{Dati su altre cariche: }{<div style="clear:both"></div>}{<div style="clear:both"></div>}{<strong>Compensi connessi alla carica</strong>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }{<div class="campoOggetto86">Altre informazioni</div>}{}', 'normale||{soloetichetta||{formatodata|d-m-Y|{formatodata|d-m-Y|{soloetichettadv||{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichettadv||incarico}!= \'\'{soloetichetta||{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||{soloetichetta||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{normale||{normale||', '27{24{114{114{0{0{0{86{77{77{0{0{0{0{0{0{0{0{0{0{86{48{48{48{48{48{48{48{48{48{0{0{80{80{80{48{48{0{0', 'referente{uffici{id', '}Nome{}Uffici di appartenenza{}Referente per', 'linkreview|normale|{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(134, 'titolo,descrizione_mod', 'Nome }{Descrizione: }', 'normale||{normale||', '71{71', 'titolo{strutture{procedimenti{id{descrizione_mod{allegato{allegato_2{allegato_3{allegato_4{allegato_5{allegato_6', '}{Strutture organizzative: <strong>}</strong>{Procedimenti: <strong>}</strong>{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}{}{Scarica il documento: }{Scarica il documento: }{Scarica il documento: }{Scarica il documento }{Scarica il documento: }{Scarica il documento: }', 'linkreview|normale|{link||{link||{soloetichetta||{anteprima|120|{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||', '171{0{0{0{138{48{48{48{48{48{48', 'titolo{procedimenti{descrizione_mod{id', '}{Procedimenti associati: }{}{dettagli}', 'linkreview|normale|{link||{anteprima|240|{etichettalink||', '137{138{138{47', '', '', '', ''),
	(113, 'titolo,descrizione_mod', 'Nome }{Descrizione: }', 'normale||{normale||', '71{71', 'titolo{strutture{procedimenti{id{descrizione_mod{allegato{allegato_2{allegato_3', '}{Strutture organizzative: <strong>}</strong>{Procedimenti: <strong>}</strong>{<div style="margin:10px 0px;border-bottom:1px solid #EBE6D2;"></div>}{}{Scarica documento: }{Scarica documento: }{Scarica documento: }', 'linkreview|normale|{link||{link||{soloetichetta||}criterio $_GET[id_doc] > 0{anteprima|120|{linkcompleto||{linkcompleto||{linkcompleto||', '171{0{0{0{138{48{48{48', 'titolo{procedimenti{descrizione_mod{id', '}{Procedimenti associati: }{}{dettagli}', 'linkreview|normale|{link||{anteprima|240|{etichettalink||', '137{138{138{47', '', '', '', ''),
	(135, 'nome_ufficio,referente', 'Nome ufficio}{Responsabile }', 'normale||{inputsearch||', '71{71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{25{114{114{114{114', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '0{103', '', '', '', ''),
	(136, 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{25{114{114{114{114', 'sede{nome_ufficio{referente{sede{telefono{email_riferimento', '}{}{Responsabile: }{}{Telefono: }{Email:  }', 'gmaps_normale|nome_ufficio|pres_sede}== \'si\'{linkreview|normale|{link||{gmaps_testo||pres_sede}== \'si\'{normale||{link||', '101{171{0{0{0{0', '', '', '', ''),
	(137, 'nome_ufficio,referente', 'Nome ufficio}{Responsabile }', 'normale||{inputsearch||', '71{71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|{linkreview|normale|{gmaps_testo||{link||{normale||{link||', '101{25{114{114{114{114', 'nome_ufficio{id', '}{}', 'linkreview|normale|{oggettoelencocamposearch|13£struttura|', '0{103', '', '', '', ''),
	(109, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio di appartenenza}', 'normale||{normale||{normalecond|id_ente = ".$idEnte."|', '71{71{71', 'foto{referente{email{telefono{commissioni{id', '}{<strong style="font-size:124%">}</strong>{}{Telefono: }{Commissioni di appartenenza: }{<div style="height:10px"></div>}', 'normale||{linkreview|normale|{link||{normale||{normale||{soloetichettadv||', '126{0{77{0{0{0', 'referente{uffici{id', '}Nome{}Uffici di appartenenza{}Referente per', 'linkreview|normale|{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(140, 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', 'Tipologia: }{Oggetto: }{Data di Attivazione: }{Data di Scadenza: }{Maggiori Dettagli: }', 'normale||{normale||{normale||{normale||{normale||', '71{71{71{71{71', 'tipologia{data_scadenza{id{oggetto', '<strong>}</strong>{ - Data di Scadenza: <strong>}</strong>{<div></div>}{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{soloetichetta||{linkreview|normale|', '67{67{0{25', 'oggetto{elenco_aggiudicatari{valore_importo_aggiudicazione{data_attivazione', '}Oggetto{}Fornitore{}Importo di aggiudicazione{}Data', 'linkreview|normale|{normale||{normale||{formatodata|d-m-Y|', '25{0{179{0', '', '', '', ''),
	(141, 'numero,oggetto,tipo_articolo,tipo,data,data,struttura', 'Numero}{Oggetto}{Tipologia art. 23 }{Tipo }{Data - dal giorno}{Data - fino al giorno}{<? selectRicercaStruttura(\'struttura_mcrt_14\'); ?>}', 'normale||{normale||{normale||}criterio moduloAttivo(\'agid\'){normale||{normaledatamag||{normaledatamin||{soloetichettadv||{normale||}criterio moduloAttivo(\'agid\')', '71{71{71{71{71{71{0', '', '', '', '', 'oggetto{tipo{data', '}Oggetto{}Tipologia{}Data', 'linkreview|normale|{normale||{formatodata|d-m-Y|', '25{0{0', '', '', '', ''),
	(143, 'oggetto,tipo,data,struttura', 'Oggetto}{Tipo }{Data}{<? selectRicercaStruttura(\'struttura_mcrt_14\'); ?>}', 'normale||{normale||{normale||{soloetichettadv||', '71{71{71{0', '', '', '', '', 'oggetto{tipo{data', '}Oggetto{}Tipologia{}Data', 'linkreview|normale|{normale||{formatodata|d-m-Y|', '25{0{0', '', '', '', ''),
	(144, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio di appartenenza}', 'normale||{normale||{normalecond|id_ente = ".$idEnte."|', '71{71{71', 'foto{referente{carica_inizio{carica_fine{organo{ruolo_politico{note{id{id{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{incarico{id{allegato_nomina{curriculum{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{altre_cariche{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita{altre_info', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{In carica da: <strong> }</strong>{In carica fino a: <strong>}</strong>{<div>\rOrgano politico-amministrativo: <? echo traduciOrgani($istanzaOggetto[\'organo\']); ?>\r</div>}{Incarico di stampo politico: }{}{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Strutture organizzative di appartenenza </div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Procedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Altri dati}{Atto di nomina o proclamazione: }{Curriculum: }{Retribuzione: }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali: }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{Dati su altre cariche: }{<div style="clear:both"></div>}{<strong>Compensi connessi alla carica</strong>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }{<div class="campoOggetto86">Altre informazioni</div>}', 'normale||{soloetichetta||{formatodata|d-m-Y|{formatodata|D d M, Y g:i a|{soloetichettadv||{normale||{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichettadv||incarico}!= \'\'{soloetichetta||{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{normale||', '27{24{114{114{0{0{0{0{86{77{77{0{0{0{0{0{0{0{0{0{0{86{48{48{48{48{48{48{48{48{48{0{80{80{80{48{48{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(104, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio di appartenenza}', 'normale||{normale||{normalecond|id_ente = ".$idEnte."|', '71{71{71', 'foto{referente{organo{ruolo_politico{email{telefono{id', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{<div>\rOrgano politico-amministrativo: <? echo traduciOrgani($istanzaOggetto[\'organo\']); ?>\r</div>}{Incarico di stampo politico: }{Email: }{Telefono: }{<div style="height:10px"></div>}', 'normale||{etichettalink||{soloetichettadv||{normale||{link||{normale||{soloetichettadv||', '126{171{0{0{0{0{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(146, '', '', '', '', 'immagine{nome{indirizzo{email{telefono{fax{descrizione{id{presidente{segretari{membri', '}{}{Indirizzo: }{Email: }{Telefono: }{Fax: }{}{<span style="font-size:82%;">Membri del gruppo</span>}{Capogruppo: }{Segretari: }{}', 'linkreview|normale|{normale||{normale||{link||{normale||{normale||{normale||{soloetichetta||{link||{link||{linklista||', '126{171{114{77{114{114{0{86{114{114{0', '', '', '', '', '', '', '', ''),
	(154, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{email{telefono{id', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{Email: }{Telefono: }{<div style="height:10px"></div>}', 'normale||{etichettalink||{link||{normale||{soloetichettadv||', '126{171{0{0{0', 'referente{organo{carica_inizio{carica_fine', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{<div>\r<? echo traduciOrgani($istanzaOggetto[\'organo\']); ?>\r</div>}Organo{}In carica da{}In carica fino a', 'etichettalink||{soloetichettadv||{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0{0', '', '', '', ''),
	(155, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{email{telefono{id', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{Email: }{Telefono: }{<div style="height:10px"></div>}', 'normale||{etichettalink||{link||{normale||{soloetichettadv||', '126{171{0{0{0', 'referente{ruolo{carica_inizio{carica_fine', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Ruolo{ }In carica da{}In carica fino a', 'etichettalink||{normale||{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0{0', '', '', '', ''),
	(157, 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', 'Tipologia: }{Oggetto: }{Data di Attivazione: }{Data di Scadenza: }{Maggiori Dettagli: }', 'normale||{normale||{normale||{normale||{normale||', '71{71{71{71{71', 'data_attivazione{oggetto{importo_liquidato', '}Anno{}Oggetto{}Importo liquidato', 'formatodata|Y|{linkreview|normale|{normale||', '114{114{114', 'oggetto{data_attivazione{data_scadenza', '}Oggetto{}Data di pubblicazione{}Data di scadenza', 'linkreview|normale|{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0', '', '', '', ''),
	(106, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{organo{id{ruolo_politico{ruolo_politico{email{telefono{id', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{<strong>Consigliere con delega</strong>}{Incarico di stampo politico: }{Organo di controllo (art.20 d.lgs 30 giugno 2011, n.123): }{Email: }{Telefono: }{<div style="height:10px"></div>}', 'normale||{etichettalink||{soloetichettadv||{soloetichetta||delega}== 1{normale||}criterio !moduloAttivo(\'agid\'){normale||}criterio moduloAttivo(\'agid\'){link||{normale||{soloetichettadv||', '126{171{0{0{0{0{0{0{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(105, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{carica_inizio{carica_fine{ruolo{organo{ruolo_politico{commissioni{id{note{telefono{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{id{incarico{allegato_nomina{curriculum{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{altre_cariche{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita{altre_info', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{In carica da: <strong>}</strong>{In carica fino a: <strong>}</strong>{Ruolo:  <strong>}</strong>{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{Incarico di stampo politico: }{Commissioni di appartenenza: }{<div style="clear:both;"></div>}{}{Contatti}{Email: }{Email certificata: }{Telefono fisso: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Referente per le strutture</div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Provvedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{Altri dati}{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Atto di nomina o proclamazione: }{Curriculum: }{Dati sulla retribuzione }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali:  }{Dati patrimoniali anni precedenti:}{Dati patrimoniali anni precedenti: }{Dati su altre cariche: }{<div style="clear:both"></div>}{<strong>Compensi connessi alla carica</strong>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }{<div class="campoOggetto86">Altre informazioni</div>}', 'normale||{soloetichetta||{formatodata|d-m-Y|{formatodata|d-m-Y|{normale||{soloetichettadv||{normale||{normale||{soloetichettadv||{normale||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichetta||{soloetichettadv||incarico}!= \'\'{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||{normale||', '126{171{114{114{114{0{0{0{0{80{86{77{77{114{114{114{0{0{0{0{0{0{86{0{48{48{48{48{48{48{48{48{48{0{80{80{80{48{48{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(107, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{ruolo{organo{commissioni{ruolo_politico{note{id{telefono{email{email_cert{mobile{fax{id{uffici{id{id{id{id{id{incarico{determinato{allegato_nomina{curriculum{retribuzione{patrimonio{patrimonio1{patrimonio2{altre_cariche{altre_info', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{Ruolo:  <strong>}</strong>{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{Commissioni di appartenenza: }{Incarico di stampo politico: }{}{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Referente per le strutture</div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Provvedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{Altri dati}{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Contratto tempo determinato: }{Atto di nomina o proclamazione: }{Curriculum: }{Dati sulla retribuzione }{Dati patrimoniali:  }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{Dati su altre cariche: }{<div class="campoOggetto86">Altre informazioni</div>}', 'normale||{soloetichetta||{normale||{soloetichettadv||{normale||{normale||{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichetta||{soloetichettadv||incarico}!= \'\'{normale||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||{normale||', '126{171{114{0{0{0{80{0{86{77{77{114{114{0{0{0{0{0{0{86{0{0{48{48{48{48{48{48{48{0', 'referente{uffici{id', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}Nome{}Uffici di appartenenza{}Referente per', 'etichettalink||{linklista||{oggettolistacamposearch|13£referente|', '25{0{0', '', '', '', ''),
	(160, 'nome_ufficio', 'Nome ufficio}', 'normale||', '71', 'sede{nome_ufficio{sede{struttura{telefono{email_riferimento', '}{}{}{Struttura di appartenenza: }{Telefono: }{Indirizzo email: }', 'gmaps_statica|desc_att|}criterio $istanzaOggetto[\'pres_sede\'] == \'si\'{linkreview|normale|{gmaps_testo||}criterio $istanzaOggetto[\'pres_sede\'] == \'si\'{link||{normale||{link||', '101{171{114{114{114{114', 'nome_ufficio{email_certificate', '}Struttura organizzativa{}Email certificate', 'linkreview|normale|{link||', '25{0', '', '', '', ''),
	(138, 'tipologia,oggetto,data_attivazione,data_scadenza,dettagli', 'Tipologia: }{Oggetto: }{Data di Attivazione: }{Data di Scadenza: }{Maggiori Dettagli: }', 'normale||{normale||{normale||{normale||{normale||', '71{71{71{71{71', 'tipologia{data_scadenza{id{oggetto', '<strong>}</strong>{ - Data di Scadenza: <strong>}</strong>{<div></div>}{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{soloetichetta||{linkreview|normale|', '67{67{0{25', 'oggetto{data_attivazione{data_scadenza_esito', '}Oggetto{}Data di pubblicazione{}Data di scadenza', 'linkreview|normale|{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0', '', '', '', ''),
	(166, 'tipologia,oggetto,data_attivazione,data_attivazione,dettagli', '<? selectRicercaBandi(); ?>}{Oggetto: }{Data di Pubblicazione - dal: }{Data di Pubblicazione - al: }{Maggiori Dettagli: }', 'soloetichettadv||{normale||{normaledatamag||{normaledatamin||{normale||', '0{71{71{71{71', 'tipologia{data_scadenza{id{oggetto', '<strong>}</strong>{ - Data di Scadenza: <strong>}</strong>{<div></div>}{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{soloetichetta||{linkreview|normale|', '67{67{0{25', 'oggetto{data_attivazione{data_scadenza', '}Oggetto{}Data di pubblicazione{}Data di scadenza', 'linkreview|normale|{formatodata|d-m-Y|{formatodata|d-m-Y|', '25{0{0', '', '', '', ''),
	(164, 'oggetto,descrizione', '', '', '', 'tipologia{data_scadenza{orario_scadenza{oggetto', '<strong>}</strong>{ - Data di scadenza: <strong>}</strong>{ - Orario scadenza: <strong>}</strong>{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{normale||{linkreview|normale|', '67{67{67{25', 'oggetto{tipologia{data_attivazione{id', '}Oggetto{}Tipologia{}Data di pubblicazione{dettagli}', 'linkreview|normale|{normale||{formatodata|d-m-Y|{etichettalink||', '25{0{114{114', '', '', '', ''),
	(165, 'oggetto,descrizione', '', '', '', 'tipologia{data_scadenza{orario_scadenza{oggetto', '<strong>}</strong>{ - Data di scadenza: <strong>}</strong>{ - Orario scadenza: <strong>}</strong>{<strong style="color:#780000">}</strong>', 'normale||{formatodata|D d M, Y|{normale||{linkreview|normale|', '67{67{67{25', 'oggetto{tipologia{data_attivazione{id', '}Oggetto{}Tipologia{}Data di pubblicazione{dettagli}', 'linkreview|normale|{normale||{formatodata|d-m-Y|{etichettalink||', '25{0{114{114', '', '', '', ''),
	(170, 'referente,ruolo,uffici', 'Nominativo}{Ruolo}{Ufficio}', 'normale||{normale||{inputsearch||', '71{71{71', 'foto{referente{ruolo{organo{commissioni{ruolo_politico{note{id{telefono{email{email_cert{telefono{mobile{fax{id{uffici{id{id{id{id{id{determinato{allegato_nomina{curriculum{retribuzione{retribuzione1{retribuzione2{patrimonio{patrimonio1{patrimonio2{incarico{altre_cariche{id{compensi{importi_viaggi{altri_incarichi{dic_inconferibilita{dic_incompatibilita', '}{<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}{Ruolo:  <strong>}</strong>{<? visualizzaOrganoPolitico($istanzaOggetto); ?>}{Commissioni di appartenenza: }{Incarico di stampo politico: }{}{<div style="clear:both;"></div>}{Contatti}{Email: }{Email certificata: }{Telefono fisso: }{Telefono mobile: }{Fax: }{<div class="campoOggetto86"> Referente per le strutture</div>}{Altre strutture organizzative: }{<div class="campoOggetto86">Procedimenti seguiti come responsabile di procedimento </div>}{<div class="campoOggetto86">Provvedimenti seguiti come responsabile di provvedimento </div>}{<div class="campoOggetto86"> Presidente o capogruppo per</div>}{<div class="campoOggetto86"> Membro di</div>}{Altri dati}{Contratto tempo determinato: }{Atto di nomina o proclamazione: }{Curriculum: }{Dati sulla retribuzione }{Dati sulla retribuzione anni precedenti: }{Dati sulla retribuzione anni precedenti: }{Dati patrimoniali:  }{Dati patrimoniali anni precedenti: }{Dati patrimoniali anni precedenti: }{<?visualizzaOggettoIncarico($istanzaOggetto[incarico]);?>}{Dati su altre cariche: }{<div style="clear:both"></div>}{<strong>Compensi connessi alla carica</strong>}{<strong>Importi di viaggi di servizi e missioni</strong>}{<strong>Altri incarichi con oneri a carico della finanza pubblica e relativi compensi</strong>}{Dichiarazione insussistenza cause inconferibilità: }{Dichiarazione insussistenza cause incompatibilità: }', 'normale||{soloetichetta||{normale||{soloetichettadv||{normale||{normale||{normale||{soloetichettadv||{soloetichetta||{link||{link||{normale||{normale||{normale||{oggettolistacamposearch|13£referente|{link||{oggettolistacamposearch|16£referente_proc|{oggettolistacamposearch|16£referente_prov|{oggettorichiamocamposearch|43£presidente|{oggettocamposearch|43£membri|{soloetichetta||{normale||{linkcompleto||{linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){linkcompleto||}criterio !moduloAttivo(\'privacy\') OR (!$istanzaOggetto[omissis] AND moduloAttivo(\'privacy\')){soloetichettadv||incarico}!= \'\'{linkcompleto||{soloetichetta||{normale||{normale||{normale||{linkcompleto||{linkcompleto||', '126{171{114{0{0{0{80{0{86{77{77{114{114{114{0{0{0{0{0{0{86{0{48{48{48{48{48{48{48{48{0{48{0{80{80{80{48{48', 'referente', '<strong>".$istanzaOggetto[tit]." ".$istanzaOggetto[referente]."</strong>}', 'etichettalink||', '25', '', '', '', '');
/*!40000 ALTER TABLE `oggetti_campi_aree` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetti_notifiche_push: 0 rows
DELETE FROM `oggetti_notifiche_push`;
/*!40000 ALTER TABLE `oggetti_notifiche_push` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetti_notifiche_push` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetti_notifiche_push_devices: 0 rows
DELETE FROM `oggetti_notifiche_push_devices`;
/*!40000 ALTER TABLE `oggetti_notifiche_push_devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetti_notifiche_push_devices` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetti_strutture_webservice: 0 rows
DELETE FROM `oggetti_strutture_webservice`;
/*!40000 ALTER TABLE `oggetti_strutture_webservice` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetti_strutture_webservice` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bandi_requisiti_qualificazione: 57 rows
DELETE FROM `oggetto_bandi_requisiti_qualificazione`;
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione` DISABLE KEYS */;
INSERT INTO `oggetto_bandi_requisiti_qualificazione` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `codice`, `denominazione`) VALUES
	(1, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312881, 1327312881, 0, 0, 0, 'istanza', 'AA', 'Altro (es. Stazioni appaltanti con sistema di qualificazione proprio)'),
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312901, 1327312901, 0, 0, 0, 'istanza', 'OG1', 'Edifici civili e industriali'),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312913, 1327312913, 0, 0, 0, 'istanza', 'OG2', 'Restauro e manutenzione dei beni immobili sottoposti a tutela ai sensi delle disposizioni in materia di beni culturali e ambientali'),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312935, 1327312935, 0, 0, 4, 'istanza', 'OG3', 'Strade, autostrade, ponti, viadotti, ferrovie, metropolitane, funicolari, piste aeroportuali e relative opere complementari'),
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312949, 1327312949, 0, 0, 0, 'istanza', 'OG4', 'Opere d\'arte nel sottosuolo'),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312965, 1327312965, 0, 0, 0, 'istanza', 'OG5', 'Dighe'),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312980, 1327312980, 0, 0, 0, 'istanza', 'OG6', 'Acquedotti, gasdotti, oleodotti, opere di irrigazione e di evacuazione'),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327312997, 1327312997, 0, 0, 0, 'istanza', 'OG7', 'Opere marittime e lavori di dragaggio'),
	(9, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313011, 1327313011, 0, 0, 0, 'istanza', 'OG8', 'Opere fluviali, di difesa, di sistemazione idraulica e di bonifica'),
	(10, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313036, 1327313036, 0, 0, 0, 'istanza', 'OG9', 'Impianti per la produzione di energia elettrica'),
	(11, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313050, 1327313050, 0, 0, 0, 'istanza', 'OG10', 'Impianti per la trasformazione alta/media tensione e per la distribuzione di energia elettrica in corrente alternata e continua'),
	(12, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313060, 1327313060, 0, 0, 0, 'istanza', 'OG11', 'Impianti tecnologici'),
	(13, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313069, 1327313069, 0, 0, 0, 'istanza', 'OG12', 'Opere ed impianti di bonifica e protezione ambientale'),
	(14, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313080, 1327313080, 0, 0, 0, 'istanza', 'OG13', 'Opere di ingegneria naturalistica'),
	(15, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313090, 1327313090, 0, 0, 0, 'istanza', 'OS1', 'Lavori in terra'),
	(16, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313104, 1327313104, 0, 0, 0, 'istanza', 'OS2', 'Superfici decorate e beni mobili di interesse storico e artistico fino al 5.12.2011'),
	(17, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313127, 1327313127, 0, 0, 0, 'istanza', 'OS2-A', 'Superfici decorate di beni immobili del patrimonio culturale e beni culturali mobili di interesse storico, artistico, archeologico ed etnoantropologico a partire dal 6.12.2011'),
	(18, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313156, 1327313156, 0, 0, 0, 'istanza', 'OS2-B', 'Beni culturali mobili di interesse archivistico e librario a partire dal 6.12.2011'),
	(19, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313165, 1327313165, 0, 0, 0, 'istanza', 'OS3', 'Impianti idrico sanitario, cucine, lavanderie'),
	(20, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313173, 1327313173, 0, 0, 0, 'istanza', 'OS4', 'Impianti elettromeccanici trasportatori'),
	(21, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313183, 1327313183, 0, 0, 0, 'istanza', 'OS5', 'Impianti pneumatici e antintrusione'),
	(22, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313193, 1327313193, 0, 0, 0, 'istanza', 'OS6', 'Finiture di opere generali in materiali lignei, plastici, metallici e vetrosi'),
	(23, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313206, 1327313206, 0, 0, 0, 'istanza', 'OS7', 'Finiture di opere generali di natura edile'),
	(24, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313221, 1327313221, 0, 0, 0, 'istanza', 'OS8', 'Finiture di opere generali di natura tecnica'),
	(25, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313234, 1327313234, 0, 0, 0, 'istanza', 'OS9', 'Impianti per la segnaletica luminosa e la sicurezza del traffico'),
	(26, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313242, 1327313242, 0, 0, 0, 'istanza', 'OS10', 'Segnaletica stradale non luminosa'),
	(27, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313252, 1327313252, 0, 0, 0, 'istanza', 'OS11', 'Apparecchiature strutturali speciali'),
	(28, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313261, 1327313261, 0, 0, 0, 'istanza', 'OS12', 'Barriere e protezioni stradali fino al 5.12.2011'),
	(29, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313274, 1327313274, 0, 0, 0, 'istanza', 'OS12-A', 'Barriere stradali di sicurezza a partire dal 6.12.2011'),
	(30, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313288, 1327313288, 0, 0, 0, 'istanza', 'OS12-B', 'Barriere paramassi, fermaneve e simili a partire dal 6.12.2011'),
	(31, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313304, 1327313304, 0, 0, 0, 'istanza', 'OS13', 'Strutture prefabbricate in cemento armato'),
	(32, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313315, 1327313315, 0, 0, 0, 'istanza', 'OS14', 'Impianti di smaltimento e recupero rifiuti'),
	(33, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313330, 1327313330, 0, 0, 0, 'istanza', 'OS15', 'Pulizia di acque marine, lacustri, fluviali'),
	(34, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313343, 1327313343, 0, 0, 0, 'istanza', 'OS16', 'Impianti per centrali produzione energia elettrica'),
	(35, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313351, 1327313351, 0, 0, 0, 'istanza', 'OS17', 'Linee telefoniche ed impianti di telefonia'),
	(36, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313363, 1327313363, 0, 0, 0, 'istanza', 'OS18', 'Componenti strutturali in acciaio o metallo fino al 5.12.2011'),
	(37, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313374, 1327313374, 0, 0, 0, 'istanza', 'OS18-A', 'Componenti strutturali in acciaio a partire dal 6.12.2011'),
	(38, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313389, 1327313389, 0, 0, 0, 'istanza', 'OS18-B', 'Componenti per facciate continue a partire dal 6.12.2011'),
	(39, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313401, 1327313401, 0, 0, 0, 'istanza', 'OS19', 'Impianti di reti di telecomunicazione e di trasmissione dati'),
	(40, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313413, 1327313413, 0, 0, 0, 'istanza', 'OS20', 'Rilevamenti topografici fino al 5.12.2011'),
	(41, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313421, 1327313421, 0, 0, 0, 'istanza', 'OS20-A', 'Rilevamenti topografici a partire dal 6.12.2011'),
	(42, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313431, 1327313431, 0, 0, 0, 'istanza', 'OS20-B', 'Indagini geognostiche a partire dal 6.12.2011'),
	(43, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313443, 1327313443, 0, 0, 0, 'istanza', 'OS21', 'Opere strutturali speciali'),
	(44, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313452, 1327313452, 0, 0, 0, 'istanza', 'OS22', 'Impianti di potabilizzazione e depurazione'),
	(45, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313477, 1327313477, 0, 0, 0, 'istanza', 'OS23', 'Demolizione di opere'),
	(46, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313494, 1327313494, 0, 0, 0, 'istanza', 'OS24', 'Verde e arredo urbano'),
	(47, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313504, 1327313504, 0, 0, 0, 'istanza', 'OS25', 'Scavi archeologici'),
	(48, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313515, 1327313515, 0, 0, 0, 'istanza', 'OS26', 'Pavimentazioni e sovrastrutture speciali'),
	(49, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313525, 1327313525, 0, 0, 0, 'istanza', 'OS27', 'Impianti per la trazione elettrica'),
	(50, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313535, 1327313535, 0, 0, 0, 'istanza', 'OS28', 'Impianti termici e di condizionamento'),
	(51, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313546, 1327313546, 0, 0, 0, 'istanza', 'OS29', 'Armamento ferroviario'),
	(52, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313556, 1327313556, 0, 0, 0, 'istanza', 'OS30', 'Impianti interni elettrici, telefonici, radiotelefonici e televisivi'),
	(53, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313566, 1327313566, 0, 0, 0, 'istanza', 'OS31', 'Impianti per la mobilit&agrave;'),
	(54, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313576, 1327313576, 0, 0, 0, 'istanza', 'OS32', 'Impianti per la mobilit&agrave;'),
	(55, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313586, 1327313586, 0, 0, 0, 'istanza', 'OS33', 'Coperture speciali'),
	(56, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313595, 1327313595, 0, 0, 0, 'istanza', 'OS34', 'Sistemi antirumore per infrastrutture di mobilit&agrave;'),
	(57, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1327313606, 1327313606, 0, 0, 0, 'istanza', 'OS35', 'Interventi a basso impatto ambientale a partire dal 6.12.2011');
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bandi_requisiti_qualificazione_backup: 0 rows
DELETE FROM `oggetto_bandi_requisiti_qualificazione_backup`;
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bandi_requisiti_qualificazione_workflow: 0 rows
DELETE FROM `oggetto_bandi_requisiti_qualificazione_workflow`;
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_bandi_requisiti_qualificazione_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_banner: 0 rows
DELETE FROM `oggetto_banner`;
/*!40000 ALTER TABLE `oggetto_banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_banner` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_banner_backup: 0 rows
DELETE FROM `oggetto_banner_backup`;
/*!40000 ALTER TABLE `oggetto_banner_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_banner_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_banner_workflow: 0 rows
DELETE FROM `oggetto_banner_workflow`;
/*!40000 ALTER TABLE `oggetto_banner_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_banner_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bilanci: 0 rows
DELETE FROM `oggetto_bilanci`;
/*!40000 ALTER TABLE `oggetto_bilanci` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_bilanci` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bilanci_backup: 0 rows
DELETE FROM `oggetto_bilanci_backup`;
/*!40000 ALTER TABLE `oggetto_bilanci_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_bilanci_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_bilanci_workflow: 0 rows
DELETE FROM `oggetto_bilanci_workflow`;
/*!40000 ALTER TABLE `oggetto_bilanci_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_bilanci_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_commissioni: 0 rows
DELETE FROM `oggetto_commissioni`;
/*!40000 ALTER TABLE `oggetto_commissioni` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_commissioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_commissioni_backup: 0 rows
DELETE FROM `oggetto_commissioni_backup`;
/*!40000 ALTER TABLE `oggetto_commissioni_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_commissioni_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_commissioni_workflow: 0 rows
DELETE FROM `oggetto_commissioni_workflow`;
/*!40000 ALTER TABLE `oggetto_commissioni_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_commissioni_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_concorsi: 0 rows
DELETE FROM `oggetto_concorsi`;
/*!40000 ALTER TABLE `oggetto_concorsi` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_concorsi` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_concorsi_backup: 0 rows
DELETE FROM `oggetto_concorsi_backup`;
/*!40000 ALTER TABLE `oggetto_concorsi_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_concorsi_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_concorsi_workflow: 0 rows
DELETE FROM `oggetto_concorsi_workflow`;
/*!40000 ALTER TABLE `oggetto_concorsi_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_concorsi_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_contatti: 0 rows
DELETE FROM `oggetto_contatti`;
/*!40000 ALTER TABLE `oggetto_contatti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_contatti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_contatti_backup: 0 rows
DELETE FROM `oggetto_contatti_backup`;
/*!40000 ALTER TABLE `oggetto_contatti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_contatti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_contatti_workflow: 0 rows
DELETE FROM `oggetto_contatti_workflow`;
/*!40000 ALTER TABLE `oggetto_contatti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_contatti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_elenco_fornitori: 0 rows
DELETE FROM `oggetto_elenco_fornitori`;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_elenco_fornitori_backup: 0 rows
DELETE FROM `oggetto_elenco_fornitori_backup`;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_elenco_fornitori_duplicate: 0 rows
DELETE FROM `oggetto_elenco_fornitori_duplicate`;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_duplicate` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_duplicate` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_elenco_fornitori_duplicati: 0 rows
DELETE FROM `oggetto_elenco_fornitori_duplicati`;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_duplicati` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_duplicati` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_elenco_fornitori_workflow: 0 rows
DELETE FROM `oggetto_elenco_fornitori_workflow`;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_elenco_fornitori_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help: 51 rows
DELETE FROM `oggetto_etrasp_help`;
/*!40000 ALTER TABLE `oggetto_etrasp_help` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_help` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `id_sezione_etrasp`, `tipo_enti`, `testo_html`, `operazioni`, `tipo_cont`, `frequenza_agg`) VALUES
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375753862, 1375796929, 0, 0, 0, 'istanza', 43, '', '<p>In questa pagina &egrave; necessario pubblicare il Piano per la trasparenza e l\'integrit&agrave;. Il Programma triennale per la trasparenza e l&rsquo;integrit&agrave; &egrave; delineato come strumento di programmazione autonomo rispetto al Piano di prevenzione della corruzione, pur se ad esso strettamente collegato, tant&rsquo;&egrave; che mil Programma &ldquo; di norma&rdquo; integra una sezione del predetto piano. Il collegamento fra il Piano di prevenzione della corruzione e il Programma triennale per la trasparenza &egrave; assicurato dal Responsabile della trasparenza.&nbsp;</p>\n', 'editare contenuto di sezione', '1,6', 'Annuale'),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375798540, 1375798540, 0, 0, 0, 'istanza', 747, '', '<p>Sono da inserire i riferimenti normativi con i relativi link alle norme di legge statale pubblicate nella banca dati "Normattiva" che regolano l\'istituzione, l\'organizzazione e l\'attivit&agrave; delle pubbliche amministrazioni. Pubblicare anche direttive, circolari, programmi, istruzioni e ogni atto che dispone in generale sulla organizzazione, sulle funzioni, sugli obiettivi, sui procedimenti e gli atti nei quali si determina l\'interpretazione di norme giuridiche che riguardano o dettano disposizioni per l\'applicazione di esse, compresi i codici di condotta.</p>\n', 'sezione ospitante oggetti', '1,12,6', 'Tempestivo'),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375798648, 1375798648, 0, 0, 0, 'istanza', 700, '5', '<p>Sono da pubblicare i regolamenti ministeriali o interministeriali, provvedimenti amministrativi a carattere generale adottati dalle amministrazioni dello Stato per regolare l\'esercizio di poteri autorizzatori, concessori o certificatori, nonch&egrave; l\'accesso ai servizi pubblici ovvero la concessione di benefici con allegato elenco di tutti gli oneri informativi gravanti sui cittadini e sulle imprese introdotti o eliminati con i medesimi atti</p>\n', 'sezione ospitante oggetti', '13', 'Tempestivo'),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375799571, 1375800558, 0, 0, 0, 'istanza', 701, '1', '<p>Sono da pubblicare gli organi di indirizzo politico e di amministrazione e gestione, con l\'indicazione delle rispettive competenze,&nbsp;Atto di nomina o di proclamazione, con l\'indicazione della durata dell\'incarico o del mandato elettivo, curricula,&nbsp;compensi di qualsiasi natura connessi all\'assunzione della carica,&nbsp;importi di viaggi di servizio e missioni pagati con fondi pubblici,&nbsp;Dati relativi all\'assunzione di altre cariche presso enti pubblici o privati e relativi compensi a qualsiasi titolo corrisposti.</p>\n<p>Sono inoltre da pubblicare in formato tabellare altri eventuali incarichi con oneri a carico della finanza pubblica con indicazione dei compensi spettanti:</p>\n<ol>\n<li>dichiarazione concernente diritti reali su beni immobili e su beni mobili iscritti in pubblici registri, azioni di societ&agrave;, quote di partecipazione a societ&agrave;, esercizio di funzioni di amministratore o di sindaco di societ&agrave;, con l\'apposizione della formula &laquo;sul mio onore affermo che la dichiarazione corrisponde al vero&raquo;. Per il soggetto, il coniuge non separato e i parenti entro il secondo grado, ove gli stessi vi consentano (dando eventualmente evidenza del mancato consenso)</li>\n<li>copia dell\'ultima dichiarazione dei redditi soggetti all\'imposta sui redditi delle persone fisiche. Per il soggetto, il coniuge non separato e i parenti entro il secondo grado, ove gli stessi vi consentano dando eventualmente evidenza del mancato consenso. (limitare, con appositi accorgimenti a cura dell\'interessato o della amministrazione, la pubblicazione dei dati sensibili)</li>\n<li>dichiarazione concernente le spese sostenute e le obbligazioni assunte per la propaganda elettorale ovvero attestazione di essersi avvalsi esclusivamente di materiali e di mezzi propagandistici predisposti e messi a disposizione dal partito o dalla formazione politica della cui lista il soggetto ha fatto parte, con l\'apposizione della formula &laquo;sul mio onore affermo che la dichiarazione corrisponde al vero&raquo; (con allegate copie delle dichiarazioni relative a finanziamenti e contributi per un importo che nell\'anno superi 5.000 &euro;) [Per il soggetto, il coniuge non separato e i parenti entro il secondo grado, ove gli stessi vi consentano (NB: dando eventualmente evidenza del mancato consenso)]</li>\n<li>attestazione concernente le variazioni della situazione patrimoniale intervenute nell\'anno precedente e copia della dichiarazione dei redditi [Per il soggetto, il coniuge non separato e i parenti entro il secondo grado, ove gli stessi vi consentano (NB: dando eventualmente evidenza del mancato consenso)]</li>\n<li>dichiarazione concernente le variazioni della situazione patrimoniale intervenute dopo l\'ultima attestazione (con copia della dichiarazione annuale relativa ai redditi delle persone fisiche) [Per il soggetto, il coniuge non separato e i parenti entro il secondo grado, ove gli stessi vi consentano (NB: dando eventualmente evidenza del mancato consenso)]</li>\n</ol>\n', 'sezione ospitante oggetti', '1,2', 'Tempestivo'),
	(9, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375800682, 1375800716, 0, 0, 0, 'istanza', 709, '1', '<p>Occorre inserire tutti i provvedimenti di erogazione delle sanzioni amministrative pecuniarie a carico del responsabile della mancata comunicazione per la mancata o incompleta comunicazione dei dati concernenti la situazione patrimoniale complessiva del titolare dell\'incarico (di organo di indirizzo politico) al momento dell\'assunzione della carica, la titolarit&agrave; di imprese, le partecipazioni azionarie proprie, del coniuge e dei parenti entro il secondo grado di parentela, nonch&egrave; tutti i compensi cui d&agrave; diritto l\'assunzione della carica.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(10, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375800805, 1375801001, 0, 0, 0, 'istanza', 710, '3', '<p>Pubblicare i rendiconti di esercizio annuale dei gruppi consiliari regionali e provinciali, con evidenza delle risorse trasferite o assegnate a ciascun gruppo ed indicazione del titolo di trasferimento e dell\'impiego delle risorse utilizzate. Sono inoltre da pubblicare gli atti e lerelazioni degli organi di controllo.</p>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(11, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375801151, 1375802694, 0, 0, 0, 'istanza', 25, '1', '<p>Inserire tutte le informazioni relative a ciascuna struttura organizzativa (es. Settori, Servizi, Uffici), anche di livello dirigenziale non generale.&nbsp;</p>\n', 'sezione ospitante oggetti', '14', 'Tempestivo'),
	(12, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375802788, 1375802788, 0, 0, 0, 'istanza', 65, '1', '<p>Inserire tutte le informazioni relative a ciascuna struttura organizzativa (es. Settori, Servizi, Uffici), anche di livello dirigenziale non generale.&nbsp;</p>\n', 'sezione ospitante oggetti', '14', 'Tempestivo'),
	(13, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375803424, 1383064083, 0, 0, 0, 'istanza', 19, '1', '<p>E\' necessario pubblicare gli estremi degli atti di conferimento di incarichi di collaborazione o di consulenza a soggetti esterni a qualsiasi titolo (compresi quelli affidati con contratto di collaborazione coordinata e continuativa) per i quali &egrave; previsto un compenso con indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato. Sono da pubblicare anche le seguenti informazioni:&nbsp;</p>\n<ol>\n<li>Curricula, redatti in conformit&agrave; al vigente modello europeo</li>\n<li>Compensi comunque denominati, relativi al rapporto di lavoro, di consulenza o di collaborazione (compresi quelli affidati con contratto di collaborazione coordinata e continuativa), con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato</li>\n<li>Dati relativi allo svolgimento di incarichi o alla titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o allo svolgimento di attivit&agrave; professionali</li>\n<li>Tabelle relative agli elenchi dei consulenti con indicazione di oggetto, durata e compenso dell\'incarico (comunicate alla Funzione pubblica)</li>\n<li>Attestazione dell\'avvenuta verifica dell\'insussistenza di situazioni, anche potenziali, di conflitto di interesse</li>\n</ol>\n', 'sezione ospitante oggetti', '8', 'Tempestivo'),
	(14, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375803819, 1375803819, 0, 0, 0, 'istanza', 60, '1', '<p>E\' necessario pubblicare gli estremi degli atti di conferimento di incarichi di collaborazione o di consulenza a soggetti esterni a qualsiasi titolo (compresi quelli affidati con contratto di collaborazione coordinata e continuativa) per i quali &egrave; previsto un compenso con indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato. Sono da pubblicare anche le seguenti informazioni:&nbsp;</p>\n<ol>\n<li>Curricula, redatti in conformit&agrave; al vigente modello europeo</li>\n<li>Compensi comunque denominati, relativi al rapporto di lavoro, di consulenza o di collaborazione (compresi quelli affidati con contratto di collaborazione coordinata e continuativa), con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato</li>\n<li>Dati relativi allo svolgimento di incarichi o alla titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o allo svolgimento di attivit&agrave; professionali</li>\n<li>Tabelle relative agli elenchi dei consulenti con indicazione di oggetto, durata e compenso dell\'incarico (comunicate alla Funzione pubblica)</li>\n<li>Attestazione dell\'avvenuta verifica dell\'insussistenza di situazioni, anche potenziali, di conflitto di interesse&nbsp;</li>\n</ol>\n', 'sezione ospitante oggetti', '8', 'Tempestivo'),
	(15, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375803870, 1375803870, 0, 0, 0, 'istanza', 61, '1', '<p>E\' necessario pubblicare gli estremi degli atti di conferimento di incarichi di collaborazione o di consulenza a soggetti esterni a qualsiasi titolo (compresi quelli affidati con contratto di collaborazione coordinata e continuativa) per i quali &egrave; previsto un compenso con indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato. Sono da pubblicare anche le seguenti informazioni:&nbsp;</p>\n<ol>\n<li>Curricula, redatti in conformit&agrave; al vigente modello europeo</li>\n<li>Compensi comunque denominati, relativi al rapporto di lavoro, di consulenza o di collaborazione (compresi quelli affidati con contratto di collaborazione coordinata e continuativa), con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato</li>\n<li>Dati relativi allo svolgimento di incarichi o alla titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o allo svolgimento di attivit&agrave; professionali</li>\n<li>Tabelle relative agli elenchi dei consulenti con indicazione di oggetto, durata e compenso dell\'incarico (comunicate alla Funzione pubblica)</li>\n<li>Attestazione dell\'avvenuta verifica dell\'insussistenza di situazioni, anche potenziali, di conflitto di interesse</li>\n</ol>\n', 'sezione ospitante oggetti', '8', 'Tempestivo'),
	(16, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375804390, 1375866826, 0, 0, 0, 'istanza', 748, '8,1', '<p>Sono da pubblicare in questa sezione tutte le informazioni obbligatorie relative a tali figure professionali:</p>\n<ol>\n<li>Estremi degli atti di conferimento di incarichi dirigenziali di vertice a soggetti dipendenti della pubblica amministrazione</li>\n<li>Estremi degli atti di conferimento di incarichi dirigenziali di vertice a soggetti estranei alla pubblica amministrazione con indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato</li>\n<li>Curricula, redatti in conformit&agrave; al vigente modello europeo</li>\n<li>Compensi, comunque denominati, relativi al rapporto di lavoro, con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato, ed ammontare erogato</li>\n<li>Dati relativi allo svolgimento di incarichi o alla titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o allo svolgimento di attivit&agrave; professionali, e relativi compensi&nbsp;</li>\n<li>Dichiarazione sulla insussistenza di una delle cause di inconferibilit&agrave; dell\'incarico (solo per&nbsp;Pubbliche amministrazioni di cui all\'articolo 1,comma2, del decreto legislativo 30 marzo 2001,n.165, ivi compresi gli enti pubblici,nonch&eacute; gli enti di diritto privato in controllo pubblico)</li>\n</ol>\n', 'sezione ospitante oggetti', '8,2', 'Tempestivo'),
	(17, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375867230, 1375868060, 0, 0, 0, 'istanza', 50, '8,1', '<p>Occorrre pubblicare le seguenti informazioni per tali figure professionali:</p>\n<ol>\n<li>Estremi degli atti di conferimento di incarichi dirigenziali a soggetti dipendenti della pubblica amministrazione</li>\n<li>Estremi degli atti di conferimento di incarichi dirigenziali a soggetti estranei alla pubblica amministrazione con indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato</li>\n<li>Curricula, redatti in conformit&agrave; al vigente modello europeo</li>\n<li>Compensi, comunque denominati, relativi al rapporto di lavoro, con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato</li>\n<li>Dati relativi allo svolgimento di incarichi o alla titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o allo svolgimento di attivit&agrave; professionali, e relativi compensi</li>\n<li>Dichiarazione sulla insussistenza di una delle cause di inconferibilit&agrave; dell\'incarico (solo per&nbsp;PA di cui all\'articolo 1,comma 2, del decreto legislativo 30 marzo 2001,n.165, ivi compresi gli enti pubblici,nonch&eacute; gli enti di diritto privato in controllo pubblico)</li>\n</ol>\n', 'sezione ospitante oggetti', '8,2', 'Tempestivo'),
	(18, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375868441, 1375868441, 0, 0, 0, 'istanza', 51, '1', '<p>E\' necessario inserire tramite l\'oggetto "Personale" informazioni e curricula dei titolari di posizioni organizzative redatti in conformit&agrave; al vigente modello europeo.</p>\n', 'sezione ospitante oggetti', '2', 'Tempestivo'),
	(19, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375869120, 1375869120, 0, 0, 0, 'istanza', 68, '1', '<p>Occorre inserire in questa sezione:</p>\n<ol>\n<li>Conto annuale del personale e relative spese sostenute, nell\'ambito del quale sono rappresentati i dati relativi alla dotazione organica e al personale effettivamente in servizio e al relativo costo, con l\'indicazione della distribuzione tra le diverse qualifiche e aree professionali, con particolare riguardo al personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico</li>\n<li>Costo complessivo del personale a tempo indeterminato in servizio, articolato per aree professionali, con particolare riguardo al personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(20, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375869793, 1375869793, 0, 0, 0, 'istanza', 749, '1', '<p>Occorre inserire il personale con rapporto di lavoro non a tempo indeterminato ed elenco dei titolari dei contratti a tempo determinato, con l\'indicazione delle diverse tipologie di rapporto, della distribuzione di questo personale tra le diverse qualifiche e aree professionali, ivi compreso il personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico</p>\n', 'sezione ospitante oggetti', '1,2', 'Annuale'),
	(21, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375870063, 1375870063, 0, 0, 0, 'istanza', 54, '1', '<p>E\' necessario pubblicare in formato tabellarte i tassi di assenza del personale distinti per uffici di livello dirigenziale</p>\n', 'editare contenuto di sezione', '1', 'Trimestrale'),
	(22, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375870894, 1375870936, 0, 0, 0, 'istanza', 59, '1', '<p>Occorre pubblicare tutti gli incarichi conferiti o autorizzati a ciascun dipendente, con l\'indicazione dell\'oggetto, della durata e del compenso spettante per ogni incarico</p>\n', 'sezione ospitante oggetti', '8,2', 'Tempestivo'),
	(23, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375880568, 1375881520, 0, 0, 0, 'istanza', 609, '1', '<p>Occorre inserire i riferimenti necessari per la consultazione dei contratti e accordi collettivi nazionali ed eventuali interpretazioni autentiche.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(24, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375881107, 1375881253, 0, 0, 0, 'istanza', 63, '1', '<p>E\' necessario inserire:</p>\n<ol>\n<li>Contratti integrativi stipulati, con la relazione tecnico-finanziaria e quella illustrativa certificate dagli organi di controllo (collegio dei revisori dei conti, collegio sindacale, uffici centrali di bilancio o analoghi organi previsti dai rispettivi ordinamenti)</li>\n<li>Specifiche informazioni sui costi della contrattazione integrativa, certificate dagli organi di controllo interno, trasmesse al Ministero dell\'Economia e delle finanze, che predispone, allo scopo, uno specifico modello di rilevazione (aggiornamento annuale)</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(25, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375881472, 1394448733, 0, 0, 0, 'istanza', 53, '1', '<p>Occorre inserire i dati degli Organismi Indipendenti di Valutazione (nominativi, curricula e compensi)</p>\n', 'sezione ospitante oggetti', '8', 'Tempestivo'),
	(26, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375882314, 1375882314, 0, 0, 0, 'istanza', 639, '1', '<p>Occorre pubblicare tutti i bandi di concorso per il reclutamento, a qualsiasi titolo, di personale presso l\'amministrazione, i concorsi e le prove selettive per l\'assunzione del personale e progressioni di carriera.</p>\n', 'sezione ospitante oggetti', '10', 'Tempestivo'),
	(27, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375882544, 1375882544, 0, 0, 0, 'istanza', 44, '1', '<p>E\' necessario pubblicare il Piano della Performance (art. 10, d.lgs. 150/2009)</p>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(28, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375882669, 1375883355, 0, 0, 0, 'istanza', 715, '1', '<p>Occorre pubblicare:</p>\n<ol>\n<li>la Relazione sulla Performance (art. 10, d.lgs. 150/2009)</li>\n<li>Il documento dell\'OIV di validazione della Relazione sulla Performance</li>\n<li>La relazione dell\'OIV sul funzionamento complessivo del Sistema di valutazione, trasparenza e integrit&agrave; dei controlli interni</li>\n</ol>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(29, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375883274, 1375883274, 0, 0, 0, 'istanza', 56, '1', '<p>Inserire l\'ammontare complessivo dei premi collegati alla performance stanziati e l\'ammontare dei premi effettivamente distribuiti</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(30, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375883595, 1375883595, 0, 0, 0, 'istanza', 57, '1', '<div>E\' necessario pubblicare in questa sezione:</div>\n<ol>\n<li>Entit&agrave; del premio mediamente conseguibile dal personale dirigenziale e non dirigenziale</li>\n<li>Distribuzione del trattamento accessorio, in forma aggregata, al fine di dare conto del livello di selettivit&agrave; utilizzato nella distribuzione dei premi e degli incentivi</li>\n<li>Grado di differenziazione dell\'utilizzo della premialit&agrave; sia per i dirigenti sia per i dipendenti</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(31, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375883774, 1375883774, 0, 0, 0, 'istanza', 716, '1', '<p>Bisogna pubblicare i Livelli di benessere organizzativo</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(32, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375885022, 1375885022, 0, 0, 0, 'istanza', 718, '', '<p>Elenco in formato tabellare degli enti pubblici, comunque denominati, istituiti, vigilati e finanziati dall\'amministrazione ovvero per i quali l\'amministrazione abbia il potere di nomina degli amministratori dell\'ente, con l\'indicazione delle funzioni attribuite e delle attivit&agrave; svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate. Per ciascuno degli enti pubblicare:&nbsp;</p>\n<ol>\n<li>ragione sociale</li>\n<li>misura dell\'eventuale partecipazione dell\'amministrazione</li>\n<li>durata dell\'impegno</li>\n<li>onere complessivo a qualsiasi titolo gravante per l\'anno sul bilancio dell\'amministrazione</li>\n<li>numero dei rappresentanti dell\'amministrazione negli organi di governo e trattamento economico complessivo a ciascuno di essi spettante</li>\n<li>risultati di bilancio degli ultimi tre esercizi finanziari</li>\n<li>incarichi di amministratore dell\'ente e relativo trattamento economico complessivo</li>\n<li>Dichiarazione sulla insussistenza di una delle cause di inconferibilit&agrave; dell\'incarico (solo per&nbsp;Pubbliche amministrazioni di cu iall\'articolo 1,comma 2,del DLgs 30 marzo 2001,n.165, ivi compresi gli enti pubblici, nonch&eacute; gli enti di diritto privato in controllo pubblico)</li>\n<li>Collegamento con i siti istituzionali degli enti pubblici vigilati nei quali sono pubblicati i dati relativi ai componenti degli organi di indirizzo politico e ai soggetti titolari di incarichi dirigenziali, di collaborazione o consulenza</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(33, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375885413, 1375885432, 0, 0, 0, 'istanza', 719, '1', '<p>Elenco in formato tabelare degli enti di diritto privato, comunque denominati, in controllo dell\'amministrazione, con l\'indicazione delle funzioni attribuite e delle attivit&agrave; svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate.&nbsp;Per ciascuno degli enti:</p>\n<ol>\n<li>ragione sociale</li>\n<li>misura dell\'eventuale partecipazione dell\'amministrazione</li>\n<li>durata dell\'impegno</li>\n<li>onere complessivo a qualsiasi titolo gravante per l\'anno sul bilancio dell\'amministrazione</li>\n<li>numero dei rappresentanti dell\'amministrazione negli organi di governo e trattamento economico complessivo a ciascuno di essi spettante</li>\n<li>risultati di bilancio degli ultimi tre esercizi finanziari</li>\n<li>incarichi di amministratore dell\'ente e relativo trattamento economico complessivo</li>\n<li>Dichiarazione sulla insussistenza di una delle cause di inconferibilit&agrave; dell\'incarico (solo per&nbsp;Pubbliche amministrazioni di cu iall\'articolo 1,comma 2,del DLgs 30 marzo 2001,n.165, ivi compresi gli enti pubblici, nonch&eacute; gli enti di diritto privato in controllo pubblico)</li>\n<li>Collegamento con i siti istituzionali degli enti di diritto privato controllati nei quali sono pubblicati i dati relativi ai componenti degli organi di indirizzo politico e ai soggetti titolari di incarichi dirigenziali, di collaborazione o consulenza</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(34, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375885820, 1375887392, 0, 0, 0, 'istanza', 64, '', '<p>Elenco in formato tabellare delle societ&agrave; di cui l\'amministrazione detiene direttamente quote di partecipazione anche minoritaria, con l\'indicazione dell\'entit&agrave;, delle funzioni attribuite e delle attivit&agrave; svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate.&nbsp;Per ciascuna delle societ&agrave; occorre pubblicare:</p>\n<ol>\n<li>ragione sociale</li>\n<li>misura dell\'eventuale partecipazione dell\'amministrazione</li>\n<li>durata dell\'impegno</li>\n<li>onere complessivo a qualsiasi titolo gravante per l\'anno sul bilancio dell\'amministrazione</li>\n<li>numero dei rappresentanti dell\'amministrazione negli organi di governo e trattamento economico complessivo a ciascuno di essi spettante</li>\n<li>risultati di bilancio degli ultimi tre esercizi finanziari</li>\n<li>incarichi di amministratore della societ&agrave; e relativo trattamento economico complessivo</li>\n<li>Collegamento con i siti istituzionali delle societ&agrave; partecipate nei quali sono pubblicati i dati relativi ai componenti degli organi di indirizzo politico e ai soggetti titolari di incarichi dirigenziali, di collaborazione o consulenza</li>\n</ol>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(35, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375887715, 1375887715, 0, 0, 0, 'istanza', 720, '', '<p>Pubblicare una o pi&ugrave; rappresentazioni grafiche che evidenziano i rapporti tra l\'amministrazione e gli enti pubblici vigilati, le societ&agrave; partecipate, gli enti di diritto privato controllati.</p>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(36, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375888272, 1375888272, 0, 0, 0, 'istanza', 721, '1', '<p>Le pubbliche amministrazioni che organizzano, a fini conoscitivi&nbsp;e statistici, i dati relativi alla attivit&agrave; amministrativa, li devono pubblicare in forma aggregata, per settori di attivit&agrave;, per competenza degli organi e degli uffici, per tipologia di procedimenti.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(37, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375889477, 1375889477, 0, 0, 0, 'istanza', 22, '', '<p>Inserire per ciascuna tipologia di procedimento le informazioni richieste dal sistema</p>\n', 'sezione ospitante oggetti', '1,3', 'Tempestivo'),
	(38, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375889770, 1375889770, 0, 0, 0, 'istanza', 722, '', '<p>Sono da pubblicare in questa sezione i risultati del monitoraggio periodico concernente il rispetto dei tempi procedimentali.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(39, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375890622, 1375952627, 0, 0, 0, 'istanza', 723, '1', '<p>In questa sezione occorre effettuare una scelta tra le Strutture Organizzative dell\'ufficio responsabile per le attivit&agrave; volte a gestire, garantire e verificare la trasmissione dei dati o l\'accesso diretto degli stessi da parte delle amministrazioni procedenti all\'acquisizione d\'ufficio dei dati e allo svolgimento dei controlli sulle dichiarazioni sostitutive.</p>\n', 'sezione ospitante oggetti', '1,14', 'Tempestivo'),
	(40, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375953107, 1375953107, 0, 0, 0, 'istanza', 725, '', '<p>Occorre inserire i provvedimenti emanati dagli organi di indirizzo politico, con particolare riferimento ai provvedimenti finali dei procedimenti di autorizzazione o concessione. Occorre inoltre inserire: la scelta del contraente per l\'affidamento di lavori, forniture e servizi, anche con riferimento alla modalit&agrave; di selezione prescelta; i concorsi e le prove selettive per l\'assunzione del personale e progressioni di carriera; gli accordi stipulati dall\'amministrazione con soggetti privati o con altre amministrazioni pubbliche.</p>\n', 'sezione ospitante oggetti', '4', 'Semestrale'),
	(41, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375953262, 1375953351, 0, 0, 0, 'istanza', 726, '', '<p>Occorre inserire i provvedimenti emanati dagli organi di indirizzo politico, con particolare riferimento ai provvedimenti finali dei procedimenti di autorizzazione o concessione. Occorre inoltre inserire: la scelta del contraente per l\'affidamento di lavori, forniture e servizi, anche con riferimento alla modalit&agrave; di selezione prescelta; i concorsi e le prove selettive per l\'assunzione del personale e progressioni di carriera; gli accordi stipulati dall\'amministrazione con soggetti privati o con altre amministrazioni pubbliche.</p>\n', 'sezione ospitante oggetti', '4', 'Semestrale'),
	(42, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375953685, 1375953918, 0, 0, 0, 'istanza', 727, '1', '<p>E\' necessario inserire le&nbsp;tipologie di controllo a cui sono assoggettate le imprese in ragione della dimensione e del settore di attivit&agrave;, con l\'indicazione per ciascuna di esse dei criteri e delle relative modalit&agrave; di svolgimento. Inserire anche l\'elenco degli obblighi e degli adempimenti oggetto delle attivit&agrave; di controllo che le imprese sono tenute a rispettare per ottemperare alle disposizioni normative.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(43, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375956198, 1375957339, 0, 0, 0, 'istanza', 731, '', '<p>In questa sezione &egrave; necessario pubblicare il Bilancio di previsione e il bilancio consuntivo di ciascun anno in forma sintetica, aggregata e semplificata, anche con il ricorso a rappresentazioni grafiche.</p>\n', 'sezione ospitante oggetti', '7', 'Tempestivo'),
	(44, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375957528, 1375957528, 0, 0, 0, 'istanza', 732, '', '<p>Inserire il Piano degli indicatori e risultati attesi di bilancio, con l&rsquo;integrazione delle risultanze osservate in termini di raggiungimento dei risultati attesi e le motivazioni degli eventuali scostamenti e gli aggiornamenti in corrispondenza di ogni nuovo esercizio di bilancio, sia tramite la specificazione di nuovi obiettivi e indicatori, sia attraverso l&rsquo;aggiornamento dei valori obiettivo e la soppressione di obiettivi gi&agrave; raggiunti oppure oggetto di ripianificazione.</p>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(45, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375958642, 1375958642, 0, 0, 0, 'istanza', 734, '', '<p>In questa sezione occorre inserire le informazioni identificative degli immobili posseduti dall\'Ente.</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(46, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375958943, 1383063454, 0, 0, 0, 'istanza', 735, '', '<p>In questa sezione occorre inserire i canoni di locazione o di affitto versati o percepiti dall\'Ente</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(47, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375959837, 1375969809, 0, 0, 0, 'istanza', 632, '', '<p>Carta dei servizi o documento contenente gli standard di qualit&agrave; dei servizi pubblici</p>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(48, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375971254, 1375971254, 0, 0, 0, 'istanza', 62, '', '<p>Inserire i costi contabilizzati dei servizi erogati agli utenti, sia finali che intermedi, evidenziando quelli effettivamente sostenuti e quelli imputati al personale per ogni servizio erogato e il relativo andamento nel tempo</p>\n', 'editare contenuto di sezione', '1,6', 'Annuale'),
	(49, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1376042052, 1376042052, 0, 0, 0, 'istanza', 739, '', '<p>Inserire e riportaqre in questa pagina l\'indicatore dei tempi medi di pagamento relativi agli acquisti di beni, servizi e forniture</p>\n', 'editare contenuto di sezione', '1', 'Annuale'),
	(50, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1376042475, 1376042475, 0, 0, 0, 'istanza', 740, '', '<p>E\' necessario inserire &nbsp;per le richieste di pagamento le seguenti informazioni: i codici IBAN identificativi del conto di pagamento, ovvero di imputazione del versamento in Tesoreria, tramite i quali i soggetti versanti possono effettuare i pagamenti mediante bonifico bancario o postale, ovvero gli identificativi del conto corrente postale sul quale i soggetti versanti possono effettuare i pagamenti mediante bollettino postale, nonch&egrave; i codici identificativi del pagamento da indicare obbligatoriamente per il versamento</p>\n', 'editare contenuto di sezione', '1', 'Tempestivo'),
	(51, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1376043361, 1376043361, 0, 0, 0, 'istanza', 741, '', '<p>Occorre inserire le seguenti informazioni:</p>\n<ol>\n<li>Documenti di programmazione, anche pluriennale, delle opere pubbliche di competenza dell\'amministrazione</li>\n<li>Linee guida per la valutazione degli investimenti</li>\n<li>Relazioni annuali</li>\n<li>Ogni altro documento predisposto nell\'ambito della valutazione, ivi inclusi i pareri dei valutatori che si discostino dalle scelte delle amministrazioni e gli esiti delle valutazioni ex post che si discostino dalle valutazioni ex ante</li>\n<li>Informazioni relative ai Nuclei di valutazione e verifica degli investimenti pubblici, incluse le funzioni e i compiti specifici ad essi attribuiti, le procedure e i criteri di individuazione dei componenti e i loro nominativi</li>\n<li>Informazioni relative ai tempi e agli indicatori di realizzazione delle opere pubbliche completate</li>\n<li>Informazioni relative ai costi unitari di realizzazione delle opere pubbliche completate</li>\n</ol>\n', 'editare contenuto di sezione', '1,6', 'Tempestivo'),
	(52, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1376043985, 1376043985, 0, 0, 0, 'istanza', 742, '', '<p>Inserire gli atti di governo del territorio quali, tra gli altri, piani territoriali, piani di coordinamento, piani paesistici, strumenti urbanistici, generali e di attuazione, nonch&eacute; le loro varianti</p>\n', 'editare contenuto di sezione', '1,4', 'Tempestivo'),
	(53, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1376044507, 1376044507, 0, 0, 0, 'istanza', 745, '', '<p>Occorre inserire le seguenti informazioni:</p>\n<ol>\n<li>Provvedimenti adottati concernenti gli interventi straordinari e di emergenza che comportano deroghe alla legislazione vigente, con l\'indicazione espressa delle norme di legge eventualmente derogate e dei motivi della deroga, nonch&eacute; con l\'indicazione di eventuali atti amministrativi o giurisdizionali intervenuti</li>\n<li>Termini temporali eventualmente fissati per l\'esercizio dei poteri di adozione dei provvedimenti straordinari</li>\n<li>Costo previsto degli interventi e costo effettivo sostenuto dall\'amministrazione</li>\n<li>Particolari forme di partecipazione degli interessati ai procedimenti di adozione dei provvedimenti straordinari</li>\n</ol>\n', 'sezione ospitante oggetti', '1,4,6', 'Tempestivo'),
	(54, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1417002680, 1417002680, 0, 0, 0, 'istanza', 771, '', '', '', '4,6', ''),
	(55, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1423066344, 1423066576, 0, 0, 0, 'istanza', 769, '', '', '', '2,4,6', '');
/*!40000 ALTER TABLE `oggetto_etrasp_help` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help_adminui: 4 rows
DELETE FROM `oggetto_etrasp_help_adminui`;
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_help_adminui` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `menu`, `menusec`, `azione`, `titolo`, `tipo_cont`, `contenuto`) VALUES
	(1, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373395578, 1373689798, 0, 0, 0, 'istanza', 'desktop', 'nessuna', 'nessuna', 'Desktop amministrativo dell\'utente', '', '<p>Le funzionalit&agrave; presenti nella pagina Desktop, guidano l\'utente verso una efficace gestione delle informazioni da pubblicare nella piattaforma PAT.</p>\n'),
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373395946, 1373395946, 0, 0, 0, 'istanza', 'organizzazione', 'strutture', 'nessuna', 'Gestione delle strutture organizzative dell\'ente', '', '<p>Le strutture organizzative contengono i dati sull\'organigramma degli uffici dell\'ente. La loro gestione &egrave; importante poich&egrave; diverse aree del portale devono farne uso per le loro pubblicazioni.<br /></p>\n'),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373396003, 1373396003, 0, 0, 0, 'istanza', 'organizzazione', 'strutture', 'aggiungi', 'Aggiunta di una Struttura Organizzativa', '', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim</p>\n'),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1399647310, 1399647900, 0, 0, 0, 'istanza', 'organizzazione', 'societa', 'nessuna', 'Societ&agrave; partecipate', '16', '<p>- Elenco degli enti pubblici istituiti, vigilati e finanziati dall\'amministrazione</p>\n<p>-&nbsp;Elenco delle societ&agrave; di cui l\'amministrazione detiene direttamente quote di partecipazione&nbsp;</p>\n<div>-&nbsp;Elenco degli enti di diritto privato in controllo dell\'amministrazione</div>\n');
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help_adminui_backup: 0 rows
DELETE FROM `oggetto_etrasp_help_adminui_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help_adminui_workflow: 0 rows
DELETE FROM `oggetto_etrasp_help_adminui_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_help_adminui_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help_backup: 0 rows
DELETE FROM `oggetto_etrasp_help_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_help_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_help_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_help_workflow: 0 rows
DELETE FROM `oggetto_etrasp_help_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_help_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_help_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_modello: 0 rows
DELETE FROM `oggetto_etrasp_modello`;
/*!40000 ALTER TABLE `oggetto_etrasp_modello` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_modello` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_modello_backup: 0 rows
DELETE FROM `oggetto_etrasp_modello_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_modello_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_modello_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_modello_workflow: 0 rows
DELETE FROM `oggetto_etrasp_modello_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_modello_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_modello_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_news_admin: 0 rows
DELETE FROM `oggetto_etrasp_news_admin`;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_news_admin_backup: 0 rows
DELETE FROM `oggetto_etrasp_news_admin_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_news_admin_workflow: 0 rows
DELETE FROM `oggetto_etrasp_news_admin_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_news_admin_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_norma: 48 rows
DELETE FROM `oggetto_etrasp_norma`;
/*!40000 ALTER TABLE `oggetto_etrasp_norma` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_norma` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `norma`, `num_art`, `commi`, `tipo_enti`, `testo_norma`, `altre_note`, `sezioni`, `tipo_cont`) VALUES
	(1, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373073861, 1423126525, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 13', 'Comma1 Lettera A', '', '<p><strong>Obblighi di pubblicazione concernenti l\'organizzazione delle pubbliche amministrazioni</strong><br />\n1. Le pubbliche amministrazioni pubblicano e aggiornano le informazioni e i dati concernenti la propria organizzazione, corredati dai documenti anche normativi di riferimento. Sono pubblicati, tra gli altri, i dati relativi:<br />\na) agli organi di indirizzo politico e di amministrazione e gestione, con l\'indicazione delle rispettive competenze;</p>\n', '', '701', '2'),
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373073891, 1423212648, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 47', '', '', '<p><strong>Sanzioni per casi specifici</strong><br />\n1. La mancata o incompleta comunicazione delle informazioni e dei dati di cui all\'articolo 14, concernenti la situazione patrimoniale complessiva del titolare dell\'incarico al momento dell\'assunzione in carica, la titolarita\' di imprese, le partecipazioni azionarie proprie, del coniuge e dei parenti entro il secondo grado, nonch&egrave; tutti i compensi cui da diritto l\'assunzione della carica, da\' luogo a una sanzione amministrativa pecuniaria da 500 a 10.000 euro a carico del responsabile della mancata comunicazione e il relativo provvedimento &egrave; pubblicato sul sito internet dell\'amministrazione o organismo interessato.<br />\n2. La violazione degli obblighi di pubblicazione di cui all\'articolo 22, comma 2, da\' luogo ad una sanzione amministrativa pecuniaria da 500 a 10.000 euro a carico del responsabile della violazione. La stessa sanzione si applica agli amministratori societari che non comunicano ai soci pubblici il proprio incarico ed il relativo compenso entro trenta giorni dal conferimento ovvero, per le indennit&agrave; di risultato, entro trenta giorni dal percepimento.<br />\n3. Le sanzioni di cui ai commi 1 e 2 sono irrogate dall\'autorita\' amministrativa competente in base a quanto previsto dalla legge 24 novembre 1981, n. 689.</p>\n', '', '709', '1'),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373396244, 1423150324, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 13', 'Comma 1 Lettera B,C', '', '<p><strong>Obblighi di pubblicazione concernenti l\'organizzazione delle pubbliche amministrazioni</strong><br />\n1. Le pubbliche amministrazioni pubblicano e aggiornano le informazioni e i dati concernenti la propria organizzazione, corredati dai documenti anche normativi di riferimento. Sono pubblicati, tra gli altri, i dati relativi:<br />\nb) all\'articolazione degli uffici, le competenze e le risorse a disposizione di ciascun ufficio, anche di livello dirigenziale non generale, i nomi dei dirigenti responsabili dei singoli uffici;<br />\nc) all\'illustrazione in forma semplificata, ai fini della piena accessibilit&agrave; e comprensibilita\' dei dati, dell\'organizzazione dell\'amministrazione, mediante l\'organigramma o analoghe rappresentazioni grafiche;</p>\n', '', '25', '1,2,14'),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373396413, 1423154920, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 13', 'Comma1 Lettera D', '', '<p><strong>Obblighi di pubblicazione concernenti l\'organizzazione delle pubbliche amministrazioni</strong><br />\n1. Le pubbliche amministrazioni pubblicano e aggiornano le informazioni e i dati concernenti la propria organizzazione, corredati dai documenti anche normativi di riferimento. Sono pubblicati, tra gli altri, i dati relativi:<br />\nd) all\'elenco dei numeri di telefono nonch&egrave; delle caselle di posta elettronica istituzionali e delle caselle di posta elettronica certificata dedicate, cui il cittadino possa rivolgersi per qualsiasi richiesta inerente i compiti istituzionali.</p>\n', '', '65', '2,14'),
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495045, 1375786831, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', '28', 'Comma 1', '3', '<p><strong>Pubblicit&agrave; dei rendiconti dei gruppi consiliari regionali e provinciali</strong><br />\n1. Le regioni, le province autonome di Trento e Bolzano e le province pubblicano i rendiconti di cui all\'articolo 1, comma 10, del decreto-legge 10 ottobre 2012, n. 174, convertito, con modificazioni, dalla legge 7 dicembre 2012, n. 213, dei gruppi consiliari regionali e provinciali, con evidenza delle risorse trasferite o assegnate a ciascun gruppo, con indicazione del titolo di trasferimento e dell\'impiego delle risorse utilizzate. Sono altres&igrave; pubblicati gli atti e le relazioni degli organi di controllo.</p>\n', '', '710', '1'),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495092, 1423153764, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 10', 'Comma 8 Lettera A', '', '<p>8. Ogni amministrazione ha l\'obbligo di pubblicare sul proprio sito istituzionale nella sezione: &laquo;Amministrazione trasparente&raquo; di cui all\'articolo 9:<br />\na) il Programma triennale per la trasparenza e l\'integrit&agrave; ed il relativo stato di attuazione</p>\n', '', '43', '1,6'),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495171, 1430234179, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 12', 'Comma 1,2', '', '<p><strong>Obblighi di pubblicazione concernenti gli atti di carattere normativo e amministrativo generale</strong><br />\n1. Fermo restando quanto previsto per le pubblicazioni nella Gazzetta Ufficiale della Repubblica italiana dalla legge 11 dicembre 1984, n. 839, e dalle relative norme di attuazione, le pubbliche<br />\namministrazioni pubblicano sui propri siti istituzionali i riferimenti normativi con i relativi link alle norme di legge statale pubblicate nella banca dati &laquo;Normattiva&raquo; che ne regolano l\'istituzione, l\'organizzazione e l\'attivit&agrave;. Sono altres&igrave; pubblicati le direttive, le circolari, i programmi e le istruzioni emanati dall\'amministrazione e ogni atto che dispone in generale sulla organizzazione, sulle funzioni, sugli obiettivi, sui procedimenti ovvero nei quali si determina l\'interpretazione di norme giuridiche che le riguardano o si dettano disposizioni per l\'applicazione di esse, ivi compresi i codici di condotta.<br />\n2. Con riferimento agli statuti e alle norme di legge regionali, che regolano le funzioni, l\'organizzazione e lo svolgimento delle attivit&agrave; di competenza dell\'amministrazione, sono pubblicati gli estremi degli atti e dei testi ufficiali aggiornati.</p>\n', '', '747', '12,6'),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495217, 1423212828, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 34', 'Comma 1,2', '5', '<p><strong>Trasparenza degli oneri informativi</strong><br />\n1. I regolamenti ministeriali o interministeriali, nonch&egrave; i provvedimenti amministrativi a carattere generale adottati dalle amministrazioni dello Stato per regolare l\'esercizio di poteri autorizzatori, concessori o certificatori, nonche\' l\'accesso ai servizi pubblici ovvero la concessione di benefici, recano in allegato l\'elenco di tutti gli oneri informativi gravanti sui cittadini e sulle imprese introdotti o eliminati con gli atti medesimi. Per onere informativo si intende qualunque obbligo informativo o adempimento che comporti la raccolta, l\'elaborazione, la trasmissione, la conservazione e la produzione di informazioni e documenti alla pubblica amministrazione.<br />\n2. Ferma restando, ove prevista, la pubblicazione nella Gazzetta Ufficiale, gli atti di cui al comma 1 sono pubblicati sui siti istituzionali delle amministrazioni, secondo i criteri e le modalit&agrave; definite con il regolamento di cui all\'articolo 7, commi 2 e 4, della legge 11 novembre 2011, n. 180.</p>\n', '<p><strong><br /></strong></p>\n', '700', '13'),
	(46, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375886021, 1423126309, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 22', '', '', '<p><strong>Obblighi di pubblicazione dei dati relativi agli enti pubblici&nbsp;vigilati, e agli enti di diritto privato in controllo pubblico,&nbsp;nonch&egrave; alle partecipazioni in societ&agrave; di diritto privato.</strong></p>\n<p>1. Ciascuna amministrazione pubblica e aggiorna annualmente:<br />\na) l\'elenco degli enti pubblici, comunque denominati, istituiti,&nbsp;vigilati e finanziati dalla amministrazione medesima ovvero per i quali l\'amministrazione abbia il potere di nomina degli&nbsp;amministratori dell\'ente, con l\'elencazione delle funzioni attribuite&nbsp;e delle attivita\' svolte in favore dell\'amministrazione o delle<br />\nattivita\' di servizio pubblico affidate;b) l\'elenco delle societa\' di cui detiene direttamente quote di partecipazione anche minoritaria indicandone l\'entita\', con l\'indicazione delle funzioni attribuite e delle attivita\' svolte in favore dell\'amministrazione o delle attivita\' di servizio pubblico affidate; c) l\'elenco degli enti di diritto privato, comunque denominati, in controllo dell\'amministrazione, con l\'indicazione delle funzioni attribuite e delle attivita\' svolte in favore dell\'amministrazione o delle attivita\' di servizio pubblico affidate. Ai fini delle presenti disposizioni sono enti di diritto privato in controllo pubblico gli enti di diritto privato sottoposti a controllo da parte di amministrazioni pubbliche, oppure gli enti costituiti o vigilati da pubbliche amministrazioni nei quali siano a queste riconosciuti, anche in assenza di una partecipazione azionaria, poteri di nomina dei vertici o dei componenti degli organi; d) una o piu\' rappresentazioni grafiche che evidenziano i rapporti tra l\'amministrazione e gli enti di cui al precedente comma.<br />\n2. Per ciascuno degli enti di cui alle lettere da a) a c) del comma&nbsp;1 sono pubblicati i dati relativi alla ragione sociale, alla misura&nbsp;della eventuale partecipazione dell\'amministrazione, alla durata dell\'impegno, all\'onere complessivo a qualsiasi titolo gravante per&nbsp;l\'anno sul bilancio dell\'amministrazione, al numero dei&nbsp;rappresentanti dell\'amministrazione negli organi di governo, al&nbsp;trattamento economico complessivo a ciascuno di essi spettante, ai risultati di bilancio degli ultimi tre esercizi finanziari. Sono&nbsp;altresi\' pubblicati i dati relativi agli incarichi di amministratore&nbsp;dell\'ente e il relativo trattamento economico complessivo.<br />\n3. Nel sito dell\'amministrazione e\' inserito il collegamento con i&nbsp;siti istituzionali degli enti di cui al comma 1, nei quali sono&nbsp;pubblicati i dati relativi ai componenti degli organi di indirizzo e ai soggetti titolari di incarico, in applicazione degli articoli 14 e&nbsp;15.<br />\n4. Nel caso di mancata o incompleta pubblicazione dei dati relativi&nbsp;agli enti di cui al comma 1, e\' vietata l\'erogazione in loro favore&nbsp;di somme a qualsivoglia titolo da parte dell\'amministrazione&nbsp;interessata.<br />\n5. Le amministrazioni titolari di partecipazioni di controllo&nbsp;promuovono l\'applicazione dei principi di trasparenza di cui ai commi&nbsp;1, lettera b), e 2, da parte delle societa\' direttamente controllate&nbsp;nei confronti delle societa\' indirettamente controllate dalle&nbsp;medesime amministrazioni.<br />\n6. Le disposizioni di cui al presente articolo non trovano&nbsp;applicazione nei confronti delle societa\', partecipate da&nbsp;amministrazioni pubbliche, quotate in mercati regolamentati e loro&nbsp;controllate</p>\n', '', '717', '16'),
	(10, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495433, 1423212419, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 15', 'Comma 1,2,5', '', '<div class="sub_paragrafo18"><strong>Obblighi di pubblicazione concernenti i titolari di incarichi dirigenziali e di collaborazione o consulenza</strong><br />\n<p>1. Fermi restando gli obblighi di comunicazione di cui all\'articolo 17, comma 22, della legge 15 maggio 1997, n. 127, le pubbliche amministrazioni pubblicano e aggiornano le seguenti informazioni relative ai titolari di incarichi amministrativi di vertice e di incarichi dirigenziali, a qualsiasi titolo conferiti, nonch&egrave; di collaborazione o consulenza:<br />\na) gli estremi dell\'atto di conferimento dell\'incarico;<br />\nb) il curriculum vitae;<br />\nc) i dati relativi allo svolgimento di incarichi o la titolarit&agrave; di cariche in enti di diritto privato regolati o finanziati dalla pubblica amministrazione o lo svolgimento di attivit&agrave; professionali;<br />\nd) i compensi, comunque denominati, relativi al rapporto di lavoro, di consulenza o di collaborazione, con specifica evidenza delle eventuali componenti variabili o legate alla valutazione del risultato.<br />\n2. La pubblicazione degli estremi degli atti di conferimento di incarichi dirigenziali a soggetti estranei alla pubblica amministrazione, di collaborazione o di consulenza a soggetti esterni a qualsiasi titolo per i quali &egrave; previsto un compenso, completi di indicazione dei soggetti percettori, della ragione dell\'incarico e dell\'ammontare erogato, nonch&egrave; la comunicazione alla Presidenza del&nbsp;Consiglio dei Ministri - Dipartimento della funzione pubblica dei relativi dati ai sensi dell\'articolo 53, comma 14, secondo periodo, del decreto legislativo 30 marzo 2001, n. 165 e successive modificazioni, sono condizioni per l\'acquisizione dell\'efficacia dell\'atto e per la liquidazione dei relativi compensi.<br />\nLe amministrazioni pubblicano e mantengono aggiornati sui rispettivi siti istituzionali gli elenchi dei propri consulenti indicando l\'oggetto, la durata e il compenso dell\'incarico. Il Dipartimento della funzione pubblica consente la consultazione, anche per nominativo, dei dati di cui al presente comma.<br />\n5. Le pubbliche amministrazioni pubblicano e mantengono aggiornato l\'elenco delle posizioni dirigenziali, integrato dai relativi titoli e curricula, attribuite a persone, anche esterne alle pubbliche<br />\namministrazioni, individuate discrezionalmente dall\'organo di indirizzo politico senza procedure pubbliche di selezione, di cui all\'articolo 1, commi 39 e 40, della legge 6 novembre 2012, n. 190.</p>\n</div>\n', '', '50', '2'),
	(11, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373495467, 1423154937, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 10', 'Comma 8 Lettera D', '', '<p>8. Ogni amministrazione ha l\'obbligo di pubblicare sul proprio sito istituzionale nella sezione: &laquo;Amministrazione trasparente&raquo; di cui all\'articolo 9:<br />\nd) i curricula e i compensi dei soggetti di cui all\'articolo 15, comma 1, nonch&egrave; i curricula dei titolari di posizioni organizzative, redatti in conformit&agrave; al vigente modello europeo.</p>\n', '', '50,51', '2'),
	(12, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575176, 1423217274, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 16', 'Comma 1,2', '', '<p><strong>Obblighi di pubblicazione concernenti la dotazione organica e il costo del personale con rapporto di lavoro a tempo indeterminato.</strong><br />\n1. Le pubbliche amministrazioni pubblicano il conto annuale del personale e delle relative spese sostenute, di cui all\'articolo 60, comma 2, del decreto legislativo 30 marzo 2001, n. 165, nell\'ambito del quale sono rappresentati i dati relativi alla dotazione organica e al personale effettivamente in servizio e al relativo costo, con l\'indicazione della sua distribuzione tra le diverse qualifiche e aree professionali, con particolare riguardo al personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico.<br />\n2. Le pubbliche amministrazioni, nell\'ambito delle pubblicazioni di cui al comma 1, evidenziano separatamente, i dati relativi al costo complessivo del personale a tempo indeterminato in servizio,&nbsp;articolato per aree professionali, con particolare riguardo al personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico.</p>\n', '', '68', '1'),
	(13, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575219, 1423212803, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 17', 'Comma 1,2', '', '<p><strong>Obblighi di pubblicazione dei dati relativi al personale non a tempo indeterminato</strong><br />\n1. Le pubbliche amministrazioni pubblicano annualmente, nell\'ambito di quanto previsto dall\'articolo 16, comma 1, i dati relativi al personale con rapporto di lavoro non a tempo indeterminato, con la indicazione delle diverse tipologie di rapporto, della distribuzione di questo personale tra le diverse qualifiche e aree professionali, ivi compreso il personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico. La pubblicazione comprende l\'elenco dei titolari dei contratti a tempo determinato.<br />\n2. Le pubbliche amministrazioni pubblicano trimestralmente i dati relativi al costo complessivo del personale di cui al comma 1, articolato per aree professionali, con particolare riguardo al personale assegnato agli uffici di diretta collaborazione con gli organi di indirizzo politico.</p>\n', '', '749,713', '1'),
	(14, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575285, 1430234097, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 16', 'Comma 3', '', '<p>Le pubbliche amministrazioni pubblicano trimestralmente i dati relativi ai tassi di assenza del personale distinti per uffici di livello dirigenziale.</p>\n', '', '54,713', '1'),
	(15, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575354, 1423212768, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33 - DLgs  n. 165/2001', 'articolo 18, articolo 53', 'Comma 1, Comma 14', '', '<p><strong>Obblighi di pubblicazione dei dati relativi agli incarichi conferiti ai dipendenti pubblici</strong><br />\n1. Le pubbliche amministrazioni pubblicano l\'elenco degli incarichi conferiti o autorizzati a ciascuno dei propri dipendenti, con l\'indicazione della durata e del compenso spettante per ogni incarico.</p>\n', '', '59', '8'),
	(16, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575401, 1430233972, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 21', 'Comma 1', '', '<p><strong>Obblighi di pubblicazione concernenti i dati sulla contrattazione collettiva</strong><br />\n1. Le pubbliche amministrazioni pubblicano i riferimenti necessari per la consultazione dei contratti e accordi collettivi nazionali, che si applicano loro, nonch&egrave; le eventuali interpretazioni autentiche.</p>\n', '', '609', '1'),
	(17, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575465, 1430311727, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33 - Art. 55, c. 4, DLgs  n. 150/2009', 'articolo 21', 'Comma 2', '', '<p>2. Fermo restando quanto previsto dall\'articolo 47, comma 8, del decreto legislativo 30 marzo 2001, n. 165, le pubbliche amministrazioni pubblicano i contratti integrativi stipulati, con la relazione tecnico-finanziaria e quella illustrativa certificate dagli organi di controllo di cui all\'articolo 40-bis, comma 1, del decreto legislativo n. 165 del 2001, nonch&egrave; le informazioni trasmesse annualmente ai sensi del comma 3 dello stesso articolo. La relazione illustrativa, fra l\'altro, evidenzia gli effetti attesi in esito alla sottoscrizione del contratto integrativo in materia di produttivita\' ed efficienza dei servizi erogati, anche in relazione alle richieste dei cittadini.</p>\n', '', '63', '1'),
	(18, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575522, 1423212544, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33 - Art. 14.2, delib. CiVIT n. 12/2013', 'articolo 10', 'Comma 8 Lettera C', '', '<p>c) i nominativi ed i curricula dei componenti degli organismi indipendenti di valutazione di cui all\'articolo 14 del decreto legislativo n. 150 del 2009</p>\n', '', '53', '8'),
	(19, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575780, 1423212903, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33 - Art. 1, c. 16, lett. d), l. n. 190/2012 ', 'articolo 19', 'Comma 1,2', '', '<p><strong>Bandi di concorso</strong><br />\n1. Fermi restando gli altri obblighi di pubblicit&agrave; legale, le pubbliche amministrazioni pubblicano i bandi di concorso per il reclutamento, a qualsiasi titolo, di personale presso l\'amministrazione.<br />\n2. Le pubbliche amministrazioni pubblicano e tengono costantemente aggiornato l\'elenco dei bandi in corso, nonch&egrave; quello dei bandi espletati nel corso dell\'ultimo triennio, accompagnato dall\'indicazione, per ciascuno di essi, del numero dei dipendenti assunti e delle spese effettuate.</p>\n', '', '639', '10'),
	(20, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575944, 1423212618, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 22', 'Comma 1 Lettera A', '', '<p><strong>Obblighi di pubblicazione dei dati relativi agli enti pubblici vigilati, e agli enti di diritto privato in controllo pubblico, nonch&egrave; alle partecipazioni in societ&agrave; di diritto privato.</strong><br />\n1. Ciascuna amministrazione pubblica e aggiorna annualmente:<br />\na) l\'elenco degli enti pubblici, comunque denominati, istituiti, vigilati e finanziati dalla amministrazione medesima ovvero per i quali l\'amministrazione abbia il potere di nomina degli amministratori dell\'ente, con l\'elencazione delle funzioni attribuite e delle attivita\' svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate;</p>\n', '', '718', '1'),
	(21, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373575980, 1423212698, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 22', 'Comma 2,3', '', '<p>2. Per ciascuno degli enti di cui alle lettere da a) a c) del comma 1 sono pubblicati i dati relativi alla ragione sociale, alla misura della eventuale partecipazione dell\'amministrazione, alla durata dell\'impegno, all\'onere complessivo a qualsiasi titolo gravante per l\'anno sul bilancio dell\'amministrazione, al numero dei rappresentanti dell\'amministrazione negli organi di governo, al trattamento economico complessivo a ciascuno di essi spettante, ai risultati di bilancio degli ultimi tre esercizi finanziari. Sono altres&igrave; pubblicati i dati relativi agli incarichi di amministratore dell\'ente e il relativo trattamento economico complessivo.<br />\n3. Nel sito dell\'amministrazione &egrave; inserito il collegamento con i siti istituzionali degli enti di cui al comma 1, nei quali sono pubblicati i dati relativi ai componenti degli organi di indirizzo e ai soggetti titolari di incarico, in applicazione degli articoli 14 e 15.</p>\n', '', '719', '1,16'),
	(22, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576039, 1423150172, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 22', 'Comma 1 Lettera B', '', '<p>b) l\'elenco delle societ&agrave; di cui detiene direttamente quote di partecipazione anche minoritaria indicandone l\'entit&agrave;, con l\'indicazione delle funzioni attribuite e delle attivit&agrave; svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate;</p>\n', '', '64', '1'),
	(23, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576122, 1430311849, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 22', 'Comma 1 Lettera C', '', '<p>c) l\'elenco degli enti di diritto privato, comunque denominati, in controllo dell\'amministrazione, con l\'indicazione delle funzioni attribuite e delle attivit&agrave; svolte in favore dell\'amministrazione o delle attivit&agrave; di servizio pubblico affidate. Ai fini delle presenti disposizioni sono enti di diritto privato in controllo pubblico gli enti di diritto privato sottoposti a controllo da parte di amministrazioni pubbliche, oppure gli enti costituiti o vigilati da pubbliche amministrazioni nei quali siano a queste riconosciuti, anche in assenza di una partecipazione azionaria, poteri di nomina dei vertici o dei componenti degli organi;</p>\n', '', '719', '1'),
	(24, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576213, 1430311689, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 22', 'Comma 1 Lettera D', '', '<p>d) una o pi&ugrave; rappresentazioni grafiche che evidenziano i rapporti tra l\'amministrazione e gli enti di cui al precedente comma.</p>\n', '', '720', '1'),
	(25, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576434, 1430311706, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 24', 'Comma 1', '', '<p><strong>Obblighi di pubblicazione dei dati aggregati relativi all\'attivit&agrave;&nbsp;amministrativa<br /></strong></p>\n<p>Le pubbliche amministrazioni che organizzano, a fini conoscitivi e statistici, i dati relativi alla propria attivit&agrave; amministrativa, in forma aggregata, per settori di attivit&agrave;, per competenza degli organi e degli uffici, per tipologia di procedimenti, li pubblicano e li tengono costantemente aggiornati.</p>\n', '', '721', '1'),
	(26, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576493, 1423212513, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 35', 'Comma 1,2', '', '<p><strong>1. Le pubbliche amministrazioni pubblicano i dati relativi alle tipologie di procedimento di propria competenza. Per ciascuna tipologia di procedimento sono pubblicate le seguenti informazioni:</strong><br />\na) una breve descrizione del procedimento con indicazione di tutti i riferimenti normativi utili;<br />\nb) l\'unit&agrave; organizzativa responsabile dell\'istruttoria;<br />\nc) il nome del responsabile del procedimento, unitamente ai recapiti telefonici e alla casella di posta elettronica istituzionale, nonch&egrave;\', ove diverso, l\'ufficio competente all\'adozione del provvedimento finale, con l\'indicazione del nome del responsabile dell\'ufficio, unitamente ai rispettivi recapiti telefonici e alla casella di posta elettronica istituzionale;<br />\nd) per i procedimenti ad istanza di parte, gli atti e i documenti da allegare all\'istanza e la modulistica necessaria, compresi i fac-simile per le autocertificazioni, anche se la produzione a corredo dell\'istanza e\' prevista da norme di legge, regolamenti o atti pubblicati nella Gazzetta Ufficiale, nonche\' gli uffici ai quali rivolgersi per informazioni, gli orari e le modalit&agrave; di accesso con indicazione degli indirizzi, dei recapiti telefonici e delle caselle di posta elettronica istituzionale, a cui presentare le istanze;<br />\ne) le modalit&agrave; con le quali gli interessati possono ottenere le informazioni relative ai procedimenti in corso che li riguardino;<br />\nf) il termine fissato in sede di disciplina normativa del procedimento per la conclusione con l\'adozione di un provvedimento espresso e ogni altro termine procedimentale rilevante;<br />\ng) i procedimenti per i quali il provvedimento dell\'amministrazione pu&ograve; essere sostituito da una dichiarazione dell\'interessato, ovvero il procedimento puo\' concludersi con il silenzio assenso dell\'amministrazione;<br />\nh) gli strumenti di tutela, amministrativa e giurisdizionale, riconosciuti dalla legge in favore dell\'interessato, nel corso del procedimento e nei confronti del provvedimento finale ovvero nei casi di adozione del provvedimento oltre il termine predeterminato per la sua conclusione e i modi per attivarli;<br />\ni) il link di accesso al servizio on line, ove sia gia\' disponibile in rete, o i tempi previsti per la sua attivazione;<br />\nl) le modalita\' per l\'effettuazione dei pagamenti eventualmente necessari, con le informazioni di cui all\'articolo 36;<br />\nm) il nome del soggetto a cui &egrave; attribuito, in caso di inerzia, il potere sostitutivo, nonch&egrave; le modalit&agrave; per attivare tale potere, con indicazione dei recapiti telefonici e delle caselle di posta elettronica istituzionale;<br />\nn) i risultati delle indagini di customer satisfaction condotte sulla qualita\' dei servizi erogati attraverso diversi canali, facendone rilevare il relativo andamento.<br />\n<strong>2. Le pubbliche amministrazioni non possono richiedere l\'uso di moduli e formulari che non siano stati pubblicati</strong>; in caso di omessa pubblicazione, i relativi procedimenti possono essere avviati anche in assenza dei suddetti moduli o formulari. L\'amministrazione non pu&ograve; respingere l\'istanza adducendo il mancato utilizzo dei moduli o formulari o la mancata produzione di tali atti o documenti, e deve invitare l\'istante a integrare la documentazione in un termine congruo.</p>\n', '', '22', '5,2,3,14'),
	(27, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576543, 1430311835, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 24', 'Comma 2', '', '<p>Le amministrazioni pubblicano e rendono consultabili i risultati del monitoraggio periodico concernente il rispetto dei tempi procedimentali effettuato ai sensi dell\'articolo 1, comma 28, della&nbsp;legge 6 novembre 2012, n. 190.</p>\n', '', '722', '1'),
	(28, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576641, 1430311815, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 35', 'Comma 3', '', '<p><strong>Le pubbliche amministrazioni pubblicano nel sito istituzionale:</strong><br />\na) i recapiti telefonici e la casella di posta elettronica istituzionale dell\'ufficio responsabile per le attivit&agrave; volte a gestire, garantire e verificare la trasmissione dei dati o l\'accesso diretto agli stessi da parte delle amministrazioni procedenti ai sensi degli articoli 43, 71 e 72 del decreto del Presidente della<br />\nRepubblica 28 dicembre 2000, n. 445;<br />\nb) le convenzioni-quadro volte a disciplinare le modalit&agrave; di accesso ai dati di cui all\'articolo 58 del codice dell\'amministrazione digitale, di cui al decreto legislativo 7 marzo 2005, n. 82;<br />\nc) le ulteriori modalit&agrave; per la tempestiva acquisizione d\'ufficio dei dati nonch&egrave; per lo svolgimento dei controlli sulle dichiarazioni sostitutive da parte delle amministrazioni procedenti</p>\n', '', '723', '1,14'),
	(29, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576798, 1423212490, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 23', 'Comma 1,2', '', '<p><strong>Obblighi di pubblicazione concernenti i provvedimenti amministrativi</strong><br />\n1. Le pubbliche amministrazioni pubblicano e aggiornano ogni sei mesi, in distinte partizioni della sezione &laquo;Amministrazione trasparente&raquo;, gli elenchi dei provvedimenti adottati dagli organi di indirizzo politico e dai dirigenti, con particolare riferimento ai provvedimenti finali dei procedimenti di:<br />\na) autorizzazione o concessione;<br />\nb) scelta del contraente per l\'affidamento di lavori, forniture e servizi, anche con riferimento alla modalit&agrave;\' di selezione prescelta ai sensi del codice dei contratti pubblici, relativi a lavori,servizi e forniture, di cui al decreto legislativo 12 aprile 2006, n. 163;<br />\nc) concorsi e prove selettive per l\'assunzione del personale e progressioni di carriera di cui all\'articolo 24 del decreto legislativo n. 150 del 2009;<br />\nd) accordi stipulati dall\'amministrazione con soggetti privati o con altre amministrazioni pubbliche.<br />\n2. Per ciascuno dei provvedimenti compresi negli elenchi di cui al comma 1 sono pubblicati il contenuto, l\'oggetto, la eventuale spesa prevista e gli estremi relativi ai principali documenti contenuti nel&nbsp;fascicolo relativo al procedimento. La pubblicazione avviene nella forma di una scheda sintetica, prodotta automaticamente in sede di formazione del documento che contiene l\'atto.</p>\n', '', '725,726', '4'),
	(30, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576834, 1375451419, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', '25', '', '', '<p><strong>Obblighi di pubblicazione concernenti i controlli sulle imprese</strong><br />\n1. Le pubbliche amministrazioni, in modo dettagliato e facilmente comprensibile, pubblicano sul proprio sito istituzionale e sul sito: www.impresainungiorno.gov.it:<br />\na) l\'elenco delle tipologie di controllo a cui sono assoggettate le imprese in ragione della dimensione e del settore di attivit&agrave;, indicando per ciascuna di esse i criteri e le relative modalit&agrave; di svolgimento;<br />\nb) l\'elenco degli obblighi e degli adempimenti oggetto delle attivit&agrave; di controllo che le imprese sono tenute a rispettare per ottemperare alle disposizioni normative.</p>\n', '', '727', '1'),
	(31, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373576879, 1423153962, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 37', 'Comma 1,2', '', '<p>Obblighi di pubblicazione concernenti i contratti pubblici di lavori, servizi e forniture<br />\n1. Fermi restando gli altri obblighi di pubblicit&agrave; legale e, in particolare, quelli previsti dall\'articolo 1, comma 32, della legge 6 novembre 2012, n. 190, ciascuna amministrazione pubblica, secondo quanto previsto dal decreto legislativo 12 aprile 2006, n. 163, e, in particolare, dagli articoli 63, 65, 66, 122, 124, 206 e 223, le informazioni relative alle procedure per l\'affidamento e l\'esecuzione di opere e lavori pubblici, servizi e forniture.<br />\n2. Le pubbliche amministrazioni sono tenute altres&igrave; a pubblicare, nell\'ipotesi di cui all\'articolo 57, comma 6, del decreto legislativo 12 aprile 2006, n. 163, la delibera a contrarre.</p>\n', '', '566', '9'),
	(32, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577129, 1423212568, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 27', '', '', '<p>1. La pubblicazione di cui all\'articolo 26, comma 2, comprende necessariamente, ai fini del comma 3 del medesimo articolo:<br />\na) il nome dell\'impresa o dell\'ente e i rispettivi dati fiscali o il nome di altro soggetto beneficiario;<br />\nb) l\'importo del vantaggio economico corrisposto;<br />\nc) la norma o il titolo a base dell\'attribuzione;<br />\nd) l\'ufficio e il funzionario o dirigente responsabile del relativo procedimento amministrativo;<br />\ne) la modalit&agrave; seguita per l\'individuazione del beneficiario;<br />\nf) il link al progetto selezionato e al curriculum del soggetto incaricato.<br />\n2. Le informazioni di cui al comma 1 sono riportate, nell\'ambito della sezione &laquo;Amministrazione trasparente&raquo; e secondo modalit&agrave; di facile consultazione, in formato tabellare aperto che ne consente l\'esportazione, il trattamento e il riutilizzo ai sensi dell\'articolo 7 e devono essere organizzate annualmente in unico elenco per singola amministrazione.</p>\n', '', '728,48', '11'),
	(33, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577161, 1423212597, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 26', 'Comma 2', '', '<p>2. Le pubbliche amministrazioni pubblicano gli atti di concessione delle sovvenzioni, contributi, sussidi ed ausili finanziari alle imprese, e comunque di vantaggi economici di qualunque genere a persone ed enti pubblici e privati ai sensi del citato articolo 12 della legge n. 241 del 1990, di importo superiore a mille euro.</p>\n', '', '48,729,728', '11'),
	(34, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577206, 1423212865, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 26', 'Comma 1', '', '<p>1. Le pubbliche amministrazioni pubblicano gli atti con i quali sono determinati, ai sensi dell\'articolo 12 della legge 7 agosto 1990, n. 241, i criteri e le modalit&agrave; cui le amministrazioni stesse devono attenersi per la concessione di sovvenzioni, contributi, sussidi ed ausili finanziari e per l\'attribuzione di vantaggi economici di qualunque genere a persone ed enti pubblici e privati.</p>\n', '', '729,48,728', '11'),
	(35, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577263, 1423150112, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 29', 'Comma 1', '', '<p><strong>Obblighi di pubblicazione del bilancio, preventivo e consuntivo, e&nbsp;del Piano degli indicatori e risultati attesi di bilancio, nonch&egrave;&nbsp;dei dati concernenti il monitoraggio degli obiettivi<br /></strong></p>\n<p>1. Le pubbliche amministrazioni pubblicano i dati relativi al bilancio di previsione e a quello consuntivo di ciascun anno in forma sintetica, aggregata e semplificata, anche con il ricorso a rappresentazioni grafiche, al fine di assicurare la piena accessibilit&agrave; e comprensibilit&agrave;.</p>\n', '', '731', '7'),
	(36, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577325, 1423154973, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 29', 'Comma 2', '', '<p>2. Le pubbliche amministrazioni pubblicano il Piano di cui all\'articolo 19 del decreto legislativo 31 maggio 2011, n. 91, con le integrazioni e gli aggiornamenti di cui all\'articolo 22 del medesimo decreto legislativo n. 91 del 2011.</p>\n', '', '732', '7'),
	(37, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577374, 1423212930, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 30', '', '', '<p>Obblighi di pubblicazione concernenti i beni immobili e la gestione del patrimonio.<br />\n1. Le pubbliche amministrazioni pubblicano le informazioni identificative degli immobili posseduti, nonche\' i canoni di locazione o di affitto versati o percepiti.</p>\n', '', '734,735', '1'),
	(38, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577458, 1375449278, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', '31', 'Comma 1', '', '<p><strong>Obblighi di pubblicazione concernenti i dati relativi ai controlli sull\'organizzazione e sull\'attivit&agrave; dell\'amministrazione.</strong><br />\n1. Le pubbliche amministrazioni pubblicano, unitamente agli atti cui si riferiscono, i rilievi non recepiti degli organi di controllo interno, degli organi di revisione amministrativa e contabile e tutti i rilievi ancorch&egrave;\' recepiti della Corte dei conti, riguardanti l\'organizzazione e l\'attivit&agrave; dell\'amministrazione o di singoli uffici.</p>\n', '', '736', '1'),
	(39, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577521, 1430311765, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 33', '', '', '<p><strong>Obblighi di pubblicazione concernenti i tempi di pagamento dell\'amministrazione</strong><br />\n1. Le pubbliche amministrazioni pubblicano, con cadenza annuale, un indicatore dei propri tempi medi di pagamento relativi agli acquisti di beni, servizi e forniture, denominato: &laquo;indicatore di tempestivita\' dei pagamenti&raquo;.</p>\n', '', '739', '1'),
	(40, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577547, 1423212846, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 36', '', '', '<p><strong>Pubblicazione delle informazioni necessarie per l\'effettuazione di pagamenti informatici</strong></p>\n<p>1. Le pubbliche amministrazioni pubblicano e specificano nelle richieste di pagamento i dati e le informazioni di cui all\'articolo 5 del decreto legislativo 7 marzo 2005, n. 82.</p>\n', '', '740', '1'),
	(41, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577571, 1423217250, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33 - DLgs  12 aprile 2006, n. 163', 'articolo 38', '', '', '<p><strong>Pubblicit&agrave; dei processi di pianificazione, realizzazione e valutazione delle opere pubbliche</strong></p>\n<p><br />\n1. Le pubbliche amministrazioni pubblicano tempestivamente sui propri siti istituzionali: i documenti di programmazione anche pluriennale delle opere pubbliche di competenza dell\'amministrazione,&nbsp;le linee guida per la valutazione degli investimenti; le relazioni annuali; ogni altro documento predisposto nell\'ambito della valutazione, ivi inclusi i pareri dei valutatori che si discostino&nbsp;dalle scelte delle amministrazioni e gli esiti delle valutazioni ex post che si discostino dalle valutazioni ex ante; le informazioni relative ai Nuclei di valutazione e verifica degli investimenti pubblici di cui all\'articolo 1 della legge 17 maggio 1999, n. 144, incluse le funzioni e i compiti specifici ad essi attribuiti, le procedure e i criteri di individuazione dei componenti e i loro nominativi.<br />\n2. Le pubbliche amministrazioni pubblicano, fermi restando gli obblighi di pubblicazione di cui all\'articolo 128 del decreto legislativo 12 aprile 2006, n. 163, le informazioni relative ai tempi, ai costi unitari e agli indicatori di realizzazione delle opere pubbliche completate. Le informazioni sui costi sono pubblicate sulla base di uno schema tipo redatto dall\'Autorit&agrave; per la vigilanza sui contratti pubblici di lavori, servizi e forniture, che ne cura altres&igrave; la raccolta e la pubblicazione nel proprio sito web istituzionale al fine di consentirne una agevole comparazione.</p>\n', '', '741', '1'),
	(42, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577601, 1375284440, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', '39', '', '', '<p><strong>Trasparenza dell\'attivit&agrave; di pianificazione e governo del territorio</strong></p>\n<p><br />\n1. Le pubbliche amministrazioni pubblicano:<br />\na) gli atti di governo del territorio, quali, tra gli altri, piani territoriali, piani di coordinamento, piani paesistici, strumenti urbanistici, generali e di attuazione, nonch&egrave; le loro varianti;<br />\nb) per ciascuno degli atti di cui alla lettera a) sono pubblicati, tempestivamente, gli schemi di provvedimento prima che siano portati all\'approvazione; le delibere di adozione o approvazione; i relativi allegati tecnici.<br />\n2. La documentazione relativa a ciascun procedimento di presentazione e approvazione delle proposte di trasformazione urbanistica d\'iniziativa privata o pubblica in variante allo strumento urbanistico generale comunque denominato vigente nonch&egrave; delle proposte di trasformazione urbanistica d\'iniziativa privata o pubblica in attuazione dello strumento urbanistico generale vigente che comportino premialit&agrave; edificatorie a fronte dell\'impegno dei privati alla realizzazione di opere di urbanizzazione extra oneri o della cessione di aree o volumetrie per finalit&agrave; di pubblico interesse &egrave; pubblicata in una sezione apposita nel sito del comune interessato, continuamente aggiornata.<br />\n3. La pubblicit&agrave; degli atti di cui al comma 1, lettera a), &egrave;\' condizione per l\'acquisizione dell\'efficacia degli atti stessi.<br />\n4. Restano ferme le discipline di dettaglio previste dalla vigente legislazione statale e regionale.</p>\n', '', '742', '1'),
	(43, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577628, 1375284523, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', '40', '', '', '<p><strong>Pubblicazione e accesso alle informazioni ambientali<br /></strong></p>\n<p>1. In materia di informazioni ambientali restano ferme le disposizioni di maggior tutela gi&agrave; previste dall\'articolo 3-sexies del decreto legislativo 3 aprile 2006 n. 152, dalla legge 16 marzo 2001, n. 108, nonch&egrave;\' dal decreto legislativo 19 agosto 2005 n. 195.<br />\n2. Le amministrazioni di cui all\'articolo 2, comma 1, lettera b), del decreto legislativo n. 195 del 2005, pubblicano, sui propri siti istituzionali e in conformit&agrave; a quanto previsto dal presente decreto, le informazioni ambientali di cui all\'articolo 2, comma 1, lettera a), del decreto legislativo 19 agosto 2005, n. 195, che detengono ai fini delle proprie attivit&agrave; istituzionali, nonch&egrave;\' le relazioni di cui all\'articolo 10 del medesimo decreto legislativo. Di tali informazioni deve essere dato specifico rilievo all\'interno di un\'apposita sezione detta &laquo;Informazioni ambientali&raquo;.<br />\n3. Sono fatti salvi i casi di esclusione del diritto di accesso alle informazioni ambientali di cui all\'articolo 5 del decreto legislativo 19 agosto 2005, n. 195.<br />\n4. L\'attuazione degli obblighi di cui al presente articolo non &egrave;\' in alcun caso subordinata alla stipulazione degli accordi di cui all\'articolo 11 del decreto legislativo 19 agosto 2005, n. 195. Sono&nbsp;fatti salvi gli effetti degli accordi eventualmente gia\' stipulati, qualora assicurino livelli di informazione ambientale superiori a quelli garantiti dalle disposizioni del presente decreto. Resta fermo il potere di stipulare ulteriori accordi ai sensi del medesimo articolo 11, nel rispetto dei livelli di informazione ambientale garantiti dalle disposizioni del presente decreto.</p>\n', '', '743', '1'),
	(44, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577659, 1375369540, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', '41', 'Comma 4', '2', '<p>E\' pubblicato e annualmente aggiornato l\'elenco delle strutture&nbsp;sanitarie private accreditate. Sono altres&igrave; pubblicati gli accordi&nbsp;con esse intercorsi.</p>\n', '<p>Da pubblicare in formato tabellare</p>\n', '744', '1'),
	(45, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1373577690, 1430311911, 0, 0, 0, 'istanza', 'Dlgs 14 marzo 2013, n. 33', 'articolo 42', '', '', '<p><strong>Obblighi di pubblicazione concernenti gli interventi straordinari e di emergenza che comportano deroghe alla legislazione vigente.</strong></p>\n<p><br />\n1. Le pubbliche amministrazioni che adottano provvedimenti contingibili e urgenti e in generale provvedimenti di carattere straordinario in caso di calamita\' naturali o di altre emergenze, ivi<br />\ncomprese le amministrazioni commissariali e straordinarie costituite in base alla legge 24 febbraio 1992, n. 225, o a provvedimenti legislativi di urgenza, pubblicano:<br />\na) i provvedimenti adottati, con la indicazione espressa delle norme di legge eventualmente derogate e dei motivi della deroga, nonche\' l\'indicazione di eventuali atti amministrativi o giurisdizionali intervenuti;<br />\nb) i termini temporali eventualmente fissati per l\'esercizio dei poteri di adozione dei provvedimenti straordinari;<br />\nc) il costo previsto degli interventi e il costo effettivo sostenuto dall\'amministrazione;<br />\nd) le particolari forme di partecipazione degli interessati ai procedimenti di adozione dei provvedimenti straordinari.</p>\n', '', '745', '1'),
	(47, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375968952, 1423126426, 0, 0, 0, 'istanza', 'DLgs 14 marzo 2013, n. 33', 'articolo 14', 'Comma 1,2', '', '<p><strong>Obblighi di pubblicazione concernenti i componenti degli organi di&nbsp;indirizzo politico</strong></p>\n<p>1. Con riferimento ai titolari di incarichi politici, di carattere&nbsp;elettivo o comunque di esercizio di poteri di indirizzo politico, di&nbsp;livello statale regionale e locale, le pubbliche amministrazioni&nbsp;pubblicano con riferimento a tutti i propri componenti, i seguenti&nbsp;documenti ed informazioni:<br />\na) l\'atto di nomina o di proclamazione, con l\'indicazione della&nbsp;durata dell\'incarico o del mandato elettivo;<br />\nb) il curriculum;<br />\nc) i compensi di qualsiasi natura connessi all\'assunzione della&nbsp;carica; gli importi di viaggi di servizio e missioni pagati con fondi&nbsp;pubblici;<br />\nd) i dati relativi all\'assunzione di altre cariche, presso enti&nbsp;pubblici o privati, ed i relativi compensi a qualsiasi titolo&nbsp;corrisposti;<br />\ne) gli altri eventuali incarichi con oneri a carico della finanza&nbsp;pubblica e l\'indicazione dei compensi spettanti;<br />\nf) le dichiarazioni di cui all\'articolo 2, della legge 5 luglio&nbsp;1982, n. 441, nonche\' le attestazioni e dichiarazioni di cui agli&nbsp;articoli 3 e 4 della medesima legge, come modificata dal presente&nbsp;decreto, limitatamente al soggetto, al coniuge non separato e ai&nbsp;parenti entro il secondo grado, ove gli stessi vi consentano. Viene&nbsp;in ogni caso data evidenza al mancato consenso. Alle informazioni di&nbsp;cui alla presente lettera concernenti soggetti diversi dal titolare&nbsp;dell\'organo di indirizzo politico non si applicano le disposizioni di&nbsp;cui all\'articolo 7.<br />\n2. Le pubbliche amministrazioni pubblicano i dati cui al comma 1&nbsp;entro tre mesi dalla elezione o dalla nomina e per i tre anni&nbsp;successivi dalla cessazione del mandato o dell\'incarico dei soggetti,&nbsp;salve le informazioni concernenti la situazione patrimoniale e, ove&nbsp;consentita, la dichiarazione del coniuge non separato e dei parenti&nbsp;entro il secondo grado, che vengono pubblicate fino alla cessazione&nbsp;dell\'incarico o del mandato. Decorso il termine di pubblicazione ai sensi del presente comma le informazioni e i dati concernenti la situazione patrimoniale non vengono trasferiti nelle sezioni di archivio</p>\n<p><strong>La Delibera CIVIT n. 65/2013&nbsp;in "Applicazione dell&rsquo;art. 14, comma 1, lettera f), del d.lgs n. 33/2013 ai Comuni" recita: "sono soggetti agli obblighi di pubblicazione relativamente alla situazione reddituale e patrimoniale dei titolari di cariche elettive i comuni con popolazione superiore ai 15.000 abitanti"<br /></strong></p>\n', '', '701', '2'),
	(49, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1423048076, 1423062997, 0, 0, 0, 'istanza', 'Delibera Civit n. 50/2013', 'Allegato 1 Delibera Civit n. 50/2013 ', '', '8', '<div>Occorre pubblicare i seguenti contenuti: Piano triennale di prevenzione della corruzione, Responsabile della prevenzione della corruzione, Responsabile della trasparenza, Regolamenti per la prevenzione e la repressione della corruzione e dell\'illegalit&agrave;, Relazione del responsabile della corruzione, Atti di adeguamento a provvedimenti CiVIT (ora ANAC), Atti di accertamento delle violazioni&nbsp;</div>\n', '', '769', ''),
	(50, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1423155175, 1423652183, 0, 0, 0, 'istanza', 'Dlgs 33/2013, Delibera Civit n. 50/2013', 'articolo 5 - Allegato 1 Delibera Civit n. 50/2013 ', '', '', '<p>Occorre Pubblicare: nome del Responsabile della trasparenza cui &egrave; presentata la richiesta di accesso civico, nonch&egrave; modalit&agrave; per l\'esercizio di tale diritto, con indicazione dei recapiti telefonici e delle caselle di posta elettronica istituzionale;&nbsp;nome del titolare del potere sostitutivo, attivabile nei casi di ritardo o mancata risposta, con indicazione dei recapiti telefonici e delle caselle di posta elettronica istituzionale.</p>\n', '', '770', '');
/*!40000 ALTER TABLE `oggetto_etrasp_norma` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_norma_backup: 0 rows
DELETE FROM `oggetto_etrasp_norma_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_norma_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_norma_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_norma_workflow: 0 rows
DELETE FROM `oggetto_etrasp_norma_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_norma_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_norma_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_notifiche: 0 rows
DELETE FROM `oggetto_etrasp_notifiche`;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_notifiche_backup: 0 rows
DELETE FROM `oggetto_etrasp_notifiche_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_notifiche_workflow: 0 rows
DELETE FROM `oggetto_etrasp_notifiche_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_notifiche_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipocontenuti: 16 rows
DELETE FROM `oggetto_etrasp_tipocontenuti`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_tipocontenuti` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `nome`, `nome_breve`, `id_oggetto`, `descrizione`) VALUES
	(1, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501846, 1375275287, 0, 0, 0, 'istanza', 'Contenuto editabile', 'contenuti', '0', ''),
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501859, 1375273617, 0, 0, 0, 'istanza', 'Personale', 'personale', '3', ''),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501869, 1375273772, 0, 0, 0, 'istanza', 'Procedimenti', 'procedimenti', '16', ''),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501884, 1375273756, 0, 0, 0, 'istanza', 'Provvedimenti politici e dirigenziali', 'provvedimenti', '28', ''),
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501894, 1375273851, 0, 0, 0, 'istanza', 'Modulistica', 'modulistica', '5', ''),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501911, 1375273662, 0, 0, 0, 'istanza', 'Regolamenti e documentazione', 'regolamenti', '19', ''),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501950, 1375273916, 0, 0, 0, 'istanza', 'Bilanci', 'bilanci', '29', ''),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501965, 1375273895, 0, 0, 0, 'istanza', 'Incarichi e consulenze', 'incarichi', '4', ''),
	(9, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501979, 1375273935, 0, 0, 0, 'istanza', 'Bandi di gara', 'bandigara', '11', ''),
	(10, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374501989, 1375273822, 0, 0, 0, 'istanza', 'Bandi di concorso', 'bandiconcorso', '21', ''),
	(11, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374502011, 1375273563, 0, 0, 0, 'istanza', 'Sovvenzioni e vantaggi economici', 'sovvenzioni', '38', ''),
	(12, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1374502020, 1375273693, 0, 0, 0, 'istanza', 'Normativa', 'normativa', '27', ''),
	(13, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375273993, 1375273993, 0, 0, 0, 'istanza', 'Oneri informativi per cittadini ed imprese', 'oneri', '30', ''),
	(14, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375274064, 1375274064, 0, 0, 0, 'istanza', 'Strutture organizzative', 'strutture', '13', ''),
	(15, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1375275275, 1375275275, 0, 0, 0, 'istanza', 'Contenuti speciali', 'speciali', '0', ''),
	(16, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 1399647233, 1399647233, 0, 0, 0, 'istanza', 'Enti controllati - Societ&agrave;', 'societa', '37', '<p>Test</p>\n');
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipocontenuti_backup: 0 rows
DELETE FROM `oggetto_etrasp_tipocontenuti_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipocontenuti_workflow: 0 rows
DELETE FROM `oggetto_etrasp_tipocontenuti_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipocontenuti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoenti: 8 rows
DELETE FROM `oggetto_etrasp_tipoenti`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_tipoenti` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `nome`, `descrizione`) VALUES
	(1, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Tutte le Amministrazioni di cui all\'articolo 1, comma 2, del DLgs 30 marzo 2001, n.165 e s.m.i.', ''),
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Regioni', ''),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Regioni, Province autonome e Province', ''),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comuni', ''),
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Amministrazioni dello Stato', ''),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Aziende sanitarie ed ospedaliere', ''),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Enti, aziende e strutture pubbliche e private che erogano prestazioni per conto del servizio sanitario', ''),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Pubbliche amministrazioni art. 1,c. 2, DLgs 30 marzo 2001,n.165, ivi compresi gli enti pubblici, nonch&eacute; gli enti di diritto privato in controllo pubblico', '');
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoentisemplice: 17 rows
DELETE FROM `oggetto_etrasp_tipoentisemplice`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice` DISABLE KEYS */;
INSERT INTO `oggetto_etrasp_tipoentisemplice` (`id`, `stato`, `stato_workflow`, `id_proprietario`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `data_creazione`, `ultima_modifica`, `id_sezione`, `id_lingua`, `numero_letture`, `template`, `nome_tipo`, `tipo_ente`, `sezioni_esclusione`, `org_commissario`, `org_sub_commissario`, `org_sindaco`, `org_vicesindaco`, `org_giunta`, `org_presidente`, `org_consiglio`, `org_direzione`, `org_segretario`, `org_commissioni`, `org_ass_sindaci`) VALUES
	(2, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Istituti e scuole di ogni ordine e grado, istituzioni  educative', '1', '809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(3, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Aziende ed amministrazioni dello Stato ad ordinamento autonomo', '1', '809,810,710,793', '', '', '', '', 'Comitato di Indirizzo', '', 'Collegio dei revisori dei conti', 'Direzione Generale', '', '', ''),
	(4, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Regioni', '1', '809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(5, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Province', '1', '703,810', '', '', 'Presidente della Provincia', 'Vice Presidente', 'Giunta', '', 'Consiglio Provinciale', 'Direttore Generale', 'Segretario Generale', '', 'Assemblea dei Sindaci'),
	(6, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comuni (default)', '1', '800,809,810', '', '', 'Sindaco', 'Vicesindaco', 'Giunta ed assessori', 'Presidente Consiglio Comunale', 'Consiglio Comunale', 'Direzione Generale', 'Segretario Generale', 'Commissioni', ''),
	(7, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comunit&agrave; montane', '1', '809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(8, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Istituzioni universitarie', '1', '710,744,793,809,810', '', '', 'Rettore', '', 'Senato', '', 'Consiglio di Amministrazione', '', 'Direttore generale', 'Commissioni e OIV', ''),
	(9, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Istituti autonomi case popolari - IACP', '1', '809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(10, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Camere di commercio, industria, artigianato e agricoltura e loro associazioni', '1', '', '', '', 'Presidente', '', 'Giunta', '', 'Consiglio', 'Collegio dei revisori dei Conti', '', 'Commissioni e OIV', ''),
	(11, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Enti  pubblici non economici nazionali, regionali e locali', '1', '809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(13, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Amministrazioni, aziende ed enti del Servizio sanitario nazionale (senza gare)', '1', '787,636,788,790,789,809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(14, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Enti strumentali della Regione', '8', '809,810', '', '', 'Presidente', 'Vice Presidente', '', 'Consiglio di Amministrazione', 'Presidente del Collegio Revisore dei Conti', 'Componente del Collegio Revisore dei Conti', 'Presidente Organo di Valutazione', 'Componente Organo di Valutazione', ''),
	(15, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comuni (eliminata - Direzione Generale)', '1', '810', '', '', 'Sindaco', 'Vicesindaco', 'Giunta ed assessori', 'Presidente Consiglio Comunale', 'Consiglio Comunale', '', 'Segretario Generale', 'Commissioni', ''),
	(16, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comuni (con commissario)', '1', '809', 'Commissario Prefettizio', '', 'Sindaco', 'Vicesindaco', 'Giunta ed assessori', 'Presidente Consiglio Comunale', 'Consiglio Comunale', 'Direzione Generale', 'Segretario Generale', 'Commissioni', ''),
	(17, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Amministrazioni, aziende ed enti del Servizio sanitario nazionale', '1', '793,809,810', '', '', '', '', '', '', '', '', '', '', ''),
	(18, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Comuni (definitivo con commissario e subcommisario)', '1', '809,793,702,792,703,704,705,706', 'Commissario', 'Sub Commissario', 'Sindaco', 'Vicesindaco', 'Giunta ed assessori', 'Presidente del Consiglio Comunale', 'Consiglio Comunale', 'Direzione Generale', 'Segretario Generale', '', ''),
	(19, 1, 'finale', 0, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, 0, 0, 'istanza', 'Consorzi Acquedottistici', '1', '809,810', '', '', 'Amministratore Delegato', 'Presidente del Consiglio di Gestione', 'Consiglio di Sorveglianza', 'Presidente del Consiglio di Sorveglianza', 'Consiglio di Gestione', '', 'Revisore Unico', '', '');
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoentisemplice_backup: 0 rows
DELETE FROM `oggetto_etrasp_tipoentisemplice_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoentisemplice_workflow: 0 rows
DELETE FROM `oggetto_etrasp_tipoentisemplice_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoentisemplice_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoenti_backup: 0 rows
DELETE FROM `oggetto_etrasp_tipoenti_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_tipoenti_workflow: 0 rows
DELETE FROM `oggetto_etrasp_tipoenti_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_tipoenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_workflow: 0 rows
DELETE FROM `oggetto_etrasp_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_workflow_backup: 0 rows
DELETE FROM `oggetto_etrasp_workflow_backup`;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_etrasp_workflow_workflow: 0 rows
DELETE FROM `oggetto_etrasp_workflow_workflow`;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_etrasp_workflow_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_eventi: 0 rows
DELETE FROM `oggetto_eventi`;
/*!40000 ALTER TABLE `oggetto_eventi` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_eventi` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_eventi_backup: 0 rows
DELETE FROM `oggetto_eventi_backup`;
/*!40000 ALTER TABLE `oggetto_eventi_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_eventi_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_eventi_workflow: 0 rows
DELETE FROM `oggetto_eventi_workflow`;
/*!40000 ALTER TABLE `oggetto_eventi_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_eventi_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_faq: 0 rows
DELETE FROM `oggetto_faq`;
/*!40000 ALTER TABLE `oggetto_faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_faq` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_faq_backup: 0 rows
DELETE FROM `oggetto_faq_backup`;
/*!40000 ALTER TABLE `oggetto_faq_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_faq_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_faq_workflow: 0 rows
DELETE FROM `oggetto_faq_workflow`;
/*!40000 ALTER TABLE `oggetto_faq_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_faq_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_gare_atti: 0 rows
DELETE FROM `oggetto_gare_atti`;
/*!40000 ALTER TABLE `oggetto_gare_atti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_gare_atti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_gare_atti_backup: 0 rows
DELETE FROM `oggetto_gare_atti_backup`;
/*!40000 ALTER TABLE `oggetto_gare_atti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_gare_atti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_gare_atti_workflow: 0 rows
DELETE FROM `oggetto_gare_atti_workflow`;
/*!40000 ALTER TABLE `oggetto_gare_atti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_gare_atti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini: 0 rows
DELETE FROM `oggetto_immagini`;
/*!40000 ALTER TABLE `oggetto_immagini` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini_backup: 0 rows
DELETE FROM `oggetto_immagini_backup`;
/*!40000 ALTER TABLE `oggetto_immagini_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini_testata: 0 rows
DELETE FROM `oggetto_immagini_testata`;
/*!40000 ALTER TABLE `oggetto_immagini_testata` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini_testata` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini_testata_backup: 0 rows
DELETE FROM `oggetto_immagini_testata_backup`;
/*!40000 ALTER TABLE `oggetto_immagini_testata_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini_testata_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini_testata_workflow: 0 rows
DELETE FROM `oggetto_immagini_testata_workflow`;
/*!40000 ALTER TABLE `oggetto_immagini_testata_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini_testata_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_immagini_workflow: 0 rows
DELETE FROM `oggetto_immagini_workflow`;
/*!40000 ALTER TABLE `oggetto_immagini_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_immagini_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_incarichi: 0 rows
DELETE FROM `oggetto_incarichi`;
/*!40000 ALTER TABLE `oggetto_incarichi` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_incarichi` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_incarichi_backup: 0 rows
DELETE FROM `oggetto_incarichi_backup`;
/*!40000 ALTER TABLE `oggetto_incarichi_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_incarichi_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_incarichi_workflow: 0 rows
DELETE FROM `oggetto_incarichi_workflow`;
/*!40000 ALTER TABLE `oggetto_incarichi_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_incarichi_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_link: 0 rows
DELETE FROM `oggetto_link`;
/*!40000 ALTER TABLE `oggetto_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_link` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_link_backup: 0 rows
DELETE FROM `oggetto_link_backup`;
/*!40000 ALTER TABLE `oggetto_link_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_link_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_link_workflow: 0 rows
DELETE FROM `oggetto_link_workflow`;
/*!40000 ALTER TABLE `oggetto_link_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_link_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_menu_banner: 0 rows
DELETE FROM `oggetto_menu_banner`;
/*!40000 ALTER TABLE `oggetto_menu_banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_menu_banner` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_menu_banner_backup: 0 rows
DELETE FROM `oggetto_menu_banner_backup`;
/*!40000 ALTER TABLE `oggetto_menu_banner_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_menu_banner_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_menu_banner_workflow: 0 rows
DELETE FROM `oggetto_menu_banner_workflow`;
/*!40000 ALTER TABLE `oggetto_menu_banner_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_menu_banner_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_modulistica_regolamenti: 0 rows
DELETE FROM `oggetto_modulistica_regolamenti`;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_modulistica_regolamenti_backup: 0 rows
DELETE FROM `oggetto_modulistica_regolamenti_backup`;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_modulistica_regolamenti_workflow: 0 rows
DELETE FROM `oggetto_modulistica_regolamenti_workflow`;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_modulistica_regolamenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_normativa: 0 rows
DELETE FROM `oggetto_normativa`;
/*!40000 ALTER TABLE `oggetto_normativa` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_normativa` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_normativa_backup: 0 rows
DELETE FROM `oggetto_normativa_backup`;
/*!40000 ALTER TABLE `oggetto_normativa_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_normativa_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_normativa_workflow: 0 rows
DELETE FROM `oggetto_normativa_workflow`;
/*!40000 ALTER TABLE `oggetto_normativa_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_normativa_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_notizie: 0 rows
DELETE FROM `oggetto_notizie`;
/*!40000 ALTER TABLE `oggetto_notizie` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_notizie` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_notizie_backup: 0 rows
DELETE FROM `oggetto_notizie_backup`;
/*!40000 ALTER TABLE `oggetto_notizie_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_notizie_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_notizie_workflow: 0 rows
DELETE FROM `oggetto_notizie_workflow`;
/*!40000 ALTER TABLE `oggetto_notizie_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_notizie_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_oneri: 0 rows
DELETE FROM `oggetto_oneri`;
/*!40000 ALTER TABLE `oggetto_oneri` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_oneri` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_oneri_backup: 0 rows
DELETE FROM `oggetto_oneri_backup`;
/*!40000 ALTER TABLE `oggetto_oneri_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_oneri_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_oneri_workflow: 0 rows
DELETE FROM `oggetto_oneri_workflow`;
/*!40000 ALTER TABLE `oggetto_oneri_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_oneri_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_paragrafo: 7 rows
DELETE FROM `oggetto_paragrafo`;
/*!40000 ALTER TABLE `oggetto_paragrafo` DISABLE KEYS */;
INSERT INTO `oggetto_paragrafo` (`id`, `stato`, `id_proprietario`, `id_lingua`, `data_creazione`, `ultima_modifica`, `nome`, `contenuto`) VALUES
	(786, 1, 0, 0, 0, 0, 'Contenuto di sezione', ''),
	(787, 1, 0, 0, 0, 0, 'Contenuto di sezione', ''),
	(788, 1, 0, 0, 0, 0, 'Contenuto di sezione', ''),
	(789, 1, 0, 0, 0, 0, '', ''),
	(785, 1, 0, 0, 0, 0, 'Benvenuti nel portale Trasparenza', '<div class=\\"sub_paragrafo18\\">In questo portale saranno pubblicati, raggruppati secondo le indicazioni di legge, documenti, informazioni e dati concernenti l\\\'organizzazione dell\\\'amministrazione, le sue attivit&agrave; e le relative modalit&agrave; di realizzazione. ( Decreto Legislativo 14 marzo 2013, n.33 - Riordino della disciplina riguardante gli obblighi di pubblicit&agrave;, trasparenza e diffusione di informazioni da parte delle pubbliche amministrazioni - pubblicato in Gazzetta Ufficiale n. 80 in data 05/04/2013 - in vigore dal 20/04/2013).</div>'),
	(790, 1, 0, 0, 0, 0, '', '<p>Questa informativa &egrave; fornita, ai sensi dell\\\'art. 13 del d.lgs. n. 196/2003 (Codice in materia di protezione dei dati personali).<br />\r\n<br />\r\nIl trattamento dei dati, che dovessero pervenire via posta elettronica o moduli elettronici di registrazione, &egrave; conforme a quanto previsto dalla normativa sulla privacy ai sensi del Decreto legislativo 2003, n. 196 &ldquo;Codice in materia di protezione dei dati personali e alla Raccomandazione 17 maggio 2001, n. 2 per la raccolta di dati on-line nell&rsquo;Unione Europea a cura del Gruppo di lavoro ex Articolo 29 Direttiva 95/46/CE.<br />\r\n<br />\r\nL\\\'Ente, titolare del trattamento dei dati, fornisce agli utenti che si collegano a pagine web del sito, le seguenti informazioni. L&rsquo;interessato, letta l&rsquo;informativa sotto riportata, accetta espressamente la registrazione ed il trattamento dei propri dati, nelle modalit&agrave; sotto indicate.<br />\r\n<br />\r\nFinalit&agrave; del trattamento<br />\r\n<br />\r\nI dati personali saranno trattati in relazione ai servizi offerti dall\\\'Ente esclusivamente per le finalit&agrave; che rientrano nei compiti istituzionali dell&rsquo;Amministrazione o per gli adempimenti previsti da norme di legge o di regolamento.<br />\r\n<br />\r\nDati di navigazione<br />\r\n<br />\r\nL&rsquo;accesso al sito comporta la registrazione di dati utilizzati al solo fine di ricavare informazioni statistiche o per garantire il corretto funzionamento. A fini statistici possono venire registrati alcuni dati relativi all&rsquo;accesso al sito quali l&rsquo;indirizzo di protocollo internet (IP), il sistema operativo utilizzato dal computer dell&rsquo;utente, il tipo di browser, ecc.<br />\r\nTali dati possono essere pubblicati sul nostro sito sotto forma di informazioni statistiche anonime sull&rsquo;uso del sito e utilizzate per verificare il corretto funzionamento del sito. Questi dati sono cancellati dopo l&rsquo;elaborazione. Non viene fatto uso di cookies per la trasmissione di informazioni di carattere personale, n&eacute; per il tracciamento degli utenti.<br />\r\n<br />\r\nCookies<br />\r\n<br />\r\nNessun dato personale degli utenti viene di proposito acquisito dal sito. L&rsquo;uso di c.d. cookies di sessione &egrave; strettamente limitato alla trasmissione di identificativi di sessione (costituiti da numeri casuali generati dal server) necessari per consentire l&rsquo;esplorazione sicura ed efficiente del sito. Le modalit&agrave; di funzionamento nonch&eacute; le opzioni per limitare o bloccare i cookie, possono essere effettuate modificando le impostazioni del proprio browser Internet.<br />\r\n&Egrave; possibile inoltre visitare il sito, in lingua inglese, www.aboutcookies.org per informazioni su come poter gestire/eliminare i cookie in base al tipo di browser utilizzato. Per eliminare i cookie dal browser Internet del proprio smartphone/tablet, &egrave; necessario fare riferimento al manuale d&rsquo;uso del dispositivo.<br />\r\n<br />\r\n<br />\r\nDati forniti volontariamente dall&rsquo;utente<br />\r\n<br />\r\nL\\\'eventuale raccolta di dati personali individuali (nome, posta elettronica, indirizzo, ecc.) tramite il presente sito e finalizzata a fornire un servizio &egrave; accompagnata di volta in volta da specifiche informative di sintesi (disclaimer) che contengono le informazioni previste per legge (responsabile del trattamento, finalit&agrave;, natura obbligatoria o facoltativa del dato, destinatari dei dati raccolti, diritto di accesso e rettifica, periodo di conservazione dei dati, possibilit&agrave; di cancellazione dall&rsquo;iscrizione). Il mancato inserimento di dati obbligatori da parte dell&rsquo;utente comporta l&rsquo;impossibilit&agrave; di erogazione del servizio richiesto.&nbsp;<br />\r\n<br />\r\nModalit&agrave; del trattamento<br />\r\n<br />\r\nI dati sono trattati principalmente con strumenti elettronici e informatici, memorizzati sia su supporti informatici che su supporti cartacei nel rispetto delle misure di sicurezza.<br />\r\n<br />\r\nDiritti dell&rsquo;interessato<br />\r\n<br />\r\nIn relazione al presente trattamento di dati personali, ai sensi dell&rsquo;art. 7 - Diritto di accesso ai dati personali ed altri diritti, Decreto legislativo 2003, n. 196:<br />\r\n1. L&rsquo;interessato ha diritto di ottenere la conferma dell&rsquo;esistenza o meno di dati personali che lo riguardano e la loro comunicazione in forma intelligibile.<br />\r\n2. L&rsquo;interessato ha diritto di ottenere l&rsquo;indicazione:<br />\r\na) dell&rsquo;origine dei dati personali;<br />\r\nb) delle finalit&agrave; e modalit&agrave; del trattamento;<br />\r\nc) della logica applicata in caso di trattamento effettuato con l&rsquo;ausilio di strumenti elettronici;<br />\r\nd) degli estremi identificativi del titolare, dei responsabili e del rappresentante designato ai sensi dell&rsquo;articolo 5, comma 2;<br />\r\ne) dei soggetti o delle categorie di soggetti ai quali i dati personali possono essere comunicati o che possono venirne a conoscenza in qualit&agrave; di rappresentante designato nel territorio dello Stato, di responsabili o incaricati.<br />\r\n3. L&rsquo;interessato ha diritto di ottenere:<br />\r\na) l&rsquo;aggiornamento, la rettificazione ovvero, quando vi ha interesse, l&rsquo;integrazione dei dati;<br />\r\nb) la cancellazione, la trasformazione in forma anonima o il blocco dei dati trattati in violazione di legge, compresi quelli di cui non &egrave; necessaria la conservazione in relazione agli scopi per i quali i dati sono stati raccolti o successivamente trattati;<br />\r\nc) l&rsquo;attestazione che le operazioni di cui alle lettere a) e b) sono state portate a conoscenza, anche per quanto riguarda il loro contenuto, di coloro ai quali i dati sono stati comunicati o diffusi, eccettuato il caso in cui tale adempimento si rivela impossibile o comporta un impiego di mezzi manifestamente sproporzionato rispetto al diritto tutelato.<br />\r\n4. L&rsquo;interessato ha diritto di opporsi, in tutto o in parte:<br />\r\na) per motivi legittimi al trattamento dei dati personali che lo riguardano, ancorch&eacute; pertinenti allo scopo della raccolta;<br />\r\nb) al trattamento di dati personali che lo riguardano a fini di invio di materiale pubblicitario o di vendita diretta o per il compimento di ricerche di mercato o di comunicazione commerciale.<br />\r\n<br />\r\nTitolare del trattamento dei dati &egrave; l\\\'Ente</p>'),
	(791, 1, 0, 0, 0, 0, '', '<p><strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">I cookie sono file di testo che i siti visitati dagli utenti inviano ai loro terminali e che vengono ritrasmessi ai siti stessi alla visita successiva. I cookie si possono suddividere in due macro-categorie: &ldquo;cookie di profilazione&rdquo; e &ldquo;cookie tecnici&rdquo;.</span><br />\r\n<br />\r\n<strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie di profilazione</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Il presente sito NON utilizza cookie di profilazione, cio&egrave; cookie volti a creare profili relativi all\\\'utente al fine di inviare messaggi pubblicitari in linea con le preferenze manifestate nell\\\'ambito della navigazione sul sito.&nbsp;</span><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Nessun dato personale degli utenti viene di proposito acquisito dal sito.</span><br />\r\n<br />\r\n<strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie tecnici</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Il sito utilizza cookie tecnici per consentire l&rsquo;esplorazione sicura, rapida ed efficiente del sito stesso e per fornire agli utenti i servizi richiesti. Per l\\\'installazione di tali cookie non &egrave; richiesto il preventivo consenso degli utenti.</span><br />\r\n<br />\r\n<strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie tecnici di sessione</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">I cookie di sessione sono utilizzati per la navigazione e per l&rsquo;autenticazione ai servizi online e alle aree riservate. L&rsquo;uso di questi cookie (che non vengono memorizzati in modo persistente sul computer dell&rsquo;utente e svaniscono con la chiusura del browser) &egrave; strettamente limitato alla trasmissione di identificativi di sessione (costituiti da numeri casuali generati dal server) necessari per consentire la navigazione efficace del sito. I cookie di sessione utilizzati in questo sito evitano il ricorso ad altre tecniche informatiche potenzialmente pregiudizievoli per la riservatezza della navigazione degli utenti e non consentono l&rsquo;acquisizione di dati personali identificativi dell&rsquo;utente.</span><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">L&rsquo;utilizzo di cookie permanenti &egrave; strettamente limitato all&rsquo;acquisizione dei dati statistici utili a comprendere il livello di utilizzo del proprio sito.</span><br />\r\n<br />\r\n<strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie analytics</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Sono assimilati ai cookie tecnici e sono utilizzati per raccogliere informazioni, in forma aggregata, sul numero degli utenti e su come questi visitano il sito. I dati ricavabili da questi cookie (compresi gli indirizzi IP) sono gestiti dall&rsquo;Ente in qualit&agrave; di gestore del sito esclusivamente per finalit&agrave; statistiche e per l&rsquo;elaborazione di report sull&rsquo;utilizzo del sito stesso.</span><br />\r\n<br />\r\n<strong><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Cookie di terze parti</span></strong><br />\r\n<span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Non sono installati cookie di terze parti<br />\r\n<br /></span> <strong style=\\"font-size: 12.8000001907349px; line-height: 1.5;\\"><span style=\\"font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">Come disabilitare i cookie (opt-out)</span></strong></p>\r\n<p style=\\"color: rgb(66, 66, 66); font-family: \\\'Open Sans\\\', Tahoma, sans-serif; font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\">La maggior parte dei browser accettano i cookies automaticamente, ma &egrave; possibile rifiutarli. Se non si desidera ricevere o memorizzare i cookie, si possono modificare le impostazioni di sicurezza del browser (Internet Explorer, Google Chrome, MozillaFirefox, Safari Opera, ecc&hellip;). Ciascun browser presenta procedure diverse per la gestione delle impostazioni:<br />\r\n<br />\r\nMicrosoft Internet Explorer<br />\r\nDa &ldquo;Strumenti&rdquo; selezionare &ldquo;Opzioni internet&rdquo;. Nella finestra pop up selezionare &ldquo;Privacy&rdquo; e regolare le impostazioni dei cookies oppure tramite i link:<br />\r\n<a href=\\"http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies\\">http://windows.microsoft.com/en-us/windows-vista/block-or-allow-cookies</a><br />\r\n<a href=\\"http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9\\">http://windows.microsoft.com/it-it/internet-explorer/delete-manage-cookies#ie=ie-9</a><br />\r\n<br />\r\nGoogle Chrome per Desktop<br />\r\nSelezionare &ldquo;Impostazioni&rdquo;, poi &ldquo;Mostra impostazioni avanzate&rdquo;, successivamente nella sezione &ldquo;Privacy&rdquo; selezionare &ldquo;Impostazione Contenuti&rdquo; e regolare le impostazioni dei cookie oppure accedere tramite i link:<br />\r\n<a href=\\"https://support.google.com/chrome/bin/answer.py?hl=en&amp;answer=95647&amp;p=cpn_cookies\\">https://support.google.com/chrome/bin/answer.py?hl=en&amp;answer=95647&amp;p=cpn_cookies</a><br />\r\n<a href=\\"https://support.google.com/accounts/answer/61416?hl=it\\">https://support.google.com/accounts/answer/61416?hl=it</a><br />\r\n<br />\r\nGoogle Chrome per Mobile<br />\r\nAccedere tramite link:<br />\r\n<a href=\\"https://support.google.com/chrome/answer/2392971?hl=it\\">https://support.google.com/chrome/answer/2392971?hl=it</a><br />\r\n<br />\r\nMozilla Firefox<br />\r\nSelezionare &ldquo;Opzioni&rdquo; e nella finestra di pop up selezionare &ldquo;Privacy&rdquo; per regolare le impostazioni dei cookie, oppure accedere tramite i link:</p>\r\n<p style=\\"color: rgb(66, 66, 66); font-family: \\\'Open Sans\\\', Tahoma, sans-serif; font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\"><a href=\\"http://support.mozilla.org/en-US/kb/Enabling%20and%20disabling%20cookies\\">http://support.mozilla.org/en-US/kb/Enabling%20and%20disabling%20cookies</a><br />\r\n<a href=\\"https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie\\">https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie</a><br />\r\n<br />\r\nApple Safari<br />\r\nSelezionare &ldquo;Preferenze&rdquo; e poi &ldquo;Sicurezza&rdquo; dove regolare le impostazioni dei cookie oppure accedere tramite il link:<br />\r\n<a href=\\"https://support.apple.com/it-it/HT201265\\">https://support.apple.com/it-it/HT201265</a><br />\r\n<br />\r\nOpera<br />\r\nSelezionare &ldquo;Preferenze&rdquo;, poi &ldquo;Avanzate&rdquo; e poi &ldquo;Cookie&rdquo; dove regolare le impostazioni dei cookie oppure accedere tramite i link:</p>\r\n<p style=\\"color: rgb(66, 66, 66); font-family: \\\'Open Sans\\\', Tahoma, sans-serif; font-size: 12.8000001907349px; line-height: 16.6399993896484px;\\"><a href=\\"http://www.opera.com/help/tutorials/security/cookies/\\">http://www.opera.com/help/tutorials/security/cookies/</a><br />\r\n<a href=\\"http://help.opera.com/Windows/10.00/it/cookies.html\\">http://help.opera.com/Windows/10.00/it/cookies.html</a><br />\r\n<br />\r\nBrowser nativo Android<br />\r\nSelezionare &ldquo;Impostazioni&rdquo;, poi &ldquo;Privacy&rdquo; e selezionare o deselezionare la casella &ldquo;Accetta cookie&rdquo;.</p>\r\n<table border=\\"0\\" style=\\"width: 99%;\\" summary=\\"Elenco dei cookie presenti\\">\r\n<caption><span class=\\"classEditor159\\"><span class=\\"classEditor285\\">Elenco dei cookie presenti</span></span></caption>\r\n<thead>\r\n<tr>\r\n<th scope=\\"col\\">NOME</th>\r\n<th>FINALITA\\\'</th>\r\n<th>DURATA</th>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n<tr>\r\n<td>KCFINDER_displaySettings</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>6 mesi</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_order</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>4 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_orderDesc</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>4 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_showname</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>4 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_showsize</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>4 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_showtime</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>4 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>KCFINDER_view</td>\r\n<td>Cookie tecnico destinato al corretto funzionamento del KC FINDER</td>\r\n<td>6 mesi</td>\r\n</tr>\r\n<tr>\r\n<td>nomecookie</td>\r\n<td>Cookie tecnico per la propagazione delle informazioni sulla sessione</td>\r\n<td>10 giorni</td>\r\n</tr>\r\n<tr>\r\n<td>nomecookie</td>\r\n<td>Raccoglie informazioni circa i percorsi di navigazione dei visitatori al fine di migliorarne l\\\'esperienza di navigazione</td>\r\n<td>20 anni</td>\r\n</tr>\r\n<tr>\r\n<td>_data</td>\r\n<td>Cookie tecnico per la propagazione delle informazioni sulla sessione</td>\r\n<td>1 anno</td>\r\n</tr>\r\n<tr>\r\n<td>_personali</td>\r\n<td>Raccoglie informazioni circa i percorsi di navigazione dei visitatori al fine di migliorarne l\\\'esperienza di navigazione</td>\r\n<td>20 anni</td>\r\n</tr>\r\n<tr>\r\n<td>_sid</td>\r\n<td>Propagazione session id</td>\r\n<td>sessione</td>\r\n</tr>\r\n</tbody>\r\n</table>');
/*!40000 ALTER TABLE `oggetto_paragrafo` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_paragrafo_backup: 0 rows
DELETE FROM `oggetto_paragrafo_backup`;
/*!40000 ALTER TABLE `oggetto_paragrafo_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_paragrafo_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_paragrafo_workflow: 0 rows
DELETE FROM `oggetto_paragrafo_workflow`;
/*!40000 ALTER TABLE `oggetto_paragrafo_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_paragrafo_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_primopiano: 0 rows
DELETE FROM `oggetto_primopiano`;
/*!40000 ALTER TABLE `oggetto_primopiano` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_primopiano` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_primopiano_backup: 0 rows
DELETE FROM `oggetto_primopiano_backup`;
/*!40000 ALTER TABLE `oggetto_primopiano_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_primopiano_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_primopiano_workflow: 0 rows
DELETE FROM `oggetto_primopiano_workflow`;
/*!40000 ALTER TABLE `oggetto_primopiano_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_primopiano_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_procedimenti: 0 rows
DELETE FROM `oggetto_procedimenti`;
/*!40000 ALTER TABLE `oggetto_procedimenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_procedimenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_procedimenti_backup: 0 rows
DELETE FROM `oggetto_procedimenti_backup`;
/*!40000 ALTER TABLE `oggetto_procedimenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_procedimenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_procedimenti_pre_az: 0 rows
DELETE FROM `oggetto_procedimenti_pre_az`;
/*!40000 ALTER TABLE `oggetto_procedimenti_pre_az` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_procedimenti_pre_az` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_procedimenti_workflow: 0 rows
DELETE FROM `oggetto_procedimenti_workflow`;
/*!40000 ALTER TABLE `oggetto_procedimenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_procedimenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_provvedimenti: 0 rows
DELETE FROM `oggetto_provvedimenti`;
/*!40000 ALTER TABLE `oggetto_provvedimenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_provvedimenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_provvedimenti_backup: 0 rows
DELETE FROM `oggetto_provvedimenti_backup`;
/*!40000 ALTER TABLE `oggetto_provvedimenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_provvedimenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_provvedimenti_workflow: 0 rows
DELETE FROM `oggetto_provvedimenti_workflow`;
/*!40000 ALTER TABLE `oggetto_provvedimenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_provvedimenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_regolamenti: 0 rows
DELETE FROM `oggetto_regolamenti`;
/*!40000 ALTER TABLE `oggetto_regolamenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_regolamenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_regolamenti_backup: 0 rows
DELETE FROM `oggetto_regolamenti_backup`;
/*!40000 ALTER TABLE `oggetto_regolamenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_regolamenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_regolamenti_workflow: 0 rows
DELETE FROM `oggetto_regolamenti_workflow`;
/*!40000 ALTER TABLE `oggetto_regolamenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_regolamenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_riferimenti: 0 rows
DELETE FROM `oggetto_riferimenti`;
/*!40000 ALTER TABLE `oggetto_riferimenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_riferimenti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_riferimenti_backup: 0 rows
DELETE FROM `oggetto_riferimenti_backup`;
/*!40000 ALTER TABLE `oggetto_riferimenti_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_riferimenti_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_riferimenti_rapallo: 0 rows
DELETE FROM `oggetto_riferimenti_rapallo`;
/*!40000 ALTER TABLE `oggetto_riferimenti_rapallo` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_riferimenti_rapallo` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_riferimenti_workflow: 0 rows
DELETE FROM `oggetto_riferimenti_workflow`;
/*!40000 ALTER TABLE `oggetto_riferimenti_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_riferimenti_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_societa: 0 rows
DELETE FROM `oggetto_societa`;
/*!40000 ALTER TABLE `oggetto_societa` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_societa` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_societa_backup: 0 rows
DELETE FROM `oggetto_societa_backup`;
/*!40000 ALTER TABLE `oggetto_societa_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_societa_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_societa_workflow: 0 rows
DELETE FROM `oggetto_societa_workflow`;
/*!40000 ALTER TABLE `oggetto_societa_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_societa_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_sovvenzioni: 0 rows
DELETE FROM `oggetto_sovvenzioni`;
/*!40000 ALTER TABLE `oggetto_sovvenzioni` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_sovvenzioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_sovvenzioni_backup: 0 rows
DELETE FROM `oggetto_sovvenzioni_backup`;
/*!40000 ALTER TABLE `oggetto_sovvenzioni_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_sovvenzioni_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_sovvenzioni_workflow: 0 rows
DELETE FROM `oggetto_sovvenzioni_workflow`;
/*!40000 ALTER TABLE `oggetto_sovvenzioni_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_sovvenzioni_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_uffici: 0 rows
DELETE FROM `oggetto_uffici`;
/*!40000 ALTER TABLE `oggetto_uffici` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_uffici` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_uffici_backup: 0 rows
DELETE FROM `oggetto_uffici_backup`;
/*!40000 ALTER TABLE `oggetto_uffici_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_uffici_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_uffici_workflow: 0 rows
DELETE FROM `oggetto_uffici_workflow`;
/*!40000 ALTER TABLE `oggetto_uffici_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_uffici_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_url_avcp: 0 rows
DELETE FROM `oggetto_url_avcp`;
/*!40000 ALTER TABLE `oggetto_url_avcp` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_url_avcp` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_url_avcp_backup: 0 rows
DELETE FROM `oggetto_url_avcp_backup`;
/*!40000 ALTER TABLE `oggetto_url_avcp_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_url_avcp_backup` ENABLE KEYS */;

-- Dump dei dati della tabella pat.oggetto_url_avcp_workflow: 0 rows
DELETE FROM `oggetto_url_avcp_workflow`;
/*!40000 ALTER TABLE `oggetto_url_avcp_workflow` DISABLE KEYS */;
/*!40000 ALTER TABLE `oggetto_url_avcp_workflow` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_modelli: 7 rows
DELETE FROM `regole_modelli`;
/*!40000 ALTER TABLE `regole_modelli` DISABLE KEYS */;
INSERT INTO `regole_modelli` (`id`, `nome`, `descrizione`, `id_regola_template`, `id_sezione_nativa`, `modello_dinamico_richiami`, `modello_dinamico_media`, `modello_scheda`, `modello_scheda_posizione`, `stili_inclusi`) VALUES
	(1, 'Banner nella colonna destra', 'Modello per la presentazione dei banner nella colonna destra del portale', 18, 0, 0, 0, 0, 'inizio', 1),
	(2, 'Banner nella colonna sinistra', 'Modello per la presentazione dei banner nella colonna sinistra del portale', 9, 0, 0, 0, 0, 'inizio', 1),
	(3, 'Aree Tematiche', 'Modello contenente il menu di navighazione dell\'oggetto aree tematiche', 8, 0, 0, 0, 0, 'inizio', 1),
	(4, 'Leggi tutto in colonna destra', 'Modello per le correlazioni informative via tag: pubblica gli argomenti della sezione, e le notizie, gli eventi e le immagini correlate', 20, 0, 0, 0, 0, 'inizio', 1),
	(5, 'Correlazioni organizzative', 'Modello contenente gli uffici, i procedimenti,i regolamenti e la modulistica scelti per la visualizzazione in sezione, e delle domande frequenti associate ai tags visualizzati in sezione', 10, 0, 1, 0, 0, 'inizio', 1),
	(6, 'Oggetti informativi correlati alla lattura completa', 'Modello formato dagli oggetti informativi (notizie,eventi,immagini) correlati alla lettura completa che si sta visualizzando', 10, 0, 0, 0, 0, 'inizio', 1),
	(7, 'Modello di contenuti trasparenza aggiuntivi', 'Questo modello pubblica il richiamo del modello di contenuto dedicato agli enti di PAT', 129, 0, 0, 0, 0, 'inizio', 1);
/*!40000 ALTER TABLE `regole_modelli` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_modelli_applicazioni: 650 rows
DELETE FROM `regole_modelli_applicazioni`;
/*!40000 ALTER TABLE `regole_modelli_applicazioni` DISABLE KEYS */;
INSERT INTO `regole_modelli_applicazioni` (`id_sezione`, `id_lingua`, `id_modello`, `id_regola_template`) VALUES
	(0, 1, 1, 18),
	(0, 1, 2, 9),
	(0, 1, 3, 8),
	(3, 1, 1, 18),
	(3, 1, 2, 9),
	(3, 1, 3, 8),
	(6, 1, 1, 18),
	(6, 1, 2, 9),
	(6, 1, 3, 8),
	(7, 1, 1, 18),
	(7, 1, 2, 9),
	(7, 1, 3, 8),
	(8, 1, 1, 18),
	(8, 1, 2, 9),
	(8, 1, 3, 8),
	(10, 1, 1, 18),
	(10, 1, 2, 9),
	(10, 1, 3, 8),
	(12, 1, 1, 18),
	(12, 1, 2, 9),
	(12, 1, 3, 8),
	(12, 1, 6, 10),
	(13, 1, 1, 18),
	(13, 1, 2, 9),
	(13, 1, 3, 8),
	(13, 1, 6, 10),
	(14, 1, 1, 18),
	(14, 1, 2, 9),
	(14, 1, 3, 8),
	(15, 1, 1, 18),
	(15, 1, 2, 9),
	(15, 1, 3, 8),
	(15, 1, 6, 10),
	(16, 1, 1, 18),
	(16, 1, 2, 9),
	(16, 1, 3, 8),
	(17, 1, 1, 18),
	(17, 1, 2, 9),
	(17, 1, 3, 8),
	(18, 1, 1, 18),
	(18, 1, 2, 9),
	(18, 1, 3, 8),
	(19, 1, 1, 18),
	(19, 1, 2, 9),
	(19, 1, 3, 8),
	(768, 1, 7, 129),
	(768, 1, 1, 18),
	(768, 1, 3, 8),
	(21, 1, 1, 18),
	(21, 1, 2, 9),
	(21, 1, 3, 8),
	(22, 1, 1, 18),
	(22, 1, 2, 9),
	(22, 1, 3, 8),
	(25, 1, 1, 18),
	(25, 1, 2, 9),
	(25, 1, 3, 8),
	(26, 1, 1, 18),
	(26, 1, 2, 9),
	(26, 1, 3, 8),
	(27, 1, 1, 18),
	(27, 1, 2, 9),
	(27, 1, 3, 8),
	(36, 1, 1, 18),
	(36, 1, 2, 9),
	(36, 1, 3, 8),
	(39, 1, 1, 18),
	(39, 1, 2, 9),
	(39, 1, 3, 8),
	(43, 1, 1, 18),
	(43, 1, 2, 9),
	(43, 1, 3, 8),
	(44, 1, 1, 18),
	(44, 1, 2, 9),
	(44, 1, 3, 8),
	(705, 1, 7, 129),
	(788, 1, 7, 129),
	(788, 1, 1, 18),
	(46, 1, 1, 18),
	(46, 1, 2, 9),
	(46, 1, 3, 8),
	(788, 1, 3, 8),
	(788, 1, 2, 9),
	(749, 1, 7, 129),
	(48, 1, 1, 18),
	(48, 1, 2, 9),
	(48, 1, 3, 8),
	(59, 1, 7, 129),
	(68, 1, 7, 129),
	(53, 1, 7, 129),
	(50, 1, 1, 18),
	(50, 1, 2, 9),
	(50, 1, 3, 8),
	(51, 1, 1, 18),
	(51, 1, 2, 9),
	(51, 1, 3, 8),
	(768, 1, 2, 9),
	(769, 1, 7, 129),
	(769, 1, 1, 18),
	(53, 1, 1, 18),
	(53, 1, 2, 9),
	(53, 1, 3, 8),
	(54, 1, 1, 18),
	(54, 1, 2, 9),
	(54, 1, 3, 8),
	(769, 1, 3, 8),
	(769, 1, 2, 9),
	(770, 1, 7, 129),
	(56, 1, 1, 18),
	(56, 1, 2, 9),
	(56, 1, 3, 8),
	(57, 1, 1, 18),
	(57, 1, 2, 9),
	(57, 1, 3, 8),
	(724, 1, 7, 129),
	(51, 1, 7, 129),
	(793, 1, 7, 129),
	(59, 1, 1, 18),
	(59, 1, 2, 9),
	(59, 1, 3, 8),
	(60, 1, 1, 18),
	(60, 1, 2, 9),
	(60, 1, 3, 8),
	(61, 1, 1, 18),
	(61, 1, 2, 9),
	(61, 1, 3, 8),
	(62, 1, 1, 18),
	(62, 1, 2, 9),
	(62, 1, 3, 8),
	(63, 1, 1, 18),
	(63, 1, 2, 9),
	(63, 1, 3, 8),
	(64, 1, 1, 18),
	(64, 1, 2, 9),
	(64, 1, 3, 8),
	(65, 1, 1, 18),
	(65, 1, 2, 9),
	(65, 1, 3, 8),
	(66, 1, 1, 18),
	(66, 1, 2, 9),
	(66, 1, 3, 8),
	(68, 1, 1, 18),
	(68, 1, 2, 9),
	(68, 1, 3, 8),
	(69, 1, 1, 18),
	(69, 1, 2, 9),
	(69, 1, 3, 8),
	(50, 1, 7, 129),
	(717, 1, 7, 129),
	(714, 1, 7, 129),
	(522, 1, 1, 18),
	(522, 1, 2, 9),
	(522, 1, 3, 8),
	(523, 1, 1, 18),
	(523, 1, 2, 9),
	(523, 1, 3, 8),
	(711, 1, 7, 129),
	(713, 1, 7, 129),
	(21, 1, 7, 129),
	(566, 1, 1, 18),
	(566, 1, 2, 9),
	(566, 1, 3, 8),
	(604, 1, 1, 18),
	(604, 1, 2, 9),
	(604, 1, 3, 8),
	(605, 1, 1, 18),
	(605, 1, 2, 9),
	(605, 1, 3, 8),
	(606, 1, 1, 18),
	(606, 1, 2, 9),
	(606, 1, 3, 8),
	(607, 1, 1, 18),
	(607, 1, 2, 9),
	(607, 1, 3, 8),
	(609, 1, 1, 18),
	(609, 1, 2, 9),
	(609, 1, 3, 8),
	(770, 1, 1, 18),
	(770, 1, 3, 8),
	(770, 1, 2, 9),
	(793, 1, 1, 18),
	(793, 1, 3, 8),
	(793, 1, 2, 9),
	(771, 1, 7, 129),
	(771, 1, 1, 18),
	(771, 1, 3, 8),
	(616, 1, 1, 18),
	(616, 1, 2, 9),
	(616, 1, 3, 8),
	(19, 1, 7, 129),
	(640, 1, 7, 129),
	(22, 1, 7, 129),
	(771, 1, 2, 9),
	(789, 1, 7, 129),
	(789, 1, 1, 18),
	(627, 1, 1, 18),
	(627, 1, 2, 9),
	(627, 1, 3, 8),
	(628, 1, 1, 18),
	(628, 1, 2, 9),
	(628, 1, 3, 8),
	(629, 1, 1, 18),
	(629, 1, 2, 9),
	(629, 1, 3, 8),
	(741, 1, 7, 129),
	(566, 1, 7, 129),
	(43, 1, 7, 129),
	(632, 1, 1, 18),
	(632, 1, 2, 9),
	(632, 1, 3, 8),
	(633, 1, 1, 18),
	(633, 1, 2, 9),
	(633, 1, 3, 8),
	(634, 1, 1, 18),
	(634, 1, 2, 9),
	(634, 1, 3, 8),
	(635, 1, 1, 18),
	(635, 1, 2, 9),
	(635, 1, 3, 8),
	(636, 1, 1, 18),
	(636, 1, 2, 9),
	(636, 1, 3, 8),
	(637, 1, 1, 18),
	(637, 1, 2, 9),
	(637, 1, 3, 8),
	(609, 1, 7, 129),
	(63, 1, 7, 129),
	(701, 1, 7, 129),
	(639, 1, 1, 18),
	(639, 1, 2, 9),
	(639, 1, 3, 8),
	(640, 1, 1, 18),
	(640, 1, 2, 9),
	(640, 1, 3, 8),
	(641, 1, 1, 18),
	(641, 1, 2, 9),
	(641, 1, 3, 8),
	(789, 1, 3, 8),
	(789, 1, 2, 9),
	(708, 1, 7, 129),
	(61, 1, 7, 129),
	(25, 1, 7, 129),
	(790, 1, 7, 129),
	(790, 1, 1, 18),
	(790, 1, 3, 8),
	(790, 1, 2, 9),
	(746, 1, 7, 129),
	(712, 1, 7, 129),
	(639, 1, 7, 129),
	(765, 1, 3, 8),
	(765, 1, 2, 9),
	(663, 1, 1, 18),
	(663, 1, 3, 8),
	(663, 1, 2, 9),
	(765, 1, 1, 18),
	(662, 1, 5, 10),
	(662, 1, 1, 18),
	(662, 1, 3, 8),
	(662, 1, 4, 20),
	(662, 1, 2, 9),
	(18, 1, 7, 129),
	(742, 1, 7, 129),
	(709, 1, 7, 129),
	(702, 1, 3, 8),
	(688, 1, 2, 9),
	(688, 1, 3, 8),
	(688, 1, 1, 18),
	(689, 1, 2, 9),
	(689, 1, 3, 8),
	(689, 1, 1, 18),
	(702, 1, 2, 9),
	(701, 1, 1, 18),
	(701, 1, 3, 8),
	(701, 1, 2, 9),
	(40, 1, 5, 10),
	(40, 1, 1, 18),
	(40, 1, 3, 8),
	(40, 1, 4, 20),
	(40, 1, 2, 9),
	(702, 1, 1, 18),
	(703, 1, 2, 9),
	(703, 1, 3, 8),
	(703, 1, 1, 18),
	(704, 1, 2, 9),
	(704, 1, 3, 8),
	(704, 1, 1, 18),
	(705, 1, 2, 9),
	(705, 1, 3, 8),
	(705, 1, 1, 18),
	(706, 1, 2, 9),
	(706, 1, 3, 8),
	(706, 1, 1, 18),
	(707, 1, 2, 9),
	(707, 1, 3, 8),
	(707, 1, 1, 18),
	(708, 1, 2, 9),
	(708, 1, 3, 8),
	(708, 1, 1, 18),
	(712, 1, 2, 9),
	(712, 1, 3, 8),
	(712, 1, 1, 18),
	(709, 1, 2, 9),
	(709, 1, 3, 8),
	(709, 1, 1, 18),
	(723, 1, 2, 9),
	(723, 1, 3, 8),
	(723, 1, 1, 18),
	(722, 1, 2, 9),
	(722, 1, 3, 8),
	(722, 1, 1, 18),
	(721, 1, 2, 9),
	(721, 1, 3, 8),
	(721, 1, 1, 18),
	(724, 1, 2, 9),
	(724, 1, 3, 8),
	(724, 1, 1, 18),
	(725, 1, 2, 9),
	(725, 1, 3, 8),
	(725, 1, 1, 18),
	(726, 1, 2, 9),
	(726, 1, 3, 8),
	(726, 1, 1, 18),
	(727, 1, 2, 9),
	(727, 1, 3, 8),
	(727, 1, 1, 18),
	(728, 1, 2, 9),
	(728, 1, 3, 8),
	(728, 1, 1, 18),
	(729, 1, 2, 9),
	(729, 1, 3, 8),
	(729, 1, 1, 18),
	(730, 1, 2, 9),
	(730, 1, 3, 8),
	(730, 1, 1, 18),
	(731, 1, 2, 9),
	(731, 1, 3, 8),
	(731, 1, 1, 18),
	(732, 1, 2, 9),
	(732, 1, 3, 8),
	(732, 1, 1, 18),
	(733, 1, 2, 9),
	(733, 1, 3, 8),
	(733, 1, 1, 18),
	(734, 1, 2, 9),
	(734, 1, 3, 8),
	(734, 1, 1, 18),
	(735, 1, 2, 9),
	(735, 1, 3, 8),
	(735, 1, 1, 18),
	(736, 1, 2, 9),
	(736, 1, 3, 8),
	(736, 1, 1, 18),
	(737, 1, 2, 9),
	(737, 1, 3, 8),
	(737, 1, 1, 18),
	(739, 1, 2, 9),
	(739, 1, 3, 8),
	(739, 1, 1, 18),
	(740, 1, 2, 9),
	(740, 1, 3, 8),
	(740, 1, 1, 18),
	(741, 1, 2, 9),
	(741, 1, 3, 8),
	(741, 1, 1, 18),
	(745, 1, 2, 9),
	(745, 1, 3, 8),
	(745, 1, 1, 18),
	(744, 1, 2, 9),
	(744, 1, 3, 8),
	(744, 1, 1, 18),
	(743, 1, 2, 9),
	(743, 1, 3, 8),
	(743, 1, 1, 18),
	(742, 1, 2, 9),
	(742, 1, 3, 8),
	(742, 1, 1, 18),
	(711, 1, 2, 9),
	(711, 1, 3, 8),
	(711, 1, 1, 18),
	(747, 1, 2, 9),
	(747, 1, 3, 8),
	(747, 1, 1, 18),
	(700, 1, 2, 9),
	(700, 1, 3, 8),
	(700, 1, 1, 18),
	(710, 1, 2, 9),
	(710, 1, 3, 8),
	(710, 1, 1, 18),
	(713, 1, 2, 9),
	(713, 1, 3, 8),
	(713, 1, 1, 18),
	(748, 1, 2, 9),
	(748, 1, 3, 8),
	(748, 1, 1, 18),
	(749, 1, 2, 9),
	(749, 1, 3, 8),
	(749, 1, 1, 18),
	(714, 1, 2, 9),
	(714, 1, 3, 8),
	(714, 1, 1, 18),
	(717, 1, 2, 9),
	(717, 1, 3, 8),
	(717, 1, 1, 18),
	(718, 1, 2, 9),
	(718, 1, 3, 8),
	(718, 1, 1, 18),
	(719, 1, 2, 9),
	(719, 1, 3, 8),
	(719, 1, 1, 18),
	(720, 1, 2, 9),
	(720, 1, 3, 8),
	(720, 1, 1, 18),
	(752, 1, 2, 9),
	(752, 1, 3, 8),
	(752, 1, 1, 18),
	(751, 1, 2, 9),
	(751, 1, 3, 8),
	(751, 1, 1, 18),
	(738, 1, 2, 9),
	(738, 1, 3, 8),
	(738, 1, 1, 18),
	(746, 1, 2, 9),
	(746, 1, 3, 8),
	(746, 1, 1, 18),
	(715, 1, 2, 9),
	(715, 1, 3, 8),
	(715, 1, 1, 18),
	(716, 1, 2, 9),
	(716, 1, 3, 8),
	(716, 1, 1, 18),
	(725, 1, 7, 129),
	(745, 1, 7, 129),
	(733, 1, 7, 129),
	(735, 1, 7, 129),
	(46, 1, 7, 129),
	(734, 1, 7, 129),
	(726, 1, 7, 129),
	(704, 1, 7, 129),
	(702, 1, 7, 129),
	(707, 1, 7, 129),
	(744, 1, 7, 129),
	(637, 1, 7, 129),
	(703, 1, 7, 129),
	(747, 1, 7, 129),
	(39, 1, 7, 129),
	(66, 1, 7, 129),
	(765, 1, 7, 129),
	(739, 1, 7, 129),
	(727, 1, 7, 129),
	(54, 1, 7, 129),
	(65, 1, 7, 129),
	(766, 1, 2, 9),
	(766, 1, 3, 8),
	(766, 1, 1, 18),
	(766, 1, 7, 129),
	(26, 1, 7, 129),
	(730, 1, 7, 129),
	(728, 1, 7, 129),
	(48, 1, 7, 129),
	(729, 1, 7, 129),
	(748, 1, 7, 129),
	(791, 1, 2, 9),
	(791, 1, 3, 8),
	(791, 1, 1, 18),
	(791, 1, 7, 129),
	(787, 1, 2, 9),
	(787, 1, 3, 8),
	(787, 1, 1, 18),
	(787, 1, 7, 129),
	(773, 1, 2, 9),
	(773, 1, 3, 8),
	(773, 1, 1, 18),
	(773, 1, 7, 129),
	(718, 1, 7, 129),
	(719, 1, 7, 129),
	(634, 1, 7, 129),
	(635, 1, 7, 129),
	(752, 1, 7, 129),
	(751, 1, 7, 129),
	(44, 1, 7, 129),
	(64, 1, 7, 129),
	(731, 1, 7, 129),
	(792, 1, 2, 9),
	(792, 1, 3, 8),
	(792, 1, 1, 18),
	(792, 1, 7, 129),
	(736, 1, 7, 129),
	(740, 1, 7, 129),
	(721, 1, 7, 129),
	(737, 1, 7, 129),
	(632, 1, 7, 129),
	(62, 1, 7, 129),
	(738, 1, 7, 129),
	(777, 1, 2, 9),
	(777, 1, 3, 8),
	(777, 1, 1, 18),
	(777, 1, 7, 129),
	(776, 1, 2, 9),
	(776, 1, 3, 8),
	(776, 1, 1, 18),
	(776, 1, 7, 129),
	(775, 1, 2, 9),
	(775, 1, 3, 8),
	(775, 1, 1, 18),
	(775, 1, 7, 129),
	(636, 1, 7, 129),
	(700, 1, 7, 129),
	(633, 1, 7, 129),
	(641, 1, 7, 129),
	(716, 1, 7, 129),
	(743, 1, 7, 129),
	(767, 1, 2, 9),
	(767, 1, 3, 8),
	(767, 1, 1, 18),
	(767, 1, 7, 129),
	(720, 1, 7, 129),
	(69, 1, 7, 129),
	(774, 1, 2, 9),
	(774, 1, 3, 8),
	(774, 1, 1, 18),
	(774, 1, 7, 129),
	(772, 1, 2, 9),
	(772, 1, 3, 8),
	(772, 1, 1, 18),
	(772, 1, 7, 129),
	(706, 1, 7, 129),
	(715, 1, 7, 129),
	(778, 1, 2, 9),
	(778, 1, 3, 8),
	(778, 1, 1, 18),
	(778, 1, 7, 129),
	(732, 1, 7, 129),
	(722, 1, 7, 129),
	(723, 1, 7, 129),
	(56, 1, 7, 129),
	(57, 1, 7, 129),
	(779, 1, 2, 9),
	(779, 1, 3, 8),
	(779, 1, 1, 18),
	(779, 1, 7, 129),
	(780, 1, 2, 9),
	(780, 1, 3, 8),
	(780, 1, 1, 18),
	(780, 1, 7, 129),
	(781, 1, 2, 9),
	(781, 1, 3, 8),
	(781, 1, 1, 18),
	(781, 1, 7, 129),
	(782, 1, 2, 9),
	(782, 1, 3, 8),
	(782, 1, 1, 18),
	(782, 1, 7, 129),
	(783, 1, 2, 9),
	(783, 1, 3, 8),
	(783, 1, 1, 18),
	(783, 1, 7, 129),
	(784, 1, 2, 9),
	(784, 1, 3, 8),
	(784, 1, 1, 18),
	(784, 1, 7, 129),
	(785, 1, 2, 9),
	(785, 1, 3, 8),
	(785, 1, 1, 18),
	(785, 1, 7, 129),
	(786, 1, 2, 9),
	(786, 1, 3, 8),
	(786, 1, 1, 18),
	(786, 1, 7, 129),
	(710, 1, 7, 129),
	(794, 1, 2, 9),
	(794, 1, 3, 8),
	(794, 1, 1, 18),
	(794, 1, 7, 129),
	(795, 1, 2, 9),
	(795, 1, 3, 8),
	(795, 1, 1, 18),
	(795, 1, 7, 129),
	(796, 1, 2, 9),
	(796, 1, 3, 8),
	(796, 1, 1, 18),
	(796, 1, 7, 129),
	(797, 1, 2, 9),
	(797, 1, 3, 8),
	(797, 1, 1, 18),
	(797, 1, 7, 129),
	(798, 1, 2, 9),
	(798, 1, 3, 8),
	(798, 1, 1, 18),
	(798, 1, 7, 129),
	(60, 1, 7, 129),
	(799, 1, 2, 9),
	(799, 1, 3, 8),
	(799, 1, 1, 18),
	(799, 1, 7, 129),
	(801, 1, 2, 9),
	(801, 1, 3, 8),
	(801, 1, 1, 18),
	(801, 1, 7, 129),
	(802, 1, 2, 9),
	(802, 1, 3, 8),
	(802, 1, 1, 18),
	(802, 1, 7, 129),
	(803, 1, 2, 9),
	(803, 1, 3, 8),
	(803, 1, 1, 18),
	(803, 1, 7, 129),
	(804, 1, 2, 9),
	(804, 1, 3, 8),
	(804, 1, 1, 18),
	(804, 1, 7, 129),
	(805, 1, 2, 9),
	(805, 1, 3, 8),
	(805, 1, 1, 18),
	(806, 1, 2, 9),
	(806, 1, 3, 8),
	(806, 1, 1, 18),
	(806, 1, 7, 129),
	(807, 1, 2, 9),
	(807, 1, 3, 8),
	(807, 1, 1, 18),
	(807, 1, 7, 129),
	(800, 1, 2, 9),
	(800, 1, 3, 8),
	(800, 1, 1, 18),
	(800, 1, 7, 129),
	(808, 1, 2, 9),
	(808, 1, 3, 8),
	(808, 1, 1, 18),
	(808, 1, 7, 129),
	(809, 1, 2, 9),
	(809, 1, 3, 8),
	(809, 1, 1, 18),
	(809, 1, 7, 129),
	(810, 1, 2, 9),
	(810, 1, 3, 8),
	(810, 1, 1, 18),
	(810, 1, 7, 129),
	(811, 1, 2, 9),
	(812, 1, 2, 9),
	(812, 1, 3, 8),
	(812, 1, 1, 18),
	(812, 1, 7, 129),
	(813, 1, 2, 9),
	(813, 1, 3, 8),
	(813, 1, 1, 18),
	(813, 1, 7, 129),
	(814, 1, 2, 9),
	(814, 1, 7, 129),
	(815, 1, 2, 9),
	(815, 1, 7, 129);
/*!40000 ALTER TABLE `regole_modelli_applicazioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_open_data_default: 16 rows
DELETE FROM `regole_open_data_default`;
/*!40000 ALTER TABLE `regole_open_data_default` DISABLE KEYS */;
INSERT INTO `regole_open_data_default` (`id`, `id_oggetto`, `campi`) VALUES
	(1, 38, 'nominativo,dati_fiscali,oggetto,struttura,responsabile,data,compenso,note,modo_individuazione,file_atto,progetto,cv_soggetto'),
	(2, 4, 'nominativo,oggetto,tipo_incarico,dirigente,struttura,inizio_incarico,fine_incarico,compenso,note,estremi_atti,file_atto,modo_individuazione,progetto,cv_soggetto'),
	(3, 27, 'nome,uffici,link,desc_cont,allegato1,allegato2,allegato3,allegato4'),
	(4, 19, 'titolo,strutture,procedimenti,allegato,allegato_2,allegato_3,descrizione_mod'),
	(5, 30, 'cittadini,imprese,titolo,data,descrizione,procedimenti,provvedimenti,regolamenti,allegato1,allegato2,allegato3,allegato4'),
	(6, 13, 'nome_ufficio,struttura,referente,referenti_contatti,email_riferimento,email_certificate,telefono,fax,desc_att,orari'),
	(7, 3, 'tit,referente,ruolo,incarico,determinato,uffici,ruolo_politico,allegato_nomina,telefono,mobile,fax,email,email_cert,curriculum,retribuzione,note'),
	(8, 22, 'tipologia,oggetto,sede_provincia,sede_comune,sede_indirizzo,data_attivazione,data_scadenza,orario_scadenza,spesa_prevista,spese_fatte,dipendenti_assunti,struttura,descrizione,calendario_prove,concorso_collegato,allegato1,allegato2,alelgato3,allegato4,allegato5,allegato6,allegato7,allegato8,allegato9,allegato10,allegato11,allegato12,allegato13,allegato14,allegato15,allegato16,allegato17,allegato18,allegato19,allegato20'),
	(9, 16, 'nome,referente_proc,referente_prov,resp_sost,ufficio_def,personale_proc,ufficio,descrizione,costi,norme,termine,area,link_servizio,tempi_servizio'),
	(10, 28, 'oggetto,tipo,struttura,responsabile,data,contenuto,spesa,estremi,allegato1,allegato2,allegato3,allegato4'),
	(11, 11, 'contratto,denominazione_aggiudicatrice,dati_aggiudicatrice,tipo_amministrazione,sede_provincia,sede_comune,sede_indirizzo,struttura,senza_importo,valore_base_asta,valore_importo_aggiudicazione,importo_liquidato,data_attivazione,data_scadenza,data_scadenza_esito,data_inizio_lavori,data_lavori_fine,requisiti_qualificazione,codice_cpv,codice_scp,url_scp,cig,bando_collegato,oggetto,dettagli,scelta_contraente,note_scelta,elenco_partecipanti,elenco_aggiudicatari,allegato1,allegato2,allegato3,allegato4,allegato5,allegato6,allegato7,allegato8,allegato9,allegato10,allegato11,allegato12,allegato13,allegato14,allegato15,allegato16,allegato17,allegato18,allegato19,allegato20,allegato21,allegato22'),
	(12, 29, 'nome,tipologia,anno,descrizione,allegato1,allegato2,allegato3,allegato4,allegato5,allegato6,allegato7,allegato8,allegato9,allegato10'),
	(13, 5, 'titolo,procedimenti,allegato,allegato_1,descrizione_mod'),
	(14, 44, 'ragione,tipologia,descrizione,misura,durata,oneri_anno,rappresentanti,incarichi_trattamento,indirizzo_web,bilancio,bilancio_allegato,dic_inconferibilita,dic_incompatibilita'),
	(15, 43, 'nome,tipologia,presidente,segretari,descrizione,membri,immagine,telefono,fax,indirizzo,email'),
	(16, 33, 'id_ente,id_sezione_etrasp,html_generico,modulistica_tit,modulistica,modulistica_opz,normativa_tit,normativa,normativa_opz,referenti_tit,referenti,referenti_opz,regolamenti_tit,regolamenti,regolamenti_opz,procedimenti_tit,procedimenti,procedimenti_opz,provvedimenti_tit,provvedimenti,provvedimenti_opz,strutture_tit,strutture,strutture_opz,incarichi,incarichi_opz,incarichi_tit');
/*!40000 ALTER TABLE `regole_open_data_default` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione: 169 rows
DELETE FROM `regole_pubblicazione`;
/*!40000 ALTER TABLE `regole_pubblicazione` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione` (`id`, `id_sezione`, `id_regola_template`, `id_lingua`, `id_criterio`, `tipo_elemento`, `id_elemento`, `id_stile_elemento`, `id_stile_elemento_sottofamiglia`, `id_stile_elemento_speciale`, `css_id`, `css_classe`, `priorita`) VALUES
	(1, -1, 1, -1, 0, 'contenuto_automatico', 0, 42, 0, 0, '', '', 0),
	(3, 3, 1, 1, 0, 'paragrafo', 2, 18, 0, 0, '', '', 687),
	(6, 6, 1, 1, 0, 'paragrafo', 5, 18, 0, 0, '', '', 683),
	(7, 0, 1, 1, 0, 'paragrafo', 7, 18, 0, 0, '', '', 683),
	(8, 0, 1, 1, 0, 'oggetto', 1, 58, 20, 0, '', '', 684),
	(936, 766, 1, 1, 0, 'paragrafo', 786, 18, 0, 0, '', '', 8),
	(11, 8, 1, 1, 0, 'paragrafo', 9, 18, 0, 0, '', '', 681),
	(37, 16, 1, 1, 0, 'oggetto', 17, 38, 20, 68, '', '', 671),
	(13, 10, 1, 1, 0, 'paragrafo', 11, 18, 0, 0, '', '', 679),
	(15, 12, 1, 1, 0, 'paragrafo', 13, 18, 0, 0, '', '', 676),
	(16, 12, 1, 1, 0, 'oggetto', 3, 38, 20, 0, '', '', 678),
	(17, 12, 1, 1, 0, 'oggetto', 4, 19, 41, 72, '', '', 677),
	(21, 13, 1, 1, 0, 'paragrafo', 14, 18, 0, 0, '', '', 675),
	(20, 0, 17, 1, 0, 'oggetto', 7, 53, 49, 0, '', '', 1),
	(22, 14, 1, 1, 0, 'paragrafo', 15, 18, 0, 0, '', '', 674),
	(939, 766, 1, 1, 0, 'oggetto', 134, 38, 20, 0, '', '', 9),
	(24, 13, 1, 1, 0, 'oggetto', 9, 38, 61, 0, '', '', 676),
	(104, 22, 10, 1, 14, 'oggetto', 57, 19, 0, 72, '', '', 2),
	(88, -1, 24, -1, 0, 'contenuto_automatico', 0, 0, 0, 0, '', '', 0),
	(29, 15, 1, 1, 0, 'paragrafo', 16, 18, 0, 0, '', '', 671),
	(30, 15, 1, 1, 0, 'oggetto', 12, 84, 20, 0, '', '', 674),
	(31, 15, 1, 1, 0, 'oggetto', 13, 83, 20, 0, '', '', 675),
	(32, 15, 1, 1, 0, 'oggetto', 14, 65, 0, 68, '', '', 671),
	(33, 16, 1, 1, 0, 'paragrafo', 17, 18, 0, 0, '', '', 671),
	(34, 17, 1, 1, 0, 'paragrafo', 18, 18, 0, 0, '', '', 671),
	(35, 15, 1, 1, 0, 'oggetto', 15, 66, 0, 72, '', '', 672),
	(36, 17, 1, 1, 0, 'oggetto', 16, 38, 20, 0, '', '', 671),
	(39, 18, 1, 1, 0, 'paragrafo', 19, 18, 0, 0, '', '', 671),
	(132, 59, 1, 1, 0, 'oggetto', 62, 76, 0, 0, '', '', 637),
	(973, 806, 1, 1, 0, 'oggetto', 164, 76, 0, 72, '', '', 1),
	(108, 44, 1, 1, 0, 'paragrafo', 45, 18, 0, 0, '', '', 644),
	(840, 702, 1, 1, 0, 'oggetto', 103, 38, 0, 0, '', '', 19),
	(101, 22, 1, 1, 0, 'oggetto', 54, 19, 0, 72, '', '', 669),
	(54, 22, 1, 1, 0, 'oggetto', 26, 76, 0, 0, '', '', 670),
	(139, 50, 1, 1, 0, 'oggetto', 67, 76, 0, 0, '', '', 642),
	(53, 21, 1, 1, 0, 'oggetto', 25, 19, 0, 72, '', '', 670),
	(56, 26, 1, 1, 0, 'oggetto', 27, 38, 20, 0, '', '', 666),
	(57, 27, 1, 1, 0, 'paragrafo', 28, 18, 0, 0, '', '', 661),
	(58, 27, 1, 1, 0, 'oggetto', 28, 38, 20, 0, '', '', 664),
	(59, 27, 1, 1, 0, 'oggetto', 29, 19, 0, 72, '', '', 663),
	(963, 799, 1, 1, 117, 'oggetto', 156, 76, 0, 0, '', '', 5),
	(727, 633, 1, 1, 96, 'oggetto', 84, 76, 0, 0, '', '', 74),
	(64, 0, 1, 1, 0, 'oggetto', 33, 83, 20, 0, '', '', 686),
	(962, 798, 1, 1, 0, 'oggetto', 155, 76, 0, 0, '', '', 5),
	(731, 636, 1, 1, 96, 'oggetto', 87, 76, 0, 0, '', '', 71),
	(735, 566, 10, 1, 14, 'oggetto', 90, 64, 20, 0, '', '', 3),
	(74, 36, 1, 1, 0, 'paragrafo', 37, 18, 0, 0, '', '', 652),
	(75, 36, 1, 1, 0, 'oggetto', 36, 76, 0, 0, '', '', 651),
	(76, 0, 1, 1, 0, 'oggetto', 37, 84, 20, 0, '', '', 685),
	(77, 14, 1, 1, 0, 'oggetto', 38, 38, 20, 0, '', '', 675),
	(105, 14, 10, 1, 14, 'oggetto', 58, 133, 20, 0, '', '', 2),
	(968, 805, 1, 1, 0, 'oggetto', 160, 76, 0, 0, '', '', 5),
	(932, 50, 1, 1, 0, 'oggetto', 131, 19, 0, 72, '', '', 640),
	(83, 26, 1, 1, 0, 'oggetto', 42, 19, 0, 72, '', '', 664),
	(91, 39, 1, 1, 0, 'oggetto', 46, 38, 20, 0, '', '', 649),
	(92, 39, 1, 1, 0, 'oggetto', 47, 19, 0, 72, '', '', 648),
	(106, 14, 10, 1, 14, 'oggetto', 59, 133, 20, 0, '', '', 3),
	(95, 25, 1, 1, 0, 'oggetto', 50, 38, 20, 0, '', '', 666),
	(931, 65, 1, 1, 0, 'oggetto', 130, 19, 0, 72, '', '', 636),
	(930, 732, 1, 1, 0, 'oggetto', 129, 76, 0, 0, '', '', 9),
	(980, 809, 10, 1, 0, 'oggetto', 170, 38, 20, 0, '', '', 2),
	(972, 639, 10, 1, 14, 'oggetto', 163, 64, 20, 0, '', '', 4),
	(971, 639, 10, 1, 14, 'oggetto', 162, 64, 20, 0, '', '', 3),
	(959, 767, 1, 1, 0, 'oggetto', 152, 76, 20, 0, '', '', 8),
	(953, 718, 1, 1, 0, 'oggetto', 147, 19, 177, 0, '', '', 5),
	(954, 64, 1, 1, 0, 'oggetto', 148, 19, 177, 0, '', '', 5),
	(955, 719, 1, 1, 0, 'oggetto', 149, 19, 177, 0, '', '', 5),
	(942, 773, 1, 1, 0, 'oggetto', 137, 19, 0, 72, '', '', 5),
	(977, 808, 1, 1, 96, 'oggetto', 167, 76, 0, 0, '', '', 5),
	(131, 65, 1, 1, 0, 'oggetto', 61, 76, 0, 0, '', '', 637),
	(133, 60, 1, 1, 0, 'oggetto', 63, 76, 0, 0, '', '', 2),
	(134, 61, 1, 1, 97, 'oggetto', 64, 76, 0, 0, '', '', 637),
	(943, 788, 1, 1, 96, 'oggetto', 138, 76, 0, 0, '', '', 5),
	(136, 19, 1, 1, 97, 'oggetto', 65, 19, 0, 72, '', '', 671),
	(137, 66, 1, 1, 0, 'oggetto', 66, 76, 0, 0, '', '', 637),
	(140, 51, 1, 1, 0, 'oggetto', 68, 76, 0, 0, '', '', 642),
	(142, 8, 1, 1, 0, 'oggetto', 70, 38, 0, 0, '', '', 682),
	(841, 703, 1, 1, 0, 'oggetto', 104, 38, 20, 0, '', '', 19),
	(734, 566, 10, 1, 14, 'oggetto', 89, 64, 20, 0, '', '', 2),
	(732, 637, 1, 1, 61, 'oggetto', 88, 38, 20, 0, '', '', 70),
	(730, 635, 1, 1, 96, 'oggetto', 86, 76, 0, 0, '', '', 72),
	(970, 639, 10, 1, 14, 'oggetto', 161, 64, 20, 0, '', '', 2),
	(969, 805, 1, 1, 0, 'oggetto', 130, 19, 0, 72, '', '', 1),
	(152, 69, 1, 1, 0, 'paragrafo', 71, 18, 0, 0, '', '', 632),
	(153, 69, 1, 1, 0, 'oggetto', 79, 76, 0, 0, '', '', 635),
	(154, 69, 1, 1, 0, 'oggetto', 80, 19, 0, 72, '', '', 633),
	(155, 68, 1, 1, 0, 'oggetto', 81, 76, 0, 72, '', '', 635),
	(159, 7, 1, 1, 0, 'oggetto', 82, 38, 0, 0, '', '', 683),
	(160, 7, 1, 1, 0, 'oggetto', 83, 64, 20, 0, '', '', 682),
	(610, 522, 1, 1, 0, 'paragrafo', 524, 18, 0, 0, '', '', 179),
	(611, 523, 1, 1, 0, 'paragrafo', 525, 18, 0, 0, '', '', 178),
	(952, 793, 1, 1, 0, 'oggetto', 146, 19, 177, 0, '', '', 5),
	(634, 545, 1, 1, 0, 'pannello', 12, 56, 0, 0, '', '', 157),
	(960, 796, 1, 1, 0, 'oggetto', 153, 38, 0, 0, '', '', 5),
	(974, 807, 1, 1, 0, 'oggetto', 165, 76, 0, 72, '', '', 1),
	(754, -1, 54, -1, 0, 'contenuto_automatico', 0, 42, 0, 0, '', '', 0),
	(751, 15, 1, 1, 0, 'pannello', 20, 0, 0, 0, '', '', 673),
	(961, 797, 1, 1, 0, 'oggetto', 154, 76, 0, 0, '', '', 5),
	(935, 51, 1, 1, 0, 'oggetto', 133, 19, 0, 72, '', '', 641),
	(934, 765, 1, 1, 97, 'oggetto', 62, 76, 0, 0, '', '', 637),
	(773, 0, 66, 1, 0, 'oggetto', 97, 38, 0, 0, '', '', 1),
	(925, 0, 61, 1, 0, 'pannello', 34, 0, 0, 0, '', '', 1),
	(777, 663, 1, 1, 0, 'paragrafo', 664, 18, 0, 0, '', '', 49),
	(817, 604, 1, 1, 0, 'paragrafo', 700, 18, 0, 0, '', '', 23),
	(818, 605, 1, 1, 0, 'paragrafo', 701, 18, 0, 0, '', '', 23),
	(819, 606, 1, 1, 0, 'paragrafo', 702, 18, 0, 0, '', '', 23),
	(820, 607, 1, 1, 0, 'paragrafo', 703, 18, 0, 0, '', '', 23),
	(967, 803, 1, 1, 0, 'oggetto', 159, 76, 0, 0, '', '', 5),
	(928, 48, 1, 1, 98, 'oggetto', 127, 76, 0, 0, '', '', 10),
	(846, 708, 1, 1, 0, 'oggetto', 109, 38, 20, 0, '', '', 19),
	(845, 707, 1, 1, 0, 'oggetto', 108, 38, 0, 0, '', '', 19),
	(933, 68, 1, 1, 0, 'oggetto', 132, 19, 0, 72, '', '', 634),
	(733, 637, 1, 1, 0, 'pannello', 15, 119, 0, 0, '', '', 71),
	(729, 634, 1, 1, 96, 'oggetto', 85, 76, 0, 0, '', '', 73),
	(844, 706, 1, 1, 0, 'oggetto', 107, 38, 20, 0, '', '', 19),
	(843, 705, 1, 1, 0, 'oggetto', 106, 38, 20, 0, '', '', 19),
	(842, 704, 1, 1, 0, 'oggetto', 105, 38, 0, 0, '', '', 19),
	(736, 566, 10, 1, 14, 'oggetto', 91, 64, 20, 0, '', '', 4),
	(964, 566, 10, 1, 0, 'oggetto', 157, 181, 0, 0, '', '', 5),
	(965, 801, 1, 1, 0, 'oggetto', 158, 76, 0, 0, '', '', 5),
	(740, 640, 1, 1, 100, 'oggetto', 92, 76, 0, 72, '', '', 67),
	(741, 641, 1, 1, 100, 'oggetto', 93, 76, 0, 72, '', '', 66),
	(966, 25, 20, 1, 123, 'pannello', 42, 56, 0, 0, '', '', 1),
	(929, 48, 1, 1, 98, 'oggetto', 128, 19, 0, 72, '', '', 9),
	(923, 0, 54, 1, 0, 'pannello', 33, 172, 0, 0, '', '', 8),
	(951, 708, 1, 1, 0, 'oggetto', 145, 19, 20, 0, '', '', 20),
	(809, 688, 1, 1, 0, 'paragrafo', 693, 18, 0, 0, '', '', 24),
	(810, 688, 1, 1, 0, 'oggetto', 101, 38, 0, 0, '', '', 25),
	(812, 689, 92, 1, 0, 'pannello', 32, 43, 0, 0, '', '', 2),
	(813, -1, 92, -1, 0, 'contenuto_automatico', 0, 0, 0, 0, '', '', 0),
	(814, 690, 92, 1, 0, 'paragrafo', 695, 18, 0, 0, '', '', 1),
	(815, 690, 92, 1, 61, 'oggetto', 102, 38, 0, 0, '', '', 2),
	(950, 0, 54, 1, 0, 'pannello', 40, 178, 0, 0, '', '', 7),
	(949, 792, 1, 1, 0, 'oggetto', 144, 19, 0, 0, '', '', 5),
	(947, 791, 1, 1, 0, 'oggetto', 142, 76, 0, 0, '', '', 5),
	(948, 791, 1, 1, 0, 'oggetto', 143, 19, 0, 72, '', '', 5),
	(946, 724, 1, 1, 95, 'oggetto', 141, 19, 0, 72, '', '', 5),
	(940, 25, 1, 1, 0, 'oggetto', 135, 19, 0, 72, '', '', 665),
	(941, 773, 1, 1, 0, 'oggetto', 136, 76, 20, 0, '', '', 6),
	(976, 808, 1, 1, 96, 'oggetto', 166, 19, 0, 72, '', '', 1),
	(975, 566, 131, 1, 96, 'oggetto', 166, 19, 0, 72, '', '', 1),
	(937, 767, 1, 1, 0, 'paragrafo', 787, 18, 0, 0, '', '', 7),
	(938, 768, 1, 1, 0, 'paragrafo', 788, 18, 0, 0, '', '', 6),
	(979, 787, 131, 1, 96, 'oggetto', 169, 76, 0, 0, '', '', 1),
	(897, 752, 1, 1, 99, 'oggetto', 111, 19, 0, 72, '', '', 19),
	(898, 752, 1, 1, 99, 'oggetto', 112, 38, 20, 0, '', '', 19),
	(899, 751, 1, 1, 0, 'oggetto', 113, 38, 20, 0, '', '', 19),
	(927, 748, 1, 1, 0, 'oggetto', 126, 38, 20, 0, '', '', 19),
	(901, 749, 1, 1, 0, 'oggetto', 115, 76, 0, 0, '', '', 19),
	(905, 725, 1, 1, 95, 'oggetto', 116, 19, 0, 72, '', '', 19),
	(906, 725, 1, 1, 95, 'oggetto', 117, 76, 0, 0, '', '', 19),
	(907, 726, 1, 1, 95, 'oggetto', 118, 76, 0, 0, '', '', 19),
	(908, 726, 1, 1, 95, 'oggetto', 119, 19, 0, 72, '', '', 18),
	(945, 790, 1, 1, 96, 'oggetto', 140, 76, 0, 0, '', '', 5),
	(944, 789, 1, 1, 96, 'oggetto', 139, 76, 0, 0, '', '', 5),
	(912, 731, 1, 1, 0, 'oggetto', 121, 76, 20, 0, '', '', 19),
	(913, 700, 1, 1, 0, 'oggetto', 122, 76, 0, 0, '', '', 19),
	(981, 810, 1, 1, 0, 'oggetto', 171, 38, 0, 0, '', '', 1),
	(983, 799, 1, 1, 149, 'pannello', 43, 157, 0, 0, '', '', 3),
	(984, 790, 1, 1, 96, 'oggetto', 172, 19, 0, 72, '', '', 4),
	(985, 787, 10, 1, 0, 'oggetto', 173, 19, 0, 72, '', '', 2),
	(990, 813, 1, 1, 0, 'oggetto', 176, 19, 177, 0, '', '', 1),
	(988, 812, 10, 1, 0, 'oggetto', 174, 38, 20, 0, '', '', 2),
	(987, 811, 1, 1, 0, 'paragrafo', 791, 18, 0, 0, '', '', 1),
	(986, 605, 1, 1, 0, 'paragrafo', 790, 18, 0, 0, '', '', 22),
	(989, 812, 10, 1, 0, 'oggetto', 175, 19, 20, 0, '', '', 1),
	(993, 765, 1, 1, 97, 'oggetto', 65, 19, 0, 72, '', '', 1),
	(991, 60, 1, 1, 97, 'oggetto', 65, 19, 0, 72, '', '', 1),
	(992, 61, 1, 1, 97, 'oggetto', 65, 19, 0, 72, '', '', 1);
/*!40000 ALTER TABLE `regole_pubblicazione` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_modelli: 18 rows
DELETE FROM `regole_pubblicazione_modelli`;
/*!40000 ALTER TABLE `regole_pubblicazione_modelli` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_modelli` (`id`, `id_modello`, `id_elemento_riferimento`, `id_criterio`, `tipo_elemento`, `titolo_iniziale`, `id_stile_elemento`, `id_stile_elemento_sottofamiglia`, `id_stile_elemento_speciale`, `css_id`, `css_classe`, `priorita`) VALUES
	(1, 1, 6, 0, 'oggetto', '', 38, 49, 0, '', '', 1),
	(2, 2, 5, 0, 'oggetto', '', 38, 49, 0, '', '', 1),
	(3, 3, 2, 0, 'oggetto', '', 19, 93, 0, '', '', 1),
	(20, 5, 124, 0, 'oggetto', '', 133, 20, 0, '', '', 2),
	(5, 4, 6, 0, 'pannello', '', 105, 0, 0, '', '', 1),
	(6, 4, 11, 0, 'oggetto', '', 133, 20, 0, '', '', 3),
	(7, 4, 40, 0, 'oggetto', '', 133, 20, 0, '', '', 4),
	(8, 4, 41, 0, 'oggetto', '', 133, 90, 0, '', '', 5),
	(10, 5, 49, 0, 'oggetto', '', 133, 20, 0, '', '', 5),
	(11, 5, 45, 0, 'oggetto', '', 133, 20, 0, '', '', 1),
	(12, 5, 48, 0, 'oggetto', '', 133, 20, 0, '', '', 3),
	(13, 5, 52, 0, 'oggetto', '', 133, 20, 0, '', '', 6),
	(14, 6, 8, 0, 'oggetto', '', 133, 59, 0, '', '', 2),
	(15, 6, 55, 0, 'oggetto', '', 133, 59, 0, '', '', 3),
	(16, 6, 56, 0, 'oggetto', '', 133, 130, 0, '', '', 4),
	(17, 5, 60, 0, 'oggetto', '', 133, 20, 0, '', '', 4),
	(18, 4, 98, 0, 'oggetto', '', 162, 93, 0, '', '', 2),
	(21, 7, 151, 0, 'oggetto', '', 38, 20, 0, '', '', 1);
/*!40000 ALTER TABLE `regole_pubblicazione_modelli` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_modelli_applicazioni: 9 rows
DELETE FROM `regole_pubblicazione_modelli_applicazioni`;
/*!40000 ALTER TABLE `regole_pubblicazione_modelli_applicazioni` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_modelli_applicazioni` (`id_sezione`, `id_regola_pubblicazione_modello`, `id_elemento`, `proprieta_modello`) VALUES
	(0, 1, 6, ''),
	(0, 2, 5, ''),
	(0, 3, 2, ''),
	(12, 14, 8, ''),
	(12, 15, 55, ''),
	(12, 16, 56, ''),
	(0, 17, 60, ''),
	(0, 20, 124, ''),
	(709, 21, 151, '');
/*!40000 ALTER TABLE `regole_pubblicazione_modelli_applicazioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_modelli_open_data: 0 rows
DELETE FROM `regole_pubblicazione_modelli_open_data`;
/*!40000 ALTER TABLE `regole_pubblicazione_modelli_open_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `regole_pubblicazione_modelli_open_data` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_oggetti: 171 rows
DELETE FROM `regole_pubblicazione_oggetti`;
/*!40000 ALTER TABLE `regole_pubblicazione_oggetti` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_oggetti` (`id`, `tipologia`, `id_oggetto`, `id_criterio`, `nome`, `numero`, `proprieta`, `proprieta_sec`, `includi_speciale`, `condizione`, `se_vuoto`, `vista_tabella`) VALUES
	(1, 'richiamo_automatico', 10, 36, 'In Primo Piano', '1', '', '', 0, 'undefined', '', 0),
	(2, 'richiamo_automatico', 2, 6, '<a href="Javascript:apriLayer(\'comeFare\');" title="Come fare per">Come fare per</a>', '0', '', '', 0, 'undefined', '', 3),
	(3, 'elenco', 10, 13, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(4, 'ricerca', 10, 0, 'Ricerca in <strong>archivio</strong>', '0', 'semplice', 'nessuna', 0, '', '', 0),
	(5, 'richiamo_automatico', 7, 20, 'Banner colonna sinistra', '0', '', '', 0, 'undefined', '', 0),
	(6, 'richiamo_automatico', 7, 19, 'Banner colonna destra', '0', '', '', 0, 'undefined', '', 0),
	(7, 'richiamo_automatico', 9, 5, 'Galleria immagini', '1', '', '', 0, 'undefined', '', 0),
	(8, 'richiamo_automatico', 10, 23, 'Notizie e Comunicati <strong>correlati</strong>', '0', '', '', 0, 'undefined', '', 3),
	(9, 'elenco', 9, 1, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(10, 'richiamo_automatico', 9, 1, 'Altre immagini correlate', '0', '', '', 0, 'undefined', '', 0),
	(11, 'richiamo_automatico', 10, 18, 'Notizie correlate', '0', '', '', 0, 'undefined', '', 0),
	(12, 'richiamo_automatico', 6, 22, 'Prossimi <strong>Eventi</strong>', '2', '', '', 0, 'undefined', '<div class="noIstanze">Nessun evento da visualizzare</div>', 0),
	(13, 'richiamo_automatico', 6, 24, 'Ultimi <strong>Eventi</strong>', '2', '', '', 0, 'undefined', '<div class="noIstanze">Nessun evento da visualizzare</div>', 0),
	(14, 'speciale', 6, 16, 'Calendario <strong>Eventi</strong>', '0', '', 'undefined', 1, 'undefined', '', 0),
	(15, 'ricerca', 6, 17, 'Ricerca <strong>Eventi</strong>', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(16, 'elenco', 6, 65, 'Risultati ricerca', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(17, 'elenco', 6, 8, '', '0', 'nessuna', '', 1, 'undefined', '<div class="noIstanze">Nessun evento nel giorno che hai selezionato</div>', 0),
	(18, 'speciale', 6, 16, 'Calendario degli eventi', '0', '', 'undefined', 1, 'undefined', '', 0),
	(19, 'elenco', 4, 6, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono incarichi e consulenze da visualizzare</div>', 1),
	(20, 'elenco', 3, 6, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(55, 'richiamo_automatico', 6, 43, 'Eventi <strong>correlati</strong>', '4', '', '', 0, 'undefined', '', 3),
	(56, 'richiamo_automatico', 9, 44, 'Immagini <strong>correlate</strong>', '6', '', '', 0, 'undefined', '', 3),
	(54, 'ricerca', 16, 0, 'Ricerca procedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(24, 'ricerca', 13, 0, 'Cerca un ufficio', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(25, 'ricerca', 16, 0, 'Cerca un procedimento', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(26, 'elenco', 16, 127, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(27, 'elenco', 5, 115, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(28, 'elenco', 12, 6, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(29, 'ricerca', 12, 0, 'Cerca nell\'archivio di FAQ', '0', 'semplice', 'nessuna', 0, '', '', 0),
	(30, 'richiamo_automatico', 11, 1, '<a href="http://www.server-is2.it/caserta/pagina566_bandi-di-gara.html">Avvisi // <strong>Bandi</strong></a>', '5', '', '', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi di gara in scadenza questa settimana</div>', 0),
	(31, 'richiamo_automatico', 11, 31, 'Ultimi scaduti', '2', '', '', 0, 'undefined', '', 0),
	(32, 'ricerca', 11, 29, 'Cerca nell\'archivio dei concorsi gare e bandi', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(33, 'richiamo_automatico', 11, 30, 'Concorsi Gare e Bandi', '8', '', '', 0, 'undefined', '<div class="noIstanze">Non ci sono concorsi gare e bandi al momento attivi nel Comune di Ferentino</div>', 0),
	(34, 'elenco', 11, 6, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(35, 'ricerca', 11, 29, 'Ricerca', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(36, 'elenco', 8, 6, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(37, 'richiamo_automatico', 10, 37, '', '3', '', '', 0, 'undefined', '', 0),
	(38, 'richiamo_automatico', 2, 6, '', '0', '', '', 0, 'undefined', '', 0),
	(39, 'richiamo_automatico', 16, 38, 'Lista di procedimenti del comune in quest\'area', '0', '', '', 0, 'undefined', '', 0),
	(40, 'richiamo_automatico', 6, 39, 'Eventi correlati', '0', '', '', 0, 'undefined', '', 0),
	(41, 'richiamo_automatico', 9, 40, 'Immagini correlate', '4', '', '', 0, 'undefined', '', 0),
	(42, 'ricerca', 5, 0, 'Ricerca', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(53, 'richiamo_automatico', 13, 42, 'Strutture organizzative del comune', '0', '', '', 0, 'undefined', '', 0),
	(45, 'richiamo_scelta', 5, 0, 'Modulistica // <strong>Documenti utili</strong>', '0', '', 'undefined', 0, 'undefined', '', 0),
	(46, 'elenco', 19, 87, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(47, 'ricerca', 19, 39, 'Ricerca in archivio', '0', 'semplice', 'nessuna', 0, '', '', 0),
	(48, 'richiamo_scelta', 19, 0, 'Regolamenti <strong>del Comune</strong>', '0', '', 'undefined', 0, 'undefined', '', 0),
	(49, 'richiamo_scelta', 13, 0, 'Riferimenti // <strong>Contatti</strong>', '0', '', 'undefined', 0, 'undefined', '', 0),
	(50, 'elenco', 13, 42, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(51, 'richiamo_automatico', 12, 41, 'Domande e risposte correlate', '0', '', '', 0, 'undefined', '', 0),
	(52, 'richiamo_automatico', 12, 46, 'Domande <strong>frequenti</strong>', '4', '', '', 0, 'undefined', '', 0),
	(57, 'ricerca', 16, 0, 'Ricerca procedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(58, 'richiamo_automatico', 16, 38, 'Procedimenti in <strong>quest\'area tematica</strong>', '0', '', '', 0, 'undefined', '', 0),
	(59, 'richiamo_automatico', 12, 45, 'Domande <strong>frequenti</strong>', '0', '', '', 0, 'undefined', '', 0),
	(60, 'richiamo_scelta', 16, 0, 'Procedimenti // <strong>Servizi</strong>', '0', '', 'undefined', 0, 'undefined', '', 0),
	(61, 'elenco', 13, 6, 'Indirizzi email istituzionali del Comune', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(62, 'elenco', 4, 47, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun incarico da visualizzare</div>', 1),
	(63, 'elenco', 4, 48, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun incarico da visualizzare</div>', 1),
	(64, 'elenco', 4, 49, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun incarico da visualizzare</div>', 1),
	(65, 'ricerca', 4, 66, 'Ricerca incarichi e consulenze', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(66, 'elenco', 4, 127, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">La ricerca non ha prodotto risultati</div>', 1),
	(67, 'elenco', 3, 50, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(68, 'elenco', 3, 51, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(69, 'elenco', 3, 52, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(70, 'mappa', -7, 0, 'Mappa del Sito', '0', '', 'undefined', 1, 'undefined', '', 0),
	(71, 'elenco', 11, 28, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun concorso attualmente attivo</div>', 1),
	(72, 'elenco', 11, 33, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun concorso in archivio</div>', 1),
	(73, 'elenco', 11, 29, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono gare attive</div>', 1),
	(74, 'elenco', 11, 32, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono gare in archivio</div>', 1),
	(75, 'elenco', 11, 27, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi attivi</div>', 1),
	(76, 'elenco', 11, 34, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessun bando in archivio</div>', 1),
	(77, 'speciale', 3, 68, 'Rubrica referenti del Comune', '0', '', 'undefined', 1, 'undefined', '', 0),
	(78, 'ricerca', 3, 69, 'Ricerca referenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(79, 'elenco', 3, 109, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(80, 'ricerca', 3, 69, 'Ricerca in archivio', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(81, 'elenco', 3, 70, '', '0', 'nessuna', 'ordinabile', 1, 'undefined', '<div class="noIstanze">Nessun referente per la lettera selezionata</div>', 1),
	(82, 'webapplication', 20, 0, '', '0', 'aggiunta', 'undefined', 0, 'undefined', 'La tua richiesta è stata inviata con successo.', 0),
	(83, 'richiamo_automatico', 13, 42, 'Struttura organizzativa da contattare', '0', '', '', 0, 'undefined', '', 0),
	(84, 'elenco', 11, 54, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi di lavori attivi.</div>', 1),
	(85, 'elenco', 11, 55, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi di servizi attivi.</div>', 1),
	(86, 'elenco', 11, 56, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi di forniture attivi.</div>', 1),
	(87, 'elenco', 11, 57, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi scaduti.</div>', 1),
	(88, 'richiamo_automatico', 11, 62, '', '1', '', '', 0, 'undefined', '', 0),
	(89, 'richiamo_automatico', 11, 59, 'Avvisi relativi', '0', '', '', 0, 'undefined', '', 0),
	(90, 'richiamo_automatico', 11, 60, 'Esiti relativi', '0', '', '', 0, 'undefined', '', 0),
	(91, 'richiamo_automatico', 11, 58, 'Procedura relativa', '0', '', '', 0, 'undefined', '', 0),
	(92, 'elenco', 22, 135, '', '0', 'ricerca completa nord', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono Concorsi attivi</div>', 1),
	(93, 'elenco', 22, 64, '', '0', 'ricerca completa nord', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono Concorsi scaduti</div>', 1),
	(94, 'elenco', 3, 50, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(95, 'richiamo_automatico', 6, 22, 'Agenda eventi<div class="sottoTitolo">i prossimi eventi in calendario</div>', '2', '', '', 1, 'undefined', '<div class="noIstanze">Non ci sono eventi in programma</div>', 0),
	(96, 'richiamo_scelta', 24, 0, 'Menu banner', '0', '5-3-8-4-', 'undefined', 0, 'undefined', '', 3),
	(97, 'richiamo_scelta', 7, 0, 'Area Banner', '0', '', 'undefined', 0, 'undefined', '', 0),
	(98, 'richiamo_automatico', -2, 11, '', '0', '', '', 0, 'undefined', '', 0),
	(99, 'richiamo_scelta', 24, 0, '', '0', '7-6-10-', 'undefined', 0, 'undefined', '', 3),
	(125, 'richiamo_automatico', 10, 36, '<a href="http://www.server-is2.it/caserta/pagina12_notizie-dal-comune.html">In Evidenza</a>', '3', '', '', 0, 'undefined', '', 0),
	(100, 'elenco', 4, 69, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(101, 'webapplication', 26, 0, '', '0', 'aggiunta', 'undefined', 0, 'undefined', 'La richiesta di appuntamento è stata inviata al Comune.<br />\rLe verrà inviata una email all\'indirizzo indicato con la data e l\'ora del suo appuntamento.', 0),
	(102, 'webapplication', 26, 0, '', '0', 'modifica', 'undefined', 0, 'undefined', 'L\'appuntamento è stato modificato.', 0),
	(103, 'richiamo_automatico', 3, 71, '', '0', '', '', 0, 'undefined', '', 0),
	(104, 'richiamo_automatico', 3, 72, '', '0', '', '', 0, 'undefined', '', 0),
	(105, 'richiamo_automatico', 3, 74, '', '0', '', '', 0, 'undefined', '', 0),
	(106, 'richiamo_automatico', 3, 73, '', '0', '', '', 0, 'undefined', '', 0),
	(107, 'richiamo_automatico', 3, 75, '', '0', '', '', 0, 'undefined', '', 0),
	(108, 'richiamo_automatico', 3, 76, '', '0', '', '', 0, 'undefined', '', 0),
	(109, 'richiamo_automatico', 3, 77, '', '0', '', '', 0, 'undefined', '', 0),
	(110, 'richiamo_scelta', 13, 0, 'Ufficio responsabile', '0', '52-', 'undefined', 0, 'undefined', '', 0),
	(111, 'ricerca', 27, 0, 'Cerca nella normativa', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(112, 'elenco', 27, 143, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(113, 'richiamo_automatico', 19, 83, '', '0', '', '', 0, 'undefined', '', 0),
	(114, 'elenco', 4, 78, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(115, 'elenco', 3, 79, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(116, 'ricerca', 28, 725, 'Ricerca nei provvedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(117, 'elenco', 28, 81, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(118, 'elenco', 28, 80, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(119, 'ricerca', 28, 726, 'Ricerca nei provvedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(120, 'richiamo_scelta', 19, 0, '', '0', '', 'undefined', 0, 'undefined', '', 0),
	(121, 'elenco', 29, 85, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(122, 'elenco', 30, 110, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono oneri informativi da visualizzare</div>', 1),
	(123, 'da_impostare', 26, 0, 'Regole da impostare', '0', '', 'nessuna', 0, '', '', 0),
	(124, 'richiamo_scelta', 27, 0, 'Riferimenti <strong>normativi</strong>', '0', '', 'undefined', 0, 'undefined', '', 0),
	(126, 'richiamo_automatico', 3, 84, '', '0', '', '', 0, 'undefined', '', 0),
	(127, 'elenco', 38, 127, 'Elenco delle sovvenzioni, sussidi ed altri vantaggi economici', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(128, 'ricerca', 38, 0, 'Ricerca in archivio', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(129, 'elenco', 29, 86, 'Non sono presenti documenti in quest\'area', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(130, 'ricerca', 13, 65, 'Cerca una struttura', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(131, 'ricerca', 3, 50, 'Cerca nei dirigenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(132, 'ricerca', 3, 69, 'Cerca nella dotazione organica', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(133, 'ricerca', 3, 51, 'Cerca nelle posizioni organizzative', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(134, 'richiamo_automatico', 19, 88, '', '0', '', '', 0, 'undefined', '', 0),
	(135, 'ricerca', 13, 773, 'Cerca negli uffici', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(136, 'elenco', 13, 6, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 0),
	(137, 'ricerca', 13, 773, 'Cerca negli uffici', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(138, 'elenco', 11, 91, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(139, 'elenco', 11, 89, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(140, 'elenco', 11, 90, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(141, 'ricerca', 28, 791, 'Ricerca nei provvedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(142, 'elenco', 28, 127, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(143, 'ricerca', 28, 791, 'Ricerca nei provvedimenti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(144, 'richiamo_automatico', 3, 94, '', '0', '', '', 0, 'undefined', '', 0),
	(145, 'richiamo_automatico', 43, 102, '', '0', '', '', 0, 'undefined', '', 0),
	(146, 'richiamo_automatico', 43, 103, '', '0', '', '', 0, 'undefined', '', 0),
	(147, 'richiamo_automatico', 44, 106, '', '0', '', '', 0, 'undefined', '', 0),
	(148, 'richiamo_automatico', 44, 104, '', '0', '', '', 0, 'undefined', '', 0),
	(149, 'richiamo_automatico', 44, 105, '', '0', '', '', 0, 'undefined', '', 0),
	(150, 'da_impostare', 28, 0, 'Regole da impostare', '0', '', 'nessuna', 0, '', '', 0),
	(151, 'richiamo_automatico', 33, 108, '', '0', '', '', 0, 'undefined', '', 0),
	(152, 'elenco', 30, 111, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non sono presenti nuovi obblighi amministrativi</div>', 1),
	(153, 'richiamo_automatico', 3, 112, '', '0', '', '', 0, 'undefined', '', 0),
	(154, 'elenco', 3, 113, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">L\'archivio storico è attualmente vuoto</div>', 1),
	(155, 'elenco', 3, 114, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">L\'archivio storico è attualmente vuoto</div>', 1),
	(156, 'elenco', 45, 116, '', '0', 'nessuna', '', 0, 'undefined', '', 1),
	(176, 'richiamo_automatico', 43, 155, '', '0', '', '', 0, 'undefined', '', 0),
	(157, 'richiamo_automatico', 11, 118, 'Somme liquidate dopo la Pubblicazione', '0', '', '', 0, 'undefined', '', 1),
	(158, 'elenco', 11, 121, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(159, 'elenco', 11, 124, '', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(160, 'elenco', 13, 151, 'Posta elettronica certificata del Comune', '0', 'nessuna', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Nessuna informazione da visualizzare. Se provieni da una ricerca, riprova con altri parametri.</div>', 1),
	(161, 'richiamo_automatico', 22, 129, 'Avvisi relativi al concorso', '0', '', '', 0, 'undefined', '', 0),
	(162, 'richiamo_automatico', 22, 130, 'Esiti relativi al concorso', '0', '', '', 0, 'undefined', '', 0),
	(163, 'richiamo_automatico', 22, 131, 'Bando di Concorso relativo', '0', '', '', 0, 'undefined', '', 0),
	(164, 'elenco', 22, 133, '', '0', 'ricerca completa nord', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono Avvisi attivi</div>', 1),
	(165, 'elenco', 22, 134, '', '0', 'ricerca completa nord', 'ordinabile', 0, 'undefined', '<div class="noIstanze">Non ci sono Esiti attivi</div>', 1),
	(166, 'ricerca', 11, 808, 'Ricerca Bandi di gara e contratti in pubblicazione', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(167, 'elenco', 11, 139, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Nessun elemento trovato</div>', 1),
	(168, 'da_impostare', 26, 0, 'Regole da impostare', '0', '', 'nessuna', 0, '', '', 0),
	(169, 'elenco', 11, 140, '', '0', 'nessuna', '', 0, 'undefined', '<div class="noIstanze">Non ci sono bandi attivi.</div>', 1),
	(170, 'elenco', 3, 147, '', '0', 'nessuna', '', 0, 'undefined', '', 0),
	(171, 'richiamo_automatico', 3, 148, '', '0', '', '', 0, 'undefined', '', 0),
	(172, 'ricerca', 11, 790, 'Ricerca in Bandi di gara e contratti', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(173, 'ricerca', 11, 808, 'Ricerca Bandi di gara e contratti in pubblicazione', '0', 'complessa', 'nessuna', 0, '', '', 0),
	(174, 'richiamo_automatico', 3, 153, '', '0', '', '', 0, 'undefined', '', 0),
	(175, 'richiamo_automatico', 43, 154, '', '0', '', '', 0, 'undefined', '', 0);
/*!40000 ALTER TABLE `regole_pubblicazione_oggetti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_oggetti_criteri: 155 rows
DELETE FROM `regole_pubblicazione_oggetti_criteri`;
/*!40000 ALTER TABLE `regole_pubblicazione_oggetti_criteri` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_oggetti_criteri` (`id`, `tipo`, `nome`, `descrizione`, `compatibilita`, `id_oggetto`, `campi`, `condizioni`, `valori`, `oggetti_prov`, `contesti`, `ord_campi`, `ord_senso`, `ord_oggetti_prov`, `query`, `query_order`) VALUES
	(1, 'manuale', 'Ultime istanze inserite', 'Pubblicazione delle ultime istanze inserite relativamente a questo oggetto. Questo criterio non usa nè le sezioni di appartenza dell\'istanza, nè dei tags.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ORDER BY data_creazione DESC'),
	(2, 'manuale', 'Ultime inserite nella sezione', 'Pubblicazione delle ultime istanze inserite associate a alla sezione in cui si è pubblicati il richiamo. Questo criterio non tiene conto dell\'eventuale associazione dell\'istanza a tag.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ORDER BY data_creazione DESC'),
	(3, 'manuale', 'Ultime istanze modificate', 'Pubblicazione delle ultime istanze modificate relativamente a questo oggetto. Questo criterio non usa nè le sezioni di appartenza dell\'istanza, nè dei tags.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ORDER BY ultima_modifica DESC'),
	(4, 'manuale', 'Ultime modificate nella sezione', 'Pubblicazione delle ultime istanze modificate associate a alla sezione in cui si è pubblicati il richiamo. Questo criterio non tiene conto dell\'eventuale associazione dell\'istanza a tag.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ORDER BY ultima_modifica DESC'),
	(5, 'manuale', 'Estrazione casuale', 'Criterio di sistema che estrae casualmente un numero scelto di istanze. Utilizzabile con tutti gli oggetti, quando non si ha necessità di controllare altre condizioni.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ORDER BY RAND()'),
	(6, 'manuale', 'PAT (prof ente) - Criterio Fantasma per elenchi', 'Questo criterio non applica alcuna logica di prelievo: è quindi l\'ideale per gli elenchi di oggetti che possono mantenere l\'ordinabilità tramite interfaccia.', 'generale', 0, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', ''),
	(7, 'manuale', 'Pubblica le istanze associate alla sezione in navigazione', 'Pubblicazione delle istanze associate alla sezione attualmente in navigazione. Questo criterio può essere usato su tutti gli oggetti con un campo riferimento a sezioni (multiplo o singolo) chiamato rifsez.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '(rifsez LIKE \'%,{idSezione},%\' OR rifsez = \'{idSezione}\' OR rifsez LIKE \'{idSezione},%\' OR rifsez LIKE \'%,{idSezione}\')', NULL),
	(8, 'manuale', 'Pubblicazione di istanze "calendario" in corso di svolgimento', 'Pubblica le istanze di un oggetto con proprieta calendario quando è pubblicata l\'interfaccia. L\'oggeto deve avere i campi data_inizio e data_fine.', 'generale', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'data_inizio<=({giornoSet}+63200) AND data_fine>={giornoSet}', 'ORDER BY data_inizio'),
	(9, 'manuale', 'Estrazione casuale tra i sondaggi attivi', 'Criterio di sistema che estrae casualmente un numero scelto di sondaggi tra quellli attivi.', 'sondaggio', -8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'data_attivazione<{oraCorrente} AND data_scadenza>{oraCorrente} AND proprieta=\'sondaggio\'', 'ORDER BY RAND()'),
	(10, 'manuale', 'Pubblica i sondaggi attivi', 'Pubblica tutti i sondaggi attivi ordinati cronologicamene in base alla loro attivazione.', 'sondaggio', -8, '', '', '', NULL, NULL, NULL, NULL, NULL, 'data_attivazione<{oraCorrente} AND data_scadenza>{oraCorrente} AND proprieta=\'sondaggio\'', 'ORDER BY data_attivazione DESC'),
	(11, 'manuale', 'Sezioni correlate via tag', 'Pubblica le sezioni con tags correlati alla sezione che si sta navigando.', 'sezione', -2, '', '', '', '', '', '', '', '', '*sezioneNavigazione[\'tags\']*OR*(tags LIKE \'%,{indice},%\' OR tags = \'{indice}\' OR tags LIKE \'{indice},%\' OR tags LIKE \'%,{indice}\')|AND permessi_lettura=\'N/A\' AND id!={idSezione} AND id_riferimento!={idSezione} AND id_riferimento!={idSezioneRiferimento} AND id!={idSezioneRiferimento} AND \'{sezioneNavigazione["tags"]}\' != \'\'', ''),
	(12, 'manuale', 'Informazioni attive (criterio generico) senza forzatura ordine', '', 'informativa', 0, '', '', '', '', '', '', '', '', 'data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL)', ''),
	(13, 'manuale', 'Informazioni attive (criterio generico) con forzatura ordine (data)', '', 'informativa', 0, '', '', '', '', '', '', '', '', 'data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL)', 'ORDER BY data DESC'),
	(14, 'manuale', 'Visualizza elemento in lettura completa', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{idOggetto}>0 and {idDocumento}>0', ''),
	(15, 'manuale', 'Visualizza elemento se presente POST', '', 'elemento', 0, '', '', '', '', '', '', '', '', 'count({_POST}) != 0', ''),
	(16, 'manuale', 'Visualizza elemento se assente POST', '', 'elemento', 0, '', '', '', '', '', '', '', '', 'count({_POST}) == 0', ''),
	(17, 'manuale', 'Visualizza elemento se non si navigano le categorie', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{idCategoria} == 0', ''),
	(18, 'manuale', 'Notizie attive correlate via tag alla sezione ordinate per data', '', 'informativa', 10, '', '', '', '', '', '', '', '', '*sezioneNavigazione[\'tags\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL) AND \'{sezioneNavigazione["tags"]}\'!=\'\'', 'ORDER BY data DESC'),
	(19, 'manuale', 'Banner nella colonna destra', '', 'informativa', 7, '', '', '', '', '', '', '', '', '((sezioni LIKE \'%,{idSezione},%\' OR sezioni = \'{idSezione}\' OR sezioni LIKE \'{idSezione},%\' OR sezioni LIKE \'%,{idSezione}\') OR visualizzato=1) AND colonna=\'colonna destra\'', 'ORDER BY priorita'),
	(20, 'manuale', 'Banner nella colonna sinistra', '', 'informativa', 7, '', '', '', '', '', '', '', '', '((sezioni LIKE \'%,{idSezione},%\' OR sezioni = \'{idSezione}\' OR sezioni LIKE \'{idSezione},%\' OR sezioni LIKE \'%,{idSezione}\') OR visualizzato=1) AND colonna=\'colonna sinistra\'', 'ORDER BY priorita'),
	(21, 'manuale', 'Eventi per la data calendario scelta', '', 'informativa', 6, '', '', '', '', '', '', '', '', 'data_inizio<=({giornoSet}+63200) AND data_fine>={giornoSet}', ''),
	(22, 'manuale', 'Prossimi eventi da svolgere', '', 'informativa', 6, '', '', '', '', '', '', '', '', 'data_fine>{oggi}', 'ORDER BY data_inizio'),
	(23, 'manuale', 'Notizie attive correlate via tag alla lettura completa ordinate per data', '', 'informativa', 10, '', '', '', '', '', '', '', '', '*oggettoReview[\'argomenti\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL) AND \'{oggettoReview["argomenti"]}\'!=\'\' AND \'{oggettoReview["id"]}\'!= id', 'ORDER BY data DESC'),
	(24, 'manuale', 'Ultimi eventi già svolti', 'Estre gli eventi già svolti ordinandoli dall\'ultimo svolto in poi', 'informativa', 6, '', '', '', '', '', '', '', '', 'data_fine<{oggi}', 'ORDER BY data_fine DESC'),
	(43, 'manuale', 'Eeventi correlati via tag alla lettura completa ordinate per data', '', 'informativa', 6, '', '', '', '', '', '', '', '', '*oggettoReview[\'argomenti\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND \'{oggettoReview["argomenti"]}\'!=\'\' AND \'{oggettoReview["id"]}\'!= id', 'ORDER BY data_inizio DESC'),
	(26, 'guidato', 'Uffici ordinati per servizio', '', 'informativa', 13, '', '', '', '', '', 'servizio', '', '13', '', ''),
	(27, 'manuale', 'PAT (prof ente) - Pubblica i bandi attivi', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'bandi ed inviti\' AND \r\n(data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza=\'\') AND \r\ndata_attivazione <= {domani} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(28, 'manuale', 'PAT (prof ente) - Pubblica i concorsi attivi', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'concorso\' AND \r\ndata_scadenza >= {oggi} AND \r\ndata_attivazione <= {oggi} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(29, 'manuale', 'PAT (prof ente) - Pubblica le gare attive', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'gara\' AND \r\ndata_scadenza >= {oggi} AND \r\ndata_attivazione <= {domani} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(30, 'manuale', 'PAT (prof ente) - Pubblica tutti i concorsi gare e bandi attivi', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ndata_scadenza >= {oggi} AND \r\ndata_attivazione <= {domani} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(31, 'manuale', 'PAT (prof ente) - Pubblica tutti i concorsi gare e bandi scaduti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ndata_scadenza < {oggi} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(32, 'manuale', 'PAT (prof ente) - Pubblica le gare scadute', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'gara\' AND \r\ndata_scadenza < {oggi} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(33, 'manuale', 'PAT (prof ente) - Pubblica i concorsi scaduti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'concorso\' AND \r\ndata_scadenza < {oggi} AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(34, 'manuale', 'PAT (prof ente) - Pubblica i bandi scaduti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'bandi ed inviti\' AND data_scadenza < {oggi} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(35, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi di gara in scadenza questa settimana', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\n(data_scadenza >= {inizioSettimana} AND data_scadenza < {fineSettimana}) AND data_attivazione <= {oggi} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(36, 'manuale', 'Notizie e Comunicati attivi in primo piano', '', 'informativa', 10, '', '', '', '', '', '', '', '', 'data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL) AND primopiano=1', 'ORDER BY data DESC'),
	(37, 'manuale', 'Notizie e Comunicati attivi NON in primo piano', '', 'informativa', 10, '', '', '', '', '', '', '', '', 'data_inizio<{domani} AND  (data_fine>={oggi} OR data_fine IS NULL) AND (primopiano !=1 OR primopiano IS NULL)', 'ORDER BY data DESC'),
	(38, 'manuale', 'Procedimenti nell\'area tematica', '', 'informativa', 16, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\narea LIKE \'%,{idDocumento},%\' OR area LIKE \'%,{idDocumento}\' OR area LIKE \'{idDocumento},%\' OR area = \'{idDocumento}\'', 'ORDER BY nome'),
	(39, 'manuale', 'Eventi correlati via tag alla sezione ordinate per data', '', 'informativa', 6, '', '', '', '', '', '', '', '', '*sezioneNavigazione[\'tags\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND \'{sezioneNavigazione["tags"]}\'!=\'\'', 'ORDER BY data_fine DESC'),
	(40, 'manuale', 'Photogallery correlati via tag alla sezione ordinate casualmente', '', 'informativa', 9, '', '', '', '', '', '', '', '', '*sezioneNavigazione[\'tags\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND \'{sezioneNavigazione["tags"]}\'!=\'\'', 'ORDER BY RAND()'),
	(41, 'manuale', 'FAQ nell\'area tematica', '', 'informativa', 12, '', '', '', '', '', '', '', '', 'area LIKE \'%,{idDocumento},%\' OR area = \'%,{idDocumento}\' OR area LIKE \'{idDocumento},%\' OR area LIKE \'{idDocumento}\'', 'ORDER BY domanda'),
	(42, 'manuale', 'PAT (prof ente) - Uffici 1 livello', '', 'informativa', 13, '', '', '', '', '', '', '', '', '(struttura = \'\' OR struttura = 0 OR struttura IS NULL) AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND articolazione=1', 'ORDER BY ordine,nome_ufficio'),
	(44, 'manuale', 'Photogallery correlate via tag alla lettura completa ordinate casualmente', '', 'informativa', 9, '', '', '', '', '', '', '', '', '*oggettoReview[\'argomenti\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND \'{oggettoReview["argomenti"]}\'!=\'\' AND \'{oggettoReview["id"]}\'!= id', 'ORDER BY RAND()'),
	(45, 'manuale', 'FAQ dei procedimenti caricati nelle aree tematiche', '', 'informativa', 12, '', '', '', '', '', '', '', '', '*configurazione[\'procedimenti_caricati\']*OR*(procedimento LIKE \'%,{indice},%\' OR procedimento = \'{indice}\' OR procedimento LIKE \'{indice},%\' OR procedimento LIKE \'%,{indice}\')|AND \'{configurazione["procedimenti_caricati"]}\'!=\'\'', 'ORDER BY domanda'),
	(46, 'manuale', 'FAQ correlate via tag alla sezione ordinate per domanda', '', 'informativa', 12, '', '', '', '', '', '', '', '', '*sezioneNavigazione[\'tags\']*OR*(argomenti LIKE \'%,{indice},%\' OR argomenti = \'{indice}\' OR argomenti LIKE \'{indice},%\' OR argomenti LIKE \'%,{indice}\')|AND (permessi_lettura=\'N/A\' OR permessi_lettura=\'HM\') AND \'{sezioneNavigazione["tags"]}\'!=\'\'', 'ORDER BY domanda'),
	(47, 'manuale', 'PAT (prof ente) - Incarichi dipendenti interni', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo_incarico = \'incarichi dipendenti interni\'\r\nAND dirigente != 1', 'ORDER BY inizio_incarico DESC'),
	(48, 'manuale', 'PAT (prof ente) - Incarichi dipendenti esterni', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipo_incarico=\'incarichi dipendenti esterni\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY inizio_incarico DESC'),
	(49, 'manuale', 'PAT (prof ente) - Incarichi soggetti esterni', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo_incarico != \'incarichi dipendenti interni\'\r\nAND dirigente != 1', 'ORDER BY inizio_incarico DESC'),
	(50, 'manuale', 'PAT (prof ente) - Dirigenti del comune', '', 'informativa', 3, '', '', '', '', '', '', '', '', '(ruolo=\'Dirigente\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND archivio != 1)\rOR\r(ruolo=\'Dirigente D.U.\' AND \'{entePubblicato[tipo_ente]}\' = \'17\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND archivio != 1)\r', 'ORDER BY referente'),
	(51, 'manuale', 'PAT (prof ente) - Posizioni amministrative', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'ruolo = \'P.O.\' AND  id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND archivio != 1', 'ORDER BY referente'),
	(52, 'guidato', 'Incaricati politici', '', 'informativa', 3, '', '', '', '', '', '', '', '', '', ''),
	(53, 'manuale', 'MENU - Pubblica il menu che sto navigando', '', 'generale', 0, '', '', '', '', '', '', '', '', '{sezione["id_riferimento"]} == $arraySitemap[{livelloSitemap}-1]["id"] OR $sezioneNavigazione["id"]==0', ''),
	(54, 'manuale', 'PAT (prof ente) - Gare e Contratti - Bandi di lavori attivi ordinati per data di pubblicazione', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rcontratto=\'lavori\' AND tipologia = \'bandi ed inviti\' AND data_attivazione < {domani} AND (data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza=\'\') AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(55, 'manuale', 'PAT (prof ente) - Gare e Contratti - Bandi di servizi attivi ordinati per data di pubblicazione', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rcontratto=\'servizi\' AND tipologia = \'bandi ed inviti\' AND data_attivazione < {domani} AND (data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza=\'\') AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(56, 'manuale', 'PAT (prof ente) - Gare e contratti - Bandi di forniture attivi ordinati per data di pubblicazione', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rcontratto=\'forniture\' AND tipologia = \'bandi ed inviti\' AND data_attivazione < {domani} AND (data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza=\'\') AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione'),
	(57, 'manuale', 'PAT (prof ente) - Gare e contratti - Bandi e avvisi di gara scaduti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'bandi ed inviti\' AND data_scadenza < {oggi} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(58, 'manuale', 'PAT (prof ente) - Gare e Contratti - Bando dell\'avviso/esito in lettura completa', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid = 0{oggettoReview[bando_collegato]} AND tipologia = \'bandi ed inviti\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(59, 'manuale', 'PAT (prof ente) - Gare e contratti - Avvisi del bando in lettura completa', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rbando_collegato = 0{_GET[id_doc]}\rAND \rtipologia = \'avvisi pubblici\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')\rAND \r(bando_collegato IS NOT NULL AND bando_collegato!=0)', 'ORDER BY data_attivazione DESC'),
	(60, 'manuale', 'PAT (prof ente) - Gare e contratti - Esiti del bando in lettura completa', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nbando_collegato = 0{_GET[id_doc]}\rAND \rtipologia = \'esiti\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')\r\nAND \r\n(bando_collegato IS NOT NULL AND bando_collegato!=0)', 'ORDER BY data_attivazione DESC'),
	(61, 'manuale', 'Visualizza se ho id_doc in GET', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{_GET[id_doc]} > 0', ''),
	(62, 'manuale', 'PAT (prof ente) - Informazioni di indicizzazione di un bando avviso esito', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid = 0{_GET[id_doc]} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', ''),
	(63, 'manuale', 'PAT (prof ente) - Concorsi attivi', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r data_attivazione<{domani} AND  (data_scadenza>={oggi} OR data_scadenza IS NULL)  AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(64, 'manuale', 'PAT (prof ente) - Concorsi scaduti', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \rtipologia = \'concorsi\' AND \rdata_scadenza < {oggi} AND  id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza DESC'),
	(65, 'guidato', 'Eventi ordinati per data inizio', '', 'informativa', 6, '', '', '', '', '', 'data_inizio', '', '6', '', ''),
	(66, 'manuale', 'MENU - Menu il comune', '', 'elemento', 0, '', '', '', '', '', '', '', '', '2 == $arraySitemap[{livelloSitemap}-1]["id"]', ''),
	(67, 'manuale', 'MENU - Menu Vivere Caserta', '', 'elemento', 0, '', '', '', '', '', '', '', '', '650 == $arraySitemap[{livelloSitemap}-1]["id"]', ''),
	(68, 'manuale', 'MENU - Menu informazioni', '', 'elemento', 0, '', '', '', '', '', '', '', '', '6 == $arraySitemap[{livelloSitemap}-1]["id"]', ''),
	(69, 'manuale', 'PAT (prof ente) - Albi beneficiari provvidenze', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\npubblica_su_sovvenzioni=1 AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', ''),
	(70, 'manuale', 'PAT (prof ente) - Referenti che non sono Incaricati politici', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'ruolo != \'Incaricato politico\' AND vis_elenchi=1 AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')  AND archivio != 1', 'ORDER BY referente'),
	(71, 'manuale', 'PAT (prof ente) - Organo politico - Sindaco', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'sindaco\' OR organo LIKE \'%,sindaco,%\' OR organo  LIKE \'sindaco,%\' OR organo LIKE \'%,sindaco\' ) AND archivio != 1', 'ORDER BY referente'),
	(72, 'manuale', 'PAT (prof ente) - Organo politico - Giunta', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'giunta comunale\' OR organo LIKE \'%,giunta comunale,%\' OR organo  LIKE \'giunta comunale,%\' OR organo LIKE \'%,giunta comunale\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(73, 'manuale', 'PAT (prof ente) - Organo politico - Consiglio Comunale', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'consiglio comunale\' OR organo LIKE \'%,consiglio comunale,%\' OR organo  LIKE \'consiglio comunale,%\' OR organo LIKE \'%,consiglio comunale\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(74, 'manuale', 'PAT (prof ente) - Organo politico - Presidente Consiglio Comunale', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'presidente consiglio comunale\' OR organo LIKE \'%,presidente consiglio comunale,%\' OR organo  LIKE \'presidente consiglio comunale,%\' OR organo LIKE \'%,presidente consiglio comunale\' ) AND archivio != 1', 'ORDER BY referente'),
	(75, 'manuale', 'PAT (prof ente) - Organo politico - Direzione Generale', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'direzione generale\' OR organo LIKE \'%,direzione generale,%\' OR organo  LIKE \'direzione generale,%\' OR organo LIKE \'%,direzione generale\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(76, 'manuale', 'PAT (prof ente) - Organo politico - Segretario Generale', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'segretario generale\' OR organo LIKE \'%,segretario generale,%\' OR organo  LIKE \'segretario generale,%\' OR organo LIKE \'%,segretario generale\' ) AND archivio != 1', ''),
	(77, 'manuale', 'PAT (prof ente) - Organo politico - Commissioni', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'commissioni\' OR organo LIKE \'%,commissioni,%\' OR organo  LIKE \'commissioni,%\' OR organo LIKE \'%,commissioni\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(78, 'manuale', 'PAT (prof ente) - Incarichi dirigenziali', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ndirigente=1 AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY inizio_incarico DESC'),
	(79, 'manuale', 'PAT (prof ente) - Tempo determinato', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'determinato=1  AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')  AND archivio != 1', 'ORDER BY referente'),
	(80, 'manuale', 'PAT (prof ente) - Provvedimenti dirigenziali', '', 'informativa', 28, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipo = \'provvedimento dirigenziale\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data DESC,oggetto'),
	(81, 'manuale', 'PAT (prof ente) - Provvedimenti politici', '', 'informativa', 28, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipo = \'provvedimento organo politico\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data DESC,oggetto'),
	(82, 'manuale', 'PAT (prof ente) - Bilanci della categoria in navigazione', '', 'informativa', 29, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\'', 'ORDER BY ultima_modifica DESC'),
	(83, 'manuale', 'PAT (prof ente) - Regolamento di tipo statuto comunale', '', 'informativa', 19, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo=\'statuto\'', 'ORDER BY ordine'),
	(84, 'manuale', 'PAT (prof ente) - Incarichi Amministrativi di Vertie', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'incarico != \'\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND incarico IN (SELECT id FROM oggetto_incarichi WHERE dirigente=1) AND archivio != 1', 'ORDER BY referente'),
	(85, 'manuale', 'PAT (prof ente) - Bilanci preventivi e consuntivi (ordine per anno)', '', 'informativa', 29, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia != \'piano indicatori e risultati\'', 'ORDER BY anno DESC'),
	(86, 'manuale', 'PAT (prof ente) - Bilanci piani e indicatori (ordine per anno)', '', 'informativa', 29, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia = \'piano indicatori e risultati\'', 'ORDER BY anno DESC'),
	(87, 'manuale', 'PAT (prof ente) - Regolamento di tipo regolamento', '', 'informativa', 19, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo != \'statuto\' AND tipo != \'codice\'', 'ORDER BY ordine,titolo'),
	(88, 'manuale', 'PAT (prof ente) - Regolamento di tipo codice', '', 'informativa', 19, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo=\'codice\'', 'ORDER BY ordine'),
	(89, 'manuale', 'PAT (prof ente) - Gare e Contratti - Avvisi pubblici attivi ordinati per data di scadenza', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'avvisi pubblici\' AND data_attivazione < {domani} AND (data_scadenza >= {oggi} OR data_scadenza IS NULL ) AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(90, 'manuale', 'PAT (prof ente) - Gare e Contratti - Affidamenti ordinati per data', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'affidamenti\' AND data_attivazione < {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(91, 'manuale', 'PAT (prof ente) - Gare e Contratti - Esiti ordinati per data decrestentre', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'esiti\' AND data_attivazione < {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(92, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi di gara e contratti attivi ordinati per data di pubblicazione decrescente', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ndata_scadenza >= {oggi} AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia != \'somme liquidate\'', 'ORDER BY data_attivazione DESC'),
	(93, 'manuale', 'PAT (prof ente) - Procedimenti più consultati', 'Elenco dei procedimenti più consultati', 'informativa', 16, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY numero_letture DESC'),
	(94, 'manuale', 'PAT (prof ente) - Organo politico - Vicesindaco', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'vicesindaco\' OR organo LIKE \'%,vicesindaco,%\' OR organo  LIKE \'vicesindaco,%\' OR organo LIKE \'%,vicesindaco\' ) AND archivio != 1', 'ORDER BY referente'),
	(95, 'manuale', 'PAT - Visualizzo Archivio provvedimenti', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_provvedimenti]} != 0', ''),
	(96, 'manuale', 'PAT - Visualizzo Archivio Bandi di gara', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_bandi_gara]} != 0', ''),
	(97, 'manuale', 'PAT - Visualizzo Archivio Incarichi', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_incarichi]} != 0', ''),
	(98, 'manuale', 'PAT - Visualizzo Archivio Sovvenzioni', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_sovvenzioni]} != 0', ''),
	(99, 'manuale', 'PAT - Visualizzo Archivio Normativa', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_normativa]} != 0', ''),
	(100, 'manuale', 'PAT - Visualizzo Archivio Concorsi', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_concorsi]} != 0', ''),
	(101, 'manuale', 'Pubblica i concorsi attivi', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ndata_scadenza >= {oggi} AND data_attivazione <= {oggi}', 'ORDER BY data_scadenza'),
	(102, 'manuale', 'PAT (prof ente) - Commissioni', '', 'informativa', 43, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'commissione\' AND archivio != 1', 'ORDER BY ordine'),
	(103, 'manuale', 'PAT (prof ente) - Gruppi consiliari', '', 'informativa', 43, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'gruppo consiliare\' AND archivio != 1', 'ORDER BY ordine'),
	(104, 'manuale', 'PAT (prof ente) - Enti controllati - societa partecipata', '', 'informativa', 44, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'societa partecipata\'', 'ORDER BY ragione'),
	(105, 'manuale', 'Copia di PAT (prof ente) - Enti controllati - ente di diritto privato controllato', '', 'informativa', 44, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'ente di diritto privato controllato\'', 'ORDER BY ragione'),
	(106, 'manuale', 'PAT (prof ente) - Enti controllati - ente pubblico vigilato', '', 'informativa', 44, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'ente pubblico vigilato\'', 'ORDER BY ragione'),
	(107, 'manuale', 'PAT (prof ente) - Uffici 1 livello ordinati', '', 'informativa', 13, '', '', '', '', '', '', '', '', '(struttura = \'\' OR struttura = 0 OR struttura IS NULL) AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND articolazione=1', 'ORDER BY ordine'),
	(108, 'manuale', 'Modello di contenuto dell\'ente e della pagina in navigazione', '', 'informativa', 33, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (id_sezione_etrasp LIKE \'%,{idSezione},%\' OR id_sezione_etrasp = \'{idSezione}\' OR id_sezione_etrasp LIKE \'{idSezione},%\' OR id_sezione_etrasp LIKE \'%,{idSezione}\')', ''),
	(125, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi avvisi ed esiti ordinati per data di pubblicazione decrescente', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\n(data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza = \'\') AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia NOT IN (\'somme liquidate\',\'delibere e determine a contrarre\',\'lotto\') ', 'ORDER BY data_attivazione DESC'),
	(109, 'manuale', 'PAT (prof ente) - Ricerca Personale - Escludi incaricati politici', 'Ricerca Personale - Escludi incaricati politici', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND vis_elenchi = 1 AND archivio != 1', ''),
	(110, 'manuale', 'PAT (prof ente) - Oneri informativi', 'Questo criterio non applica alcuna logica di prelievo: è quindi l\'ideale per gli elenchi di oggetti che possono mantenere l\'ordinabilità tramite interfaccia.', 'informativa', 30, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo=\'onere\'', ''),
	(111, 'manuale', ' PAT (prof ente) - Obblighi informativi', 'Questo criterio non applica alcuna logica di prelievo: è quindi l\'ideale per gli elenchi di oggetti che possono mantenere l\'ordinabilità tramite interfaccia.', 'informativa', 30, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipo=\'obbligo\'', ''),
	(112, 'manuale', 'PAT (prof ente) - Organo politico - Commissario', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'commissario\' OR organo LIKE \'%,commissario,%\' OR organo  LIKE \'commissario,%\' OR organo LIKE \'%,commissario\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(113, 'manuale', 'PAT (prof ente) - ARCHVIO STORICO per Organo politico', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND organo != \'\' AND archivio = 1', 'ORDER BY priorita,referente'),
	(114, 'manuale', 'PAT (prof ente) - ARCHVIO STORICO normale', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND organo = \'\' AND archivio = 1', 'ORDER BY priorita,referente'),
	(115, 'manuale', 'Modulistica in ordine di priorità', '', 'informativa', 5, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY ordine,titolo'),
	(116, 'manuale', 'PAT (prof ente) - URL per AVCP', '', 'informativa', 45, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY anno'),
	(117, 'manuale', 'PAT - Visualizzo Tabelle AVCP XML', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_tabelle_avcp]} != 0', ''),
	(118, 'manuale', 'PAT (prof ente) - Gare e Contratti - Somme liquidate dell\'elemento in lettura completa', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nbando_collegato = 0{oggettoReview[id]} AND tipologia = \'somme liquidate\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(119, 'manuale', 'PAT (prof ente) - Informazioni del Sindaco', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND archivio != 1 AND ruolo_politico=\'Sindaco\'', ''),
	(120, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi di gara e contratti attivi ordinati per data di pubblicazione decrescente e Esiti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\n(data_scadenza >= {oggi} OR tipologia=\'esiti\') AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia != \'somme liquidate\'', 'ORDER BY data_attivazione DESC'),
	(121, 'manuale', 'PAT (prof ente) - Gare e Contratti - Avvisi pubblici scaduti ordinati per data di scadenza', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'avvisi pubblici\' AND data_scadenza < {oggi} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(122, 'manuale', 'PAT (prof ente) - Modulistica in ordine di titolo', '', 'informativa', 5, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY titolo'),
	(123, 'manuale', 'Visualizza se modulo \'immagine_organigramma\' è attivo', '', 'elemento', 0, '', '', '', '', '', '', '', '', 'moduloAttivo(\'immagine_organigramma\')', ''),
	(124, 'manuale', 'PAT (prof ente) - Delibere e determine a contrarre', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\ntipologia = \'delibere e determine a contrarre\' AND data_attivazione < {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(126, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi ed avvisi ordinati per data di pubblicazione decrescente', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\n(data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza = \'\') AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia NOT IN (\'esiti\',\'somme liquidate\',\'delibere e determine a contrarre\',\'lotto\') ', 'ORDER BY data_attivazione DESC'),
	(127, 'manuale', 'PAT (prof ente) - Criterio Fantasma per elenchi (con stato_pubblicazione)', 'Questo criterio non applica alcuna logica di prelievo: è quindi l\'ideale per gli elenchi di oggetti che possono mantenere l\'ordinabilità tramite interfaccia.', 'generale', 0, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\nid_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', ''),
	(128, 'manuale', 'PAT (prof ente) - Pubblica bandi avvisi ed esiti in base alla data di scadenza di pubblicazione esito (Marsciano)', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r(data_scadenza_esito >= {oggi} OR data_scadenza_esito IS NULL) AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia != \'somme liquidate\' AND tipologia != \'lotto\'', 'ORDER BY data_attivazione DESC'),
	(129, 'manuale', 'PAT (prof ente) - Concorsi - Avvisi del concorso in lettura completa', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rconcorso_collegato = 0{_GET[id_doc]}\rAND \rtipologia = \'avvisi\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')\rAND \r(concorso_collegato IS NOT NULL AND concorso_collegato!=0)', 'ORDER BY data_attivazione DESC'),
	(130, 'manuale', 'PAT (prof ente) - Concorsi -  Esiti del concorso in lettura completa', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rconcorso_collegato = 0{_GET[id_doc]}\rAND \r tipologia = \'esiti\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(131, 'manuale', 'PAT (prof ente) - Concorsi - Bando di concorso dell\'avviso/esito in lettura completa', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rid = 0{oggettoReview[concorso_collegato]} AND tipologia = \'concorsi\' AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(132, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi avvisi ed esiti senza affidamenti', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r(((data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza = \'\') AND tipologia != \'esiti\')  OR (tipologia=\'esiti\' AND ( data_scadenza_esito >= {oggi} OR data_scadenza_esito IS NULL OR data_scadenza_esito = \'\' ))) \rAND data_attivazione <= {domani} \rAND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') \rAND tipologia NOT IN (\'affidamenti\',\'somme liquidate\',\'delibere e determine a contrarre\',\'lotto\') ', 'ORDER BY data_attivazione DESC'),
	(133, 'manuale', 'PAT (prof ente) - Avvisi di concorso', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipologia=\'avvisi\' AND data_attivazione<{domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(143, 'manuale', 'PAT (prof ente) - Elenco normativa per ordine alfabetico', 'Questo criterio non applica alcuna logica di prelievo: è quindi l\'ideale per gli elenchi di oggetti che possono mantenere l\'ordinabilità tramite interfaccia.', 'informativa', 27, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY nome'),
	(134, 'manuale', 'PAT (prof ente) - Esiti di concorso', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipologia=\'esiti\' AND data_attivazione<{domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(135, 'manuale', 'PAT (prof ente) - Concorsi attivi - Solo etichetta concorsi', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipologia=\'concorsi\' AND data_attivazione<{domani} AND  (data_scadenza>={oggi} OR data_scadenza IS NULL)  AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(136, 'manuale', 'PAT (prof ente) - Pubblica tutte le informazioni di un Ente (open data in amministrazione)', '', 'generale', 0, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\'', 'ORDER BY data_attivazione DESC'),
	(137, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi avvisi ed esiti senza affidamenti ordinati per tipo (Sorrento)', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r(((data_scadenza!=\'\' AND data_scadenza >= {oggi}) AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia IN (\'bandi ed inviti\',\'avvisi pubblici\')) \r\rOR\r\r((data_scadenza_esito!=\'\' AND data_scadenza_esito >= {oggi}) AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia  IN (\'esiti\'))) ', 'ORDER BY tipologia,data_attivazione DESC'),
	(138, 'manuale', 'PAT - Visualizzo Archivio Bandi di gara - DEBUG', '', 'elemento', 0, '', '', '', '', '', '', '', '', '{entePubblicato[oggetto_bandi_gara]} != 0 AND {datiUser[permessi] == 10}', ''),
	(139, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi di gara e contratti attivi ordinati per data di pubblicazione decrescente (pagina', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r(data_scadenza >= {oggi} OR data_scadenza = \'\' OR data_scadenza = 0 OR data_scadenza IS NULL OR tipologia=\'esiti\') \rAND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') \rAND tipologia != \'somme liquidate\'', 'ORDER BY data_attivazione DESC'),
	(140, 'manuale', 'PAT (prof ente) - Gare e Contratti - Bandi di lavori servizi e forniture attivi ordinati per data di pubblicazione', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipologia = \'bandi ed inviti\' AND data_attivazione < {domani} AND (data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza=\'\') AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC'),
	(141, 'manuale', 'PAT (prof ente) - Pubblica i bandi attivi con data di scadenza impostata', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rtipologia = \'bandi ed inviti\' AND (data_scadenza >= {oggi}) AND data_attivazione <= {domani} AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_scadenza'),
	(142, 'manuale', 'PAT (prof ente) - Concorsi attivi-data pubblicazione decrescente', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\r data_attivazione<{domani} AND  (data_scadenza>={oggi} OR data_scadenza IS NULL)  AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY data_attivazione DESC, data_scadenza'),
	(144, 'manuale', '(eTRASP) - Bandi attivi - tutti gli affidamenti - tutti gli avvisi e tutti gli esiti (no liquidazioni)', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rdata_attivazione <= {domani} AND  id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND\r ((tipologia=\'bandi ed inviti\' AND data_scadenza>{oggi})  OR   tipologia != \'somme liquidate\')', 'ORDER BY data_attivazione DESC'),
	(145, 'manuale', '(eTRASP) - Concorsi attivi - tutti gli avvisi e tutti gli esiti', '', 'informativa', 22, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rdata_attivazione <= {domani} AND  id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND\r ((tipologia=\'concorsi\' AND data_scadenza>{oggi}) OR tipologia=\'esiti\' OR tipologia=\'avvisi\')', 'ORDER BY data_attivazione DESC'),
	(147, 'manuale', 'PAT (prof ente) - Organo politico - Assemblea dei Sindaci', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'assemblea dei sindaci\' OR organo LIKE \'%,assemblea dei sindaci,%\' OR organo  LIKE \'assemblea dei sindaci,%\' OR organo LIKE \'%,assemblea dei sindaci\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(148, 'manuale', 'PAT (prof ente) - Organo politico - Sub Commissario', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'sub commissario\' OR organo LIKE \'%,sub commissario,%\' OR organo  LIKE \'sub commissario,%\' OR organo LIKE \'%,sub commissario\' ) AND archivio != 1', 'ORDER BY priorita,referente'),
	(149, 'manuale', 'Visualizza se modulo \'dati_real_time_aou\' è attivo - Ospedali Riuniti Ancona', '', 'elemento', 0, '', '', '', '', '', '', '', '', 'moduloAttivo(\'dati_real_time_aou\')', ''),
	(150, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi ed avvisi senza esiti senza affidamenti e senza del e det a contrarre successivi ', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \rdata_attivazione>=1419984000 AND\r\r(((data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza = \'\') AND tipologia != \'esiti\')  OR (tipologia=\'esiti\' AND ( data_scadenza_esito >= {oggi} OR data_scadenza_esito IS NULL OR data_scadenza_esito = \'\' ))) \rAND data_attivazione <= {domani} \rAND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') \rAND tipologia NOT IN (\'affidamenti\',\'somme liquidate\',\'delibere e determine a contrarre\',\'lotto\',\'esiti\') ', 'ORDER BY data_attivazione DESC'),
	(151, 'manuale', 'PAT (prof ente) - Elenco strutture ordinate per nome e per PEC', '', 'informativa', 13, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', 'ORDER BY email_certificate DESC,nome_ufficio'),
	(152, 'manuale', 'PAT (prof ente) - Uffici appartenenti alla struttura in {configurazione[struttura_padre]} - NON ELIMINARE', '', 'informativa', 13, '', '', '', '', '', '', '', '', '(struttura = {configurazione[struttura_padre]}) AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND articolazione=1', 'ORDER BY ordine,nome_ufficio'),
	(157, 'manuale', 'PAT (prof ente) - Escludi incarichi amministrativi di vertice (usato in ricerca)', '', 'informativa', 4, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\rdirigente != 1 AND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\')', ''),
	(155, 'manuale', 'PAT (prof ente) - Gruppi consiliari (archivio)', '', 'informativa', 43, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'gruppo consiliare\' AND archivio = 1', 'ORDER BY ordine'),
	(154, 'manuale', 'PAT (prof ente) - Commissioni (archivio)', '', 'informativa', 43, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND tipologia=\'commissione\' AND archivio = 1', 'ORDER BY ordine'),
	(153, 'manuale', 'PAT (prof ente) - Organo politico - Commissioni (archivio)', '', 'informativa', 3, '', '', '', '', '', '', '', '', 'id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') AND (organo = \'commissioni\' OR organo LIKE \'%,commissioni,%\' OR organo  LIKE \'commissioni,%\' OR organo LIKE \'%,commissioni\' ) AND archivio = 1', 'ORDER BY priorita,referente'),
	(156, 'manuale', 'PAT (prof ente) - Pubblica tutti i bandi avvisi esiti affidamenti (nettuno)', '', 'informativa', 11, '', '', '', '', '', '', '', '', 'stato_pubblicazione = \'100\' AND \r\n\r\n(\r\n	((data_scadenza >= {oggi} OR data_scadenza IS NULL OR data_scadenza = \'\') AND tipologia != \'esiti\')  \r\n	OR \r\n	(tipologia=\'esiti\' AND ( data_scadenza_esito >= {oggi} OR data_scadenza_esito IS NULL OR data_scadenza_esito = \'\' ))\r\n) \r\nAND data_attivazione <= {domani} \r\nAND id_ente = \'{idEnte}\' AND (\'{idEnte}\' != \'0\' AND \'{idEnte}\' != \'\') \r\nAND tipologia NOT IN (\'somme liquidate\',\'delibere e determine a contrarre\',\'lotto\') ', 'ORDER BY data_attivazione DESC');
/*!40000 ALTER TABLE `regole_pubblicazione_oggetti_criteri` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_open_data: 45 rows
DELETE FROM `regole_pubblicazione_open_data`;
/*!40000 ALTER TABLE `regole_pubblicazione_open_data` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_open_data` (`id`, `id_regola`) VALUES
	(1, 928),
	(2, 134),
	(3, 934),
	(4, 898),
	(5, 91),
	(6, 913),
	(7, 131),
	(8, 927),
	(9, 139),
	(10, 140),
	(11, 155),
	(12, 901),
	(13, 132),
	(14, 740),
	(15, 741),
	(16, 54),
	(17, 906),
	(18, 907),
	(19, 727),
	(20, 729),
	(21, 730),
	(22, 731),
	(23, 912),
	(24, 930),
	(25, 56),
	(26, 944),
	(27, 947),
	(28, 953),
	(29, 954),
	(30, 955),
	(31, 951),
	(32, 952),
	(33, 945),
	(34, 95),
	(35, 959),
	(36, 973),
	(37, 974),
	(38, 843),
	(39, 840),
	(40, 842),
	(41, 949),
	(42, 844),
	(43, 841),
	(44, 845),
	(45, 979);
/*!40000 ALTER TABLE `regole_pubblicazione_open_data` ENABLE KEYS */;

-- Dump dei dati della tabella pat.regole_pubblicazione_template: 68 rows
DELETE FROM `regole_pubblicazione_template`;
/*!40000 ALTER TABLE `regole_pubblicazione_template` DISABLE KEYS */;
INSERT INTO `regole_pubblicazione_template` (`id`, `id_template`, `tipo_elemento`, `id_criterio`, `id_elemento`, `id_stile_elemento`, `id_stile_elemento_sottofamiglia`, `id_stile_elemento_speciale`, `id_stile_elemento_sottomenu`, `id_stile_pulsante_sottomenu`, `id_stile_speciale_sottomenu`, `css_id`, `css_classe`, `posizione`, `priorita`) VALUES
	(1, 1, 'regola_default', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 3),
	(2, 1, 'pannello', 0, 1, 56, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 2),
	(125, 1, 'pannello', 0, 36, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 6),
	(7, 1, 'menu', 0, 2, 13, 14, 15, 106, 107, 108, '', '', 'colonna_sx', 1),
	(8, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 4),
	(9, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 2),
	(10, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 4),
	(12, 1, 'pannello', 0, 2, 30, 0, 0, 0, 0, 0, '', '', 'inizio', 4),
	(123, 3, 'pannello', 0, 26, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 2),
	(54, 3, 'regola_default', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 1),
	(15, 1, 'pannello', 0, 4, 35, 0, 0, 0, 0, 0, '', '', 'inizio', 5),
	(16, 1, 'pannello', 0, 5, 157, 0, 0, 0, 0, 0, '', '', 'centro', 8),
	(17, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 5),
	(18, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 6),
	(19, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 8),
	(20, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 3),
	(21, 1, 'pannello', 0, 7, 43, 0, 0, 0, 0, 0, '', '', 'centro', 7),
	(73, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 2),
	(87, 1, 'pannello', 0, 28, 0, 0, 0, 0, 0, 0, '', '', 'centro', 1),
	(70, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 6),
	(69, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 1),
	(84, 1, 'pannello', 0, 27, 155, 0, 0, 0, 0, 0, '', '', 'fine', 1),
	(66, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 5),
	(85, 3, 'pannello', 0, 27, 0, 0, 0, 0, 0, 0, '', '', 'fine', 1),
	(61, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 3),
	(60, 3, 'pannello', 0, 1, 56, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 4),
	(59, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 2),
	(128, 1, 'pannello', 0, 39, 175, 0, 0, 0, 0, 0, '', '', 'centro', 2),
	(124, 1, 'pannello', 0, 35, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 3),
	(88, 1, 'pannello', 0, 29, 0, 0, 0, 0, 0, 0, '', '', 'centro', 9),
	(126, 1, 'pannello', 0, 37, 174, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 1),
	(122, 3, 'pannello', 0, 25, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 1),
	(76, 3, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 2),
	(82, 1, 'pannello', 0, 25, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 1),
	(83, 1, 'pannello', 0, 26, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 2),
	(127, 1, 'pannello', 0, 38, 88, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 7),
	(92, 4, 'regola_default', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 6),
	(94, 4, 'pannello', 0, 25, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 1),
	(95, 4, 'pannello', 0, 28, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 1),
	(96, 4, 'menu', 0, 1, 13, 14, 15, 106, 107, 108, '', '', 'colonna_sx', 1),
	(97, 4, 'pannello', 0, 28, 0, 0, 0, 0, 0, 0, '', '', 'centro', 1),
	(98, 4, 'pannello', 0, 27, 155, 0, 0, 0, 0, 0, '', '', 'fine', 1),
	(99, 4, 'pannello', 0, 8, 88, 0, 0, 0, 0, 0, '', '', 'centro', 2),
	(100, 4, 'paragrafo', 0, 6, 17, 0, 0, 0, 0, 0, '', '', 'fine', 2),
	(101, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 2),
	(102, 4, 'menu', 0, 2, 13, 14, 15, 106, 107, 108, '', '', 'colonna_sx', 2),
	(103, 4, 'media', 0, 676, 11, 10, 0, 0, 0, 0, '', '', 'inizio', 2),
	(104, 4, 'pannello', 0, 2, 30, 0, 0, 0, 0, 0, '', '', 'centro', 3),
	(105, 4, 'pannello', 0, 11, 113, 0, 0, 0, 0, 0, '', '', 'inizio', 3),
	(106, 4, 'menu', 0, 5, 13, 14, 15, 106, 107, 108, '', '', 'colonna_sx', 3),
	(107, 4, 'pannello', 0, 30, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 3),
	(108, 4, 'pannello', 0, 26, 0, 0, 0, 0, 0, 0, '', '', 'inizio', 4),
	(109, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 4),
	(110, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 4),
	(111, 4, 'pannello', 0, 4, 35, 0, 0, 0, 0, 0, '', '', 'centro', 4),
	(112, 4, 'pannello', 0, 1, 56, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 5),
	(113, 4, 'media', 0, 75, 91, 92, 0, 0, 0, 0, '', '', 'centro', 5),
	(114, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 5),
	(115, 4, 'pannello', 0, 29, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 6),
	(116, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_sx', 6),
	(117, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 7),
	(118, 4, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 7),
	(119, 4, 'pannello', 0, 5, 158, 0, 0, 0, 0, 0, '', '', 'centro', 8),
	(120, 4, 'pannello', 0, 29, 0, 0, 0, 0, 0, 0, '', '', 'centro', 9),
	(121, 4, 'pannello', 0, 7, 56, 0, 0, 0, 0, 0, '', '', 'centro', 10),
	(129, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 5),
	(130, 3, 'pannello', 0, 41, 180, 0, 0, 0, 0, 0, '', '', 'colonna_dx', 1),
	(131, 1, 'regola', 0, 0, 0, 0, 0, 0, 0, 0, '', '', 'centro', 6);
/*!40000 ALTER TABLE `regole_pubblicazione_template` ENABLE KEYS */;

-- Dump dei dati della tabella pat.sezioni: 164 rows
DELETE FROM `sezioni`;
/*!40000 ALTER TABLE `sezioni` DISABLE KEYS */;
INSERT INTO `sezioni` (`id`, `id_riferimento`, `data_creazione`, `data_ultima_modifica`, `data_attivazione`, `data_scadenza`, `tipo_scadenza`, `nome`, `nome_breve`, `utilizzo_nome_breve`, `title_code`, `h1_code`, `h2_code`, `descrizione`, `ricercabile`, `keywords`, `tags`, `id_proprietario`, `core`, `core_edit`, `permessi_lettura`, `tipo_proprietari_lettura`, `id_proprietari_lettura`, `permessi_admin`, `tipo_proprietari_admin`, `id_proprietari_admin`, `priorita`, `id_immagine`, `immagine_sostitutiva`, `link`, `forza_sottosezioni`, `livello_invisibile`, `impedisci_cache`, `accesskey`, `altre_lingue`, `id_oggetto_nativo`, `stile_oggetto_nativo`, `stile_admin_oggetto_nativo`, `adatta_template`, `template`, `css`, `permetti_valutazione`, `consultazione_valutazioni`, `id_dominio`, `dc_description`, `dc_contributor`, `dc_source`, `dc_relation`, `dc_coverage`, `forza_notabs`) VALUES
	(0, 0, 0, 0, 1315267200, 0, 'avvisa_prop', 'Amministrazione trasparente', '', '', '', '', '', '', 1, '', '', 0, 0, 1, 'N/A', 'tutti', '-1', 'N/A', 'tutti', '-1', 0, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 3, 1, 0, 0, 0, '', '', '', '', '', 0),
	(3, 0, 1309352730, 0, 1309305600, 0, 'nascondi', 'Utilit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 1, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(6, 0, 1309353961, 0, 1309305600, 0, 'nascondi', 'Informazioni', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 1, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(7, 3, 1309441101, 1358252823, 1309392000, 0, 'nascondi', 'Contatta l\'Ente', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(8, 3, 1309441114, 0, 1309392000, 0, 'nascondi', 'Mappa del sito', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(18, 0, 1309783500, 1351594922, 1309737600, 0, 'nascondi', 'Amministrazione trasparente', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 1, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(10, 3, 1309441140, 0, 1309392000, 0, 'nascondi', 'Accessibilit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(12, 663, 1309441436, 0, 1309392000, 0, 'nascondi', 'Notizie dal Comune', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 10, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(13, 663, 1309509269, 0, 1309478400, 0, 'nascondi', 'Galleria Immagini', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 9, 36, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(14, 663, 1309510501, 0, 1309478400, 0, 'nascondi', 'Come fare per', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 2, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(15, 663, 1309528622, 0, 1309478400, 0, 'nascondi', 'Eventi in agenda', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 6, 36, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(16, 15, 1309531929, 0, 1309478400, 0, 'nascondi', 'Eventi del giorno', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(17, 15, 1309531959, 0, 1309478400, 0, 'nascondi', 'Ricerca eventi', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(19, 18, 1309783545, 1368629133, 1309737600, 0, 'nascondi', 'Consulenti e collaboratori', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 4, 36, 0, 0, 1, 1, 0, 0, 0, 'Incarichi a dipendenti e soggetti privati', '', '', '', '', 0),
	(775, 46, 1380123920, 0, 1380067200, 0, 'nascondi', 'Elenco debiti scaduti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(21, 18, 1309790908, 1358252786, 1309737600, 0, 'nascondi', 'Attivit&agrave; e procedimenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 8, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Organigramma', '', '', '', '', 0),
	(22, 21, 1309799873, 1368625095, 1309737600, 0, 'nascondi', 'Tipologie di procedimento', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 16, 36, 0, 0, 1, 1, 0, 0, 0, 'Procedimenti', '', '', '', '', 0),
	(43, 711, 1311528195, 1368629875, 1311465600, 0, 'nascondi', 'Programma per la Trasparenza e l\'Integrit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Trasparenza', '', '', '', '', 0),
	(25, 712, 1309800102, 1368631345, 1309737600, 0, 'nascondi', 'Articolazione degli uffici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 13, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(26, 747, 1309855075, 0, 1309824000, 0, 'nascondi', 'Modulistica', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 5, 36, 0, 0, 1, 1, 0, 0, 0, 'Modulistica', '', '', '', '', 0),
	(27, 663, 1309857277, 0, 1309824000, 0, 'nascondi', 'Domande e risposte (FAQ)', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 12, 36, 0, 0, 1, 1, NULL, NULL, 0, '', '', '', '', '', 0),
	(635, 787, 1337355226, 0, 1337299200, 0, 'nascondi', 'Bandi di forniture', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(634, 787, 1337355215, 0, 1337299200, 0, 'nascondi', 'Bandi di servizi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(633, 787, 1337355204, 0, 1337299200, 0, 'nascondi', 'Bandi di lavori', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(636, 566, 1337355240, 0, 1337299200, 0, 'nascondi', 'Gare e procedure scadute', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(36, 663, 1309869415, 0, 1309824000, 0, 'nascondi', 'Link Utili', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 8, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(39, 747, 1310035667, 0, 1309996800, 0, 'nascondi', 'Regolamenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(44, 714, 1311528222, 0, 1311465600, 0, 'nascondi', 'Piano della Performance', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Il Piano e la relazione sulle performance', '', '', '', '', 0),
	(770, 746, 1379514372, 0, 1379462400, 0, 'nascondi', 'Accesso civico', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(46, 18, 1311528534, 0, 1311465600, 0, 'nascondi', 'Pagamenti dell\'amministrazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 17, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Gestione dei pagamenti', '', '', '', '', 0),
	(48, 728, 1311528578, 1368626624, 1311465600, 0, 'nascondi', 'Atti di concessione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 38, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(730, 18, 1368626694, 0, 1368576000, 0, 'nascondi', 'Bilanci', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 13, 0, 0, '', 0, 0, 0, '', '', 29, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(729, 728, 1368626282, 1368626365, 1368576000, 0, 'nascondi', 'Criteri e modalit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(50, 713, 1311528699, 1368632457, 1311465600, 0, 'nascondi', 'Dirigenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Ruolo dirigenti', '', '', '', '', 0),
	(51, 713, 1311528724, 1368632661, 1311465600, 0, 'nascondi', 'Posizioni organizzative', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(53, 713, 1311528785, 1368633387, 1311465600, 0, 'nascondi', 'OIV', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 10, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Nominativi e CV dei Valutatori', '', '', '', '', 0),
	(54, 713, 1311528815, 1368633061, 1311465600, 0, 'nascondi', 'Tassi di assenza', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Assenze e presenze', '', '', '', '', 0),
	(774, 711, 1380104647, 0, 1380067200, 0, 'nascondi', 'Attestazioni OIV o di struttura analoga', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(56, 714, 1311528878, 0, 1311465600, 0, 'nascondi', 'Ammontare complessivo dei premi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Premi e compensi', '', '', '', '', 0),
	(57, 714, 1311528906, 0, 1311465600, 0, 'nascondi', 'Dati relativi ai premi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Premialit&agrave;', '', '', '', '', 0),
	(59, 713, 1311529351, 1368670424, 1311465600, 0, 'nascondi', 'Incarichi conferiti e autorizzati ai dipendenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(60, 19, 1311529382, 0, 1311465600, 0, 'nascondi', 'Incarichi retribuiti e non retribuiti dei dipendenti di altre amministrazioni', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(61, 19, 1311529411, 0, 1311465600, 0, 'nascondi', 'Incarichi retribuiti e non retribuiti affidati a soggetti esterni', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(62, 737, 1311529902, 0, 1311465600, 0, 'nascondi', 'Costi contabilizzati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Contabilizzazione servizi erogati', '', '', '', '', 0),
	(63, 713, 1311529921, 1368633318, 1311465600, 0, 'nascondi', 'Contrattazione integrativa', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 9, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Contratti integrativi', '', '', '', '', 0),
	(64, 717, 1311529945, 1368634037, 1311465600, 0, 'nascondi', 'Societ&agrave; partecipate', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Consorzi Enti e Societ&agrave;', '', '', '', '', 0),
	(65, 712, 1311530969, 1368631445, 1311465600, 0, 'nascondi', 'Telefono e posta elettronica', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Posta elettronica istituzionale', '', '', '', '', 0),
	(66, 19, 1311532794, 0, 1311465600, 0, 'nascondi', 'Risultati ricerca', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(68, 713, 1311602426, 1368632746, 1311552000, 0, 'nascondi', 'Dotazione organica', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(69, 713, 1311602603, 0, 1311552000, 0, 'nascondi', 'Ricerca nel personale', '', '', '', '', '', '', 1, '', '', 0, 1, 0, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 11, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(700, 804, 1368583020, 1368630147, 1368576000, 0, 'nascondi', 'Oneri informativi per cittadini ed imprese', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 30, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(663, 6, 1350557952, 0, 1350518400, 0, 'nascondi', 'Oggetti informativi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(566, 18, 1321973084, 1368635659, 1321923600, 0, 'nascondi', 'Bandi di gara e contratti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 11, 0, 0, '', 0, 0, 0, '', '', 11, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(604, 3, 1321976000, 0, 1321923600, 0, 'nascondi', 'Responsabile del Procedimento di Pubblicazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(605, 3, 1321976017, 1433403560, 1321923600, 0, 'nascondi', 'Privacy', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Privacy', '', '', '', '', 0),
	(606, 3, 1321976027, 1358249488, 1321923600, 0, 'nascondi', 'Note legali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 8, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Note legali', '', '', '', '', 0),
	(607, 3, 1322047500, 1360685104, 1322010000, 0, 'nascondi', 'Posta Elettronica Certificata', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 9, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Posta elettronica certificata CAD', '', '', '', '', 0),
	(609, 713, 1322061495, 1368633288, 1322010000, 0, 'nascondi', 'Contrattazione collettiva', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 8, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Contrattazione nazionale', '', '', '', '', 0),
	(773, 25, 1380047141, 0, 1379980800, 0, 'nascondi', 'Risultati della ricerca', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(772, 746, 1379514418, 0, 1379462400, 0, 'nascondi', 'Dati ulteriori', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(616, 663, 1322066453, 0, 1322010000, 0, 'nascondi', 'Elenco siti tematici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 'Elenco siti tematici', '', '', '', '', 0),
	(768, 711, 1379009413, 0, 1378944000, 0, 'nascondi', 'Burocrazia zero', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(637, 566, 1337355268, 0, 1337299200, 0, 'nascondi', 'Informazioni d\'indicizzazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(767, 804, 1379009381, 0, 1378944000, 0, 'nascondi', 'Scadenzario dei nuovi obblighi amministrativi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(632, 737, 1337266696, 0, 1337212800, 0, 'nascondi', 'Carta dei Servizi e standard di qualit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(639, 18, 1337356593, 1368633551, 1337299200, 0, 'nascondi', 'Bandi di concorso', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 22, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(640, 639, 1337586190, 0, 1337558400, 0, 'nascondi', 'Concorsi attivi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(641, 639, 1337586202, 0, 1337558400, 0, 'nascondi', 'Concorsi scaduti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(771, 746, 1379514396, 0, 1379462400, 0, 'nascondi', 'Accessibilit&agrave; e Catalogo di dati, metadati e banche dati', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(766, 747, 1379009252, 0, 1378944000, 0, 'nascondi', 'Codice disciplinare e codice di condotta', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(765, 19, 1375934178, 0, 1311465600, 0, 'nascondi', 'Incarichi conferiti e autorizzati a personale interno', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(688, 3, 1353926417, 0, 1353891600, 0, 'nascondi', 'Richiesta appuntamento', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 10, 0, 0, '', 0, 0, 0, '', '', 0, 0, 36, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(689, 688, 1353927687, 0, 1353891600, 0, 'nascondi', 'Agenda appuntamenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 1, 0, '', '', 0, 0, 0, 0, 4, 1, 0, 0, 0, '', '', '', '', '', 0),
	(690, 689, 1354022403, 0, 1353978000, 0, 'nascondi', 'Modifica appuntamento', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 1, 0, '', '', 0, 0, 36, 0, 4, 1, 0, 0, 0, '', '', '', '', '', 0),
	(721, 21, 1368622439, 1368624420, 1368576000, 0, 'nascondi', 'Dati aggregati attivit&agrave; amministrativa', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(701, 712, 1368583104, 1368630408, 1368576000, 0, 'nascondi', 'Organi di indirizzo politico-amministrativo', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 43, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(702, 701, 1368584014, 0, 1368576000, 0, 'nascondi', 'Sindaco', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(703, 701, 1368584029, 0, 1368576000, 0, 'nascondi', 'Giunta ed assessori', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(704, 701, 1368584069, 0, 1368576000, 0, 'nascondi', 'Presidente Consiglio Comunale', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(705, 701, 1368584086, 0, 1368576000, 0, 'nascondi', 'Consiglio Comunale', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(706, 701, 1368584109, 0, 1368576000, 0, 'nascondi', 'Direzione Generale', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(707, 748, 1368584123, 0, 1368576000, 0, 'nascondi', 'Segretario Generale', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(708, 701, 1368584135, 0, 1368576000, 0, 'nascondi', 'Commissioni', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 9, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(709, 712, 1368585105, 1368631158, 1368576000, 0, 'nascondi', 'Sanzioni per mancata comunicazione dei dati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(710, 712, 1368585121, 1368631228, 1368576000, 0, 'nascondi', 'Rendiconti gruppi consiliari regionali/provinciali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(711, 18, 1368585181, 0, 1368576000, 0, 'nascondi', 'Disposizioni generali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(712, 18, 1368585245, 0, 1368576000, 0, 'nascondi', 'Organizzazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(713, 18, 1368618423, 0, 1368576000, 0, 'nascondi', 'Personale', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 3, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(714, 18, 1368621819, 0, 1368576000, 0, 'nascondi', 'Performance', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(715, 714, 1368621952, 0, 1368576000, 0, 'nascondi', 'Relazione sulla Performance', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(716, 714, 1368622116, 0, 1368576000, 0, 'nascondi', 'Benessere organizzativo', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(717, 18, 1368622149, 0, 1368576000, 0, 'nascondi', 'Enti controllati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(718, 717, 1368622237, 1368633867, 1368576000, 0, 'nascondi', 'Enti pubblici vigilati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(719, 717, 1368622259, 1368634211, 1368576000, 0, 'nascondi', 'Enti di diritto privato controllati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(720, 717, 1368622299, 1368634262, 1368576000, 0, 'nascondi', 'Rappresentazione grafica', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(722, 21, 1368623647, 1368624290, 1368576000, 0, 'nascondi', 'Monitoraggio tempi procedimentali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(723, 21, 1368623690, 1368623969, 1368576000, 0, 'nascondi', 'Dichiarazioni sostitutive e acquisizione d\'ufficio dei dati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(724, 18, 1368625162, 0, 1368576000, 0, 'nascondi', 'Provvedimenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 9, 0, 0, '', 0, 0, 0, '', '', 28, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(725, 724, 1368625210, 1368625352, 1368576000, 0, 'nascondi', 'Provvedimenti organi indirizzo-politico', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(726, 724, 1368625227, 1368625382, 1368576000, 0, 'nascondi', 'Provvedimenti dirigenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(727, 18, 1368625823, 1368625912, 1368576000, 0, 'nascondi', 'Controlli sulle imprese', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 10, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(728, 18, 1368626155, 0, 1368576000, 0, 'nascondi', 'Sovvenzioni, contributi, sussidi, vantaggi economici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 12, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(731, 730, 1368626745, 1368626845, 1368576000, 0, 'nascondi', 'Bilancio preventivo e consuntivo', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(732, 730, 1368626769, 1368626887, 1368576000, 0, 'nascondi', 'Piano degli indicatori e risultati attesi di bilancio', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(733, 18, 1368626958, 0, 1368576000, 0, 'nascondi', 'Beni immobili e gestione patrimonio', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 14, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(734, 733, 1368626987, 1368627058, 1368576000, 0, 'nascondi', 'Patrimonio immobiliare', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(735, 733, 1368627003, 1368627077, 1368576000, 0, 'nascondi', 'Canoni di locazione o affitto', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(736, 18, 1368627124, 1368627194, 1368576000, 0, 'nascondi', 'Controlli e rilievi sull\'amministrazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 15, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(737, 18, 1368627226, 0, 1368576000, 0, 'nascondi', 'Servizi erogati', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 16, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(738, 737, 1368627348, 0, 1368576000, 0, 'nascondi', 'Tempi medi di erogazione dei servizi', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(739, 46, 1368627489, 1368627572, 1368576000, 0, 'nascondi', 'Indicatore di tempestivit&agrave; dei pagamenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(740, 46, 1368627511, 1368627612, 1368576000, 0, 'nascondi', 'IBAN e pagamenti informatici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(741, 18, 1368627676, 1368627817, 1368576000, 0, 'nascondi', 'Opere pubbliche', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 18, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(809, 701, 1416990488, 0, 1416963600, 0, 'nascondi', 'Assemblea dei Sindaci', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 12, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(742, 18, 1368627870, 1368628408, 1368576000, 0, 'nascondi', 'Pianificazione e governo del territorio', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 19, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(743, 18, 1368627892, 1368628296, 1368576000, 0, 'nascondi', 'Informazioni ambientali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 20, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(744, 18, 1368627913, 1368628192, 1368576000, 0, 'nascondi', 'Strutture sanitarie private accreditate', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 21, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(808, 566, 1406728624, 0, 1406678400, 0, 'nascondi', 'Ricerca Bandi di gara e contratti in pubblicazione', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 10, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(745, 18, 1368627942, 1368628120, 1368576000, 0, 'nascondi', 'Interventi straordinari e di emergenza', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 22, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(746, 18, 1368627955, 0, 1368576000, 0, 'nascondi', 'Altri contenuti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 23, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(807, 639, 1399976847, 0, 1399939200, 0, 'nascondi', 'Esiti', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(747, 711, 1368629932, 1368630056, 1368576000, 0, 'nascondi', 'Atti generali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 19, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(748, 713, 1368631531, 1368631952, 1368576000, 0, 'nascondi', 'Incarichi amministrativi di vertice', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(806, 639, 1399976835, 0, 1399939200, 0, 'nascondi', 'Avvisi', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(749, 713, 1368631589, 1368632973, 1368576000, 0, 'nascondi', 'Personale non a tempo indeterminato', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(751, 747, 1368666442, 0, 1368662400, 0, 'nascondi', 'Statuti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(752, 747, 1368666468, 0, 1368662400, 0, 'nascondi', 'Normativa', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 27, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(769, 746, 1379514357, 0, 1379462400, 0, 'nascondi', 'Corruzione', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(776, 46, 1380123936, 0, 1380067200, 0, 'nascondi', 'Piano dei pagamenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(804, 711, 1396430714, 0, 1396396800, 0, 'nascondi', 'Oneri informativi per cittadini e imprese', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(777, 46, 1380123962, 0, 1380067200, 0, 'nascondi', 'Elenco debiti comunicati ai creditori', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(778, 714, 1380653909, 0, 1380585600, 0, 'nascondi', 'Sistema di misurazione e valutazione della Performance', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(779, 714, 1380653922, 0, 1380585600, 0, 'nascondi', 'Documento dell\'OIV di validazione della Relazione sulla Performance', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(803, 566, 1395841344, 0, 1395795600, 0, 'nascondi', 'Delibere e Determine a contrarre', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 8, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(780, 714, 1380653945, 0, 1380585600, 0, 'nascondi', 'Relazione dell\'OIV sul funzionamento complessivo del Sistema di valutazione trasparenza e integrit&agrave; dei controlli interni', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 8, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(791, 724, 1381409846, 0, 1381363200, 0, 'nascondi', 'Risultati ricerca', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(781, 741, 1380654075, 0, 1380585600, 0, 'nascondi', 'Documenti di programmazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(782, 741, 1380654087, 0, 1380585600, 0, 'nascondi', 'Linee guida per la valutazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(783, 741, 1380654104, 0, 1380585600, 0, 'nascondi', 'Relazioni annuali', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(801, 789, 1394186329, 0, 1394154000, 0, 'nascondi', 'Avvisi pubblici scaduti', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(784, 741, 1380654124, 0, 1380585600, 0, 'nascondi', 'Altri documenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(785, 741, 1380654153, 0, 1380585600, 0, 'nascondi', 'Nuclei di valutazione e  verifica degli investimenti pubblici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(800, 737, 1391042293, 0, 1391043600, 0, 'nascondi', 'Liste di attesa', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(786, 741, 1380654167, 0, 1380585600, 0, 'nascondi', 'Tempi e costi di realizzazione', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 6, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(787, 566, 1381068534, 0, 1381017600, 0, 'nascondi', 'Gare e procedure in corso', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(788, 566, 1381068563, 0, 1381017600, 0, 'nascondi', 'Avvisi di aggiudicazione ed esiti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(789, 566, 1381068583, 0, 1381017600, 0, 'nascondi', 'Avvisi pubblici', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 5, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(799, 566, 1390818538, 0, 1390784400, 0, 'nascondi', 'Tabelle riassuntive ai sensi dell\'Art. 1 comma 32 della legge n. 190/2012', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 9, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(790, 566, 1381068612, 0, 1381017600, 0, 'nascondi', 'Affidamenti', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(802, 728, 1394534039, 0, 1394499600, 0, 'nascondi', 'Albo dei Beneficiari', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 3, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(792, 701, 1382286321, 0, 1382227200, 0, 'nascondi', 'Vicesindaco', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 4, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(805, 3, 1399292177, 0, 1399248000, 0, 'nascondi', 'Posta Elettronica Certificata', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 11, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(798, 713, 1389959784, 0, 1389920400, 0, 'nascondi', 'Archivio', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 12, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(793, 701, 1383670649, 0, 1383613200, 0, 'nascondi', 'Gruppi consiliari', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 10, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(797, 701, 1389911138, 0, 1389834000, 0, 'nascondi', 'Archivio', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 11, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(794, 771, 1386835580, 0, 1386810000, 0, 'nascondi', 'Obiettivi di accessibilit&agrave;', '', '', '', '', '', '', 1, '', '', 0, 0, 0, 'H', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(795, 566, 1388174201, 0, 1388106000, 0, 'nascondi', 'Fornitori', '', '', '', '', '', '', 1, '', '', 0, 1, 1, 'HM', 'tutti', '-1', 'N/A', 'gruppo', '', 7, 0, 0, '', 0, 0, 0, '', '', 41, 36, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(796, 701, 1389909819, 0, 1389834000, 0, 'nascondi', 'Commissario Prefettizio', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 0, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(810, 701, 1420819228, 0, 1420765200, 0, 'nascondi', 'Sub Commissario', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(813, 793, 1436955065, 0, 1436918400, 0, 'nascondi', 'Archivio', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(811, 605, 1433403587, 1433403643, 1433376000, 0, 'nascondi', 'Cookie policy', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 1, 1, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(812, 708, 1436953670, 0, 1436918400, 0, 'nascondi', 'Archivio', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(814, 769, 1445625967, 0, 1445558400, 0, 'nascondi', 'Iscrizione wistleblower', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 1, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0),
	(815, 769, 1445626019, 0, 1445558400, 0, 'nascondi', 'Effettua una segnalazione', '', '', '', '', '', '', 1, 'servizi,uffici,contatti,procedimenti,trasparenza', '', 0, 0, 0, 'N/A', 'tutti', '-1', 'N/A', 'gruppo', '', 2, 0, 0, '', 0, 0, 0, '', '', 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '', '', 0);
/*!40000 ALTER TABLE `sezioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.sezioni_lingue: 0 rows
DELETE FROM `sezioni_lingue`;
/*!40000 ALTER TABLE `sezioni_lingue` DISABLE KEYS */;
/*!40000 ALTER TABLE `sezioni_lingue` ENABLE KEYS */;

-- Dump dei dati della tabella pat.sezioni_relazioni_oggetti: 0 rows
DELETE FROM `sezioni_relazioni_oggetti`;
/*!40000 ALTER TABLE `sezioni_relazioni_oggetti` DISABLE KEYS */;
/*!40000 ALTER TABLE `sezioni_relazioni_oggetti` ENABLE KEYS */;

-- Dump dei dati della tabella pat.sezioni_relazioni_oggetti_dati: 0 rows
DELETE FROM `sezioni_relazioni_oggetti_dati`;
/*!40000 ALTER TABLE `sezioni_relazioni_oggetti_dati` DISABLE KEYS */;
/*!40000 ALTER TABLE `sezioni_relazioni_oggetti_dati` ENABLE KEYS */;

-- Dump dei dati della tabella pat.sezioni_revisioni_valutazioni: 0 rows
DELETE FROM `sezioni_revisioni_valutazioni`;
/*!40000 ALTER TABLE `sezioni_revisioni_valutazioni` DISABLE KEYS */;
/*!40000 ALTER TABLE `sezioni_revisioni_valutazioni` ENABLE KEYS */;

-- Dump dei dati della tabella pat.stili_elementi: 176 rows
DELETE FROM `stili_elementi`;
/*!40000 ALTER TABLE `stili_elementi` DISABLE KEYS */;
INSERT INTO `stili_elementi` (`id`, `nome`, `id_template`, `famiglia`, `sotto_famiglia`, `id_elemento`, `visualizza_titolo`, `id_immagine_titolo`, `id_stile_riferimento`, `id_stile_titolo`, `id_stile_form`, `id_stile_form_bottoni`, `visualizza_foot`, `id_immagine_foot`, `id_stile_foot`, `link`, `display`, `larghezza`, `altezza`, `scroll`, `position`, `pos_alto`, `pos_dx`, `pos_basso`, `pos_sx`, `allineamento`, `impedisci_allineamento`, `distanza`, `rientro`, `bordo_alto`, `bordo_dx`, `bordo_basso`, `bordo_sx`, `bgcolore`, `bgimg`, `bgimg_repeat`, `bgimg_position`, `testo_allineamento`, `testo_acapo`, `testo_colore`, `link_colore`, `testo_font`, `testo_size`, `testo_lineheight`, `testo_spessore`, `testo_effetti`, `stili_personalizzati`, `stili_personalizzati_classe`) VALUES
	(1, 'Stile zona testata', 1, 'zona', 'nessuna', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(2, 'Stile zona colonna sinistra', 1, 'zona', 'nessuna', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '240px', 'auto', 'visible', 'relative', '', '', '', '', 'left', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(3, 'Stile zona colonna destra', 1, 'zona', 'nessuna', 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '200px', 'auto', 'visible', 'relative', '', '', '', '', 'right', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'repeat-x', '', 'destra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(4, 'Stile zona centro', 1, 'zona', 'nessuna', 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 16px 0px 16px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(5, 'Stile zona chiusura', 1, 'zona', 'nessuna', 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', '1px solid #EBE6D2', 'none', 'none', 'none', '#ffffff', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(6, 'Contenitore logo in testata', 1, 'media', 'nessuna', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, NULL),
	(7, 'Logo in testata', 1, 'media', 'immagine', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, NULL),
	(8, 'Pannello Login', 1, 'pannello', 'nessuna', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '20px 0px 20px 0px', '5px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(9, 'Contenitore immagine testata', 1, 'media', 'nessuna', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(10, 'Media logo', 1, 'media', 'immagine', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 'http://www.dominiopat.it/pagina0_home.html', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(11, 'Contenitore logo testata', 1, 'media', 'nessuna', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '224px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '5px 0px 5px 20px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(12, 'Media normale', 1, 'media', 'immagine', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(13, 'PAT - Menu sfondo bianco (titolo blu con icona)', 1, 'menu', 'nessuna', 0, 1, 0, 0, 132, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', ''),
	(123, 'Menu Il Comune', 1, 'menu', 'nessuna', 0, 1, 0, 0, 124, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', ''),
	(14, 'PAT - Pulsante menu generico navigazione', 1, 'menu', 'bottone', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 6px 0px', '2px 0px 2px 16px', 'none', 'none', '1px dotted  #DBD4D8', 'none', 'transparent', '799', 'no-repeat', '3px 6px', 'sinistra', 'normal', '#353535', '', '', '', '1.5', '300', 'none', '', ''),
	(15, 'Pulsante rollover menu generico navigazione', 1, 'menu', 'rollover', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '   ', '   ', 'none', 'none', '1px solid  #EDEDED', 'none', '#F6F6F6', '0', 'no-repeat', '', 'sinistra', 'normal', '#082F58', '', '', '', '1.5', '300', 'none', '', ''),
	(16, 'PAT - Titoli default (sfondo blu)', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 16px 0px', '8px 8px 8px 8px', '1px solid  #2C6387', '1px solid  #2C6387', '1px solid  #2C6387', '1px solid  #2C6387', '#377ba8', '0', 'repeat-x', 'bottom', 'sinistra', 'normal', '#FFFFFF', '', 'Open Sans, Tahoma, serif', '92%', '1.2', '600', 'uppercase', '', 'a{\r\ncolor: #FFFFFF;\r\ntext-decoration:none;\r\n}\r\na:hover{\r\ncolor: #E3E3E3;\r\ntext-decoration:none;\r\n}\r\np {\r\nmargin:0px;\r\npadding:0px;\r\n}'),
	(17, 'Paragrafo Copyright', 1, 'contenuto', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '20px 0px 0px 0px', '10px 0px 20px 0px', 'none', 'none', 'none', 'none', '#208dce', '0', 'no-repeat', '', 'centrato', 'normal', '#FFFFFF', '#F3F1F2', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(18, 'PAT - Paragrafo trasparente (no titolo) (default)', 1, 'contenuto', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '7px 7px 7px 7px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', ''),
	(19, 'PAT - Contenitore normale bianco (titolo blu)', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid #E4E4E4', '1px solid #E4E4E4', '1px solid #E4E4E4', '1px solid #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', ''),
	(161, 'Immagine auto centrata con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', 'img {border:1px solid #DCDCDC;margin:0 auto;box-shadow: 0 1px 3px #CDCDCD;}'),
	(20, 'PAT - Istanza normale con bordo basso beige', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px 5px 6px 5px', 'none', 'none', '1px solid #D4D4D4', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(21, 'Immagine 140px a sinistra con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '140px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '3px 10px 6px 0px', '0px   ', '3px solid  #FFFFFF', '3px solid  #FFFFFF', '3px solid  #FFFFFF', '3px solid  #FFFFFF', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', 'max-height: 140px;\r\noverflow: hidden;\r\nz-index: 3;\r\n\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;', ''),
	(22, 'Testo 140% bold rosso', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 2px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#A82E24', '#A82E24', 'Tahoma, sans-serif', '140%', '1.5', '600', 'none', '', ''),
	(23, 'Testo 120% bold rosso', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 2px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#A82E24', '#A82E24', 'Tahoma, sans-serif', '120%', '1.5', '600', 'none', '', ''),
	(24, 'PAT - Testo 170% blu', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#377BA8', '#377BA8', 'Open Sans, Times, serif', '182%', '1.5', '300', 'none', '', ''),
	(25, 'PAT - Testo 100% bold blu scuro', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 2px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#377BA8', '#377BA8', 'Open Sans, Tahoma, serif', '100%', '1.5', '600', 'none', '', 'a { text-decoration: none; }\r\na:hover { text-decoration: underline; }'),
	(26, 'Immagine 220px a sinistra con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '220px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 12px 8px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border: 1px solid #DBD4D8;\r\n}'),
	(27, 'PAT - Immagine 120px a sinistra (con bordi)', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '120px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 8px 6px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', 'z-index: 3;\r\nmax-height: 120px;\r\noverflow: hidden;\r\n', ''),
	(28, 'Contenitore Aree tematiche', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 45, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', 'none', 'none', '1px solid #D2D0BA', 'none', '#EBE6D2', '0', 'no-repeat', 'bottom right', 'sinistra', 'normal', '#F3F1F2', '#890000', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', ''),
	(29, 'Istanza pulsante sfondo beige', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '7px 5px 6px 5px', '0px 20px 3px 5px', 'none', 'none', '1px solid #D2D0BA', 'none', 'transparent', '586', 'no-repeat', 'right', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'a {text-decoration:none;}\r\na:hover {\r\ntext-decoration:none;\r\ncolor: #680000;\r\n}'),
	(30, 'PAT - Pannello Titolo della pagina', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 4px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '#377BA8', '#377BA8', 'Open Sans, Times, serif', '172%', '1.0', '600', 'capitalize', 'letter-spacing: -0.6pt;', ''),
	(31, 'Menu Tools', 1, 'menu', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '4px 10px 10px 10px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', 'Tahoma, sans-serif', '66%', '1.5', '300', 'none', '', ''),
	(32, 'Pulsante menu Tools', 1, 'menu', 'bottone', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'none', '0px 0px 0px 0px', '2px 12px 2px 12px', 'none', '1px solid  #DBD4D8', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'nowrap', '#353535', '', '', '', '1.5', '300', 'none', '', ''),
	(33, 'Pulsante rollover menu Tools', 1, 'menu', 'rollover', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'none', '   ', '   ', 'none', '1px solid  #DBD4D8', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#A82E24', '', '', '', '1.5', '300', 'none', '', ''),
	(34, 'Pannello Cerca nel sito', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 75, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'right', '0px 10px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', 'display:none;', 'label {display:none;}'),
	(35, 'Pannello Briciole di pane', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '#353535', '#082F58', 'Open Sans, Times, serif', '68%', '1.5', '300', 'none', '', 'a{\r\ntext-decoration:none;\r\n}\r\na:hover{\r\ntext-decoration:underline;\r\n}'),
	(36, 'PAT - Lettura completa (sfondo bianco)', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '100%', '1.2', '300', 'none', '', 'p {\r\nmargin: 0px;\r\n}'),
	(37, 'Lettura completa con bordi e sfondo', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, NULL),
	(38, 'PAT - Contenitore normale senza titolo', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', '.interfacciaPagine {\r\nmargin: 28px 0px 20px 0px;\r\nborder-top:2px solid #EBE6D2;\r\npadding: 5px 5px 0px 5px;\r\nfont-weight:bold;\r\ncolor:#353535;\r\nline-height:2.5;\r\nclear:both;\r\n}\r\n.interfacciaPagine a {\r\nmargin:8px 5px;\r\nfont-weight:normal;\r\npadding: 3px 5px;\r\nbackground: #F4F4F4;\r\nborder:1px solid #377BA8;\r\ncolor:#377BA8;\r\ntext-decoration:none;\r\n\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;\r\n\r\n}\r\n.interfacciaPagine a:hover {\r\nbackground: #377BA8;\r\ncolor:#FFFFFF;\r\nborder:1px solid #377BA8;\r\n}\r\n'),
	(39, 'Istanza con bordi e sfondo', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px   ', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(40, 'Contenitore interfaccia di ricerca', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '5px   ', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(41, 'Istanza  in Notizie e Comunicati', 1, 'oggetto', 'istanza', 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, NULL),
	(42, 'Contenuto automatico', 1, 'misto', 'contenuto_automatico', 0, 1, 0, 0, 0, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '15px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', ''),
	(43, 'PAT - Pannello trasparente testo destra (no titolo)', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 20px 0px', '7px 7px 7px 7px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', '.at4-icon span {\r\n	display: none;\r\n}'),
	(175, 'PAT - Pannello contenuto con profilazione Ente', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', ''),
	(44, 'Pulsante menu', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '2px 0px 6px 0px', '2px 0px 2px 16px', 'none', 'none', '1px dotted  #E0E0E0', 'none', 'transparent', '799', 'no-repeat', '3px 6px', 'sinistra', 'normal', '#6C0202', '#6C0202', 'Open Sans, Tahoma, serif', '100%', '1.2', '300', 'none', '', 'a {\r\ntext-decoration:none;\r\n}'),
	(45, 'Titolo aree tematiche', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '4px 8px 2px 8px', 'none', 'none', '1px solid #D2D0BA', 'none', '#6C0202', '581', 'repeat-x', 'bottom', 'sinistra', 'normal', '#FFFFFF', '', 'Georgia, Times, serif', '130%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', 'a {\r\ntext-decoration:none;\r\ncolor:#FFFFFF;\r\n}'),
	(46, 'Pulsante indietro', 0, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '20px 0px 20px 0px', '10px 5px 10px 20px', '2px solid #E4E4E4', 'none', 'none', 'none', 'transparent', '589', 'no-repeat', 'left', 'sinistra', 'normal', '#13385D', '#13385D', 'Tahoma, sans-serif', '100%', '1.5', '600', 'none', '', 'a {text-decoration:none;}\r\na:hover {\r\ntext-decoration:underline;\r\ncolor: #353535;\r\n}'),
	(60, 'Immagine 140px (in altezza) con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '140px', '100px', 'hidden', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '4px 4px 4px 4px', '1px solid  #D4D4D4', '1px solid  #D4D4D4', '1px solid  #D4D4D4', '1px solid  #D4D4D4', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border: 2px solid #DDDDDD;\r\n	margin:0 auto;\r\n}'),
	(47, 'Testo 100% blu a destra', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '#353535', '#377BA8', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', ''),
	(48, 'Pulsante download (icona floppy)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'left', '10px 0px 5px 0px', '5px 5px 5px 26px', '1px solid #D3D3D3', '1px solid #D3D3D3', '1px solid #D3D3D3', '1px solid #D3D3D3', '#EDEDED', '588', 'no-repeat', '5px 7px', 'sinistra', 'normal', '#208DCE', '#353535', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;', ''),
	(49, 'Istanza semplice senza bordi e sfondo', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(50, 'Immagine 192px centrata senza bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '192px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {border:none;margin:0 auto;}'),
	(51, 'Immagine 190px centrata con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '190px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border:1px solid #DBD4D8;\r\n	margin:0 auto;\r\n}'),
	(52, 'Titolo galleria immagini', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '10px 8px 10px 34px', 'none', 'none', '1px solid  #F3F1F2', 'none', '#660000', '83', 'no-repeat', '', 'sinistra', 'normal', '#FFFFFF', '', 'Tahoma, sans-serif', '100%', '1.5', '600', 'uppercase', '', ''),
	(53, 'Contenitore Galleria immagini', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 52, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', '1px solid  #232323', '1px solid  #232323', '1px solid  #232323', '1px solid  #232323', '#660000', '0', 'no-repeat', '', 'sinistra', 'normal', '#F3F1F2', '#FFFFFF', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(54, 'Immagine 166px centrata con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '166px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border:1px solid #FFFFFF;\r\n	margin:0 auto;\r\n}'),
	(55, 'Immagine 480px centrata con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '480px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border: 3px solid #EBE6D2;\r\n	margin:0 auto;\r\n	-moz-border-radius: 5px;\r\n	-webkit-border-radius: 5px;\r\n	border-radius: 5px;\r\n}'),
	(56, 'eTRASP- Pannello bianco con ombra (titolo blu)', 1, 'pannello', 'nessuna', 0, 1, 0, 0, 16, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '4px 4px 4px 4px', '1px solid  #D4D4D4', '1px solid  #D4D4D4', '1px solid  #D4D4D4', '1px solid  #D4D4D4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#082F58', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', 'box-shadow: 0 1px 3px #CDCDCD;', 'div {padding:10px 4px;}'),
	(57, 'Contenitore normale con titolo scuro e bordi', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(58, 'Contenitore normale con titolo scuro e bordi', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(59, 'Istanza allineata sx (46%)', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '46%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '10px 1% 10px 1%', '0px 1% 6px 1%', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(61, 'Istanza allineata sx con larghezza auto', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '10px 10px 10px 10px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(62, 'Immagine 60px (in altezza) con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', '60px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border: 1px solid #DBD4D8;\r\n}'),
	(63, 'Titoli sfondo chiaro default', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 8px 0px', '4px 8px 4px 8px', 'none', 'none', 'none', 'none', '#666666', '95', 'no-repeat', 'top left', 'sinistra', 'normal', '#FFFFFF', '', 'Tahoma, sans-serif', '100%', '1.5', '300', 'lowercase', '', ''),
	(64, 'PAT - Contenitore normale con titolo default blu scuro', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '80%', '1.5', '300', 'none', '', ''),
	(65, 'Contenitore allineato sx 50% con titolo default rosso sfondo beige', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'left', '0px 0px 10px 0px', '0px   ', 'none', 'none', '1px solid  #DBD4D8', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#208DCE', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', 'min-height: 250px;', ''),
	(66, 'Contenitore allineato dx 50% con titolo default rosso sfondo beige', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 73, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'right', '0px 0px 10px 0px', '0px   ', 'none', 'none', '1px solid  #DBD4D8', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', 'min-height: 250px;', ''),
	(67, 'Testo 100% nero monoriga', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(68, 'Interfaccia calendario default', 1, 'oggetto', 'interfaccia', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px auto 10px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'table{\r\n	font-size:100%;\r\n	padding:0px; \r\n	border:1px solid #DDDDDD;\r\n	padding:2px;\r\n	margin:0px auto 4px auto;\r\n}\r\ntd{\r\n	background-color:#F5F5F5;\r\n	height:auto;\r\n	width:auto;\r\n	padding:0px;\r\n	text-align:center;\r\n	color:#353535;\r\n}\r\nth{\r\n	background-color:#10508c;\r\n	height:auto;\r\n	width:auto;\r\n	font-weight:bold;\r\n	padding:3px;\r\n	text-align:center;\r\n	color:#FFFFFF;\r\n	margin:0px;\r\n}\r\n.calGiornoScelto {\r\n	background-color:#FFFFFF !important;\r\n	color:#353535 !important;\r\n	border:1px solid #D2D0BA !important;\r\n}\r\n.intMese{\r\n	text-align:center;\r\n}\r\n.giornoSceltoTxt {\r\n	font-size:120%;\r\n	margin:18px auto;\r\n	color:#353535;	\r\n}\r\n.dataFooter {\r\n	margin: 5px auto;\r\n	text-align: center;\r\n}'),
	(69, 'Contenitore allineato sx 50% con titolo chiaro senza bordi', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 63, 0, 0, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'left', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(70, 'Contenitore allineato dx 50% con titolo chiaro senza bordi', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 63, 0, 0, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'right', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', ''),
	(71, 'Campo ricerca (icona lente)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 75, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '2px 0px 2px 22px', 'none', 'none', 'none', 'none', 'transparent', '114', 'no-repeat', 'left', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(72, 'PAT - Interfaccia ricerca default (no ordine,num risultati)', 1, 'oggetto', 'interfaccia', 0, 1, 0, 0, 0, 110, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '0px 5px 0px 5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', '.esattamenteogg110, .ricercaOrdineLimite {\r\ndisplay: none;\r\n}\r\n'),
	(73, 'PAT - Pulsante form scuro', 1, 'misto', 'formbottoni', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', 'auto', '24px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 12px', '0px 7px 0px 7px', 'none', 'none', 'none', 'none', '#377BA8', '0', 'no-repeat', '', 'centrato', 'normal', '#FFFFFF', '', 'Open Sans, Tahoma, serif', '90%', '1.5', '600', 'uppercase', 'cursor: pointer;\r\nvertical-align: top;\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;', ''),
	(74, 'Pulsante form chiaro', 1, 'misto', 'formbottoni', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 4px 0px 4px', '2px 3px 2px 3px', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '1px solid #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '#890000', '', 'Tahoma, sans-serif', '72%', '1.5', '300', 'uppercase', '', ''),
	(75, 'Campo testo automatico', 1, 'misto', 'form', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 2px 0px 2px', '3px 3px 3px 3px', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', ''),
	(76, 'PAT - Contenitore tabella bianco (no titolo)', 1, 'oggetto', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', '.vistaTabella {\r\n	width:100%;	\r\n}\r\n.vistaTabella th {\r\n	background:#377BA8;\r\n	color:#FFFFFF;\r\n	padding:8px 8px;\r\n	text-transform:uppercase;\r\n	font-size:92%;\r\n	font-weight:bold;	\r\n}\r\n.vistaTabella th a{\r\n	color:#FFFFFF;\r\n}\r\n.vistaTabella td {\r\n	padding:6px 4px;\r\n}\r\n.dispari {\r\n	background: #F1F1F1 !important;\r\n}\r\n.pari {\r\n	background: #FFFFFF;\r\n}\r\n.interfacciaPagine {\r\nmargin: 28px 0px 20px 0px;\r\nborder-top:2px solid #EBE6D2;\r\npadding: 5px 5px 0px 5px;\r\nfont-weight:bold;\r\ncolor:#353535;\r\nline-height:2.5;\r\nclear:both;\r\n}\r\n.interfacciaPagine a {\r\nmargin:8px 5px;\r\nfont-weight:normal;\r\npadding: 3px 5px;\r\nbackground: #F4F4F4;\r\nborder:1px solid #377BA8;\r\ncolor:#377BA8;\r\ntext-decoration:none;\r\n\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;\r\n\r\n}\r\n.interfacciaPagine a:hover {\r\nbackground: #377BA8;\r\ncolor:#FFFFFF;\r\nborder:1px solid #377BA8;\r\n}\r\n.pulsanteIstanza {\r\n	margin:0px 3px;\r\n	background: transparent;\r\n	padding:0px;\r\n}\r\ntd ul {\r\n	margin: 0px;\r\n	padding:0px;\r\n	list-style-type:none;\r\n	line-height:1.4;\r\n}\r\n'),
	(77, 'Campo email (icona mail)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '0px 0px 0px 22px', 'none', 'none', 'none', 'none', 'transparent', '592', 'no-repeat', 'left', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(78, 'Campo utente (icona user)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '0px 0px 0px 20px', 'none', 'none', 'none', 'none', 'transparent', '591', 'no-repeat', 'left', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(79, 'Campo default (icona freccia)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '0px 0px 0px 22px', 'none', 'none', 'none', 'none', 'transparent', '590', 'no-repeat', 'left', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(80, 'Testo 100% normale con margine alto e basso', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '15px 0px 15px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(81, 'Testo 100% con bordo e margine sinistro', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '8px 0px 8px 20px', '1px 0px 1px 6px', 'none', 'none', 'none', '6px solid  #DBD4D8', '#F3F1F2', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '600', 'none', '', ''),
	(82, 'Testo 92% nero', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '92%', '1.5', '300', 'none', '', ''),
	(83, 'Contenitore allineato dx 50% con titolo scuro con bordi', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 73, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'right', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(84, 'Contenitore allineato sx 50% con titolo default rosso', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', '49%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'left', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(85, 'Titolo con sfondo scuro', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 8px 0px', '7px 8px 11px 8px', 'none', 'none', 'none', 'none', 'transparent', '77', 'no-repeat', '', 'sinistra', 'normal', '#FFFFFF', '#FFFFFF', 'Tahoma, sans-serif', '100%', '1.5', '600', 'uppercase', '', ''),
	(86, 'Titolo piccolo con bordo basso', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '22px 0px 8px 0px', '0px 0px 4px 0px', 'none', 'none', '1px solid  #DBD4D8', 'none', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#757575', '#757575', 'Open Sans, Times, serif', '130%', '1.5', '300', 'none', '', ''),
	(160, 'Immagine auto centrata senza bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', 'img {border:none;margin:0 auto;}'),
	(87, 'Istanza allineata sx con bordo basso e poco margine', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '46%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '4px 1% 4px 1%', '0px 1% 4px 1%', 'none', 'none', '1px solid  #DBD4D8', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(88, 'PAT - Pannello vuoto', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'both', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', ''),
	(89, 'Immagine 80px con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '60px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'img {\r\n	border: 1px solid #DBD4D8;\r\n}'),
	(90, 'Istanza photogallery in colonna', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '62px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '5px 5px 5px 5px', '0px   ', '1px solid  #FFFFFF', '1px solid  #FFFFFF', '1px solid  #FFFFFF', '1px solid  #FFFFFF', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(91, 'Contenitore media reset', 1, 'media', 'nessuna', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(92, 'Media invisibile', 1, 'media', 'immagine', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'none', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(93, 'PAT - Pulsante a menu', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', 'none', 'none', '1px solid #DBD4D8', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#353535', 'Tahoma, sans-serif', '100%', '1.2', '300', 'none', '', 'a {\r\ntext-decoration:none;\r\ndisplay: block;\r\npadding: 9px 5px 2px 5px;\r\ncolor: #333333;\r\n}\r\na:hover {\r\ncolor: #A82E24;\r\n}'),
	(121, 'Pulsante form pannello ricerca', 1, 'misto', 'formbottoni', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', '28px', '27px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 10px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '690', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', 'Tahoma, sans-serif', '0%', '1.5', '300', 'uppercase', '', ''),
	(162, 'Contenitore sezioni Leggi Anche', 1, 'oggetto', 'nessuna', 0, 0, 0, 0, 0, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '-10px 0px 20px 5px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#13385D', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(99, 'Contenitore per vista tabella con titolo chiaro senza intestazioni', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 63, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#890000', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', '.vistaTabella {\r\n	width:100%;	\r\n}\r\n.vistaTabella th {\r\n	display:none;\r\n	height:0px !important;	\r\n}\r\n.vistaTabella td {\r\n	padding:6px 4px;\r\n}\r\n.dispari {\r\n	background: #F3F1F2;\r\n}\r\n.pari {\r\n	background: #FFFFFF;\r\n}\r\n.interfacciaPagine {\r\nmargin: 28px 0px 20px 0px;\r\nborder-top:2px solid #EBE6D2;\r\npadding: 5px 5px 0px 5px;\r\nfont-weight:bold;\r\ncolor:#353535;\r\nline-height:2.5;\r\nclear:both;\r\n}\r\n.interfacciaPagine a {\r\nmargin:8px 5px;\r\nfont-weight:normal;\r\npadding: 3px 5px;\r\nbackground: #EBE6D2;\r\nborder:1px solid #D2D0BA;\r\ncolor:#A82E24;\r\ntext-decoration:none;\r\n\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;\r\n\r\n}\r\n.interfacciaPagine a:hover {\r\nbackground: #560504;\r\ncolor:#FFFFFF;\r\nborder:1px solid #560504;\r\n}\r\n.pulsanteIstanza {\r\n	margin:0px 3px;\r\n	background: transparent;\r\n	padding:0px;\r\n}\r\n'),
	(100, 'Google Maps tuttoschermo con 220px altezza', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '99%', '220px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '10px 0px 10px 0px', '0px   ', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', '1px solid  #DBD4D8', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(101, 'Google Maps 100x100px a sinistra', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100px', '100px', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 12px 8px 0px', '0px   ', '1px solid #D2D0BA', '1px solid #D2D0BA', '1px solid #D2D0BA', '1px solid #D2D0BA', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ' '),
	(102, 'Titolo grande', 1, 'contenuto', 'editor', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '24px 0px 12px 0px', '0px 0px 4px 0px', 'none', 'none', '2px solid  #D4D4D4', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#218DCD', '#218DCD', 'Open Sans, Times, serif', '170%', '1.0', '300', 'none', '', ''),
	(103, 'PAT - Testo 100% con bordo sfondo e margine sinistro', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '8px 0px 8px 20px', '3px 0px 1px 6px', 'none', 'none', 'none', '5px solid #EFEFEF', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'a {\r\ntext-decoration: none;\r\n}\r\na:hover {\r\ntext-decoration: underline;\r\n}'),
	(104, 'Campo default alternativo (icona freccia 2)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '0px 0px 0px 16px', 'none', 'none', 'none', 'none', 'transparent', '33', 'no-repeat', '4px 5px', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'a {text-decoration:none;}\r\na:hover {text-decoration:underline;}'),
	(105, 'Pannello leggi anche', 1, 'pannello', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', 'ul {\r\nmargin:0px 5px;\r\npadding:0px;\r\nlist-style:none;\r\n}'),
	(106, 'PAT - Sottomenu Generico Navigazione', 1, 'menu', 'sottomenu', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 12px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', 'Open Sans, Tahoma, serif', '100%', '1.2', '300', 'none', '', ''),
	(107, 'PAT - Pulsante sottomenu generico', 1, 'menu', 'sottobottone', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 6px 0px', '2px 5px 2px 5px', 'none', 'none', '1px dotted #E4E4E4', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', '', '', '1.5', '300', 'none', '', ''),
	(108, 'Pulsante sottomenu generico rollover', 1, 'menu', 'sottorollover', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '   ', '   ', 'none', 'none', '1px solid #EBE6D2', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#890000', '', '', '', '1.5', '300', 'none', '', ''),
	(109, 'Campo contatto (icona freccia)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 110, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '6px 0px 16px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'label {\r\ndisplay:block;\r\n}'),
	(110, 'Campo testo normale', 1, 'misto', 'form', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', '80%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '4px 5px 4px 5px', '1px solid #DBD4D8', '1px solid #E2E3E2', '1px solid #E2E3E2', '1px solid #DBD4D8', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', 'Tahoma, sans-serif', '100%', '1.0', '300', 'none', '', ''),
	(111, 'Campo testo grande (textarea)', 1, 'misto', 'form', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '80%', '120px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '6px   ', '1px solid #DBD4D8', '1px solid #E2E3E2', '1px solid #E2E3E2', '1px solid #DBD4D8', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(112, 'Campo contatto grande (icona foglio)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 111, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '6px 0px 16px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'label {\r\ndisplay:block;\r\n}'),
	(113, 'Contenitore Pannello ricerca (autocomplete)', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 120, 121, 0, 0, 0, '', 'block', '320px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'right', 'none', '4px 0px 0px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '82%', '1.5', '300', 'none', '', ''),
	(120, 'Campo testo pannello ricerca', 1, 'misto', 'form', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', '234px', '23px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '2px 0px 0px 0px', '2px 6px 4px 8px', 'none', 'none', 'none', 'none', 'transparent', '689', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', 'Tahoma, sans-serif', '80%', '1.2', '300', 'none', 'vertical-align: top;', ''),
	(114, 'PAT - Testo 100% nero', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Open Sans, Tahoma, serif', '100%', '1.5', '300', 'none', '', ''),
	(115, 'Contenitore Menu Canali', 1, 'menu', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '600px', '38px', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'repeat-x', '', 'sinistra', 'normal', '', '', 'Open Sans, sans-serif', '14px', '1.5', '300', 'uppercase', '', ''),
	(153, 'Immagine banner menu', 3, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', NULL, NULL),
	(116, 'Pulsante Menu Canali', 1, 'menu', 'bottone', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '9px 0px 0px 0px', '3px 20px 3px 20px', 'none', 'none', 'none', '1px solid  #D4D4D4', 'transparent', '0', 'no-repeat', '', 'sinistra', 'nowrap', '#646464', '', '', '', '1.5', '300', 'none', '\r\n', ''),
	(117, 'Rollover Pulsante Menu Canali', 1, 'menu', 'rollover', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '   ', '   ', 'none', 'none', 'none', '1px solid #D4D4D4', 'transparent', '0', 'repeat-x', '', 'sinistra', 'normal', '#354D62', '', '', '', '1.5', '300', 'none', '', ''),
	(118, 'Immagine 160px a sinistra con bordi', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '160px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 0px 0px 0px', '0px 12px 8px 0px', 'none', 'none', 'none', 'none', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', 'z-index: 3;', 'img {\r\n	border: 1px solid #EBE6D2;\r\n}'),
	(119, 'Contenitore pannello Tabella informazione indicizzazione', 1, 'pannello', 'nessuna', 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '500px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 20px 0px', '4px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Tahoma, sans-serif', '76%', '1.5', '300', 'none', 'overflow-x: scroll;', '.vistaTabella {\r\n	width:100%;	\r\n}\r\n.vistaTabella th {\r\n	background:#377BA8;\r\n	color:#FFFFFF;\r\n	padding:4px 6px;\r\n	text-transform:uppercase;\r\n	font-size:80%;\r\n	font-weight:bold;	\r\n}\r\n.vistaTabella th a{\r\n	color:#FFFFFF;\r\n}\r\n.vistaTabella td {\r\n	padding:4px 2px;\r\n}\r\n.dispari {\r\n	background: #FAFAFA !important;\r\n}\r\n.pari {\r\n	background: #FFFFFF;\r\n}'),
	(122, 'Contenitore pannello testata random', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', '192px', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', '', ''),
	(124, 'Titolo menu Il Comune', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '3px 8px 4px 8px', 'none', 'none', 'none', 'none', 'transparent', '585', 'no-repeat', '', 'sinistra', 'normal', '#72181F', '', 'Georgia, Times, serif', '130%', '1.5', '300', 'none', '', 'a{\r\ncolor: #72181F;\r\ntext-decoration:none;\r\n}\r\na:hover{\r\ncolor: #353535;\r\ntext-decoration:none;\r\n}'),
	(125, 'Titoli default (testo nero)', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '3px 8px 2px 8px', '1px solid  #EBE6D2', '1px solid  #EBE6D2', '1px solid  #EBE6D2', '1px solid  #EBE6D2', '#FFFFFF', '584', 'repeat-x', 'bottom', 'sinistra', 'normal', '#353535', '', 'Georgia, Times, serif', '130%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', 'a{\r\ncolor: #353535;\r\ntext-decoration:none;\r\n}\r\na:hover{\r\ncolor: #72181F;\r\ntext-decoration:none;\r\n}'),
	(126, 'Immagine 80px a sinistra', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '80px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '0px 8px 6px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', 'z-index: 3;\r\nmax-height: 80px;\r\noverflow: hidden;', ''),
	(128, 'Campo link (icona mondo)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '0px 0px 0px 20px', 'none', 'none', 'none', 'none', 'transparent', '587', 'no-repeat', 'left', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(127, 'Testo 100% rosso monoriga', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#890000', '#890000', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', ''),
	(129, 'Contenitore normale con titolo default rosso e sfondo beige', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px   ', 'none', 'none', '1px solid #D2D0BA', 'none', '#EBE6D2', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;', ''),
	(130, 'Istanza allineata sx per immagini correlate', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '32%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '10px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', '.inner_oggetto130 {\r\nwidth: 140px;\r\nmargin: 0px auto;\r\npadding: 0;\r\n/* text-align: center; */\r\n}'),
	(131, 'Immagine 120px', 1, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '120px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 0px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', 'max-height: 120px;\r\noverflow: hidden;', ''),
	(132, 'PAT - Titoli default (sfondo blu con spazio icona)', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 16px 0px', '8px 8px 8px 24px', 'none', 'none', 'none', 'none', '#377ba8', '0', 'repeat-x', 'bottom', 'sinistra', 'normal', '#FFFFFF', '', 'Open Sans, Tahoma, serif', '92%', '1.5', '600', 'uppercase', '', 'a{\r\ncolor: #FFFFFF;\r\ntext-decoration:none;\r\n}\r\na:hover{\r\ncolor: #F6F6F6;\r\ntext-decoration:none;\r\n}'),
	(133, 'Contenitore normale con titolo default blu', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(134, 'Campo per elenchi puntati (icona freccia)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '3px 0px 3px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', 'ul {\r\nlist-style-type: none;\r\npadding: 0px;\r\nmargin: 5px;\r\n}\r\nli {\r\nbackground-image: url(http://www.dominiopat.it/media/586_icona-freccia-rossa.gif);\r\nbackground-repeat: no-repeat;\r\nbackground-position: left;\r\npadding-left: 20px;\r\n}\r\na {\r\ntext-decoration: none;\r\n}\r\na:hover {\r\ncolor: #353535;\r\n}'),
	(140, 'Stile zona testata', 3, 'zona', 'nessuna', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(135, 'Campo testo normale (corto)', 1, 'misto', 'form', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', '100px', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '4px 5px 4px 5px', 'none', '1px solid  #EDEDED', '1px solid  #EDEDED', 'none', '#FFFFFF', '579', 'no-repeat', '', 'sinistra', 'normal', '#353535', '', 'Tahoma, sans-serif', '100%', '1.0', '300', 'none', '', ''),
	(136, 'PAT - Testo bold blu con icona freccia piena', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 2px 0px', '2px 0px 2px 20px', 'none', 'none', 'none', 'none', 'transparent', '590', 'no-repeat', 'left', 'sinistra', 'normal', '#377BA8', '#377BA8', 'Open Sans, Tahoma, serif', '118%', '1.2', '600', 'none', '', 'a { text-decoration: none; }\r\na:hover { text-decoration: underline; }'),
	(137, 'Testo bold rosso con icona documento', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 2px 0px', '2px 0px 2px 20px', 'none', 'none', 'none', 'none', 'transparent', '593', 'no-repeat', 'left', 'sinistra', 'normal', '#377BA8', '#377BA8', 'Tahoma, sans-serif', '100%', '1.5', '600', 'none', '', 'a { text-decoration: none; }\r\na:hover { text-decoration: underline; }'),
	(138, 'Testo 100% nero con rientro da sinistra', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 20px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', ''),
	(139, 'Pannello per mappa del Comune', 1, 'pannello', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px 0px 10px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#890000', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', '#map_canvas21 {\r\nheight: 300px;\r\n}'),
	(141, 'Stile zona colonna sinistra', 3, 'zona', 'nessuna', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '210px', 'auto', 'visible', 'relative', '', '', '', '', 'left', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(142, 'Stile zona colonna destra', 3, 'zona', 'nessuna', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '418px', 'auto', 'visible', 'relative', '', '', '', '', 'right', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(143, 'Stile zona chiusura', 3, 'zona', 'nessuna', 5, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', '1px solid  #EBE6D2', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(144, 'Stile zona centro', 3, 'zona', 'nessuna', 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 10px 0px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(145, 'Istanza normale con sfondo beige', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '10px   ', 'none', 'none', '1px solid  #D2D0BA', 'none', '#EBE6D2', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', ''),
	(146, 'Contenitore con titolo blu (no bordi) e margine laterale destro', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 5px 20px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(147, 'Testo 100% grigio monoriga (non sottolineato)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'inline', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#737373', '#737373', 'Tahoma, sans-serif', '100%', '1.5', '300', 'none', '', 'a { text-decoration: none; }\r\na:hover { text-decoration: underline; }'),
	(148, 'Titoli senza sfondo (testo blu)', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 15px 0px', '3px 0px 2px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#218DCD', '', 'Open Sans, Times, serif', '182%', '1.5', '300', 'none', '', 'a {text-decoration:none;color:#218DCD;}\r\na:hover {text-decoration:underline;}'),
	(149, 'Contenitore con titolo blu (no bordi) e margine laterale sinistro', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 20px 5px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(154, 'Contenitore menu banner in homepage', 1, 'oggetto', 'nessuna', 0, 0, 0, 0, 0, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#890000', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '', 'img:hover {\r\n	border:1px solid #80c6f0 !important;\r\n	box-shadow: 0 1px 3px #999999 !important;\r\n}'),
	(150, 'Titoli senza sfondo (testo rosso chiaro)', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 15px 0px', '3px 0px 2px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#A82E24', '', 'Georgia, Times, serif', '140%', '1.5', '300', 'uppercase', '', ''),
	(151, 'Contenitore con titolo rosso scuro e margine laterale destro', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 5px 20px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', ''),
	(152, 'Contenitore con titolo rosso chiaro e margine laterale sinistro', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 148, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 20px 5px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A82E24', 'Tahoma, sans-serif', '72%', '1.5', '300', 'none', '-moz-border-radius: 4px;\r\n-webkit-border-radius: 4px;\r\nborder-radius: 4px;', ''),
	(159, 'Titolo piccolo', 1, 'contenuto', 'editor', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '16px 0px 6px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#082F58', '#082F58', 'Open Sans, Times, serif', '100%', '1.0', '600', 'uppercase', '', ''),
	(155, 'Pannello Menu Footer', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', '', ''),
	(156, 'Titolo per menu', 1, 'misto', 'titolo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 10px 0px', '12px 20px 12px 20px', 'none', 'none', '2px solid  #D4D4D4', 'none', 'transparent', '682', 'repeat-x', '', 'sinistra', 'normal', '#218DCD', '', 'Open Sans, Times, serif', '158%', '1.5', '300', 'none', '', 'a {text-decoration:none;color:#218DCD;}'),
	(157, 'PAT - Pannello normale bianco (titolo blu)', 1, 'pannello', 'nessuna', 0, 1, 0, 0, 132, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', ''),
	(158, 'Pannello normale sfondo bianco con titolo chiaro', 1, 'pannello', 'nessuna', 0, 1, 0, 0, 148, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'both', '0px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '68%', '1.5', '300', 'none', '', ''),
	(163, 'Stile zona testata', 4, 'zona', 'nessuna', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 10px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(164, 'Stile zona colonna sinistra', 4, 'zona', 'nessuna', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '180px', 'auto', 'visible', 'relative', '', '', '', '', 'left', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(165, 'Stile zona colonna destra', 4, 'zona', 'nessuna', 3, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '180px', 'auto', 'visible', 'relative', '', '', '', '', 'right', 'none', '   ', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'repeat-x', '', 'destra', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(166, 'Stile zona chiusura', 4, 'zona', 'nessuna', 5, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '100%', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 0px 0px 0px', '0px 0px 0px 0px', '1px solid #EBE6D2', 'none', 'none', 'none', '#ffffff', '0', 'no-repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(167, 'Stile zona centro', 4, 'zona', 'nessuna', 4, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'relative', '', '', '', '', 'none', 'none', '0px 10px 0px 10px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'repeat', '', 'centrato', 'normal', '', '', '', '', '1.5', '300', 'none', NULL, ''),
	(168, 'Paragrafo NOTE', 1, 'contenuto', 'editor', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '10px 0px 10px 0px', '5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', NULL, NULL),
	(169, 'Immagine 192x80px (tagliata)', 3, 'oggetto', 'campo', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '192px', '80px', 'hidden', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px auto 6px auto', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'centrato', 'normal', '#353535', '#082F58', 'Tahoma, sans-serif', '100%', '1.2', '300', 'none', 'border:none;', 'img {border:none;margin:0 auto}'),
	(170, 'Istanza allineata sx (31%)', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', '31%', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'left', 'none', '10px 1% 10px 1%', '0px 0px 6px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(171, 'PAT - Titolo normale (Open Sans)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '5px 0px 5px 0px', '0px 0px 0px 0px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#208DCE', '#208DCE', 'Open Sans, Times, serif', '136%', '1.5', '600', 'none', '', 'a {text-decoration:none;}\r\na:hover {text-decoration:underline;}'),
	(172, 'PAT - Pannello menu HomePage', 3, 'pannello', 'nessuna', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E0E0E0', '1px solid  #E0E0E0', '1px solid  #E0E0E0', '1px solid  #E0E0E0', '#EFEFEF', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', '', ''),
	(173, 'PAT - Paragrafo con bordi e titolo blu', 3, 'contenuto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#A34D04', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', ''),
	(174, 'PAT - Pannello normale bianco (no titolo)', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 16, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.5', '300', 'none', '', ''),
	(176, 'Testo per titoli nel motore di ricerca (autocomplete)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '7px 0px 7px 0px', '0px 5px 0px 5px', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '#377BA8', '#377BA8', 'Open Sans, Times, serif', '130%', '1.2', '600', 'none', '', ''),
	(177, 'PAT - Istanza normale con doppio bordo', 1, 'oggetto', 'istanza', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 30px 0px', '0px 5px 6px 5px', 'none', 'none', '1px solid  #D4D4D4', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.5', '300', 'none', '', ''),
	(179, 'PAT - Testo 100% nero (destra)', 1, 'oggetto', 'campo', 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 0px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'destra', 'normal', '#353535', '#A82E24', 'Open Sans, Tahoma, serif', '100%', '1.5', '300', 'none', '', ''),
	(178, 'PAT - Pannello paragrafo Home', 1, 'pannello', 'nessuna', 0, 0, 0, 0, 16, 0, 73, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '6px 6px 6px 6px', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '1px solid  #E4E4E4', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#2C6387', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', ''),
	(180, 'Contenitore Pannello Banner Prov Salerno', 3, 'pannello', 'nessuna', 41, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 5px 0px', '0px   ', 'none', 'none', 'none', 'none', 'transparent', '0', 'no-repeat', '', 'sinistra', 'normal', '', '', '', '', '1.2', '300', 'none', '', ''),
	(181, 'PAT - Contenitore tabella bianco (con titolo)', 1, 'oggetto', 'nessuna', 0, 1, 0, 0, 16, 0, 0, 0, 0, 0, '', 'block', 'auto', 'auto', 'visible', 'static', '0px', '0px', '0px', '0px', 'none', 'none', '0px 0px 20px 0px', '0px   ', 'none', 'none', 'none', 'none', '#FFFFFF', '0', 'no-repeat', '', 'sinistra', 'normal', '#353535', '#377BA8', 'Open Sans, Tahoma, serif', '80%', '1.2', '300', 'none', '', '.vistaTabella {\r\n	width:100%;	\r\n}\r\n.vistaTabella th {\r\n	background: #ffffff;\r\n	color: #082F58;\r\n	padding: 0px 3px;\r\n	border-bottom: 1px solid #082F58;\r\n	text-transform: uppercase;\r\n	font-size: 92%;\r\n	font-weight: bold;	\r\n	}\r\n.vistaTabella th a{\r\n	color:#FFFFFF;\r\n}\r\n.vistaTabella td {\r\n	padding:6px 4px;\r\n}\r\n.dispari {\r\n	background: #FAFAFA !important;\r\n}\r\n.pari {\r\n	background: #FFFFFF;\r\n}\r\n.interfacciaPagine {\r\nmargin: 28px 0px 20px 0px;\r\nborder-top:2px solid #EBE6D2;\r\npadding: 5px 5px 0px 5px;\r\nfont-weight:bold;\r\ncolor:#353535;\r\nline-height:2.5;\r\nclear:both;\r\n}\r\n.interfacciaPagine a {\r\nmargin:8px 5px;\r\nfont-weight:normal;\r\npadding: 3px 5px;\r\nbackground: #F4F4F4;\r\nborder:1px solid #377BA8;\r\ncolor:#377BA8;\r\ntext-decoration:none;\r\n\r\n-moz-border-radius: 3px;\r\n-webkit-border-radius: 3px;\r\nborder-radius: 3px;\r\n\r\n}\r\n.interfacciaPagine a:hover {\r\nbackground: #377BA8;\r\ncolor:#FFFFFF;\r\nborder:1px solid #377BA8;\r\n}\r\n.pulsanteIstanza {\r\n	margin:0px 3px;\r\n	background: transparent;\r\n	padding:0px;\r\n}\r\ntd ul {\r\n	margin: 0px;\r\n	padding:0px;\r\n	list-style-type:none;\r\n	line-height:1.4;\r\n}\r\n');
/*!40000 ALTER TABLE `stili_elementi` ENABLE KEYS */;

-- Dump dei dati della tabella pat.template: 2 rows
DELETE FROM `template`;
/*!40000 ALTER TABLE `template` DISABLE KEYS */;
INSERT INTO `template` (`id`, `nome`, `descrizione`, `tipo_template`, `url_template`, `url_css_template`, `nome_file`, `id_mobile`, `tipo_lettura`, `id_proprietari_lettura`, `usa_stili_completi`, `stili_personalizzati`, `webfonts_google`, `codice_google`, `nome_sito`, `descrizione_sito`, `motori_ricerca`, `keywords`, `cookie_dominio`, `cookie_nome`, `id_stile_par_auto`, `testo_font`, `testo_colore`, `testo_size`, `testo_lineheight`, `paragrafo_margine`, `paragrafo_rientro`, `link_colore`, `stile_centro`, `usa_testata`, `stile_testata`, `usa_colonnasx`, `stile_colonnasx`, `usa_colonnadx`, `stile_colonnadx`, `usa_chiusura`, `stile_chiusura`, `usa_accesskey`, `usa_h1h2`, `corpo`, `corpo_larghezza`, `corpo_altezza`, `corpo_scroll`, `corpo_rientro`, `corpo_bgcolore`, `corpo_bgimg`, `corpo_bgimg_repeat`, `corpo_bgimg_position`, `corpo_bordo_alto`, `corpo_bordo_dx`, `corpo_bordo_basso`, `corpo_bordo_sx`, `sfondo_rientro`, `sfondo_bgcolore`, `sfondo_bgimg`, `sfondo_bgimg_repeat`, `sfondo_bgimg_position`, `calendario_sfondo_testata`, `calendario_colore_testata`, `calendario_sfondo1`, `calendario_sfondo2`, `calendario_colore1`, `calendario_colore2`, `msg_sfondo`, `msg_colore`, `msg_bordi`, `bot_sfondo`, `bot_bordi`, `bot_colore`, `player_colore`, `player_colore_leggero`, `player_sfondo_interfaccia`, `player_logo`, `tab_colore`, `tab_colore_head`, `tab_colore_testo`, `tab_colore_testo_head`, `tab_colore_bordo`, `tab_colore_celle_bordo`, `tab_colore_roll`, `tab_colore_ancore`, `stili_editor`) VALUES
	(1, 'Template di navigazione', 'Template per la navigazione (Home esclusa)', 'sistema', '', '', 'index', 0, 'normale', '0', 1, '#partedx {\r\n	margin-top:-102px;\r\n}\r\n#partesx {\r\n	margin-top:-22px;\r\n}\r\n.wait {\r\n    background: url(/grafica/ajax-loader.gif) no-repeat 280px 9px !important;\r\n}', '<link href=\'http://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic&amp;subset=latin,latin-ext\' rel=\'stylesheet\' type=\'text/css\' />\r\n<link rel="stylesheet" href="grafica/jquery-ui/css/custom-theme/jquery-ui-1.9.0.custom.css" type="text/css" media="screen" />\r\n<link rel="stylesheet" href="personalizzazioni/popover/popover.css" type="text/css" media="screen" />', '', '', NULL, 1, NULL, '', '', 0, 'Open Sans, Tahoma, serif', '#353535', '100%', '1.2', '8px 0px  ', '0px   ', '#A34D04', 4, 0, 1, 1, 2, 1, 3, 0, 5, 1, 1, 'centrato', '992px', 'auto', 'visible', '0px 0px 0px 0px', 'transparent', '0', 'repeat-x', '', 'none', 'none', 'none', 'none', '0px 0px 0px 0px', '#f7f7f7', '0', 'no-repeat', '', '#8C8C7D', '#FFFFFF', '#E3E3C7', '#EFEFDE', '#000000', '#FF6633', '#F3F1F2', '#353535', '#377BA8', '#DFF1FF', '#316AC5', '#333333', '#FFFFFF', '#FF9966', '#000000', '0', '#FFFFFF', '#890000', '#232323', '#FFFFFF', '#DBD4D8', '#FFFFFF', '#FFFFFF', '#890000', NULL),
	(3, 'Template Home page', 'Template per la sola Homepage del sito', 'sistema', '', '', 'index', 0, 'normale', '0', 1, '', '<link href=\'http://fonts.googleapis.com/css?family=Open+Sans:400,700,400italic,700italic&amp;subset=latin,latin-ext\' rel=\'stylesheet\' type=\'text/css\' />\r\n<link rel="stylesheet" href="grafica/jquery-ui/css/custom-theme/jquery-ui-1.9.0.custom.css" type="text/css" media="screen" />\r\n<link rel="stylesheet" href="personalizzazioni/popover/popover.css" type="text/css" media="screen" />', '', '', '', 1, '', '', '', 0, 'Open Sans, Tahoma, serif', '#353535', '100%', '1.2', '8px 0px  ', '0px   ', '#A34D04', 144, 0, 140, 0, 141, 1, 142, 0, 143, 1, 1, 'centrato', 'auto', 'auto', 'visible', '0px 0px 0px 0px', 'transparent', '0', 'repeat-x', '', 'none', 'none', 'none', 'none', '0px 0px 0px 0px', '#f7f7f7', '0', 'repeat-x', '', '#8C8C7D', '#FFFFFF', '#E3E3C7', '#EFEFDE', '#000000', '#FF6633', '#F3F1F2', '#353535', '#377BA8', '#DFF1FF', '#316AC5', '#333333', '#FFFFFF', '#FF9966', '#000000', '0', '#FFFFFF', '#208DCE', '#232323', '#FFFFFF', '#DBD4D8', '#FFFFFF', '#FFFFFF', '#13385D', '');
/*!40000 ALTER TABLE `template` ENABLE KEYS */;

-- Dump dei dati della tabella pat.template_css: 0 rows
DELETE FROM `template_css`;
/*!40000 ALTER TABLE `template_css` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_css` ENABLE KEYS */;

-- Dump dei dati della tabella pat.template_css_importati: 0 rows
DELETE FROM `template_css_importati`;
/*!40000 ALTER TABLE `template_css_importati` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_css_importati` ENABLE KEYS */;

-- Dump dei dati della tabella pat.template_lingue: 0 rows
DELETE FROM `template_lingue`;
/*!40000 ALTER TABLE `template_lingue` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_lingue` ENABLE KEYS */;

-- Dump dei dati della tabella pat.utenti: 3 rows
DELETE FROM `utenti`;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;
INSERT INTO `utenti` (`id`, `id_ente_admin`, `acl`, `editor_avanzato`, `attivo`, `nome`, `username`, `password`, `refresh_password`, `utente_sessione_time`, `utente_sessione_pagina`, `ultima_visita`, `data_registrazione`, `permessi`, `admin_accessibile`, `id_profilo_permessi`, `email`, `cellulare`, `nuova_password`, `actkey`, `msg_non_letti`, `admin_skin`, `admin_interfaccia`, `admin_avvisi`, `istatus`, `dtmlastvisited`) VALUES
	(-1, 0, '', 0, 0, 'Utente anonimo', 'Anonimo', '', 0, 0, 0, 0, 0, 0, 0, 0, NULL, '', NULL, '', NULL, 'classic', 'scomparsa', 'visualizza', 0, '0000-00-00 00:00:00'),
	(0, 0, '', 0, 1, 'Amministratore di sistema PAT', 'patroot', '', 0, 0, -2, 0, 0, 10, 0, 0, 'administrator@dominiopat.it', '0', NULL, '', NULL, 'classic', 'scomparsa', 'visualizza', 0, '0000-00-00 00:00:00'),
	(1, 1, '3', 0, 1, 'Amministratore PAT', 'patadmin', '', 0, 0, -2, 0, 0, 2, 0, 0, 'administrator@dominiopat.it', '', NULL, '', NULL, 'classic', 'visualizzata', 'visualizza', 0, '0000-00-00 00:00:00');

