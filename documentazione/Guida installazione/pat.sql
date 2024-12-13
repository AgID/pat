-- Dump della struttura del database pat
CREATE DATABASE IF NOT EXISTS `pat`;
USE `pat`;

-- Dump della struttura di tabella pat.acl_profiles
CREATE TABLE IF NOT EXISTS `acl_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `is_admin` int(11) NOT NULL DEFAULT '0',
  `is_system` tinyint(4) NOT NULL,
  `versioning` int(11) NOT NULL COMMENT 'Colonna preposta per il ripristino della gesrtione delle versioni dei records',
  `lock_user` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Campo per il permesso di bloccare un utente',
  `advanced` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Campo per il permesso avanzato di modifica del profilo',
  `export_csv` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Campo per il permesso di esportazione di csv',
  `editor_wishing` varchar(20) DEFAULT 'base',
  `file_archive` int(11) DEFAULT '1' COMMENT 'Campo per il permesso di visualizzazione del file manager',
  `archiving` tinyint(4) DEFAULT '0' COMMENT 'Campo per il permesso di archiviazione dei record',
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella Profili: in relazione con la tabella Utenti e con le tabelle sections_fo e sections_bo tramite la tabella permessi(Permits).';

-- Dump dei dati della tabella pat.acl_profiles: ~1 rows (circa)
INSERT INTO `acl_profiles` (`id`, `institution_id`, `is_admin`, `is_system`, `versioning`, `lock_user`, `advanced`, `export_csv`, `editor_wishing`, `file_archive`, `archiving`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 0, 0, 0, 0, 1, 'base', 3, 0, 'Default -  Amministratore completo dei contenuti', 'Default -  Amministratore completo dei contenuti', '2022-04-06 15:41:16', '2023-11-21 12:22:34');

-- Dump della struttura di tabella pat.activity_log
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `is_superadmin` int(11) NOT NULL DEFAULT '0',
  `institution_id` int(11) DEFAULT NULL,
  `object_id` int(11) DEFAULT NULL COMMENT 'Id dell''archivio di riferimento',
  `record_id` int(11) DEFAULT NULL COMMENT 'Id dell''istanza su cui è stata svolta l''azione',
  `area` varchar(50) DEFAULT NULL COMMENT 'Area dell''attività, serve per indicare se l''azione è stata eseguita su un oggetto, su un contenuto(pagina) oppure è un azione di autenticazione',
  `client_info` text,
  `ip_address` varchar(191) DEFAULT NULL,
  `action` varchar(191) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL COMMENT 'Identifica il tipo dell''azione eseguita',
  `description` text,
  `platform` varchar(50) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `referer` varchar(191) DEFAULT NULL,
  `request_post` text,
  `request_get` text,
  `request_file` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- Dump dei dati della tabella pat.activity_log: ~32 rows (circa)
INSERT INTO `activity_log` (`id`, `user_id`, `is_superadmin`, `institution_id`, `object_id`, `record_id`, `area`, `client_info`, `ip_address`, `action`, `action_type`, `description`, `platform`, `uri`, `referer`, `request_post`, `request_get`, `request_file`, `created_at`, `updated_at`) VALUES
	(1, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 28-11-2023 alle ore 18:02:42.', 'pat', 'http://pat.local/auth.html?t&equals;fea5ae7814300724a33b3be845a98f7f', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-28 18:02:42', '2023-11-28 18:02:42'),
	(2, 1, 0, 1, NULL, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Modifica profilo utente', NULL, 'Modifica profilo utente con ID (1)', 'pat', 'http://pat.local/admin/profile/update/password.html', 'http://pat.local/admin/profile/force/password.html', 'eNqdF2lzokj7+/4K1nq3anZrwiHgudlaxFZJFAhgNFtWUQitMh4wgJpka/77+zTQSjKpvZJomn7u+8HriJ0/047UqcVRmtW6XqdJntudWop9N4u2+FDrApzv1MRg2RLEVrAMZMlv4SBoCo065ttSXZSkoBms+HZ72Wpggt8Cfl6anqMkII+C3KnJiTaUnnp/CKPhl5fsx/xa6NQS7P4DTInoc8gxQJMcUezU3DSLEm+N3ewlzsU2OrVjHHhZ/lDv1MKCZ6cm5P/JzSHNwuyYYbcK+5aCG2prnDuA7/z5LWeV4uSEE3LVqBOnEN0s1NcspDqu7SjO1CYsgLLO8+QEXhs5jumODNspn2MvY3eR7+2ocTmCaug6MNEMPb8Gk7YYxzfeLjzlqgtgL+A4SHfcMdKHzqgU1Gzz1HE5I0VVkemUwF+4X3K7KXDuWuhhimwH9d2ZVvAgnOeT8SjLYgt/PWIS84piUxtZrjIEuYUYYDWJXsPdzuNklmc+zcJDEJ1TRncYgWf5LgMXDanLPDeknxkljnd4hpf3YcbJYpMVG8yn+5EzGX9mduEWM0Psb6OfGXWTRHvMCUKb5ckvY3srLwlLEhooar7zZKI8tJBS++MuC2MvybhVlOxvINBel1lGx0PgJS+3N/BTSB8AtFdem2fxdSa+YFlsaadnVX7jPsPShloRA4j3BpzS4bi3IauXqBYaIAtZ5E7mv8flvGAfHrg4iVbhDhP9fMzRxGY32T7nVuffxM1Fumr0NX1IE3r9GsafmQCvdjSH3xGMFX04hfjkEYeghdmN5nwOs+7XW55tf8aHm6mdn1twzg/NNwarhnGvoSIRgNwcmTayba1/y5+asiekm8ZR9Ph6IAjp8riW8eYkdxkw0o1S14tj95jsbonpP4nKT/UB/J3PZ/bihHeo4VvUj9E20HZu37gcMtRUimyVJIi62lmYL9kmOoiCsLD9JIyzdNF9c0ueZpreN2b2In1JM7wX65W7KtguwYvZEu8/BhQ5bkZnnNgbvNstTgLLLz7ENWJ8sO1RoU4SrRNvzwwgAdLFMMwW/j4ggLO3jxvSAZ8Xy/CwiDcx+bRYYPo9mRMlWRSm2H7UCfZfkgtihb4PxbBQoz20cVAbZ8eY0lcR/E3kRyS5Xt5BS/GHKMBf0g/9ufCjwypcl89loi+g5nPGYxK9xST0kyiNVhl1IoBT2uIKp1lRlNGbqxRy0yRFP7FNpOaZ36zCK1rsAxY/45KAJAqa580KenSNBQZdFs1Rl+0pTpdVJ/0u+9izyRfc3dnkA4eZPSBfoy47sdUuaz6Rz6ycIESkZn2sI2kT0CIfoUva2lCHEWAV1cRDhf3qBUGC0/Q3Jfb8DebqrMTKUt40SXuEYuOKsDH7KHBX/joMAEdkBf4mwCfGzscN42XMpRwYE9KBafG/cpT1D7RdUy2MgTNTCiVk6CD/STRtESVPXZmgD8fXFUXp961y6nQ6wjugaVhOOYBbl3FloYnhoI8I4dg31OmEdHvLMArSvO65S+pz0Gc4CLZh31ja1DboKCsHnGurI1QoLRW9+c0UnTuuCf1bm1f2BzIlKfC/ia9XvDEppkgdXEaIis3h9+uRDQ8nmO6XFcdWLQ3a+UCD6V56W/wbmRxUFH5mofbfuZS6G9g2JKEpU+Uuq8rUGtN58W5MFcvS93NKgNIaKg6aKU+uBj6yBoqKaI0ONQ4y6V0empbhGKoxLpc/MmwuWJU4TZAzMvq0y5drElEWwNYTLFVWOQ3plidcaaeW9m/NEC6epl4mJf3Wk618DLo2Gg8+RhBaVx0cbYLcwdhQHGq/0ORhk4H6b7K8LLSuzr8SULZX1LLT9Htk4Sr9ClPfg/3mUhK9d1siyNq/pF93JTrAK2tmXqObqNzmhBzcVxylpxT8iUpQy0l4TCN3HVbRyMpHvQNBSWh/LqCmYtszw+pXYgLiHOMe6e7drNg8yYqEUHsw3iqqtfUHx6Ez/N9zPL/Xx9vWbIWSOqZJVRDmO4ehu/foiTJIRHv/MBZm9893o412vsse/OlAfm6379fW6FO5IbUogwFJE8h8+HdRoZdojXXgGLwkHu/UYFt//DJ48mfWeRZnh+NpWsZamTqji2Bi493BG76OenfRl34zebH74vB0//AqJoZ5fm3sqdyJoo1dewIrVDXTyWvJ/tpsrkg0NHUSNS/cQf1nODngLI12x9cwOoRseAl1TlYNA+EF0YrS38P0jJfvUasxISk4F/UHCct948kZ+uGPdE5dtVGtJ9MxyrbrjO0yigWzsnsARG41qbmwmBIaEqQyk0pzs+R4eUOpIFUiubL9+mmUzHvByI+1bTtQrf2RXz6KZylST8EdNaVCrT1SnfUUrXgn7t/Nlq15/DAtC0XVzFGxe5O0VJB9U5cbN6pjlUElVfaHoSOKgY5JFGPOgvcMeLn79n8u+HmE', NULL, NULL, '2023-11-28 18:03:37', '2023-11-28 18:03:37'),
	(3, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 28-11-2023 alle ore 18:12:53.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/profile.html', NULL, NULL, NULL, '2023-11-28 18:12:53', '2023-11-28 18:12:53'),
	(4, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 10:09:01.', 'pat', 'http://pat.local/auth.html?t&equals;ddd4564de4edf22dade1177da0f3f01a', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 10:09:01', '2023-11-29 10:09:01'),
	(5, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 10:09:16.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/absence-rates.html', NULL, NULL, NULL, '2023-11-29 10:09:16', '2023-11-29 10:09:16'),
	(6, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:18:46.', 'pat', 'http://pat.local/auth.html?t&equals;9974065b9016485a563fbe225d201e64', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:18:46', '2023-11-29 14:18:46'),
	(7, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:18:53.', 'pat', 'http://pat.local/auth.html?t&equals;6f46b107397015eab3253db8cad25419', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:18:53', '2023-11-29 14:18:53'),
	(8, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:19:30.', 'pat', 'http://pat.local/auth.html?t&equals;29fef8fe0f10689c55b8a39704499c15', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:19:30', '2023-11-29 14:19:30'),
	(9, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 14:20:04.', 'pat', 'http://pat.local/auth.html?t&equals;ffe12ebde490de0601af6ee12b8865c9', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:20:04', '2023-11-29 14:20:04'),
	(10, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 14:20:07.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/dashboard.html', NULL, NULL, NULL, '2023-11-29 14:20:07', '2023-11-29 14:20:07'),
	(11, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:23:43.', 'pat', 'http://pat.local/auth.html?t&equals;252a23b16be36ae230e29b884534bfe4', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:23:43', '2023-11-29 14:23:43'),
	(12, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:24:42.', 'pat', 'http://pat.local/auth.html?t&equals;74caf95bbe73475bd43ab38223a3f6a6', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:24:42', '2023-11-29 14:24:42'),
	(13, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:28:48.', 'pat', 'http://pat.local/auth.html?t&equals;2f94c8722873b286b8938e3d8e1b9ca4', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:28:48', '2023-11-29 14:28:48'),
	(14, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 14:31:41.', 'pat', 'http://pat.local/auth.html?t&equals;f40e108df746594d0f49e1ee5fcfa2dc', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:31:41', '2023-11-29 14:31:41'),
	(15, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 14:34:17.', 'pat', 'http://pat.local/auth.html?t&equals;5882dbf5475230d1e7e0310fdf0777b8', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:34:17', '2023-11-29 14:34:17'),
	(16, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 14:42:47.', 'pat', 'http://pat.local/auth.html?t&equals;2d2bab6f4a24bc9b5e552317764bf511', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:42:47', '2023-11-29 14:42:47'),
	(17, 1, 0, 1, 54, 1, 'user', 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Modifica profilo utente', 'updateUserInstance', 'Modifica profilo utente con ID (1)', 'pat', 'http://pat.local/admin/profile/update.html', 'http://pat.local/admin/profile.html', 'eNqdV3tzosgW//9+CsqqrdrdGlFUwMfm1hBEZaLiBRyTLasolOahQpMG89it+e73NNAGM9ndqU2iafr8+pzT54077A7/zIa9YSPFWd4YuUOhRzcGw0aG9k6OjyhpjADQHjYGqN1FA1cWBjup25FcedDdi7uBuBPFgSyIg327s5O93q7ADxuJGyO6BFYeirHrxVHBqj9snDNE/oaculn2jIlHHwV52PC/3O0Oj4N03j1a2lmZfC4IwrBBkPNDWHHYQLEbnei60ykF8oXEz+jFjdMT4vc4rqQD0x8EA9s0xElxDbBPoUIX9gj2oxNyotgN6rQetWnivUM7WY4JAJ38NS3QEtgn9dy8eAD5UXm3YUMo/sOOU0nIdC97T4uSLI/ycw7ia+e+ZeDnRoAKD7eHf34rxIAXnhChW5JEnS7AhUxtrJuaajuWrdhri7KAk512u/LVzLZXzsyw7Oo5dXP+hPduYS7KoACoxnIJTHRjWWzDdY8IpU33FD0V14IgawDG1pa2M9eWU3tWGUgQOwLzbsFJUVVtZVdq/Nr6tTAKI947pva/tWbZ2tjZ6CUTyvp+MZ/leWqixzOiUV3TbG1ppqNMQXApBlgt8B/R6eS2RL7N/byJEg8/Z9zS5oQ23x5xsCH1RtyL1PuFU1Lw/gbt7qK8JXZlvitxP9/N7MX8E3eKjoibov0R/8KpIcExagnCgG/TX85yfZdE1RHmKXZ/+2GlFX6H2IvPpzxKXZK3fEziJkSBO+J2+Jx4Lnm9acJPKX0C1NtqGwm/3+0CSfZug9WDtBCvzGeY+lQvnQAOD8Eow1br2medCmpqE83UzMLU4vfYVpECrSr0+DCPywxpXznK0ZaqMdaXUxbewR9R+onzkH9iEf3uwFxZTtfgECY3ypu6/SnKR483bX7wCSXNtVWs+7AuFvLVDVXDuNO10vNiEY8Ozhw3TZ0zOd3QW/zUVX7qTODv+fmZv9xnxF1Bo2vox7AQauRNjbSarSzNsvTxzT70kscAp3EYHw6ngZv2jwe51wEHVYG9UqoY74Gb1eF29ZpD6egKwtbakyjNs+3oapc+bfTl2NhY2+w1y1Hc7dT26mSrIm83OxR/TCiDeoWfEbFCdDptnwS+vf0Qa6QosaxZqQ7BAXFjbkKLzXYa5dt97FHCM9RBqZeg5+0uSrZpmNJPnwem3x+zMclxlCHr65Ki//a40K2dH0P0b1UcQ2cCtVF+Ttn5OmAf4j2mwfX6jlqJT7CHDtmH9tzuceJHQfVcRfYWkrxgPKc+3i6iPcEZ9nNmRCBnrKiVRjMxztnOmxS6I9MsX1grTS0iX67Ta1rEHo9eUHWABop2X1QnCVKTBwYjXrvXRvytYo94dTEe8V9vLfoFe18s+oHFxprQr9mIX1jqiF890M+m6idUpG5+rCOtC1ATv0JZtPTpEoq+WWZTGzLsN9fzCMqy/yqpuw9Rq8P3eLFXVElaDyH+W6XbuBh7jr8PIg8wXV5oNz30xFlFg+HcnLskDbeCcOD67d9ajPV/WH1mWhgTe6OUSohQQf6VaFYiKp5LZaF92LDeIMp4bFZtZjgU3hFXhmlX7bjfZjRTWxi29tFBWI4Ndb2g5d00jPJokfetS+i3oBq1wNmG1TT1tWWw3lV1NMdSZ1qpdK8sxld98952VlCw9fvaNEHbIiP+O/GdmjUWZdvogMnooXJW+Py25KPkCfq5d/GeaupQzic69PPK2t1/kNmCjEIvPOT+O5MycwNbURTaMlPuMpyszXmhHMTudV9qlaPTpT3RkXCq2NpGeXB0MI05UVSNpeZUb0EAvQu/lWnYhmrMq2mQ9pgLquaehWbPjDEr7tU8RHUEsvkA05NZNUE26glvZ9em/oPaCxe7MpvSBL62GygJyeBY2nzyMUDov4m29YXmTOaGYrNrC3Jb6EiiKPf4riAP3kz9doCxfYNWdWV8S+epypzQ410YXy4JcPtuCgRZ8Wv2eKrgQK+NkUVGhrga1oSCPFZs5VYp+VOVIHNJdM6wE0R1GJ3omHXAF4RV45K6UixrY5jjmitAnG3caUvny6ZMDdD2EHo7PzoT38sCifjnICTHQxKyECrxRa83ls6d9lCEN+jkHxLobSGOAr8v92US+GFEwsCPltNAes0Js3/JYEKDAsIb/tmMw873olCW+rIU+iQIfTc8BIh4L/vM83qI5JWLlbU9qwseT6YzkeRJEOHjOQ/9CPvm1J4M+vLA94NQYoIXij53rAUMSvXApq8i8VtJeQMxl9CuQBH8u1ce6tkC/VdWZ/S63et0qS5NNR9WtlEVT3tuVd4pGVQ1AChiv6gAHeADteV2rjkFYmnY+kRXlVqA+e4pu0yZ34GcUhbLSLGCqGvLNhbOwpqyKooIwQRx2RleC7jY3e/DKCkGOWFwfcZa336BCGcRuoJO1+TK08wBMBDTW1LxVUxXJsnJ+fIuVAMxH9N3Ju+Q+Dg6wOdIkHfwnxJMkicfn0iCn5i5a2f1r8zGhyDxjwjCMYHvRMyrhFX11awc8Wl6KJrV7IhSU7XNKspotv9uLDWG0M4Ep6hlYvqu/u3b/wFgh+Qu', NULL, NULL, '2023-11-29 14:46:14', '2023-11-29 14:46:14'),
	(18, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 14:47:03.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/dashboard.html', NULL, NULL, NULL, '2023-11-29 14:47:03', '2023-11-29 14:47:03'),
	(19, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 14:47:13.', 'pat', 'http://pat.local/auth.html?t&equals;1ffb5b89c2e4687ab7c80e92743e1723', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 14:47:13', '2023-11-29 14:47:13'),
	(20, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 14:47:16.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/dashboard.html', NULL, NULL, NULL, '2023-11-29 14:47:16', '2023-11-29 14:47:16'),
	(21, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 29-11-2023 alle ore 16:38:32.', 'pat', 'http://pat.local/auth.html?t&equals;a8c8946b69980b926e16cfafd1826999', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 16:38:32', '2023-11-29 16:38:32'),
	(22, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 16:38:52.', 'pat', 'http://pat.local/auth.html?t&equals;096c24e4dd5ad2ffab52ab4500491e7b', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 16:38:52', '2023-11-29 16:38:52'),
	(23, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 16:38:56.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/dashboard.html', NULL, NULL, NULL, '2023-11-29 16:38:56', '2023-11-29 16:38:56'),
	(24, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 16:40:21.', 'pat', 'http://pat.local/auth.html?t&equals;272c17a3e48df8356d71e616e47a4469', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 16:40:21', '2023-11-29 16:40:21'),
	(25, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 16:40:25.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/profile/force/password.html', NULL, NULL, NULL, '2023-11-29 16:40:25', '2023-11-29 16:40:25'),
	(26, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 29-11-2023 alle ore 17:52:08.', 'pat', 'http://pat.local/auth.html?t&equals;5a42da8d21dc148038c975f7b19b4a62', 'http://pat.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2023-11-29 17:52:08', '2023-11-29 17:52:08'),
	(27, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0tNQzAEFNoGB4Zl5KfnmxhiGIr2QNAHvLDgs=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 29-11-2023 alle ore 17:52:11.', 'pat', 'http://pat.local/logout.html', 'http://pat.local/admin/profile/force/password.html', NULL, NULL, NULL, '2023-11-29 17:52:11', '2023-11-29 17:52:11'),
	(28, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0MtUzAEFNoGB4Zl5KfnmxhiGIr2QNAHt+Dgg=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 03-06-2024 alle ore 16:27:24.', 'pat', 'http://patos-riuso.local/auth.html?t&equals;e3041a7a20ce47decb7e85761fe2250d', 'http://patos-riuso.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2024-06-03 16:27:24', '2024-06-03 16:27:24'),
	(29, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0MtUzAEFNoGB4Zl5KfnmxhiGIr2QNAHt+Dgg=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "deomoadmin" fallito nel giorno 03-06-2024 alle ore 16:27:50.', 'pat', 'http://patos-riuso.local/auth.html?t&equals;ae43daa624af11c1ca56f74ed57d6b65', 'http://patos-riuso.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62MjSwUkpJzc/NT0zJzcxTsq4FAPptDOE=', NULL, NULL, '2024-06-03 16:27:50', '2024-06-03 16:27:50'),
	(30, NULL, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0MtUzAEFNoGB4Zl5KfnmxhiGIr2QNAHt+Dgg=', NULL, 'Accesso utente - fallito', NULL, 'Accesso utente "demoadmin" fallito nel giorno 03-06-2024 alle ore 16:28:01.', 'pat', 'http://patos-riuso.local/auth.html?t&equals;f034f773d2873e479bf4af060ff6c420', 'http://patos-riuso.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2024-06-03 16:28:01', '2024-06-03 16:28:01'),
	(31, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0MtUzAEFNoGB4Zl5KfnmxhiGIr2QNAHt+Dgg=', NULL, 'Accesso utente - riuscito', NULL, 'Accesso utente "demoadmin" riuscito nel giorno 03-06-2024 alle ore 16:29:33.', 'pat', 'http://patos-riuso.local/auth.html?t&equals;80ee55fe403f88c43331bab920f64315', 'http://patos-riuso.local/auth.html', 'eNpLtDK0qi62srBSKi1OLcpLzE1Vsi62srRSSknNzU9Myc3MU7KuBQDjPwxK', NULL, NULL, '2024-06-03 16:29:33', '2024-06-03 16:29:33'),
	(32, 1, 0, 1, 54, NULL, NULL, 'eNortjIxtlIKT03yzixRqFFwzijKz01V0DA0MtUzAEFNoGB4Zl5KfnmxhiGIr2QNAHt+Dgg=', NULL, 'Logout utente', NULL, 'Logout "demo.admin@example.com" nel giorno 03-06-2024 alle ore 16:29:37.', 'pat', 'http://patos-riuso.local/logout.html', 'http://patos-riuso.local/admin/profile/force/password.html', NULL, NULL, NULL, '2024-06-03 16:29:37', '2024-06-03 16:29:37');

-- Dump della struttura di tabella pat.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `archive_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_id` int(11) DEFAULT NULL,
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
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella per gestire gli allegati.';

-- Dump dei dati della tabella pat.attachments: 0 rows
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;

-- Dump della struttura di tabella pat.attachment_label
CREATE TABLE IF NOT EXISTS `attachment_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attachment_label_id_uindex` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Nome allegati';

-- Dump dei dati della tabella pat.attachment_label: ~28 rows (circa)
INSERT INTO `attachment_label` (`id`, `name`, `deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Curriculum', 0, '2022-06-01 12:11:51', NULL, NULL),
	(2, 'Atto di nomina o proclamazione', 0, '2022-06-01 12:18:44', NULL, NULL),
	(3, 'Dati relativi all\'assunzione di altre cariche, presso enti pubblici o privati', 0, '2022-06-01 12:19:02', NULL, NULL),
	(4, 'Ultima dichiarazione dei redditi', 0, '2022-06-01 12:19:13', NULL, NULL),
	(5, 'Dichiarazione dei redditi anni precedenti', 0, '2022-06-01 12:19:24', NULL, NULL),
	(6, 'Dati patrimoniali', 0, '2022-06-01 12:19:36', NULL, NULL),
	(7, 'Dati patrimoniali anni precedenti', 0, '2022-06-01 12:19:50', NULL, NULL),
	(8, 'Note e dichiarazioni Art. 14', 0, '2022-06-01 12:20:09', NULL, NULL),
	(9, 'Dichiarazione insussistenza cause inconferibilità', 0, '2022-06-01 12:20:20', NULL, NULL),
	(10, 'Diritti reali', 0, '2022-06-01 12:20:44', NULL, NULL),
	(11, 'Risultati di bilancio', 0, '2022-06-01 12:31:14', NULL, NULL),
	(12, 'Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell\'incarico  ', 0, '2022-06-01 12:31:30', NULL, NULL),
	(13, 'Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell\'incarico', 0, '2022-06-01 12:31:42', NULL, NULL),
	(14, 'Atto di concessione', 0, '2022-06-01 14:35:15', NULL, NULL),
	(15, 'Progetto selezionato', 0, '2022-06-01 14:35:26', NULL, NULL),
	(16, 'Curriculum del soggetto incaricato', 0, '2022-06-01 14:35:35', NULL, NULL),
	(17, 'Atto di conferimento', 0, '2022-06-01 14:37:28', NULL, NULL),
	(18, 'Curriculum del soggetto incaricato', 0, '2022-06-01 14:37:41', NULL, NULL),
	(19, 'Progetto selezionato', 0, '2022-06-01 14:37:53', NULL, NULL),
	(20, 'Attestazione della verifica sul conflitto d\'interessi', 0, '2022-06-01 14:38:04', NULL, NULL),
	(21, 'Informazioni per Art. 15, c. 1, lett. c), d.lgs. n. 33/2013', 0, '2022-06-01 14:38:20', NULL, NULL),
	(22, 'Testo del provvedimento', 0, '2022-06-01 14:39:37', NULL, NULL),
	(23, 'Fase esecutiva', 0, '2022-06-01 14:42:38', NULL, NULL),
	(24, 'Verbali delle commissioni di gara', 0, '2023-03-03 17:02:28', NULL, NULL),
	(25, 'Copia dell\'ultimo rapporto sulla situazione del personale maschile e femminile', 0, NULL, NULL, NULL),
	(26, 'Relazione di genere sulla situazione del personale maschile e femminile consegnata', 0, NULL, NULL, NULL),
	(27, 'Certificazione di cui all\'articolo 17 della legge 12 marzo 1999, n. 68', 0, NULL, NULL, NULL),
	(28, 'Relazione relativa all\'assolvimento degli obblighi di cui alla legge 68/1999 e alle eventuali sanzioni e provvedimenti disposti a carico dell\'operatore economico nel triennio antecedente la data di scadenza di presentazione delle offerte', 0, NULL, NULL, NULL);

