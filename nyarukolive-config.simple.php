<?php

// 以下內容取自 WordPress 的 wp-config.php 文件。你只需要複製相關內容到這裡即可開始使用，不推薦自定義。

// ** MySQL 設定 - 您可以從主機服務提供商獲取相關資訊。 ** //
/** WordPress 的資料庫名稱，請更改 "database_name_here" */
define('DB_NAME', 'wpdb');

/** MySQL 資料庫使用者名稱，請更改 "username_here" */
define('DB_USER', 'wpdbuser');

/** MySQL 資料庫密碼，請更改 "password_here" */
define('DB_PASSWORD', 'password');

/** MySQL 主機位址 uuumoedb.mysql.rds.aliyuncs.com:3838 */
define('DB_HOST', '127.0.0.1');

/** 建立資料表時預設的文字編碼 */
define('DB_CHARSET', 'utf8mb4');

/** 資料庫對照型態。如果不確定請勿更改。 */
define('DB_COLLATE', 'utf8_unicode_ci');

/**
 * WordPress 資料表前綴。
 *
 * 若您為每個 WordPress 設定不同的資料表前綴，則可在同個資料庫內安裝多個 WordPress。
 * 前綴只能使用半型數字、字母和底線！
 */
$table_prefix  = 'racing_';