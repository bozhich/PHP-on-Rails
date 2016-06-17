<?php

/**
 * Class Cms_Packer
 */
class Cms_Packer {
	/**
	 *
	 */
	const MINIFY_CMD = 'java -jar %1$scompiler.jar --js_output_file=%2$s --compilation_level SIMPLE --language_in ECMASCRIPT5 %3$s';

	/**
	 * @param       $hash_id
	 * @param array $files
	 * @return null|string
	 * @throws Exception
	 */
	public static function js($hash_id, array $files) {
		if (empty($files)) {
			return false;
		}
		if (cfg()->dev_mode) {
			$scripts = null;
			foreach ($files as $file) {
				if (!file_exists(cfg()->static_path . 'js' . DS . $file)) {
					throw new Exception('file ' . cfg()->static_path . 'js' . DS . $file . ' does not exist');
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
			// check should we re-generate the cache file?
			$cache_name = '';
			foreach ($files as &$file) {
				$file = cfg()->static_path . 'js' . DS . $file;
				$cache_name .= Core_Files::fileSize($file);
			}

			$f_name = md5($hash_id . $cache_name) . '.js';
			$f_path = cfg()->cache_path . 'js' . DS . $f_name;
			$f_public = cfg()->cache_address . 'js/' . $f_name;
			if (!is_file($f_path)) {
				// lets empty the directory
				$cache_files = Core_Files::listFiles(cfg()->cache_path . 'js' . DS);
				foreach ($cache_files as $cfile) {
					Core_Files::delete($cfile, true);
				}

				$cmd = sprintf(self::MINIFY_CMD, cfg()->minify_script_src, $f_path, join(' ', $files));
				shell_exec($cmd);
			}

			return '<script type="text/javascript" src="' . $f_public . '"></script>';
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
	 * @param       $hash_id
	 * @param array $files
	 * @return null|string
	 */
	public static function css($hash_id, array $files) {
		if (empty($files)) {
			return false;
		}
		$cache_name = '';
		foreach ($files as &$file) {
			$file = cfg()->static_path . 'css' . DS . $file;
			$cache_name .= Core_Files::fileSize($file);
		}
		unset($file);

		$f_name = md5($hash_id . $cache_name) . '.css';
		$f_path = cfg()->cache_path . 'css' . DS . $f_name;
		$f_public = cfg()->cache_address . 'css/' . $f_name;
		if (!is_file($f_path)) {
			// lets empty the directory
			$cache_files = Core_Files::listFiles(cfg()->cache_path . 'css' . DS);
			foreach ($cache_files as $cfile) {
				Core_Files::delete($cfile, true);
			}

			$css_content = '';
			foreach ($files as $file) {
				$content = Core_Files::getContent($file);
				$css_content .= $content;
			}

			$css_content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content);
			// Remove space after colons
			$css_content = str_replace(': ', ':', $css_content);
			// Remove whitespace
			$css_content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css_content);

			// now save the file
			Core_Files::putContent($f_path, $css_content);
		}

		return '<link type="text/css" rel="stylesheet" href="' . $f_public . '"/>';
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	public static function replaceLangTagsCallBack($data) {
		return str_replace($data[1], __($data[1]), $data[0]);
	}
}
