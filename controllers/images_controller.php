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
				$image = $this->Image->find('first', array(
					'conditions' => array('md5' => $upload['Filedata']['md5']),
					'fields' => array($this->Image->primaryKey, 'slug', 'name'),
					'recursive' => -1));

				if (empty($image)) {
					$new_image['Image'] = $upload['Filedata'];
					$this->Image->save($new_image);
					$meta['slug'] = $upload['Filedata']['slug'];
					$meta['id'] = $this->Image->id;
				} else {
					$this->UploadHandler->removeDuplicates($image['Image']['name']);
					$meta['slug'] = $image['Image']['slug'];
					$meta['id'] = $image['Image'][$this->Image->primaryKey];
				}

				$meta['input_name'] = '['.join('][', explode(',', $this->params['form']['model'])).']';
				$this->set('file_info', $meta);
			}
		}
	}

}