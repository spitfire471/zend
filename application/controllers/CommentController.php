<?php

class CommentController extends Zend_Controller_Action {
		
    function indexAction() {
        if ($this->_request->isPost()) {
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$name = trim($filter->filter($this->_request->getPost('name')));
			$comment = trim($filter->filter($this->_request->getPost('comment')));
			$page_id = trim($filter->filter($this->_request->getPost('page_id')));
			
			
			if ($name != '' && $comment != '' && $page_id != '' ) {
				$data = array(
					'user' => $name,
					'comment' => $comment,
					'page_id' => $page_id,
					
				);
				$comment = new Application_Model_Comment();
				$comment->insert($data);
				$this->_redirect('/');
				return;
			}
			else {
				echo "all fields must be input";
			}

		}
	}	
}