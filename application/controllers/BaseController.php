<?php

require_once('Config.php');

class BaseController extends Zend_Controller_Action
{
    public function init()
    {
    	Config::init($this);
    	
        $this->req = $this->getRequest();
        
    	$this->view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
    	$this->view->controllerUrl = $this->view->baseUrl.'/'.$this->getRequest()->getControllerName();
    }
    
    protected function log($msg) {
    	echo $msg.'<br>';
    }
    
    public function getMandatoryParam($id, $default = null, $throwOnMissing = false) {
    	return $this->getParam($id, null, true);
    }
    
    public function getParam($id, $default = null, $throwOnMissing = false)
    {
    	$v = $this->req->getParam($id);
    	if (!isset($v) && $throwOnMissing)
    		throw new Exception("missing request parameter: $id");
    		
    	return isset($v)?$v:$default;
    }
    
    
}