-- Dump della struttura di tabella pat.attempts
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(101) DEFAULT NULL,
  `client_info` varchar(225) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.attempts: ~0 rows (circa)

-- Dump della struttura di tabella pat.concurrent_sess
CREATE TABLE IF NOT EXISTS `concurrent_sess` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `platform` text,
  `browser` text,
  `device` text,
  `ip` text,
  `browser_private_mode` int(11) DEFAULT NULL,
  `sess_id` varchar(255) DEFAULT NULL,
  `created_at` text,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.concurrent_sess: ~10 rows (circa)
INSERT INTO `concurrent_sess` (`id`, `institution_id`, `user_id`, `platform`, `browser`, `device`, `ip`, `browser_private_mode`, `sess_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '58405765e562e95461428bdc68b4c9f0', '2023-11-28 18:02:42', '2023-11-28 18:02:42', NULL),
	(2, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '036ec959ec6be774779da131d5b7c474', '2023-11-29 10:09:01', '2023-11-29 10:09:01', NULL),
	(3, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '64b4cc7e1f056b9ca27c4cea2c9acd39', '2023-11-29 14:20:04', '2023-11-29 14:20:04', NULL),
	(4, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '2f930dfdc66549c6a58cbec8439ea7ad', '2023-11-29 14:34:17', '2023-11-29 14:34:17', NULL),
	(5, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, 'fb15373075fabbfa070f52d3d0a1c4e1', '2023-11-29 14:42:47', '2023-11-29 14:42:47', NULL),
	(6, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '91a57f7ff1a96bbca2249bb80d071f80', '2023-11-29 14:47:13', '2023-11-29 14:47:13', NULL),
	(7, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '4bff39cd9531a1d9d0461ebc33eeddea', '2023-11-29 16:38:52', '2023-11-29 16:38:52', NULL),
	(8, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '80c08b80c0fc898167f09d59dfda16ee', '2023-11-29 16:40:21', '2023-11-29 16:40:21', NULL),
	(9, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '1d4e6388cd384f4a80798be702754f10', '2023-11-29 17:52:08', '2023-11-29 17:52:08', NULL),
	(10, 1, 1, 'Windows', 'Chrome', 'WebKit', '::1', 1, '627cda1d93565082f410598939f52f92', '2024-06-03 16:29:33', '2024-06-03 16:29:33', NULL);

-- Dump della struttura di tabella pat.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) DEFAULT NULL,
  `section_fo_id` int(11) NOT NULL,
  `section_fo_parent_id` int(11) DEFAULT NULL,
  `institution_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(500) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `o_id` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  `last_update_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.content_section_fo: ~0 rows (circa)

-- Dump della struttura di tabella pat.contraent_choice
CREATE TABLE IF NOT EXISTS `contraent_choice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(2) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.contraent_choice: 29 rows
/*!40000 ALTER TABLE `contraent_choice` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `contraent_choice` ENABLE KEYS */;

-- Dump della struttura di tabella pat.cpv_codes
CREATE TABLE IF NOT EXISTS `cpv_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- Dump dei dati della tabella pat.cpv_codes: ~0 rows (circa)

-- Dump della struttura di tabella pat.data_historical_personnel
CREATE TABLE IF NOT EXISTS `data_historical_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personnel_id` int(11) DEFAULT '0',
  `historical_role` varchar(255) DEFAULT NULL,
  `historical_structure` varchar(255) DEFAULT NULL,
  `historical_from_date` datetime DEFAULT NULL,
  `historical_to_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Dati dello storico incarichi del personale';

-- Dump dei dati della tabella pat.data_historical_personnel: ~0 rows (circa)

-- Dump della struttura di tabella pat.data_monitoring_proceedings
CREATE TABLE IF NOT EXISTS `data_monitoring_proceedings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proceeding_id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `year_concluded_proceedings` int(11) DEFAULT NULL,
  `percentage_year_concluded_proceedings` int(11) DEFAULT NULL,
  `conclusion_days` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='dati monitoraggio procedimenti';

-- Dump dei dati della tabella pat.data_monitoring_proceedings: ~0 rows (circa)

-- Dump della struttura di tabella pat.institutions
CREATE TABLE IF NOT EXISTS `institutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_creator` int(11) NOT NULL COMMENT 'label ex PAT: id_creatore',
  `institution_type_id` int(11) DEFAULT NULL COMMENT 'label ex PAT: tipo_ente',
  `trasp_responsible_user_id` int(11) DEFAULT NULL COMMENT 'ID utente responsabile trasparenza',
  `state` tinyint(4) DEFAULT '1',
  `full_name_institution` varchar(191) NOT NULL,
  `short_institution_name` varchar(191) DEFAULT NULL,
  `vat` varchar(45) NOT NULL COMMENT 'campo p_iva su vecchio PAT.',
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
  `two_factors_identification` tinyint(4) DEFAULT NULL COMMENT 'Per vedere se è abilitata l''autenticazione a due fattori',
  `trasparenza_logo_file` varchar(225) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `domain_cookies` varchar(255) DEFAULT NULL,
  `trasparenza_urls` varchar(225) DEFAULT NULL,
  `url_pat` varchar(255) DEFAULT NULL COMMENT 'url_etrasparenza su vecchio pat',
  `bulletin_board_url` varchar(225) DEFAULT NULL COMMENT 'label ex PAT: url_albopretorio',
  `online_register_id` int(11) DEFAULT NULL COMMENT 'id albo online',
  `customer_support` tinyint(4) DEFAULT NULL,
  `simple_logo_file` varchar(225) DEFAULT NULL,
  `custom_css` varchar(255) DEFAULT NULL,
  `favicon_file` varchar(225) DEFAULT NULL,
  `opendata_channel` tinyint(4) DEFAULT NULL,
  `show_update_date` tinyint(4) DEFAULT NULL COMMENT 'campo mostra_data_aggiornamento su vecchio PAT.',
  `google_maps_api_key` varchar(255) DEFAULT 'null',
  `indexable` tinyint(4) DEFAULT '1',
  `show_regulation_in_structure` int(11) DEFAULT '1',
  `tabular_display_org_ind_pol` tinyint(4) DEFAULT '0' COMMENT 'campo visualizzazione_tabellare_org_ind_pol su vecchio PAT.',
  `max_users` int(11) DEFAULT NULL COMMENT 'numeor masimo di utenti attivabili per l''ente.',
  `client_code` varchar(45) DEFAULT NULL COMMENT 'codice cliente.',
  `smtp_user` varchar(191) DEFAULT NULL,
  `smtp_pass` varchar(191) DEFAULT NULL,
  `smtp_host` varchar(191) DEFAULT NULL,
  `smtp_port` varchar(45) DEFAULT NULL,
  `smtp_security` varchar(10) DEFAULT NULL,
  `smtp_auth` int(11) DEFAULT NULL,
  `show_smtp_auth` int(11) DEFAULT NULL,
  `email_notifications` varchar(191) DEFAULT NULL,
  `publication_responsible` varchar(191) DEFAULT NULL,
  `privacy_url` varchar(191) DEFAULT NULL,
  `private_token` varchar(255) DEFAULT NULL COMMENT 'token utilizzato per le chaimate REST.',
  `last_visit_time_limit` int(11) DEFAULT '2592000',
  `personnel_roles` text,
  `statistics_tracking_code` text,
  `favicon` varchar(191) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'indica se un ente è attivo o meno',
  `limits_call_api` int(11) DEFAULT '0' COMMENT 'limita chiamate api',
  `link_site_home` tinyint(4) DEFAULT '0' COMMENT 'Indica quale link mettere sul nome dell''ente nel footer e nel header',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella etrasp_enti su vecchio PAT.';

-- Dump dei dati della tabella pat.institutions: ~1 rows (circa)
INSERT INTO `institutions` (`id`, `id_creator`, `institution_type_id`, `trasp_responsible_user_id`, `state`, `full_name_institution`, `short_institution_name`, `vat`, `email_address`, `certified_email_address`, `institutional_website_name`, `institutional_website_url`, `top_level_institution_name`, `top_level_institution_url`, `welcome_text`, `footer_text`, `accessibility_text`, `address_street`, `address_zip_code`, `address_city`, `address_province`, `phone`, `two_factors_identification`, `trasparenza_logo_file`, `activation_date`, `expiration_date`, `domain_cookies`, `trasparenza_urls`, `url_pat`, `bulletin_board_url`, `online_register_id`, `customer_support`, `simple_logo_file`, `custom_css`, `favicon_file`, `opendata_channel`, `show_update_date`, `google_maps_api_key`, `indexable`, `show_regulation_in_structure`, `tabular_display_org_ind_pol`, `max_users`, `client_code`, `smtp_user`, `smtp_pass`, `smtp_host`, `smtp_port`, `smtp_security`, `smtp_auth`, `show_smtp_auth`, `email_notifications`, `publication_responsible`, `privacy_url`, `private_token`, `last_visit_time_limit`, `personnel_roles`, `statistics_tracking_code`, `favicon`, `active`, `limits_call_api`, `link_site_home`, `deleted`, `created_at`, `deleted_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 'Comune di Esempio', 'comune_di_esempio', '', 'comune@esempio.it', 'pec@esempio.it', 'Example', 'http://www.example.com', 'Ente di appartenenza', '', '<p>Vulputate elementum efficitur parturient class felis. Quam malesuada taciti nullam tellus nostra sed sapien lacus tincidunt risus. Commodo vivamus lorem torquent himenaeos aliquam tortor. Faucibus massa congue maximus nam ridiculus senectus quam maecenas. Eleifend iaculis praesent porta parturient facilisis cubilia velit neque quam. Amet nisi posuere iaculis primis donec class eleifend nullam gravida.</p>\r\n\r\n<p>Vitae dapibus litora congue semper finibus metus. Ullamcorper eu tortor senectus consectetuer id litora urna velit suscipit. Efficitur quisque etiam inceptos duis luctus nunc justo euismod convallis. Convallis feugiat nullam urna nulla cubilia pharetra. Primis nec lacinia pharetra tempor taciti neque ligula suspendisse vel eleifend. Nostra dui sit tempor euismod facilisis bibendum ligula.</p>\r\n', '<p>Footer personalizzato</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\r\n', NULL, 'Via XX Settembre', '', 'Roma', 'AQ', '0000000000', 1, 'NULL', '2022-10-20 11:07:17', '2025-11-27 18:09:17', '', 'http://patos-riuso.local', '', 'http://patos-riuso.loca', 222, 0, '25c8ba755b3c67e30cbcff8c9ef24282.png', 'index.css', 'NULL', NULL, 0, 'null', 1, 0, 1, NULL, NULL, '', '', '', '', NULL, NULL, 1, NULL, '', '', NULL, 2592000, NULL, '', NULL, 1, 0, 0, 0, '2021-10-06 10:42:04', NULL, '2023-11-27 18:09:17');

