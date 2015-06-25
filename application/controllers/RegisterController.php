<?php

class RegisterController extends Zend_Controller_Action {
		
	function init(){
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }
	
    function indexAction(){
        $this->view->form = new Application_Form_Register();
    }
	
	function registerAction() {
		$user = new Application_Model_User();
		$this->view->form = new Application_Form_Register();
        if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$name = trim($filter->filter($this->_request->getPost('name')));
			$pass = trim($filter->filter($this->_request->getPost('pass')));
			$email = trim($filter->filter($this->_request->getPost('email')));
			$pass = md5($pass);
			
			$userRow = $user->fetchRow($user->select()->where('name = ?', $name));
			//$userArray = $userRow->toArray();
			if ($userRow !=''){
				echo "User name already exist";
			}
			else{
				$userRow = $user->fetchRow($user->select()->where('email = ?', $email));
				//$userArray = $userRow->toArray();
				if ($userRow !=''){
					echo "Email already exist";	
				}
				else {
					if ($this->view->form->isValid($this->getRequest()->getPost())){
						$hash=md5(microtime());
						
						$mail = new Zend_Mail();
						
						$mail->setBodyText('Hello 
						Your email regisrated on website spitfire.mydev.org.ua 
						to confirm your account click link bellow
						http://spitfire.mydev.org.ua/register/confirmuser/&hash=<');
						$mail->setFrom('spitfire.net@gmail.com',  'Some Sender');
						$mail->addTo('spitfire.ukr@gmail.com' ,'Some Sender');
						$mail->setSubject('TestSubject');
						$mail->send();
						if ($name != '' && $pass != '') {
							$data = array(
								'name' => $name,
								'email' => $email,
								'pass' => $pass,
								
						);
						$user->insert($data);
						$this->_redirect('/');
						return;
						}
					}
					else {
						echo "Captcha wrong";
					}
				}
			}
		
		}
	}
	function confirmuserAction() {
		
		echo "hello";
	}
}