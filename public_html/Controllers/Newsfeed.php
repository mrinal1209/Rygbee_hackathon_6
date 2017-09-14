<?php
require_once(__DIR__.'/../Models/UniversalConnect.php');

class Newsfeed{

  private $hookup;

  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }

public function getSavedNews($request , $response , $args)
{
  $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
  $sql = "SELECT author, title, description, url, url_to_img, published_at from news_feed where feed_id in (select feed_id from bookmark where user_id = :id)";
  $stmt = $this->hookup->prepare($sql);
  $stmt->bindValue(':id', $id);

  $stmt->execute();
  $data=array();
  $data =  $stmt->fetchAll(PDO::FETCH_ASSOC);
  $newResponse =  $response->withJson($data);
  return $newResponse;
}

public function saveBookmark($request , $response , $args){
  $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
  $feed_id = $request->getParam('feed_id');
  $sql = "INSERT INTO bookmark values (:id, :feedId)";
  try{
  $stmt = $this->hookup->prepare($sql);
  $stmt->bindValue(':id', $id);
  $stmt->bindValue(':feedId', $feed_id);
  $stmt->execute();

      $data = array('Success'=>'Record inserted Successfully' , 'status_code'=>200);

    }
    catch(PDOException $e)
    {
      $data = array('error'=>$e->getMessage() , 'status_code'=>404);
    }

    $newResponse =  $response->withJson($data);
    return $newResponse;
  }

  public function saveRating($request , $response , $args)
  {
    $feed_id = $request->getParam('feed_id');
    $rating = $request->getParam('rating');
    try {
      $sql = "SELECT rating, no_of_users from newsfeed_rating where feed_id = :feedId";
      $stmt = $this->hookup->prepare($sql);
      $stmt->bindValue(':feedId', $feed_id);
      $stmt->execute();
      $var=$stmt->fetchAll(PDO::FETCH_NUM);
      $rating1 = $var[0][0];
      $noOfUsers = $var[0][1];
      $rating1 = $rating1 * $noOfUsers;
      $noOfUsers += 1;
      $rating1 += $rating;
      $rating1 /= $noOfUsers;

      $sql = "UPDATE newsfeed_rating SET rating = :rating, no_of_users = :noOfUsers where feed_id = :feedId";
      $stmt = $this->hookup->prepare($sql);
      $stmt->bindValue(':feedId', $feed_id);
      $stmt->bindValue(':rating', $rating1);
      $stmt->bindValue(':noOfUsers', $noOfUsers);
      $stmt->execute();
      $data = array('Success'=>'Record inserted Successfully' , 'status_code'=>200);

    }
    catch(PDOException $e)
    {
      $data = array('error'=>$e->getMessage() , 'status_code'=>404);
    }

    $newResponse =  $response->withJson($data);
    return $newResponse;
  }

  public function deleteBookmark($request , $response , $args){
    $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
    $feed_id = $request->getParam('feed_id');
    $sql = "DELETE FROM bookmark where user_id = :id and feed_id = :feedId";
    try{
    $stmt = $this->hookup->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':feedId', $feed_id);
    $stmt->execute();
  $data = array('Success'=>'Record Deleted Successfully' , 'status_code'=>200);

      }
      catch(PDOException $e)
      {
        $data = array('error'=>$e->getMessage() , 'status_code'=>404);
      }

      $newResponse =  $response->withJson($data);
      return $newResponse;


  }
}
