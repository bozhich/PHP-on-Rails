<?php

/**
 * in other words returns a Time String like facebook 'Before 10 minutes' and so...
 */
class Cms_Time {
	/**
	 * @var array
	 */
	protected static $days = array();

	/**
	 * @var array
	 */
	protected static $months = array();


	/**
	 *
	 */
	public static function initTranslate() {
		self::$days = array(
			1 => __('time: day 1'),
			2 => __('time: day 2'),
			3 => __('time: day 3'),
			4 => __('time: day 4'),
			5 => __('time: day 5'),
			6 => __('time: day 6'),
			7 => __('time: day 7'),
		);

		self::$months = array(
			1  => __('time: month 1'),
			2  => __('time: month 2'),
			3  => __('time: month 3'),
			4  => __('time: month 4'),
			5  => __('time: month 5'),
			6  => __('time: month 6'),
			7  => __('time: month 7'),
			8  => __('time: month 8'),
			9  => __('time: month 9'),
			10 => __('time: month 10'),
			11 => __('time: month 11'),
			12 => __('time: month 12'),
		);
	}


	/**
	 * @param $date
	 * @return mixed|string
	 */
	public static function label($date) {
		$orig_time = (is_string($date) ? strtotime($date) : $date);
		$curr_time = time();
		$diff = self::dateDiff($orig_time, $curr_time);

		$hours_label = date('H:i', $orig_time) != '00:00' ? date('H:i', $orig_time) : null;

		if ($diff['seconds_total'] < 60) {
			if ($diff['positively']) {
				if ($diff['seconds'] > 1) {
					return __('time: after %1$s seconds', $diff['seconds']);
				} else {
					return __('time: after one second');
				}
			} else {
				if ($diff['seconds'] > 1) {
					return __('time: before %1$s seconds', $diff['seconds']);
				} else {
					return __('time: before one second');
				}
			}
		} elseif ($diff['hours_total'] == 0) {
			if ($diff['positively']) {
				if ($diff['minutes'] > 1) {
					return __('time: after %1$s minutes', $diff['minutes']);
				} else {
					return __('time: after one minute');
				}
			} else {

				if ($diff['minutes'] > 1) {
					return __('time: before %1$s minutes', $diff['minutes']);
				} else {
					return __('time: before one minute');
				}
			}
		} elseif ($diff['hours_total'] < 24) {
			if ($diff['positively']) {
				if ($diff['hours'] > 1) {
					if ($diff['minutes'] > 1) {
						return __('time: after %1$s hours and %2$s minutes', $diff['hours'], $diff['minutes']);
					} else {
						return __('time: after %1$s hours', $diff['hours']);
					}
				} else {
					if ($diff['minutes'] > 1) {
						return __('time: after one hour and %1$s minutes', $diff['minutes']);
					} else {
						return __('time: after one hour');
					}
				}
			} else {
				if ($diff['hours'] > 1) {
					if ($diff['minutes'] > 1) {
						return __('time: before %1$s hours and %2$s minutes', $diff['hours'], $diff['minutes']);
					} else {
						return __('time: before %1$s hours', $diff['hours']);
					}
				} else {
					if ($diff['minutes'] > 1) {
						return __('time: before one hour and %1$s minutes', $diff['minutes']);
					} else {
						return __('time: before one hour');
					}
				}
			}
		} elseif ($diff['days_calendar'] == 1) {
			if ($diff['positively']) {
				return __('time: tomorrow at %1$s', $hours_label);
			} else {
				return __('time: yesterday at %1$s', $hours_label);
			}
		} elseif ($diff['days_calendar'] <= 4) {
			return self::$days[date('N', $orig_time)] . ' ' . date('H:i', $orig_time);
		} elseif ($diff['years_calendar'] == 0) {
			return __('time: month: %1$s; day: %2$s; hours: %3$s', self::$months[date('n', $orig_time)], date('j ', $orig_time), $hours_label);
		} else {
			return __('time: year: %1$s; month: %2$s; day: %3$s; hours: %4$s', date('Y', $orig_time), self::$months[date('n', $orig_time)], date('j ', $orig_time), $hours_label);
		}
	}


