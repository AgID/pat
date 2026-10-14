-- Dump della struttura del database pat
CREATE DATABASE IF NOT EXISTS `pat`;
USE `pat`;

-- Dump della struttura di tabella pat.acl_profiles
CREATE TABLE IF NOT EXISTS `acl_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `is_admin` int NOT NULL DEFAULT '0',
  `is_system` tinyint NOT NULL,
  `versioning` int NOT NULL,
  `lock_user` tinyint NOT NULL DEFAULT '0' COMMENT 'Campo per il permesso di bloccare un utente',
  `advanced` tinyint NOT NULL DEFAULT '0' COMMENT 'Campo per il permesso avanzato di modifica del profilo',
  `export_csv` tinyint NOT NULL DEFAULT '0',
  `editor_wishing` varchar(20) DEFAULT 'base',
  `file_archive` int DEFAULT '1' COMMENT 'Campo per il permesso di visualizzazione del file manager',
  `archiving` tinyint DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Tabella Profili: in relazione con la tabella Utenti e con le tabelle sections_fo e sections_bo tramite la tabella permessi(Permits).';

-- Dump dei dati della tabella pat.acl_profiles: ~1 rows (circa)
INSERT INTO `acl_profiles` (`id`, `institution_id`, `is_admin`, `is_system`, `versioning`, `lock_user`, `advanced`, `export_csv`, `editor_wishing`, `file_archive`, `archiving`, `name`, `description`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 0, 0, 0, 0, 0, 'base', 3, 0, 'Default -  Amministratore completo dei contenuti', 'Default -  Amministratore completo dei contenuti', '2022-04-06 15:41:16', '2026-09-30 10:44:15');

-- Dump della struttura di tabella pat.activity_log
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `is_superadmin` int NOT NULL DEFAULT '0',
  `institution_id` int DEFAULT NULL,
  `object_id` int DEFAULT NULL COMMENT 'Id dell''archivio di riferimento',
  `record_id` int DEFAULT NULL COMMENT 'Id dell''istanza su cui è stata svolta l''azione',
  `parent_id` int DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL COMMENT 'Area dell''attività, serve per indicare se l''azione è stata eseguita su un oggetto, su un contenuto(pagina) oppure è un azione di autenticazione',
  `client_info` text,
  `ip_address` varchar(191) DEFAULT NULL,
  `action` varchar(191) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL COMMENT 'Identifica il tipo dell''azione eseguita',
  `description` text,
  `platform` varchar(50) DEFAULT NULL,
  `origin` varchar(50) DEFAULT 'web',
  `uri` varchar(255) DEFAULT NULL,
  `referer` varchar(191) DEFAULT NULL,
  `request_post` text,
  `request_get` text,
  `request_file` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb3;


-- Dump della struttura di tabella pat.attachment_label
CREATE TABLE IF NOT EXISTS `attachment_label` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attachment_label_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC COMMENT='Nome allegati';


-- Dump della struttura di tabella pat.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `cat_id` int DEFAULT NULL,
  `owner_id` int DEFAULT NULL,
  `sort` int NOT NULL,
  `archive_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_id` int DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raw_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orig_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ext` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_width` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_height` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_size_str` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indexable` tinyint(1) DEFAULT '0' COMMENT 'Utilizzato per dire se l''allegato è indicizzabile o meno dai motori di ricerca',
  `active` tinyint(1) DEFAULT '1' COMMENT 'Utilizzato per dire se l''allegato è pubblico o meno',
  `bdncp_cat` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella per gestire gli allegati.';


-- Dump della struttura di tabella pat.attempts
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(101) DEFAULT NULL,
  `client_info` varchar(225) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.concurrent_sess
CREATE TABLE IF NOT EXISTS `concurrent_sess` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `platform` text COLLATE utf8mb4_unicode_ci,
  `browser` text COLLATE utf8mb4_unicode_ci,
  `device` text COLLATE utf8mb4_unicode_ci,
  `ip` text COLLATE utf8mb4_unicode_ci,
  `browser_private_mode` int DEFAULT NULL,
  `sess_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destroyed_sess_id` int DEFAULT '0',
  `created_at` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime DEFAULT NULL,
  `check_state` int DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dump della struttura di tabella pat.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `opt_key` varchar(191) NOT NULL,
  `opt_value` longtext NOT NULL,
  `opt_group` varchar(191) DEFAULT 'null',
  `opt_description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.configs: ~1 rows (circa)
INSERT INTO `configs` (`id`, `institution_id`, `opt_key`, `opt_value`, `opt_group`, `opt_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (2, NULL, 'last_visit_time_limit', '2592000', 'null', 'tempo limite di accesso permesso dopo l\'ultima visita. 30 GG', NULL, NULL, NULL);


-- Dump della struttura di tabella pat.content_section_fo
CREATE TABLE IF NOT EXISTS `content_section_fo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int DEFAULT NULL,
  `section_fo_id` int NOT NULL,
  `section_fo_parent_id` int DEFAULT NULL,
  `institution_id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(500) DEFAULT NULL,
  `sort` int NOT NULL,
  `content` longtext NOT NULL,
  `o_id` int DEFAULT NULL,
  `deleted` int DEFAULT '0',
  `last_update_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.contraent_choice
CREATE TABLE IF NOT EXISTS `contraent_choice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(2) DEFAULT NULL,
  `deleted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.contraent_choice: 29 rows
INSERT INTO `contraent_choice` (`id`, `name`, `code`, `deleted`) VALUES
  (1, '01-PROCEDURA APERTA', NULL, 0),
  (2, '02-PROCEDURA RISTRETTA', NULL, 0),
  (3, '03-PROCEDURA NEGOZIATA PREVIA PUBBLICAZIONE', NULL, 0),
  (4, '04-PROCEDURA NEGOZIATA SENZA PREVIA PUBBLICAZIONE', NULL, 0),
  (5, '05-DIALOGO COMPETITIVO', NULL, 0),
  (6, '06-PROCEDURA NEGOZIATA SENZA PREVIA INDIZIONE DI GARA (SETTORI SPECIALI)', NULL, 0),
  (7, '07-SISTEMA DINAMICO DI ACQUISIZIONE', NULL, 0),
  (8, '08-AFFIDAMENTO IN ECONOMIA - COTTIMO FIDUCIARIO', NULL, 0),
  (9, '14-PROCEDURA SELETTIVA EX ART 238 C.7 D.LGS. 163/2006', NULL, 0),
  (10, '17-AFFIDAMENTO DIRETTO EX ART. 5 DELLA LEGGE 381/91', NULL, 0),
  (11, '21-PROCEDURA RISTRETTA DERIVANTE DA AVVISI CON CUI SI INDICE LA GARA', NULL, 0),
  (12, '22-PROCEDURA NEGOZIATA CON PREVIA INDIZIONE DI GARA (SETTORI SPECIALI)', NULL, 0),
  (13, '23-AFFIDAMENTO DIRETTO', NULL, 0),
  (14, '24-AFFIDAMENTO DIRETTO A SOCIETA\' IN HOUSE', NULL, 0),
  (15, '25-AFFIDAMENTO DIRETTO A SOCIETA\' RAGGRUPPATE/CONSORZIATE O CONTROLLATE NELLE CONCESSIONI E NEI PARTENARIATI', NULL, 0),
  (16, '26-AFFIDAMENTO DIRETTO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE', NULL, 0),
  (17, '27-CONFRONTO COMPETITIVO IN ADESIONE AD ACCORDO QUADRO/CONVENZIONE', NULL, 0),
  (18, '28-PROCEDURA AI SENSI DEI REGOLAMENTI DEGLI ORGANI COSTITUZIONALI', NULL, 0),
  (19, '29-PROCEDURA RISTRETTA SEMPLIFICATA', NULL, 0),
  (20, '30-PROCEDURA DERIVANTE DA LEGGE REGIONALE', NULL, 0),
  (21, '31-AFFIDAMENTO DIRETTO PER VARIANTE SUPERIORE AL 20% DELL\'IMPORTO CONTRATTUALE', NULL, 0),
  (22, '32-AFFIDAMENTO RISERVATO', NULL, 0),
  (23, '33-PROCEDURA NEGOZIATA PER AFFIDAMENTI SOTTO SOGLIA', NULL, 0),
  (24, '34-PROCEDURA ART.16 COMMA 2-BIS DPR 380/2001 PER OPERE URBANIZZAZIONE A SCOMPUTO PRIMARIE SOTTO SOGLIA COMUNITARIA', NULL, 0),
  (25, '35-PARTERNARIATO PER L\'NNOVAZIONE', NULL, 0),
  (26, '36-AFFIDAMENTO DIRETTO PER LAVORI SERVIZI O FORNITURE SUPPLEMENTARI', NULL, 0),
  (27, '37-PROCEDURA COMPETITIVA CON NEGOZIAZIONE', NULL, 0),
  (28, '38-PROCEDURA DISCIPLINATA DA REGOLAMENTO INTERNO PER SETTORI SPECIALI', NULL, 0),
  (29, '39-AFFIDAMENTO DIRETTO PER MODIFICHE CONTRATTUALI O VARIANTI PER LE QUALI È NECESSARIA UNA NUOVA PROCEDURA DI AFFIDAMENTO', '39', 0);


-- Dump della struttura di tabella pat.cpv_codes
CREATE TABLE IF NOT EXISTS `cpv_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;


-- Dump della struttura di tabella pat.data_historical_personnel
CREATE TABLE IF NOT EXISTS `data_historical_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `personnel_id` int DEFAULT '0',
  `historical_role` varchar(255) DEFAULT NULL,
  `historical_structure` varchar(255) DEFAULT NULL,
  `historical_from_date` datetime DEFAULT NULL,
  `historical_to_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Dati dello storico incarichi del personale';


-- Dump della struttura di tabella pat.data_monitoring_proceedings
CREATE TABLE IF NOT EXISTS `data_monitoring_proceedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proceeding_id` int NOT NULL,
  `year` int DEFAULT NULL,
  `year_concluded_proceedings` int DEFAULT NULL,
  `percentage_year_concluded_proceedings` int DEFAULT NULL,
  `conclusion_days` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='dati monitoraggio procedimenti';


-- Dump della struttura di tabella pat.institution_links
CREATE TABLE IF NOT EXISTS `institution_links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `sort` int DEFAULT NULL,
  `position` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_links_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella per la personalizzazione dei link in testata e footer';


-- Dump della struttura di tabella pat.institution_type
CREATE TABLE IF NOT EXISTS `institution_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `label_institution_type_id` int NOT NULL COMMENT 'relazione con tabella label_institution_type.',
  `name` varchar(191) NOT NULL,
  `state` tinyint DEFAULT '1',
  `type_name` varchar(255) DEFAULT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_etrasp_tipoentisemplice su vecchio PAT.';


-- Dump della struttura di tabella pat.institutions
CREATE TABLE IF NOT EXISTS `institutions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_creator` int NOT NULL COMMENT 'label ex PAT: id_creatore',
  `institution_type_id` int DEFAULT NULL COMMENT 'label ex PAT: tipo_ente',
  `trasp_responsible_user_id` int DEFAULT NULL COMMENT 'ID utente responsabile trasparenza',
  `state` tinyint DEFAULT '1',
  `full_name_institution` varchar(191) NOT NULL,
  `short_institution_name` varchar(191) DEFAULT NULL,
  `vat` varchar(45) NOT NULL COMMENT 'campo p_iva su vecchio PAT.',
  `fiscal_code` varchar(45) DEFAULT NULL,
  `email_address` varchar(45) NOT NULL,
  `certified_email_address` varchar(191) NOT NULL,
  `institutional_website_name` varchar(45) DEFAULT NULL COMMENT 'nome portale istituzionale.',
  `institutional_website_url` varchar(225) DEFAULT NULL COMMENT 'url portale istituzionale.',
  `top_level_institution_name` varchar(255) DEFAULT NULL COMMENT 'nome ente di appartenenza.',
  `top_level_institution_url` varchar(225) DEFAULT NULL COMMENT 'url ente di appartenenza.',
  `welcome_text` text,
  `footer_text` text,
  `accessibility_text` text,
  `address_street` varchar(191) NOT NULL,
  `address_zip_code` varchar(45) NOT NULL COMMENT 'campo indirizzo_cap su vecchio PAT.',
  `address_city` varchar(45) NOT NULL COMMENT 'label ex PAt indirizzo_comune',
  `address_province` varchar(45) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `two_factors_identification` tinyint DEFAULT NULL COMMENT 'Per vedere se è abilitata l''autenticazione a due fattori',
  `trasparenza_logo_file` varchar(225) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `domain_cookies` varchar(255) DEFAULT NULL,
  `trasparenza_urls` varchar(225) DEFAULT NULL,
  `url_pat` varchar(255) DEFAULT NULL COMMENT 'url_etrasparenza su vecchio pat',
  `bulletin_board_url` varchar(225) DEFAULT NULL COMMENT 'label ex PAT: url_albopretorio',
  `bulletin_board_url_name` varchar(225) DEFAULT NULL,
  `online_register_id` int DEFAULT NULL COMMENT 'id albo online',
  `customer_support` tinyint DEFAULT NULL,
  `simple_logo_file` varchar(225) DEFAULT NULL,
  `custom_css` varchar(255) DEFAULT NULL,
  `favicon_file` varchar(225) DEFAULT NULL,
  `opendata_channel` tinyint DEFAULT NULL,
  `show_update_date` tinyint DEFAULT NULL COMMENT 'campo mostra_data_aggiornamento su vecchio PAT.',
  `indexable` tinyint DEFAULT '1',
  `show_regulation_in_structure` int DEFAULT '1',
  `tabular_display_org_ind_pol` tinyint DEFAULT '0' COMMENT 'campo visualizzazione_tabellare_org_ind_pol su vecchio PAT.',
  `max_users` int DEFAULT NULL COMMENT 'numeor masimo di utenti attivabili per l''ente.',
  `client_code` varchar(45) DEFAULT NULL COMMENT 'codice cliente.',
  `smtp_user` varchar(191) DEFAULT NULL,
  `smtp_from` varchar(191) DEFAULT NULL,
  `smtp_pass` varchar(191) DEFAULT NULL,
  `smtp_host` varchar(191) DEFAULT NULL,
  `smtp_port` varchar(45) DEFAULT NULL,
  `smtp_security` varchar(10) DEFAULT NULL,
  `smtp_auth` int DEFAULT NULL,
  `show_smtp_auth` int DEFAULT NULL,
  `pec_smtp_user` varchar(191) DEFAULT NULL,
  `pec_smtp_from` varchar(191) DEFAULT NULL,
  `pec_smtp_pass` varchar(191) DEFAULT NULL,
  `pec_smtp_host` varchar(191) DEFAULT NULL,
  `pec_smtp_port` varchar(45) DEFAULT NULL,
  `pec_smtp_security` varchar(10) DEFAULT NULL,
  `pec_show_smtp_auth` int DEFAULT NULL,
  `email_notifications` varchar(191) DEFAULT NULL,
  `publication_responsible` varchar(191) DEFAULT NULL,
  `privacy_url` varchar(191) DEFAULT NULL,
  `last_visit_time_limit` int DEFAULT '2592000',
  `personnel_roles` text,
  `statistics_tracking_code` text,
  `favicon` varchar(191) DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1' COMMENT 'indica se un ente è attivo o meno',
  `link_site_home` tinyint DEFAULT '0' COMMENT 'Indica quale link mettere sul nome dell''ente nel footer e nel header',
  `deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella etrasp_enti su vecchio PAT.';


INSERT INTO `institutions` (`id`, `id_creator`, `institution_type_id`, `trasp_responsible_user_id`, `state`, `full_name_institution`, `short_institution_name`, `vat`, `fiscal_code`, `email_address`, `certified_email_address`, `institutional_website_name`, `institutional_website_url`, `top_level_institution_name`, `top_level_institution_url`, `welcome_text`, `footer_text`, `accessibility_text`, `address_street`, `address_zip_code`, `address_city`, `address_province`, `phone`, `two_factors_identification`, `trasparenza_logo_file`, `activation_date`, `expiration_date`, `domain_cookies`, `trasparenza_urls`, `url_pat`, `bulletin_board_url`, `bulletin_board_url_name`, `online_register_id`, `customer_support`, `simple_logo_file`, `custom_css`, `favicon_file`, `opendata_channel`, `show_update_date`, `indexable`, `show_regulation_in_structure`, `tabular_display_org_ind_pol`, `max_users`, `client_code`, `smtp_user`, `smtp_from`, `smtp_pass`, `smtp_host`, `smtp_port`, `smtp_security`, `smtp_auth`, `show_smtp_auth`, `pec_smtp_user`, `pec_smtp_from`, `pec_smtp_pass`, `pec_smtp_host`, `pec_smtp_port`, `pec_smtp_security`, `pec_show_smtp_auth`, `email_notifications`, `publication_responsible`, `privacy_url`, `last_visit_time_limit`, `personnel_roles`, `statistics_tracking_code`, `favicon`, `active`, `link_site_home`, `deleted`, `created_at`, `deleted_at`, `updated_at`) VALUES
  (1, 1, 1, 1, 1, 'Comune di Esempio', 'comune_di_esempio', '01722270665', NULL, 'comune@esempio.it', 'pec@esempio.it', 'Example', 'http://www.example.com', 'Ente di appartenenza', '', '<p>Vulputate elementum efficitur parturient class felis. Quam malesuada taciti nullam tellus nostra sed sapien lacus tincidunt risus. Commodo vivamus lorem torquent himenaeos aliquam tortor. Faucibus massa congue maximus nam ridiculus senectus quam maecenas. Eleifend iaculis praesent porta parturient facilisis cubilia velit neque quam. Amet nisi posuere iaculis primis donec class eleifend nullam gravida.</p>\r\n\r\n<p>Vitae dapibus litora congue semper finibus metus. Ullamcorper eu tortor senectus consectetuer id litora urna velit suscipit. Efficitur quisque etiam inceptos duis luctus nunc justo euismod convallis. Convallis feugiat nullam urna nulla cubilia pharetra. Primis nec lacinia pharetra tempor taciti neque ligula suspendisse vel eleifend. Nostra dui sit tempor euismod facilisis bibendum ligula.</p>\r\n', '<p>Footer personalizzato</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\r\n', NULL, 'Via XX Settembre', '67055', 'Roma', 'AQ', '0000000000', 1, 'NULL', '2022-10-20 11:07:17', '2027-09-23 15:29:17', '', 'http://patos-riuso.local', '', 'http://patos-riuso.local', '', 222, 0, NULL, 'index.css', 'NULL', NULL, 1, 1, 0, 1, NULL, NULL, '', '', '', '', '', NULL, 1, 1, '', '', NULL, '', '', 'no', NULL, NULL, '', '', 2592000, NULL, '', NULL, 1, 0, 0, '2021-10-06 10:42:04', NULL, '2026-09-23 15:29:17');



-- Dump della struttura di tabella pat.object_absence_rates
CREATE TABLE IF NOT EXISTS `object_absence_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'relazione con tabella object_structures.',
  `state` tinyint DEFAULT '1',
  `structure_name` varchar(255) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL COMMENT 'periodo',
  `year` int DEFAULT NULL,
  `presence_percentage` decimal(10,2) DEFAULT NULL COMMENT 'campo presenza su vecchio PAT',
  `total_absence` decimal(10,2) DEFAULT NULL COMMENT 'campo assenza_totale su vecchio PAT.',
  `absence_illness` decimal(2,0) DEFAULT NULL COMMENT 'campo assenza_malattia su vecchio PAT.',
  `illness_days` decimal(2,0) DEFAULT NULL COMMENT 'campo giorni_malattia su vecchio PAT.',
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_tassi_assenza su vecchio PAT.';


