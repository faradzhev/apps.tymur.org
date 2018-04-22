<?php

require_once "common.php";

class ToolsUser {
	
	private $DATABASE;
	private $id;
	private $email;
	private $name;
	private $access;
	private $status;
	private $api;
	private $autosave;
	private $password;
	private $salt;
	
	public function __construct($DATABASE) {
		$this->DATABASE = $DATABASE;
	}
	
	//public function 
	
	public function updateSetting($setting, $value) {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		if ($setting == 'password') {
			$salt = $this->getRandomSalt();
			$encrypted_pass = $this->encryptPassword($value,$salt);
		}
		switch ($setting) {
			case 'password':
				$this->salt = $this->getRandomSalt();
				$this->password = $this->encryptPassword($value,$this->salt);
				$this->updateUser(true);
				break;
			case 'name':
				$this->name = $value;
				$this->updateUser();
				break;
			case 'autosave':
				$this->autosave = $value;
				$this->updateUser();
				break;
			default: 
				break;
		}
	}
	
	public function getUserSettings() {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		//get user settings here
		return $this->_requestStatus(500);
	}
	
	public function generateAPI() {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		$salt = $this->getRandomSalt();
		$api = '__' . $salt . '&' . hash('sha256', $salt.$this->email);
		$this->api = $api;
		$query = " 
			UPDATE users 
			SET 
				api = :api
			WHERE 
				id = :id 
		"; 
		$params = array(':api' => $api, ':id' => $this->id);
		$this->_queryDB($query,$params,false);
		
		return $this->_requestStatus(200,array('api'=>$api));
	}
	
	public function terminateToken($token) {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		/*
		$query = " 
			UPDATE tokens 
			SET 
				expire = :expire
			WHERE 
				token = :token AND user_id = :user_id
		"; 
		$params = array(':token' => $token, ':user_id' => $this->id, ':expire' => 0);
		*/
		$query = " 
			DELETE FROM tokens
			WHERE token = :token AND user_id = :user_id
		"; 
		$params = array(':token' => $token, ':user_id' => $this->id);
		$this->_queryDB($query,$params,false);
	}
	
	public function getAllTokens() {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		$query = " 
			SELECT *
			FROM tokens 
			WHERE user_id = :user_id"; 
		$params = array(':user_id' => $this->id);
		return $this->_queryDB($query,$params,true,true);
	}
	
	public function checkToken($token) {
		$query = " 
			SELECT *
			FROM tokens 
			WHERE token = :token"; 
		$params = array(':token' => $token);
		$result = $this->_queryDB($query,$params);
		
		//print_r($result);
		
		if ($result['token'] && $result['expire'] > time() /*&& $result['ip']==$_SERVER['REMOTE_ADDR']*/) {
			$query_update = " 
				UPDATE tokens 
				SET 
					last_active = :last_active
				WHERE 
					token = :token 
			"; 
			$params_update = array(':token' => $token, ':last_active' => time());
			$this->_queryDB($query_update,$params_update,false);
			
			
			$this->selectUser($result['user_id']);
			return $this->_requestStatus(200,array('token'=>$result['token'],
													'date'=>$result['date'],
													'expire'=>$result['expire'],
													'email'=>$this->email,
													'name'=>$this->name,
													'access'=>$this->access,
													'api'=>$this->api,
													'autosave'=>$this->autosave,
													'status'=>$this->status));
		}
		else {
			return $this->_requestStatus(431);
		}
		return $this->_requestStatus(500);
	}
	
