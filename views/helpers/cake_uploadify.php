<?php

class CakeUploadifyHelper extends AppHelper {

	public function create($settings) {
		$settings['model'] = explode('.', $settings['model']);
  	$View =& ClassRegistry::getObject('view');
		$View->set(compact('settings'));
		echo $View->element('upload', array('plugin' => 'CakeUploadify'));
	}

}