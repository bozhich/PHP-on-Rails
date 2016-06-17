<?php

/**
 * Class Core_Image_Factory
 */
class Core_Image_Factory {
	/**
	 *
	 */
	const TYPE_MAX = 1;
	/**
	 *
	 */
	const TYPE_MIN = 2;
	/**
	 *
	 */
	const TYPE_PROPORTIONAL = 3;
	/**
	 *
	 */
	const TYPE_CROP = 4;


	/**
	 * @param $source_file
	 * @param $destination_file
	 * @param $type
	 * @param $x
	 * @param $y
	 * @return string
	 */
	public static function resize($source_file, $destination_file, $type, $x, $y) {
		if (!is_file($source_file)) {
			return '$source_file: "' . $source_file . '" is missing';
		}

		$img = new Core_Image($source_file);

		if ($type === self::TYPE_MAX) {
			$img->cMax($x, $y);
		} elseif ($type === self::TYPE_MIN) {
			$img->cMin($x, $y);
		} elseif ($type === self::TYPE_PROPORTIONAL) {
			$img->cProportional($x, $y, true);
		} elseif ($type === self::TYPE_CROP) {
			$img->cCrop($x, $y);
		}

		return $img->generate('jpg', $destination_file);
	}
}
