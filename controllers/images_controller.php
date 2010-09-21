<?php

class ImagesController extends CakeUploadifyAppController {
	public $dir;
	public $cache_dir;
	public $components = array('CakeUploadify.UploadHandler', 'RequestHandler');

	function beforeFilter() {
		if ($this->action == 'admin_upload') {
			if (!empty($this->params['pass'][0])) {
				$this->Session->id($this->params['pass'][0]);
				$this->Session->start();
			}
		}

		$this->dir = WWW_ROOT.'files'.DS.'images'.DS;
		$this->cache_dir = WWW_ROOT.'generated'.DS.'images'.DS;
		$this->presets = array(
			'preset1' => array(
				'w' => 400,
				'h' => 400,
				'format' => 'png',
				'fltr' => 'stc|FFFFFF|0|100',
				'nocache' => 1
			)
		);

		$this->colours = array(
			'orange' => 'E99C00',
			'blue' => '0092A7',
			'purple' => '7C1272',
			'green' => '84B819',
			'pink' => 'E74687',
			'red' => 'DC002E',
			'brown' => '5C4D43',
			'yellow' => 'FEE300',
		);

		parent::beforeFilter();
	}

	public function admin_upload() {
		if (isset($this->params['form']['Filedata'])) {

			if ($this->UploadHandler->upload()) {
				$this->data['Image'] = $this->UploadHandler->params['form']['Filedata'];
				$imageExists = $this->Image->find('first', array('conditions' => array('Image.md5' => $this->data['Image']['md5']), 'recursive' => -1));

				if (empty($imageExists)) {
					$this->Image->save($this->data);
					$this->data['Image']['id'] = $this->Image->id;
				} else {
					$this->UploadHandler->removeDuplicate(WWW_ROOT.$this->data['Image']['path'].$this->data['Image']['name']);
					$this->data = $imageExists;
				}

				$this->data['uuid'] = $this->params['form']['uuid'];

				$file_info = $this->data;
				$this->set(compact('file_info'));
			}
		}
	}

	function view()	{

		set_time_limit(120);
		ini_set('memory_limit', '64M');
		Configure::write('debug', 0);

		$this->autoLayout = false;

		$options = $this->_get_specs(end($this->params['pass']));

		if (isset($this->presets[$options['_']])){
			$options = am($this->presets[$options['_']],$options);
		}

		$image = $this->Image->findBySlug($options['slug']);
		$image = $image['Image'];

		if ((!$image || !file_exists($image['path'].$image['name'])) && empty($options['pdf'])) {
			header("HTTP/1.0 404 Not Found");
			Configure::write('debug', 0);
			exit;
		}


		$source_file = $image['path'].$image['name'];

		$dest_file = WWW_ROOT.str_replace('/', DS, substr($this->here,1));


		// Load phpThumb
		App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/phpthumb.class.php'));


		// check to see if we should resize or just copy source file over
		if ($this->_dontBotherResizing($image, $options)) {
			copy($source_file,$dest_file);
			header('Content-Disposition: inline; filename="'.substr(strrchr($this->here,"/"),1).'"');
			header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype($options['format']));
			include($dest_file);
			exit;
		}

		$phpThumb = new phpThumb();

		$phpThumb->config_allow_src_above_docroot = true;

		// we read in any phpThumb configuration options that might be set
		$phpThumbConf = Configure::read('phpThumb');
		if (is_array($phpThumbConf)) {
			foreach ($phpThumbConf as $key => $value) {
				$phpThumb->setParameter('config_'.$key, $value);
			}
		}

		// eliminate white and replace with background colour if we got it
		if (!empty($options['ew'])) {
			$replaceBgCol = ($options['ew'] === true) ? $image['bgcolor'] : $options['ew'];
			if (strlen($replaceBgCol) == 0) {
				$replaceBgCol = 'yellow';
			}
			if (!empty($this->colours[$replaceBgCol])) {
				$replaceBgCol = $this->colours[$replaceBgCol];
			}
			// make sure we have hex value here
			if (preg_match('/^[0-9A-F]{6}$/i', $replaceBgCol)) {
				$cmd = $phpThumb->ImageMagickCommandlineBase();
				$tmpdest = TMP.String::UUID().'.jpg';

				$cmd .= ' '.escapeshellarg($source_file).' -negate -background "#000000" -alpha Shape -background "#'.$replaceBgCol.'" -flatten -quality 100 '.escapeshellarg($tmpdest);
				$cmdoutput = phpthumb_functions::SafeExec($cmd);
				$source_file = $tmpdest;
				$delete_after_resizing = $tmpdest;
			}
		}

		$phpThumb->setSourceFilename($source_file);

