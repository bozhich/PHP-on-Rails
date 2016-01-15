<?php

class Cms_Notifications_TipLike extends Cms_Notifications_Abstract {
	const TYPE = self::TYPE_TIP_LIKE;

	protected $user_id = null;
	protected $item_id = null;

	/*
	__('%1$s liked your tip for match %2$s')
	__('%1$s and %2$s liked your tip for match %3$s')
	__('%1$s and %2$s others liked your tip for match %3$s')
	*/

	protected $messages_to_count = array(
		1 => '%1$s liked your tip for match %2$s',
		2 => '%1$s and %2$s liked your tip for match %3$s',
		3 => '%1$s and %2$s others liked your tip for match %3$s',
	);

	/**
	 * @param $user_id
	 */
	public function __construct($user_id, $item_id) {
		$this->setUserId($user_id);
		$this->setItemId($item_id);
	}

	/**
	 * @return null
	 */
	public function getItemId() {
		return $this->item_id;
	}

	/**
	 * @param null $item_id
	 */
	public function setItemId($item_id) {
		$this->item_id = $item_id;
	}


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}

	/**
	 * @return int
	 */
	public function getType() {
		return self::TYPE;
	}


	/**
	 * @return mixed
	 */
	public function getText() {
		$notifications_rs = Default_NotificationsModel::getList(array(
			'user_id' => $this->getUserId(),
			'item_id' => $this->getItemId(),
			'type'    => $this->getType(),
		));
		switch (count($notifications_rs)) {
			case 1:
				return $this->parseSingle($notifications_rs);
				break;
			case 2:
				return $this->parseDouble($notifications_rs);
				break;
			default:
				return $this->parseMulti($notifications_rs);
				break;
		}
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	protected function parseSingle($data) {
		$message = $this->messages_to_count[1];
		$params = array();

		foreach ($data as $row) {
			$author_rs = Default_UsersModel::get(array(
				'id' => $row->author_id,
			));
			$comment_rs = Default_CommentsModel::get(array(
				'id' => $row->item_id,
				'res_type' => Const_Comments::RESOURCE_TYPE_MATCH
			));
			$match_rs = Default_MatchesModel::get(array(
				'id' => $comment_rs->res_id
			));
			$match_full_rs = Default_MatchesModel::getMatchesFullData(array(
				'm.id' => $comment_rs->res_id
			));

			$match_link = Default_MatchesHelper::buildMatchLinkByMatchRow($match_rs);

			$params[] = '<a href="' . l('user/profile/' . $author_rs->user) . '">' . $author_rs->user . '</a>';
			$params[] = '<a href="' . $match_link . '">' . $match_full_rs[0]->local_team_name . ' vs ' . $match_full_rs[0]->visitor_team_name . '</a>';

			break;
		}

		return __($message, $params);
	}


	/**
	 * @param $data
	 * @return mixed
	 */
	protected function parseDouble($data) {
		$message = $this->messages_to_count[2];
		$params = array();

		foreach ($data as $row) {
			$author_rs = Default_UsersModel::get(array(
				'id' => $row->author_id,
			));
			$comment_rs = Default_CommentsModel::get(array(
				'id' => $row->item_id,
				'res_type' => Const_Comments::RESOURCE_TYPE_MATCH
			));
			$match_rs = Default_MatchesModel::get(array(
				'id' => $comment_rs->res_id
			));
			$match_full_rs = Default_MatchesModel::getMatchesFullData(array(
				'm.id' => $comment_rs->res_id
			));
			$other_author_rs = Default_UsersModel::get(array(
				'id' => $data[1]->author_id,
			));

			$match_link = Default_MatchesHelper::buildMatchLinkByMatchRow($match_rs);

			$params[] = '<a href="' . l('user/profile/' . $author_rs->user) . '">' . $author_rs->user . '</a>';
			$params[] = '<a href="' . l('user/profile/' . $other_author_rs->user) . '">' . $other_author_rs->user . '</a>';
			$params[] = '<a href="' . $match_link . '">' . $match_full_rs[0]->local_team_name . ' vs ' . $match_full_rs[0]->visitor_team_name . '</a>';

			break;
		}

		return __($message, $params);
	}


	/**
	 * @param $data
	 * @return mixed
	 */
	protected function parseMulti($data) {
		$message = $this->messages_to_count[3];
		$params = array();

		foreach ($data as $row) {
			$author_rs = Default_UsersModel::get(array(
				'id' => $row->author_id,
			));
			$comment_rs = Default_CommentsModel::get(array(
				'id' => $row->item_id,
				'res_type' => Const_Comments::RESOURCE_TYPE_MATCH
			));
			$match_rs = Default_MatchesModel::get(array(
				'id' => $comment_rs->res_id
			));
			$match_full_rs = Default_MatchesModel::getMatchesFullData(array(
				'm.id' => $comment_rs->res_id
			));

			$match_link = Default_MatchesHelper::buildMatchLinkByMatchRow($match_rs);

			$params[] = '<a href="' . l('user/profile/' . $author_rs->user) . '">' . $author_rs->user . '</a>';
			$params[] = count($data) - 1;
			$params[] = '<a href="' . $match_link . '">' . $match_full_rs[0]->local_team_name . ' vs ' . $match_full_rs[0]->visitor_team_name . '</a>';

			break;
		}

		return __($message, $params);
	}
}
