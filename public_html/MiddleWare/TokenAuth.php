<?php
require_once(__DIR__.'/../Models/UserToken.php');
class TokenAuth
{
	public function __invoke($request,$response,$next){
		if($request->hasHeader('Authorization')){
			    $user_token = $request->getHeader('Authorization')[0];

					$route = $request->getAttribute('route');
          $user_id = $route->getArgument('id');

  		    if(!UserToken::authenicate($user_token,$user_id))
  		    {
						$data = array('Message'=>"Token not valid for the user.");
						$newResponse =  $response->withJson($data);
						return $newResponse->withStatus(401);
  		    }
		}
		else{
						$data = array('Message'=>"Authorization Header Missing");
						$newResponse =  $response->withJson($data);
				    return $newResponse->withStatus(401);
		    }

		$response = $next($request,$response);
		return $response;
	}
}



?>
