<?php
/*
    TBL quakeData
    地震データ
*/
require_once(dirname(__FILE__).'/commonDB.php');

class DbQuakeData extends CommonDb
{
    /*
        地震データinsert
            地震データ取得タスクから呼び出されるため複数レコードを同時に登録する。
    */
    public function insertQuakeData($param)
    {
        // 重複行を排除してkeyを再設定
        $param = array_unique($param, SORT_REGULAR);
        $param = array_merge($param);

        $time = time();
        $cnt  = count($param); // 登録レコード数を取得

        $sql = 'INSERT INTO quakeData ('
             . 'occurrenceDate, '
             . 'quakeScale, '
             . 'tsunamiFlag, '
             . 'epicenterID, '
             . 'epicenterChar, '
             . 'depth, '
             . 'depthChar, '
             . 'magnitude, '
             . 'latitude, '
             . 'longitude, '
             . 'announcedDistrict, '
             . 'createdDate, '
             . 'updatedDate) VALUES ';

        // 複数レコードを同時に登録するためvalueを配列数分指定する。
        for ($i = 0; $i <= $cnt-1; $i++) {
            $sql .= "(:occurrenceDate$i, "
                  . ":quakeScale$i, "
                  . ":tsunamiFlag$i, "
                  . ":epicenterID$i, "
                  . ":epicenterChar$i, "
                  . ":depth$i, "
                  . ":depthChar$i, "
                  . ":magnitude$i, "
                  . ":latitude$i, "
                  . ":longitude$i, "
                  . ":announcedDistrict$i, "
                  . ":createdDate$i, "
                  . ":updatedDate$i)";

            // 最後のレコード以外はカンマを付ける。
            $sql .= ($i == $cnt-1) ? '' : ',';
        }

        $pdo = $this->pdo->prepare($sql);

        foreach ($param AS $key => $value) {
            $pdo->bindValue(":occurrenceDate$key",    $value['occurrenceDate'],    PDO::PARAM_INT);
            $pdo->bindValue(":quakeScale$key",        $value['quakeScale'],        PDO::PARAM_INT);
            $pdo->bindValue(":tsunamiFlag$key",       $value['tsunamiFlag'],       PDO::PARAM_INT);
            $pdo->bindValue(":epicenterID$key",       $value['epicenterID'],       PDO::PARAM_INT);
            $pdo->bindValue(":epicenterChar$key",     $value['epicenterChar'],     PDO::PARAM_STR);
            $pdo->bindValue(":depth$key",             $value['depth'],             PDO::PARAM_INT);
            $pdo->bindValue(":depthChar$key",         $value['depthChar'],         PDO::PARAM_STR);
            $pdo->bindValue(":magnitude$key",         $value['magnitude'],         PDO::PARAM_INT);
            $pdo->bindValue(":latitude$key",          $value['latitude'],          PDO::PARAM_INT);
            $pdo->bindValue(":longitude$key",         $value['longitude'],         PDO::PARAM_INT);
            $pdo->bindValue(":announcedDistrict$key", $value['announcedDistrict'], PDO::PARAM_STR);
            $pdo->bindValue(":createdDate$key",       $time,                       PDO::PARAM_INT);
            $pdo->bindValue(":updatedDate$key",       $time,                       PDO::PARAM_INT);
        }

        $result = $pdo->execute();

        return $result;
    }


}