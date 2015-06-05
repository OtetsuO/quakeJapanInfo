<?php
/*
    PDO接続
    各テーブル用のファイルの親クラス
*/
require_once(dirname(__FILE__).'/../config/constant.php');

class CommonDb
{
    public function __construct()
    {
        $this->initDb();
    }

    public function initDb()
    {
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s', DB_HOST, DB_NAME);
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            );
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            exit('DB connection failed. ('.$e->getMessage().')');
        }
    }

    public function __destruct()
    {
        $this->connection = null;  
    }

}