<?php
require_once 'DbAdapter.php';

class Questionaire {
	const UUID_PARAM_PREFIX = '__uuid__';
	
	protected $uuid;
	protected $json;
	protected $data;
	
	// get the json and instantiate with the retrieved json as data
	public static function getInstance($uuid, $doLoadLocalFallBack = true) {
		$productJsonUrl = Config::get('kpRestUrl').'/form/'.$uuid.'/main';
		
		try {
			$c = new Zend_Http_Client($productJsonUrl);
			$res = $c->request();
			if (!$res->isSuccessful())
				throw new Exception('retrieving json object failed');
			
			$json = $res->getBody();
		}
		catch (Exception $e) {
			if (!$doLoadLocalFallBack || ($json = file_get_contents(Config::get('kpLocalJsonPath').'/'.$uuid.'.json')) === false)
				throw $e;
		}
		
		$questionaire = new self;
		$questionaire->json = $json;
		$questionaire->data = Zend_Json::decode($questionaire->json);
		
		return $questionaire;
	}
	
	public static function getUuids($asOptions = true) {
		$options = array();
		 
		if (($file = fopen(Config::get('productsCsv'), 'r')) === false)
			throw new Exception('cannot open products csv');
		
		while (($pair = fgetcsv($file, null, ';')) !== false)
			$options[$pair[1]] = $pair[0];
		fclose($file);
		 
		return $asOptions?$options:array_keys($options);
	}
	
	// data section
	
	public function getData() {
		return $this->data;
	}
	
	public function getJSON() {
		return $this->json;
	}
	
	
	// page section
	
	public function getPageCount() {
		return count($this->data['pages']);
	}
	
	public function getPage($i) {
		if ($i > $this->getPageCount())
			return false;
		
		$page = $this->data['pages'][$i - 1];
		$page['number'] = $i;
		
		return $page;
	}
	
	// question section
	
	// to facilitate easy processing in getAnswersFromRequest,
	// the uuids that are used as names for input html elements
	// are prefixed with '__uuid__'
	protected static function isWrappedQuestionIdParam($uuid) {
		return strpos($uuid, self::UUID_PARAM_PREFIX) === 0;
	}
	
	protected static function unwrapQuestionIdParam($uuid) {
		return substr($uuid, strlen(self::UUID_PARAM_PREFIX));
	}
	
	public static function wrapQuestionIdParam($uuid) {
		return self::UUID_PARAM_PREFIX.$uuid;
	}
	
	public static function isMultipleAnswerQuestion($question) {
		return isset($question['multiple_answers']) && $question['multiple_answers'] === true;
	}
	
	// answer section
	
	// process the answers in this request parameters
	public function getAnswersFromRequest($params) {
		$answers = array();
		foreach ($params as $question => $answer) {
			if (!self::isWrappedQuestionIdParam($question))
				continue;
				
			$question = self::unwrapQuestionIdParam($question);
			
			// go in array mode for checkbox processing
			if (!is_array($answer))
				$answer = array($answer);
			
			foreach ($answer as $subAnswer)
				$answers[] = array($question, $subAnswer);
		}
		
		return $answers;
	}


	// save the answers for this user in row format
	public static function save($userId, $answers) {
		$db = DbAdapter::getKpInstance();
		
		foreach ($answers as $answer) {
			$data = array(
				'user_id' => $userId,
				'question_uuid' => $answer[0],
				'answer_uuid' => $answer[1]
			);
			
			$db->insert('questionaire', $data);
		}
	}
}