-- Dump della struttura di tabella pat.institution_type
CREATE TABLE IF NOT EXISTS `institution_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `label_institution_type_id` int(11) NOT NULL COMMENT 'relazione con tabella label_institution_type.',
  `name` varchar(191) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `type_name` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_etrasp_tipoentisemplice su vecchio PAT.';

-- Dump dei dati della tabella pat.institution_type: ~1 rows (circa)
INSERT INTO `institution_type` (`id`, `owner_id`, `label_institution_type_id`, `name`, `state`, `workflow_state`, `type_name`, `deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 3, 1, 'Comune', 1, 'finale_demo', 'finale_demo', 0, '2021-10-06 10:24:20', NULL, NULL);

-- Dump della struttura di tabella pat.object_absence_rates
CREATE TABLE IF NOT EXISTS `object_absence_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'relazione con tabella object_structures.',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `structure_name` varchar(255) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL COMMENT 'periodo',
  `year` int(4) DEFAULT NULL,
  `presence_percentage` decimal(10,2) DEFAULT NULL COMMENT 'campo presenza su vecchio PAT',
  `total_absence` decimal(10,2) DEFAULT NULL COMMENT 'campo assenza_totale su vecchio PAT.',
  `absence_illness` decimal(2,0) DEFAULT NULL COMMENT 'campo assenza_malattia su vecchio PAT.',
  `illness_days` decimal(2,0) DEFAULT NULL COMMENT 'campo giorni_malattia su vecchio PAT.',
  `attachments_id` int(11) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_tassi_assenza su vecchio PAT.';

-- Dump dei dati della tabella pat.object_absence_rates: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_assignments
CREATE TABLE IF NOT EXISTS `object_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL COMMENT 'id_propietario',
  `institution_id` int(11) NOT NULL COMMENT 'ente_id',
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'struttura organizzativa. Relazione con object_structures',
  `related_assignment_id` int(11) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `typology` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'Incarico',
  `consulting_type` varchar(255) DEFAULT NULL COMMENT 'tipo_consulenza.',
  `name` varchar(255) DEFAULT NULL COMMENT 'campo nominativo.',
  `object` varchar(600) DEFAULT NULL,
  `assignment_type` varchar(255) DEFAULT NULL COMMENT 'tipo_incarico',
  `assignment_start` datetime DEFAULT NULL,
  `assignment_end` datetime DEFAULT NULL,
  `end_of_assignment_not_available` tinyint(4) DEFAULT NULL,
  `end_of_assignment_not_available_txt` text,
  `compensation` decimal(15,2) DEFAULT NULL,
  `compensation_provided` decimal(15,2) DEFAULT NULL COMMENT 'compenso erogato.',
  `compensation_provided_date` datetime DEFAULT NULL COMMENT 'compenso erogato data.',
  `liquidation_date` datetime DEFAULT NULL COMMENT 'data liquidazione',
  `liquidation_year` int(4) DEFAULT NULL,
  `variable_compensation` text COMMENT 'compenso_variabile',
  `notes` text,
  `acts_extremes` text COMMENT 'estremi atti.',
  `attachments_id` int(11) DEFAULT NULL,
  `assignment_reason` varchar(255) DEFAULT NULL,
  `dirigente` int(11) DEFAULT NULL COMMENT 'Campo per la retrocompatibilità',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'stato di pubblicazione',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_incarichi su vecchio PAT.';

-- Dump dei dati della tabella pat.object_assignments: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_balance_sheets
CREATE TABLE IF NOT EXISTS `object_balance_sheets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_measure_id` int(11) DEFAULT NULL COMMENT 'ID del provvedmento associato al bilancio',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `name` varchar(600) DEFAULT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `description` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `register_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_bilanci su vecchio PAT.';

-- Dump dei dati della tabella pat.object_balance_sheets: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_bdncp_general_acts_documents
CREATE TABLE IF NOT EXISTS `object_bdncp_general_acts_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `publishing_status` tinyint(4) DEFAULT '1',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Soluzioni tecnologiche BDNCP';

-- Dump dei dati della tabella pat.object_bdncp_general_acts_documents: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_bdncp_procedure
CREATE TABLE IF NOT EXISTS `object_bdncp_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `procurement_id` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID appalto',
  `owner_id` int(11) NOT NULL,
  `object` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cig` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `typology` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_procedure_id` int(11) DEFAULT NULL COMMENT 'Id della procedura relativa ad un avviso',
  `alert_date` datetime DEFAULT NULL COMMENT 'Data avviso',
  `liquidation_date` datetime DEFAULT NULL COMMENT 'Data della liquidazione',
  `amount_liquidated` decimal(15,2) DEFAULT NULL,
  `multicig` tinyint(4) DEFAULT NULL,
  `publish_father_document` tinyint(4) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `bdncp_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1',
  `public_debate_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Dibattito pubblico',
  `public_debate_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Dibattito Pubblico',
  `notice_documents_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Documenti di gara ',
  `notice_documents_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Documenti di gara',
  `judging_commission_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Commissione giudicatrice',
  `judging_commission_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Commissione Giudicatrice',
  `equal_opportunities_af_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Pari opportunita fase Affidamento',
  `equal_opportunities_af_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Pari oportunità fase affidamento',
  `local_public_services_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Servizi Pubblici Locali',
  `local_public_services_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note servizi pubblici locali',
  `advisory_board_technical_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Collegio consultivo tecnico',
  `advisory_board_technical_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note collegio consultivo tecnico',
  `equal_opportunities_es_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Pari opportunita fase esecutiva',
  `equal_opportunities_es_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note pari opportunita fase esecutiva',
  `free_contract_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Contratto gratuito e forme speciali di partenariato',
  `free_contract_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Stai pubblicando un Contratto gratuito e forme speciali di partenariato?',
  `emergency_foster_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Affidamenti di somma urgenza',
  `emergency_foster_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note Affidamenti di somma urgenza',
  `foster_procedure_check` tinyint(2) DEFAULT '0' COMMENT 'checkbox Procedura di Affidamento',
  `foster_procedure_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note procedura di affidamento',
  `publishing_start` datetime DEFAULT NULL COMMENT 'Data di inizio pubblicazione',
  `publishing_end` datetime DEFAULT NULL COMMENT 'Data di fine pubblicazione',
  `unfixed` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `__tag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella per le PROCEDURE DELIBERA 261/2023';

-- Dump dei dati della tabella pat.object_bdncp_procedure: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_charges
CREATE TABLE IF NOT EXISTS `object_charges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `type` varchar(255) DEFAULT NULL,
  `citizen` tinyint(4) DEFAULT '0',
  `companies` tinyint(4) DEFAULT '0',
  `title` varchar(600) DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `description` text,
  `info_url` varchar(255) DEFAULT NULL,
  `normative_id` int(11) DEFAULT NULL,
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'stato di pubblicazione',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_oneri su vecchio PAT.';

-- Dump dei dati della tabella pat.object_charges: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_commissions
CREATE TABLE IF NOT EXISTS `object_commissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `name` varchar(225) DEFAULT NULL,
  `typology` varchar(25) DEFAULT NULL,
  `president_id` int(11) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `archive` tinyint(4) DEFAULT NULL COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text COMMENT 'informazioni staticizzate a seguito dell''archiviazione',
  `archived_end_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `archived` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_commissioni su vecchio PAT.';

-- Dump dei dati della tabella pat.object_commissions: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_company
CREATE TABLE IF NOT EXISTS `object_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
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
  `archived` int(11) NOT NULL DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_info` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_societa su vecchio PAT. Relazione con personale tramite la tabella rel_personnel_company.';

-- Dump dei dati della tabella pat.object_company: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_contest
CREATE TABLE IF NOT EXISTS `object_contest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `related_contest_id` int(11) DEFAULT NULL COMMENT 'campo concorso_collegato su vecchio PAT.',
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con object_structures.',
  `object_measure_id` int(11) DEFAULT NULL COMMENT 'ID provvedimento associato al bando di concorso',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
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
  `hired_employees` int(11) DEFAULT NULL COMMENT 'campo dipendenti_assunti su vecchio PAT.',
  `description` text,
  `test_calendar` text,
  `evaluation_criteria` text,
  `traces_written_tests` text,
  `attachments_id` int(11) DEFAULT NULL,
  `publication_state` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'stato di pubblicazione',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_concorsi su vecchio PAT.';

-- Dump dei dati della tabella pat.object_contest: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_contests_acts
CREATE TABLE IF NOT EXISTS `object_contests_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con object_structures',
  `object_personnel_id` int(11) DEFAULT NULL COMMENT 'campo rup su vecchio PAT.Relazione con object_personnel.',
  `relative_procedure_id` int(11) DEFAULT NULL,
  `object_measure_id` int(11) DEFAULT NULL COMMENT 'ID provvedimento associato',
  `relative_notice_id` int(11) DEFAULT NULL COMMENT 'Bando relativo al lotto',
  `qualification_requirement_id` int(11) DEFAULT NULL COMMENT 'ID del requisito di qualificazione',
  `type` varchar(45) DEFAULT NULL,
  `typology` varchar(20) DEFAULT NULL,
  `object` varchar(800) DEFAULT NULL,
  `sector` varchar(50) DEFAULT NULL,
  `cig` varchar(255) DEFAULT NULL,
  `is_multicig` tinyint(4) DEFAULT '0',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `attachments_id` int(11) DEFAULT NULL,
  `contract` varchar(50) DEFAULT NULL,
  `customize_admin_data` tinyint(4) DEFAULT '0' COMMENT 'Personalizzazione dati amministrazione',
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
  `anac_year` int(4) DEFAULT NULL COMMENT 'data atto',
  `act_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `guue_date` datetime DEFAULT NULL COMMENT 'Data di pubblicazione del bando di gara sulla G.U.U.E.',
  `guri_date` datetime DEFAULT NULL COMMENT 'Data di pubblicazione del bando di gara sulla G.U.R.I.',
  `cpv_code_id` int(11) DEFAULT NULL COMMENT 'ID del codice CPV',
  `codice_scp` varchar(255) DEFAULT NULL,
  `url_scp` varchar(255) DEFAULT NULL,
  `details` text,
  `contraent_choice` varchar(255) DEFAULT NULL COMMENT 'campo scelta_contraente su vecchio PAT.',
  `typology_result` varchar(255) DEFAULT NULL COMMENT 'campo tipologia_esito su vecchio PAT, per esito/affidamento.',
  `award_amount_value` decimal(15,2) DEFAULT NULL COMMENT 'campo valore_importo_aggiudicazione su vecchio PAT, usato per esito/affidamento',
  `amount_liquidated` decimal(15,2) DEFAULT NULL COMMENT 'campo importo_liquidato su vecchio PAT, utilizzato per liquidazione',
  `publication_date_type` varchar(255) DEFAULT NULL COMMENT 'campo tipologia_data_pubblicazione su vecchio PAT, usato per esiti/affidamenti.',
  `decree_163` int(11) DEFAULT '0',
  `work_start_date` datetime DEFAULT NULL COMMENT 'campo data_inizio_lavori su vecchio PAT, utilizzato per esito/affidamento',
  `work_end_date` datetime DEFAULT NULL COMMENT 'campo data_inizio_lavori su vecchio PAT, utilizzato per esito/affidamento.',
  `contracting_stations_publication_date` datetime DEFAULT NULL COMMENT 'campo data_pubblicazione_sa su vecchio PAT, utilizzato per esiti/affidamenti',
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `number_readings` int(11) DEFAULT NULL COMMENT 'campo numero letture vecchio PAT',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `source_id` varchar(255) DEFAULT NULL COMMENT 'campo id_ori su vecchio PAT',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_gare_atti (bandi) su ex PAT.';

-- Dump dei dati della tabella pat.object_contests_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_grants
CREATE TABLE IF NOT EXISTS `object_grants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'campo struttura su vecchio PAT. Relazione con tabella object_structures.',
  `object_regulations_id` int(11) DEFAULT NULL COMMENT 'campo regolamento su vecchio PAT. Relazione con object_regulations.',
  `grant_id` int(11) DEFAULT NULL COMMENT 'sovvenzione relativa',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `beneficiary_name` varchar(191) DEFAULT NULL COMMENT 'campo nominativo.',
  `fiscal_data_not_available` tinyint(4) DEFAULT '0' COMMENT 'campo fiscali_non_disponibili su vecchio PAT.',
  `fiscal_data` text,
  `object` varchar(800) DEFAULT NULL,
  `typology` varchar(20) NOT NULL DEFAULT 'Sovvenzione',
  `type` varchar(20) NOT NULL DEFAULT 'grant',
  `concession_act_date` datetime DEFAULT NULL COMMENT 'Data atto di concessione.',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `concession_amount` decimal(15,2) DEFAULT NULL COMMENT 'campo compenso su vecchio PAT.',
  `detection_mode` text COMMENT 'campo modo_individuazione su vecchio PAT.',
  `privacy` tinyint(4) DEFAULT NULL COMMENT 'campo omissis privacy.',
  `reference_date` datetime DEFAULT NULL,
  `attachments_id` int(11) DEFAULT NULL,
  `compensation_paid` decimal(15,2) DEFAULT NULL COMMENT 'campo compenso_erogato su vecchio PAT.',
  `compensation_paid_date` int(11) DEFAULT NULL COMMENT 'campo data_compenso_erogato su vecchio PAT.',
  `notes` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_sovvenzioni su vecchio PAT.';

-- Dump dei dati della tabella pat.object_grants: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_interventions
CREATE TABLE IF NOT EXISTS `object_interventions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `name` varchar(60) DEFAULT NULL,
  `description` text,
  `derogations` text,
  `time_limits` datetime DEFAULT NULL COMMENT 'campo termini_temporali su vecchio PAT.',
  `estimated_cost` varchar(45) DEFAULT NULL,
  `effective_cost` varchar(45) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_interventi su vecchio PAT.';

-- Dump dei dati della tabella pat.object_interventions: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_lease_canons
CREATE TABLE IF NOT EXISTS `object_lease_canons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'campo id_ufficio su vecchio PAT. Relazione con object_structures.',
  `state` tinyint(4) DEFAULT NULL,
  `workflow_state` varchar(50) DEFAULT NULL,
  `canon_type` varchar(255) DEFAULT NULL,
  `beneficiary` varchar(255) DEFAULT NULL,
  `fiscal_code` varchar(45) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `contract_statements` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `notes` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_canoni_localizzazione';

-- Dump dei dati della tabella pat.object_lease_canons: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_measures
CREATE TABLE IF NOT EXISTS `object_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int(11) DEFAULT NULL COMMENT 'campo id_procedura su vecchio PAT. Relazione con tabella object_measures.',
  `object_bdncp_procedure_id` int(11) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `number` varchar(255) DEFAULT NULL,
  `object` varchar(900) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `article_type` int(11) DEFAULT NULL COMMENT 'Tipologia art.23',
  `date` datetime DEFAULT NULL,
  `content` text,
  `expense` text,
  `extremes` text,
  `choice_of_contractor` text COMMENT 'campo scelta_contraente su vecchio PAT.',
  `notes` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_provvedimenti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_measures: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_modules_regulations
