<?php

header('Content-type: text/html; charset=utf-8');

class Linux {
	
	private $db;
	
	function __construct($db) {
	    $this->db = $db;
	}
	
	/* --- FIRST RUN --- */
	public function firstrun() {
	    $db = $this->db;
		$query = " 
            
            CREATE TABLE IF NOT EXISTS `linux_categories` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(45) CHARACTER SET utf8 NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id_UNIQUE` (`id`),
              UNIQUE KEY `categorie_UNIQUE` (`title`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            
            INSERT INTO `linux_categories` VALUES (1,'dir navigation'),(2,'file searching'),(3,'archives and compression');
            
            CREATE TABLE IF NOT EXISTS `linux_commands` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `category` int(11) DEFAULT '0',
              `command` varchar(180) CHARACTER SET utf8 NOT NULL,
              `rootonly` tinyint(4) NOT NULL DEFAULT '0',
              `description` text COLLATE utf8_unicode_ci,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id_UNIQUE` (`id`),
              UNIQUE KEY `command_UNIQUE` (`command`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            
            INSERT INTO `linux_commands` VALUES (1,1,'cd',0,'Change directory'),(2,2,'find',0,'Search files');
            
            CREATE TABLE IF NOT EXISTS `linux_examples` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `command` int(11) NOT NULL,
              `example` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `description` text COLLATE utf8_unicode_ci,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id_UNIQUE` (`id`),
              UNIQUE KEY `example_UNIQUE` (`example`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            
            INSERT INTO `linux_examples` VALUES (1,1,'cd -','Go to previous directory'),(2,1,'cd','Go to HOME directory');

        "; 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute();} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	/* --- ADDING --- */
	public function addCommand($data) {
	    
	    $db = $this->db;
		$query = " 
            INSERT INTO linux_commands ( 
                category, 
                command,  
                rootonly, 
                description
            ) VALUES ( 
                :category, 
                :command,  
                :rootonly, 
                :description
            ) 
        "; 
        
        $query_params = array( 
            ':category' => $data['category'], 
            ':command' => $data['command'], 
            ':rootonly' => $data['rootonly'], 
            ':description' => $data['description']
        ); 
        
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function addCategory($title) {
	    $db = $this->db;
		$query = " 
            INSERT INTO linux_categories ( 
                title
            ) VALUES ( 
                :title
            ) 
        "; 
        
        $query_params = array( 
            ':title' => $title
        ); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function addExample($data) {
	    $db = $this->db;
		$query = " 
            INSERT INTO linux_examples ( 
                command,
                example,
                description
            ) VALUES ( 
                :command,
                :example,
                :description
            ) 
        "; 
        
        $query_params = array( 
            ':command' => (int)$data['command'],
            ':example' => $data['example'],
            ':description' => $data['description'],
        ); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	/* --- UPDATING --- */
	public function updateCommand($id, $data) {
	    $db = $this->db;
		$query = " 
             UPDATE linux_commands SET  
                category = :category, 
                command = :command,  
                rootonly = :rootonly, 
                description = :description
             WHERE id = :id
        "; 
        
        $query_params = array( 
            ':id' => (int)$id, 
            ':category' => $data['category'], 
            ':command' => $data['command'], 
            ':rootonly' => $data['rootonly'], 
            ':description' => $data['description']
        ); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function updateCategory($id, $title) {
	    $db = $this->db;
		$query = " 
            UPDATE linux_categories 
             SET title = :title
             WHERE id = :id
        "; 
        
        $query_params = array( 
            ':id' => (int)$id,
            ':title' => $title
        ); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function updateExample($id, $data) {
	    $db = $this->db;
		$query = " 
            UPDATE linux_examples SET 
                example = :example,
                description = :description
             WHERE id = :id
        "; 
        
        $query_params = array( 
            ':id' => (int)$id,
            ':example' => $data['example'],
            ':description' => $data['description'],
        ); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	/* --- GETTING --- */
	public function getCommand($id) {
	    $db = $this->db;
		$query = "SELECT * FROM linux_commands WHERE id = :id"; 
		$query_params = array(':id' => $id); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
		
		$command = $stmt->fetch();
		
		return $command;
	}
	
	public function getRandomCommand() {
		$commands = $this->getCommands();
		return $commands[rand(0, count($commands))];
	}
	
	public function getCommands($q="", $category=0) {
	    $db = $this->db;
	    $category = (int)$category;
		$query = "SELECT * FROM linux_commands ".(($category>0)?"WHERE category = $category":"");
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute();} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; }
		
		$commands = $stmt->fetchAll();
		
		if ($q) {
		    for($i=0; $i<count($commands); $i++) {
		        if (!(strpos($commands[$i]['command'], $q) !== false || strpos($commands[$i]['description'], $q) !== false)) {
		            unset($commands[$i]);
		        }
		    }
		}
		
		return $commands;
	}
	
	public function getCategories() {
		$db = $this->db;
		
		$query = "SELECT * FROM linux_categories"; 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute();} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
		
		$categories = $stmt->fetchAll();
		
		usort($categories, array($this, "sortCategories"));
		return $categories;
	}
	
	public function sortCategories($cat1,$cat2) {
	    return ($cat1['id'] < $cat2['id']) ? -1 : 1;
	}
	
	public function getExamples($command_id) {
	    $db = $this->db;
	    $command_id = (int)$command_id;
		$query = "SELECT * FROM linux_examples WHERE command = $command_id";
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute();} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; }
		
		$examples = $stmt->fetchAll();
		
		return $examples;
	}
	
	/* --- REMOVING --- */
	public function removeCommand($id) {
	    if (!empty($this->getExamples($id))) {
	        throw new Exception($this->_requestStatus(406));
	    }
	    
	    $db = $this->db;
		$query = "DELETE FROM linux_commands WHERE id = :id"; 
		$query_params = array(':id' => $id); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function removeCategory($id) {
	    if (!empty($this->getCommands("", $id))) {
	        throw new Exception($this->_requestStatus(406));
	    }
	    
	    $db = $this->db;
		$query = "DELETE FROM linux_categories WHERE id = :id"; 
		$query_params = array(':id' => $id); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	public function removeExample($id) {
	    $db = $this->db;
		$query = "DELETE FROM linux_examples WHERE id = :id"; 
		$query_params = array(':id' => $id); 
		
		try {$stmt = $db->prepare($query); $result = $stmt->execute($query_params);} 
		catch(PDOException $ex) { echo "Failed to run query: " . $ex->getMessage(); exit; } 
	}
	
	private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            403 => 'Unknown Command',
            404 => 'No Command Found',
            405 => 'No Category Passed',
            406 => 'Category has Commands. Remove Commands from Category first.',
            407 => 'Command has Examples. Remove Examples from Command first.',
            500 => 'Internal Server Error'
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }

}
