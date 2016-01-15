<?php

class Api_StatisticsController extends Api_AdminControllerHelper {

	public function init() {
	}

	public function statsAction() {
		$return = array();

		/* get disk space free (in bytes) */
		$df = disk_free_space("/");
		/* and get disk space total (in bytes)  */
		$dt = disk_total_space("/");
		/* now we calculate the disk space used (in bytes) */
		$du = $dt - $df;
		/* percentage of disk used - this will be used to also set the width % of the progress bar */
		$dp = sprintf('%.2f', ($du / $dt) * 100);

		/* and we formate the size from bytes to MB, GB, etc. */
		$df = Core_Tools::formatSize($df);
		$du = Core_Tools::formatSize($du);
		$dt = Core_Tools::formatSize($dt);

		$return['disk'] = array(
			'free'     => $df,
			'total'    => $dt,
			'usage'    => $du,
			'percente' => $dp,
		);

		$return['cpu'] = sys_getloadavg();


		//uptime
		exec("uptime", $system); // get the uptime stats
		$string = $system[0]; // this might not be necessary

		@list(
			$dummy,   //0 => ""
			$uptime,  //1 => "23:06:37" (8)
			$dummy,   //2 => "up" (2)
			$up_days, //3 => "1"
			$dummy,   //4 => "day," (4)
			$dummy,   //5 => ""
			$dummy,   //6 => "1:53," (5)
			$dummy,   //7 => ""
			$users,   //8 => "4"
			$dummy,   //9 => "users," (6)
			$dummy,   //10 => ""
			$dummy,   //11 => "load" (4)
			$dummy,   //12 => "average:" (8)
			$load0,   //13 => "0.73," (5)
			$load1,   //14 => "0.95," (5)
			$load2,   //15 => "1.05" (4)
			) = explode(" ", $string);
		unset($dummy);

		$return['uptime'] = array(
			'uptime'  => $uptime,
			'up_days' => $up_days,
			'users'   => $users,
			'load0'   => $load0,
			'load1'   => $load1,
			'load2'   => $load2,
		);

		//memory
		$free = shell_exec('free');
		$free = (string) trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);

		$total_available = $mem[1];
		$memory_usage = $mem[2];
		$return['memory'] = array(
			'total' => $total_available / 1000000,
			'used'  => $memory_usage / 1000000,
			'free'  => ($total_available - $memory_usage) / 1000000,
		);

		// users
		$users_rs = Default_UsersModel::getCount();
		$return['users'] = $users_rs['cnt'];

		// coments
		$comments_rs = Default_CommentsModel::getCount();
		$return['comments'] = $comments_rs['cnt'];


		// feedback
		$feedback_rs = Default_FeedbackModel::getCount(array(
			'is_read' => 0
		));
		$return['feedback'] = $feedback_rs['cnt'];

		// reports
		$reports_rs = Default_ReportsModel::getCount(array(
			'is_read' => 0
		));
		$return['reports'] = $reports_rs['cnt'];

		// slips
		$slips_rs = Default_BettingSlipsModel::getCount();
		$return['slips'] = $slips_rs['cnt'];

		// messages
		$messages_rs = Default_MessagesModel::getCount();
		$return['messages'] = $messages_rs['cnt'];

//		online users
		$online_rs = Default_UsersModel::getOnline(cfg()->activity_period);
		$return['online'] = count($online_rs);

		// regs nad logins
		$regs_and_logins = Api_StatisticsHelper::getUsersData();
		$return['regs'] = $regs_and_logins;


		// votes
		$votes_rs = Default_VotesModel::getCount();
		$return['votes'] = $votes_rs['cnt'];

		// ALL translates
		$translates_rs = Default_TranslateModel::getCount(array(
			'not_found_date' => '0000-00-00 00:00:00'
		));
		$return['translates'] = $translates_rs['cnt'];

		$this->addResponse($return);
	}
}