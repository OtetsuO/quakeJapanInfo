<?php
/*
    TBL epicenter
    震源地データ
*/
require_once(dirname(__FILE__).'/commonDB.php');

class DbEpicenter extends CommonDb
{
    /*
        震源地データ登録
        地震データ取得タスクより呼び出される。
            1. 引数に設定された震源地名が未登録だった場合、新たに登録してidを返却
            2. 引数に設定された震源地名が登録済みだった場合、登録済みのidを返却
    */
    public function registerByQuakeDataRequestTask($name)
    {
        $checkResult = $this->selectEpicenterByName($name);

        if ($checkResult) {
            // 登録済みデータあり
            $epicenterID = $checkResult[0]['id'];
        } else {
            // 登録済みデータなし
            $epicenterID = $this->insertEpicenter($name);
        }

        return $epicenterID;
    }


    /*
        震源地データselect
            震源地名によって震源地データをselectする。
    */
    public function selectEpicenterByName($name)
    {
        $sql = 'SELECT * FROM epicenter '
             . 'WHERE name = :name';

        $pdo = $this->pdo->prepare($sql);

        $pdo->bindValue(':name', $name, PDO::PARAM_STR);

        $pdo->execute();
        $epicenter = $pdo->fetchAll();

        return $epicenter;
    }


    /*
        震源地データinsert
            insertを実行し、震源地データのidを返却する。
    */
    public function insertEpicenter($name)
    {
        $sql = 'INSERT INTO epicenter ('
             . 'name, '
             . 'createdDate, '
             . 'updatedDate) VALUES ('
             . ':name, '
             . ':createdDate, '
             . ':updatedDate)';

        $pdo = $this->pdo->prepare($sql);

        $pdo->bindValue(':name',        $name,  PDO::PARAM_STR);
        $pdo->bindValue(':createdDate', time(), PDO::PARAM_INT);
        $pdo->bindValue(':updatedDate', time(), PDO::PARAM_INT);

        $pdo->execute();
        $lastInsertID = $this->pdo->lastInsertId();

        return $lastInsertID;
    }


}