<?php
require __DIR__.'/../services/User.php';
require __DIR__.'/../services/Posts.php';
$pdo = require __DIR__.'/../services/Db.php';

class RestAPI {
    /**
	 * User
	 */
    private $_user;
    /**
	 * PostMessage
	 */
    private $_postMessage;
    /**
	 * Request method
	 */
	private $_requestMethod;

    /**
     *resource name
     */
    private $_resourceName;

    /**
     * post message ID
     */
    private $_id;
    /**
	 * allowed restful resources
	 *
	 * @var        array
	 */
    private $_allowResources = ['Users','postmessage'];
    /**
	 * Allowed Request Methods
	 *
	 * @var        array
	 */
    private $_allowRequestMethods = ['GET','POST','PUT','DELETE','OPTIONS'];
    /**
	 * http status codes
	 *
	 * @var        array
	 */
	private $_statusCodes = [
		200 => 'OK',
		204 => 'No Content',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		500 => 'Server Internet Error'
     ];

     /**
	 * constructor
	 *
	 * @param      User     $_user     The user
	 * @param      PostMessage  $_postMessage  The post message
	 */
	public function __construct(User $_user, Posts $_postMessage){
		$this->_user 	= $_user;
		$this->_postMessage = $_postMessage;
    }

    public function run(){
		try{
			$this->_setupRequestMethod();
			$this->_setupResource();
			// $this->_setId();
			if($this->_resourceName == 'Users'){
				return $this->_json($this->_handleUser());
			}else{
				return $this->_json($this->_handlePost());
			}
		}catch(Exception $e){
			$this->_json(['error'=>$e->getMessage()],$e->getCode());
		}

    }

    /**
    * Setting up the api request method
    */
   private function _setupRequestMethod(){
       $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
       if(!in_array($this->_requestMethod, $this->_allowRequestMethods)){
           throw new Exception("not allowed method", 405);
       }
   }

   /**
	 * setting up the api resources
	 */
	private function _setupResource(){
		$path = $_SERVER['REQUEST_URI'];
		$params = explode('/',$path);
		$this->_resourceName = $params[3];
		if(!in_array($this->_resourceName, $this->_allowResources)){
			throw new Exception("permission denied", 405);
		}
		if(!empty($params[5])){
			$this->_id = $params[5];
		}
    }

    /**
	 * converting to json
	 *
	 * @param      array  $array  The array
	 */
	private function _json($array, $code=0){
		if($array === null && $code === 0){
			$code = 204;
		}
		if($array !== null && $code === 0){
			$code = 200;
		}
		header("HTTP/1.1 ".$code."  ".$this->_statusCodes[$code]);
		header("Content-Type=application/json;charset=UTF-8 ");
		if($array !== null){
			echo  json_encode($array, JSON_UNESCAPED_UNICODE);
		}
		exit();
    }

    /**
	 * handling user registration
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     <type>     ( description_of_the_return_value )
	 */
	private function _handleUser(){
		if($this->_requestMethod != 'POST'){
			throw new Exception("request method should be post", 405);
		}
		$body = $this->_getBodyParams();
		if(empty($body['username'])){
			throw new Exception("username shouldnot empty", 400);
		}
		if(empty($body['password'])){
			throw new Exception("password should not empty", 400);
		}
		return $this->_user->register($body['username'],$body['password']);
    }

    private function _handlePost(){
		switch ($this->_requestMethod){
			case 'POST':
				return $this->_handlePostCreate();
			case 'PUT':
				return $this->_handlePostEdit();
			case 'DELETE':
				return $this->_handlePostDelete();
			case 'GET':
				if(empty($this->_id)){
					return $this->_handlePostList();
				}else{
					return $this->_handlePostView();
				}
			default:
				throw new Exception("unknown request method", 405);
		}
    }

    private function _handlePostCreate(){
		$body = $this->_getBodyParams();
		if(empty($body['title'])){
			throw new Exception("Title should not be empty", 400);
		}
		if(empty($body['content'])){
			throw new Exception("Content should not be empty", 400);
		}
		// $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		try {
			$postMessage = $this->_postMessage->create($body['title'],$body['content'], 1);
			return $postMessage;
		} catch (Exception $e) {
			if(!in_array($e->getMessage(), [
                   ErrorCode::POST_TITLE_CANNOT_EMPTY,
                   ErrorCode::POST_CONTENT_CANNOT_EMPTY
				])
			){
				throw new Exception($e->getMessage(), 400);

			}
			throw new Exception($e->getMessage(), 500);
		}
    }

    private function _handlePostDelete(){
		// $user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		try {
			$postMessage = $this->_postMessage->view($this->_id);
			if($postMessage['user_id'] != 1){
				throw new Exception("Permission denied", 403);
			}
			$this->_postMessage->delete(1,$postMessage['id']);
			return null;
		} catch (Exception $e) {
			if($e->getCode() < 100){
				if($e->getCode() == ErrorCode::POST_NOT_FOUND){
					throw new Exception($e->getMessage(), 404);
				}else{
					throw new Exception($e->getMessage(), 400);
				}
			}else{
				throw $e;
			}
		}
    }

    /**
	 * handling postmessage list
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     <type>     ( description_of_the_return_value )
	 */
	private function _handlePostList(){
		//$user = $this->_userLogin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$size = isset($_GET['size']) ? $_GET['size'] : 10;
		if($size > 100){
			throw new Exception("Pagination cannot be greater than 100", 400);
		}
    header("Content-type:application/json");
		return $this->_postMessage->getList(1,$page,$size);
	}

	/**
	 * handle single post message
	 *
	 * @throws     Exception  (description)
	 *
	 * @return     <type>     ( description_of_the_return_value )
	 */
	private function _handlePostView(){
		try {
      header("Content-type:application/json");
			return [$this->_postMessage->view($this->_id)];
		} catch (Exception $e) {
			if($e->getMessage() == ErrorCode::POST_NOT_FOUND){
				throw new Exception($e->getMessage(), 404);
			}else{
				throw new Exception($e->getMessage(), 500);
			}
		}
	}

    /**
	 * User authentication
	 *
	 * @param      <type>  $PHP_AUTH_USER  The php auth user
	 * @param      <type>  $PHP_AUTH_PW    The php auth pw
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private function _userLogin($PHP_AUTH_USER, $PHP_AUTH_PW){
		try {
			return $this->_user->login($PHP_AUTH_USER,$PHP_AUTH_PW);
		} catch (Exception $e) {
			if(in_array($e->getCode(),[
					ErrorCode::USERNAME_CANNOT_EMPTY,
					ErrorCode::PASSWORD_CANNOT_EMPTY,
					ErrorCode::USERNAME_OR_PASSWORD_INVALID
				])){
				throw new Exception($e->getMessage(), 400);
			}
			throw new Exception($e->getMessage(), 500);
		}
	}

    private function _getBodyParams(){
		$raw = file_get_contents('php://input');
		if(empty($raw)){
			throw new Exception("Request parameter error", 400);
		}
		return json_decode($raw,true);
	}

}

$user = new User($pdo);
$post = new Posts($pdo);
$restful = new RestAPI($user, $post);
$restful->run();
