<?php

require_once(__DIR__.'/../Models/UniversalConnect.php');

class Profile{

  private $hookup;

  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }

  public function getProfile($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_profile="SELECT * FROM user WHERE user_id = :id";
        $sql_interest="SELECT name FROM tags WHERE tag_id IN (SELECT tag_id from user_interest where user_id = :id)";
        $sql_domain="SELECT name FROM tags WHERE tag_id IN (SELECT tag_id from domain_expertise where user_id = :id)";
        $stmt = $this->hookup->prepare($sql_profile);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        // for removing the scaler error
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $this->hookup->prepare($sql_interest);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        array_push($data,array('areas_of_interest' => $stmt->fetchAll(PDO::FETCH_COLUMN)));
        $stmt = $this->hookup->prepare($sql_domain);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        array_push($data,array('domain_of_expertise' => $stmt->fetchAll(PDO::FETCH_COLUMN)));
        $newResponse =  $response->withJson($data);
        return $newResponse;
  }

    public function getProjectDetail($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_project="SELECT * FROM project WHERE project_id = :id";
        $stmt = $this->hookup->prepare($sql_project);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

    public function getUserProject($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_project="SELECT * FROM project WHERE user_id = :id";
        $stmt = $this->hookup->prepare($sql_project);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data=array();
        $data =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

    public function getUserInterest($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_interest="SELECT source FROM tags WHERE tag_id IN (SELECT tag_id from user_interest where user_id = :id)";
        $stmt = $this->hookup->prepare($sql_interest);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data=array('areas_of_interest' => $stmt->fetchAll(PDO::FETCH_COLUMN));
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

//users only
    public function updateRecordById($request, $response){
      $id = filter_var( $request->getAttribute('id'), FILTER_SANITIZE_NUMBER_INT);
      $username = $request->getParam('username');
      $firstname = $request->getParam('user_firstname');
      $lastname = $request->getParam('user_lastname');
      $email = $request->getParam('user_email');
      $affiliation = $request->getParam('user_affiliation');
      $aboutMe = $request->getParam('user_aboutme');
      $isMentor = $request->getParam('user_ismentor');
      $profilepic=$request->getParam('user_profile_pic');
      //setting up image location
      $upload_directory = "Uploads/ProfilePics/".$username;
      //inserting image  to sepecifed location
      if(file_put_contents($upload_directory.".jpeg",base64_decode($profilepic)) != false)
      {
          $profilepic=$upload_directory.".jpeg";
      }
    $sql = " UPDATE user SET
            username = COALESCE(:username,username),
            profile_pic = COALESCE(:profilepic,profile_pic),
            lastname = COALESCE(:lastname,lastname),
            affiliation = COALESCE(:affiliation,affiliation),
            firstname = COALESCE(:firstname,firstname),
            about_me = COALESCE(:about,about_me),
            email = COALESCE(:email,email),
            mentor_flag = COALESCE(:ismentor,mentor_flag)
            WHERE user_id = $id ";

    try{
    $stmt = $this->hookup->prepare($sql);
    $stmt->bindParam(':username' , $username);
    $stmt->bindParam(':profilepic' , $profilepic);
    $stmt->bindParam(':lastname' , $lastname);
    $stmt->bindParam(':affiliation' , $affiliation);
    $stmt->bindParam(':firstname' , $firstname);
    $stmt->bindParam(':about' , $aboutMe);
    $stmt->bindParam(':email' , $email);
    $stmt->bindParam(':ismentor' , $isMentor);
    // execute the query
    $stmt->execute();

    $data = array('Success'=>'Record Updated Successfully' , 'status_code'=>200);

  }
  catch(PDOException $e)
  {
    $data = array('error'=>$e->getMessage() , 'status_code'=>404);
  }

  $newResponse =  $response->withJson($data);
  return $newResponse;
  }

  public function deleteProject($request , $response , $args){
      $user_id = filter_var($args['user_id'],FILTER_SANITIZE_NUMBER_INT);
      $project_id = $request->getParam('project_id');
     // sql to delete a record
      $sql = "DELETE FROM project WHERE project_id=:proid AND user_id=:userid";

      try{
        $stmt = $this->hookup->prepare($sql);
        $stmt->bindValue(':proid', $project_id);
        $stmt->bindValue(':userid', $user_id);
        $stmt->execute();
        $data = array('Success'=>'Record Updated Successfully' , 'status_code'=>200);
      }
      catch(PDOException $e){
        $data = array('error'=>$e->getMessage() , 'status_code'=>404);
      }
      $newResponse =  $response->withJson($data);
      return $newResponse;
  }

  public function insertProject($request , $response , $args){
    $user_id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
    $title = $request->getParam('title');
    $content = $request->getParam('content');
    $role = $request->getParam('role');
    $isGroup = $request->getParam('is_group');
    $startdate = $request->getParam('start_date');
    $enddate=$request->getParam('end_date');
    $isIdea = $request->getParam('is_idea');
    $sql = "INSERT INTO project (title,content,role,is_group,start_date,end_date,is_idea,user_id)
            VALUES(:title,:content,:role,:is_group,:startdate,:enddate,:is_idea,:userid)";
            try{
              $stmt = $this->hookup->prepare($sql);
              $stmt->bindValue(':title', $title);
              $stmt->bindValue(':content',$content);
              $stmt->bindValue(':role',$role);
              $stmt->bindValue(':is_group',$isGroup);
              $stmt->bindValue(':startdate',$startdate);
              $stmt->bindValue(':enddate',$enddate);
              $stmt->bindValue(':is_idea',$isIdea);
              $stmt->bindValue(':userid',$user_id);
              $stmt->execute();
              $data = array('Success'=>'Record Updated Successfully' , 'status_code'=>200);
            }
            catch(PDOException $e){
              $data = array('error'=>$e->getMessage() , 'status_code'=>404);
            }
            $newResponse =  $response->withJson($data);
            return $newResponse;
  }




}
