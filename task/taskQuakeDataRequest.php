<?php
/*
    地震データ取得タスク
        P2P地震情報から2007/03/01以降の地震データを取得する。
*/
require_once(dirname(__FILE__).'/../class/dbQuakeData.php');
require_once(dirname(__FILE__).'/../class/dbEpicenter.php');


// 取得対象年月日の設定
date_default_timezone_set('Asia/Tokyo');
$targetDate = file_get_contents(QUAKE_DATA_REQUEST_TARGET_DATE);
$targetY    = date('Y', $targetDate);
$targetM    = date('m', $targetDate);
$targetD    = date('d', $targetDate);

if ($targetDate >= mktime(0, 0, 0, date('n'), date('j'), date('y'))) {
    exit; // 取得対象年月日が処理日を超えた場合は処理抜け。
}

$dbQuakeData = new DbQuakeData();
$dbEpicenter = new DbEpicenter();

$taskLog = fopen(QUAKE_DATA_REQUEST_LOG_DIR.QUAKE_DATA_REQUEST_LOG_FILE, 'a');
fwrite($taskLog, 'START ('.date('Y/m/d H:i:s').")\n");
fwrite($taskLog, "  |- targetDate : $targetY/$targetM/$targetD \n");


header('content-Type: text/html; charset=shift_JIS');
$requrestUrl = QUAKE_DATA_REQUEST_URL."$targetY/$targetM/$targetD";

try {
    $responseData = file_get_contents($requrestUrl, false, null);

    if ($responseData != false) {
        mb_convert_variables('UTF-8', 'SJIS', $responseData);
        $responseData = preg_split('/\R/', $responseData);

        $cnt           = 0;
        $registerParam = array();

        foreach ($responseData AS $key => $value) {
            $responseDataDetail = explode(',', $value);
            /*
                $responseDataDetail
                [0] 通知日時
                [1] 通知コード("ARP"=ピア数、"UQU"=地震感知情報、"QUA"=地震情報)
                [2] 詳細情報

                通知コードが"QUA"(地震情報)のデータのみ処理続行。
            */

            if ($responseDataDetail[1] == 'QUA') {
                $quakeDataDetail = explode('/', $responseDataDetail[2]);
                /*
                    $quakeDataDetail
                    [0]  発生日時 (dd日HH時ii分)
                    [1]  震度
                    [2]  津波有無 ("0"=なし、"1"=あり、"2"=調査中、"3"=不明)
                    [3]  地震情報種類 ("1"=震度速報、"2"=震源情報、"3"=震源・震度情報、"4"=震源・詳細情報、"5"=遠地地震情報)
                    [4]  震源
                    [5]  深さ
                    [6]  マグニチュード
                    [7]  震度訂正 ("0"=いいえ、"1"=はい)
                    [8]  緯度
                    [9]  経度
                    [10] 発表区分

                    地震情報種類が"4"(震源・詳細震度情報)のデータのみ処理続行。
                */

                if ($quakeDataDetail[3] == 4) {
                    $cnt++;

                    // 発生日時
                    $day  = substr($quakeDataDetail[0], 0, 2);  // 日
                    $hour = substr($quakeDataDetail[0], 5, 2);  // 時
                    $min  = substr($quakeDataDetail[0], 10, 2); // 秒
                    $registerParam[$cnt]['occurrenceDate'] = mktime((int)$hour, (int)$min, 0, $targetM, $day, $targetY);

                    // 震度
                    $registerParam[$cnt]['quakeScale'] = $quakeDataDetail[1];

                    // 津波有無
                    $registerParam[$cnt]['tsunamiFlag'] = $quakeDataDetail[2];

                    // 震源
                    $registerParam[$cnt]['epicenterID']   = $dbEpicenter->registerByQuakeDataRequestTask($quakeDataDetail[4]);
                    $registerParam[$cnt]['epicenterChar'] = $quakeDataDetail[4];

                    // 深さ
                    $registerParam[$cnt]['depth']     = mb_ereg_replace('[^0-9.]', '', $quakeDataDetail[5]);
                    $registerParam[$cnt]['depthChar'] = $quakeDataDetail[5];

                    // マグニチュード
                    $registerParam[$cnt]['magnitude'] = $quakeDataDetail[6];

                    // 緯度
                    $registerParam[$cnt]['latitude'] = mb_ereg_replace('[^0-9.]', '', $quakeDataDetail[8]);

                    // 経度
                    $registerParam[$cnt]['longitude'] = mb_ereg_replace('[^0-9.]', '', $quakeDataDetail[9]);

                    // 発表管区
                    $registerParam[$cnt]['announcedDistrict'] = $quakeDataDetail[10];
                }
            }
        }

        if ($quakeDataDetail) {
            $dbQuakeData->insertQuakeData($registerParam);
        }

        // 取得対象年月日を1日進める
        file_put_contents(QUAKE_DATA_REQUEST_TARGET_DATE, mktime(0, 0, 0, $targetM, $targetD+1, $targetY));
        fwrite($taskLog, "  |- result : $cnt \n\n");

    } else {
        // 取得対象年月日を1日進める
        file_put_contents(QUAKE_DATA_REQUEST_TARGET_DATE, mktime(0, 0, 0, $targetM, $targetD+1, $targetY));
        fwrite($taskLog, "request failed (1) \n\n");
    }

} catch (Exception $e) {
    fwrite($taskLog, "request failed (2) \n\n");
}

fclose($taskLog);
