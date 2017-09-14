<?php
require_once('UniversalConnect.php');

class UserToken{
  private static $hookup;
  public static function authenicate($token , $user_id){
    self::$hookup = UniversalConnect::doConnect();
    $stmt = self::$hookup->prepare("SELECT user_id FROM user WHERE user_token='$token' AND user_id='$user_id'");
    $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(!$row)
    {
      return false;
    }
    else {
      return true;
    }
    }
  }


 ?>
