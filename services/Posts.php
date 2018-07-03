<?php
require_once __DIR__."/ErrorCode.php";

class Posts {
    private $_db;

    /**
	 * database connection object
	 *
	 * @param      <type>  $_db    The database
	 */

    public function __construct($_db) {
        $this->_db = $_db;
    }
    /**
	 * creating the post message
	 *
	 * @param      <type>  $title    The title
	 * @param      <type>  $content  The content
	 * @param      <type>  $userId   The user identifier
	 */
     public function create($title, $content, $userId) {
        if(empty($title)) {
            throw new Exception("Empty Content", ErrorCode::POST_TITLE_CANNOT_EMPTY);
        }

        if(empty($content)) {
        throw new Exception("content cannot be empty", ErrorCode::POST_CONTENT_CANNOT_EMPTY);
        }

        $add_time = time();
        $sql = 'INSERT INTO `posts` (`title`,`content`,`addtime`,`user_id`) VALUES(:title,:content,:addtime,:user_id)';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':title',$title);
        $stmt->bindparam(':content',$content);
        $stmt->bindparam(':addtime',$addtime);
        $stmt->bindparam(':user_id',$userId);
        if(!$stmt->execute()){
        throw new Exception("Failed creation of post message", ErrorCode::POST_CREATE_FAIL);
        }

        return [
        'postId' => $this->_db->lastInsertId(),
        'title' 	=> $title,
        'content' 	=> $content,
        'addtime' 	=> $addtime,
        'userId' 	=> $userId
        ];
     }

     /**
	 * View a post message based on it's ID
	 *
	 * @param      <type>     $postId  The post message identifier
	 *
	 * @throws     Exception  (description)
	 */
	public function view($postId){
		if(empty($postId)){
			throw new Exception("Empty ID", ErrorCode::POST_ID_CANNOT_EMPTY);
		}

		$sql = 'SELECT * FROM `posts` WHERE `id` = :id';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id',$postId);
		$stmt->execute();
		$postMessage = $stmt->fetch(PDO::FETCH_ASSOC);
		$title = $postMessage['title'];
		$postMessage['palindrom'] = $this->checkPalindrom($title);
		if(empty($postMessage)){
			throw new Exception("post message not found", ErrorCode::POST_NOT_FOUND);
		}
		return $postMessage;
	}

    /**
	 * Delete Post Message
	 *
	 * @param      <type>  $userId     The user identifier
	 * @param      <type>  $postId  The article identifier
	 */
	public function delete($userId, $postId){
		$postMessage = $this->view($postId);
		if($postMessage['user_id'] !== $userId){
			throw new Exception("Permission Denied", ErrorCode::PERMISSION_DENIED);
		}

		$sql = 'DELETE FROM `posts` WHERE `id` = :id AND `user_id` = :user_id';
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id',$postId);
		$stmt->bindParam(':user_id',$userId);
		if(!$stmt->execute()){
			throw new Exception("Delete failed", ErrorCode::POST_DELETE_FAIL);
		}
		return $stmt->execute();
	}
    /**
	 * Checking whether a Post Message is Palindrom or Not
	 *
	 * @param      <type>  $postMessage     The message identifier
	 *
	 */
    public function checkPalindrom($postMessage){
        // echo 'test';exit;
        $strLen = strlen($postMessage)-1;
        $revStr = '';
        for($i=$strLen; $i>=0; $i--){
            $revStr.=$postMessage[$i];
        }
        if($revStr == $postMessage)
            return 1;
        else
            return 0;
	}

    /**
	 * get all post messages
	 *
	 * @param      <type>   $userId  The user identifier
	 * @param      integer  $page    The page
	 * @param      integer  $size    The size
	 */
	public function getList($userId, $page=1, $size=10){
		if($size > 100){
			throw new Exception("size exceeded", ErrorCode::PAGE_SIZE_TO_BIG);
		}

		$sql = 'SELECT * FROM `posts` WHERE user_id = :user_id LIMIT :limit,:offset';
		$limit = ($page - 1) * $size;
		$limit = $limit < 0 ? 0 : $limit;
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':user_id',$userId);
		$stmt->bindParam(':limit',$limit);
		$stmt->bindParam(':offset',$size);
		if(!$stmt->execute()){
			throw new Exception("Error Processing Request", 1);
		}
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}

}
