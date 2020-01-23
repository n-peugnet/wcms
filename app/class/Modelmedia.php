<?php

namespace Wcms;

class Modelmedia extends Model
{

	const MEDIA_SORTBY = ['id', 'size', 'type'];



	/**
	 * Get the Media Object
	 * 
	 * @param string $entry Id of the file
	 * @param string $dir Directory of media file
	 * 
	 * @return Media|bool
	 */
	public function getmedia(string $entry, string $dir)
	{
		$fileinfo = pathinfo($entry);

		if (isset($fileinfo['extension'])) {
			$datas = array(
				'id' => str_replace('.' . $fileinfo['extension'], '', $fileinfo['filename']),
				'path' => $dir,
				'extension' => $fileinfo['extension']
			);
			return new Media($datas);
		} else {
			return false;
		}
	}

	public function medialistopt(Medialist $mediaopt)
	{
		$medialist = $this->getlistermedia($mediaopt->dir(), $mediaopt->type());
		$this->medialistsort($medialist, $mediaopt->sortby(), $mediaopt->order());

		return $medialist;
	}

	/**
	 * Display a list of media
	 * 
	 * @param string $path
	 * @param array $type
	 * 
	 * @return array of Media objects
	 */
	public function getlistermedia($dir, $type = [])
	{
		if (is_dir($dir)) {
			if ($handle = opendir($dir)) {
				$list = [];
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {

						$media = $this->getmedia($entry, $dir);

						if ($media != false) {

							$media->analyse();

							if (empty($type) || in_array($media->type(), $type)) {
								$list[] = $media;
							}
						}
					}
				}
				return $list;
			}
		} else {
			return false;
		}
	}


	/**
	 * Sort an array of media
	 * 
	 * @param array $medialist
	 * @param string $sortby
	 * @param int order Can be 1 or -1
	 */
	public function medialistsort(array &$medialist, string $sortby = 'id', int $order = 1): bool
	{
		$sortby = (in_array($sortby, self::MEDIA_SORTBY)) ? $sortby : 'id';
		$order = ($order === 1 || $order === -1) ? $order : 1;
		return usort($medialist, $this->buildsorter($sortby, $order));
	}
	
	public function buildsorter($sortby, $order)
	{
		return function ($media1, $media2) use ($sortby, $order) {
			$result = $this->mediacompare($media1, $media2, $sortby, $order);
			return $result;
		};
	}

	public function mediacompare($media1, $media2, $method = 'id', $order = 1)
	{
		$result = ($media1->$method() <=> $media2->$method());
		return $result * $order;
	}





	public function listfavicon()
	{
		$glob = Model::FAVICON_DIR . '*.png';
		$faviconlist = glob($glob);
		$faviconlist = array_map(function ($input){
			return basename($input);
		}, $faviconlist);
		return $faviconlist;
	}


	public function listinterfacecss()
	{
		$glob = Model::CSS_DIR . '*.css';
		$listinterfacecss = glob($glob);
		$listinterfacecss = array_map(function ($input) {
			return basename($input);
		}, $listinterfacecss);
		$listinterfacecss = array_diff($listinterfacecss, ['edit.css', 'home.css']);
		return $listinterfacecss;
	}


	/**
	 * Generate an reccursive array where each folder is a array and containing a filecount in each folder
	 */
	public function listdir(string $dir) : array
	{
		$result = array();

		$cdir = scandir($dir);
		$result['dirfilecount'] = 0;
		foreach ($cdir as $key => $value) {
			if (!in_array($value, array(".", ".."))) {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = $this->listdir($dir . DIRECTORY_SEPARATOR . $value);
				} else {
					$result['dirfilecount']++;
				}
			}
		}

		return $result;
	}

	/**
	 * Analyse reccursive array of content to generate list of path
	 * 
	 * @param array $dirlist Array generated by the listdir function
	 * @param string $parent used to create the strings
	 * @param array $pathlist used by reference, must be an empty array
	 * 
	 * @return array list of path as string
	 */
	public function listpath(array $dirlist, string $parent = '', array &$pathlist = [])
	{
		foreach ($dirlist as $dir => $content) {
			if(is_array($content)) {
				$pathlist[] = $parent . $dir . DIRECTORY_SEPARATOR;
				$this->listpath($content, $parent . $dir . DIRECTORY_SEPARATOR, $pathlist);
			}
		}
	}

	/**
	 * Upload single file
	 * 
	 * @param string $index The file id
	 * @param string $destination File final destination
	 * @param bool|int $maxsize Max file size in octets
	 * @param bool|array $extensions List of authorized extensions
	 * @param bool $jpgrename Change the file exentension to .jpg
	 * 
	 * @return bool If upload process is a succes or not
	 */
	function simpleupload(string $index, string $destination, $maxsize = false, $extensions = false, bool $jpgrename = false): bool
	{
		//Test1: if the file is corectly uploaded
		if (!isset($_FILES[$index]) || $_FILES[$index]['error'] > 0) return false;
		//Test2: check file size
		if ($maxsize !== false && $_FILES[$index]['size'] > $maxsize) return false;
		//Test3: check extension
		$ext = substr(strrchr($_FILES[$index]['name'], '.'), 1);
		if ($extensions !== false && !in_array($ext, $extensions)) return false;
		if ($jpgrename !== false) {
			$destination .= '.jpg';
		} else {
			$destination .= '.' . $ext;
		}
		//Move to dir
		return move_uploaded_file($_FILES[$index]['tmp_name'], $destination);
	}

	/**
	 * Upload multiple files
	 * 
	 * @param string $index Id of the file input
	 * @param string $target direction to save the files
	 */
	public function multiupload(string $index, string $target)
	{
		if ($target[strlen($target) - 1] != DIRECTORY_SEPARATOR)
			$target .= DIRECTORY_SEPARATOR;
		$count = 0;
		foreach ($_FILES[$index]['name'] as $filename) {
			$fileinfo = pathinfo($filename);
			$extension = idclean($fileinfo['extension']);
			$id = idclean($fileinfo['filename']);

			$tmp = $_FILES['file']['tmp_name'][$count];
			$count = $count + 1;
			$temp = $target . $id . '.' . $extension;
			move_uploaded_file($tmp, $temp);
			$temp = '';
			$tmp = '';
		}
	}

	public function adddir($dir, $name)
	{
		$newdir = $dir . DIRECTORY_SEPARATOR . $name;
		if (!is_dir($newdir)) {
			return mkdir($newdir);
		} else {
			return false;
		}
	}

	/**
	 * Completely delete dir and it's content
	 * 
	 * @param string $dir Directory to destroy
	 * 
	 * @return bool depending on operation success
	 */
	public function deletedir(string $dir) : bool
	{
		if(substr($dir, -1) !== '/') {
			$dir .= '/';
		}
		if(is_dir($dir)) {
			return $this->deltree($dir);
		} else {
			return false;
		}
	}

	/**
	 * Function do reccursively delete a directory
	 */
	public function deltree(string $dir)
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->deltree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	/**
	 * Delete a file
	 */
	public function deletefile(string $filedir)
	{
		if(is_file($filedir)) {
			return unlink($filedir);
		} else {
			return false;
		}
	}

	public function multifiledelete(array $filelist)
	{
		foreach ($filelist as $filedir ) {
			if(is_string($filedir)) {
				$this->deletefile($filedir);
			}
		}
	}

	public function movefile(string $filedir, string $dir)
	{
		if(substr($dir, -1) !== '/') {
			$dir .= '/';
		}
		if(is_file($filedir)) {
			$newdir = $dir . basename($filedir);
			return rename($filedir, $newdir);
		} else {
			return false;
		}
	}

	public function multimovefile(array $filedirlist, string $dir)
	{
		$success = [];
		foreach ($filedirlist as $filedir ) {
			if(is_string($filedir)) {
				$success[] = $this->movefile($filedir, $dir);
			}
		}
		if(in_array(false, $success)) {
			return false;
		} else {
			return true;
		}
	}



}