-- Dump della struttura di tabella pat.object_assignments
CREATE TABLE IF NOT EXISTS `object_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL COMMENT 'id_propietario',
  `institution_id` int NOT NULL COMMENT 'ente_id',
  `object_structures_id` int DEFAULT NULL COMMENT 'struttura organizzativa. Relazione con object_structures',
  `related_assignment_id` int DEFAULT NULL,
  `state` tinyint DEFAULT '1',
  `typology` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'Incarico',
  `consulting_type` varchar(255) DEFAULT NULL COMMENT 'tipo_consulenza.',
  `name` varchar(255) DEFAULT NULL COMMENT 'campo nominativo.',
  `object` varchar(600) DEFAULT NULL,
  `assignment_type` varchar(255) DEFAULT NULL COMMENT 'tipo_incarico',
  `assignment_start` datetime DEFAULT NULL,
  `assignment_end` datetime DEFAULT NULL,
  `end_of_assignment_not_available` tinyint DEFAULT NULL,
  `end_of_assignment_not_available_txt` text,
  `compensation` decimal(15,2) DEFAULT NULL,
  `compensation_provided` decimal(15,2) DEFAULT NULL COMMENT 'compenso erogato.',
  `compensation_provided_date` datetime DEFAULT NULL COMMENT 'compenso erogato data.',
  `liquidation_date` datetime DEFAULT NULL COMMENT 'data liquidazione',
  `liquidation_year` int DEFAULT NULL,
  `variable_compensation` text COMMENT 'compenso_variabile',
  `notes` text,
  `acts_extremes` text COMMENT 'estremi atti.',
  `attachments_id` int DEFAULT NULL,
  `assignment_reason` varchar(255) DEFAULT NULL,
  `dirigente` int DEFAULT NULL COMMENT 'Campo per la retrocompatibilità',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_incarichi su vecchio PAT.';


-- Dump della struttura di tabella pat.object_balance_sheets
CREATE TABLE IF NOT EXISTS `object_balance_sheets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_measure_id` int DEFAULT NULL COMMENT 'ID del provvedmento associato al bilancio',
  `state` tinyint DEFAULT '1',
  `name` varchar(600) DEFAULT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `description` text,
  `attachments_id` int DEFAULT NULL,
  `register_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_bilanci su vecchio PAT.';


-- Dump della struttura di tabella pat.object_bdncp_general_acts_documents
CREATE TABLE IF NOT EXISTS `object_bdncp_general_acts_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `object` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_date` datetime DEFAULT NULL,
  `external_link` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typology` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cup` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `financing_amount` decimal(15,2) DEFAULT NULL,
  `financial_sources` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procedural_implementation_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishing_start` datetime DEFAULT NULL COMMENT 'Data di inizio pubblicazione',
  `publishing_end` datetime DEFAULT NULL COMMENT 'Data di fine pubblicazione',
  `unfixed` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COMMENT='Soluzioni tecnologiche BDNCP';


-- Dump della struttura di tabella pat.object_bdncp_procedure
CREATE TABLE IF NOT EXISTS `object_bdncp_procedure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `procurement_id` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID appalto',
  `owner_id` int NOT NULL,
  `object` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cig` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typology` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_procedure_id` int DEFAULT NULL COMMENT 'Id della procedura relativa ad un avviso',
  `alert_date` datetime DEFAULT NULL COMMENT 'Data avviso',
  `liquidation_date` datetime DEFAULT NULL COMMENT 'Data della liquidazione',
  `amount_liquidated` decimal(15,2) DEFAULT NULL,
  `multicig` tinyint DEFAULT NULL,
  `publish_father_document` tinyint DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `bdncp_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eproc_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_debate_check` tinyint DEFAULT '0' COMMENT 'checkbox Dibattito pubblico',
  `public_debate_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Dibattito Pubblico',
  `notice_documents_check` tinyint DEFAULT '0' COMMENT 'checkbox Documenti di gara ',
  `notice_documents_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Documenti di gara',
  `judging_commission_check` tinyint DEFAULT '0' COMMENT 'checkbox Commissione giudicatrice',
  `judging_commission_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Commissione Giudicatrice',
  `equal_opportunities_af_check` tinyint DEFAULT '0' COMMENT 'checkbox Pari opportunita fase Affidamento',
  `equal_opportunities_af_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Pari oportunità fase affidamento',
  `local_public_services_check` tinyint DEFAULT '0' COMMENT 'checkbox Servizi Pubblici Locali',
  `local_public_services_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note servizi pubblici locali',
  `advisory_board_technical_check` tinyint DEFAULT '0' COMMENT 'checkbox Collegio consultivo tecnico',
  `advisory_board_technical_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note collegio consultivo tecnico',
  `equal_opportunities_es_check` tinyint DEFAULT '0' COMMENT 'checkbox Pari opportunita fase esecutiva',
  `equal_opportunities_es_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note pari opportunita fase esecutiva',
  `free_contract_check` tinyint DEFAULT '0' COMMENT 'checkbox Contratto gratuito e forme speciali di partenariato',
  `free_contract_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Stai pubblicando un Contratto gratuito e forme speciali di partenariato?',
  `emergency_foster_check` tinyint DEFAULT '0' COMMENT 'checkbox Affidamenti di somma urgenza',
  `emergency_foster_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Affidamenti di somma urgenza',
  `foster_procedure_check` tinyint DEFAULT '0' COMMENT 'checkbox Procedura di Affidamento',
  `foster_procedure_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note procedura di affidamento',
  `publishing_start` datetime DEFAULT NULL COMMENT 'Data di inizio pubblicazione',
  `publishing_end` datetime DEFAULT NULL COMMENT 'Data di fine pubblicazione',
  `unfixed` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `__tag` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella per le PROCEDURE DELIBERA 261/2023';


-- Dump della struttura di tabella pat.object_charges
CREATE TABLE IF NOT EXISTS `object_charges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `type` varchar(255) DEFAULT NULL,
  `citizen` tinyint DEFAULT '0',
  `companies` tinyint DEFAULT '0',
  `title` varchar(600) DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `description` text,
  `info_url` varchar(255) DEFAULT NULL,
  `normative_id` int DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_oneri su vecchio PAT.';


-- Dump della struttura di tabella pat.object_commissions
CREATE TABLE IF NOT EXISTS `object_commissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `name` varchar(225) DEFAULT NULL,
  `typology` varchar(25) DEFAULT NULL,
  `president_id` int DEFAULT NULL,
  `description` text,
  `skills` text,
  `image` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `order` int DEFAULT NULL,
  `archive` tinyint DEFAULT NULL COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text COMMENT 'informazioni staticizzate a seguito dell''archiviazione',
  `archived_end_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `archived` int DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_commissioni su vecchio PAT.';


-- Dump della struttura di tabella pat.object_company
CREATE TABLE IF NOT EXISTS `object_company` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'campo ragione sociale.',
  `typology` varchar(255) DEFAULT NULL,
  `description` text COMMENT 'campo misura su vecchio PAT.',
  `participation_measure` varchar(255) DEFAULT NULL COMMENT 'campo misura.',
  `duration` varchar(255) DEFAULT NULL,
  `year_charges` text COMMENT 'campo oneri_anno su vecchio PAT.',
  `treatment_assignments` text,
  `website_url` varchar(255) DEFAULT NULL,
  `balance` text COMMENT 'campo risultati bilancio.',
  `inconferability_dec_link` varchar(255) DEFAULT NULL COMMENT 'campo link dichiarazione di inconferibilità',
  `incompatibility_dec_link` varchar(255) DEFAULT NULL COMMENT 'campo link dichiarazione di incompatibilita',
  `notes` text,
  `archived` int NOT NULL DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_info` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_societa su vecchio PAT. Relazione con personale tramite la tabella rel_personnel_company.';


-- Dump della struttura di tabella pat.object_contest
CREATE TABLE IF NOT EXISTS `object_contest` (
  `id` int NOT NULL AUTO_INCREMENT,
  `related_contest_id` int DEFAULT NULL COMMENT 'campo concorso_collegato su vecchio PAT.',
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con object_structures.',
  `object_measure_id` int DEFAULT NULL COMMENT 'ID provvedimento associato al bando di concorso',
  `state` tinyint DEFAULT '1',
  `typology` varchar(225) DEFAULT NULL,
  `object` text,
  `province_office` varchar(45) DEFAULT NULL,
  `city_office` varchar(45) DEFAULT NULL,
  `office_address` varchar(45) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `expiration_contest_date` datetime DEFAULT NULL,
  `expiration_time` varchar(45) DEFAULT NULL,
  `expected_expenditure` varchar(45) DEFAULT NULL COMMENT 'campo spesa_prevista su vecchio PAT.',
  `expenditures_made` varchar(45) DEFAULT NULL COMMENT 'campo spese_fatte su vecchio PAT.',
  `hired_employees` int DEFAULT NULL COMMENT 'campo dipendenti_assunti su vecchio PAT.',
  `external_assignment` text,
  `description` text,
  `test_calendar` text,
  `evaluation_criteria` text,
  `traces_written_tests` text,
  `attachments_id` int DEFAULT NULL,
  `publication_state` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_concorsi su vecchio PAT.';


-- Dump della struttura di tabella pat.object_contests_acts
CREATE TABLE IF NOT EXISTS `object_contests_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con object_structures',
  `object_personnel_id` int DEFAULT NULL COMMENT 'campo rup su vecchio PAT.Relazione con object_personnel.',
  `relative_procedure_id` int DEFAULT NULL,
  `object_measure_id` int DEFAULT NULL COMMENT 'ID provvedimento associato',
  `relative_notice_id` int DEFAULT NULL COMMENT 'Bando relativo al lotto',
  `qualification_requirement_id` int DEFAULT NULL COMMENT 'ID del requisito di qualificazione',
  `type` varchar(45) DEFAULT NULL,
  `typology` varchar(20) DEFAULT NULL,
  `object` varchar(800) DEFAULT NULL,
  `sector` varchar(50) DEFAULT NULL,
  `bdncp_link` varchar(255) DEFAULT NULL,
  `cig` varchar(255) DEFAULT NULL,
  `is_multicig` tinyint DEFAULT '0',
  `state` tinyint DEFAULT '1',
  `attachments_id` int DEFAULT NULL,
  `contract` varchar(50) DEFAULT NULL,
  `customize_admin_data` tinyint DEFAULT '0' COMMENT 'Personalizzazione dati amministrazione',
  `adjudicator_name` varchar(255) DEFAULT NULL COMMENT 'campo denominazione_aggiudicatrice su vecchio PAT.',
  `adjudicator_data` varchar(255) DEFAULT NULL COMMENT 'campo dati_aggiudicatrice su vecchio PAT.',
  `administration_type` varchar(255) DEFAULT NULL COMMENT 'campo tipo_amministrazione su vecchio PAT.',
  `province_office` varchar(45) DEFAULT NULL,
  `municipality_office` varchar(45) DEFAULT NULL,
  `office_address` varchar(45) DEFAULT NULL,
  `istat_office` varchar(45) DEFAULT NULL COMMENT 'campo sede_istat su vecchio PAT.',
  `nuts_office` varchar(45) DEFAULT NULL COMMENT 'Sede di gara - Codice NUTS',
  `no_amount` varchar(45) DEFAULT NULL COMMENT 'campo senza_importo su vecchio PAT.',
  `asta_base_value` decimal(15,2) DEFAULT NULL COMMENT 'valore base asta',
  `anac_year` int DEFAULT NULL COMMENT 'data atto',
  `act_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `guue_date` datetime DEFAULT NULL COMMENT 'Data di pubblicazione del bando di gara sulla G.U.U.E.',
  `guri_date` datetime DEFAULT NULL COMMENT 'Data di pubblicazione del bando di gara sulla G.U.R.I.',
  `cpv_code_id` int DEFAULT NULL COMMENT 'ID del codice CPV',
  `codice_scp` varchar(255) DEFAULT NULL,
  `url_scp` varchar(255) DEFAULT NULL,
  `details` text,
  `contraent_choice` varchar(255) DEFAULT NULL COMMENT 'campo scelta_contraente su vecchio PAT.',
  `typology_result` varchar(255) DEFAULT NULL COMMENT 'campo tipologia_esito su vecchio PAT, per esito/affidamento.',
  `award_amount_value` decimal(15,2) DEFAULT NULL COMMENT 'campo valore_importo_aggiudicazione su vecchio PAT, usato per esito/affidamento',
  `amount_liquidated` decimal(15,2) DEFAULT NULL COMMENT 'campo importo_liquidato su vecchio PAT, utilizzato per liquidazione',
  `publication_date_type` varchar(255) DEFAULT NULL COMMENT 'campo tipologia_data_pubblicazione su vecchio PAT, usato per esiti/affidamenti.',
  `decree_163` int DEFAULT '0',
  `work_start_date` datetime DEFAULT NULL COMMENT 'campo data_inizio_lavori su vecchio PAT, utilizzato per esito/affidamento',
  `work_end_date` datetime DEFAULT NULL COMMENT 'campo data_inizio_lavori su vecchio PAT, utilizzato per esito/affidamento.',
  `contracting_stations_publication_date` datetime DEFAULT NULL COMMENT 'campo data_pubblicazione_sa su vecchio PAT, utilizzato per esiti/affidamenti',
  `number_readings` int DEFAULT NULL COMMENT 'campo numero letture vecchio PAT',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `source_id` varchar(255) DEFAULT NULL COMMENT 'campo id_ori su vecchio PAT',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_gare_atti (bandi) su ex PAT.';


-- Dump della struttura di tabella pat.object_grants
CREATE TABLE IF NOT EXISTS `object_grants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con tabella object_structures.',
  `object_regulations_id` int DEFAULT NULL COMMENT 'campo regolamento su vecchio PAT. Relazione con object_regulations.',
  `grant_id` int DEFAULT NULL COMMENT 'sovvenzione relativa',
  `state` tinyint DEFAULT '1',
  `beneficiary_name` varchar(191) DEFAULT NULL COMMENT 'campo nominativo.',
  `fiscal_data_not_available` tinyint DEFAULT '0' COMMENT 'campo fiscali_non_disponibili su vecchio PAT.',
  `fiscal_data` text,
  `object` varchar(800) DEFAULT NULL,
  `typology` varchar(20) NOT NULL DEFAULT 'Sovvenzione',
  `type` varchar(20) NOT NULL DEFAULT 'grant',
  `concession_act_date` datetime DEFAULT NULL COMMENT 'Data atto di concessione.',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `concession_amount` decimal(15,2) DEFAULT NULL COMMENT 'campo compenso su vecchio PAT.',
  `detection_mode` text COMMENT 'campo modo_individuazione su vecchio PAT.',
  `privacy` tinyint DEFAULT NULL COMMENT 'campo omissis privacy.',
  `reference_date` datetime DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `compensation_paid` decimal(15,2) DEFAULT NULL COMMENT 'campo compenso_erogato su vecchio PAT.',
  `compensation_paid_date` int DEFAULT NULL COMMENT 'campo data_compenso_erogato su vecchio PAT.',
  `notes` text,
  `project_link` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_sovvenzioni su vecchio PAT.';


-- Dump della struttura di tabella pat.object_interventions
CREATE TABLE IF NOT EXISTS `object_interventions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `name` varchar(60) DEFAULT NULL,
  `description` text,
  `derogations` text,
  `time_limits` datetime DEFAULT NULL COMMENT 'campo termini_temporali su vecchio PAT.',
  `estimated_cost` varchar(45) DEFAULT NULL,
  `effective_cost` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_interventi su vecchio PAT.';


-- Dump della struttura di tabella pat.object_lease_canons
CREATE TABLE IF NOT EXISTS `object_lease_canons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'campo id_ufficio su vecchio PAT. Relazione con object_structures.',
  `state` tinyint DEFAULT NULL,
  `canon_type` varchar(255) DEFAULT NULL,
  `beneficiary` varchar(255) DEFAULT NULL,
  `fiscal_code` varchar(45) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `contract_statements` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `notes` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_canoni_localizzazione';


-- Dump della struttura di tabella pat.object_measures
CREATE TABLE IF NOT EXISTS `object_measures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int DEFAULT NULL COMMENT 'campo id_procedura su vecchio PAT. Relazione con tabella object_measures.',
  `object_bdncp_procedure_id` int DEFAULT NULL,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `number` varchar(255) DEFAULT NULL,
  `object` varchar(900) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `article_type` int DEFAULT NULL COMMENT 'Tipologia art.23',
  `date` datetime DEFAULT NULL,
  `content` text,
  `expense` text,
  `extremes` text,
  `choice_of_contractor` text COMMENT 'campo scelta_contraente su vecchio PAT.',
  `notes` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_provvedimenti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_modules_regulations
CREATE TABLE IF NOT EXISTS `object_modules_regulations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `typology` varchar(255) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `description` text,
  `order` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_modulistica_regolamenti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_news_notices
CREATE TABLE IF NOT EXISTS `object_news_notices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `news_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title` varchar(180) DEFAULT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `evidence` tinyint DEFAULT NULL,
  `public_in_notice` tinyint DEFAULT NULL COMMENT 'campo publica_in_bando su vecchio PAT.',
  `content` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_news_avvisi su vecchio PAT.';


-- Dump della struttura di tabella pat.object_normatives
CREATE TABLE IF NOT EXISTS `object_normatives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL COMMENT 'campo data_emissione su vecchio PAT.',
  `act_type` varchar(45) DEFAULT NULL COMMENT 'campo tipologia_atto su vecchio PAT.',
  `number` varchar(11) DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `normative_link` varchar(255) DEFAULT NULL,
  `normative_topic` varchar(225) DEFAULT NULL,
  `description` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_normativa su vecchio PAT.';


-- Dump della struttura di tabella pat.object_notices_acts
CREATE TABLE IF NOT EXISTS `object_notices_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int DEFAULT NULL COMMENT 'campo id_bando su vecchio PAT. Relazione con tabella object_contests_act.\n',
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `object` varchar(500) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `projects_start_date` datetime DEFAULT NULL,
  `cup` varchar(50) DEFAULT NULL,
  `total_fin_amount` varchar(50) DEFAULT NULL,
  `financial_sources` varchar(191) DEFAULT NULL,
  `implementation_state` varchar(255) DEFAULT NULL,
  `details` text,
  `attachments_id` int DEFAULT NULL COMMENT 'label relazione',
  `public_in` varchar(191) DEFAULT NULL,
  `number_readings` int DEFAULT NULL COMMENT 'numero di letture su vecchio PAT',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_bandi_atti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_notices_for_qualification_requirements
CREATE TABLE IF NOT EXISTS `object_notices_for_qualification_requirements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `denomination` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_bandi_requisiti_qualificazione su vecchio PAT.';

