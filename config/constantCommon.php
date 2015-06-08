<?php
/*
    システム定数定義::サイト固有値以外
*/

// PDO
define('PDO_TYPE_STR', 1);
define('PDO_TYPE_INT', 2);

// P2P地震情報 取得API
define('QUAKE_DATA_HTTP_REQUEST_URL',        'http://p2pquake.ddo.jp/p2pquake/api_userquake.pl?date=');
define('QUAKE_DATA_HTTP_REQUEST_TARGET_DATE', dirname(__FILE__).'/../task/targetDate.txt');
define('QUAKE_DATA_HTTP_REQUEST_LOG_DIR',     dirname(__FILE__).'/../task/log/');
define('QUAKE_DATA_HTTP_REQUEST_LOG_FILE',   'taskQuakeDataHttpRequest.log');

// P2P地震情報 取得API(JASON)
define('QUAKE_DATA_JSON_REQUEST_URL',      'http://api.p2pquake.com/v1/human-readable?limit=');
define('QUAKE_DATA_JSON_REQUEST_LIMIT',    100); // jsonデータ取得時の件数(指定なし=10件、最大値=100件)
define('QUAKE_DATA_JSON_REQUEST_LOG_DIR',  dirname(__FILE__).'/../task/log/');
define('QUAKE_DATA_JSON_REQUEST_LOG_FILE', 'taskQuakeDataJsonRequest.log');
define('QUAKE_DATA_JSON_REQUEST_MAX_SCALE',
            serialize(array(
                0 =>  0, // なし
                10 => 1, // 震度1
                20 => 2, // 震度2
                30 => 3, // 震度3
                40 => 4, // 震度4
                45 => 4, // 震度5弱
                50 => 5, // 震度5強
                55 => 6, // 震度6弱
                60 => 6, // 震度6強
                70 => 7, // 震度7
            ))
);
define('QUAKE_DATA_JSON_REQUEST_TUNAMI_PARAM',
            serialize(array(
                'None'         => 0, // なし
                'Unknown'      => 1, // 不明
                'Checking'     => 2, // 調査中
                'NonEffective' => 3, // 若干の海面変動[被害の心配なし]
                'Watch'        => 4, // 津波注意報
                'Warning'      => 5, // 津波予報[種類不明]
            ))
);

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
