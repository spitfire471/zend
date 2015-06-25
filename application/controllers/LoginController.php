<?php

class LoginController extends Zend_Controller_Action {
	
	function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }
    function indexAction()
    {
        $this->_redirect('/');
    }
	
	function loginAction()
    {
        $this->view->message = '';
        if ($this->_request->isPost()) {
            // collect the data from the user
            Zend_Loader::loadClass('Zend_Filter_StripTags');
            $f = new Zend_Filter_StripTags();
            $name = $f->filter($this->_request->getPost('name'));
            $pass = $f->filter($this->_request->getPost('pass'));
			$pass = md5($pass);
            if (empty($name)) {
                $this->view->message = 'Please provide a username.';
            } else {
                // setup Zend_Auth adapter for a database table
                Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				$db = Zend_Db_Table::getDefaultAdapter();
                $authAdapter = new Zend_Auth_Adapter_DbTable($db);
                $authAdapter->setTableName('users');
                $authAdapter->setIdentityColumn('name');
                $authAdapter->setCredentialColumn('pass');
                $authAdapter->setIdentity($name);
                $authAdapter->setCredential($pass);
                // do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success: store database row to auth's storage
                    // system. (Not the password though!)
					$data = $authAdapter->getResultRowObject(null, 'pass');
					$auth->getStorage()->write($data);
					$auth = Zend_Auth::getInstance();
					$user = $auth->getIdentity();
					$activated = $this->view->escape(ucfirst($user->activated));
						//user activation check
						if ($activated == "1" ){
							$this->_redirect('/');
						}
						else {
							Zend_Auth::getInstance()->clearIdentity();
							$this->view->message = 'User not activated.';
							
						}

        

							//$this->_redirect('/');
				
                } else {
                    // failure: clear database row from session
                    $this->view->message = 'Login failed.';
                }
            }
        }
        $this->view->title = "Log in";
        $this->render();
    }
	
	function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
	

}
