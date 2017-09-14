<?php




require_once(__DIR__.'/../Models/UniversalConnect.php');

class feeds{

  private $hookup;
  public $API_KEY = "ccf91f1137bf499489e486f88769feae";
  public function __construct(){
    $this->hookup = UniversalConnect::doConnect();
  }

function getUserSources($id){
  $sql_interest="SELECT source FROM tags WHERE tag_id IN (SELECT tag_id from user_interest where user_id = :id)";
  $stmt = $this->hookup->prepare($sql_interest);
  $stmt->bindValue(':id', $id);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
public function getUserFeeds($id)
{
  # code...
  $sql = "SELECT author, title, description, url, url_to_img, published_at from news_feed where feed_id in (select feed_id from bookmark where user_id = :id)";
  $stmt = $this->hookup->prepare($sql);
  $stmt->bindValue(':id', $id);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getFeeds($request , $response , $args)
{
  $id = filter_var($args['id'],FILTER_SANITIZE_NUMBER_INT);
//  $sources_ = ["national-geographic", "mtv-news", "bbc-sport"];
  $sources_ = $this->getUserSources($id);

  $articles_data = $this->fetchAllArtilesFrom($sources_); //fetching articles
  shuffle($articles_data);

  //Now fetching bookmarked articles
//  $bookmarks = (object)array("source" => "src", "title" => "Aloha!", "author" => "Deep", "urlToImage"=>"Image");
$bookmarks = $this->getUserFeeds($id);
//var_dump($bookmarks);
foreach($bookmarks as $b){
  //echo ((object)$b)->title;
  array_unshift($articles_data,(object)$b);
}
  //array_unshift($articles_data, $bookmark); //adding bookmarks at first position

  // testing
/*  foreach($articles_data as $art){
      echo "<div style=\"border: 10px red solid; \">";
      echo "AUTHOR : " . $art->author . "<br>";
      echo "SOURCE : " . $art->source . "<br>";
      echo "TITLE : " . $art->title . "<br>";
      echo "IMAGE : <img height=80 src=" . $art->urlToImage . " /><br><br>";
      echo "</div>";
  }
*/
//  return json_encode(array("feeds" => $articles_data));
  $newResponse =  $response->withJson(array("feeds" => $articles_data));
  return $newResponse;
}
/*
    Return articles for a given source
*/
function getArticles($source){
    $news_url = "https://newsapi.org/v1/articles?source=" . $source . "&apiKey=". $this->API_KEY;
    $data = file_get_contents($news_url);
    return $data;
}

function fetchAllArtilesFrom($sources_){
    $articles_data = array();
    foreach($sources_ as $source){
        $source_artiles_json = $this->getArticles($source); //fetched all articles
        if($source_artiles_json){
            $source_artiles = json_decode($source_artiles_json);
            //echo $source_artiles_json;
            foreach($source_artiles->articles as $article){
                $article->source = $source;
                array_push($articles_data, $article);
            }
        }
    }
    return $articles_data;
}

}
