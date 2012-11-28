<?php

class DbAdapter extends Zend_Db_Adapter_Mysqli {
	protected $dbId;
	static protected $dbs = array();
	
	public function __construct($config) {
		parent::__construct($config);
	}
	
	public static function getInstance($dbId) {
		if (array_key_exists($dbId, self::$dbs))
			return self::$dbs[$dbId];
		
		$config = array(
			'host' => Config::get('hostname', 'db'),
			'dbname' => $dbId,
			'username' => Config::get('username', 'db'),
			'password' => Config::get('password', 'db')
		);
		$db = new self($config);
		$db->dbId = $dbId;
		
		return self::$dbs[$dbId] = $db;
	}
	
	public static function getKpInstance() {
		return self::getInstance('kp');
	}
	
	protected function __connect() {	
		if ($this->_connection)
            return;            
        
		$this->_config['host'] = Config::get('host', 'db');
		$this->_config['dbname'] = $this->dbId;
		$this->_config['username'] = Config::get('username', 'db');
		$this->_config['password'] = Config::get('password', 'db');

		parent::_connect();
	}
}