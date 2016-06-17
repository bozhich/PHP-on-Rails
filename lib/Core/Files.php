<?php

/**
 * Class Core_Files
 */
class Core_Files {
	/**
	 * @var array
	 */
	protected static $ignore = array(
		'.',
		'..',
		'.svn',
		'.git',
	);


	/**
	 * @param $file
	 * @return bool|string
	 */
	public static function getContent($file) {
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return false;
		}
	}


	/**
	 * @param      $file
	 * @param      $data
	 * @param null $flags
	 * @return int
	 */
	public static function putContent($file, $data, $flags = null) {
		$dir = dirname($file);
		if (!is_dir($dir)) {
			$old_umask = umask(0);
			mkdir($dir, 0777, true);
			umask($old_umask);
		}

		return file_put_contents($file, $data, $flags);
	}


	/**
	 * @param      $directory
	 * @param bool $sub_folders
	 * @param int  $size
	 * @return int
	 */
	public static function dirSize($directory, $sub_folders = true, $size = 0) {
		if (is_dir($directory)) {
			$dir_handle = opendir($directory);
			while ($file = readdir($dir_handle)) {
				if (array_search($file, self::$ignore) === false) {
					if (is_dir($directory . $file) && $sub_folders) {
						$size = self::dirSize($directory . $file, $sub_folders . DS, $size);
					} elseif (is_file($directory . $file)) {
						$size += filesize($directory . $file);
					}
				}
			}
			closedir($dir_handle);
		}

		return $size;
	}

	/**
	 * @param $file
	 * @return mixed
	 */
	public static function info($file) {
		$info = pathinfo($file);
		$info['path'] = $info['dirname'] . DS . $info['basename'];
		$info['size'] = filesize($file);
		$info['normal_size'] = self::normalFileSize($file);
		$info['created_time'] = filemtime($file);

		return $info;
	}

	/**
	 * @param $file
	 * @return string
	 */
	public static function normalFileSize($file) {
		return self::normalSize(self::fileSize($file));
	}

	/**
	 * @param $size
	 * @return string
	 */
	public static function normalSize($size) {
		if ($size == false) {
			$size = 0;
		}

		if ($size >= 1099511627776) {
			return round($size / 1024 / 1024 / 1024 / 1024, 2) . ' TB';
		} elseif ($size >= 1073741824) {
			return round($size / 1024 / 1024 / 1024, 2) . ' GB';
		} elseif ($size >= 1048576) {
			return round($size / 1024 / 1024, 2) . ' MB';
		} elseif ($size >= 1024) {
			return round($size / 1024, 2) . ' KB';
		} else {
			return round($size, 2) . ' Byte';
		}
	}

	/**
	 * @param $file
	 * @return bool|int
	 */
	public static function fileSize($file) {
		return is_file($file) ? filesize($file) : false;
	}

	/**
	 * @param      $directory
	 * @param bool $sub_folders
	 * @param bool $delete_root
	 */
	public static function deleteDirectory($directory, $sub_folders = false, $delete_root = false) {
		if (is_dir($directory)) {
			$dir_handle = opendir($directory);
			while ($file = readdir($dir_handle)) {
				if (array_search($file, self::$ignore) === false) {
					if (is_dir($directory . DS . $file) && $sub_folders) {
						self::deleteDirectory($directory . DS . $file, $sub_folders . DS, true);
					} elseif (is_file($directory . DS . $file)) {
						unlink($directory . DS . $file);
					}
				}
			}
			closedir($dir_handle);

			if ($delete_root) {
				rmdir($directory);
			}
		}
	}

	/**
	 * @param $file_name
	 * @return string
	 */
	public static function getExtension($file_name) {
		return strtolower(substr(strrchr($file_name, '.'), 1));
	}

	/**
	 * @param      $file_name
	 * @param      $destination
	 * @param bool $is_from_upload
	 */
	public static function moveFile($file_name, $destination, $is_from_upload = false) {
		$file = preg_replace('[.*\\' . DS . ']', null, $destination);
		$dir = substr($destination, 0, -strlen($file));

		if (!is_dir($dir)) {
			$old_umask = umask(0);
			mkdir($dir, 0777, true);
			umask($old_umask);
		}

		if ($is_from_upload) {
			return move_uploaded_file($file_name, $destination);
		} else {
			return rename($file_name, $destination);
		}
	}

	/**
	 * @param      $file
	 * @param bool $delete_similar
	 * @return bool
	 */
	public static function delete($file, $delete_similar = false) {
		if ($delete_similar) {
			$base_file = pathinfo($file);

			$files = self::listFiles($base_file['dirname'] . DS, false);
			foreach ($files as $check_file) {
				$check_file = pathinfo($check_file);
				$compare_file = $base_file['filename'] . '_';


				if (substr($check_file['filename'], 0, strlen($compare_file)) == $compare_file) {
					self::delete($check_file['dirname'] . DS . $check_file['basename']);
				}
			}
		}

		if (is_file($file)) {
			return unlink($file);
		} else {
			return false;
		}
	}

	/**
	 * @param       $directory
	 * @param bool  $sub_folders
	 * @param array $files
	 * @return array
	 */
	public static function listFiles($directory, $sub_folders = true, array $files = array()) {
		if (is_dir($directory)) {
			$dir_handle = opendir($directory);
			while ($file = readdir($dir_handle)) {
				if (array_search($file, self::$ignore) === false) {
					if (is_dir($directory . $file) && $sub_folders) {
						$files = self::listFiles($directory . $file . DS, $sub_folders, $files);
					} elseif (is_file($directory . $file)) {
						$files[] = $directory . $file;
					}
				}
			}
			closedir($dir_handle);
		}

		return $files;
	}

	/**
	 * @param $path
	 * @param $extension
	 * @return string
	 */
	public static function uniqueFileName($path, $extension) {
		for ($i_str = 4; $i_str <= 36; $i_str++) {
			for ($i = 0; $i <= 100; $i++) {
				$name = substr(md5(time() + $i . mt_rand(0, $i)), 0, $i_str) . '.' . $extension;

				if (!is_file($path . $name)) {
					return $name;
				}
			}
		}
	}
}