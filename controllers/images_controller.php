<?php

class ImagesController extends CakeUploadifyAppController {

	public $components = array('CakeUploadify.UploadHandler', 'RequestHandler');

	function beforeFilter() {
		if ($this->action == 'admin_upload') {
			if (!empty($this->params['pass'][0])) {
				$this->Session->id($this->params['pass'][0]);
				$this->Session->start();
			}
		}

		if (Configure::load('cake_uploadify') !== true) {
			die('Could not load cache my image config file');
		}

		parent::beforeFilter();
	}

	public function admin_upload() {
		if (isset($this->params['form']['Filedata'])) {

			$params = $this->params['form'];
			$presets = Configure::read('presets');
			$dest_dir = $presets[$params['dest']]['path'];
			$upload = $this->UploadHandler->upload($dest_dir);

			if ($upload !== false) {
				// $image = $this->Image->find('first', array(
				// 					'conditions' => array('md5' => $upload['Filedata']['md5']),
				// 					'fields' => array('slug', 'name'),
				// 					'recursive' => -1));

				if (empty($image)) {
					$new_image['Image'] = $upload['Filedata'];
					$this->Image->save($new_image);
					$image['metadata'] = $upload['Filedata']['slug'];
				} else {
					$this->UploadHandler->removeDuplicates($image['Image']['name']);
					$image['metadata'] = $image['Image']['slug'];
				}

				$this->set('file_info', $image);
			}
		}
	}

}