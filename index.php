<?php


require_once 'HTTP/Request2.php';
require_once 'Services/Twitter.php';
require_once 'HTTP/OAuth/Consumer.php';


/**
 * silex
 **/
require_once __DIR__.'/silex.phar';

$app = new Silex\Application();

//debug mode = on
ini_set('display_errors',1);

$app->get('/test/', function () {
	return "This is test.";
});

$app->get('/hello/{name}', function ($name) {
	return "Hello $name";
});
$app->get("/tweet/",function(){

	$message = '投稿テストです';

	$twitter = new Services_Twitter();

	$oauth = new HTTP_OAuth_Consumer(
		'D1zDyZUYaLXDtI4LqaPA',		//consumer key
		'hSeAmn6W1IZXpWPm99FO3KTmBt7dHHUbkde2L7Kg',		//consumer secret
		'6056432-NsV6Vn1eKlhgCF1QlqKMIxIM0kkG7e3zT0EX36WkjX',			//access token
		'nby5n2yT4yyY0WqD4pUZ8K07QSaYkB0UKjNZ0cINCg'
	);		//access token secret
	$twitter->setOAuth($oauth);
	$msg = $twitter->statuses->update($message);
	echo "OK";
	return "send finish.";
});


$app->get("/twitter/auth/",function() use ($app){
	$request_token_url = "https://api.twitter.com/oauth/request_token";
	$callback_url = "http://vm-silex.manjiro.net/";
	$authorize_url = "https://api.twitter.com/oauth/authorize";
	session_start();


	// [2] URL情報

	// [1] インスタンス生成
	$http_request = new HTTP_Request2();
	$http_request->setConfig("ssl_verify_peer",false);
	$consumer = new HTTP_OAuth_Consumer("D1zDyZUYaLXDtI4LqaPA","hSeAmn6W1IZXpWPm99FO3KTmBt7dHHUbkde2L7Kg");
	$consumer_request = new HTTP_OAuth_Consumer_Request;
	$consumer_request->accept($http_request);
	$consumer->accept($consumer_request);

	// [2] getRequestTokenでリクエストトークンを取得
	$consumer->getRequestToken( $request_token_url, $callback_url );

	// [3] getToken/getTokenSecretで、トークンをセッションに保存
	$_SESSION["request_token"] = $consumer->getToken();
	$_SESSION["request_token_secret"] = $consumer->getTokenSecret();

	// [4] getAuthorizeUrlでURLを取得
	$auth_url = $consumer->getAuthorizeUrl( $authorize_url );

	// [5] 必要とあらばリダイレクト
	header( "Location: " . $auth_url );
	return $app->redirect($auth_url);

});

$app->get("/",function(){
	return "welcome to silex.";
});

$app->run();
