<?php
require_once('BaseController.php');
require_once('User.php');
require_once('Cache.php');
require_once('Questionaire.php');

class QuestionaireController extends BaseController
{
	
    public function init()
    {
    	parent::init();
    	
    	$this->view->addScriptPath('./application/views/scripts/questionaire');
    }

    public function indexAction() {
    	$this->_forward('page');
	}
	
	public function pageAction() {
		try {
			$this->view->uuid = $uuid = $this->req->getParam('uuid');
			if (!isset($uuid))
				throw new Exception('missing parameter uuid');			
			$questionaire = Questionaire::getInstance($uuid);

			$page = $this->req->getParam('page', 0);
			$this->view->answers = array();
			if ($page > 0) {
				$this->view->answers = array_merge(
					Zend_Json::decode($this->req->getParam('answers')),
					$questionaire->getAnswersFromRequest($this->req->getParams())
				);
			}

			if ($page >= $questionaire->getPageCount()) {
				$this->render('userinfo');
				return;
			}
			
			$this->view->page = $questionaire->getPage(++$page);
		}
		catch (Exception $e) {
			$this->view->error = $e->getMessage();
			$this->renderScript('error.phtml');
			return;
		}
	}

	public function completeAction() {
		try {
			$this->view->uuid = $uuid = $this->getMandatoryParam('uuid');
			$questionaire = Questionaire::getInstance($uuid);
		
			$userInfo = array(
				'name' => $this->getMandatoryParam('name'),
				'surname' => $this->getMandatoryParam('surname')
			);
//Zend_Debug::dump($userInfo);exit;
			$user = User::getInstance($userInfo);
			
			$userId = $user->getId();
			if ($userId === false)
				$userId = $user->save();
			
			$answers = Zend_Json::decode($this->getMandatoryParam('answers'));
//echo $userId;exit;			
//Zend_Debug::dump($answers);exit;
			$questionaire->save($userId, $answers);
		}
		catch (Exception $e) {
			$this->view->error = $e->getMessage();
			$this->renderScript('error.phtml');
			return;
		}
	}
	
	public function cacheAction() {
		try {
			//		echo Config::get('cacheDir');exit;
			$cache = Cache::getInstance();
			
			if(!(@$counter = $cache->load('counter')))
				$counter = 0;
			
			@$cache->save(++$counter, 'counter');
				
			echo $counter;exit;
		}
		
		catch (Exception $e) {
			$this->view->error = $e->getMessage();
			$this->renderScript('error.phtml');
			return;
		}
	}
				
}