		$phpThumb->setParameter('q', 95); // default 95% quality

		if (!empty($image['bgcolor'])){
			$phpThumb->setParameter('bg', $image['bgcolor']);
		}

		$phpThumb->setParameter('f', $options['format']);

		if (!empty($options['kb'])){
			$phpThumb->setParameter('maxb', $options['kb']*1024);
		}

		if (!empty($options['w'])){
			$phpThumb->setParameter('w', $options['w']);
		}

		if (!empty($options['h'])){
			$phpThumb->setParameter('h', $options['h']);
		}

		if (!empty($options['q'])){
			$phpThumb->setParameter('q', $options['q']);
		}

		if (!empty($options['bg'])){
			$phpThumb->setParameter('bg', $options['bg']);
		}

		$phpThumb->setParameter('aoe', false);

		/*
		execution order = crop, rotate, zoom
		*/

		if (!empty($image['crop']) && !isset($options['nocrop'])) {
			list($sx, $sy, $sw, $sh) = explode(',', $image['crop']);
			$phpThumb->setParameter('sx', $sx);
			$phpThumb->setParameter('sy', $sy);
			$phpThumb->setParameter('sw', $sw);
			$phpThumb->setParameter('sh', $sh);
		}

		// auto-rotate based on exif data (i don't think we want to do this)
		//$phpThumb->setParameter('ar', true);

		if (!empty($image['rotate'])) {
			// ra will always overwrite ar
			$phpThumb->setParameter('ra', intval($image['rotate']));
		}

		if (!empty($options['fltr'])){
			$phpThumb->setParameter('fltr', $options['fltr']);
		}

		if (!empty($options['zoom'])){
			$nocrop = ($image['zoom'] === '0');
		} else {
			$nocrop = false;
		}
		if (isset($options['nocrop'])) $nocrop = true;

		if (!isset($options['fit']) || !(isset($options['w']) && isset($options['h']))){
			if ($nocrop || empty($options['w']) || empty($options['h'])){
				$phpThumb->setParameter('far',1);
			} else {
				if (!empty($image['zoom'])){
					$phpThumb->setParameter('zc', $image['zoom']);
				} else {
					$phpThumb->setParameter('zc', 'C');
				}
			}
		}


		if($phpThumb->generateThumbnail()){

			//$this->log($phpThumb->debugmessages);

			if (!isset($options['nocache'])){
				$phpThumb->RenderToFile($dest_file);
			}


			$phpThumb->OutputThumbnail();

		} else {

			// we should really try and handle this a bit better
			// maybe create an image with the dimensions above
			// with error message?

			echo $phpThumb->fatalerror;

		}

		if (!empty($delete_after_resizing) && file_exists($delete_after_resizing)) {
			unlink($delete_after_resizing);
		}
		exit;
	}

	function _get_specs($filename) {
		preg_match('/^([^_\.]+)_?((.+)?)\.(jpg|png|gif|jpeg)$/i',$filename,$matches);
		list(,$slug,$raw,$options,$format) = $matches;
		$return = array( '_' => $raw, 'slug' => $slug, 'format' => str_replace('jpeg','jpg',strtolower($format)) );
		$options = explode('_',strtolower($options));
		foreach ($options as $option){
			if (preg_match('/^(w|h|q|kb)(\d+)$/',$option,$matches)){ // match numbers
				$return[$matches[1]] = $matches[2];
			} elseif ($option == 'nocache'){
				$return['nocache'] = true;
			} elseif ($option == 'fit'){
				$return['fit'] = true;
			} elseif ($option == 'pdf'){
				$return['pdf'] = true;
			} elseif ($option == 'nocrop'){
				$return['nocrop'] = true;
			} elseif ($option == 'ew'){
				$return['ew'] = true;
			} elseif (preg_match('/^(bg|ew)\#?([0-9A-F]{6})$/i',$option,$matches)){ // match hex colours
				$return[$matches[1]] = $matches[2];
			}
		}
		return $return;
	}

/**
 * Some checks to see if we need to resize the image or just use the source file.
 * Eventually I would like to add in code to generate a thumbnail and compare filesizes too to get the best result.
 */
	function _dontBotherResizing($image, $options) {

		$temp = $options;

		unset($temp['slug']);

		if (!empty($options['_']) && !array_key_exists($options['_'], $this->presets)) {
			unset($temp['_']);
		}

		if ($options['format'] == $image['ext']) {
			unset($temp['format']);
		}

		if (isset($options['w']) && $options['w'] == $image['width']) {
			unset($temp['w']);
		}

		if (isset($options['h']) && $options['h'] == $image['height']) {
			unset($temp['h']);
		}

		return !$temp;

	}

}

?>