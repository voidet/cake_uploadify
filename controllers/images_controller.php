<?php

class ImagesController extends CakeUploadifyAppController {

	public $components = array('CakeUploadify.UploadHandler');

	function beforeFilter() {
		if ($this->action == 'admin_upload') {
			if (!empty($this->params['pass'][0])) {
				$this->Session->id($this->params['pass'][0]);
				$this->Session->start();
			}
		}
		parent::beforeFilter();
	}

	public function admin_upload() {
		$this->log($this->params);
		if (isset($this->params['form']['Filedata'])) {

			if ($this->UploadHandler->upload()) {
				$this->data['Image'] = $this->UploadHandler->params['form']['Filedata'];
				$imageExists = $this->Image->find('first', array('conditions' => array('Image.md5' => $this->data['Image']['md5'])));

				if (empty($imageExists)) {
					$this->Image->save($this->data);
				} else {
					$this->UploadHandler->removeDuplicate($this->data['Image']['full_path']);
				}

				$this->data['Image']['id'] = $this->Image->getLastInsertId();
				$file_info = $this->data;
				$this->set(compact('file_info'));
			}
		}
	}

}

?>