	private function generateToken() {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		else {
			$expire = time()+strtotime("+1 year");
			$token = $this->encryptPassword($this->email . $this->name . $expire, $this->getRandomSalt());
			
			$query = 'INSERT INTO tokens (
									user_id,
									token,
									expire,
									last_active,
									date,
									ip,
									agent
								) VALUES ( 
									:user_id, 
									:token, 
									:expire, 
									:last_active,
									:date, 
									:ip,
									:agent
								) '; 
			$params = array(  
								':user_id'=>"".$this->id,
								':token'=>"$token",
								':expire'=>"$expire",
								':last_active'=>"".time(),
								':date'=>"".time(),
								':ip'=>"".$_SERVER['REMOTE_ADDR'],
								':agent'=>"".$_SERVER['HTTP_USER_AGENT']
							); 
			
			$this->_queryDB($query,$params,false);
			
			return $this->_requestStatus(200,array('token'=>$token,'expire'=>$expire));
		}
		return $this->_requestStatus(500);
	}
	
	public function selectUser($email_or_id, $include_pass = false) {
		if (!$email_or_id) {
			return $this->_requestStatus(404);
		}
		
		$query = " 
			SELECT *
			FROM users 
			WHERE email = :email_or_id OR id = :email_or_id"; 
		$params = array(':email_or_id' => $email_or_id);
		$user = $this->_queryDB($query,$params);
		
		if ($user['id']) {
			$this->id = $user['id'];
			$this->name = $user['name'];
			$this->email = $user['email'];
			$this->access = $user['access'];
			$this->status = $user['status'];
			$this->api = $user['api'];
			$this->autosave = $user['autosave'];
			if ($include_pass) {
				$this->password = $user['password'];
				$this->salt = $user['salt'];
			}
			return $this->_requestStatus(200);
		}
		else {
			return $this->_requestStatus(404);
		}
		return $this->_requestStatus(500);
	}
	
	public function updateUser($include_pass = false) {
		if (!$this->id) {
			return $this->_requestStatus(403);
		}
		
		$query = " 
			UPDATE users 
			SET
				name = :name,
				autosave = :autosave
			WHERE id = :user_id"; 
		$params = array(':user_id' => $this->id, ':name' => $this->name, ':autosave' => $this->autosave);
		
		if($include_pass) {
			$query = " 
			UPDATE users 
			SET
				name = :name,
				autosave = :autosaved,
				password = :password,
				salt = :salt
			WHERE id = :user_id";
			$params[':password'] = $this->password;
			$params[':salt'] = $this->salt;
		}
		
		$this->_queryDB($query,$params,false);
		
		return $this->_requestStatus(200);
	}
	
	public function login($email,$password) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return $this->_requestStatus(411);
		}
		else if (strlen("$password") < 8) {
			return $this->_requestStatus(413);
		}
		else {
			$result = $this->selectUser($email,true);
			$encrypted_pass = $this->encryptPassword($password,$this->salt);
			if ($result['code'] == 200 && $encrypted_pass == $this->password) {
				$token = $this->generateToken();
				if ($token['code'] == 200) {
					return $this->checkToken($token['data']['token']);
				}
			}
			else {
				return $this->_requestStatus(424);
			}
		}
		return $this->_requestStatus(500);
	}
	
	public function register($name,$email,$password,$repeat_password) {
		if (!$name) {
			return $this->_requestStatus(410);
		}
		else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return $this->_requestStatus(411);
		}
		else if ($password != $repeat_password) {
			return $this->_requestStatus(412);
		}
		else if (strlen($password)<8) {
			return $this->_requestStatus(413);
		}
		else {
			$user_check = new ToolsUser($this->DATABASE);
			$existing = $user_check->selectUser($email);
			if ($existing['code'] == 200) {
				return $this->_requestStatus(415);
			}
			
			$salt = $this->getRandomSalt();
			$encrypted_pass = $this->encryptPassword($password,$salt);
			
			$query = " 
				INSERT INTO users ( 
									email, 
									name, 
									password, 
									salt, 
									registered,
									status,
									autosave,
									access
								) VALUES ( 
									:email, 
									:name, 
									:password, 
									:salt, 
									:registered,
									:status,
									:autosave,
									:access
								) "; 
			$params = array(  
								':email'=>"$email",
								':name'=>"$name",
								':password'=>"$encrypted_pass",
								':salt'=>"$salt",
								':registered'=>"".time(),
								':status'=>'active',
								':autosave'=>'optional',
								':access'=>'user'
							); 
							
			$this->_queryDB($query,$params,false);
			
			//$this->selectUser($email);
			//$this->generateAPI();
			
			return $this->_requestStatus(200);
			
		}
		return $this->_requestStatus(500); 
	}
	
	private function encryptPassword($password,$salt) {
		$e_password = hash('sha256', $password.$salt); 
		for($round = 0; $round < 65535; $round++) { 
			if ($round % 2) {
				$e_password = hash('sha256', $e_password . $salt);
			}
			else {
				$e_password = hash('sha256', $salt . $e_password);
			}
		} 
		return $e_password;
	}
	
	private function getRandomSalt() {
		return dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
	}
	
	private function _requestStatus($code,$data='') {
        $status = array(  
            200 => 'OK',
            
            410 => "Ім'я не може бути пустим",
            411 => 'Невірний E-mail',
            412 => 'Паролі не співпадають',
            413 => 'Довжина паролю має містити хоча б 8 символів',
            414 => 'Помилка реєстрації',
            415 => 'Email вже використовується',
            
            424 => 'Email або пароль не вірні',
            425 => 'Підтвердіть свою електронну пошту',
            
            431 => 'Сессія скінчилася, повторіть вхід',
            432 => 'Ваш обліковий запис заблоковано',
            
            403 => "Користувача не вибрано. Успішний результат попереднього виклику методу selectUser() є обов'язковим!",
            404 => 'Користувач не існує.',
            500 => 'Internal Server Error'
        ); 
        $code = ($status[$code])?$code:500;
        return array('status'=>$status[$code],'code'=>$code,'data'=>$data);
    }
    
    private function _queryDB($query, $params, $is_fatchable = true, $is_multiple_request = false) {
		$db = $this->DATABASE;
		/*
		$query = " 
			SELECT 
				id, 
				username, 
				password, 
				salt, 
				email,
				type,
				loginattempts,
				status 
			FROM users 
			WHERE 
				email = :email 
		"; 
		*/
		//The parameter values 
		/*
		$params = array( 
			':email' => $_POST['email'] 
		); 
		*/
		try { $stmt = $db->prepare($query); $result = $stmt->execute($params); } 
		catch(PDOException $ex) { die("Failed to run query: " . $ex->getMessage()); } 
		
		if ($is_fatchable) {
			if ($is_multiple_request) {
				$result = $stmt->fetchAll(); 
			}
			else {
				$result = $stmt->fetch(); 
			}
			return $result;
		}
	}
}

//$tu = new ToolsUser($db);

//print_r($tu->login('faradzhev.tymur@gmail.com','LwNbTsA#2tl'));

?>
