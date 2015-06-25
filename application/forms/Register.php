<?

class Application_Form_Register extends Zend_Form {
	
	public function init() {
		
		$this->addElement('text', 'name', array(
			'placeholder' => 'select nickname',
			'filters'    => array('StringTrim', 'StringToLower'),
			'validators' => array(array('StringLength', false, array(3, 45))),
			'required'   => true ));
			
		$this->addElement('text', 'email', array(
			'placeholder' => 'email',
			'filters'    => array('StringTrim', 'StringToLower'),
			'validators' => array(array('StringLength', true, array(0, 96)), array('EmailAddress', true)),
			'required'   => true ));

		$this->addElement('password', 'pass', array(
			'placeholder' => 'password ( min 8 symbols)',
			'filters'    => array('StringTrim'),
			'validators' => array(array('StringLength', false, array(8, 256))),
			'required'   => true ));

		$this->addElement('captcha','captchaImage', array (
		'captcha' => array('captcha' => 'Image', 'worldLen' => 6, 'timeout' => 300,
		'imgDir' => '/home/devzone/spitfire/zend1/public/captcha/', 'imgUrl' => '/captcha/',
			'width' => 250, 'height' => 150,
			'font' => '/home/devzone/spitfire/zend1/public/tahoma.ttf', 'fontSize' => 34,
		))		);

		
		
		$this->addElement('submit', 'Create', array(
			'class'				=> 'btn',
			'required'   => false,
			'ignore'     => true, ));
	}
}

