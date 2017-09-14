<?php

require_once(__DIR__.'/../Models/UniversalConnect.php');

class UserController{

  private $hookup;

  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }

  public function userRegistration($request , $response)
  {
    $username = $request->getParam('username');
    $firstname = $request->getParam('user_firstname');
    $lastname = $request->getParam('user_lastname');
    $email = $request->getParam('user_email');
    $affiliation = $request->getParam('user_affiliation');
    $aboutMe = $request->getParam('user_aboutme');
    $isMentor = $request->getParam('user_ismentor');
    $creation = date("Y-m-d h:i:s");
    $profilepic=$request->getParam('user_profile_pic');
    //genrating 32bit token for authentication based on unique field PHONENO.
    $token =md5(md5($username)."".md5(microtime()));
    //setting up image location
    $upload_directory = "Uploads/ProfilePics/".$username;
    //inserting image  to sepecifed location
    if(file_put_contents($upload_directory.".jpeg",base64_decode($profilepic)) != false)
    {
        $profilepic=$upload_directory.".jpeg";
    }

    $sql = "INSERT INTO user (username,profile_pic,lastname,affiliation,created_at,firstname,about_me,email,mentor_flag,user_token)
            VALUES(:username , :profilepic , :lastname,:affiliation,:creation,:firstname,:about,:email,:ismentor,:user_token)";
    try{
      $stmt = $this->hookup->prepare($sql);

      $stmt->bindParam(':username' , $username);
      $stmt->bindParam(':profilepic' , $profilepic);
      $stmt->bindParam(':lastname' , $lastname);
      $stmt->bindParam(':affiliation' , $affiliation);
      $stmt->bindParam(':creation' , $creation);
      $stmt->bindParam(':firstname' , $firstname);
      $stmt->bindParam(':about' , $aboutMe);
      $stmt->bindParam(':email' , $email);
      $stmt->bindParam(':ismentor' , $isMentor);
      $stmt->bindParam(':user_token' , $token);

      // execute the query
      $stmt->execute();
      $id=$this->hookup->lastInsertId();
      $data = array('user_id'=>$id,'user_token'=>$token,'Message'=>'Record Added Successfully');
    }
    catch(PDOException $e)
    {
      //Catching Duplicate records and showing them as registered members.
      if($e->errorInfo[1] == 1062)
      {
        $sql="SELECT user_id, user_token , is_active FROM user WHERE username=".$username;
        $stmt = $this->hookup->prepare($sql);
        $stmt->execute();
        // for removing the scaler error
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);

        // adding custom Message in Response
        $data['Message']='Already a existing Member';

        //Re-activating the deactive user

        if($data['user_is_active'] == 0)
        {
          $sql="UPDATE user SET is_active='1' WHERE username=".$username;
          $stmt = $this->hookup->prepare($sql);
          $stmt->execute();
        }
        //removing data which is not supposed to be seen .
          unset($data['is_active']);
      }
      else {
        $data = array('Message'=>$e->getMessage());
      }

    }
//genralized response fetching different results.
    $newResponse =  $response->withJson($data);
    return $newResponse;

  }

  public function userDeactivation($request , $response){
      $user_id = filter_var( $request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT);
      $sql="UPDATE user SET is_active='0' WHERE user_id=".$user_id;
      $stmt = $this->hookup->prepare($sql);
      $stmt->execute();
      $data = array('Message'=>"User is :- $user_id is deactivated");
      $newResponse =  $response->withJson($data);
      return $newResponse;

  }

}


 ?>
