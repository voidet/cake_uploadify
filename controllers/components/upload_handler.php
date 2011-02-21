<?php

class UploadHandlerComponent extends Object {

	var $name = 'UploadHandlerComponent';
	var $params = array();
	var $uploadpath;
	var $filename;
	var $overwrite = false;

	function startup(&$controller) {
		$this->params = $controller->params;
	}

	function upload($dest_dir) {
		$this->uploadpath = $dest_dir;
		$this->filename = $this->params['form']['Filedata']['name'];
		$uploadStatus = $this->write();

		if (!$uploadStatus) {
			header("HTTP/1.0 500 Internal Server Error");
		}

		$this->setParams();
		if ($uploadStatus === true) {
			return $this->params['form'];
		} else {
			return false;
		}
	}

	function setParams() {
		$this->params['form']['Filedata']['name'] = $this->filename;
		$extension = array_pop(explode('.', $this->filename));
		$this->params['form']['Filedata']['path'] = $this->uploadpath.$this->filename;
		$this->params['form']['Filedata']['ext'] = $extension;
		$this->params['form']['Filedata']['slug'] = strtolower(Inflector::slug($this->filename, '-'));
		$this->params['form']['Filedata']['md5'] = md5_file($this->uploadpath.$this->filename);
	}

	function removeDuplicates($filename) {
		if (file_exists($filename)) {
			$file = $this->uploadpath.$this->filename;
			return unlink($file);
		} else {
			rename($this->uploadpath.$this->filename, $this->uploadpath.$filename);
		}
	}

	function findUniqueFilename($existing_files = null) {
		$filenumber = 0;
		$filesuffix = '';
		$fileparts = explode('.', $this->filename);
		$fileext = '.' . array_pop($fileparts);
		$filebase = implode('.', $fileparts);

		if (is_array($existing_files)) {
			do {
				$newfile = $filebase.$filesuffix.$fileext;
				$filesuffix = '('.++$filenumber.')';
			} while (in_array($newfile, $existing_files));
		}

		return $newfile;
	}

	function write() {
		if (!class_exists('Folder')) {
			uses('folder');
		}

		$folder = new Folder($this->uploadpath, true, 0777);
		if ($folder) {
			if (!$this->overwrite) {
				$contents = $folder->read();
				$this->filename = $this->findUniqueFilename($contents[1]);
			}
			if (move_uploaded_file($this->params['form']['Filedata']['tmp_name'], $this->uploadpath.$this->filename)) {
				return true;
			}
		}

	}

}
?>