CREATE TABLE IF NOT EXISTS `object_modules_regulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `typology` varchar(255) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `description` text,
  `order` int(11) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_modulistica_regolamenti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_modules_regulations: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_news_notices
CREATE TABLE IF NOT EXISTS `object_news_notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `news_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title` varchar(180) DEFAULT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `evidence` tinyint(4) DEFAULT NULL,
  `public_in_notice` tinyint(4) DEFAULT NULL COMMENT 'campo publica_in_bando su vecchio PAT.',
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `content` text,
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_news_avvisi su vecchio PAT.';

-- Dump dei dati della tabella pat.object_news_notices: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_normatives
CREATE TABLE IF NOT EXISTS `object_normatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `name` varchar(255) DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL COMMENT 'campo data_emissione su vecchio PAT.',
  `act_type` varchar(45) DEFAULT NULL COMMENT 'campo tipologia_atto su vecchio PAT.',
  `number` varchar(11) DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `normative_link` varchar(255) DEFAULT NULL,
  `normative_topic` varchar(225) DEFAULT NULL,
  `description` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è PUBBLICATO O MENO',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_normativa su vecchio PAT.';

-- Dump dei dati della tabella pat.object_normatives: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_notices_acts
CREATE TABLE IF NOT EXISTS `object_notices_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int(11) DEFAULT NULL COMMENT 'campo id_bando su vecchio PAT. Relazione con tabella object_contests_act.\n',
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `object` varchar(500) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `projects_start_date` datetime DEFAULT NULL,
  `cup` varchar(50) DEFAULT NULL,
  `total_fin_amount` varchar(50) DEFAULT NULL,
  `financial_sources` varchar(191) DEFAULT NULL,
  `implementation_state` varchar(255) DEFAULT NULL,
  `details` text,
  `attachments_id` int(11) DEFAULT NULL COMMENT 'label relazione',
  `public_in` varchar(191) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `number_readings` int(11) DEFAULT NULL COMMENT 'numero di letture su vecchio PAT',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_bandi_atti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_notices_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_notices_for_qualification_requirements
CREATE TABLE IF NOT EXISTS `object_notices_for_qualification_requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `title` varchar(45) DEFAULT NULL,
  `content` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_altri_contenuti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_other_contents: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_personnel
CREATE TABLE IF NOT EXISTS `object_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `title` varchar(45) DEFAULT NULL COMMENT 'titolo accademico o professionale',
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `fiscal_code` varchar(45) DEFAULT NULL,
  `qualification` varchar(45) DEFAULT NULL,
  `determined_term` tinyint(4) DEFAULT NULL COMMENT 'campo determinato su vecchio PAT.',
  `political_role` varchar(255) DEFAULT NULL,
  `commissions` varchar(50) DEFAULT NULL,
  `political_organ` varchar(255) DEFAULT NULL,
  `delegation` tinyint(4) DEFAULT NULL COMMENT 'campo delega su vecchio PAT.',
  `delegation_text` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `mobile_phone` varchar(45) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `not_available_email` tinyint(4) DEFAULT NULL,
  `not_available_email_txt` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `certified_email` varchar(255) DEFAULT NULL,
  `details_conferment_act` text,
  `notes` text,
  `compensations` text,
  `trips_import` text COMMENT 'campo importi_viaggi su vecchio PAT.',
  `other_assignments` text,
  `personnel_lists` tinyint(4) DEFAULT NULL,
  `in_office_since` datetime DEFAULT NULL,
  `in_office_until` datetime DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `other_info` text,
  `information_archive` text,
  `on_leave` tinyint(4) DEFAULT NULL COMMENT 'in aspettativa',
  `extremes_of_conference` text,
  `public_in` varchar(20) DEFAULT NULL,
  `archived` int(11) DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text COMMENT 'informazioni staticizzate al momento dell''archviazione',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `last_update_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `firstname` (`firstname`,`lastname`),
  FULLTEXT KEY `full_name` (`full_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_riferimenti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_personnel: 0 rows
/*!40000 ALTER TABLE `object_personnel` DISABLE KEYS */;
/*!40000 ALTER TABLE `object_personnel` ENABLE KEYS */;

-- Dump della struttura di tabella pat.object_proceedings
CREATE TABLE IF NOT EXISTS `object_proceedings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '0',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `name` varchar(500) DEFAULT NULL,
  `contact` varchar(50) DEFAULT 'struttura referenti' COMMENT 'Visualizzazione del Chi Contattare',
  `description` text NOT NULL,
  `costs` text,
  `silence_consent` tinyint(4) DEFAULT NULL,
  `declaration` tinyint(1) DEFAULT NULL,
  `regulation` text,
  `url_service` varchar(225) DEFAULT NULL,
  `deadline` text,
  `protection_instruments` varchar(255) DEFAULT NULL,
  `service_available` tinyint(1) DEFAULT NULL,
  `publication_state` varchar(225) DEFAULT NULL,
  `service_time` varchar(500) DEFAULT NULL,
  `public_monitoring_proceeding` tinyint(4) DEFAULT NULL,
  `attachments_id` int(11) DEFAULT NULL,
  `automatically_publish_monitoring_data` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'campo Pubblica automaticamente i dati sul monitoraggio su vecchio Pat',
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `archived` int(11) DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_info` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_procedimenti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_proceedings: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_programming_acts
CREATE TABLE IF NOT EXISTS `object_programming_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `attachments_id` int(11) DEFAULT NULL,
  `object` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `act_type` varchar(255) DEFAULT NULL,
  `public_in_public_works` tinyint(4) DEFAULT '0',
  `description` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_atti_programmazione su vecchio PAT.';

-- Dump dei dati della tabella pat.object_programming_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_real_estate_asset
CREATE TABLE IF NOT EXISTS `object_real_estate_asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `attachments_id` int(11) DEFAULT NULL,
  `sheet` varchar(191) DEFAULT NULL COMMENT 'foglio',
  `particle` varchar(191) DEFAULT NULL COMMENT 'particella',
  `subaltern` varchar(191) DEFAULT NULL COMMENT 'subalterno',
  `gross_surface` varchar(255) DEFAULT NULL,
  `discovered_surface` varchar(255) DEFAULT NULL,
  `description` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se un record è pubblicato o meno',
  `archived` tinyint(4) DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_info` text,
  `archived_end_date` datetime DEFAULT NULL COMMENT 'Data di fine archiviazione',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_patrimonio_immobiliare su vecchio PAT.';

-- Dump dei dati della tabella pat.object_real_estate_asset: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_regulations
CREATE TABLE IF NOT EXISTS `object_regulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `description` text,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `title` varchar(600) DEFAULT NULL,
  `issue_date` datetime DEFAULT NULL COMMENT 'campo su vecchio PAT: data_emissione',
  `number` varchar(191) DEFAULT NULL,
  `protocol` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_regolamenti su vecchio PAT.';

-- Dump dei dati della tabella pat.object_regulations: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_relief_checks
CREATE TABLE IF NOT EXISTS `object_relief_checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `object_structures_id` int(11) DEFAULT NULL COMMENT 'campo ufficio su vecchio PAT.',
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(50) DEFAULT 'finale',
  `object` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `description` text,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è publicato o meno',
  `attachments_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_controlli_rilievi su vecchio PAT.';

-- Dump dei dati della tabella pat.object_relief_checks: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_structures
CREATE TABLE IF NOT EXISTS `object_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `structure_of_belonging_id` int(11) DEFAULT NULL COMMENT 'campo struttura su vecchio PAT.',
  `institution_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `structure_name` varchar(255) DEFAULT NULL,
  `responsible_not_available` tinyint(4) DEFAULT NULL,
  `referent_not_available_txt` varchar(255) DEFAULT NULL,
  `ad_interim` tinyint(4) DEFAULT NULL,
  `reference_email` varchar(45) DEFAULT NULL,
  `email_not_available` tinyint(4) DEFAULT NULL,
  `email_not_available_txt` varchar(45) DEFAULT NULL,
  `certified_email` varchar(191) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `description` text,
  `articulation` tinyint(4) DEFAULT NULL,
  `address_detail` varchar(255) DEFAULT NULL,
  `based_structure` tinyint(4) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `lat` varchar(50) DEFAULT NULL,
  `lon` varchar(50) DEFAULT NULL,
  `timetables` varchar(500) DEFAULT NULL COMMENT 'campo orari su vecchio PAT.',
  `order` int(11) DEFAULT NULL,
  `archived` tinyint(4) DEFAULT '0' COMMENT 'Utilizzato per indicare se un elemento è stato archiviato o meno',
  `archived_end_date` datetime DEFAULT NULL COMMENT 'data di fine archiviazione',
  `archived_active_to` datetime DEFAULT NULL COMMENT 'data attiva fino al(per l''archiviazione)',
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è pubblicato o meno',
  `archived_info` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella su vecchio PAT: oggetto_uffici';

-- Dump dei dati della tabella pat.object_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.object_supplie_list
CREATE TABLE IF NOT EXISTS `object_supplie_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `institution_id` varchar(45) NOT NULL,
  `state` tinyint(4) DEFAULT '1',
  `workflow_state` varchar(45) DEFAULT 'finale',
  `typology` varchar(255) DEFAULT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'Fornitore singolo',
  `it` tinyint(4) DEFAULT NULL COMMENT 'Indica se il fornitore è italiano o estero',
  `name` varchar(255) DEFAULT NULL,
  `vat` varchar(255) DEFAULT NULL,
  `foreign_tax_identification` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `publishing_responsable` int(11) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `publishing_status` tinyint(4) DEFAULT '1' COMMENT 'Indica se il record è stato pubblicato o meno',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella oggetto_elenco_fornitori su vecchio PAT.';

-- Dump dei dati della tabella pat.object_supplie_list: ~0 rows (circa)

-- Dump della struttura di tabella pat.password_history
CREATE TABLE IF NOT EXISTS `password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Salvataggio vecchie password';

-- Dump dei dati della tabella pat.password_history: 0 rows
/*!40000 ALTER TABLE `password_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_history` ENABLE KEYS */;

-- Dump della struttura di tabella pat.permits
CREATE TABLE IF NOT EXISTS `permits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acl_profiles_id` int(11) NOT NULL,
  `sections_bo_id` int(11) NOT NULL,
  `sections_fo_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `create` tinyint(4) DEFAULT NULL,
  `read` tinyint(4) DEFAULT NULL,
  `update` tinyint(4) DEFAULT NULL,
  `delete` tinyint(4) DEFAULT NULL,
  `send_notify_app_io` int(11) NOT NULL DEFAULT '0' COMMENT 'Puo avere valore 0:non può inviare notifiche push app io, 1:puo inviare solo le notifiche nei record creati da lui, 2: puo inviare le notifiche push da tutti i record',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1789 DEFAULT CHARSET=latin1 COMMENT='Permessi, tabella di relazione tra acl_profiles e sections.';

-- Dump dei dati della tabella pat.permits: ~188 rows (circa)
INSERT INTO `permits` (`id`, `acl_profiles_id`, `sections_bo_id`, `sections_fo_id`, `institution_id`, `create`, `read`, `update`, `delete`, `send_notify_app_io`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1601, 1, 2, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1602, 1, 3, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1603, 1, 4, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1604, 1, 5, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1605, 1, 6, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1606, 1, 7, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1607, 1, 8, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1608, 1, 9, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1609, 1, 10, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1610, 1, 12, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1611, 1, 13, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1612, 1, 14, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1613, 1, 15, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1614, 1, 92, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1615, 1, 91, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1616, 1, 22, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1617, 1, 23, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1618, 1, 24, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1619, 1, 25, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1620, 1, 26, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1621, 1, 27, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1622, 1, 28, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1623, 1, 17, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1624, 1, 19, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1625, 1, 20, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1626, 1, 44, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1627, 1, 45, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1628, 1, 49, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1629, 1, 51, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1630, 1, 52, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1631, 1, 61, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1632, 1, 54, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1633, 1, 55, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1634, 1, 56, NULL, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1635, 1, 44, 1, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1636, 1, 44, 21, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1637, 1, 44, 23, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1638, 1, 44, 24, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1639, 1, 44, 25, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1640, 1, 44, 26, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1641, 1, 44, 27, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1642, 1, 44, 28, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1643, 1, 44, 29, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1644, 1, 44, 30, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1645, 1, 44, 31, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1646, 1, 44, 32, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1647, 1, 44, 2, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1648, 1, 44, 37, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1649, 1, 44, 241, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1650, 1, 44, 243, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1651, 1, 44, 246, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1652, 1, 44, 247, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1653, 1, 44, 38, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1654, 1, 44, 39, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1655, 1, 44, 40, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1656, 1, 44, 41, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1657, 1, 44, 42, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1658, 1, 44, 43, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1659, 1, 44, 44, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1660, 1, 44, 3, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1661, 1, 44, 46, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1662, 1, 44, 47, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1663, 1, 44, 50, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1664, 1, 44, 4, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1665, 1, 44, 58, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1666, 1, 44, 59, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1667, 1, 44, 60, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1668, 1, 44, 61, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1669, 1, 44, 62, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1670, 1, 44, 63, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1671, 1, 44, 64, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1672, 1, 44, 65, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1673, 1, 44, 66, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1674, 1, 44, 67, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1675, 1, 44, 68, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1676, 1, 44, 69, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1677, 1, 44, 70, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1678, 1, 44, 71, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1679, 1, 44, 72, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1680, 1, 44, 73, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1681, 1, 44, 5, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1682, 1, 44, 75, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1683, 1, 44, 76, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1684, 1, 44, 77, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1685, 1, 44, 78, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1686, 1, 44, 6, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1687, 1, 44, 82, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1688, 1, 44, 83, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1689, 1, 44, 84, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1690, 1, 44, 85, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1691, 1, 44, 86, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1692, 1, 44, 7, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1693, 1, 44, 89, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1694, 1, 44, 91, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1695, 1, 44, 92, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1696, 1, 44, 93, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1697, 1, 44, 95, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1698, 1, 44, 8, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1699, 1, 44, 98, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1700, 1, 44, 99, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1701, 1, 44, 101, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1702, 1, 44, 9, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1703, 1, 44, 103, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1704, 1, 44, 104, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1705, 1, 44, 105, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1706, 1, 44, 106, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1707, 1, 44, 107, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1708, 1, 44, 108, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1709, 1, 44, 183, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1710, 1, 44, 10, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1711, 1, 44, 580, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1712, 1, 44, 113, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1713, 1, 44, 297, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1714, 1, 44, 298, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1715, 1, 44, 581, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1716, 1, 44, 110, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1717, 1, 44, 111, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1718, 1, 44, 112, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1719, 1, 44, 529, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1720, 1, 44, 257, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1721, 1, 44, 528, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1722, 1, 44, 524, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1723, 1, 44, 526, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1724, 1, 44, 115, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1725, 1, 44, 116, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1726, 1, 44, 530, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1727, 1, 44, 527, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1728, 1, 44, 117, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1729, 1, 44, 525, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1730, 1, 44, 532, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1731, 1, 44, 533, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1732, 1, 44, 534, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1733, 1, 44, 531, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1734, 1, 44, 588, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1735, 1, 44, 11, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1736, 1, 44, 125, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1737, 1, 44, 126, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1738, 1, 44, 127, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1739, 1, 44, 12, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1740, 1, 44, 130, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1741, 1, 44, 131, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1742, 1, 44, 13, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1743, 1, 44, 133, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1744, 1, 44, 135, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1745, 1, 44, 136, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1746, 1, 44, 137, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1747, 1, 44, 14, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1748, 1, 44, 139, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1749, 1, 44, 140, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1750, 1, 44, 141, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1751, 1, 44, 142, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1752, 1, 44, 143, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1753, 1, 44, 144, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1754, 1, 44, 145, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1755, 1, 44, 15, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1756, 1, 44, 147, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1757, 1, 44, 148, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1758, 1, 44, 149, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1759, 1, 44, 150, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1760, 1, 44, 151, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1761, 1, 44, 16, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1762, 1, 44, 154, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1763, 1, 44, 155, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1764, 1, 44, 156, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1765, 1, 44, 157, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1766, 1, 44, 158, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1767, 1, 44, 159, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1768, 1, 44, 160, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1769, 1, 44, 161, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1770, 1, 44, 17, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1771, 1, 44, 166, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1772, 1, 44, 167, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1773, 1, 44, 168, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1774, 1, 44, 184, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1775, 1, 44, 185, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1776, 1, 44, 186, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1777, 1, 44, 187, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1778, 1, 44, 18, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1779, 1, 44, 174, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1780, 1, 44, 175, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1781, 1, 44, 176, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1782, 1, 44, 177, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1783, 1, 44, 178, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1784, 1, 44, 179, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1785, 1, 44, 180, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1786, 1, 44, 19, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1787, 1, 44, 182, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL),
	(1788, 1, 44, 181, 1, 1, 1, 1, 1, 0, '2024-06-03 11:30:04', '2024-06-03 11:30:04', NULL);

-- Dump della struttura di tabella pat.recovery_password
CREATE TABLE IF NOT EXISTS `recovery_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `token` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per gestire il recupero della password.';

