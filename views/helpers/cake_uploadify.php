<?php

class CakeUploadifyHelper extends AppHelper {

	public function create($settings) {
  	$View =& ClassRegistry::getObject('view');
		$settings['folder'] = WWW_ROOT.$settings['folder'];
		$View->set(compact('settings'));
		echo $View->element('upload', array('plugin' => 'CakeUploadify'));
	}

}

?>
