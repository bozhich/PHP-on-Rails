<?php

/**
 * Class Cms_Translate
 */
class Cms_Translate {
	const PROJECT = 'Cms_Translate_project_name';

	const VERSION = 'Cms_Translate_project_version';


	/**
	 * @param      $tag
	 * @param      $parameters
	 * @param null $set_language
	 * @return array|bool|mixed|string
	 */
	public static function get($tag, $parameters, $set_language = null) {
		if (!$tag) {
			return $tag;
		}
		$translate = self::cacheGet(self::hash($tag), $set_language);

		if (!$translate) {
			$rs = Default_TranslateModel::get(array(
				'language_code' => self::getLanguageCode($set_language),
				'tag_hash'      => self::hash($tag),
			));
			if ($rs) {
				$translate = $rs->value;
			} else {
				$translate = '!' . $tag . '!';
			}
			$a = self::cacheSet(self::hash($tag), $translate, $set_language);
		}

		if (!$translate) {
			$translate = $tag;
		}

		if ($parameters) {
			if (is_array($parameters[0])) {
				return vsprintf($translate, $parameters[0]);
			}

			return vsprintf($translate, $parameters);
		} else {
			return $translate;
		}
	}


	/**
	 * @param $tag
	 * @return string
	 */
	public static function hash($tag) {
		return md5(strtolower($tag));
	}


	/**
	 * @param $tag_hash
	 * @param $set_language
	 * @return array|bool|string
	 */
	public static function cacheGet($tag_hash, $set_language = null) {
		return Core_Cache::get(self::genereteCacheId($tag_hash, $set_language));
	}


	/**
	 * @param      $tag_hash
	 * @param      $value
	 * @param null $set_language
	 */
	public static function cacheSet($tag_hash, $value, $set_language = null) {
		return Core_Cache::set(self::genereteCacheId($tag_hash, $set_language), $value, time() + mt_rand(4500, 6000));
	}


	/**
	 * @param      $tag_hash
	 * @param null $set_language
	 * @return string
	 */
	public static function genereteCacheId($tag_hash, $set_language = null) {
		return 'trns_' . self::VERSION . '_' . self::PROJECT . '_' . self::getLanguageCode($set_language) . '_' . $tag_hash;
	}


	/**
	 * @param null $set_language
	 * @return int|null|string
	 */
	public static function getLanguageCode($set_language) {
		if (isset(cfg()->language_codes[$set_language])) {
			return $set_language;
		}

		if (Core_Session::hasInstance() && s()->user->language_code !== null) {
			return s()->user->language_code;
		} else {
			foreach (cfg()->language_codes as $language_code => $language_name) {
				return $language_code;
			}
		}
	}
}
