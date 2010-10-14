<?php

class VideosController extends CakeUploadifyAppController {

	public $components = array('Auth', 'CakeUploadify.UploadHandler', 'RequestHandler');
	public $uses = array('Video', 'User');

	function beforeFilter() {
		if ($this->action == 'admin_upload') {
			$this->__configureAdmin();
			$this->__handleGeneric();
			if (!empty($this->params['pass'][0])) {
				$this->Session->id($this->params['pass'][0]);
				$this->Session->start();
			}
			$this->user = $this->User->read(null, $this->Auth->user('id'));
		}

		$this->dir = WWW_ROOT.'files'.DS.'videos'.DS;
		$this->cache_dir = WWW_ROOT.'generated'.DS.'videos'.DS;
		parent::beforeFilter();
	}

	public function admin_upload() {
		if (isset($this->params['form']['Filedata'])) {

			if ($this->UploadHandler->upload()) {
				$this->data['Video'] = $this->UploadHandler->params['form']['Filedata'];
				$this->data['metadata'] = $this->params['form'];

				//$videoExists = $this->Video->find('first', array('conditions' => array('Video.md5' => $this->data['Video']['md5']), 'recursive' => -1));

				if (empty($videoExists)) {
					$this->Video->save($this->data);
					$this->data['Video']['id'] = $this->Video->id;
					$this->uploadVideo($this->data);
				} else {
					$this->UploadHandler->removeDuplicate(WWW_ROOT.$this->data['Video']['path'].$this->data['Video']['name']);
					$this->data = $videoExists;
				}

				$file_info = $this->data;
				$this->set(compact('file_info'));
			}
		}
	}

	private function uploadVideo($fileInfo) {

		$this->log($fileInfo);

		App::import('Vendor', 'zend_include_path');
		App::import('Vendor', 'Zend/Gdata');
		App::import('Vendor', 'Zend/Gdata/Youtube');

		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Uri_Http');

		$httpClient = Zend_Gdata_AuthSub::getHttpClient($this->user['User']['youtube_session_token']);
		$yt = new Zend_Gdata_YouTube($httpClient, Configure::read('Youtube.application_title'), $user['User']['username'], Configure::read('Youtube.developer_key'));
		$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

		$filesource = $yt->newMediaFileSource($fileInfo['Video']['full_path']);
		$filesource->setContentType('video/quicktime');
		$filesource->setSlug('mytestmovie.mov');

		$myVideoEntry->setMediaSource($filesource);
		$myVideoEntry->setVideoTitle($fileInfo['metadata']['Title']);
		$myVideoEntry->setVideoDescription($fileInfo['metadata']['Description']);
		$myVideoEntry->setVideoCategory($fileInfo['metadata']['Category']);
		$myVideoEntry->setVideoPrivate();

		$myVideoEntry->setVideoTags('cars, funny');

		// optionally set some developer tags (see Searching by Developer Tags for more details)
		$myVideoEntry->setVideoDeveloperTags(array('mydevelopertag', 'anotherdevelopertag'));

		// optionally set the video's location
		$yt->registerPackage('Zend_Gdata_Geo');
		$yt->registerPackage('Zend_Gdata_Geo_Extension');

		// upload URI for the currently authenticated user
		$uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

		// try to upload the video, catching a Zend_Gdata_App_HttpException if available
		// or just a regular Zend_Gdata_App_Exception
		try {
			$newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
			$this->log($newEntry);
		} catch (Zend_Gdata_App_HttpException $httpException) {
			$this->log($httpException->getRawResponseBody());
		} catch (Zend_Gdata_App_Exception $e) {
			$this->log($e->getMessage());
		}
	}

}

?>

