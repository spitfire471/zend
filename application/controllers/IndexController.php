
<?php

class IndexController extends Zend_Controller_Action {
	
	function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login/login');
        }
    }
	
	function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
    }

public function indexAction() {
		$this->_redirect('/vehicle');
	}

}

