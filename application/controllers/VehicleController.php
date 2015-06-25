<?php

class VehicleController extends Zend_Controller_Action {
	
	function preDispatch(){
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login/login');
        }
    }
	
	function init()	{
		$this->view->baseUrl = $this->_request->getBaseUrl();
	}	
   
	function indexAction() {
        $this->view->title = "Vehicle";
		$auth = Zend_Auth::getInstance();
		$vehicle = new Application_Model_Vehicle();
		
		if($auth->hasIdentity()) {
			$user = $auth->getIdentity();
			$this->view->name = $user->name;
			$this->view->permission = $user->permission;
		}
		
		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$search = trim($filter->filter($this->_request->getPost('search')));
			$result = $vehicle->fetchAll($vehicle->select()->where("marka = ?", $search)
															->orWhere("model = ?", $search)
															->orWhere("color = ?", $search));
		}
		else {
		echo $search;
		$sort_by = $this->_request->getParam('sortBy');
		if ($sort_by !== NULL){
			$sort="".$sort_by." DESC";
		}
		else{
			$sort="id ASC";
		}		
		$result = $vehicle->fetchAll($vehicle->select()->order($sort));
		}
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->paginator=$paginator;
		
		
		
		$comment = new Application_Model_Comment();
		$commentRow = $comment->fetchAll("page_id = 'main'");		
		$commentArray = $commentRow->toArray();				
		$this->view->countComment=count($commentArray);	
		$this->view->commentArray=$commentArray;
	}


	
    function addAction() {
        $this->view->title = "Add Vehicle";

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$marka = trim($filter->filter($this->_request->getPost('marka')));
			$model = trim($filter->filter($this->_request->getPost('model')));
			$rik = trim($filter->filter($this->_request->getPost('rik')));
			$color = trim($filter->filter($this->_request->getPost('color')));
			$registration_number = trim($filter->filter($this->_request->getPost('registration_number')));
			$owner = trim($filter->filter($this->_request->getPost('owner')));
			$owner_phone = trim($filter->filter($this->_request->getPost('owner_phone')));
			$owner_address = trim($filter->filter($this->_request->getPost('owner_address')));
			$auth = Zend_Auth::getInstance();
			$user = $auth->getIdentity();
			$name = $user->name;				
			
			if ($marka != '' && $model != '' && $rik != '' && $color != '' && $registration_number != '' && $owner != '' && $owner_phone != '' && $owner_address != '') {
				$data = array(
					'marka' => $marka,
					'model' => $model,
					'rik' => $rik,
					'color' => $color,
					'registration_number' => $registration_number,
					'owner' => $owner,
					'owner_phone' => $owner_phone,
					'owner_address' => $owner_address,
					'user' =>$name,
				);
				$vehicle = new Application_Model_Vehicle();
				$vehicle->insert($data);
				$this->_redirect('/');
				return;
			}
			else {
				echo "all fields must be input";
			}
		}

		$this->view->vehicle = new stdClass();
		$this->view->vehicle->id = null;
		$this->view->vehicle->marka = '';
		$this->view->vehicle->model = '';
		$this->view->vehicle->rik = '';
		$this->view->vehicle->color = '';
		$this->view->vehicle->registration_number = '';
		$this->view->vehicle->owner = '';
		$this->view->vehicle->owner_phone = '';
		$this->view->vehicle->owner_address = '';

		// additional view fields required by form
		$this->view->action = 'add';
		$this->view->buttonText = 'Add';
    }

    function editAction(){
		$this->view->title = "Edit vehicle";
		$vehicle = new Application_Model_Vehicle();

		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$id = (int)$this->_request->getPost('id');
			$marka = trim($filter->filter($this->_request->getPost('marka')));
			$model = trim($filter->filter($this->_request->getPost('model')));
			$rik = trim($filter->filter($this->_request->getPost('rik')));
			$color = trim($filter->filter($this->_request->getPost('color')));
			$registration_number = trim($filter->filter($this->_request->getPost('registration_number')));
			$owner = trim($filter->filter($this->_request->getPost('owner')));
			$owner_phone = trim($filter->filter($this->_request->getPost('owner_phone')));
			$owner_address = trim($filter->filter($this->_request->getPost('owner_address')));

			if ($id !== false) {
				if ($marka != '' && $model != '' && $rik != '' && $color != '' && $registration_number != '' && $owner != '' && $owner_phone != '' && $owner_address != '') {

					$data = array(
					'marka' => $marka,
					'model' => $model,
					'rik' => $rik,
					'color' => $color,
					'registration_number' => $registration_number,
					'owner' => $owner,
					'owner_phone' => $owner_phone,
					'owner_address' => $owner_address,
					);

					$where = 'id = ' . $id;
					$vehicle->update($data, $where);
					$this->_redirect('/');
					return;
				} else {
					$this->view->vehicle = $vehicle->fetchRow('id='.$id);
				}
			}
		} else {
			// album id should be $params['id']
			$id = (int)$this->_request->getParam('id', 0);

			if ($id > 0) {
				$this->view->vehicle = $vehicle->fetchRow('id='.$id);
			}
		}

		// additional view fields required by form
		$this->view->action = 'edit';
		$this->view->buttonText = 'Update';
	}

    function deleteAction(){
		$this->view->title = "Delete vehicle";
		$vehicle = new Application_Model_Vehicle();
		
		if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_Alpha');
			$filter = new Zend_Filter_Alpha();
			$id = (int)$this->_request->getPost('id');
			$del = $filter->filter($this->_request->getPost('del'));

			if ($del == 'Yes' && $id > 0) {
				$where = 'id = ' . $id;
				$rows_affected = $vehicle->delete($where);
			}
		} else {
			$id = (int)$this->_request->getParam('id');

			if ($id > 0) {
				// only render if we have an id and can find the vehicle.
				$this->view->vehicle = $vehicle->fetchRow('id='.$id);

				if ($this->view->vehicle->id > 0) {
					// render template automatically
					return;
				}
			}
		}

		// redirect back to the vehicle list unless we have rendered the view
		$this->_redirect('/');
	}
	
	function infoAction() {
		$this->view->title = "Vehicle info";
		$id='';
		$id = (int)$this->_request->getParam('id', 0);
		$vehicle = new Application_Model_Vehicle();				
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?', $id));
		$this->view->vehicleArray = $vehicleRow->toArray();
		$this->view->id = $id;
		$page_id="vehicle_".$id;
		$where="page_id="."'".$page_id."'";
		$auth = Zend_Auth::getInstance();
		$user = $auth->getIdentity();
		$this->view->name = $user->name;
		$comment = new Application_Model_Comment();
		$commentRow = $comment->fetchAll($where);
		//$commentRow = $comment->fetchAll($comment->select()->order('comment DESC'));
		$commentArray = $commentRow->toArray();				
		$this->view->countComment=count($commentArray);	
		$this->view->commentArray=$commentArray;		
	}
		
}