-- Dump dei dati della tabella pat.object_notices_for_qualification_requirements: ~57 rows (circa)
INSERT INTO `object_notices_for_qualification_requirements` (`id`, `code`, `denomination`, `created_at`, `updated_at`) VALUES
  (1, 'AA', 'Altro (es. Stazioni appaltanti con sistema di qualificazione proprio)', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (2, 'OG1', 'Edifici civili e industriali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (3, 'OG2', 'Restauro e manutenzione dei beni immobili sottoposti a tutela ai sensi delle disposizioni in materia di beni culturali e ambientali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (4, 'OG3', 'Strade, autostrade, ponti, viadotti, ferrovie, metropolitane, funicolari, piste aeroportuali e relative opere complementari', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (5, 'OG4', 'Opere d\'arte nel sottosuolo', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (6, 'OG5', 'Dighe', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (7, 'OG6', 'Acquedotti, gasdotti, oleodotti, opere di irrigazione e di evacuazione', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (8, 'OG7', 'Opere marittime e lavori di dragaggio', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (9, 'OG8', 'Opere fluviali, di difesa, di sistemazione idraulica e di bonifica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (10, 'OG9', 'Impianti per la produzione di energia elettrica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (11, 'OG10', 'Impianti per la trasformazione alta/media tensione e per la distribuzione di energia elettrica in corrente alternata e continua', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (12, 'OG11', 'Impianti tecnologici', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (13, 'OG12', 'Opere ed impianti di bonifica e protezione ambientale', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (14, 'OG13', 'Opere di ingegneria naturalistica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (15, 'OS1', 'Lavori in terra', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (16, 'OS2', 'Superfici decorate e beni mobili di interesse storico e artistico fino al 5.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (17, 'OS2-A', 'Superfici decorate di beni immobili del patrimonio culturale e beni culturali mobili di interesse storico, artistico, archeologico ed etnoantropologico a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (18, 'OS2-B', 'Beni culturali mobili di interesse archivistico e librario a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (19, 'OS3', 'Impianti idrico sanitario, cucine, lavanderie', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (20, 'OS4', 'Impianti elettromeccanici trasportatori', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (21, 'OS5', 'Impianti pneumatici e antintrusione', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (22, 'OS6', 'Finiture di opere generali in materiali lignei, plastici, metallici e vetrosi', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (23, 'OS7', 'Finiture di opere generali di natura edile', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (24, 'OS8', 'Finiture di opere generali di natura tecnica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (25, 'OS9', 'Impianti per la segnaletica luminosa e la sicurezza del traffico', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (26, 'OS10', 'Segnaletica stradale non luminosa', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (27, 'OS11', 'Apparecchiature strutturali speciali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (28, 'OS12', 'Barriere e protezioni stradali fino al 5.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (29, 'OS12-A', 'Barriere stradali di sicurezza a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (30, 'OS12-B', 'Barriere paramassi, fermaneve e simili a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (31, 'OS13', 'Strutture prefabbricate in cemento armato', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (32, 'OS14', 'Impianti di smaltimento e recupero rifiuti', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (33, 'OS15', 'Pulizia di acque marine, lacustri, fluviali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (34, 'OS16', 'Impianti per centrali produzione energia elettrica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (35, 'OS17', 'Linee telefoniche ed impianti di telefonia', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (36, 'OS18', 'Componenti strutturali in acciaio o metallo fino al 5.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (37, 'OS18-A', 'Componenti strutturali in acciaio a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (38, 'OS18-B', 'Componenti per facciate continue a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (39, 'OS19', 'Impianti di reti di telecomunicazione e di trasmissione dati', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (40, 'OS20', 'Rilevamenti topografici fino al 5.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (41, 'OS20-A', 'Rilevamenti topografici a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (42, 'OS20-B', 'Indagini geognostiche a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (43, 'OS21', 'Opere strutturali speciali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (44, 'OS22', 'Impianti di potabilizzazione e depurazione', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (45, 'OS23', 'Demolizione di opere', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (46, 'OS24', 'Verde e arredo urbano', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (47, 'OS25', 'Scavi archeologici', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (48, 'OS26', 'Pavimentazioni e sovrastrutture speciali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (49, 'OS27', 'Impianti per la trazione elettrica', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (50, 'OS28', 'Impianti termici e di condizionamento', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (51, 'OS29', 'Armamento ferroviario', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (52, 'OS30', 'Impianti interni elettrici, telefonici, radiotelefonici e televisivi', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (53, 'OS31', 'Impianti per la mobilit&agrave;', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (54, 'OS32', 'Impianti per la mobilit&agrave;', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (55, 'OS33', 'Coperture speciali', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (56, 'OS34', 'Sistemi antirumore per infrastrutture di mobilit&agrave;', '2021-11-19 12:39:07', '2021-11-19 12:39:07'),
  (57, 'OS35', 'Interventi a basso impatto ambientale a partire dal 6.12.2011', '2021-11-19 12:39:07', '2021-11-19 12:39:07');


-- Dump della struttura di tabella pat.object_other_contents
CREATE TABLE IF NOT EXISTS `object_other_contents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `title` varchar(45) DEFAULT NULL,
  `content` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_altri_contenuti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_payment
CREATE TABLE IF NOT EXISTS `object_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_supplie_id` int DEFAULT NULL COMMENT 'Colonna utilizzata prima dell''aggiornamento alla delibera 481',
  `supplier` int DEFAULT NULL,
  `object_structures_id` int DEFAULT NULL,
  `state` tinyint DEFAULT '1',
  `object` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` smallint DEFAULT NULL,
  `trimester` tinyint DEFAULT NULL,
  `expense_category` tinyint DEFAULT NULL,
  `expense_typology` smallint DEFAULT NULL,
  `compensation` decimal(15,2) DEFAULT NULL,
  `omissis` tinyint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella dei dati sui pagamenti.';


-- Dump della struttura di tabella pat.object_personnel
CREATE TABLE IF NOT EXISTS `object_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `role_id` int DEFAULT NULL,
  `qualification_id` int DEFAULT NULL,
  `state` tinyint DEFAULT '1',
  `title` varchar(45) DEFAULT NULL COMMENT 'titolo accademico o professionale',
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `fiscal_code` varchar(45) DEFAULT NULL,
  `qualification` varchar(45) DEFAULT NULL,
  `determined_term` tinyint DEFAULT NULL COMMENT 'campo determinato su vecchio PAT.',
  `political_role` varchar(255) DEFAULT NULL,
  `commissions` varchar(50) DEFAULT NULL,
  `political_organ` varchar(255) DEFAULT NULL,
  `delegation` tinyint DEFAULT NULL COMMENT 'campo delega su vecchio PAT.',
  `delegation_text` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `mobile_phone` varchar(45) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `not_available_email` tinyint DEFAULT NULL,
  `not_available_email_txt` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `certified_email` varchar(255) DEFAULT NULL,
  `details_conferment_act` text,
  `notes` text,
  `compensations` text,
  `trips_import` text COMMENT 'campo importi_viaggi su vecchio PAT.',
  `other_assignments` text,
  `other_assignments_institutions` text,
  `total_emoluments` text,
  `personnel_lists` tinyint DEFAULT NULL,
  `in_office_since` datetime DEFAULT NULL,
  `in_office_until` datetime DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `other_info` text,
  `information_archive` text,
  `on_leave` tinyint DEFAULT NULL COMMENT 'in aspettativa',
  `extremes_of_conference` text,
  `public_in` varchar(20) DEFAULT NULL,
  `archived` int DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text COMMENT 'informazioni staticizzate al momento dell''archviazione',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `last_update_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_personnel_qualification_id_index` (`qualification_id`),
  FULLTEXT KEY `firstname` (`firstname`,`lastname`),
  FULLTEXT KEY `full_name` (`full_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2106 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_riferimenti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_personnel_qualifications
CREATE TABLE IF NOT EXISTS `object_personnel_qualifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `name` varchar(90) NOT NULL,
  `view_in_personnel_form` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `object_personnel_qualifications_institution_id_index` (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.object_proceedings
CREATE TABLE IF NOT EXISTS `object_proceedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `state` tinyint DEFAULT '0',
  `name` varchar(500) DEFAULT NULL,
  `contact` varchar(50) DEFAULT 'struttura referenti' COMMENT 'Visualizzazione del Chi Contattare',
  `description` text NOT NULL,
  `instance_documents` text,
  `costs` text,
  `silence_consent` tinyint DEFAULT NULL,
  `declaration` tinyint(1) DEFAULT NULL,
  `regulation` text,
  `obtaining_info` text,
  `url_service` varchar(225) DEFAULT NULL,
  `deadline` text,
  `protection_instruments` varchar(255) DEFAULT NULL,
  `service_available` tinyint(1) DEFAULT NULL,
  `publication_state` varchar(225) DEFAULT NULL,
  `service_time` varchar(500) DEFAULT NULL,
  `notes` text,
  `public_monitoring_proceeding` tinyint DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `automatically_publish_monitoring_data` tinyint NOT NULL DEFAULT '0' COMMENT 'campo Pubblica automaticamente i dati sul monitoraggio su vecchio Pat',
  `archived` int DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_info` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_procedimenti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_programming_acts
CREATE TABLE IF NOT EXISTS `object_programming_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `attachments_id` int DEFAULT NULL,
  `object` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `act_type` varchar(255) DEFAULT NULL,
  `public_in_public_works` tinyint DEFAULT '0',
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_atti_programmazione su vecchio PAT.';


-- Dump della struttura di tabella pat.object_real_estate_asset
CREATE TABLE IF NOT EXISTS `object_real_estate_asset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `sheet` varchar(191) DEFAULT NULL COMMENT 'foglio',
  `particle` varchar(191) DEFAULT NULL COMMENT 'particella',
  `subaltern` varchar(191) DEFAULT NULL COMMENT 'subalterno',
  `gross_surface` varchar(255) DEFAULT NULL,
  `discovered_surface` varchar(255) DEFAULT NULL,
  `description` text,
  `archived` tinyint DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text,
  `archived_end_date` datetime DEFAULT NULL COMMENT 'Data di fine archiviazione',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_patrimonio_immobiliare su vecchio PAT.';


-- Dump della struttura di tabella pat.object_regulations
CREATE TABLE IF NOT EXISTS `object_regulations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `description` text,
  `state` tinyint DEFAULT '1',
  `title` varchar(600) DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL COMMENT 'campo su vecchio PAT: data_emissione',
  `number` varchar(191) DEFAULT NULL,
  `protocol` int DEFAULT NULL,
  `order` int DEFAULT NULL,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_regolamenti su vecchio PAT.';


-- Dump della struttura di tabella pat.object_relief_checks
CREATE TABLE IF NOT EXISTS `object_relief_checks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `object_structures_id` int DEFAULT NULL COMMENT 'campo ufficio su vecchio PAT.',
  `state` tinyint DEFAULT '1',
  `object` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `description` text,
  `act_object` text,
  `attachments_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_controlli_rilievi su vecchio PAT.';


-- Dump della struttura di tabella pat.object_structures
CREATE TABLE IF NOT EXISTS `object_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `structure_of_belonging_id` int DEFAULT NULL COMMENT 'campo struttura su vecchio PAT.',
  `institution_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `state` tinyint DEFAULT '1',
  `structure_name` varchar(255) DEFAULT NULL,
  `management_structure_id` int DEFAULT NULL,
  `non_management_structure_id` int DEFAULT NULL,
  `responsible_not_available` tinyint DEFAULT NULL,
  `referent_not_available_txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ad_interim` tinyint DEFAULT NULL,
  `reference_email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_not_available` tinyint DEFAULT NULL,
  `email_not_available_txt` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certified_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `other_contacts_check` tinyint DEFAULT '0',
  `other_contacts` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `articulation` tinyint DEFAULT NULL,
  `address_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `based_structure` tinyint DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timetables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `order` int DEFAULT NULL,
  `archived` tinyint(1) DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_active_to` datetime DEFAULT NULL COMMENT 'data attiva fino al(per l''archiviazione)',
  `archived_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `unfixed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1 COMMENT='Tabella su vecchio PAT: oggetto_uffici';


-- Dump della struttura di tabella pat.object_supplie_list
CREATE TABLE IF NOT EXISTS `object_supplie_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `institution_id` varchar(45) NOT NULL,
  `state` tinyint DEFAULT '1',
  `typology` varchar(255) DEFAULT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'Fornitore singolo',
  `it` tinyint DEFAULT NULL COMMENT 'Indica se il fornitore è italiano o estero',
  `name` varchar(255) DEFAULT NULL,
  `vat` varchar(255) DEFAULT NULL,
  `p_iva` varchar(45) DEFAULT NULL,
  `foreign_tax_identification` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `publishing_responsable` int DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_elenco_fornitori su vecchio PAT.';


-- Dump della struttura di tabella pat.password_history
CREATE TABLE IF NOT EXISTS `password_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Salvataggio vecchie password';


-- Dump della struttura di tabella pat.permits
CREATE TABLE IF NOT EXISTS `permits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `acl_profiles_id` int NOT NULL,
  `sections_bo_id` int NOT NULL,
  `sections_fo_id` int DEFAULT NULL,
  `institution_id` int DEFAULT NULL,
  `create` tinyint DEFAULT NULL,
  `read` tinyint DEFAULT NULL,
  `update` tinyint DEFAULT NULL,
  `delete` tinyint DEFAULT NULL,
  `send_notify_app_io` int NOT NULL DEFAULT '0' COMMENT 'Puo avere valore 0:non può inviare notifiche push app io, 1:puo inviare solo le notifiche nei record creati da lui, 2: puo inviare le notifiche push da tutti i record',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2161 DEFAULT CHARSET=latin1 COMMENT='Permessi, tabella di relazione tra acl_profiles e sections.';

-- Dump dei dati della tabella pat.permits
INSERT INTO `permits` (`id`, `acl_profiles_id`, `sections_bo_id`, `sections_fo_id`, `institution_id`, `create`, `read`, `update`, `delete`, `send_notify_app_io`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1975, 1, 2, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1976, 1, 3, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1977, 1, 111, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1978, 1, 4, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1979, 1, 5, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1980, 1, 6, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1981, 1, 7, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1982, 1, 8, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1983, 1, 9, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1984, 1, 10, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1985, 1, 12, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1986, 1, 13, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1987, 1, 14, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1988, 1, 15, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1989, 1, 92, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1990, 1, 91, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1991, 1, 22, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1992, 1, 23, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1993, 1, 24, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1994, 1, 25, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1995, 1, 26, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1996, 1, 27, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1997, 1, 28, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1998, 1, 110, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (1999, 1, 19, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2000, 1, 20, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2001, 1, 17, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2002, 1, 44, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2003, 1, 45, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2004, 1, 49, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2005, 1, 51, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2006, 1, 52, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2007, 1, 61, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2008, 1, 54, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2009, 1, 55, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2010, 1, 56, NULL, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2011, 1, 44, 1, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2012, 1, 44, 21, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2013, 1, 44, 23, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2014, 1, 44, 24, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2015, 1, 44, 25, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2016, 1, 44, 26, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2017, 1, 44, 27, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2018, 1, 44, 28, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2019, 1, 44, 29, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2020, 1, 44, 30, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2021, 1, 44, 31, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2022, 1, 44, 32, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2023, 1, 44, 2, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2024, 1, 44, 37, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2025, 1, 44, 247, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2026, 1, 44, 38, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2027, 1, 44, 39, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2028, 1, 44, 40, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2029, 1, 44, 41, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2030, 1, 44, 241, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2031, 1, 44, 243, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2032, 1, 44, 42, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2033, 1, 44, 43, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2034, 1, 44, 44, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2035, 1, 44, 3, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2036, 1, 44, 46, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2037, 1, 44, 47, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2038, 1, 44, 50, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2039, 1, 44, 4, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2040, 1, 44, 58, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2041, 1, 44, 59, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2042, 1, 44, 60, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2043, 1, 44, 61, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2044, 1, 44, 62, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2045, 1, 44, 63, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2046, 1, 44, 64, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2047, 1, 44, 65, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2048, 1, 44, 66, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2049, 1, 44, 67, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2050, 1, 44, 68, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2051, 1, 44, 69, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2052, 1, 44, 70, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2053, 1, 44, 71, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2054, 1, 44, 72, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2055, 1, 44, 73, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2056, 1, 44, 5, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2057, 1, 44, 75, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2058, 1, 44, 76, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2059, 1, 44, 77, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2060, 1, 44, 78, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2061, 1, 44, 6, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2062, 1, 44, 82, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2063, 1, 44, 83, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2064, 1, 44, 84, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2065, 1, 44, 85, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2066, 1, 44, 86, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2067, 1, 44, 7, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2068, 1, 44, 89, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2069, 1, 44, 91, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2070, 1, 44, 92, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2071, 1, 44, 93, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2072, 1, 44, 95, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2073, 1, 44, 8, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2074, 1, 44, 98, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2075, 1, 44, 99, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2076, 1, 44, 101, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2077, 1, 44, 9, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2078, 1, 44, 103, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2079, 1, 44, 104, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2080, 1, 44, 105, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2081, 1, 44, 106, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2082, 1, 44, 107, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2083, 1, 44, 108, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2084, 1, 44, 183, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2085, 1, 44, 10, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2086, 1, 44, 580, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2087, 1, 44, 113, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2088, 1, 44, 297, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2089, 1, 44, 298, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2090, 1, 44, 588, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2091, 1, 44, 581, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2092, 1, 44, 110, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2093, 1, 44, 111, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2094, 1, 44, 112, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2095, 1, 44, 529, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2096, 1, 44, 257, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2097, 1, 44, 528, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2098, 1, 44, 524, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2099, 1, 44, 526, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2100, 1, 44, 115, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2101, 1, 44, 116, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2102, 1, 44, 530, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2103, 1, 44, 527, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2104, 1, 44, 117, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2105, 1, 44, 525, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2106, 1, 44, 532, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2107, 1, 44, 533, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2108, 1, 44, 534, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2109, 1, 44, 531, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2110, 1, 44, 11, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2111, 1, 44, 125, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2112, 1, 44, 126, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2113, 1, 44, 127, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2114, 1, 44, 12, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2115, 1, 44, 130, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2116, 1, 44, 131, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2117, 1, 44, 13, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2118, 1, 44, 133, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2119, 1, 44, 135, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2120, 1, 44, 136, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2121, 1, 44, 137, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2122, 1, 44, 14, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2123, 1, 44, 139, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2124, 1, 44, 140, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2125, 1, 44, 141, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2126, 1, 44, 142, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2127, 1, 44, 143, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2128, 1, 44, 144, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2129, 1, 44, 145, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2130, 1, 44, 15, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2131, 1, 44, 147, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2132, 1, 44, 148, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2133, 1, 44, 149, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2134, 1, 44, 150, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2135, 1, 44, 151, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2136, 1, 44, 16, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2137, 1, 44, 154, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2138, 1, 44, 158, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2139, 1, 44, 159, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2140, 1, 44, 160, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2141, 1, 44, 161, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2142, 1, 44, 17, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2143, 1, 44, 166, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2144, 1, 44, 167, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2145, 1, 44, 168, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2146, 1, 44, 184, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2147, 1, 44, 185, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2148, 1, 44, 186, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2149, 1, 44, 187, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2150, 1, 44, 18, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2151, 1, 44, 174, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2152, 1, 44, 175, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2153, 1, 44, 176, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2154, 1, 44, 177, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2155, 1, 44, 178, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2156, 1, 44, 179, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2157, 1, 44, 180, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2158, 1, 44, 19, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2159, 1, 44, 182, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL),
  (2160, 1, 44, 181, 1, 1, 1, 1, 1, 0, '2026-09-30 10:44:15', '2026-09-30 10:44:15', NULL);



-- Dump della struttura di tabella pat.recalls
CREATE TABLE IF NOT EXISTS `recalls` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recalls_configuration_id` int NOT NULL,
  `record_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `paragraph_id` int DEFAULT NULL,
  `recall_name` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dump della struttura di tabella pat.recalls_configuration
CREATE TABLE IF NOT EXISTS `recalls_configuration` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_fo_id` int NOT NULL,
  `section_bo_id` int NOT NULL,
  `recall_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recall_fo_id` int NOT NULL,
  `deleted` tinyint DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Per la configurazione dei richiami nelle pagine';


-- Dump dei dati della tabella pat.recalls_configuration: ~37 rows (circa)
INSERT INTO `recalls_configuration` (`id`, `section_fo_id`, `section_bo_id`, `recall_label`, `recall_fo_id`, `deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (3, 21, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (4, 23, 14, 'NORMATIVE', 24, 0, NULL, NULL, NULL),
  (5, 23, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (6, 37, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (7, 40, 2, 'STRUTTURE', 40, 0, NULL, NULL, NULL),
  (8, 3, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (9, 46, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (10, 58, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (11, 65, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (12, 71, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (13, 82, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (14, 83, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (15, 84, 12, 'PROCEDIMENTI', 29, 0, NULL, NULL, NULL),
  (16, 101, 2, 'STRUTTURE', 40, 0, NULL, NULL, NULL),
  (17, 103, 26, 'PROVVEDIMENTI', 9, 0, NULL, NULL, NULL),
  (18, 106, 26, 'PROVVEDIMENTI', 9, 0, NULL, NULL, NULL),
  (19, 147, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (20, 149, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (21, 17, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (22, 184, 26, 'PROVVEDIMENTI', 9, 0, NULL, NULL, NULL),
  (23, 187, 26, 'PROVVEDIMENTI', 9, 0, NULL, NULL, NULL),
  (24, 187, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (25, 174, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (26, 174, 26, 'PROVVEDIMENTI', 9, 0, NULL, NULL, NULL),
  (27, 174, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (28, 58, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (29, 63, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (30, 98, 7, 'PROCEDIMENTI', 98, 0, NULL, NULL, NULL),
  (31, 39, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (32, 131, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (34, 60, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (35, 60, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (36, 67, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL),
  (37, 67, 3, 'REFERENTI', 4, 0, NULL, NULL, NULL),
  (40, 43, 2, 'STRUTTURE', 40, 0, NULL, NULL, NULL),
  (41, 1, 12, 'REGOLAMENTI', 29, 0, NULL, NULL, NULL),
  (42, 139, 25, 'INCARICHI', 46, 0, NULL, NULL, NULL);


-- Dump della struttura di tabella pat.recovery_password
CREATE TABLE IF NOT EXISTS `recovery_password` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `token` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per gestire il recupero della password.';


-- Dump della struttura di tabella pat.rel_assignments_measures
CREATE TABLE IF NOT EXISTS `rel_assignments_measures` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'utilizzata per gestire i provvedimenti associati agli incarichi.',
  `object_assignments_id` int NOT NULL,
  `object_measures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati agli incarichi.';


-- Dump della struttura di tabella pat.rel_assignments_notices_acts
CREATE TABLE IF NOT EXISTS `rel_assignments_notices_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_assignments_id` int NOT NULL,
  `object_notices_acts_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli atti delle amministrazioni associati agli incarichi.';


-- Dump della struttura di tabella pat.rel_attachment_label_archive
CREATE TABLE IF NOT EXISTS `rel_attachment_label_archive` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label_id` int DEFAULT NULL,
  `archive_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel_attachment_label_archive_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;


-- Dump della struttura di tabella pat.rel_bdncp_procedure_assignments
CREATE TABLE IF NOT EXISTS `rel_bdncp_procedure_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_procedure_id` int NOT NULL COMMENT 'Id procedura bdncp',
  `object_assignment_id` int NOT NULL COMMENT 'ID incarico',
  `typology` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Indica la tipologia della relazione (se per il colleggio o la commissione',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relazione tra le procedure bdncp e gli incarichi';


-- Dump della struttura di tabella pat.rel_bdncp_procedure_bdncp_procedure
CREATE TABLE IF NOT EXISTS `rel_bdncp_procedure_bdncp_procedure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_procedure_id` int NOT NULL COMMENT 'ID della variazione o bilancio',
  `object_procedure_id2` int NOT NULL COMMENT 'ID della procedura collegata',
  `typology` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Indica la tipologia della relazione',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rel_procedure` (`object_procedure_id`,`object_procedure_id2`,`typology`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relazione tra le procedure bdncp e se stesso';


-- Dump della struttura di tabella pat.rel_categories_attachments
CREATE TABLE IF NOT EXISTS `rel_categories_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attachments_id` int NOT NULL,
  `categories_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `archive` varchar(45) DEFAULT NULL,
  `id_archive` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per la gestione degli allegati.';


-- Dump della struttura di tabella pat.rel_charges_measures
CREATE TABLE IF NOT EXISTS `rel_charges_measures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_charges_id` int NOT NULL,
  `object_measures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati agli oneri.';


-- Dump della struttura di tabella pat.rel_charges_normatives
CREATE TABLE IF NOT EXISTS `rel_charges_normatives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_charges_id` int NOT NULL,
  `object_normatives_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i riferimenti normativi associati agli oneri.';


-- Dump della struttura di tabella pat.rel_charges_proceedings
CREATE TABLE IF NOT EXISTS `rel_charges_proceedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_charges_id` int NOT NULL,
  `object_proceedings_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`object_charges_id`,`object_proceedings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati agli oneri.';


-- Dump della struttura di tabella pat.rel_charges_regulations
CREATE TABLE IF NOT EXISTS `rel_charges_regulations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_charges_id` int NOT NULL,
  `object_regulations_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i regolamenti associati agli oneri.';


-- Dump della struttura di tabella pat.rel_commissions_personnel
CREATE TABLE IF NOT EXISTS `rel_commissions_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_commissions_id` int NOT NULL,
  `object_personnel_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utlizzata per gestire presidente, vicepresidente, segretari e membri delle commissioni, specificandolo nel campo typology.';


-- Dump della struttura di tabella pat.rel_commissions_structures
CREATE TABLE IF NOT EXISTS `rel_commissions_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_commissions_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_commissions_structures_commission_id_index` (`object_commissions_id`),
  KEY `rel_commissions_structures_structure_id_index` (`object_structures_id`),
  KEY `rel_commissions_structures_typology_index` (`typology`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.rel_contest_acts_public_in
CREATE TABLE IF NOT EXISTS `rel_contest_acts_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contest_act_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1126 DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra bandi di gara e sezioni fo(per il pubblica in)';


-- Dump della struttura di tabella pat.rel_contest_assignments
CREATE TABLE IF NOT EXISTS `rel_contest_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contest_id` int NOT NULL,
  `object_assignments_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire la commissione giudicatrice incarichi.';


-- Dump della struttura di tabella pat.rel_contests_act_proceeding
CREATE TABLE IF NOT EXISTS `rel_contests_act_proceeding` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int NOT NULL,
  `object_proceeding_id` int NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.rel_contests_act_requirements
CREATE TABLE IF NOT EXISTS `rel_contests_act_requirements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int NOT NULL,
  `object_requirement_id` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.rel_contests_acts_contests_acts
CREATE TABLE IF NOT EXISTS `rel_contests_acts_contests_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int NOT NULL COMMENT 'id del bando preso in questione',
  `object_contests_acts_id1` int NOT NULL COMMENT 'id bando associato al bando preso in questione.',
  `typology` varchar(45) NOT NULL COMMENT 'tipologia del bando associato, es:altre_procedure, bando_collegato ecc',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Altre procedure.Utilizzata per gestire le varie tipologie di altre procedure associate ai vari tipi di bando.';


-- Dump della struttura di tabella pat.rel_contests_acts_supplie_list
CREATE TABLE IF NOT EXISTS `rel_contests_acts_supplie_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int NOT NULL,
  `object_supplie_list_id` int NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le tipologie di fornitori associati ai vari bandi, es: partecipanti o aggiudicatari.';


-- Dump della struttura di tabella pat.rel_general_acts_documents_public_in
CREATE TABLE IF NOT EXISTS `rel_general_acts_documents_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `general_acts_documents_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb3 COMMENT='Tabella per la gestione del pubblica in per gli Atti e documenti di carattere generale riferiti a tutte le procedure';


-- Dump della struttura di tabella pat.rel_grants_normatives
CREATE TABLE IF NOT EXISTS `rel_grants_normatives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_grants_id` int NOT NULL,
  `object_normatives_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le normative associate alla sovvenzione.';


-- Dump della struttura di tabella pat.rel_grants_personnel
CREATE TABLE IF NOT EXISTS `rel_grants_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_grants_id` int NOT NULL,
  `object_personnel_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i responsabili delle sovvenzioni.';


-- Dump della struttura di tabella pat.rel_institution_type_public_in_section
CREATE TABLE IF NOT EXISTS `rel_institution_type_public_in_section` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_type_id` int NOT NULL,
  `section_public_in_id` int NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra tipo ente e le configurazioni per il pubblica in(section_fo_config_publication_archive) per il pubblica in';


-- Dump della struttura di tabella pat.rel_institution_type_sections_labeling
CREATE TABLE IF NOT EXISTS `rel_institution_type_sections_labeling` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_type_id` int DEFAULT NULL,
  `institution_id` int DEFAULT NULL,
  `sections_id` int NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utilizzata per la gestione delle traduzioni.\nSi deve settare o l''id dell''ente o del tipo ente affinché funzioni non entrambi';


-- Dump della struttura di tabella pat.rel_interventions_measures
CREATE TABLE IF NOT EXISTS `rel_interventions_measures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_interventions_id` int NOT NULL,
  `object_measures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati all''intervento.';


-- Dump della struttura di tabella pat.rel_interventions_regulations
CREATE TABLE IF NOT EXISTS `rel_interventions_regulations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_interventions_id` int NOT NULL,
  `object_regulations_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i regolamenti assiciati agli interventi.';


-- Dump della struttura di tabella pat.rel_lease_canons_real_estate_asset
CREATE TABLE IF NOT EXISTS `rel_lease_canons_real_estate_asset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_lease_canons_id` int NOT NULL,
  `object_real_estate_asset_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli immobili associati ad un canone di locazione.';


-- Dump della struttura di tabella pat.rel_measures_bdncp_procedure
CREATE TABLE IF NOT EXISTS `rel_measures_bdncp_procedure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_measures_id` int NOT NULL,
  `object_procedure_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Dump della struttura di tabella pat.rel_measures_personnel
CREATE TABLE IF NOT EXISTS `rel_measures_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_measures_id` int NOT NULL,
  `object_personnel_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i responsabili del provvedimento.';


-- Dump della struttura di tabella pat.rel_measures_structures
CREATE TABLE IF NOT EXISTS `rel_measures_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_measures_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate ai provvedimenti.';


-- Dump della struttura di tabella pat.rel_modules_proceedings
CREATE TABLE IF NOT EXISTS `rel_modules_proceedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_modules_regulations_id` int NOT NULL,
  `object_proceedings_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati alla modulistica regolamenti.';


-- Dump della struttura di tabella pat.rel_normatives_structures
CREATE TABLE IF NOT EXISTS `rel_normatives_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_normatives_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `typology` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli uffici associati alla normativa.';


-- Dump della struttura di tabella pat.rel_notice_acts_public_in
CREATE TABLE IF NOT EXISTS `rel_notice_acts_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notice_act_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=201 DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in per gli atti delle amministrazioni';


-- Dump della struttura di tabella pat.rel_notices_acts_contests_acts
CREATE TABLE IF NOT EXISTS `rel_notices_acts_contests_acts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_notices_acts_id` int DEFAULT NULL,
  `object_contests_acts_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione Atti delle Amministrazioni e Bandi di Gara';


-- Dump della struttura di tabella pat.rel_personnel_assignments
CREATE TABLE IF NOT EXISTS `rel_personnel_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `object_assignments_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli incarichi';


-- Dump della struttura di tabella pat.rel_personnel_company
CREATE TABLE IF NOT EXISTS `rel_personnel_company` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `object_company_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i rappresentanti della società.';


-- Dump della struttura di tabella pat.rel_personnel_for_structures
CREATE TABLE IF NOT EXISTS `rel_personnel_for_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `typology` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture di cui il personale è referente.';


-- Dump della struttura di tabella pat.rel_personnel_measures
CREATE TABLE IF NOT EXISTS `rel_personnel_measures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `object_measure_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra il personale e i provvedimenti';


-- Dump della struttura di tabella pat.rel_personnel_political_organ
CREATE TABLE IF NOT EXISTS `rel_personnel_political_organ` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `political_organ_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra il personale e gli organi di indirizzo politico';


-- Dump della struttura di tabella pat.rel_personnel_public_in
CREATE TABLE IF NOT EXISTS `rel_personnel_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel_personnel_public_in_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=453 DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in del personale';


-- Dump della struttura di tabella pat.rel_proceedings_normatives
CREATE TABLE IF NOT EXISTS `rel_proceedings_normatives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int NOT NULL,
  `object_normatives_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le normative associate al procedimento.';


-- Dump della struttura di tabella pat.rel_proceedings_personnel
CREATE TABLE IF NOT EXISTS `rel_proceedings_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int NOT NULL,
  `object_personnel_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL COMMENT 'utilizzato per gestire le varie tipologie di personale, es: referente prvinciale, procedurale ecc...',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i vari referenti e il personale associati al procedimento, tramite il campo typology.';


-- Dump della struttura di tabella pat.rel_proceedings_structures
CREATE TABLE IF NOT EXISTS `rel_proceedings_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL COMMENT 'utilizzato per gestire le varie tipologie di uffici.',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate al procedimento(ufficio, ufficio_def) tramite il campo typology.';


-- Dump della struttura di tabella pat.rel_real_estate_asset_structures
CREATE TABLE IF NOT EXISTS `rel_real_estate_asset_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_real_estate_asset_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzato per gestire gli ufficio_utilizzatore associati al patrimoni immobiliare.';


-- Dump della struttura di tabella pat.rel_regulations_proceedings
CREATE TABLE IF NOT EXISTS `rel_regulations_proceedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_regulations_id` int NOT NULL,
  `object_proceedings_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati al regolamento.';


-- Dump della struttura di tabella pat.rel_regulations_public_in
CREATE TABLE IF NOT EXISTS `rel_regulations_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_regulation_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in dei regolamenti';


-- Dump della struttura di tabella pat.rel_regulations_structures
CREATE TABLE IF NOT EXISTS `rel_regulations_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_regulations_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate ai regolamenti.';


-- Dump della struttura di tabella pat.rel_relief_check_public_in
CREATE TABLE IF NOT EXISTS `rel_relief_check_public_in` (
  `id` int NOT NULL AUTO_INCREMENT,
  `relief_check_id` int NOT NULL,
  `public_in_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in dei controlli e rilievi';


-- Dump della struttura di tabella pat.rel_sections_fo_politic_organ_personnel
CREATE TABLE IF NOT EXISTS `rel_sections_fo_politic_organ_personnel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `section_fo_id` int NOT NULL,
  `object_personnel_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rel_politic_organ_personnel_institution_id_index` (`institution_id`),
  KEY `rel_politic_organ_personnel_section_fo_id_index` (`section_fo_id`),
  KEY `rel_politic_organ_personnel_object_personnel_id_index` (`object_personnel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.rel_sections_fo_politic_organ_structures
CREATE TABLE IF NOT EXISTS `rel_sections_fo_politic_organ_structures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `section_fo_id` int NOT NULL,
  `object_structures_id` int NOT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `politic_organ_structures_institution_id_index` (`institution_id`),
  KEY `politic_organ_structures_section_fo_id_index` (`section_fo_id`),
  KEY `politic_organ_structures_object_structures_id_index` (`object_structures_id`),
  KEY `politic_organ_structures_typology_index` (`typology`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.rel_supplie_list
CREATE TABLE IF NOT EXISTS `rel_supplie_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `object_supplie_list_id` int NOT NULL,
  `object_related_supplie_list` int NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire mandante, mandataria, associata, capogruppo, consorziata.';


-- Dump della struttura di tabella pat.rel_users_acl_profiles
CREATE TABLE IF NOT EXISTS `rel_users_acl_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `acl_profile_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i profili associati agli utenti.';

INSERT INTO `rel_users_acl_profiles` (`id`, `user_id`, `acl_profile_id`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, '2022-04-06 15:43:31', '2022-04-06 15:43:31');


-- Dump della struttura di tabella pat.report_publication
CREATE TABLE IF NOT EXISTS `report_publication` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `active` tinyint DEFAULT '1',
  `owner_id` int DEFAULT NULL,
  `institution_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella per i destinatari del report sulle pubblicazioni';


-- Dump della struttura di tabella pat.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `political` tinyint DEFAULT '0' COMMENT 'Indica se è un ruolo con comportamento da incaricato politico',
  `po_dir_fields` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.role: 12 rows
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (51, 'E.Q.', 0, 1, NULL, NULL, NULL);
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, 'Incaricato politico', 1, 0, NULL, NULL, NULL);
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, 'Dirigente', 0, 1, NULL, NULL, NULL);
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, 'Funzionario', 0, 0, NULL, NULL, NULL);
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, 'P.O.', 0, 1, NULL, NULL, NULL);
INSERT INTO `role` (`id`, `name`, `political`, `po_dir_fields`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 'Dipendente', 0, 0, NULL, NULL, NULL);


-- Dump della struttura di tabella pat.section_bo
CREATE TABLE IF NOT EXISTS `section_bo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `addon_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `lineage` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `deep` int DEFAULT NULL,
  `search_sort` int DEFAULT NULL,
  `sort` int DEFAULT NULL,
  `controller` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `model` int DEFAULT NULL COMMENT 'Corrispondenza con il file modelConfigs.php',
  `model_class` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Classe del modello',
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `icon` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `hidden_profile_acl` tinyint DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `searchable` int DEFAULT '1' COMMENT 'Indica se il motore di ricerca deve cercare o meno in questa sezione',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `hide` tinyint DEFAULT '0',
  `deleted` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dump dei dati della tabella pat.section_bo: ~44 rows (circa)
INSERT INTO `section_bo` (`id`, `parent_id`, `addon_id`, `name`, `lineage`, `deep`, `search_sort`, `sort`, `controller`, `model`, `model_class`, `url`, `icon`, `hidden_profile_acl`, `deleted_at`, `searchable`, `created_at`, `updated_at`, `hide`, `deleted`) VALUES
  (1, 0, NULL, 'Organizzazione dell\'Ente', '000001', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-layer-group"></i>', 0, NULL, 0, '2021-10-29 09:34:49', NULL, 0, 0),
  (2, 1, NULL, 'Strutture Organizzative', '000001-000002', 2, 5, 1, 'StructureAdminController', 1, 'StructuresModel', 'admin/structure', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:35:55', NULL, 0, 0),
  (3, 1, NULL, 'Personale', '000001-000003', 2, 5, 2, 'PersonnelAdminController', 2, 'PersonnelModel', 'admin/personnel', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:36:54', NULL, 0, 0),
  (4, 1, NULL, 'Tassi di assenza', '000001-000004', 2, 5, 3, 'AbsenceRatesAdminController', 3, 'AbsenceRatesModel', 'admin/absence-rates', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:39:55', NULL, 0, 0),
  (5, 1, NULL, 'Commissioni e gruppi consiliari', '000001-000005', 2, 5, 4, 'CommissionAdminController', 4, 'CommissionsModel', 'admin/commission', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:40:40', NULL, 0, 0),
  (6, 1, NULL, 'Enti e società controllate', '000001-000006', 2, 5, 5, 'CompanyAdminController', 5, 'CompanyModel', 'admin/company', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:41:27', NULL, 0, 0),
  (7, 1, NULL, 'Procedimenti', '000001-000007', 2, 4, 6, 'ProceedingAdminController', 6, 'ProceedingsModel', 'admin/proceeding', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:42:17', NULL, 0, 0),
  (8, 1, NULL, 'Patrimonio Immobiliare', '000001-000008', 2, 5, 7, 'RealEstateAssetAdminController', 7, 'RealEstateAssetModel', 'admin/real-estate-asset', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:44:03', NULL, 0, 0),
  (9, 1, NULL, 'Canoni di locazione', '000001-000009', 2, 5, 8, 'CanonAdminController', 8, 'LeaseCanonsModel', 'admin/canon', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:44:47', NULL, 0, 0),
  (10, 1, NULL, 'Controlli e rilievi', '000001-000010', 2, 5, 9, 'ReliefCheckAdminController', 9, 'ReliefChecksModel', 'admin/relief-check', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:47:08', NULL, 0, 0),
  (11, 0, NULL, 'Documenti e Moduli', '000011', 1, 5, 2, 'controller_name', NULL, NULL, '#!', '<i class="far fa-folder-open"></i>', 0, NULL, 0, '2021-10-29 09:48:34', NULL, 0, 0),
  (12, 11, NULL, 'Regolamenti e documentazione', '000011-000012', 2, 5, 1, 'RegulationAdminController', 10, 'RegulationsModel', 'admin/regulation', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:50:16', NULL, 0, 0),
  (13, 11, NULL, 'Modulistica', '000011-000013', 2, 5, 2, 'ModuleAdminController', 11, 'ModulesRegulationsModel', 'admin/module', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:51:03', NULL, 0, 0),
  (14, 11, NULL, 'Normativa', '000011-000014', 2, 5, 3, 'NormativeAdminController', 12, 'NormativesModel', 'admin/normative', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:51:40', NULL, 0, 0),
  (15, 11, NULL, 'Bilanci', '000011-000015', 2, 5, 4, 'BalanceAdminController', 13, 'BalanceSheetsModel', 'admin/balance', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:52:21', NULL, 0, 0),
  (16, 0, NULL, 'Atti e pubblicazioni', '000016', 1, 5, 3, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-gavel"></i>', 0, NULL, 0, '2021-10-29 09:53:10', NULL, 0, 0),
  (17, 93, NULL, 'Elenco partecipanti/aggiudicatari/fornitori', '000093-000017', 2, 5, 10, 'SupplierAdminController', 14, 'SupplieListModel', 'admin/supplier', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:54:35', NULL, 0, 0),
  (19, 93, NULL, 'Bandi gare e contratti fino al 31/12/2023', '000093-000019', 2, 1, 3, 'ContestsActAdminController', 15, 'ContestsActsModel', 'admin/contests-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:11:23', NULL, 0, 0),
  (20, 93, NULL, 'Bandi Gare e Contratti - Atti delle amministrazioni', '000093-000020', 2, 5, 4, 'NoticesActAdminController', 16, 'NoticesActsModel', 'admin/notices-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:12:17', NULL, 0, 0),
  (22, 16, NULL, 'Atti di programmazione', '000016-000022', 2, 5, 3, 'ProgrammingActAdminController', 18, 'ProgrammingActsModel', 'admin/programming-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:21:23', NULL, 0, 0),
  (23, 16, NULL, 'Bandi di Concorso', '000016-000023', 2, 2, 4, 'ContestAdminController', 19, 'ContestModel', 'admin/contest', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:22:04', NULL, 0, 0),
  (24, 16, NULL, 'Sovvenzioni e vantaggi economici', '000016-000024', 2, 5, 5, 'GrantAdminController', 20, 'GrantsModel', 'admin/grant', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:24:08', NULL, 0, 0),
  (25, 16, NULL, 'Incarichi e consulenze', '000016-000025', 2, 5, 6, 'AssignmentAdminController', 21, 'AssignmentsModel', 'admin/assignment', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:25:17', NULL, 0, 0),
  (26, 16, NULL, 'Provvedimenti Amministrativi', '000016-000026', 2, 3, 7, 'MeasureAdminController', 22, 'MeasuresModel', 'admin/measure', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:25:59', NULL, 0, 0),
  (27, 16, NULL, 'Oneri informativi e obblighi', '000016-000027', 2, 5, 8, 'ChargeAdminController', 24, 'ChargesModel', 'admin/charge', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:26:56', NULL, 0, 0),
  (28, 16, NULL, 'Interventi straordinari e di emergenza', '000016-000028', 2, 5, 9, 'InterventionAdminController', 25, 'InterventionsModel', 'admin/intervention', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:28:14', NULL, 0, 0),
  (43, 0, NULL, 'Contenuti Trasparenza', '000043', 1, 5, 6, 'controller_name', NULL, NULL, '#!', '<i class="far fa-edit"></i>', 0, NULL, 0, '2021-10-29 11:21:08', NULL, 0, 0),
  (44, 43, NULL, 'Pagine generiche', '000043-000044', 2, 5, 1, 'GenericPageAdminController', 23, NULL, 'admin/generic-page', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 11:21:50', NULL, 0, 0),
  (45, 43, NULL, 'Archivio file', '000043-000045', 2, 5, 2, 'FileArchiveAdminController', NULL, NULL, 'admin/file-archive', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:22:16', NULL, 0, 0),
  (48, 0, NULL, 'Contenuti di supporto', '000048', 1, 5, 7, 'PivotController', NULL, NULL, '#!', '<i class="fas fa-bullhorn"></i>', 0, NULL, 0, '2021-10-29 11:30:03', NULL, 0, 0),
  (49, 48, NULL, 'News ed avvisi', '000048-000049', 2, 5, 1, 'NewsNoticeAdminController', 26, 'NewsNoticesModel', 'admin/news-notice', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 11:30:48', NULL, 0, 0),
  (50, 0, NULL, 'Report e log', '000050', 1, 5, 9, 'controller_name', NULL, NULL, '#!', '<i class="far fa-chart-bar"></i>', 0, NULL, 0, '2021-10-29 11:31:50', NULL, 0, 0),
  (51, 50, NULL, 'Log delle attività', '000050-000051', 2, 5, 1, 'ActivityLogAdminController', NULL, NULL, 'admin/activity-log', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:32:48', NULL, 0, 0),
  (52, 50, NULL, 'Log utenti', '000050-000052', 2, 5, 2, 'UserLogAdminController', NULL, NULL, 'admin/user-log', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:32:48', NULL, 0, 0),
  (53, 0, NULL, 'Impostazioni', '000053', 1, 5, 10, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-cogs"></i>', 0, NULL, 0, '2021-10-29 11:33:40', NULL, 0, 0),
  (54, 53, NULL, 'Utenti', '000053-000054', 2, 5, 1, 'UsersAdminController', 39, 'UsersModel', 'admin/user', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:34:45', NULL, 0, 0),
  (55, 53, NULL, 'Profili ACL', '000053-000055', 2, 5, 2, 'AclUsersProfileAdminController', 27, 'AclProfilesModel', 'admin/acl-users-profile', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:35:19', NULL, 0, 0),
  (56, 53, NULL, 'Ente', '000053-000056', 2, 5, 3, 'InstitutionAdminController', NULL, NULL, 'admin/institution', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:36:03', NULL, 0, 0),
  (61, 50, NULL, 'Report di pubblicazione - Elenco destinatari', '000050-61', 2, 5, 2, 'ReportPublicationRecipientsController', NULL, NULL, 'admin/report-publication-recipients', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2022-09-23 11:56:11', '2022-09-23 11:56:13', 0, 0),
  (91, 16, NULL, 'Bandi gare e contratti (dal 1/1/2024)', '000016-000091', 2, 2, 2, 'BdncpProcedureController', 46, 'BdncpProcedureModel', 'admin/bdncp-procedure', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:11:23', NULL, 0, 0),
  (92, 16, NULL, 'Atti e Documenti di carattere generale', '000016-000092', 2, 1, 1, 'GeneralActsDocumentsController', 45, 'GeneralActsDocumentsModel', 'admin/general-acts-documents', '<i class="fas fa-list"></i>', 0, NULL, 1, '2021-10-29 10:11:23', NULL, 0, 0),
  (93, 0, NULL, 'Bandi gare e contratti fino al 31/12/2023', '000093', 1, 6, 4, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-gavel"></i>', 0, NULL, 0, '2021-10-29 09:53:10', NULL, 0, 0),
  (110, 16, NULL, 'Dati sui pagamenti', '000016-000110', 2, 3, 11, 'PaymentAdminController', 110, 'PaymentModel', 'admin/payment', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2025-02-28 09:40:01', NULL, 0, 0),
  (111, 1, NULL, 'Organi politici, di amministrazione e gestione', '000001-000111', 2, 5, 2, 'PoliticOrganAdminController', 111, 'PoliticOrganModel', 'admin/politic-organ', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 09:39:55', NULL, 0, 0);

-- Dump della struttura di tabella pat.section_fo
CREATE TABLE IF NOT EXISTS `section_fo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int DEFAULT NULL,
  `owner_id` int DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lineage` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `deep` int DEFAULT NULL,
  `sort` int DEFAULT NULL,
  `controller` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller_open_data` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `icon` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int DEFAULT '0' COMMENT 'campo su ex PAT: priorità',
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint DEFAULT '1',
  `default` tinyint DEFAULT '0',
  `tag` int DEFAULT NULL,
  `typology` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guide` mediumtext COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint DEFAULT '0',
  `form_filter` text COLLATE utf8mb4_unicode_ci,
  `hide` tinyint DEFAULT '0' COMMENT 'Nasconde la sezione dall''alberatura di gestione delle pagine generiche sul back-office e non la mostra nel front-office',
  `no_required` tinyint DEFAULT '0' COMMENT 'Indica se la pagina non è più soggetta a pubblicazione',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=609 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella Sezioni per pubblicazione pagine standard in front-office.';

-- Dump dei dati della tabella pat.section_fo: ~181 rows (circa)
INSERT INTO `section_fo` (`id`, `institution_id`, `owner_id`, `parent_id`, `name`, `lineage`, `deep`, `sort`, `controller`, `controller_open_data`, `archive_name`, `url`, `icon`, `last_modification_date`, `activation_date`, `expiration_date`, `description`, `order`, `meta_title`, `meta_keywords`, `meta_description`, `is_system`, `default`, `tag`, `typology`, `guide`, `deleted`, `form_filter`, `hide`, `no_required`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, NULL, NULL, 0, 'Disposizioni Generali', '000001', 1, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:53:35', '2021-11-05 09:53:35', '2021-11-05 09:53:35', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Disposizioni generali</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:53:35', NULL, NULL),
  (2, NULL, NULL, 0, 'Organizzazione', '000002', 1, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:55:52', '2021-11-05 09:55:52', '2021-11-05 09:55:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Organizzazione</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:55:52', NULL, NULL),
  (3, NULL, NULL, 0, 'Consulenti e collaboratori', '000003', 1, 3, '\\Http\\Web\\Front\\AssignmentsFrontController@indexConsultantsAndCollaborators', 'OpenDataAssignmentsFrontController@indexConsultantsAndCollaborators', 'assignment', '#!', NULL, '2021-11-05 09:56:23', '2021-11-05 09:56:23', '2021-11-05 09:56:23', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Consulenti e collaboratori</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:23', NULL, NULL),
  (4, NULL, NULL, 0, 'Personale', '000004', 1, 4, '\\Http\\Web\\Front\\PersonnelFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:56:36', '2021-11-05 09:56:36', '2021-11-05 09:56:36', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Personale</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:36', NULL, NULL),
  (5, NULL, NULL, 0, 'Bandi di concorso', '000005', 1, 5, '\\Http\\Web\\Front\\ContestsFrontController', 'OpenDataContestsFrontController@index', 'contest', '#!', NULL, '2021-11-05 09:56:51', '2021-11-05 09:56:51', '2021-11-05 09:56:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Bandi di concorso</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:51', NULL, NULL),
  (6, NULL, NULL, 0, 'Performance', '000006', 1, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:57:09', '2021-11-05 09:57:09', '2021-11-05 09:57:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Performance</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:57:09', NULL, NULL),
  (7, NULL, NULL, 0, 'Enti controllati', '000007', 1, 7, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:57:24', '2021-11-05 09:57:24', '2021-11-05 09:57:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Enti controllati</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:57:23', NULL, NULL),
  (8, NULL, NULL, 0, 'Attività e procedimenti', '000008', 1, 8, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:58:04', '2021-11-05 09:58:04', '2021-11-05 09:58:04', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Attività e procedimenti</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 09:58:04', NULL, NULL),
  (9, NULL, NULL, 0, 'Provvedimenti', '000009', 1, 9, '\\Http\\Web\\Front\\MeasuresFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:58:26', '2021-11-05 09:58:26', '2021-11-05 09:58:26', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Provvedimenti</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:58:26', NULL, NULL),
  (10, NULL, NULL, 0, 'Bandi di gara e contratti', '000010', 1, 11, '\\Http\\Web\\Front\\BdncpProcedureFrontController@index', 'OpenDataBdncpProcedureFrontController@index', 'bdncp-procedure', '#!', NULL, '2021-11-05 09:58:46', '2021-11-05 09:58:46', '2021-11-05 09:58:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Bandi di gara e contratti</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:58:46', NULL, NULL),
  (11, NULL, NULL, 0, 'Sovvenzioni, contributi, sussidi, vantaggi economici', '000011', 1, 12, '\\Http\\Web\\Front\\GrantsFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:59:46', '2021-11-05 09:59:46', '2021-11-05 09:59:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Sovvenzioni, contributi, sussidi, vantaggi economici</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 09:59:46', NULL, NULL),
  (12, NULL, NULL, 0, 'Bilanci', '000012', 1, 13, '\\Http\\Web\\Front\\BalanceFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 10:00:09', '2021-11-05 10:00:09', '2021-11-05 10:00:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Bilanci</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 10:00:09', NULL, NULL),
  (13, NULL, NULL, 0, 'Beni immobili e gestione patrimonio', '000013', 1, 14, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:00:36', '2021-11-05 10:00:36', '2021-11-05 10:00:36', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Beni immobili e gestione patrimonio</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 10:00:35', NULL, NULL),
  (14, NULL, NULL, 0, 'Controlli e rilievi sull\'amministrazione', '000014', 1, 15, '\\Http\\Web\\Front\\ReliefChecksFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 10:01:09', '2021-11-05 10:01:09', '2021-11-05 10:01:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Controlli e rilievi sull&#39;amministrazione</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:01:09', NULL, NULL),
  (15, NULL, NULL, 0, 'Servizi erogati', '000015', 1, 16, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:01:28', '2021-11-05 10:01:28', '2021-11-05 10:01:28', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Servizi erogati</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:01:28', NULL, NULL),
  (16, NULL, NULL, 0, 'Pagamenti dell\'amministrazione', '000016', 1, 17, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:01:57', '2021-11-05 10:01:57', '2021-11-05 10:01:57', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Pagamenti dell\'amministrazione</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 10:01:57', NULL, NULL),
  (17, NULL, NULL, 0, 'Opere pubbliche', '000017', 1, 18, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:02:15', '2021-11-05 10:02:15', '2021-11-05 10:02:15', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Opere pubbliche</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:02:15', NULL, NULL),
  (18, NULL, NULL, 0, 'Altri contenuti', '000018', 1, 23, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:02:30', '2021-11-05 10:02:30', '2021-11-05 10:02:30', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Altri contenuti</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 10:02:30', NULL, NULL),
  (19, NULL, NULL, 0, 'Altre sezioni', '000019', 1, 100, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:02:43', '2021-11-05 10:02:43', '2021-11-05 10:02:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:02:43', NULL, NULL),
  (21, NULL, NULL, 1, 'Piano triennale per la prevenzione della corruzione e della trasparenza', '000001-000021', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:04:22', '2021-11-05 10:04:22', '2021-11-05 10:04:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>In questa pagina &egrave; necessario pubblicare il Piano per la trasparenza e l&#39;integrit&agrave;. Il Programma triennale per la trasparenza e l&rsquo;integrit&agrave; &egrave; delineato come strumento di programmazione autonomo rispetto al Piano di prevenzione della corruzione, pur se ad esso strettamente collegato, tant&rsquo;&egrave; che mil Programma &ldquo; di norma&rdquo; integra una sezione del predetto piano. Il collegamento fra il Piano di prevenzione della corruzione e il Programma triennale per la trasparenza &egrave; assicurato dal Responsabile della trasparenza.&nbsp;</p>\r\n\r\n<p>Tipo di contenuti da pubblicare</p>\r\n\r\n<p>Contenuto editabile, Regolamenti e documentazione</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Per la pagina &quot;<strong>Piano triennale per la prevenzione della corruzione e della trasparenza</strong>&quot; &egrave; necessario editare il contenuto richiesto dalla pagina.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:04:22', NULL, NULL),
  (22, NULL, NULL, 21, 'Prevenzione della Corruzione', '000001-000021-000022', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '174,prevenzione-della-corruzione', NULL, '2021-11-05 10:05:27', '2021-11-05 10:05:27', '2021-11-05 10:05:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>Guida</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:05:27', NULL, NULL),
  (23, NULL, NULL, 1, 'Atti generali', '000001-000023', 2, 3, '\\Http\\Web\\Front\\RegulationFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 10:06:01', '2021-11-05 10:06:01', '2021-11-05 10:06:01', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>Sono da inserire i riferimenti normativi con i relativi link alle norme di legge statale pubblicate nella banca dati &quot;Normattiva&quot; che regolano l&#39;istituzione, l&#39;organizzazione e l&#39;attivit&agrave; delle pubbliche amministrazioni. Pubblicare anche direttive, circolari, programmi, istruzioni e ogni atto che dispone in generale sulla organizzazione, sulle funzioni, sugli obiettivi, sui procedimenti e gli atti nei quali si determina l&#39;interpretazione di norme giuridiche che riguardano o dettano disposizioni per l&#39;applicazione di esse, compresi i codici di condotta.</p>\r\n\r\n<p>Tipo di contenuti da pubblicare</p>\r\n\r\n<p>Contenuto editabile, Normativa, Regolamenti e documentazione</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Per la pagina &quot;<strong>Atti generali</strong>&quot; non &egrave; normalmente necessario editare del contenuto libero, poich&egrave; la sezione ospita gi&agrave; dei contenuti di pubblicazione automatica.</p>\r\n\r\n<p>Riferimenti normativi</p>\r\n\r\n<p>articolo 12 Comma 1,2 Dlgs 14 marzo 2013, n. 33</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:06:01', NULL, NULL),
  (24, NULL, NULL, 23, 'Riferimenti normativi su organizzazione e attività', '000001-000023-000024', 3, 1, '\\Http\\Web\\Front\\NormativeFrontController@indexReferencesOnOrganizationAndActivities', 'OpenDataNormativeFrontController@indexReferencesOnOrganizationAndActivities', 'normative', '#!', NULL, '2021-11-05 10:06:39', '2021-11-05 10:06:39', '2021-11-05 10:06:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:06:39', NULL, NULL),
  (25, NULL, NULL, 23, 'Atti amministrativi generali', '000001-000023-000025', 3, 2, '\\Http\\Web\\Front\\RegulationFrontController@indexGeneralAdministrativeActs', 'OpenDataRegulationsFrontController@indexGeneralAdministrativeActs', 'regulation', '#!', NULL, '2021-11-05 10:07:03', '2021-11-05 10:07:03', '2021-11-05 10:07:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:07:03', NULL, NULL),
  (26, NULL, NULL, 23, 'Documenti di programmazione strategico-gestionale', '000001-000023-000026', 3, 3, '\\Http\\Web\\Front\\RegulationFrontController@indexDocumentsStrategicManagement', 'OpenDataRegulationsFrontController@indexDocumentsStrategicManagement', 'regulation', '#!', NULL, '2021-11-05 10:07:18', '2021-11-05 10:07:18', '2021-11-05 10:07:18', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:07:18', NULL, NULL),
  (27, NULL, NULL, 23, 'Statuti e leggi regionali', '000001-000023-000027', 3, 4, '\\Http\\Web\\Front\\RegulationFrontController@indexStatutesRegionalLaws', 'OpenDataRegulationsFrontController@indexStatutesRegionalLaws', 'regulation', '#!', NULL, '2021-11-05 10:07:32', '2021-11-05 10:07:32', '2021-11-05 10:07:32', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:07:32', NULL, NULL),
  (28, NULL, NULL, 23, 'Codice disciplinare e codice di condotta', '000001-000023-000028', 3, 5, '\\Http\\Web\\Front\\RegulationFrontController@indexDisciplinaryAndConductCode', 'OpenDataRegulationsFrontController@indexDisciplinaryAndConductCode', 'regulation', '#!', NULL, '2021-11-05 10:07:43', '2021-11-05 10:07:43', '2021-11-05 10:07:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:07:43', NULL, NULL),
  (29, NULL, NULL, 23, 'Regolamenti', '000001-000023-000029', 3, 6, '\\Http\\Web\\Front\\RegulationFrontController@indexRegulations', 'OpenDataRegulationsFrontController@indexRegulations', 'regulation', '#!', NULL, '2021-11-05 10:07:51', '2021-11-05 10:07:51', '2021-11-05 10:07:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:07:51', NULL, NULL),
  (30, NULL, NULL, 23, 'Modulistica', '000001-000023-000030', 3, 7, '\\Http\\Web\\Front\\ModuleFrontController@indexModulistics', 'OpenDataModulisticsFrontController@indexModulistics', 'module', '#!', NULL, '2021-11-05 10:08:00', '2021-11-05 10:08:00', '2021-11-05 10:08:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:08:00', NULL, NULL),
  (31, NULL, NULL, 1, 'Oneri informativi per cittadini e imprese', '000001-000031', 2, 4, '\\Http\\Web\\Front\\ChargeFrontController@pivot', NULL, 'charge', '#!', NULL, '2021-11-05 10:09:57', '2021-11-05 10:09:57', '2021-11-05 10:09:57', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:09:57', NULL, NULL),
  (32, NULL, NULL, 31, 'Scadenzario obblighi amministrativi', '000001-000031-000032', 3, 1, '\\Http\\Web\\Front\\ChargeFrontController@indexSchedule', 'OpenDataChargesFrontController@indexSchedule', 'charge', '#!', NULL, '2021-11-05 10:10:27', '2021-11-05 10:10:27', '2021-11-05 10:10:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 0, '2021-11-05 10:10:27', NULL, NULL),
  (34, NULL, NULL, 1, 'Burocrazia zero', '000001-000034', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:10:56', '2021-11-05 10:10:56', '2021-11-05 10:10:56', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;"> La guida di questo contenuto non è per il momento disponibile.</p>', 0, NULL, 0, 1, '2021-11-05 10:10:56', NULL, NULL),
  (37, NULL, NULL, 2, 'Titolari di incarichi politici, di amministrazione, di direzione o di governo', '000002-000037', 2, 2, '\\Http\\Web\\Front\\PersonnelFrontController@indexPositionHolders', 'OpenDataPersonnelFrontController@indexPositionHolders', 'personnel', '#!', NULL, '2021-11-05 10:12:06', '2021-11-05 10:12:06', '2021-11-05 10:12:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>Guida</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:12:06', NULL, NULL),
  (38, NULL, NULL, 2, 'Sanzioni per mancata comunicazione dei dati', '000002-000038', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:12:24', '2021-11-05 10:12:24', '2021-11-05 10:12:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:24', NULL, NULL),
  (39, NULL, NULL, 2, 'Rendiconti gruppi consiliari regionali/provinciali', '000002-000039', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:12:42', '2021-11-05 10:12:42', '2021-11-05 10:12:42', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:42', NULL, NULL),
  (40, NULL, NULL, 2, 'Articolazione degli uffici', '000002-000040', 2, 5, '\\Http\\Web\\Front\\StructuresFrontController@index', 'OpenDataStructuresFrontController@index', 'structure', '#!', NULL, '2021-11-05 10:12:51', '2021-11-05 10:12:51', '2021-11-05 10:12:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:51', NULL, NULL),
  (41, NULL, NULL, 40, 'Organigramma', '000002-000040-000041', 3, 1, '\\Http\\Web\\Front\\StructuresFrontController@organigram', NULL, 'structure', '#!', NULL, '2021-11-05 10:13:13', '2021-11-05 10:13:13', '2021-11-05 10:13:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:13:13', NULL, NULL),
  (42, NULL, NULL, 40, 'Archivio', '000002-000040-000042', 3, 2, '\\Http\\Web\\Front\\StructuresFrontController@archive', NULL, 'structure', '#!', NULL, '2021-11-05 10:13:21', '2021-11-05 10:13:21', '2021-11-05 10:13:21', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:13:21', NULL, NULL),
  (43, NULL, NULL, 2, 'Telefono e posta elettronica', '000002-000043', 2, 6, '\\Http\\Web\\Front\\StructuresFrontController@indexPhoneMail', 'OpenDataStructuresFrontController@index', 'structure', '#!', NULL, '2021-11-05 10:13:38', '2021-11-05 10:13:38', '2021-11-05 10:13:38', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:13:38', NULL, NULL),
  (44, NULL, NULL, 43, 'Posta elettronica certificata', '000002-000043-000044', 3, 1, '\\Http\\Web\\Front\\StructuresFrontController@indexCertifiedMail', 'OpenDataStructuresFrontController@indexCertifiedMail', 'structure', '#!', NULL, '2021-11-05 10:14:09', '2021-11-05 10:14:09', '2021-11-05 10:14:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:14:09', NULL, NULL),
  (46, NULL, NULL, 3, 'Titolari di incarichi di collaborazione o consulenza', '000003-000046', 2, 2, '\\Http\\Web\\Front\\AssignmentsFrontController@indexOfficeHolders', 'OpenDataAssignmentsFrontController@indexOfficeHolders', 'assignment', '#!', NULL, '2021-11-05 10:14:54', '2021-11-05 10:14:54', '2021-11-05 10:14:54', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:14:54', NULL, NULL),
  (47, NULL, NULL, 46, 'Archivio incarichi di collaborazione o consulenza', '000003-000046-000047', 3, 1, '\\Http\\Web\\Front\\AssignmentsFrontController@indexArchiveOfficeHolders', 'OpenDataAssignmentsFrontController@indexArchiveOfficeHolders', 'assignment', '#!', NULL, '2021-11-05 10:15:14', '2021-11-05 10:15:14', '2021-11-05 10:15:14', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:15:14', NULL, NULL),
  (50, NULL, NULL, 3, 'Collegio dei Revisori dei Conti', '000003-000050', 2, 5, '\\Http\\Web\\Front\\AssignmentsFrontController@indexRevisoriDeiConti', NULL, NULL, '#!', NULL, '2021-11-05 10:16:14', '2021-11-05 10:16:14', '2021-11-05 10:16:14', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:16:14', NULL, NULL),
  (58, NULL, NULL, 4, 'Titolari di incarichi dirigenziali amministrativi di vertice', '000004-000058', 2, 2, '\\Http\\Web\\Front\\PersonnelFrontController@indexTopPositions', 'OpenDataPersonnelFrontController@indexTopPositions', 'personnel', '#!', NULL, '2021-11-05 10:22:09', '2021-11-05 10:22:09', '2021-11-05 10:22:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:22:09', NULL, NULL),
  (59, NULL, NULL, 58, 'Segretario Generale', '000004-000058-000059', 3, 1, '\\Http\\Web\\Front\\PersonnelFrontController@indexGeneralSecretary', 'OpenDataPersonnelFrontController@indexGeneralSecretary', 'personnel', '#!', NULL, '2021-11-05 10:22:37', '2021-11-05 10:22:37', '2021-11-05 10:22:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:22:37', NULL, NULL),
  (60, NULL, NULL, 4, 'Titolari di incarichi dirigenziali (dirigenti non generali)', '000004-000060', 2, 3, '\\Http\\Web\\Front\\PersonnelFrontController@indexManagerialPositions', 'OpenDataPersonnelFrontController@indexManagerialPositions', 'personnel', '#!', NULL, '2021-11-05 10:23:03', '2021-11-05 10:23:03', '2021-11-05 10:23:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:03', NULL, NULL),
  (61, NULL, NULL, 4, 'Dirigenti cessati', '000004-000061', 2, 4, '\\Http\\Web\\Front\\PersonnelFrontController@indexExecutivesTerminated', NULL, 'personnel', '#!', NULL, '2021-11-05 10:23:12', '2021-11-05 10:23:12', '2021-11-05 10:23:12', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:23:12', NULL, NULL),
  (62, NULL, NULL, 4, 'Sanzioni per mancata comunicazione dei dati', '000004-000062', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:23:21', '2021-11-05 10:23:21', '2021-11-05 10:23:21', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:21', NULL, NULL),
  (63, NULL, NULL, 4, 'Elevate qualificazioni', '000004-000063', 2, 6, '\\Http\\Web\\Front\\PersonnelFrontController@indexOrganisationalPositions', 'OpenDataPersonnelFrontController@indexOrganisationalPositions', 'personnel', '#!', NULL, '2021-11-05 10:23:32', '2021-11-05 10:23:32', '2021-11-05 10:23:32', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:32', NULL, NULL),
  (64, NULL, NULL, 4, 'Dotazione organica', '000004-000064', 2, 7, '\\Http\\Web\\Front\\PersonnelFrontController@indexStaffing', 'OpenDataPersonnelFrontController@indexStaffing', 'personnel', '#!', NULL, '2021-11-05 10:23:51', '2021-11-05 10:23:51', '2021-11-05 10:23:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:51', NULL, NULL),
  (65, NULL, NULL, 4, 'Personale non a tempo indeterminato', '000004-000065', 2, 8, '\\Http\\Web\\Front\\PersonnelFrontController@indexNotIndefinite', 'OpenDataPersonnelFrontController@indexNotIndefinite', 'personnel', '#!', NULL, '2021-11-05 10:24:00', '2021-11-05 10:24:00', '2021-11-05 10:24:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:24:00', NULL, NULL),
  (66, NULL, NULL, 4, 'Tassi di assenza', '000004-000066', 2, 9, '\\Http\\Web\\Front\\AbsenceRatesFrontController@index', 'OpenDataAbsenceRatesFrontController@index', 'absence-rates', '#!', NULL, '2021-11-05 10:24:08', '2021-11-05 10:24:08', '2021-11-05 10:24:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, 's:980:"[{"type":"form_open","name":"absence_rates","action":"{current_url}","attributes":{"method":"get"}},{"prefix":"<div class=\\"form-row mb-4\\"><div class=\\"col-lg-7\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{structures}","name":"structures","extra":"id=\\"structures\\"","label":{"for":"t","text":"Struttura da cercare","class":"active"}},{"prefix":"<div class=\\"col\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{years}","name":"start","extra":"id=\\"start_date\\"","label":{"for":"testo_form","text":"Anno","class":"active"}},{"prefix":"<div class=\\"col\\">","suffix":"<\\/div><\\/div>","type":"form_dropdown","options":"{months}","name":"months","extra":"id=\\"months\\"","label":{"for":"testo_form","text":"Mese","class":"active"}},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 10:24:08', NULL, NULL),
  (67, NULL, NULL, 4, 'Incarichi conferiti e autorizzati ai dipendenti (dirigenti e non dirigenti)', '000004-000067', 2, 10, '\\Http\\Web\\Front\\AssignmentsFrontController@indexExecutivesNonExecutives', 'OpenDataAssignmentsFrontController@indexExecutivesNonExecutives', 'assignment', '#!', NULL, '2021-11-05 10:24:17', '2021-11-05 10:24:17', '2021-11-05 10:24:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:24:17', NULL, NULL),
  (68, NULL, NULL, 67, 'Archivio incarichi dipendenti', '000004-000067-000068', 3, 1, '\\Http\\Web\\Front\\AssignmentsFrontController@indexEmployeeAssignments', 'OpenDataAssignmentsFrontController@indexEmployeeAssignments', NULL, '#!', NULL, '2021-11-05 10:24:37', '2021-11-05 10:24:37', '2021-11-05 10:24:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:24:37', NULL, NULL),
  (69, NULL, NULL, 4, 'Contrattazione collettiva', '000004-000069', 2, 11, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:01', '2021-11-05 10:25:01', '2021-11-05 10:25:01', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:01', NULL, NULL),
  (70, NULL, NULL, 4, 'Contrattazione integrativa', '000004-000070', 2, 12, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:22', '2021-11-05 10:25:22', '2021-11-05 10:25:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:22', NULL, NULL),
  (71, NULL, NULL, 4, 'OIV', '000004-000071', 2, 13, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:31', '2021-11-05 10:25:31', '2021-11-05 10:25:31', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:31', NULL, NULL),
  (72, NULL, NULL, 4, 'Elenco del personale iscritto nel registro dei revisori dei conti', '000004-000072', 2, 14, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:39', '2021-11-05 10:25:39', '2021-11-05 10:25:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:25:39', NULL, NULL),
  (73, NULL, NULL, 4, 'Archivio personale', '000004-000073', 2, 15, '\\Http\\Web\\Front\\PersonnelFrontController@indexArchive', NULL, 'personnel', '#!', NULL, '2021-11-05 10:25:48', '2021-11-05 10:25:48', '2021-11-05 10:25:48', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:25:48', NULL, NULL),
  (75, NULL, NULL, 5, 'Concorsi attivi', '000005-000075', 2, 1, '\\Http\\Web\\Front\\ContestsFrontController@indexActive', 'OpenDataContestsFrontController@indexActive', 'contest', '#!', NULL, '2021-11-05 10:26:25', '2021-11-05 10:26:25', '2021-11-05 10:26:25', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:25', NULL, NULL),
  (76, NULL, NULL, 5, 'Concorsi scaduti', '000005-000076', 2, 2, '\\Http\\Web\\Front\\ContestsFrontController@indexExpired', 'OpenDataContestsFrontController@indexExpired', 'contest', '#!', NULL, '2021-11-05 10:26:35', '2021-11-05 10:26:35', '2021-11-05 10:26:35', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:35', NULL, NULL),
  (77, NULL, NULL, 5, 'Avvisi', '000005-000077', 2, 4, '\\Http\\Web\\Front\\ContestsFrontController@indexAlert', 'OpenDataContestsFrontController@indexAlert', 'contest', '#!', NULL, '2021-11-05 10:26:47', '2021-11-05 10:26:47', '2021-11-05 10:26:47', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:47', NULL, NULL),
  (78, NULL, NULL, 5, 'Esiti', '000005-000078', 2, 5, '\\Http\\Web\\Front\\ContestsFrontController@indexResult', 'OpenDataContestsFrontController@indexResult', 'contest', '#!', NULL, '2021-11-05 10:26:54', '2021-11-05 10:26:54', '2021-11-05 10:26:54', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:54', NULL, NULL),
  (79, NULL, NULL, 5, 'Ricerca', '000005-000079', 2, 6, '\\Http\\Web\\Front\\ContestsFrontController', NULL, NULL, '#!', NULL, '2021-11-05 10:27:07', '2021-11-05 10:27:07', '2021-11-05 10:27:07', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:27:07', NULL, NULL),
  (82, NULL, NULL, 6, 'Sistema di misurazione e valutazione della Performance', '000006-000082', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:09', '2021-11-05 10:28:09', '2021-11-05 10:28:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:09', NULL, NULL),
  (83, NULL, NULL, 6, 'Piano della Performance', '000006-000083', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:17', '2021-11-05 10:28:17', '2021-11-05 10:28:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:17', NULL, NULL),
  (84, NULL, NULL, 6, 'Relazione sulla Performance', '000006-000084', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:26', '2021-11-05 10:28:26', '2021-11-05 10:28:26', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:26', NULL, NULL),
  (85, NULL, NULL, 6, 'Ammontare complessivo dei premi', '000006-000085', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:41', '2021-11-05 10:28:41', '2021-11-05 10:28:41', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:41', NULL, NULL),
  (86, NULL, NULL, 6, 'Dati relativi ai premi', '000006-000086', 2, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:50', '2021-11-05 10:28:50', '2021-11-05 10:28:50', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:50', NULL, NULL),
  (89, NULL, NULL, 7, 'Enti pubblici vigilati', '000007-000089', 2, 2, '\\Http\\Web\\Front\\CompanyFrontController@indexPublicEntities', 'OpenDataCompanyFrontController@indexPublicEntities', 'company', '#!', NULL, '2021-11-05 10:29:22', '2021-11-05 10:29:22', '2021-11-05 10:29:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:29:22', NULL, NULL),
  (91, NULL, NULL, 7, 'Società partecipate', '000007-000091', 2, 3, '\\Http\\Web\\Front\\CompanyFrontController@indexParticipatedCompanies', 'OpenDataCompanyFrontController@indexParticipatedCompanies', 'company', '#!', NULL, '2021-11-05 10:30:06', '2021-11-05 10:30:06', '2021-11-05 10:30:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:30:06', NULL, NULL),
  (92, NULL, NULL, 91, 'Archivio', '000007-000091-000092', 3, 1, '\\Http\\Web\\Front\\CompanyFrontController@archivedParticipatedCompanies', NULL, NULL, '#!', NULL, '2021-11-05 10:30:25', '2021-11-05 10:30:25', '2021-11-05 10:30:25', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:30:25', NULL, NULL),
  (93, NULL, NULL, 7, 'Enti di diritto privato controllati', '000007-000093', 2, 4, '\\Http\\Web\\Front\\CompanyFrontController@indexControlledPrivateEntities', 'OpenDataCompanyFrontController@indexControlledPrivateEntities', 'company', '#!', NULL, '2021-11-05 10:30:40', '2021-11-05 10:30:40', '2021-11-05 10:30:40', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:30:40', NULL, NULL),
  (95, NULL, NULL, 7, 'Rappresentazione grafica', '000007-000095', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:31:10', '2021-11-05 10:31:10', '2021-11-05 10:31:10', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:31:10', NULL, NULL),
  (98, NULL, NULL, 8, 'Tipologie di procedimento', '000008-000098', 2, 3, '\\Http\\Web\\Front\\ProceedingsFrontController@indexProceedingsType', 'OpenDataProceedingFrontController@indexProceedingsType', 'proceeding', '#!', NULL, '2021-11-05 10:31:53', '2021-11-05 10:31:53', '2021-11-05 10:31:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:31:53', NULL, NULL),
  (99, NULL, NULL, 98, 'Archivio procedimenti', '000008-000098-000099', 3, 1, '\\Http\\Web\\Front\\ProceedingsFrontController@archived', NULL, NULL, '#!', NULL, '2021-11-05 10:32:13', '2021-11-05 10:32:13', '2021-11-05 10:32:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:32:13', NULL, NULL),
  (101, NULL, NULL, 8, 'Dichiarazioni sostitutive e acquisizione d\'ufficio dei dati', '000008-000101', 2, 5, '\\Http\\Web\\Front\\ModuleFrontController@indexSubstituteDeclarations', 'OpenDataModulisticsFrontController@indexSubstituteDeclarations', 'module', '#!', NULL, '2021-11-05 10:32:46', '2021-11-05 10:32:46', '2021-11-05 10:32:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:32:46', NULL, NULL),
  (103, NULL, NULL, 9, 'Provvedimenti organi indirizzo politico', '000009-000103', 2, 2, '\\Http\\Web\\Front\\MeasuresFrontController@indexPolitical', 'OpenDataMeasuresFrontController@indexPolitical', 'measure', '#!', NULL, '2021-11-05 10:33:16', '2021-11-05 10:33:16', '2021-11-05 10:33:16', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:33:16', NULL, NULL),
  (104, NULL, NULL, 103, 'Determinazione del Presidente del Consiglio', '000009-000103-000104', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:33:53', '2021-11-05 10:33:53', '2021-11-05 10:33:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:33:53', NULL, NULL),
  (105, NULL, NULL, 103, 'Deliberazione dell\'Ufficio di presidenza', '000009-000103-000105', 3, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:34:08', '2021-11-05 10:34:08', '2021-11-05 10:34:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:34:08', NULL, NULL),
  (106, NULL, NULL, 9, 'Provvedimenti dirigenti amministrativi', '000009-000106', 2, 3, '\\Http\\Web\\Front\\MeasuresFrontController@indexAdministrative', 'OpenDataMeasuresFrontController@indexAdministrative', 'measure', '#!', NULL, '2021-11-05 10:34:34', '2021-11-05 10:34:34', '2021-11-05 10:34:34', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:34:34', NULL, NULL),
  (107, NULL, NULL, 106, 'Accordi con soggetti privati o altre P.A.', '000009-000106-000107', 3, 1, '\\Http\\Web\\Front\\MeasuresFrontController@indexDirectsAgreementsPa', NULL, NULL, '#!', NULL, '2021-11-05 10:35:17', '2021-11-05 10:35:17', '2021-11-05 10:35:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:35:17', NULL, NULL),
  (108, NULL, NULL, 106, 'Procedura di scelta del contraente', '000009-000106-000108', 3, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:35:27', '2021-11-05 10:35:27', '2021-11-05 10:35:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:35:27', NULL, NULL),
  (110, NULL, NULL, 581, 'Dati previsti dall\'articolo 1, comma 32, della legge 6 novembre 2012, n. 190. Informazioni sulle singole procedure', '000010-000581-000110', 2, 1, '\\Http\\Web\\Front\\ContestsActsFrontController@indexIndividualProceduresTabularFormat', 'OpenDataContestActsFrontController@indexIndividualProceduresTabularFormat', 'contests-act', '#!', NULL, '2021-11-05 10:36:10', '2021-11-05 10:36:10', '2021-11-05 10:36:10', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:36:10', NULL, NULL),
  (111, NULL, NULL, 110, 'Tabelle riassuntive ai sensi dell\'Art. 1 comma 32 della legge n. 190/2012', '000010-000581-000110-000111', 3, 1, '\\Http\\Web\\Front\\AvcpFrontController@index', NULL, NULL, '#!', NULL, '2021-11-05 10:36:37', '2021-11-05 10:36:37', '2021-11-05 10:36:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:36:37', NULL, NULL),
  (112, NULL, NULL, 581, 'Atti relativi alle procedure per l’affidamento di appalti pubblici di servizi, forniture, lavori e opere, di concorsi pubblici di progettazione, di concorsi di idee e di concessioni, compresi quelli tra enti nell\'ambito del settore pubblico di cui all\'art. 5 del dlgs n. 50/2016', '000010-000581-000112', 2, 3, '\\Http\\Web\\Front\\ContestsActsFrontController@indexActsOfContractingAuthorities', 'OpenDataContestActsFrontController@indexActsOfContractingAuthorities', 'contests-act', '#!', NULL, '2021-11-05 10:37:04', '2021-11-05 10:37:04', '2021-11-05 10:37:04', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:37:04', NULL, NULL),
  (113, NULL, NULL, 10, 'Atti relativi alla programmazione di lavori, opere, servizi e forniture', '000010-000113', 3, 2, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexActsRelatingToProgramming', 'OpenDataProgrammingActFrontController@index', 'programming-act', '#!', NULL, '2021-11-05 10:37:36', '2021-11-05 10:37:36', '2021-11-05 10:37:36', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:37:36', NULL, NULL),
  (114, NULL, NULL, 112, 'Provvedimenti di esclusione e di ammissione', '000010-000581-000112-000114', 4, 16, '\\Http\\Web\\Front\\NoticeActsFrontController@indexMeasuresExclusions', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, '2021-11-05 10:37:55', '2021-11-05 10:37:55', '2021-11-05 10:37:55', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 1, 0, '2021-11-05 10:37:55', NULL, NULL),
  (115, NULL, NULL, 112, 'Commissione giudicatrice', '000010-000581-000112-000115', 4, 6, '\\Http\\Web\\Front\\NoticeActsFrontController@indexCommissionComposition', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, '2021-11-05 10:38:13', '2021-11-05 10:38:13', '2021-11-05 10:38:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:38:13', NULL, NULL),
  (116, NULL, NULL, 112, 'Contratti', '000010-000581-000112-000116', 4, 7, '\\Http\\Web\\Front\\NoticeActsFrontController@indexManagerialPositions', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, '2021-11-05 10:38:22', '2021-11-05 10:38:22', '2021-11-05 10:38:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:38:22', NULL, NULL),
  (117, NULL, NULL, 112, 'Resoconti della gestione finanziaria dei contratti al termine della loro esecuzione', '000010-000581-000112-000117', 4, 10, '\\Http\\Web\\Front\\NoticeActsFrontController@indexFinancialManagementReports', NULL, 'notices-act', '#!', NULL, '2021-11-05 10:38:32', '2021-11-05 10:38:32', '2021-11-05 10:38:32', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:38:32', NULL, NULL),
  (125, NULL, NULL, 11, 'Criteri e modalità', '000011-000125', 2, 2, '\\Http\\Web\\Front\\NormativeFrontController@indexCriteriaAndModalities', 'OpenDataNormativeFrontController@indexCriteriaAndModalities', 'normative', '#!', NULL, '2021-11-05 10:40:58', '2021-11-05 10:40:58', '2021-11-05 10:40:58', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:40:58', NULL, NULL),
  (126, NULL, NULL, 11, 'Atti di concessione', '000011-000126', 2, 3, '\\Http\\Web\\Front\\GrantsFrontController@indexConcessionActs', 'OpenDataGrantsFrontController@indexConcessionActs', 'grant', '#!', NULL, '2021-11-05 10:41:08', '2021-11-05 10:41:08', '2021-11-05 10:41:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:41:08', NULL, NULL),
  (127, NULL, NULL, 11, 'Elenco soggetti beneficiari', '000011-000127', 2, 4, '\\Http\\Web\\Front\\GrantsFrontController@indexBeneficiary', 'OpenDataGrantsFrontController@indexBeneficiary', 'grant', '#!', NULL, '2021-11-05 10:41:22', '2021-11-05 10:41:22', '2021-11-05 10:41:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:41:22', NULL, NULL),
  (130, NULL, NULL, 12, 'Bilancio preventivo e consuntivo', '000012-000130', 2, 2, '\\Http\\Web\\Front\\BalanceFrontController@indexFinalAndQuote', 'OpenDataBalanceFrontController@indexFinalAndQuote', 'balance', '#!', NULL, '2021-11-05 10:42:02', '2021-11-05 10:42:02', '2021-11-05 10:42:02', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:42:02', NULL, NULL),
  (131, NULL, NULL, 12, 'Piano degli indicatori e dei risultati attesi di bilancio', '000012-000131', 2, 3, '\\Http\\Web\\Front\\BalanceFrontController@indexExpectedResults', 'OpenDataBalanceFrontController@indexExpectedResults', 'balance', '#!', NULL, '2021-11-05 10:42:13', '2021-11-05 10:42:13', '2021-11-05 10:42:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:42:13', NULL, NULL),
  (133, NULL, NULL, 13, 'Patrimonio immobiliare', '000013-000133', 2, 2, '\\Http\\Web\\Front\\RealEstateAssetFrontController', 'OpenDataRealEstateAssetFrontController@index', 'real-estate-asset', '#!', NULL, '2021-11-05 10:43:00', '2021-11-05 10:43:00', '2021-11-05 10:43:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:43:00', NULL, NULL),
  (135, NULL, NULL, 13, 'Canoni di locazione o affitto', '000013-000135', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:43:50', '2021-11-05 10:43:50', '2021-11-05 10:43:50', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:43:50', NULL, NULL),
  (136, NULL, NULL, 135, 'Canoni di locazione o affitto percepiti', '000013-000135-000136', 3, 1, '\\Http\\Web\\Front\\CanonFrontController@indexPerceived', 'OpenDataCanonsFrontController@indexPerceived', 'canon', '#!', NULL, '2021-11-05 10:44:23', '2021-11-05 10:44:23', '2021-11-05 10:44:23', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:44:23', NULL, NULL),
  (137, NULL, NULL, 135, 'Canoni di locazione o affitto versati', '000013-000135-000137', 3, 2, '\\Http\\Web\\Front\\CanonFrontController@indexPaid', 'OpenDataCanonsFrontController@indexPaid', 'canon', '#!', NULL, '2021-11-05 10:44:39', '2021-11-05 10:44:39', '2021-11-05 10:44:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:44:39', NULL, NULL),
  (139, NULL, NULL, 14, 'Organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe', '000014-000139', 2, 2, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexIndependentOrganisms', 'OpenDataReliefChecksController@indexIndependentOrganisms', 'relief-check', '#!', NULL, '2021-11-05 10:47:58', '2021-11-05 10:47:58', '2021-11-05 10:47:58', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:47:58', NULL, NULL),
  (140, NULL, NULL, 139, 'Attestazione dell\'OIV o di altra struttura analoga nell\'assolvimento degli obblighi di pubblicazione', '000014-000139-000140', 3, 1, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexOIVCertification', 'OpenDataReliefChecksController@indexOIVCertification', 'relief-check', '#!', NULL, '2021-11-05 10:48:30', '2021-11-05 10:48:30', '2021-11-05 10:48:30', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:30', NULL, NULL),
  (141, NULL, NULL, 139, 'Documento dell\'OIV di validazione della Relazione sulla Performance', '000014-000139-000141', 3, 2, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexOIVDocument', 'OpenDataReliefChecksController@indexOIVDocument', 'relief-check', '#!', NULL, '2021-11-05 10:48:43', '2021-11-05 10:48:43', '2021-11-05 10:48:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:43', NULL, NULL),
  (142, NULL, NULL, 139, 'Relazione dell\'OIV sul funzionamento complessivo del Sistema di valutazione trasparenza e integrità dei controlli interni', '000014-000139-000142', 3, 3, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexOIReport', 'OpenDataReliefChecksController@indexOIReport', 'relief-check', '#!', NULL, '2021-11-05 10:48:56', '2021-11-05 10:48:56', '2021-11-05 10:48:56', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:56', NULL, NULL),
  (143, NULL, NULL, 139, 'Altri atti degli organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe', '000014-000139-000143', 3, 4, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexOtherActs', 'OpenDataReliefChecksController@indexOtherActs', 'relief-check', '#!', NULL, '2021-11-05 10:49:08', '2021-11-05 10:49:08', '2021-11-05 10:49:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:49:08', NULL, NULL),
  (144, NULL, NULL, 14, 'Organi di revisione amministrativa e contabile', '000014-000144', 2, 3, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexReviewOrganisms', 'OpenDataReliefChecksController@indexReviewOrganisms', 'relief-check', '#!', NULL, '2021-11-05 10:49:49', '2021-11-05 10:49:49', '2021-11-05 10:49:49', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:49:49', NULL, NULL),
  (145, NULL, NULL, 14, 'Corte dei conti', '000014-000145', 2, 4, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexCourtOfAuditors', 'OpenDataReliefChecksController@indexCourtOfAuditors', 'relief-check', '#!', NULL, '2021-11-05 10:50:51', '2021-11-05 10:50:51', '2021-11-05 10:50:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:50:51', NULL, NULL),
  (147, NULL, NULL, 15, 'Carta dei Servizi e standard di qualità', '000015-000147', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:54:16', '2021-11-05 10:54:16', '2021-11-05 10:54:16', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:54:16', NULL, NULL),
  (148, NULL, NULL, 15, 'Class action', '000015-000148', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:54:27', '2021-11-05 10:54:27', '2021-11-05 10:54:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:54:27', NULL, NULL),
  (149, NULL, NULL, 15, 'Costi contabilizzati', '000015-000149', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:54:45', '2021-11-05 10:54:45', '2021-11-05 10:54:45', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:54:45', NULL, NULL),
  (150, NULL, NULL, 15, 'Liste di attesa', '000015-000150', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:54:53', '2021-11-05 10:54:53', '2021-11-05 10:54:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:54:53', NULL, NULL),
  (151, NULL, NULL, 15, 'Servizi in rete', '000015-000151', 2, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:55:03', '2021-11-05 10:55:03', '2021-11-05 10:55:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:55:03', NULL, NULL),
  (154, NULL, NULL, 16, 'Dati sui pagamenti', '000016-000154', 2, 2, '\\Http\\Web\\Front\\PaymentFrontController@index', 'OpenDataPaymentFrontController@index', 'payment', '#!', NULL, '2021-11-05 10:56:05', '2021-11-05 10:56:05', '2021-11-05 10:56:05', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:56:05', NULL, NULL),
  (155, NULL, NULL, 604, 'Pagamenti di sovvenzioni, contributi, sussidi, vantaggi economici', '000016-000154-000604-000155', 4, 1, '\\Http\\Web\\Front\\GrantsFrontController@indexLiquidation', 'OpenDataGrantsFrontController@indexLiquidation', 'grant', '#!', NULL, '2021-11-05 10:57:06', '2021-11-05 10:57:06', '2021-11-05 10:57:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:57:06', NULL, NULL),
  (156, NULL, NULL, 604, 'Pagamenti di Consulenti e collaboratori', '000016-000154-000604-000156', 4, 2, '\\Http\\Web\\Front\\AssignmentsFrontController@indexPaymentsConsultantsCollaborators', 'OpenDataAssignmentsFrontController@indexPaymentsConsultantsCollaborators', 'assignment', '#!', NULL, '2021-11-05 11:00:53', '2021-11-05 11:00:53', '2021-11-05 11:00:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, 's:1034:"[{"type":"form_open","name":"absence_rates","action":"{current_url}","attributes":{"method":"get"}},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"fn","id":"full_name","placeholder":"Qualunque","value":"{full_name}"},"label":{"for":"fn","text":"Cognome e nome"}},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"object","id":"object","placeholder":"Qualunque","value":"{object}"},"label":{"for":"object","text":"Oggetto"}},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{year}","name":"year","extra":"id=\\"year\\"","label":{"for":"year","text":"Anno","class":"active"}},{"type":"tag","content":"<\\/div>"},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 11:00:53', NULL, NULL),
  (157, NULL, NULL, 604, 'Pagamenti di Bandi di gara e contratti fino al 31/12/2023', '000016-000154-000604-000157', 4, 3, '\\Http\\Web\\Front\\ContestsActsFrontController@indexPaymentsContestsActs', 'OpenDataContestActsFrontController@indexPaymentsContestsActs', 'contests-act', '#!', NULL, '2021-11-05 11:01:46', '2021-11-05 11:01:46', '2021-11-05 11:01:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, 's:2118:"[{"type":"form_open","name":"contest_acts","action":"{current_url}","attributes":{"method":"get"}},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{structures}","name":"structures","extra":"id=\\"structures\\"","label":{"for":"s","text":"Struttura da cercare","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"object","value":"{object}","id":"object","class":"class_form","placeholder":"Qualunque"},"label":{"for":"object","text":"Oggetto","class":"active"}},{"type":"tag","content":"<\\/div>"},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{contraent}","name":"contraent","extra":"id=\\"contraent\\"","label":{"for":"c","text":"Scelta del contraente","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"cig","value":"{cig}","id":"cig","class":"class_form","placeholder":"Qualunque"},"label":{"for":"cig","text":"Codice CIG","class":"active"}},{"type":"tag","content":"<\\/div>"},{"type":"tag","content":"<label>Data di pubblicazione<\\/label><div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"start_p_date","value":"{start_p_date}","type":"date","id":"start_p_date","class":"class_form","placeholder":"Qualunque"},"label":{"for":"start_p_date","text":"dal","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"end_p_date","value":"{end_p_date}","type":"date","id":"end_p_date","class":"class_form","placeholder":"Qualunque"},"label":{"for":"end_p_date","text":"al","class":"active"}},{"type":"tag","content":"<\\/div>"},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 11:01:46', NULL, NULL),
  (158, NULL, NULL, 16, 'Dati sui pagamenti del servizio sanitario nazionale', '000016-000158', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:02:39', '2021-11-05 11:02:39', '2021-11-05 11:02:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:02:39', NULL, NULL),
  (159, NULL, NULL, 16, 'Indicatore di tempestività dei pagamenti', '000016-000159', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:04:52', '2021-11-05 11:04:52', '2021-11-05 11:04:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:04:52', NULL, NULL),
  (160, NULL, NULL, 159, 'Ammontare complessivo dei debiti e numero delle imprese creditrici', '000016-000159-000160', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:05:37', '2021-11-05 11:05:37', '2021-11-05 11:05:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:05:37', NULL, NULL),
  (161, NULL, NULL, 16, 'IBAN e pagamenti informatici', '000016-000161', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:06:03', '2021-11-05 11:06:03', '2021-11-05 11:06:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:06:03', NULL, NULL),
  (166, NULL, NULL, 17, 'Nuclei di valutazione e verifica degli investimenti pubblici', '000017-000166', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:08:41', '2021-11-05 11:08:41', '2021-11-05 11:08:41', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:08:41', NULL, NULL),
  (167, NULL, NULL, 17, 'Atti di programmazione delle opere pubbliche', '000017-000167', 2, 3, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexActsPublicWorks', 'OpenDataProgrammingActFrontController@indexActsPublicWorks', 'programming-act', '#!', NULL, '2021-11-05 11:08:52', '2021-11-05 11:08:52', '2021-11-05 11:08:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:08:52', NULL, NULL),
  (168, NULL, NULL, 17, 'Tempi costi e indicatori di realizzazione delle opere pubbliche', '000017-000168', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:09:01', '2021-11-05 11:09:01', '2021-11-05 11:09:01', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:09:01', NULL, NULL),
  (174, NULL, NULL, 18, 'Prevenzione della Corruzione', '000018-000174', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:07', '2021-11-05 11:10:07', '2021-11-05 11:10:07', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:07', NULL, NULL),
  (175, NULL, NULL, 18, 'Accesso Civico', '000018-000175', 2, 3, '\\Http\\Web\\Front\\PivotController@civicAccess', NULL, NULL, '#!', NULL, '2021-11-05 11:10:15', '2021-11-05 11:10:15', '2021-11-05 11:10:15', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:15', NULL, NULL),
  (176, NULL, NULL, 18, 'Accessibilità e Catalogo dei dati, metadati e banche dati', '000018-000176', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:24', '2021-11-05 11:10:24', '2021-11-05 11:10:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:24', NULL, NULL),
  (177, NULL, NULL, 18, 'Dati ulteriori', '000018-000177', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:34', '2021-11-05 11:10:34', '2021-11-05 11:10:34', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:34', NULL, NULL),
  (178, NULL, NULL, 18, 'Spese di rappresentanza', '000018-000178', 2, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:42', '2021-11-05 11:10:42', '2021-11-05 11:10:42', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:42', NULL, NULL),
  (179, NULL, NULL, 18, 'Elezioni trasparenti', '000018-000179', 2, 7, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:51', '2021-11-05 11:10:51', '2021-11-05 11:10:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:51', NULL, NULL),
  (180, NULL, NULL, 18, 'Avvisi e News', '000018-000180', 2, 8, '\\Http\\Web\\Front\\NewsNoticeFrontController', 'OpenDataNewsNoticeFrontController@index', 'news-notice', '#!', NULL, '2021-11-05 11:10:59', '2021-11-05 11:10:59', '2021-11-05 11:10:59', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:59', NULL, NULL),
  (181, NULL, NULL, 182, 'Cookie policy', '000019-000182-000181', 2, 9, '\\Http\\Web\\Front\\OtherSectionsFrontController@cookie', NULL, NULL, '#!', NULL, '2021-11-05 11:11:22', '2021-11-05 11:11:22', '2021-11-05 11:11:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:22', NULL, NULL),
  (182, NULL, NULL, 19, 'Privacy', '000019-000182', 2, 1, '\\Http\\Web\\Front\\OtherSectionsFrontController@privacy', NULL, NULL, '#!', NULL, '2021-11-05 11:11:44', '2021-11-05 11:11:44', '2021-11-05 11:11:44', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:44', NULL, NULL),
  (183, NULL, NULL, 0, 'Controlli sulle attività economiche', '000183', 1, 10, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:11:51', '2021-11-05 11:11:51', '2021-11-05 11:11:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:51', NULL, NULL),
  (184, NULL, NULL, 0, 'Pianificazione e governo del territorio', '000184', 1, 19, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:00', '2021-11-05 11:12:00', '2021-11-05 11:12:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:00', NULL, NULL),
  (185, NULL, NULL, 0, 'Informazioni ambientali', '000185', 1, 20, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:08', '2021-11-05 11:12:08', '2021-11-05 11:12:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:08', NULL, NULL),
  (186, NULL, NULL, 0, 'Strutture sanitarie private accreditate', '000186', 1, 21, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:16', '2021-11-05 11:12:16', '2021-11-05 11:12:16', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:16', NULL, NULL),
  (187, NULL, NULL, 0, 'Interventi straordinari e di emergenza', '000187', 1, 22, '\\Http\\Web\\Front\\InterventionFrontController', 'OpenDataInterventionsFrontController@index', 'intervention', '#!', NULL, '2021-11-05 11:12:27', '2021-11-05 11:12:27', '2021-11-05 11:12:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:27', NULL, NULL),
  (233, NULL, NULL, 605, 'RSPP', '000002-000040-000605-000233', 3, 23, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (234, NULL, NULL, 605, 'RPD', '000002-000040-000605-000234', 3, 24, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (235, NULL, NULL, 605, 'RLS', '000002-000040-000605-000235', 3, 25, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (236, NULL, NULL, 605, 'RTD', '000002-000040-000605-000236', 3, 26, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (238, NULL, NULL, 605, 'Sindaco', '000002-000040-000605-000238', 3, 1, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (239, NULL, NULL, 605, 'Vicesindaco', '000002-000040-000605-000239', 3, 2, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (240, NULL, NULL, 605, 'Giunta ed assessori', '000002-000040-000605-000240', 3, 3, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (241, NULL, NULL, 605, 'Consiglio Comunale', '000002-000040-000605-000241', 3, 4, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (242, NULL, NULL, 605, 'Presidente Consiglio Comunale', '000002-000040-000605-000242', 3, 5, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (243, NULL, NULL, 605, 'Direzione Generale', '000002-000040-000605-000243', 3, 6, '\\Http\\Web\\Front\\PersonnelFrontController@indexPoliticPage', 'OpenDataPersonnelFrontController@politicsPage', 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (244, NULL, NULL, 605, 'Gruppi consiliari', '000002-000040-000605-000244', 3, 7, '\\Http\\Web\\Front\\CommissionFrontController@indexGroup', 'OpenDataCommissionsFrontController@indexGroup', 'commission', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (245, NULL, NULL, 605, 'Commissioni', '000002-000040-000605-000245', 3, 8, '\\Http\\Web\\Front\\CommissionFrontController@indexCommission', 'OpenDataCommissionsFrontController@indexCommission', 'commission', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
  (246, NULL, NULL, 37, 'Titolari di incarichi di amministrazione, di direzione o di governo', '000002-000037-000246', 3, 25, '\\Http\\Web\\Front\\PersonnelFrontController@indexHoldersOfAdministrativePositions', 'OpenDataPersonnelFrontController@indexHoldersOfAdministrativePositions', 'personnel', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 1, 0, '2022-03-31 13:59:53', NULL, NULL),
  (247, NULL, NULL, 37, 'Cessati dall\'incarico', '000002-000037-000247', 3, 26, '\\Http\\Web\\Front\\PersonnelFrontController@indexArchivePositionHolders', NULL, NULL, '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:54', NULL, NULL),
  (257, NULL, NULL, 112, 'Avvisi di preinformazione', '000010-000581-000112--000257', 4, 2, '\\Http\\Web\\Front\\ContestsActsFrontController@indexPreinformationNotices', 'OpenDataContestActsFrontController@indexPreinformationNotices', 'contests-act', '#!', NULL, '2021-11-05 10:37:04', '2021-11-05 10:37:04', '2021-11-05 10:37:04', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:37:04', NULL, NULL),
  (297, NULL, NULL, 113, 'Programma triennale degli acquisti di beni e servizi', '000010-000113-000297', 4, 1, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexBiannualProgram', 'OpenDataProgrammingActFrontController@programming', 'programming-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-10-05 11:49:59', NULL, NULL),
  (298, NULL, NULL, 113, 'Programma triennale dei lavori pubblici', '000010-000113-000298', 4, 2, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexBiannualProgram', 'OpenDataProgrammingActFrontController@programming', 'programming-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-10-05 11:51:58', NULL, NULL),
  (303, NULL, NULL, 5, 'Concorsi terminati', '000005-000303', 2, 3, '\\Http\\Web\\Front\\ContestsFrontController@indexTerminated', 'OpenDataContestsFrontController@indexTerminated', NULL, '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-10-05 12:08:05', NULL, NULL),
  (524, NULL, NULL, 112, 'Avvisi e Bandi', '000010-000581-000112-000524', 4, 4, '\\Http\\Web\\Front\\ContestsActsFrontController@indexNoticesAndAdvertisements', 'OpenDataContestActsFrontController@indexNoticesAndAdvertisements', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (525, NULL, NULL, 112, 'Concessioni e partenariato pubblico privato', '000010-000581-000112-000525', 4, 11, '\\Http\\Web\\Front\\ContestsActsFrontController@indexConcessionsAndPartnership', 'OpenDataContestActsFrontController@indexConcessionsAndPartnership', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (526, NULL, NULL, 112, 'Procedure negoziate afferenti agli investimenti pubblici finanziati, in tutto o in parte, con le risorse previste dal PNRR e dal PNC e dai programmi cofinanziati dai fondi strutturali dell\'Unione europea', '000010-000581-000112-000526', 4, 5, '\\Http\\Web\\Front\\ContestsActsFrontController@indexPnrAndPncAndEuropeanFinancing', 'OpenDataContestActsFrontController@indexPnrAndPncAndEuropeanFinancing', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (527, NULL, NULL, 112, 'Fase esecutiva', '000010-000581-000112-000527', 4, 9, '\\Http\\Web\\Front\\NoticeActsFrontController@indexExecutiveStage', NULL, 'notices-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (528, NULL, NULL, 112, 'Delibera a contrarre', '000010-000581-000112-000528', 4, 3, '\\Http\\Web\\Front\\ContestsActsFrontController@indexDeliberation', 'OpenDataContestActsFrontController@indexDeliberation', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (529, NULL, NULL, 112, 'Trasparenza nella partecipazione di portatori di interessi e dibattito pubblico', '000010-000581-000112-000529', 4, 1, '\\Http\\Web\\Front\\NoticeActsFrontController@indexTransparencyOfParticipation', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (530, NULL, NULL, 112, 'Collegi consultivi tecnici', '000010-000581-000112-000530', 4, 8, '\\Http\\Web\\Front\\NoticeActsFrontController@indexTechnicalAdvisoryColleges', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (531, NULL, NULL, 112, 'Progetti di investimento pubblico', '000010-000581-000112-000531', 4, 15, '\\Http\\Web\\Front\\NoticeActsFrontController@indexPublicInvestmentProjects', 'OpenDataNoticesActsFrontController@indexNoticesActs', 'notices-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (532, NULL, NULL, 112, 'Affidamenti diretti di lavori, servizi e forniture di somma urgenza e di protezione civile', '000010-000581-000112-000532', 4, 12, '\\Http\\Web\\Front\\ContestsActsFrontController@indexDirectFoster', 'OpenDataContestActsFrontController@indexDirectFoster', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (533, NULL, NULL, 112, 'Affidamenti in house', '000010-000581-000112-000533', 4, 13, '\\Http\\Web\\Front\\ContestsActsFrontController@indexInHouseContracting', 'OpenDataContestActsFrontController@indexInHouseContracting', 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (534, NULL, NULL, 112, 'Elenchi ufficiali di operatori economici riconosciuti e certificazioni', '000010-000581-000112-000534', 4, 14, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-02-28 10:02:38', NULL, NULL),
  (580, NULL, NULL, 10, 'Atti e documenti di carattere generale riferiti a tutte le procedure', '000010-000580', 2, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (581, NULL, NULL, 10, 'Procedure fino al 31/12/2023', '000010-000581', 2, 5, '\\Http\\Web\\Front\\ContestsActsFrontController@pivot', NULL, 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2024-01-13 12:00:31', NULL, NULL),
  (582, NULL, NULL, 580, 'Avviso finalizzato ad acquisire le manifestazioni di interesse degli operatori economici in ordine ai lavori di possibile completamento di opere incompiute nonché alla gestione delle stesse', '000010-000580-000582', 3, 2, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@uncompletedWorksAlert', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (583, NULL, NULL, 580, 'Comunicazione circa la mancata redazione del programma triennale', '000010-000580-000583', 3, 3, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@triennialProgram', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (584, NULL, NULL, 580, 'Atti recanti norme, criteri oggettivi per il funzionamento del sistema di qualificazione, l’eventuale aggiornamento periodico dello stesso e durata, criteri soggettivi (requisiti relativi alle capacità economiche, finanziarie, tecniche e professionali) per l’iscrizione al sistema', '000010-000580-000584', 3, 4, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@qualificationSystemActs', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (585, NULL, NULL, 580, 'Atti eventualmente adottati recanti l\'elencazione delle condotte che costituiscono gravi illeciti professionali agli effetti degli artt. 95, co. 1, lettera e) e 98 (cause di esclusione dalla gara per gravi illeciti professionali)', '000010-000580-000585', 3, 5, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@seriousProfessionalMisconductActs', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (586, NULL, NULL, 580, 'Elenco annuale dei progetti di investimento pubblico finanziati', '000010-000580-000586', 3, 6, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@annualListFundedProjects', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (587, NULL, NULL, 580, 'Elenco delle soluzioni tecnologiche adottate dalle SA e enti concedenti per l\'automatizzazione delle proprie attività', '000010-000580-000587', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (588, NULL, NULL, 10, 'Avvisi', '000010-000588', 2, 3, '\\Http\\Web\\Front\\BdncpProcedureFrontController@alerts', 'OpenDataBdncpProcedureFrontController@alerts', 'bdncp-procedure', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (603, NULL, NULL, 10, 'Varianti in corso d\'opera', '000010-000603', 2, 4, '\\Http\\Web\\Front\\BdncpProcedureFrontController@variations', 'OpenDataBdncpProcedureFrontController@variations', 'bdncp-procedure', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
  (605, NULL, NULL, 40, 'Organi politici, organi di amministrazione e gestione', '000002-000040-000605', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'politic-organ', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:57:11', NULL, NULL),
  (606, NULL, NULL, 144, 'Relazioni degli organi di revisione amministrativa e contabile al bilancio di previsione o budget e alle relative variazioni', '000014-000144-000606', 3, 2, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexBudgetForecastReports', 'OpenDataReliefChecksController@indexBudgetForecastReports', 'relief-check', '#!', NULL, '2021-11-05 10:48:43', '2021-11-05 10:48:43', '2021-11-05 10:48:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:43', NULL, NULL),
  (607, NULL, NULL, 144, 'Relazioni degli organi di revisione amministrativa e contabile al conto consuntivo o al bilancio di esercizio', '000014-000144-000607', 3, 2, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexFinancialStatementReports', 'OpenDataReliefChecksController@indexFinancialStatementReports', 'relief-check', '#!', NULL, '2021-11-05 10:48:43', '2021-11-05 10:48:43', '2021-11-05 10:48:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:43', NULL, NULL),
  (608, NULL, NULL, 145, 'Rilievi della Corte dei conti riguardanti l\'organizzazione e l\'attività delle amministrazioni stesse e dei loro uffici', '000014-000145-000608', 3, 2, '\\Http\\Web\\Front\\ReliefChecksFrontController@indexReviewOrganizationsOrganisms', 'OpenDataReliefChecksController@indexReviewOrganizationsOrganisms', 'relief-check', '#!', NULL, '2021-11-05 10:48:43', '2021-11-05 10:48:43', '2021-11-05 10:48:43', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:48:43', NULL, NULL);



-- Dump della struttura di tabella pat.section_fo_config_publication_archive
CREATE TABLE IF NOT EXISTS `section_fo_config_publication_archive` (
  `id` int NOT NULL DEFAULT '0',
  `section_fo_id` int NOT NULL,
  `archive_name` varchar(60) NOT NULL,
  `deleted` int DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella utilizzata per gestire il pubblica in';


-- Dump della struttura di tabella pat.section_fo_politic_organ
CREATE TABLE IF NOT EXISTS `section_fo_politic_organ` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL,
  `o_id` int DEFAULT NULL,
  `object_structures_id` int DEFAULT NULL,
  `section_fo_id` int NOT NULL,
  `denomination` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `skills` text COLLATE utf8mb4_unicode_ci,
  `absence_office_reason` varchar(800) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `show_personnel` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella oggetto_pagamenti su vecchio PAT.';


-- Dump della struttura di tabella pat.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) NOT NULL,
  `data` text,
  `expire` int DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dump della struttura di tabella pat.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `institution_id` int NOT NULL COMMENT 'campo ex PAt:  id_ente_admin',
  `name` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(191) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `spid_code` varchar(45) DEFAULT NULL COMMENT 'campo ex PAt:  codicespid',
  `fiscal_code` varchar(45) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1' COMMENT 'campo ex PAt:  attivo',
  `active_key` varchar(20) DEFAULT NULL COMMENT 'Campo utilizzato per attivazione utente',
  `deleted` tinyint DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `super_admin` tinyint(1) DEFAULT '0' COMMENT 'campo ex PAt:  permessi',
  `admin` tinyint(1) DEFAULT '0',
  `prevent_password_repetition` tinyint(1) DEFAULT '0' COMMENT 'campo impedisci_ripetizioni_password su vecchio PAT.',
  `prevent_password_repetition_6_months` tinyint(1) DEFAULT '0',
  `password_expiration_days` varchar(191) DEFAULT NULL COMMENT 'campo scadenza_password_giorni su vecchio PAT.',
  `refresh_password` datetime DEFAULT NULL,
  `prevent_password_change_day` tinyint DEFAULT NULL COMMENT 'campo impedisci_cambio_pwd_giorno su vecchio PAT.',
  `force_change_password` int NOT NULL DEFAULT '0' COMMENT 'Se impostata ad uno, forza il cambio della password all''utente',
  `deactivate_account_no_use` int DEFAULT '0',
  `filter_owner_record` varchar(2) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `registration_type` varchar(45) DEFAULT NULL,
  `profile_image` varchar(64) DEFAULT NULL,
  `editor_wishing` varchar(20) DEFAULT 'inheritance',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Tabella Utenti:in relazione con la tabella Profili (tabella rel_users_profiles) e con la tabella Enti(institutions).';

-- Dump dei dati della tabella pat.users: ~2 rows (circa)
INSERT INTO `users` (`id`, `institution_id`, `name`, `username`, `password`, `email`, `phone`, `spid_code`, `fiscal_code`, `active`, `active_key`, `deleted`, `last_visit`, `registration_date`, `super_admin`, `admin`, `prevent_password_repetition`, `prevent_password_repetition_6_months`, `password_expiration_days`, `refresh_password`, `prevent_password_change_day`, `force_change_password`, `deactivate_account_no_use`, `filter_owner_record`, `notes`, `registration_type`, `profile_image`, `editor_wishing`, `created_at`, `updated_at`, `deleted_at`) VALUES
  (1, 1, 'XphT+ixZEDkM', 'XphT+ixZEDkM', 'sha256:64000:18:nLIXAsmlCVzzcxTWhOhbBV+11krdWhno:GahMP3pkWn/6MlsJvNH3L1ac', 'XphT+mNcGT0LH06W5wVUStK+rGK9bA==', NULL, NULL, NULL, 1, '', 0, '2026-10-01 11:06:16', '2021-10-08 14:25:06', 0, 0, 2, 0, '365', NULL, 2, 0, 365, '2', NULL, '', '', 'inheritance', '2021-10-08 14:25:06', '2026-09-23 14:59:17', NULL);
