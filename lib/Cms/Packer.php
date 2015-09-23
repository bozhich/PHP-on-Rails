<?php

class Cms_Packer {
	/**
	 * @param       $hash_id
	 * @param array $files
	 * @return null|string
	 * @throws Exception
	 */
	public static function js($hash_id, array $files) {
		if (cfg()->dev_mode) {
			$scripts = null;
			foreach ($files as $file) {
				if (!file_exists(cfg()->static_path . 'js' . DS . $file)) {
					throw new Exception('file ' . cfg()->static_path . $file . ' does not exist');
				}
				$source = Core_Files::getContent(cfg()->static_path . 'js' . DS . $file);
				$source = self::replaceLangTags($source);

				$new_file = 'c_' . str_replace(array('/', '\\'), '_', $file);
				$new_file_path = cfg()->cache_path . 'js' . DS . $new_file;
				Core_Files::putContent($new_file_path, $source);

				$public_path = cfg()->cache_address . 'js/' . $new_file;

				$scripts .= "\n" . '<script type="text/javascript" src="' . $public_path . '"></script>';
			}

			return $scripts;
		} else {
			$f_name = md5($hash_id . var_export($files, true)) . '.js';
			$f_path = cfg()->cache_path . 'js' . DS . $f_name;
			$f_path2 = cfg()->cache_path . 'js' . DS . 'last.js';
			$f_public = cfg()->cache_address . 'js/' . $f_name;

			if (!is_file($f_path)) {
				$source = null;
				foreach ($files as $file) {
					$source .= Core_Files::getContent(cfg()->static_path . 'js' . DS . $file);
				}
				$source = self::replaceLangTags($source);

				//require dirname(__FILE__) . DS . 'class.JavaScriptPacker.php';
//				$packer = new Cms_JsPacker($source, 'None', true, false);
//				$packed = $packer->pack();

				//			$source = preg_replace('[\t|\n\n]', '', $source);
				//			$source = preg_replace('[  ]', '', $source);
				$packed = $source;

				Core_Files::putContent($f_path, $packed);
				Core_Files::putContent($f_path2, $packed);
			}

			return '<script type="text/javascript" src="' . $f_public . '"></script>';
		}
	}


	/**
	 * @param       $hash_id
	 * @param array $files
	 * @return null|string
	 */
	public static function css($hash_id, array $files) {
		if (cfg()->dev_mode) {
			$scripts = null;
			foreach ($files as $file) {
				$source = Core_Files::getContent(cfg()->static_path . 'css' . DS . $file);
				$source = self::replaceLangTags($source);

				$new_file = 'c_' . str_replace(array('/', '\\'), '_', $file);
				$new_file_path = cfg()->cache_path . 'css' . DS . $new_file;
				Core_Files::putContent($new_file_path, $source);

				$public_path = cfg()->cache_address . 'css' . DS . $new_file;

				$scripts .= "\n" . '<link media="screen" rel="stylesheet" type="text/css" href="' . $public_path . '" />';
			}

			return $scripts;
		} else {
			$f_name = md5($hash_id . var_export($files, true)) . '.css';
			$f_path = cfg()->cache_path . 'css' . DS . $f_name;
			$f_path2 = cfg()->cache_path . 'css' . DS . 'last.css';
			$f_public = cfg()->cache_address . 'css' . DS . $f_name;

			if (!is_file($f_path)) {
				$source = null;
				foreach ($files as $file) {
					$source .= Core_Files::getContent(cfg()->static_path . 'css' . DS . $file);
				}
				$source = self::replaceLangTags($source);

				//require dirname(__FILE__) . DS . 'class.JavaScriptPacker.php';
//				$packer = new Cms_JsPacker($source);
//				$packed = $packer->pack();

				$source = preg_replace('[\r\n|\n]', '', $source);
				$source = preg_replace('[  ]', '', $source);
				$packed = $source;

				Core_Files::putContent($f_path, $packed);
				Core_Files::putContent($f_path2, $packed);
			}

			return '<link media="screen" rel="stylesheet" type="text/css" href="' . $f_public . '" />';

		}
	}


	/**
	 * @param $source
	 * @return mixed
	 */
	public static function replaceLangTags($source) {
		return preg_replace_callback('/__\([\'|"](.*?)[\'|"][\)|,]/', array(__CLASS__, 'replaceLangTagsCallBack'), $source);
	}


	/**
	 * @param $data
	 * @return mixed
	 */
	public static function replaceLangTagsCallBack($data) {
		return str_replace($data[1], __($data[1]), $data[0]);
	}
}
