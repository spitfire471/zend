<?php

class UsersController extends Zend_Controller_Action {
			
    function indexAction() {
        $this->view->title = "User administration";
		
		$user = new Application_Model_User();
		$this->view->users = $user->fetchAll();
	}
	function deleteAction() {
		$this->view->title = "Delete user";
		$user = new Application_Model_User();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();
			$id = (int)$this->_request->getPost('id');
			$del = $filter->filter($this->_request->getPost('del'));

			if ($del == 'Yes' && $id > 0) {
				$where = 'id = ' . $id;
				$rows_affected = $user->delete($where);
			}
		} else {
			$id = (int)$this->_request->getParam('id');

			if ($id > 0) {
				// only render if we have an id and can find the vehicle.
				$this->view->user = $user->fetchRow('id='.$id);

				if ($this->view->user->id > 0) {
					// render template automatically
					return;
				}
			}
		}
		// redirect back to the vehicle list unless we have rendered the view
    $this->_redirect('/users');
	}	
	
	function changepermissionAction() {
	
		$this->view->title = "Change user permission";
		$user = new Application_Model_User();
		$id = (int)$this->_request->getParam('id');
		$userRow = $user->fetchRow($user->select()->where('id = ?', $id));
		$userArray = $userRow->toArray();
		
		if ($userArray['permission'] == 0){
			$insertValue="1";
		}
		if ($userArray['permission'] == 1){
			$insertValue="0";
		}
		$data = array('permission' => $insertValue,);
		$where = 'id = ' . $id;
		$user->update($data, $where);
		$this->_redirect('/users');
		return;
	}
}
?>