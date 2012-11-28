<?php
require_once 'BaseController.php';
require_once 'Questionaire.php';
require_once 'User.php';
require_once 'Config.php';
require_once 'Cache.php';
require_once 'DbAdapter.php';

class IndexController extends BaseController
{
    public function init() {
    	parent::init();
    	
    	// nothing yet
    }
    
    public function indexAction() {
    	try {
	    	$this->view->options = Questionaire::getUuids($asOptions = true);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    		$this->renderScript('error.phtml');
    		return;
    	}
    }
    
    // 20121117
    // do only once not to be dependent on kp in the future and keep this application going...
    public function getalljsonAction() {
    	try {
	    	foreach (Questionaire::getUuids($asOptions = false) as $uuid) {
	    		try {
	    			$json = Questionaire::getInstance($uuid, $doLoadLocalFallBack = false)->getJSON();
	    		}
	    		catch (Exception $e) {
	    			// not all csv entries are valid...bad luck, but continue
	    			echo $e->getMessage().'<br>';
	    			continue;
	    		}
	    		
	    		$filePath = Config::get('kpLocalJsonPath').'/'.$uuid.'.json';
	    		echo "doing $filePath...<br>";
	    		if (($file = fopen($filePath, 'w')) === false)
	    			throw new Exception('cannot open json file for writing');
	    		
	    		if (fwrite($file, $json) === false)
	    			throw new Exception('cannot write');
	    		
	    		fclose($file);
	    	}
	    		
	    	exit;
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    		$this->renderScript('error.phtml');
    		return;
    	}
    }
}

