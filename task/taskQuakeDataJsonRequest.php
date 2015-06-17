<?php
/*
    地震データ取得タスク JSON API版
        P2P地震情報JSON APIから地震データを取得する。

        <技術仕様>
        http://www.p2pquake.com/dev/?q=json-api
*/
require_once(dirname(__FILE__).'/../class/dbQuakeData.php');
require_once(dirname(__FILE__).'/../class/dbEpicenter.php');


$dbQuakeData = new DbQuakeData();
$dbEpicenter = new DbEpicenter();

$taskLog = fopen(QUAKE_DATA_JSON_REQUEST_LOG_DIR.QUAKE_DATA_JSON_REQUEST_LOG_FILE, 'a');
fwrite($taskLog, 'START ('.date('Y/m/d H:i:s').")\n");

try {
    $quakeDataJson = file_get_contents(QUAKE_DATA_JSON_REQUEST_URL.QUAKE_DATA_JSON_REQUEST_LIMIT, false, null);

    if ($quakeDataJson != false) {
        $quakeDataArray = json_decode($quakeDataJson, true);

        $cnt         = 0;
        $uniqueCheck = null;

        $paramTsunami = unserialize(QUAKE_DATA_JSON_REQUEST_TUNAMI_PARAM);

        foreach ($quakeDataArray AS $key => $value) {
            /*
                $value
                [time] 情報配信日時
                [code] 情報種類 ("551"=地震情報、"552"=津波予報、"5610"=集計済み地震感知情報)

                情報種類が"551"(地震情報) かつ 震源地名が判明しているデータのみ処理続行
            */
            if (($value['code'] == 551) && ($value['earthquake']['hypocenter']['name'])) {
                /*
                    $value ※[code]が"551"(地震情報)の場合の固有情報で使用する項目のみ記載
                    [earthquake][time] 発生日時
                    [earthquake][hypocenter][name] 震源地名
                    [earthquake][hypocenter][latitude] 緯度
                    [earthquake][hypocenter][longitude] 経度
                    [earthquake][hypocenter][depth] 深さ
                    [earthquake][hypocenter][magnitude] マグニチュード
                    [earthquake][maxScale] 最大震度
                    [earthquake][domesticTsunami] 津波有無

                    ユニークキーが重複するデータは処理対象外
                */

                $unique = $value['earthquake']['time']
                        . $value['earthquake']['hypocenter']['name']
                        . $value['earthquake']['hypocenter']['latitude']
                        . $value['earthquake']['hypocenter']['longitude']
                        . $value['earthquake']['hypocenter']['depth']
                        . $value['earthquake']['hypocenter']['magnitude'];

                if ($uniqueCheck != $unique) {
                    $cnt++;
                    $registerParam = array();

                    // 発生日時
                    $announcedYear  = substr($value['time'], 0, 4);  // 年
                    $announcedMonth = substr($value['time'], 5, 2);  // 月
                    $announcedDay   = substr($value['time'], 8, 2);  // 日
                    $announcedHour  = substr($value['time'], 11, 2); // 時
                    $announcedMin   = substr($value['time'], 14, 2); // 分

                    $generatedDay  = substr($value['earthquake']['time'], 0, 2);  // 日
                    $generatedHour = substr($value['earthquake']['time'], 5, 2);  // 時
                    $generatedMin  = substr($value['earthquake']['time'], 10, 2); // 分

                    if ($announcedDay == $generatedDay) {
                        $month = $announcedMonth;
                    } else {
                        $month = $announcedMonth-1;
                    }
                    $registerParam['occurrenceDate'] = mktime((int)$generatedHour, (int)$generatedMin, 0, $month, $generatedDay, $announcedYear);

                    // 震度
                    $registerParam['quakeScale'] = $value['earthquake']['maxScale'];

                    // 津波有無
                    $registerParam['tsunamiFlag'] = $paramTsunami[$value['earthquake']['domesticTsunami']];

                    // 震源
                    $registerParam['epicenterID']   = $dbEpicenter->registerByQuakeDataRequestTask($value['earthquake']['hypocenter']['name']);
                    $registerParam['epicenterChar'] = $value['earthquake']['hypocenter']['name'];

                    // 深さ
                    $registerParam['depth']     = mb_ereg_replace('[^0-9.]', '', $value['earthquake']['hypocenter']['depth']);
                    $registerParam['depthChar'] = $value['earthquake']['hypocenter']['depth'];

                    // マグニチュード
                    $registerParam['magnitude'] = $value['earthquake']['hypocenter']['magnitude'];

                    // 緯度
                    $registerParam['latitude'] = mb_ereg_replace('[^0-9.]', '', $value['earthquake']['hypocenter']['latitude']);

                    // 経度
                    $registerParam['longitude'] = mb_ereg_replace('[^0-9.]', '', $value['earthquake']['hypocenter']['longitude']);

                    // 発表管区
                    $registerParam['announcedDistrict'] = $value['issue']['type'];

                    $dbQuakeData->insertQuakeData($registerParam);
                    $uniqueCheck = $unique;
                }
            }
        }

        fwrite($taskLog, "  |- result : $cnt \n\n");

    } else {
        fwrite($taskLog, "request failed (1) \n\n");
    }

} catch (Exception $e) {
    fwrite($taskLog, "request failed (2) \n\n");
}

fclose($taskLog);
