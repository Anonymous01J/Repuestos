CREATE TABLE IF NOT EXISTS `licencia_sistema` (
  `id` tinyint unsigned NOT NULL,
  `token` mediumtext DEFAULT NULL,
  `ultima_fecha_vista_utc` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

INSERT IGNORE INTO `licencia_sistema` (`id`) VALUES (1);