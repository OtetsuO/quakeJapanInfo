<?php
/*
    システム定数定義::サイト固有値以外
*/

// PDO
define('PDO_TYPE_STR', 1);
define('PDO_TYPE_INT', 2);

// P2P地震情報 取得API
define('QUAKE_DATA_REQUEST_URL',        'http://p2pquake.ddo.jp/p2pquake/api_userquake.pl?date=');
define('QUAKE_DATA_REQUEST_TARGET_DATE', dirname(__FILE__).'/../task/targetDate.txt');
define('QUAKE_DATA_REQUEST_LOG_DIR',     dirname(__FILE__).'/../task/log/');
define('QUAKE_DATA_REQUEST_LOG_FILE',   'taskQuakeDataRequest.log');

// Googole Map API
define('GOOGLE_MAP_URL',                'http://maps.googleapis.com/maps/api/js?');
define('GOOGLE_MAP_SETTING__LATITUDE',  38.2586);
define('GOOGLE_MAP_SETTING__LONGITUDE', 137.6850);
define('GOOGLE_MAP_SETTING_ZOOM',       4);
define('GOOGLE_MAP_SETTING_SENSOR',     'false');

// 地震データ表示設定
define('QUAKE_DATA_DISP_DAYS',            30); // N日前までの地震データを表示
define('QUAKE_DATA_DISP_FROM',            mktime(0, 0, 0, date('m'), date('d') - QUAKE_DATA_DISP_DAYS, date('Y')));
define('QUAKE_DATA_DISP_TO',              mktime(23, 59, 59, date('m'), date('d'), date('Y')));
define('QUAKE_DATA_DISP_TITLE_MAGNITUDE', 'マグニチュード');
define('QUAKE_DATA_DISP_TITLE_DEPTH',     '深度');
