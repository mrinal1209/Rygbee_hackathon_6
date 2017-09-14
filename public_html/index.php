<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once('Controllers/Profile.php');
require_once('Controllers/IdeaBoard.php');
require_once('MiddleWare/TokenAuth.php');
require_once('Controllers/UserController.php');
require_once('Controllers/Newsfeed.php');
require_once('Controllers/feeds.php');

$app = new \Slim\App;


$app->get('/api/user/{id}', \Profile::class.':getProfile');

$app->get('/api/project/{id}', \Profile::class.':getProjectDetail');

$app->get('/api/user/{id}/projects', \Profile::class.':getUserProject');

$app->get('/api/user/{id}/interests', \Profile::class.':getUserInterest');

$app->get('/api/idea/{id}', \IdeaBoard::class.':getIdeaDetail');

$app->get('/api/user/{id}/ideas', \IdeaBoard::class.':getUserIdea');

$app->post('/api/user/register',\UserController::class.':userRegistration');

$app->put('/api/user/deactivate/{id}',\UserController::class.':userDeactivation')->add(new TokenAuth);

$app->put('/api/user/{id}/modify',\Profile::class.':updateRecordById');

$app->delete('/api/user/{id}/idea',\IdeaBoard::class.':deleteIdea');

$app->post('/api/user/{id}/idea',\IdeaBoard::class.':insertIdea');

$app->put('/api/user/{id}/idea',\IdeaBoard::class.':updateIdea');

$app->post('/api/user/{id}/project',\Profile::class.':insertProject');

$app->get('/api/user/{id}/newsfeed',\Newsfeed::class.':getSavedNews');

$app->post('/api/user/{id}/newsfeed',\Newsfeed::class.':saveBookmark');

$app->delete('/api/user/{id}/newsfeed',\Newsfeed::class.':deleteBookmark');

$app->get('/api/feeds/user/{id}',\feeds::class.':getFeeds');

$app->post('/api/user/{id}/rating',\Newsfeed::class.':saveRating');

$app->run();
