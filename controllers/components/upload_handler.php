<?php

class UploadHandlerComponent extends Object {

	/* component configuration */
	var $name = 'UploadHandlerComponent';
	var $params = array();
	var $uploadpath;
	var $overwrite = false;
	var $filename;

	function startup(&$controller) {
		$this->params = $controller->params;
	}

	function upload() {
		$ok = false;
		$this->uploadpath = WWW_ROOT.$this->params['form']['webroot_path'];
		$this->filename = $this->params['form']['Filedata']['name'];
		$uploadStatus = $this->write();

		if (!$uploadStatus) {
			header("HTTP/1.0 500 Internal Server Error");	//this should tell SWFUpload what's up
		}

		$this->setParams();
		return $uploadStatus;
	}

	function setParams() {
		$this->params['form']['Filedata']['name'] = $this->filename;
		$this->params['form']['Filedata']['path'] = str_replace(WWW_ROOT, '', $this->uploadpath);
		$extension = array_pop(explode('.', $this->filename));

		$this->params['form']['Filedata']['ext'] = $extension;

		$filename = substr($this->filename, 0, (0 - (strlen($extension) + 1)));

		list($width, $height) = getimagesize($this->uploadpath.$this->filename);

		$this->params['form']['Filedata']['width'] = $width;
		$this->params['form']['Filedata']['height'] = $height;
		$this->params['form']['Filedata']['slug'] = strtolower(Inflector::slug($filename, '-'));
		$this->params['form']['Filedata']['md5'] = md5_file($this->uploadpath.$this->filename);
		$this->params['form']['Filedata']['full_path'] = $this->uploadpath.$this->filename;

	}

	function removeDuplicate($file) {
		return unlink($file);
	}

	function findUniqueFilename($existing_files = null) {
		// append a digit to the end of the name
		$filenumber = 0;
		$filesuffix = '';
		$fileparts = explode('.', $this->filename);
		$fileext = '.' . array_pop($fileparts);
		$filebase = implode('.', $fileparts);

		if (is_array($existing_files)) {
			do {
				$newfile = $filebase . $filesuffix . $fileext;
				$filenumber++;
				$filesuffix = '(' . $filenumber . ')';
			} while (in_array($newfile, $existing_files));
		}

		return $newfile;
	}

	function write() {
		if (!class_exists('Folder')) {
			uses('folder');
		}

		$moved = false;
		$folder = new Folder($this->uploadpath, true, 0755);

		if ($folder) {
			if (!$this->overwrite) {
				$contents = $folder->read();
				$this->filename = $this->findUniqueFilename($contents[1]);  //pass the file list as an array
			}

			$moved = move_uploaded_file($this->params['form']['Filedata']['tmp_name'], $this->uploadpath.$this->filename);
		}

		return $moved;
	}

}
?>