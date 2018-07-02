<?php
require_once __DIR__.'/ErrorCode.php';

class User{
    private $_db;

    public function __construct($_db){
		$this->_db = $_db;
  }
  
  /**
	 * Authentication
	 *
	 * @param      <type>  $username  The username
	 * @param      <type>  $password  The password
	 */
	public function login($username, $password){
		if(empty($username)){
			throw new Exception("User username required", ErrorCode::USERNAME_CANNOT_EMPTY);
		}
		if(empty($password)){
			throw new Exception("User password required", ErrorCode::PASSWORD_CANNOT_EMPTY);
    }
    
		$sql = 'SELECT * FROM `user` WHERE `username` = :username AND `password` = :password';
		$password = $this->_md5($password);
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		if(!$stmt->execute()){
			throw new Exception("internal server error", ErrorCode::SERVER_INTERNAL_ERROR);
		}
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$user){
			throw new Exception("invalid user", ErrorCode::USERNAME_OR_PASSWORD_INVALID);
		}
		return $user;
	}

	/**
	 * register a new user
	 *
	 * @param      <type>  $username  The username
	 * @param      <type>  $password  The password
	 */
	public function register($username,$password){
		if(empty($username)){
			throw new Exception("User username required", ErrorCode::USERNAME_CANNOT_EMPTY);
		}
		if(empty($password)){
			throw new Exception("User password required", ErrorCode::PASSWORD_CANNOT_EMPTY);
		}
		if($this->_isUsernameExists($username)){
			throw new Exception("username exist", ErrorCode::USERNAME_EXISTS);
		}
		
		$sql = 'INSERT INTO `user`(`username`,`password`,`addtime`) VALUES(:username,:password,:addtime)';
		$addtime = time();
		$password = $this->_md5($password);
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		$stmt->bindParam(':addtime',$addtime);
		if(!$stmt->execute()){
			throw new Exception("internal server error", ErrorCode::REGISTER_FAIL);
		}
		return [
			'userId'	=> $this->_db->lastInsertId(),
			'username'  => $username,
			'addtime'   => $addtime
		];
	}

	/**
	 * md5 creation for password
	 *
	 * @param      <type>  $string  The string
	 * @param      string  $key     The key
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private function _md5($string, $key='random'){
		return md5($string.$key);
	}

	/**
	 * checking Username exist
	 *
	 * @param      <type>   $username  The username
	 *
	 * @return     boolean  True if username exists, False otherwise.
	 */
	private function _isUsernameExists($username){
		$exists = false;
		$sql = 'SELECT * FROM `user` WHERE `username`=:username';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':username',$username);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return !empty($result);
	}
}