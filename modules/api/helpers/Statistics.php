<?php

class Api_StatisticsHelper extends Api_ControllerHelper {
	const REG_ORDER_BY_DAY = 1;
	const REG_ORDER_BY_MONTH = 2;
	const REG_ORDER_BY_YEAR = 3; // i hope we need this ^^

	// gets registrations and logins grouped by day/month/year
	public static function getUsersData($group_type = self::REG_ORDER_BY_DAY) {
		switch ($group_type) {
			case self::REG_ORDER_BY_DAY:
				$group = 'date(timestamp)';
				break;
			case self::REG_ORDER_BY_MONTH:
				$group = 'month(timestamp)';
				break;
			case self::REG_ORDER_BY_YEAR:
				$group = 'year(timestamp)';
				break;
		}
		$regs_rs = Default_UsersModel::getStats($group);
		$logins_rs = Default_LogsModel::getStats($group);

//		$return = array(
//			'dates'  => array(),
//			'regs'   => array(),
//			'logins' => array(),
//		);

		// fill the info
		$k = 1;
		$return = array();
		foreach ($logins_rs as $date => $row) {
			$return[$k]['dates'] = $date;
			$return[$k]['regs'] = isset($regs_rs[$date]) ? $regs_rs[$date]->count : 0;
			$return[$k]['logins'] = $logins_rs[$date]->count;
			$k++;
		}

		return $return;
	}
}
//
//Morris.Bar({
//        element: 'morris-bar-chart',
//        data: [{
//	y: '2006',
//            a: 100,
//            b: 90
//        }, {
//	y: '2007',
//            a: 75,
//            b: 65
//        }, {
//	y: '2008',
//            a: 50,
//            b: 40
//        }, {
//	y: '2009',
//            a: 75,
//            b: 65
//        }, {
//	y: '2010',
//            a: 50,
//            b: 40
//        }, {
//	y: '2011',
//            a: 75,
//            b: 65
//        }, {
//	y: '2012',
//            a: 100,
//            b: 90
//        }],
//        xkey: 'y',
//        ykeys: ['a', 'b'],
//        labels: ['Series A', 'Series B'],
//        hideHover: 'auto',
//        resize: true
//    });