-- Dump dei dati della tabella pat.recovery_password: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_assignments_measures
CREATE TABLE IF NOT EXISTS `rel_assignments_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'utilizzata per gestire i provvedimenti associati agli incarichi.',
  `object_assignments_id` int(11) NOT NULL,
  `object_measures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati agli incarichi.';

-- Dump dei dati della tabella pat.rel_assignments_measures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_assignments_notices_acts
CREATE TABLE IF NOT EXISTS `rel_assignments_notices_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_assignments_id` int(11) NOT NULL,
  `object_notices_acts_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli atti delle amministrazioni associati agli incarichi.';

-- Dump dei dati della tabella pat.rel_assignments_notices_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_attachment_label_archive
CREATE TABLE IF NOT EXISTS `rel_attachment_label_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_id` int(11) DEFAULT NULL,
  `archive_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel_attachment_label_archive_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dump dei dati della tabella pat.rel_attachment_label_archive: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_bdncp_procedure_assignments
CREATE TABLE IF NOT EXISTS `rel_bdncp_procedure_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_procedure_id` int(11) NOT NULL COMMENT 'Id procedura bdncp',
  `object_assignment_id` int(11) NOT NULL COMMENT 'ID incarico',
  `typology` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Indica la tipologia della relazione (se per il colleggio o la commissione',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relazione tra le procedure bdncp e gli incarichi';

-- Dump dei dati della tabella pat.rel_bdncp_procedure_assignments: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_categories_attachments
CREATE TABLE IF NOT EXISTS `rel_categories_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attachments_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `archive` varchar(45) DEFAULT NULL,
  `id_archive` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per la gestione degli allegati.';

-- Dump dei dati della tabella pat.rel_categories_attachments: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_charges_measures
CREATE TABLE IF NOT EXISTS `rel_charges_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_charges_id` int(11) NOT NULL,
  `object_measures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati agli oneri.';

-- Dump dei dati della tabella pat.rel_charges_measures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_charges_normatives
CREATE TABLE IF NOT EXISTS `rel_charges_normatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_charges_id` int(11) NOT NULL,
  `object_normatives_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i riferimenti normativi associati agli oneri.';

-- Dump dei dati della tabella pat.rel_charges_normatives: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_charges_proceedings
CREATE TABLE IF NOT EXISTS `rel_charges_proceedings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_charges_id` int(11) NOT NULL,
  `object_proceedings_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`object_charges_id`,`object_proceedings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati agli oneri.';

-- Dump dei dati della tabella pat.rel_charges_proceedings: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_charges_regulations
CREATE TABLE IF NOT EXISTS `rel_charges_regulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_charges_id` int(11) NOT NULL,
  `object_regulations_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i regolamenti associati agli oneri.';

-- Dump dei dati della tabella pat.rel_charges_regulations: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_commissions_personnel
CREATE TABLE IF NOT EXISTS `rel_commissions_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_commissions_id` int(11) NOT NULL,
  `object_personnel_id` int(11) NOT NULL,
  `typology` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utlizzata per gestire presidente, vicepresidente, segretari e membri delle commissioni, specificandolo nel campo typology.';

-- Dump dei dati della tabella pat.rel_commissions_personnel: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_contests_acts_contests_acts
CREATE TABLE IF NOT EXISTS `rel_contests_acts_contests_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contests_acts_id` int(11) NOT NULL COMMENT 'id del bando preso in questione',
  `object_contests_acts_id1` int(11) NOT NULL COMMENT 'id bando associato al bando preso in questione.',
  `typology` varchar(45) NOT NULL COMMENT 'tipologia del bando associato, es:altre_procedure, bando_collegato ecc',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Altre procedure.Utilizzata per gestire le varie tipologie di altre procedure associate ai vari tipi di bando.';

-- Dump dei dati della tabella pat.rel_contests_acts_contests_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_contests_acts_supplie_list
CREATE TABLE IF NOT EXISTS `rel_contests_acts_supplie_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int(11) NOT NULL,
  `object_supplie_list_id` int(11) NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le tipologie di fornitori associati ai vari bandi, es: partecipanti o aggiudicatari.';

-- Dump dei dati della tabella pat.rel_contests_acts_supplie_list: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_contests_act_proceeding
CREATE TABLE IF NOT EXISTS `rel_contests_act_proceeding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int(11) NOT NULL,
  `object_proceeding_id` int(11) NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.rel_contests_act_proceeding: 0 rows
/*!40000 ALTER TABLE `rel_contests_act_proceeding` DISABLE KEYS */;
/*!40000 ALTER TABLE `rel_contests_act_proceeding` ENABLE KEYS */;

-- Dump della struttura di tabella pat.rel_contests_act_requirements
CREATE TABLE IF NOT EXISTS `rel_contests_act_requirements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contest_act_id` int(11) NOT NULL,
  `object_requirement_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.rel_contests_act_requirements: 0 rows
/*!40000 ALTER TABLE `rel_contests_act_requirements` DISABLE KEYS */;
/*!40000 ALTER TABLE `rel_contests_act_requirements` ENABLE KEYS */;

-- Dump della struttura di tabella pat.rel_contest_acts_public_in
CREATE TABLE IF NOT EXISTS `rel_contest_acts_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_act_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra bandi di gara e sezioni fo(per il pubblica in)';

-- Dump dei dati della tabella pat.rel_contest_acts_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_contest_assignments
CREATE TABLE IF NOT EXISTS `rel_contest_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_contest_id` int(11) NOT NULL,
  `object_assignments_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire la commissione giudicatrice incarichi.';

-- Dump dei dati della tabella pat.rel_contest_assignments: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_general_acts_documents_public_in
CREATE TABLE IF NOT EXISTS `rel_general_acts_documents_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `general_acts_documents_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabella per la gestione del pubblica in per gli Atti e documenti di carattere generale riferiti a tutte le procedure';

-- Dump dei dati della tabella pat.rel_general_acts_documents_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_grants_normatives
CREATE TABLE IF NOT EXISTS `rel_grants_normatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_grants_id` int(11) NOT NULL,
  `object_normatives_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le normative associate alla sovvenzione.';

-- Dump dei dati della tabella pat.rel_grants_normatives: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_grants_personnel
CREATE TABLE IF NOT EXISTS `rel_grants_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_grants_id` int(11) NOT NULL,
  `object_personnel_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i responsabili delle sovvenzioni.';

-- Dump dei dati della tabella pat.rel_grants_personnel: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_institution_type_public_in_section
CREATE TABLE IF NOT EXISTS `rel_institution_type_public_in_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_type_id` int(11) NOT NULL,
  `section_public_in_id` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra tipo ente e le configurazioni per il pubblica in(section_fo_config_publication_archive) per il pubblica in';

-- Dump dei dati della tabella pat.rel_institution_type_public_in_section: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_interventions_measures
CREATE TABLE IF NOT EXISTS `rel_interventions_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_interventions_id` int(11) NOT NULL,
  `object_measures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i provvedimenti associati all''intervento.';

-- Dump dei dati della tabella pat.rel_interventions_measures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_interventions_regulations
CREATE TABLE IF NOT EXISTS `rel_interventions_regulations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_interventions_id` int(11) NOT NULL,
  `object_regulations_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i regolamenti assiciati agli interventi.';

-- Dump dei dati della tabella pat.rel_interventions_regulations: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_lease_canons_real_estate_asset
CREATE TABLE IF NOT EXISTS `rel_lease_canons_real_estate_asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_lease_canons_id` int(11) NOT NULL,
  `object_real_estate_asset_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli immobili associati ad un canone di locazione.';

-- Dump dei dati della tabella pat.rel_lease_canons_real_estate_asset: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_measures_personnel
CREATE TABLE IF NOT EXISTS `rel_measures_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_measures_id` int(11) NOT NULL,
  `object_personnel_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i responsabili del provvedimento.';

-- Dump dei dati della tabella pat.rel_measures_personnel: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_measures_structures
CREATE TABLE IF NOT EXISTS `rel_measures_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_measures_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate ai provvedimenti.';

-- Dump dei dati della tabella pat.rel_measures_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_modules_proceedings
CREATE TABLE IF NOT EXISTS `rel_modules_proceedings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_modules_regulations_id` int(11) NOT NULL,
  `object_proceedings_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati alla modulistica regolamenti.';

-- Dump dei dati della tabella pat.rel_modules_proceedings: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_normatives_structures
CREATE TABLE IF NOT EXISTS `rel_normatives_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_normatives_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `typology` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli uffici associati alla normativa.';

-- Dump dei dati della tabella pat.rel_normatives_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_notices_acts_contests_acts
CREATE TABLE IF NOT EXISTS `rel_notices_acts_contests_acts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_notices_acts_id` int(11) DEFAULT NULL,
  `object_contests_acts_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione Atti delle Amministrazioni e Bandi di Gara';

-- Dump dei dati della tabella pat.rel_notices_acts_contests_acts: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_notice_acts_public_in
CREATE TABLE IF NOT EXISTS `rel_notice_acts_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notice_act_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in per gli atti delle amministrazioni';

-- Dump dei dati della tabella pat.rel_notice_acts_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_assignments
CREATE TABLE IF NOT EXISTS `rel_personnel_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `object_assignments_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire gli incarichi';

-- Dump dei dati della tabella pat.rel_personnel_assignments: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_company
CREATE TABLE IF NOT EXISTS `rel_personnel_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `object_company_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i rappresentanti della società.';

-- Dump dei dati della tabella pat.rel_personnel_company: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_for_structures
CREATE TABLE IF NOT EXISTS `rel_personnel_for_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `typology` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture di cui il personale è referente.';

-- Dump dei dati della tabella pat.rel_personnel_for_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_measures
CREATE TABLE IF NOT EXISTS `rel_personnel_measures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `object_measure_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra il personale e i provvedimenti';

-- Dump dei dati della tabella pat.rel_personnel_measures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_political_organ
CREATE TABLE IF NOT EXISTS `rel_personnel_political_organ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `political_organ_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella di relazione tra il personale e gli organi di indirizzo politico';

-- Dump dei dati della tabella pat.rel_personnel_political_organ: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_personnel_public_in
CREATE TABLE IF NOT EXISTS `rel_personnel_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_personnel_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rel_personnel_public_in_id_uindex` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in del personale';

-- Dump dei dati della tabella pat.rel_personnel_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_proceedings_normatives
CREATE TABLE IF NOT EXISTS `rel_proceedings_normatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int(11) NOT NULL,
  `object_normatives_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le normative associate al procedimento.';

-- Dump dei dati della tabella pat.rel_proceedings_normatives: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_proceedings_personnel
CREATE TABLE IF NOT EXISTS `rel_proceedings_personnel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int(11) NOT NULL,
  `object_personnel_id` int(11) NOT NULL,
  `typology` varchar(45) DEFAULT NULL COMMENT 'utilizzato per gestire le varie tipologie di personale, es: referente prvinciale, procedurale ecc...',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i vari referenti e il personale associati al procedimento, tramite il campo typology.';

-- Dump dei dati della tabella pat.rel_proceedings_personnel: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_proceedings_structures
CREATE TABLE IF NOT EXISTS `rel_proceedings_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_proceedings_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `typology` varchar(45) DEFAULT NULL COMMENT 'utilizzato per gestire le varie tipologie di uffici.',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate al procedimento(ufficio, ufficio_def) tramite il campo typology.';

-- Dump dei dati della tabella pat.rel_proceedings_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_real_estate_asset_structures
CREATE TABLE IF NOT EXISTS `rel_real_estate_asset_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_real_estate_asset_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzato per gestire gli ufficio_utilizzatore associati al patrimoni immobiliare.';

-- Dump dei dati della tabella pat.rel_real_estate_asset_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_regulations_proceedings
CREATE TABLE IF NOT EXISTS `rel_regulations_proceedings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_regulations_id` int(11) NOT NULL,
  `object_proceedings_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i procedimenti associati al regolamento.';

-- Dump dei dati della tabella pat.rel_regulations_proceedings: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_regulations_public_in
CREATE TABLE IF NOT EXISTS `rel_regulations_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_regulation_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in dei regolamenti';

-- Dump dei dati della tabella pat.rel_regulations_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_regulations_structures
CREATE TABLE IF NOT EXISTS `rel_regulations_structures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_regulations_id` int(11) NOT NULL,
  `object_structures_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire le strutture associate ai regolamenti.';

-- Dump dei dati della tabella pat.rel_regulations_structures: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_relief_check_public_in
CREATE TABLE IF NOT EXISTS `rel_relief_check_public_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relief_check_id` int(11) NOT NULL,
  `public_in_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per la gestione del pubblica in dei controlli e rilievi';

-- Dump dei dati della tabella pat.rel_relief_check_public_in: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_supplie_list
CREATE TABLE IF NOT EXISTS `rel_supplie_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_supplie_list_id` int(11) NOT NULL,
  `object_related_supplie_list` int(11) NOT NULL,
  `typology` varchar(45) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire mandante, mandataria, associata, capogruppo, consorziata.';

-- Dump dei dati della tabella pat.rel_supplie_list: ~0 rows (circa)

-- Dump della struttura di tabella pat.rel_users_acl_profiles
CREATE TABLE IF NOT EXISTS `rel_users_acl_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `acl_profile_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Utilizzata per gestire i profili associati agli utenti.';

-- Dump dei dati della tabella pat.rel_users_acl_profiles: ~1 rows (circa)
INSERT INTO `rel_users_acl_profiles` (`id`, `user_id`, `acl_profile_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '2022-04-06 15:43:31', '2022-04-06 15:43:31');

-- Dump della struttura di tabella pat.report_publication
CREATE TABLE IF NOT EXISTS `report_publication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1',
  `owner_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella per i destinatari del report sulle pubblicazioni';

-- Dump dei dati della tabella pat.report_publication: ~0 rows (circa)

-- Dump della struttura di tabella pat.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `political` tinyint(4) DEFAULT '0' COMMENT 'Indica se è un ruolo con comportamento da incaricato politico',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.role: 12 rows
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `name`, `political`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Dipendente', 0, NULL, NULL, NULL),
	(2, 'P.O.', 0, NULL, NULL, NULL),
	(3, 'Funzionario', 0, NULL, NULL, NULL),
	(4, 'Dirigente', 0, NULL, NULL, NULL),
	(5, 'Commissario', 0, NULL, NULL, NULL),
	(6, 'Sub Commissario', 0, NULL, NULL, NULL),
	(7, 'Direttore Generale', 1, NULL, NULL, NULL),
	(8, 'Presidente del Collegio dei revisori dei conti', 1, NULL, NULL, NULL),
	(9, 'Componente del Collegio dei revisori dei conti', 1, NULL, NULL, NULL),
	(10, 'Componente Comitato indirizzo', 0, NULL, NULL, NULL),
	(11, 'Presidente OIV', 0, NULL, NULL, NULL),
	(12, 'Componente OIV', 1, NULL, NULL, NULL);
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

-- Dump della struttura di tabella pat.section_bo
CREATE TABLE IF NOT EXISTS `section_bo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `addon_id` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `lineage` text CHARACTER SET utf8,
  `deep` int(255) DEFAULT NULL,
  `search_sort` int(11) DEFAULT NULL,
  `sort` int(255) DEFAULT NULL,
  `controller` varchar(191) CHARACTER SET utf8 NOT NULL,
  `model` int(11) DEFAULT NULL COMMENT 'Corrispondenza con il file modelConfigs.php',
  `model_class` varchar(191) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Classe del modello',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL,
  `icon` varchar(191) CHARACTER SET utf8 NOT NULL,
  `hidden_profile_acl` tinyint(4) DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `searchable` int(11) DEFAULT '1' COMMENT 'Indica se il motore di ricerca deve cercare o meno in questa sezione',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `hide` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dump dei dati della tabella pat.section_bo: ~39 rows (circa)
INSERT INTO `section_bo` (`id`, `parent_id`, `addon_id`, `name`, `lineage`, `deep`, `search_sort`, `sort`, `controller`, `model`, `model_class`, `url`, `icon`, `hidden_profile_acl`, `deleted_at`, `searchable`, `created_at`, `updated_at`, `hide`, `deleted`) VALUES
	(1, 0, NULL, 'Organizzazione dell\'Ente', '000001', 1, 5, 1, 'control_name', NULL, NULL, '#!', '<i class="fas fa-cog"></i>', 0, NULL, 0, '2021-10-29 09:34:49', NULL, 0, 0),
	(2, 1, NULL, 'Strutture Organizzative', '000001-000002', 2, 5, 1, 'StructureAdminController', 1, NULL, 'admin/structure', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:35:55', NULL, 0, 0),
	(3, 1, NULL, 'Personale', '000001-000003', 2, 5, 2, 'PersonnelAdminController', 2, NULL, 'admin/personnel', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:36:54', NULL, 0, 0),
	(4, 1, NULL, 'Tassi di assenza', '000001-000004', 2, 5, 3, 'AbsenceRatesAdminController', 3, NULL, 'admin/absence-rates', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:39:55', NULL, 0, 0),
	(5, 1, NULL, 'Commissioni e gruppi consiliari', '000001-000005', 2, 5, 4, 'CommissionAdminController', 4, NULL, 'admin/commission', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:40:40', NULL, 0, 0),
	(6, 1, NULL, 'Enti e società controllate', '000001-000006', 2, 5, 5, 'CompanyAdminController', 5, NULL, 'admin/company', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:41:27', NULL, 0, 0),
	(7, 1, NULL, 'Procedimenti', '000001-000007', 2, 4, 6, 'ProceedingAdminController', 6, NULL, 'admin/proceeding', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:42:17', NULL, 0, 0),
	(8, 1, NULL, 'Patrimonio Immobiliare', '000001-000008', 2, 5, 7, 'RealEstateAssetAdminController', 7, NULL, 'admin/real-estate-asset', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:44:03', NULL, 0, 0),
	(9, 1, NULL, 'Canoni di locazione', '000001-000009', 2, 5, 8, 'CanonAdminController', 8, NULL, 'admin/canon', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:44:47', NULL, 0, 0),
	(10, 1, NULL, 'Controlli e rilievi', '000001-000010', 2, 5, 9, 'ReliefCheckAdminController', 9, NULL, 'admin/relief-check', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:47:08', NULL, 0, 0),
	(11, 0, NULL, 'Documenti e Moduli', '000011', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="far fa-folder-open"></i>', 0, NULL, 0, '2021-10-29 09:48:34', NULL, 0, 0),
	(12, 11, NULL, 'Regolamenti e documentazione', '000011-000012', 2, 5, 1, 'RegulationAdminController', 10, NULL, 'admin/regulation', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:50:16', NULL, 0, 0),
	(13, 11, NULL, 'Modulistica', '000011-000013', 2, 5, 2, 'ModuleAdminController', 11, NULL, 'admin/module', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:51:03', NULL, 0, 0),
	(14, 11, NULL, 'Normativa', '000011-000014', 2, 5, 3, 'NormativeAdminController', 12, NULL, 'admin/normative', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:51:40', NULL, 0, 0),
	(15, 11, NULL, 'Bilanci', '000011-000015', 2, 5, 4, 'BalanceAdminController', 13, NULL, 'admin/balance', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:52:21', NULL, 0, 0),
	(16, 0, NULL, 'Atti e pubblicazioni', '000016', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-gavel"></i>', 0, NULL, 0, '2021-10-29 09:53:10', NULL, 0, 0),
	(17, 16, NULL, 'Elenco partecipanti/aggiudicatari', '000016-000017', 2, 5, 1, 'SupplierAdminController', 14, NULL, 'admin/supplier', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 09:54:35', NULL, 0, 0),
	(19, 16, NULL, 'Bandi Gare e Contratti', '000016-000019', 2, 1, 3, 'ContestsActAdminController', 15, NULL, 'admin/contests-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:11:23', NULL, 0, 0),
	(20, 16, NULL, 'Bandi Gare e Contratti - Atti delle amministrazioni', '000016-000020', 2, 5, 4, 'NoticesActAdminController', 16, NULL, 'admin/notices-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:12:17', NULL, 0, 0),
	(22, 16, NULL, 'Atti di programmazione', '000016-000022', 2, 5, 6, 'ProgrammingActAdminController', 18, NULL, 'admin/programming-act', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:21:23', NULL, 0, 0),
	(23, 16, NULL, 'Bandi di Concorso', '000016-000023', 2, 2, 7, 'ContestAdminController', 19, NULL, 'admin/contest', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:22:04', NULL, 0, 0),
	(24, 16, NULL, 'Sovvenzioni e vantaggi economici', '000016-000024', 2, 5, 8, 'GrantAdminController', 20, NULL, 'admin/grant', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:24:08', NULL, 0, 0),
	(25, 16, NULL, 'Incarichi e consulenze', '000016-000025', 2, 5, 9, 'AssignmentAdminController', 21, NULL, 'admin/assignment', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:25:17', NULL, 0, 0),
	(26, 16, NULL, 'Provvedimenti Amministrativi', '000016-000026', 2, 3, 10, 'MeasureAdminController', 22, NULL, 'admin/measure', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:25:59', NULL, 0, 0),
	(27, 16, NULL, 'Oneri informativi e obblighi', '000016-000027', 2, 5, 11, 'ChargeAdminController', 24, NULL, 'admin/charge', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:26:56', NULL, 0, 0),
	(28, 16, NULL, 'Interventi straordinari e di emergenza', '000016-000028', 2, 5, 12, 'InterventionAdminController', 25, NULL, 'admin/intervention', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 10:28:14', NULL, 0, 0),
	(43, 0, NULL, 'Contenuti Trasparenza', '000043', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="far fa-edit"></i>', 0, NULL, 0, '2021-10-29 11:21:08', NULL, 0, 0),
	(44, 43, NULL, 'Pagine generiche', '000043-000044', 2, 5, 1, 'GenericPageAdminController', 23, NULL, 'admin/generic-page', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 11:21:50', NULL, 0, 0),
	(45, 43, NULL, 'Archivio file', '000043-000045', 2, 5, 2, 'FileArchiveAdminController', NULL, NULL, 'admin/file-archive', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:22:16', NULL, 0, 0),
	(48, 0, NULL, 'Contenuti di supporto', '000048', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-bullhorn"></i>', 0, NULL, 0, '2021-10-29 11:30:03', NULL, 0, 0),
	(49, 48, NULL, 'News ed avvisi', '000048-000049', 2, 5, 1, 'NewsNoticeAdminController', 26, NULL, 'admin/news-notice', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2021-10-29 11:30:48', NULL, 0, 0),
	(50, 0, NULL, 'Report e log', '000050', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="far fa-chart-bar"></i>', 0, NULL, 0, '2021-10-29 11:31:50', NULL, 0, 0),
	(51, 50, NULL, 'Log delle attività', '000050-000051', 2, 5, 1, 'ActivityLogAdminController', NULL, NULL, 'admin/activity-log', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:32:48', NULL, 0, 0),
	(52, 50, NULL, 'Log utenti', '000050-000052', 2, 5, 2, 'UserLogAdminController', NULL, NULL, 'admin/user-log', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:32:48', NULL, 0, 0),
	(53, 0, NULL, 'Impostazioni', '000053', 1, 5, 1, 'controller_name', NULL, NULL, '#!', '<i class="fas fa-cogs"></i>', 0, NULL, 0, '2021-10-29 11:33:40', NULL, 0, 0),
	(54, 53, NULL, 'Utenti', '000053-000054', 2, 5, 1, 'UsersAdminController', 39, NULL, 'admin/user', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:34:45', NULL, 0, 0),
	(55, 53, NULL, 'Profili ACL', '000053-000055', 2, 5, 2, 'AclUsersProfileAdminController', 27, NULL, 'admin/acl-users-profile', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:35:19', NULL, 0, 0),
	(56, 53, NULL, 'Ente', '000053-000056', 2, 5, 3, 'InstitutionAdminController', NULL, NULL, 'admin/institution', '<i class="fas fa-angle-right"></i>', 0, NULL, 0, '2021-10-29 11:36:03', NULL, 0, 0),
	(61, 50, NULL, 'Report di pubblicazione - Elenco destinatari', '000050-61', 2, 5, 2, 'ReportPublicationRecipientsController', NULL, NULL, 'admin/report-publication-recipients', '<i class="fas fa-angle-right"></i>', 0, NULL, 1, '2022-09-23 11:56:11', '2022-09-23 11:56:13', 0, 0);

-- Dump della struttura di tabella pat.section_fo
CREATE TABLE IF NOT EXISTS `section_fo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lineage` text CHARACTER SET utf8,
  `deep` int(255) DEFAULT NULL,
  `sort` int(255) DEFAULT NULL,
  `controller` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller_open_data` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `icon` varchar(191) CHARACTER SET utf8 DEFAULT NULL,
  `last_modification_date` datetime DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) DEFAULT '0' COMMENT 'campo su ex PAT: priorità',
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_system` tinyint(4) DEFAULT '1',
  `default` tinyint(4) DEFAULT '0',
  `tag` int(11) DEFAULT NULL,
  `typology` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guide` mediumtext COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(4) DEFAULT '0',
  `form_filter` text COLLATE utf8mb4_unicode_ci,
  `hide` tinyint(4) DEFAULT '0' COMMENT 'Nasconde la sezione dall''alberatura di gestione delle pagine generiche sul back-office e non la mostra nel front-office',
  `no_required` tinyint(4) DEFAULT '0' COMMENT 'Indica se la pagina non è più soggetta a pubblicazione',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=589 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella Sezioni per pubblicazione pagine standard in front-office.';

-- Dump dei dati della tabella pat.section_fo: ~165 rows (circa)
INSERT INTO `section_fo` (`id`, `institution_id`, `owner_id`, `parent_id`, `name`, `lineage`, `deep`, `sort`, `controller`, `controller_open_data`, `archive_name`, `url`, `icon`, `last_modification_date`, `activation_date`, `expiration_date`, `description`, `order`, `meta_title`, `meta_keywords`, `meta_description`, `is_system`, `default`, `tag`, `typology`, `guide`, `deleted`, `form_filter`, `hide`, `no_required`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, NULL, NULL, 0, 'Disposizioni Generali', '000001', 1, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:53:35', '2021-11-05 09:53:35', '2021-11-05 09:53:35', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Disposizioni generali</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:53:35', NULL, NULL),
	(2, NULL, NULL, 0, 'Organizzazione', '000002', 1, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:55:52', '2021-11-05 09:55:52', '2021-11-05 09:55:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Organizzazione</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:55:52', NULL, NULL),
	(3, NULL, NULL, 0, 'Consulenti e collaboratori', '000003', 1, 3, '\\Http\\Web\\Front\\AssignmentsFrontController@indexConsultantsAndCollaborators', 'OpenDataAssignmentsFrontController@indexConsultantsAndCollaborators', 'assignment', '#!', NULL, '2021-11-05 09:56:23', '2021-11-05 09:56:23', '2021-11-05 09:56:23', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Consulenti e collaboratori</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:23', NULL, NULL),
	(4, NULL, NULL, 0, 'Personale', '000004', 1, 4, '\\Http\\Web\\Front\\PersonnelFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:56:36', '2021-11-05 09:56:36', '2021-11-05 09:56:36', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Personale</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:36', NULL, NULL),
	(5, NULL, NULL, 0, 'Bandi di concorso', '000005', 1, 5, '\\Http\\Web\\Front\\ContestsFrontController', NULL, 'contest', '#!', NULL, '2021-11-05 09:56:51', '2021-11-05 09:56:51', '2021-11-05 09:56:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Bandi di concorso</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:56:51', NULL, NULL),
	(6, NULL, NULL, 0, 'Performance', '000006', 1, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:57:09', '2021-11-05 09:57:09', '2021-11-05 09:57:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Performance</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:57:09', NULL, NULL),
	(7, NULL, NULL, 0, 'Enti controllati', '000007', 1, 7, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:57:24', '2021-11-05 09:57:24', '2021-11-05 09:57:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Enti controllati</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:57:23', NULL, NULL),
	(8, NULL, NULL, 0, 'Attività e procedimenti', '000008', 1, 8, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 09:58:04', '2021-11-05 09:58:04', '2021-11-05 09:58:04', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Attività e procedimenti</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 09:58:04', NULL, NULL),
	(9, NULL, NULL, 0, 'Provvedimenti', '000009', 1, 9, '\\Http\\Web\\Front\\MeasuresFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:58:26', '2021-11-05 09:58:26', '2021-11-05 09:58:26', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Provvedimenti</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:58:26', NULL, NULL),
	(10, NULL, NULL, 0, 'Bandi di gara e contratti', '000010', 1, 11, '\\Http\\Web\\Front\\BdncpProcedureFrontController@index', 'OpenDataBdncpProcedureFrontController@index', NULL, '#!', NULL, '2021-11-05 09:58:46', '2021-11-05 09:58:46', '2021-11-05 09:58:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>La pagina &quot;<strong>Bandi di gara e contratti</strong>&quot; &egrave; una sezione snodo usata per organizzare altri contenuti.</p>\r\n\r\n<p>Operazioni consigliate</p>\r\n\r\n<p>Non &egrave; necessario editare alcun contenuto per le pagine di snodo.</p>\r\n', 0, NULL, 0, 0, '2021-11-05 09:58:46', NULL, NULL),
	(11, NULL, NULL, 0, 'Sovvenzioni, Contributi, Sussidi, Vantaggi economici', '000011', 1, 12, '\\Http\\Web\\Front\\GrantsFrontController@pivot', NULL, NULL, '#!', NULL, '2021-11-05 09:59:46', '2021-11-05 09:59:46', '2021-11-05 09:59:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p style="color:#646464;">La pagina "<strong>Sovvenzioni, contributi, sussidi, vantaggi economici</strong>" è una sezione snodo usata per organizzare altri contenuti.</p><h5 class="subtitle">Operazioni consigliate</h5><p style="color:#646464;">Non è necessario editare alcun contenuto per le pagine di snodo.</p>', 0, NULL, 0, 0, '2021-11-05 09:59:46', NULL, NULL),
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
	(37, NULL, NULL, 2, 'Titolari di incarichi politici, di amministrazione, di direzione o di governo', '000002-000037', 2, 2, '\\Http\\Web\\Front\\PersonnelFrontController@indexPositionHolders', NULL, 'personnel', '#!', NULL, '2021-11-05 10:12:06', '2021-11-05 10:12:06', '2021-11-05 10:12:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '<p>Guida</p>\r\n', 0, NULL, 0, 0, '2021-11-05 10:12:06', NULL, NULL),
	(38, NULL, NULL, 2, 'Sanzioni per mancata comunicazione dei dati', '000002-000038', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:12:24', '2021-11-05 10:12:24', '2021-11-05 10:12:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:24', NULL, NULL),
	(39, NULL, NULL, 2, 'Rendiconti gruppi consiliari regionali/provinciali', '000002-000039', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:12:42', '2021-11-05 10:12:42', '2021-11-05 10:12:42', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:42', NULL, NULL),
	(40, NULL, NULL, 2, 'Articolazione degli uffici', '000002-000040', 2, 5, '\\Http\\Web\\Front\\StructuresFrontController@index', 'OpenDataStructuresFrontController@index', 'structure', '#!', NULL, '2021-11-05 10:12:51', '2021-11-05 10:12:51', '2021-11-05 10:12:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:12:51', NULL, NULL),
	(41, NULL, NULL, 40, 'Organigramma', '000002-000040-000041', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'structure', '#!', NULL, '2021-11-05 10:13:13', '2021-11-05 10:13:13', '2021-11-05 10:13:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:13:13', NULL, NULL),
	(42, NULL, NULL, 40, 'Archivio', '000002-000040-000042', 3, 2, '\\Http\\Web\\Front\\PivotController', NULL, 'structure', '#!', NULL, '2021-11-05 10:13:21', '2021-11-05 10:13:21', '2021-11-05 10:13:21', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:13:21', NULL, NULL),
	(43, NULL, NULL, 2, 'Telefono e posta elettronica', '000002-000043', 2, 6, '\\Http\\Web\\Front\\StructuresFrontController@indexPhoneMail', 'OpenDataStructuresFrontController@index', 'structure', '#!', NULL, '2021-11-05 10:13:38', '2021-11-05 10:13:38', '2021-11-05 10:13:38', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:13:38', NULL, NULL),
	(44, NULL, NULL, 43, 'Posta elettronica certificata', '000002-000043-000044', 3, 1, '\\Http\\Web\\Front\\StructuresFrontController@indexCertifiedMail', 'OpenDataStructuresFrontController@indexCertifiedMail', 'structure', '#!', NULL, '2021-11-05 10:14:09', '2021-11-05 10:14:09', '2021-11-05 10:14:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:14:09', NULL, NULL),
	(46, NULL, NULL, 3, 'Titolari di incarichi di collaborazione o consulenza', '000003-000046', 2, 2, '\\Http\\Web\\Front\\AssignmentsFrontController@indexOfficeHolders', 'OpenDataAssignmentsFrontController@indexOfficeHolders', 'assignment', '#!', NULL, '2021-11-05 10:14:54', '2021-11-05 10:14:54', '2021-11-05 10:14:54', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:14:54', NULL, NULL),
	(47, NULL, NULL, 46, 'Archivio incarichi di collaborazione o consulenza', '000003-000046-000047', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'assignment', '#!', NULL, '2021-11-05 10:15:14', '2021-11-05 10:15:14', '2021-11-05 10:15:14', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:15:14', NULL, NULL),
	(50, NULL, NULL, 3, 'Collegio dei Revisori dei Conti', '000003-000050', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:16:14', '2021-11-05 10:16:14', '2021-11-05 10:16:14', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:16:14', NULL, NULL),
	(58, NULL, NULL, 4, 'Titolari di incarichi dirigenziali amministrativi di vertice', '000004-000058', 2, 2, '\\Http\\Web\\Front\\PersonnelFrontController@indexTopPositions', 'OpenDataPersonnelFrontController@indexTopPositions', 'personnel', '#!', NULL, '2021-11-05 10:22:09', '2021-11-05 10:22:09', '2021-11-05 10:22:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:22:09', NULL, NULL),
	(59, NULL, NULL, 58, 'Segretario Generale', '000004-000058-000059', 3, 1, '\\Http\\Web\\Front\\PersonnelFrontController@indexGeneralSecretary', 'OpenDataPersonnelFrontController@indexGeneralSecretary', 'personnel', '#!', NULL, '2021-11-05 10:22:37', '2021-11-05 10:22:37', '2021-11-05 10:22:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:22:37', NULL, NULL),
	(60, NULL, NULL, 4, 'Titolari di incarichi dirigenziali (dirigenti non generali)', '000004-000060', 2, 3, '\\Http\\Web\\Front\\PersonnelFrontController@indexManagerialPositions', 'OpenDataPersonnelFrontController@indexManagerialPositions', 'personnel', '#!', NULL, '2021-11-05 10:23:03', '2021-11-05 10:23:03', '2021-11-05 10:23:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:03', NULL, NULL),
	(61, NULL, NULL, 4, 'Dirigenti cessati', '000004-000061', 2, 4, '\\Http\\Web\\Front\\PersonnelFrontController@indexExecutivesTerminated', NULL, 'personnel', '#!', NULL, '2021-11-05 10:23:12', '2021-11-05 10:23:12', '2021-11-05 10:23:12', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:23:12', NULL, NULL),
	(62, NULL, NULL, 4, 'Sanzioni per mancata comunicazione dei dati', '000004-000062', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:23:21', '2021-11-05 10:23:21', '2021-11-05 10:23:21', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:21', NULL, NULL),
	(63, NULL, NULL, 4, 'Posizioni organizzative', '000004-000063', 2, 6, '\\Http\\Web\\Front\\PersonnelFrontController@indexOrganisationalPositions', 'OpenDataPersonnelFrontController@indexOrganisationalPositions', 'personnel', '#!', NULL, '2021-11-05 10:23:32', '2021-11-05 10:23:32', '2021-11-05 10:23:32', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:32', NULL, NULL),
	(64, NULL, NULL, 4, 'Dotazione organica', '000004-000064', 2, 7, '\\Http\\Web\\Front\\PersonnelFrontController@indexStaffing', 'OpenDataPersonnelFrontController@indexStaffing', 'personnel', '#!', NULL, '2021-11-05 10:23:51', '2021-11-05 10:23:51', '2021-11-05 10:23:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:23:51', NULL, NULL),
	(65, NULL, NULL, 4, 'Personale non a tempo indeterminato', '000004-000065', 2, 8, '\\Http\\Web\\Front\\PersonnelFrontController@indexNotIndefinite', 'OpenDataPersonnelFrontController@indexNotIndefinite', 'personnel', '#!', NULL, '2021-11-05 10:24:00', '2021-11-05 10:24:00', '2021-11-05 10:24:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:24:00', NULL, NULL),
	(66, NULL, NULL, 4, 'Tassi di assenza', '000004-000066', 2, 9, '\\Http\\Web\\Front\\AbsenceRatesFrontController@index', 'OpenDataAbsenceRatesFrontController@index', 'absence-rates', '#!', NULL, '2021-11-05 10:24:08', '2021-11-05 10:24:08', '2021-11-05 10:24:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, 's:980:"[{"type":"form_open","name":"absence_rates","action":"{current_url}","attributes":{"method":"get"}},{"prefix":"<div class=\\"form-row mb-4\\"><div class=\\"col-lg-7\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{structures}","name":"structures","extra":"id=\\"structures\\"","label":{"for":"t","text":"Struttura da cercare","class":"active"}},{"prefix":"<div class=\\"col\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{years}","name":"start","extra":"id=\\"start_date\\"","label":{"for":"testo_form","text":"Anno","class":"active"}},{"prefix":"<div class=\\"col\\">","suffix":"<\\/div><\\/div>","type":"form_dropdown","options":"{months}","name":"months","extra":"id=\\"months\\"","label":{"for":"testo_form","text":"Mese","class":"active"}},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 10:24:08', NULL, NULL),
	(67, NULL, NULL, 4, 'Incarichi conferiti e autorizzati ai dipendenti (dirigenti e non dirigenti)', '000004-000067', 2, 10, '\\Http\\Web\\Front\\AssignmentsFrontController@indexExecutivesNonExecutives', 'OpenDataAssignmentsFrontController@indexExecutivesNonExecutives', 'assignment', '#!', NULL, '2021-11-05 10:24:17', '2021-11-05 10:24:17', '2021-11-05 10:24:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:24:17', NULL, NULL),
	(68, NULL, NULL, 67, 'Archivio incarichi dipendenti', '000004-000067-000068', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:24:37', '2021-11-05 10:24:37', '2021-11-05 10:24:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:24:37', NULL, NULL),
	(69, NULL, NULL, 4, 'Contrattazione collettiva', '000004-000069', 2, 11, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:01', '2021-11-05 10:25:01', '2021-11-05 10:25:01', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:01', NULL, NULL),
	(70, NULL, NULL, 4, 'Contrattazione integrativa', '000004-000070', 2, 12, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:22', '2021-11-05 10:25:22', '2021-11-05 10:25:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:22', NULL, NULL),
	(71, NULL, NULL, 4, 'OIV', '000004-000071', 2, 13, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:31', '2021-11-05 10:25:31', '2021-11-05 10:25:31', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:25:31', NULL, NULL),
	(72, NULL, NULL, 4, 'Elenco del personale iscritto nel registro dei revisori dei conti', '000004-000072', 2, 14, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:25:39', '2021-11-05 10:25:39', '2021-11-05 10:25:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:25:39', NULL, NULL),
	(73, NULL, NULL, 4, 'Archivio personale', '000004-000073', 2, 15, '\\Http\\Web\\Front\\PivotController', NULL, 'personnel', '#!', NULL, '2021-11-05 10:25:48', '2021-11-05 10:25:48', '2021-11-05 10:25:48', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:25:48', NULL, NULL),
	(75, NULL, NULL, 5, 'Concorsi attivi', '000005-000075', 2, 1, '\\Http\\Web\\Front\\ContestsFrontController@indexActive', 'OpenDataContestsFrontController@indexActive', 'contest', '#!', NULL, '2021-11-05 10:26:25', '2021-11-05 10:26:25', '2021-11-05 10:26:25', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:25', NULL, NULL),
	(76, NULL, NULL, 5, 'Concorsi scaduti', '000005-000076', 2, 2, '\\Http\\Web\\Front\\ContestsFrontController@indexExpired', 'OpenDataContestsFrontController@indexExpired', 'contest', '#!', NULL, '2021-11-05 10:26:35', '2021-11-05 10:26:35', '2021-11-05 10:26:35', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:35', NULL, NULL),
	(77, NULL, NULL, 5, 'Avvisi', '000005-000077', 2, 4, '\\Http\\Web\\Front\\ContestsFrontController@indexAlert', 'OpenDataContestsFrontController@indexAlert', 'contest', '#!', NULL, '2021-11-05 10:26:47', '2021-11-05 10:26:47', '2021-11-05 10:26:47', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:47', NULL, NULL),
	(78, NULL, NULL, 5, 'Esiti', '000005-000078', 2, 5, '\\Http\\Web\\Front\\ContestsFrontController@indexResult', 'OpenDataContestsFrontController@indexResult', 'contest', '#!', NULL, '2021-11-05 10:26:54', '2021-11-05 10:26:54', '2021-11-05 10:26:54', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:26:54', NULL, NULL),
	(82, NULL, NULL, 6, 'Sistema di misurazione e valutazione della Performance', '000006-000082', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:09', '2021-11-05 10:28:09', '2021-11-05 10:28:09', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:09', NULL, NULL),
	(83, NULL, NULL, 6, 'Piano della Performance', '000006-000083', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:17', '2021-11-05 10:28:17', '2021-11-05 10:28:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:17', NULL, NULL),
	(84, NULL, NULL, 6, 'Relazione sulla Performance', '000006-000084', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:26', '2021-11-05 10:28:26', '2021-11-05 10:28:26', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:26', NULL, NULL),
	(85, NULL, NULL, 6, 'Ammontare complessivo dei premi', '000006-000085', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:41', '2021-11-05 10:28:41', '2021-11-05 10:28:41', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:41', NULL, NULL),
	(86, NULL, NULL, 6, 'Dati relativi ai premi', '000006-000086', 2, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:28:50', '2021-11-05 10:28:50', '2021-11-05 10:28:50', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:28:50', NULL, NULL),
	(89, NULL, NULL, 7, 'Enti pubblici vigilati', '000007-000089', 2, 2, '\\Http\\Web\\Front\\CompanyFrontController@indexPublicEntities', 'OpenDataCompanyFrontController@indexPublicEntities', 'company', '#!', NULL, '2021-11-05 10:29:22', '2021-11-05 10:29:22', '2021-11-05 10:29:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:29:22', NULL, NULL),
	(91, NULL, NULL, 7, 'Società partecipate', '000007-000091', 2, 3, '\\Http\\Web\\Front\\CompanyFrontController@indexParticipatedCompanies', 'OpenDataCompanyFrontController@indexParticipatedCompanies', 'company', '#!', NULL, '2021-11-05 10:30:06', '2021-11-05 10:30:06', '2021-11-05 10:30:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:30:06', NULL, NULL),
	(92, NULL, NULL, 91, 'Archivio', '000007-000091-000092', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:30:25', '2021-11-05 10:30:25', '2021-11-05 10:30:25', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:30:25', NULL, NULL),
	(93, NULL, NULL, 7, 'Enti di diritto privato controllati', '000007-000093', 2, 4, '\\Http\\Web\\Front\\CompanyFrontController@indexControlledPrivateEntities', 'OpenDataCompanyFrontController@indexControlledPrivateEntities', 'company', '#!', NULL, '2021-11-05 10:30:40', '2021-11-05 10:30:40', '2021-11-05 10:30:40', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:30:40', NULL, NULL),
	(95, NULL, NULL, 7, 'Rappresentazione grafica', '000007-000095', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:31:10', '2021-11-05 10:31:10', '2021-11-05 10:31:10', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:31:10', NULL, NULL),
	(98, NULL, NULL, 8, 'Tipologie di procedimento', '000008-000098', 2, 3, '\\Http\\Web\\Front\\ProceedingsFrontController@indexProceedingsType', 'OpenDataProceedingFrontController@indexProceedingsType', 'proceeding', '#!', NULL, '2021-11-05 10:31:53', '2021-11-05 10:31:53', '2021-11-05 10:31:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:31:53', NULL, NULL),
	(99, NULL, NULL, 98, 'Archivio procedimenti', '000008-000098-000099', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:32:13', '2021-11-05 10:32:13', '2021-11-05 10:32:13', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:32:13', NULL, NULL),
	(101, NULL, NULL, 8, 'Dichiarazioni sostitutive e acquisizione d\'ufficio dei dati', '000008-000101', 2, 5, '\\Http\\Web\\Front\\ModuleFrontController@indexSubstituteDeclarations', 'OpenDataModulisticsFrontController@indexSubstituteDeclarations', 'module', '#!', NULL, '2021-11-05 10:32:46', '2021-11-05 10:32:46', '2021-11-05 10:32:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:32:46', NULL, NULL),
	(103, NULL, NULL, 9, 'Provvedimenti organi indirizzo politico', '000009-000103', 2, 2, '\\Http\\Web\\Front\\MeasuresFrontController@indexPolitical', 'OpenDataMeasuresFrontController@indexPolitical', 'measure', '#!', NULL, '2021-11-05 10:33:16', '2021-11-05 10:33:16', '2021-11-05 10:33:16', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:33:16', NULL, NULL),
	(104, NULL, NULL, 103, 'Determinazione del Presidente del Consiglio', '000009-000103-000104', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:33:53', '2021-11-05 10:33:53', '2021-11-05 10:33:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:33:53', NULL, NULL),
	(105, NULL, NULL, 103, 'Deliberazione dell\'Ufficio di presidenza', '000009-000103-000105', 3, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:34:08', '2021-11-05 10:34:08', '2021-11-05 10:34:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:34:08', NULL, NULL),
	(106, NULL, NULL, 9, 'Provvedimenti dirigenti amministrativi', '000009-000106', 2, 3, '\\Http\\Web\\Front\\MeasuresFrontController@indexAdministrative', 'OpenDataMeasuresFrontController@indexAdministrative', 'measure', '#!', NULL, '2021-11-05 10:34:34', '2021-11-05 10:34:34', '2021-11-05 10:34:34', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:34:34', NULL, NULL),
	(107, NULL, NULL, 106, 'Accordi con soggetti privati o altre P.A.', '000009-000106-000107', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:35:17', '2021-11-05 10:35:17', '2021-11-05 10:35:17', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:35:17', NULL, NULL),
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
	(154, NULL, NULL, 16, 'Dati sui pagamenti', '000016-000154', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 10:56:05', '2021-11-05 10:56:05', '2021-11-05 10:56:05', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 10:56:05', NULL, NULL),
	(155, NULL, NULL, 154, 'Pagamenti di Sovvenzioni, contributi, sussidi, vantaggi economici', '000016-000154-000155', 3, 1, '\\Http\\Web\\Front\\GrantsFrontController@indexLiquidation', 'OpenDataGrantsFrontController@indexLiquidation', 'grant', '#!', NULL, '2021-11-05 10:57:06', '2021-11-05 10:57:06', '2021-11-05 10:57:06', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:57:06', NULL, NULL),
	(156, NULL, NULL, 154, 'Pagamenti di Consulenti e collaboratori', '000016-000154-000156', 3, 2, '\\Http\\Web\\Front\\AssignmentsFrontController@indexPaymentsConsultantsCollaborators', 'OpenDataAssignmentsFrontController@indexPaymentsConsultantsCollaborators', 'assignment', '#!', NULL, '2021-11-05 11:00:53', '2021-11-05 11:00:53', '2021-11-05 11:00:53', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, 's:1034:"[{"type":"form_open","name":"absence_rates","action":"{current_url}","attributes":{"method":"get"}},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"fn","id":"full_name","placeholder":"Qualunque","value":"{full_name}"},"label":{"for":"fn","text":"Cognome e nome"}},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"object","id":"object","placeholder":"Qualunque","value":"{object}"},"label":{"for":"object","text":"Oggetto"}},{"prefix":"<div class=\\"col-lg-4\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{year}","name":"year","extra":"id=\\"year\\"","label":{"for":"year","text":"Anno","class":"active"}},{"type":"tag","content":"<\\/div>"},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 11:00:53', NULL, NULL),
	(157, NULL, NULL, 154, 'Pagamenti di Bandi di gara e contratti fino al 31/12/2023', '000016-000154-000157', 3, 3, '\\Http\\Web\\Front\\ContestsActsFrontController@indexPaymentsContestsActs', 'OpenDataContestActsFrontController@indexPaymentsContestsActs', 'contests-act', '#!', NULL, '2021-11-05 11:01:46', '2021-11-05 11:01:46', '2021-11-05 11:01:46', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, 's:2118:"[{"type":"form_open","name":"contest_acts","action":"{current_url}","attributes":{"method":"get"}},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{structures}","name":"structures","extra":"id=\\"structures\\"","label":{"for":"s","text":"Struttura da cercare","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"object","value":"{object}","id":"object","class":"class_form","placeholder":"Qualunque"},"label":{"for":"object","text":"Oggetto","class":"active"}},{"type":"tag","content":"<\\/div>"},{"type":"tag","content":"<div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_dropdown","options":"{contraent}","name":"contraent","extra":"id=\\"contraent\\"","label":{"for":"c","text":"Scelta del contraente","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"cig","value":"{cig}","id":"cig","class":"class_form","placeholder":"Qualunque"},"label":{"for":"cig","text":"Codice CIG","class":"active"}},{"type":"tag","content":"<\\/div>"},{"type":"tag","content":"<label>Data di pubblicazione<\\/label><div class=\\"form-row mb-4\\">"},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"start_p_date","value":"{start_p_date}","type":"date","id":"start_p_date","class":"class_form","placeholder":"Qualunque"},"label":{"for":"start_p_date","text":"dal","class":"active"}},{"prefix":"<div class=\\"col-lg-6\\">","suffix":"<\\/div>","type":"form_input","attributes":{"name":"end_p_date","value":"{end_p_date}","type":"date","id":"end_p_date","class":"class_form","placeholder":"Qualunque"},"label":{"for":"end_p_date","text":"al","class":"active"}},{"type":"tag","content":"<\\/div>"},{"prefix":"<div class=\\"form-row\\">","suffix":"<\\/div>","type":"form_button","attributes":{"class":"btn btn-primary","name":"search","type":"submit","content":"<span class=\\"fas fa-search\\"><\\/span> Cerca"}},{"type":"form_close"}]";', 0, 0, '2021-11-05 11:01:46', NULL, NULL),
	(158, NULL, NULL, 16, 'Dati sui pagamenti del servizio sanitario nazionale', '000016-000158', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:02:39', '2021-11-05 11:02:39', '2021-11-05 11:02:39', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:02:39', NULL, NULL),
	(159, NULL, NULL, 16, 'Indicatore di tempestività dei pagamenti', '000016-000159', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:04:52', '2021-11-05 11:04:52', '2021-11-05 11:04:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:04:52', NULL, NULL),
	(160, NULL, NULL, 159, 'Ammontare complessivo dei debiti e numero delle imprese creditrici', '000016-000159-000160', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:05:37', '2021-11-05 11:05:37', '2021-11-05 11:05:37', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:05:37', NULL, NULL),
	(161, NULL, NULL, 16, 'IBAN e pagamenti informatici', '000016-000161', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:06:03', '2021-11-05 11:06:03', '2021-11-05 11:06:03', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:06:03', NULL, NULL),
	(166, NULL, NULL, 17, 'Nuclei di valutazione e verifica degli investimenti pubblici', '000017-000166', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:08:41', '2021-11-05 11:08:41', '2021-11-05 11:08:41', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:08:41', NULL, NULL),
	(167, NULL, NULL, 17, 'Atti di programmazione delle opere pubbliche', '000017-000167', 2, 3, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexActsPublicWorks', 'OpenDataProgrammingActFrontController@indexActsPublicWorks', 'programming-act', '#!', NULL, '2021-11-05 11:08:52', '2021-11-05 11:08:52', '2021-11-05 11:08:52', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:08:52', NULL, NULL),
	(168, NULL, NULL, 17, 'Tempi costi e indicatori di realizzazione delle opere pubbliche', '000017-000168', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:09:01', '2021-11-05 11:09:01', '2021-11-05 11:09:01', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:09:01', NULL, NULL),
	(174, NULL, NULL, 18, 'Prevenzione della Corruzione', '000018-000174', 2, 2, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:07', '2021-11-05 11:10:07', '2021-11-05 11:10:07', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:07', NULL, NULL),
	(175, NULL, NULL, 18, 'Accesso Civico', '000018-000175', 2, 3, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:15', '2021-11-05 11:10:15', '2021-11-05 11:10:15', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:15', NULL, NULL),
	(176, NULL, NULL, 18, 'Accessibilità e Catalogo dei dati, metadati e banche dati', '000018-000176', 2, 4, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:24', '2021-11-05 11:10:24', '2021-11-05 11:10:24', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:24', NULL, NULL),
	(177, NULL, NULL, 18, 'Dati ulteriori', '000018-000177', 2, 5, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:34', '2021-11-05 11:10:34', '2021-11-05 11:10:34', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:34', NULL, NULL),
	(178, NULL, NULL, 18, 'Spese di rappresentanza', '000018-000178', 2, 6, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:42', '2021-11-05 11:10:42', '2021-11-05 11:10:42', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:10:42', NULL, NULL),
	(179, NULL, NULL, 18, 'Elezioni trasparenti', '000018-000179', 2, 7, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:10:51', '2021-11-05 11:10:51', '2021-11-05 11:10:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:51', NULL, NULL),
	(180, NULL, NULL, 18, 'Avvisi e News', '000018-000180', 2, 8, '\\Http\\Web\\Front\\NewsNoticeFrontController', 'OpenDataNewsNoticeFrontController@index', 'news-notice', '#!', NULL, '2021-11-05 11:10:59', '2021-11-05 11:10:59', '2021-11-05 11:10:59', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:10:59', NULL, NULL),
	(181, NULL, NULL, 182, 'Cookie policy', '000019-000182-000181', 2, 9, '\\Http\\Web\\Front\\OtherSectionsFrontController@cookie', NULL, NULL, '#!', NULL, '2021-11-05 11:11:22', '2021-11-05 11:11:22', '2021-11-05 11:11:22', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:22', NULL, NULL),
	(182, NULL, NULL, 19, 'Privacy', '000019-000182', 2, 1, '\\Http\\Web\\Front\\OtherSectionsFrontController@privacy', NULL, NULL, '#!', NULL, '2021-11-05 11:11:44', '2021-11-05 11:11:44', '2021-11-05 11:11:44', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:44', NULL, NULL),
	(183, NULL, NULL, 0, 'Controlli sulle imprese', '000183', 1, 10, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:11:51', '2021-11-05 11:11:51', '2021-11-05 11:11:51', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 11:11:51', NULL, NULL),
	(184, NULL, NULL, 0, 'Pianificazione e governo del territorio', '000184', 1, 19, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:00', '2021-11-05 11:12:00', '2021-11-05 11:12:00', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:00', NULL, NULL),
	(185, NULL, NULL, 0, 'Informazioni ambientali', '000185', 1, 20, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:08', '2021-11-05 11:12:08', '2021-11-05 11:12:08', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:08', NULL, NULL),
	(186, NULL, NULL, 0, 'Strutture sanitarie private accreditate', '000186', 1, 21, '\\Http\\Web\\Front\\PivotController', NULL, NULL, '#!', NULL, '2021-11-05 11:12:16', '2021-11-05 11:12:16', '2021-11-05 11:12:16', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:16', NULL, NULL),
	(187, NULL, NULL, 0, 'Interventi straordinari e di emergenza', '000187', 1, 22, '\\Http\\Web\\Front\\InterventionFrontController', 'OpenDataInterventionsFrontController@index', 'intervention', '#!', NULL, '2021-11-05 11:12:27', '2021-11-05 11:12:27', '2021-11-05 11:12:27', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, '', 0, NULL, 0, 0, '2021-11-05 11:12:27', NULL, NULL),
	(241, NULL, NULL, 37, 'Collegio dei revisori dei conti', '000002-000037-000241', 3, 4, '\\Http\\Web\\Front\\PersonnelFrontController@indexCityCouncil', 'OpenDataPersonnelFrontController@indexCityCouncil', 'personnel', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
	(243, NULL, NULL, 37, 'Direzione Generale', '000002-000037-000243', 3, 6, '\\Http\\Web\\Front\\PersonnelFrontController@indexGeneralManagement', 'OpenDataPersonnelFrontController@indexGeneralManagement', 'personnel', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
	(244, NULL, NULL, 37, 'Gruppi consiliari', '000002-000037-000244', 3, 7, '\\Http\\Web\\Front\\CommissionFrontController@indexGroup', 'OpenDataCommissionsFrontController@indexGroup', 'commission', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
	(245, NULL, NULL, 37, 'Commissioni', '000002-000037-000245', 3, 8, '\\Http\\Web\\Front\\CommissionFrontController@indexCommission', 'OpenDataCommissionsFrontController@indexCommission', 'commission', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
	(246, NULL, NULL, 37, 'Titolari di incarichi di amministrazione, di direzione o di governo', '000002-000037-000246', 3, 9, '\\Http\\Web\\Front\\PersonnelFrontController@indexHoldersOfAdministrativePositions', 'OpenDataPersonnelFrontController@indexHoldersOfAdministrativePositions', 'personnel', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:53', NULL, NULL),
	(247, NULL, NULL, 37, 'Cessati dall\'incarico', '000002-000037-000247', 3, 10, '\\Http\\Web\\Front\\PersonnelFrontController@indexArchivePositionHolders', NULL, NULL, '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-03-31 13:59:54', NULL, NULL),
	(257, NULL, NULL, 112, 'Avvisi di preinformazione', '000010-000581-000112--000257', 4, 2, '\\Http\\Web\\Front\\ContestsActsFrontController@indexPreinformationNotices', 'OpenDataContestActsFrontController@indexPreinformationNotices', 'contests-act', '#!', NULL, '2021-11-05 10:37:04', '2021-11-05 10:37:04', '2021-11-05 10:37:04', NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2021-11-05 10:37:04', NULL, NULL),
	(297, NULL, NULL, 113, 'Programma biennale degli acquisti di beni e servizi', '000010-000113-000297', 4, 1, '\\Http\\Web\\Front\\ProgrammingActFrontController@indexBiannualProgram', 'OpenDataProgrammingActFrontController@programming', 'programming-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2022-10-05 11:49:59', NULL, NULL),
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
	(581, NULL, NULL, 10, 'Procedure fino al 31/12/2023', '000010-000581', 2, 3, '\\Http\\Web\\Front\\ContestsActsFrontController@pivot', NULL, 'contests-act', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2024-01-13 12:00:31', NULL, NULL),
	(582, NULL, NULL, 580, 'Avviso finalizzato ad acquisire le manifestazioni di interesse degli operatori economici in ordine ai lavori di possibile completamento di opere incompiute nonché alla gestione delle stesse', '000010-000580-000582', 3, 2, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@uncompletedWorksAlert', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(583, NULL, NULL, 580, 'Comunicazione circa la mancata redazione del programma triennale', '000010-000580-000583', 3, 3, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@triennialProgram', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(584, NULL, NULL, 580, 'Atti recanti norme, criteri oggettivi per il funzionamento del sistema di qualificazione, l’eventuale aggiornamento periodico dello stesso e durata, criteri soggettivi (requisiti relativi alle capacità economiche, finanziarie, tecniche e professionali) per l’iscrizione al sistema', '000010-000580-000584', 3, 4, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@qualificationSystemActs', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(585, NULL, NULL, 580, 'Atti eventualmente adottati recanti l\'elencazione delle condotte che costituiscono gravi illeciti professionali agli effetti degli artt. 95, co. 1, lettera e) e 98 (cause di esclusione dalla gara per gravi illeciti professionali)', '000010-000580-000585', 3, 5, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@seriousProfessionalMisconductActs', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(586, NULL, NULL, 580, 'Elenco annuale dei progetti di investimento pubblico finanziati', '000010-000580-000586', 3, 6, '\\Http\\Web\\Front\\GeneralActsDocumentsFrontController@annualListFundedProjects', 'OpenDataGeneralActsDocumentsFrontController@index', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(587, NULL, NULL, 580, 'Elenco delle soluzioni tecnologiche adottate dalle SA e enti concedenti per l\'automatizzazione delle proprie attività', '000010-000580-000587', 3, 1, '\\Http\\Web\\Front\\PivotController', NULL, 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL),
	(588, NULL, NULL, 10, 'Avvisi', '000010-000588', 2, 3, '\\Http\\Web\\Front\\BdncpProcedureFrontController@alerts', 'OpenDataBdncpProcedureFrontController@alerts', 'general-acts-documents', '#!', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 0, 0, '2023-11-15 10:57:21', NULL, NULL);

-- Dump della struttura di tabella pat.section_fo_config_publication_archive
CREATE TABLE IF NOT EXISTS `section_fo_config_publication_archive` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_fo_id` int(11) NOT NULL,
  `archive_name` varchar(60) NOT NULL,
  `deleted` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Tabella utilizzata per gestire il pubblica in';

-- Dump dei dati della tabella pat.section_fo_config_publication_archive: ~52 rows (circa)
INSERT INTO `section_fo_config_publication_archive` (`id`, `section_fo_id`, `archive_name`, `deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(0, 246, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(1, 60, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(2, 63, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(3, 58, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(4, 59, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(5, 238, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(6, 239, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(7, 242, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(8, 241, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(9, 243, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(10, 240, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(11, 245, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(12, 244, 'object_personnel', 0, '2022-04-01 17:28:19', NULL, NULL),
	(13, 114, 'object_notices_acts', 1, '2022-04-05 15:30:38', NULL, '2023-03-03 14:02:55'),
	(14, 115, 'object_notices_acts', 0, '2022-04-05 15:30:42', NULL, NULL),
	(15, 116, 'object_notices_acts', 0, '2022-04-05 15:30:42', NULL, NULL),
	(16, 117, 'object_notices_acts', 0, '2022-04-05 15:30:44', NULL, NULL),
	(17, 139, 'object_relief_checks', 0, '2022-04-05 15:30:44', NULL, NULL),
	(18, 140, 'object_relief_checks', 0, '2022-04-05 15:30:45', NULL, NULL),
	(19, 141, 'object_relief_checks', 0, '2022-04-05 15:30:46', NULL, NULL),
	(20, 142, 'object_relief_checks', 0, '2022-04-05 15:30:46', NULL, NULL),
	(21, 143, 'object_relief_checks', 0, '2022-04-05 15:30:47', NULL, NULL),
	(22, 144, 'object_relief_checks', 0, '2022-04-05 15:31:25', NULL, NULL),
	(23, 145, 'object_relief_checks', 0, '2022-04-05 15:31:58', NULL, NULL),
	(24, 29, 'object_regulations', 0, '2022-04-19 12:34:52', NULL, NULL),
	(25, 27, 'object_regulations', 0, '2022-04-19 12:34:54', NULL, NULL),
	(26, 28, 'object_regulations', 0, '2022-04-19 12:34:55', NULL, NULL),
	(27, 25, 'object_regulations', 0, '2022-04-19 12:34:56', NULL, NULL),
	(28, 26, 'object_regulations', 0, '2022-04-19 12:34:57', NULL, NULL),
	(29, 50, 'object_personnel', 0, '2022-09-08 14:22:04', NULL, NULL),
	(30, 524, 'object_contest_acts_alert', 0, '2022-09-08 14:22:04', NULL, NULL),
	(31, 525, 'object_contest_acts_alert', 0, '2022-09-08 14:22:04', NULL, NULL),
	(32, 526, 'object_contest_acts_alert', 0, '2022-09-08 14:22:04', NULL, NULL),
	(33, 257, 'object_contest_acts_alert', 0, '2022-09-08 14:22:04', NULL, NULL),
	(34, 524, 'object_contest_acts_notice', 0, '2022-09-08 14:22:04', NULL, NULL),
	(35, 525, 'object_contest_acts_notice', 0, '2022-09-08 14:22:04', NULL, NULL),
	(36, 526, 'object_contest_acts_notice', 0, '2022-09-08 14:22:04', NULL, NULL),
	(37, 524, 'object_contest_acts_deliberation', 0, '2022-09-08 14:22:04', NULL, NULL),
	(38, 526, 'object_contest_acts_deliberation', 0, '2022-09-08 14:22:04', NULL, NULL),
	(39, 526, 'object_contest_acts_result', 0, '2022-09-08 14:22:04', NULL, NULL),
	(40, 526, 'object_contest_acts_foster', 0, '2022-09-08 14:22:04', NULL, NULL),
	(41, 525, 'object_contest_acts_foster', 0, '2022-09-08 14:22:04', NULL, NULL),
	(42, 527, 'object_notices_acts', 0, '2022-09-08 14:22:04', NULL, NULL),
	(43, 529, 'object_notices_acts', 0, '2022-04-05 15:30:44', NULL, NULL),
	(44, 530, 'object_notices_acts', 0, '2022-04-05 15:30:44', NULL, NULL),
	(45, 531, 'object_notices_acts', 0, '2022-04-05 15:30:44', NULL, NULL),
	(46, 527, 'object_contest_acts_foster', 0, '2022-09-08 14:22:04', NULL, NULL),
	(55, 582, 'object_bdncp_general_acts_documents', 0, '2022-04-05 15:30:44', NULL, NULL),
	(56, 583, 'object_bdncp_general_acts_documents', 0, '2022-04-05 15:30:44', NULL, NULL),
	(57, 584, 'object_bdncp_general_acts_documents', 0, '2022-04-05 15:30:44', NULL, NULL),
	(58, 585, 'object_bdncp_general_acts_documents', 0, '2022-04-05 15:30:44', NULL, NULL),
	(59, 586, 'object_bdncp_general_acts_documents', 0, '2022-04-05 15:30:44', NULL, NULL);

-- Dump della struttura di tabella pat.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) NOT NULL,
  `data` text,
  `expire` int(11) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dump dei dati della tabella pat.sessions: ~0 rows (circa)

-- Dump della struttura di tabella pat.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL COMMENT 'campo ex PAt:  id_ente_admin',
  `name` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(191) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `spid_code` varchar(45) DEFAULT NULL COMMENT 'campo ex PAt:  codicespid',
  `fiscal_code` varchar(45) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1' COMMENT 'campo ex PAt:  attivo',
  `active_key` varchar(20) DEFAULT NULL COMMENT 'Campo utilizzato per attivazione utente',
  `deleted` tinyint(4) DEFAULT NULL,
  `last_visit` datetime DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `super_admin` tinyint(1) DEFAULT '0' COMMENT 'campo ex PAt:  permessi',
  `admin` tinyint(1) DEFAULT '0',
  `prevent_password_repetition` tinyint(1) DEFAULT '0' COMMENT 'campo impedisci_ripetizioni_password su vecchio PAT.',
  `prevent_password_repetition_6_months` tinyint(1) DEFAULT '0',
  `password_expiration_days` varchar(191) DEFAULT NULL COMMENT 'campo scadenza_password_giorni su vecchio PAT.',
  `refresh_password` datetime DEFAULT NULL,
  `prevent_password_change_day` tinyint(4) DEFAULT NULL COMMENT 'campo impedisci_cambio_pwd_giorno su vecchio PAT.',
  `force_change_password` int(11) NOT NULL DEFAULT '0' COMMENT 'Se impostata ad uno, forza il cambio della password all''utente',
  `deactivate_account_no_use` int(11) DEFAULT '0',
  `filter_owner_record` varchar(2) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `registration_type` varchar(45) DEFAULT NULL,
  `profile_image` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Tabella Utenti:in relazione con la tabella Profili (tabella rel_users_profiles) e con la tabella Enti(institutions).';

-- Dump dei dati della tabella pat.users: ~1 rows (circa)
INSERT INTO `users` (`id`, `institution_id`, `name`, `username`, `password`, `email`, `phone`, `spid_code`, `fiscal_code`, `active`, `active_key`, `deleted`, `last_visit`, `registration_date`, `super_admin`, `admin`, `prevent_password_repetition`, `prevent_password_repetition_6_months`, `password_expiration_days`, `refresh_password`, `prevent_password_change_day`, `force_change_password`, `deactivate_account_no_use`, `filter_owner_record`, `notes`, `registration_type`, `profile_image`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 'XphT+ixZEDkM', 'XphT+ixZEDkM', 'sha256:64000:18:PSoeUgN6XbAsm2fBLgXg8AcIU3Ikf39S:3+2tY4SJZzc3oLsGYLFPH4rt', 'XphT+mNcGT0LH06W5wVUStK+rGK9bA==', NULL, NULL, NULL, 1, '', 0, '2024-06-03 16:29:33', '2021-10-08 14:25:06', 0, 0, 2, 0, '365', NULL, 2, 0, 365, '2', NULL, '', '', '2021-10-08 14:25:06', '2023-11-29 14:46:14', NULL);
