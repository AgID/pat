-- --------------------------------------------------------
-- Host:                         192.168.0.4
-- Versione server:              5.5.46-0ubuntu0.14.04.2 - (Ubuntu)
-- S.O. server:                  debian-linux-gnu
-- HeidiSQL Versione:            9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

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


-- L’esportazione dei dati non era selezionata.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
