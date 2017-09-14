<?php
require_once("IConnectInfo.php");
class UniversalConnect implements IConnectInfo{

private static $server = IConnectInfo::HOST;
private static $currentDB = IConnectInfo::DBNAME;
private static $user = IConnectInfo::UNAME;
private static $pass = IConnectInfo::PW;
private static $hookup;

public static function doConnect()
{
           try{
           self::$hookup = new PDO("mysql:host=".self::$server.";dbname=".self::$currentDB,self::$user,self::$pass);
           self::$hookup->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          // echo "Connection Successfull with the DataBase";
          }
          catch(PDOException $e)
             {
          //    echo $e->getMessage();
             }

          return self::$hookup;
}


}

 ?>
