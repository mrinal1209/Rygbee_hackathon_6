<?php

require_once(__DIR__.'/../Models/UniversalConnect.php');

class IdeaBoard{

  private $hookup;

  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }
  public function create_url_slug($title, $id){
  $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $title . "-" . $id);
  return $slug;
  }


    public function getIdeaDetail($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_project="SELECT * FROM idea WHERE idea_id = :id";
        $stmt = $this->hookup->prepare($sql_project);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data=array();
        $data =  $stmt->fetch(PDO::FETCH_ASSOC);
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

    public function getUserIdea($request , $response , $args){
        $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $sql_project="SELECT * FROM idea WHERE user_id = :id";
        $stmt = $this->hookup->prepare($sql_project);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data=array("ideas" => $stmt->fetchAll(PDO::FETCH_ASSOC));
        //$data =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

    public function deleteIdea($request , $response , $args){
        $user_id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
        $idea_id = $request->getParam('idea_id');
       // sql to delete a record
        $sql = "DELETE FROM idea WHERE idea_id=:ideaid AND user_id=:userid";

        try{
          $stmt = $this->hookup->prepare($sql);
          $stmt->bindValue(':ideaid', $idea_id);
          $stmt->bindValue(':userid', $user_id);
          $stmt->execute();
          $data = array('Success'=>'Record Updated Successfully' , 'idea_id'=>$idea_id, 'user_id'=>$user_id, 'status_code'=>200);
        }
        catch(PDOException $e){
          $data = array('error'=>$e->getMessage() , 'status_code'=>404);
        }
        $newResponse =  $response->withJson($data);
        return $newResponse;
    }

    public function insertIdea($request , $response , $args)
    {
      $user_id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
      $isPrivate = $request->getParam('is_private');
      $creation = date("Y-m-d h:i:s");
      $updateTime = date("Y-m-d h:i:s");
      $pageTitle = $request->getParam('page_title');
      $pageContent = $request->getParam('page_content');
      $slug = $this->create_url_slug($pageTitle,mt_rand(1,999999999));
      $sql = "INSERT INTO idea (is_private,created_at,page_title,page_content,slug,updated_at,user_id)
              VALUES(:isprivate , :creation,:pagetitle,:pagecontent,:slug,:updatetime ,:userid)";
              try{
                $stmt = $this->hookup->prepare($sql);
                $stmt->bindValue(':isprivate', $isPrivate);
                $stmt->bindValue(':creation',$creation);
                $stmt->bindValue(':pagetitle',$pageTitle);
                $stmt->bindValue(':pagecontent',$pageContent);
                $stmt->bindValue(':slug',$slug);
                $stmt->bindValue(':updatetime',$updateTime);
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

    public function updateIdea($request , $response , $args){
      $user_id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
      $isPrivate = $request->getParam('is_private');
      $idea_id = $request->getParam('idea_id');
      $updateTime = date("Y-m-d h:i:s");
      $pageTitle = $request->getParam('page_title');
      $pageContent = $request->getParam('page_content');
      $slug="";
      if(strlen($pageTitle) > 0)
      $slug = $this->create_url_slug($pageTitle,mt_rand(1,999999999));
      $sql = " UPDATE idea SET
              is_private = COALESCE(:isprivate,is_private),
              page_title = COALESCE(:pagetitle,page_title),
              page_content = COALESCE(:pagecontent,page_content),
              slug = COALESCE(:slugy,slug),
              updated_at = COALESCE(:updatedat,updated_at)
              WHERE idea_id = :ideaid AND user_id = :userid ";
        try{
                $stmt = $this->hookup->prepare($sql);
                $stmt->bindValue(':isprivate', $isPrivate);
                $stmt->bindValue(':pagetitle',$pageTitle);
                $stmt->bindValue(':pagecontent',$pageContent);
                $stmt->bindValue(':slugy',$slug);
                $stmt->bindValue(':updatedat',$updateTime);
                $stmt->bindValue(':userid',$user_id);
                $stmt->bindValue(':ideaid',$idea_id);
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
