<?php

/**
 * Class Core_Tools
 */
class Core_Tools {
	/**
	 * array -> object (recursive)
	 * same as
	 * $obj = json_decode( json_encode($array) );
	 * @param array $array
	 */
	public static function toObject(array $array) {
		$obj = new stdClass;
		foreach ($array as $k => $v) {
			if (strlen($k)) {
				if (is_array($v)) {
					$obj->{$k} = self::toObject($v);
				} else {
					$obj->{$k} = $v;
				}
			}
		}

		return $obj;
	}


	/**
	 * @param $object
	 * @return array
	 */
	public static function toArray($object) {
		$arr = array();
		foreach ($object as $k => $v) {
			if (strlen($k)) {
				if (is_object($v)) {
					$arr[$k] = self::toArray($v);
				} else {
					$arr[$k] = $v;
				}
			}
		}

		return $arr;
	}

	// Json method prepared cuz i might change them in future
	/**
	 * @param $json
	 * @return mixed
	 */
	public static function JsonDecode($json) {
		return json_decode($json, true);
	}

	/**
	 * @param $json
	 * @return string
	 */
	public static function JsonEncode($json) {
		return json_encode($json);
	}

	/**
	 * @param $timestamp
	 * @return bool|int|string
	 */
	public static function TimestampToDate($timestamp) {
		if ($timestamp > 0) {
			return date(cfg()->date_format, $timestamp);
		}

		return 0;
	}

	/* Core_Curl used */

	/**
	 * @param $array
	 * @return bool
	 */
	public static function is_array_multidim($array) {
		if (!is_array($array)) {
			return false;
		}

		return !(count($array) === count($array, COUNT_RECURSIVE));
	}

	/**
	 * @param      $data
	 * @param null $key
	 * @return string
	 */
	public static function http_build_multi_query($data, $key = null) {
		$query = array();

		if (empty($data)) {
			return $key . '=';
		}

		$is_array_assoc = self::is_array_assoc($data);

		foreach ($data as $k => $value) {
			if (is_string($value) || is_numeric($value)) {
				$brackets = $is_array_assoc ? '[' . $k . ']' : '[]';
				$query[] = urlencode(is_null($key) ? $k : $key . $brackets) . '=' . rawurlencode($value);
			} else if (is_array($value)) {
				$nested = is_null($key) ? $k : $key . '[' . $k . ']';
				$query[] = self::http_build_multi_query($value, $nested);
			}
		}

		return implode('&', $query);
	}

	/**
	 * @param $array
	 * @return bool
	 */
	public static function is_array_assoc($array) {
		return (bool) count(array_filter(array_keys($array), 'is_string'));
	}

	/**
	 * @param $bytes
	 * @return float
	 */
	public static function formatSize($bytes) {
		$types = array('B', 'KB', 'MB', 'GB', 'TB');
		for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++) ;

//		return (round($bytes, 2) . " " . $types[$i]);
		return (round($bytes, 2));
	}

	/**
	 * @param     $from
	 * @param     $total
	 * @param int $precision
	 * @return float|int
	 */
	public static function calculatePercent($from, $total, $precision = 2) {
		if ($from == 0 || $total == 0) {
			return 0;
		}
		return round(($from / $total) * 100, $precision);
	}
}


?>