	/**
	 * @param $d1
	 * @param $d2
	 * @return array
	 */
	protected static function dateDiff($d1, $d2) {
		$d1 = (is_string($d1) ? strtotime($d1) : $d1);
		$d2 = (is_string($d2) ? strtotime($d2) : $d2);


		$diff_secs = abs($d1 - $d2);
		$base_year = min(date('Y', $d1), date('Y', $d2));

		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
		$d1_gr = explode('-', date('Y-m-d', $d1));
		$d2_gr = explode('-', date('Y-m-d', $d2));

		return array(
			'years_calendar' => abs($d1_gr[0] - $d2_gr[0]),
			'years'          => date('Y', $diff) - $base_year,
			'months_total'   => (date('Y', $diff) - $base_year) * 12 + date('n', $diff) - 1,
			'months'         => date('n', $diff) - 1,
			'days_calendar'  => abs(gregoriantojd($d1_gr[1], $d1_gr[2], $d1_gr[0]) - gregoriantojd($d2_gr[1], $d2_gr[2], $d2_gr[0])),
			'days_total'     => floor($diff_secs / (3600 * 24)),
			'days'           => date('j', $diff) - 1,
			'hours_total'    => floor($diff_secs / 3600),
			'hours'          => date('G', $diff),
			'minutes_total'  => floor($diff_secs / 60),
			'minutes'        => (int) date('i', $diff),
			'seconds_total'  => $diff_secs,
			'seconds'        => (int) date('s', $diff),
			'positively'     => $d1 > $d2,
		);
	}


	/**
	 * @param $birth_date
	 * @return mixed
	 */
	public static function calculateBirthData($birth_date) {
		$diff = self::dateDiff($birth_date, time());
		if (!$birth_date) {
			$rs['birth_year'] = null;
			$rs['birth_month'] = null;
			$rs['birth_day'] = null;
		} else {
			list($rs['birth_year'], $rs['birth_month'], $rs['birth_day']) = explode('-', $birth_date);
		}

		return $rs;
	}


	/**
	 * @param $birth_date
	 * @return mixed
	 */
	public static function calculateYearsPassed($from_date) {
		$diff = self::dateDiff($from_date, time());

		return $diff['years'];
	}


	/**
	 * @param $time
	 * @return string
	 */
	public static function hours($time) {
		$r_hours = floor($time / 3600);
		$r_minutes = floor(($time - $r_hours * 3600) / 60);
		$r_seconds = $time - ($r_minutes * 60) - ($r_hours * 3600);

		if (strlen($r_hours) <= 2) {
			$r_hours = str_repeat(0, 2 - strlen($r_hours)) . $r_hours;
		}
		if (strlen($r_minutes) <= 2) {
			$r_minutes = str_repeat(0, 2 - strlen($r_minutes)) . $r_minutes;
		}
		if (strlen($r_seconds) <= 2) {
			$r_seconds = str_repeat(0, 2 - strlen($r_seconds)) . $r_seconds;
		}

		return $r_hours . ':' . $r_minutes . ':' . $r_seconds;

	}


	/**
	 * @param $date
	 * @return int
	 */
	public static function sign($date) {
		$month = (int) date('m', strtotime($date));
		$day = (int) date('d', strtotime($date));

		if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 20)) {
			return 1;
		}

		if (($month == 4 && $day >= 21) || ($month == 5 && $day <= 21)) {
			return 2;
		}

		if (($month == 5 && $day >= 22) || ($month == 6 && $day <= 21)) {
			return 3;
		}

		if (($month == 6 && $day >= 22) || ($month == 7 && $day <= 22)) {
			return 4;
		}

		if (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) {
			return 5;
		}

		if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 21)) {
			return 6;
		}

		if (($month == 9 && $day >= 22) || ($month == 10 && $day <= 22)) {
			return 7;
		}

		if (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) {
			return 8;
		}

		if (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) {
			return 9;
		}

		if (($month == 12 && $day >= 22) || ($month == 1 && $day <= 20)) {
			return 10;
		}

		if (($month == 1 && $day >= 21) || ($month == 2 && $day <= 19)) {
			return 11;
		}

		if (($month == 2 && $day >= 20) || ($month == 3 && $day <= 20)) {
			return 12;
		}
	}
}

Cms_Time::initTranslate();