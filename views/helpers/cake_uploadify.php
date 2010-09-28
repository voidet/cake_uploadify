<?php

class CakeUploadifyHelper extends AppHelper {

	public function create($settings) {
  	$View =& ClassRegistry::getObject('view');
		$View->set(compact('settings'));
		echo $View->element('upload', array('plugin' => 'CakeUploadify'));
	}

}

?>
