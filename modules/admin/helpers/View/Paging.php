<?php

class Admin_View_PagingHelper {
	protected static $store = array();

	const Admin_ID = 'paging_';
	const MAX_PAGES = 10;

	const PARAM_TOTAL_RESULTS = 1;
	const PARAM_PER_PAGE = 2;
	const PARAM_ID = 3;
	const PARAM_PREFIX = 4;
	const PARAM_SUFIX = 5;
	const PARAM_HIDE_NAV = 6;
	const PARAM_TOTAL_PAGES = 7;
	const PARAM_CURRENT_PAGE = 8;
	const PARAM_MAX_PAGES = 9;


	public function createPaging($total_results, $per_page, array $parameters = array()) {
		$id = !empty($parameters[self::PARAM_ID]) ? $parameters[self::PARAM_ID] : self::Admin_ID;

		self::$store[$id] = $parameters;
		self::$store[$id][self::PARAM_TOTAL_RESULTS] = $total_results;
		self::$store[$id][self::PARAM_PER_PAGE] = $per_page;
		self::$store[$id][self::PARAM_TOTAL_PAGES] = ceil($total_results / $per_page);

		if (empty(self::$store[$id][self::PARAM_CURRENT_PAGE])) {
			$request = Core_Request::getInstance();
			self::$store[$id][self::PARAM_CURRENT_PAGE] = $request->{$id . 'p'} ? $request->{$id . 'p'} : 1;
		}

		if (empty(self::$store[$id][self::PARAM_MAX_PAGES])) {
			self::$store[$id][self::PARAM_MAX_PAGES] = self::MAX_PAGES;
		}

		if (empty(self::$store[$id][self::PARAM_SUFIX])) {
			self::$store[$id][self::PARAM_SUFIX] = '';
		}
	}


	public function fetchPaging($id = self::Admin_ID) {
		if (empty(self::$store[$id])) {
			return false;
		}

		$parameters = self::$store[$id];

		if ($parameters[self::PARAM_TOTAL_PAGES] < 2) {
			return;
		}

		$source = '<div class="paging">';
		// Navigation before
		if (empty($parameters[self::PARAM_HIDE_NAV]) && $parameters[self::PARAM_CURRENT_PAGE] != 1) {
			$css_class = $parameters[self::PARAM_CURRENT_PAGE] == 1 ? 'current' : '';
			$source .= '<div class="pagingOutline"><a href="' . $this->getPageLocation($id, 1) . '" class="outlined ' . $css_class . '" tooltip="' . __('paging first') . '">&laquo;</a></div>';
			$source .= '<div class="pagingOutline"><a href="' . $this->getPageLocation($id, $parameters[self::PARAM_CURRENT_PAGE] - 1) . '" class="outlined ' . $css_class . '" tooltip="' . __('paging prev') . '">&lsaquo;</a></div>';
		}

		// Paging
		if ($parameters[self::PARAM_TOTAL_PAGES] > $parameters[self::PARAM_MAX_PAGES]) {
			$half_max = ceil($parameters[self::PARAM_MAX_PAGES] / 2);
			if ($parameters[self::PARAM_CURRENT_PAGE] - $half_max < 1) {
				$start = 1;
				$end = $parameters[self::PARAM_MAX_PAGES];
			} elseif ($parameters[self::PARAM_CURRENT_PAGE] + $half_max > $parameters[self::PARAM_TOTAL_PAGES]) {
				$start = $parameters[self::PARAM_CURRENT_PAGE] - ($parameters[self::PARAM_MAX_PAGES] - ($parameters[self::PARAM_TOTAL_PAGES] - $parameters[self::PARAM_CURRENT_PAGE])) + 2;
				$end = $parameters[self::PARAM_TOTAL_PAGES];
			} else {
				$start = $parameters[self::PARAM_CURRENT_PAGE] - $half_max + 1;
				$end = $parameters[self::PARAM_CURRENT_PAGE] + $half_max - 1;
			}
		} else {
			$start = 1;
			$end = $parameters[self::PARAM_TOTAL_PAGES];
		}

		for ($i = $start; $i <= $end; $i++) {
			$css_class = $parameters[self::PARAM_CURRENT_PAGE] == $i ? 'current' : '';
			$source .= '<a href="' . $this->getPageLocation($id, $i) . '" class=" ' . $css_class . '">' . $i . '</a>';
		}

		// Navigation after
		if (empty($parameters[self::PARAM_HIDE_NAV]) && $parameters[self::PARAM_CURRENT_PAGE] != $parameters[self::PARAM_TOTAL_PAGES]) {
			$css_class = $parameters[self::PARAM_CURRENT_PAGE] == $parameters[self::PARAM_TOTAL_PAGES] ? 'current' : '';
			$source .= '<div class="pagingOutline"><a href="' . $this->getPageLocation($id, $parameters[self::PARAM_CURRENT_PAGE] + 1) . '" class="outlined ' . $css_class . '" tooltip="' . __('paging next') . '">&rsaquo;</a></div>';
			$source .= '<div class="pagingOutline"><a href="' . $this->getPageLocation($id, $parameters[self::PARAM_TOTAL_PAGES]) . '" class="outlined ' . $css_class . '" tooltip="' . __('paging last') . '">&raquo;</a></div>';
		}
		$source .= '</div>';

		return $source;
	}


	protected function getPageLocation($id, $page) {
		if ($page <= 0) {
			$page = 1;
		} elseif ($page > self::$store[$id][self::PARAM_TOTAL_PAGES]) {
			$page = self::$store[$id][self::PARAM_TOTAL_PAGES];
		}

		if (empty(self::$store[$id][self::PARAM_PREFIX])) {
			return l(null, array(
				Core_Url::ADD_CURRENT_QUERY => true,
				Core_Url::ADD_QUERY         => array($id . 'p' => $page),
			)) . self::$store[$id][self::PARAM_SUFIX];
		} else {
			return self::$store[$id][self::PARAM_PREFIX] . $page . self::$store[$id][self::PARAM_SUFIX];
		}
	}
}
