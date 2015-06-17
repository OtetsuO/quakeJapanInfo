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
    */
    public function insertQuakeData($param)
    {
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
             . 'updatedDate) VALUES ('
             . ':occurrenceDate, '
             . ':quakeScale, '
             . ':tsunamiFlag, '
             . ':epicenterID, '
             . ':epicenterChar, '
             . ':depth, '
             . ':depthChar, '
             . ':magnitude, '
             . ':latitude, '
             . ':longitude, '
             . ':announcedDistrict, '
             . ':createdDate, '
             . ':updatedDate)';

        $pdo = $this->pdo->prepare($sql);

        $pdo->bindValue(':occurrenceDate',    $param['occurrenceDate'],    PDO::PARAM_INT);
        $pdo->bindValue(':quakeScale',        $param['quakeScale'],        PDO::PARAM_INT);
        $pdo->bindValue(':tsunamiFlag',       $param['tsunamiFlag'],       PDO::PARAM_INT);
        $pdo->bindValue(':epicenterID',       $param['epicenterID'],       PDO::PARAM_INT);
        $pdo->bindValue(':epicenterChar',     $param['epicenterChar'],     PDO::PARAM_STR);
        $pdo->bindValue(':depth',             $param['depth'],             PDO::PARAM_INT);
        $pdo->bindValue(':depthChar',         $param['depthChar'],         PDO::PARAM_STR);
        $pdo->bindValue(':magnitude',         $param['magnitude'],         PDO::PARAM_INT);
        $pdo->bindValue(':latitude',          $param['latitude'],          PDO::PARAM_INT);
        $pdo->bindValue(':longitude',         $param['longitude'],         PDO::PARAM_INT);
        $pdo->bindValue(':announcedDistrict', $param['announcedDistrict'], PDO::PARAM_STR);
        $pdo->bindValue(':createdDate',       time(),                      PDO::PARAM_INT);
        $pdo->bindValue(':updatedDate',       time(),                      PDO::PARAM_INT);

        $result = $pdo->execute();

        return $result;
    }


}