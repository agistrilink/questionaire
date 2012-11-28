<?php
require_once 'DbAdapter.php';
class User {
	protected $data;
	
	public static function getInstance($data) {
		$user = new self;
		$user->data = $data;
		
		return $user;
	}
	
	public function exists() {
		$db = DbAdapter::getKpInstance();
				
		$query = $db->select()->from('users')
			->where('name=?', $this->data['name'])
			->where('surname=?', $this->data['surname']);
		$res = $db->fetchRow($query);
		
		return !empty($res);
	}
	
	public function getId() {
		$db = DbAdapter::getKpInstance();
				
		$query = $db->select()->from('users')
			->columns(array('id'))
			->where('name=?', $this->data['name'])
			->where('surname=?', $this->data['surname']);
		$res = $db->fetchRow($query);
		
		if (empty($res))
			return false;
		
		return $res['id'];
	}
	
	public function save() {
		if ($this->exists())
			throw new Exception('user insert: duplicate user');
		
		$db = DbAdapter::getKpInstance();
		$db->insert('users', $this->data);
		
		return $db->lastInsertId();
	}
}