<?php

/**
 * Created by PhpStorm.
 * User: timofeev
 * Date: 15-1-20
 * Time: 0:41
 */
class Default_UsersHelper extends Default_ControllerHelper {
	const FIELD_TYPE_EMAIL = 'email';
	const FIELD_TYPE_USERNAME = 'user';
	const FIELD_TYPE_PASSWORD = 'pass';
	//field validation error types
	const ERROR_INVALID_FIELD_TYPE = 'Invalid field type';

	protected static $required_field_validations = array(
		self::FIELD_TYPE_EMAIL    => array(
			Default_View_FormHelper::VALIDATION_RULE_REQUIRED   => true,
			Default_View_FormHelper::VALIDATION_RULE_UNIQUE     => true,
			Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH => 9,
			Default_View_FormHelper::VALIDATION_RULE_MAX_LENGTH => 320,
			Default_View_FormHelper::VALIDATION_RULE_REGEX      => "/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD"
		),
		self::FIELD_TYPE_USERNAME => array(
			Default_View_FormHelper::VALIDATION_RULE_REQUIRED   => true,
			Default_View_FormHelper::VALIDATION_RULE_UNIQUE     => true,
			Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH => 6,
			Default_View_FormHelper::VALIDATION_RULE_MAX_LENGTH => 50,
			Default_View_FormHelper::VALIDATION_RULE_REGEX      => "/[a-zA-Z0-9._-]$/"
		),
		self::FIELD_TYPE_PASSWORD => array(
			Default_View_FormHelper::VALIDATION_RULE_REQUIRED   => true,
			Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH => 4
		),
	);

	public static function login($user_id, $persistent = false) {
		s()->flush();

		// Load user data
		s()->user->id = $user_id;

		// Set user session
		Default_UsersModel::set(
			array(
				'session_id' => s()->getId(),
			), array(
				'id' => s()->user->id,
			)
		);

		Default_LogsModel::insert(array(
			'user_id' => $user_id,
			'ip'      => Core_Request::getInstance()->getServer('REMOTE_ADDR'),
		), Default_LogsModel::USER_LOGIN);

		Cms_UserData::refresh(array(Cms_UserData::ATTR_ALL));

		if ($persistent) {
			s()->setPersistent();
		}

		s()->user->welcomeNotification = true;

		// Log warehouse action
		Cms_Warehouse_Abstract::add($user_id, Cms_Warehouse_Abstract::LOG_TYPE_LOGIN, array());
	}

	public static function deleteAvatar($user_id) {
		$file_location = Default_UploadHelper::getUserUploadDir($user_id);
		Core_Files::delete($file_location . DS . Default_UsersController::AVATAR_FILE_NAME);
		Core_Files::delete($file_location . DS . 'thumb_' . Default_UsersController::AVATAR_FILE_NAME);
		Default_UsersModel::set(array(
			'has_avatar' => 0
		), array(
			'id' => $user_id,
		));
	}

	public static function remove($user_id) {
		$file_location = Default_UploadHelper::getUserUploadDir($user_id);
		//Core_Files::delete($file_location . DS . Default_UsersController::AVATAR_FILE_NAME);
		//Core_Files::delete($file_location . DS . 'thumb_' . Default_UsersController::AVATAR_FILE_NAME);
		Core_Files::deleteDirectory($file_location, true, true);
		Default_UsersModel::delete(array(
			'id' => $user_id
		));

		Default_UserDetailsModel::delete(array(
			'user_id' => $user_id
		));

		Default_UserSettingsModel::delete(array(
			'user_id' => $user_id
		));
	}

	public static function validateField($field_type, $field_value) {
		if (!in_array($field_type, array(self::FIELD_TYPE_EMAIL, self::FIELD_TYPE_PASSWORD, self::FIELD_TYPE_USERNAME))) {
			return array(
				'error' => self::ERROR_INVALID_FIELD_TYPE
			);
		}

		foreach (self::$required_field_validations[$field_type] as $validation_type => $validation_req) {
			if ($validation_type == Default_View_FormHelper::VALIDATION_RULE_UNIQUE) {
				$data = array(
					$field_type => $field_value
				);
				$user_data = Default_UsersModel::get($data);
				if ($user_data) {
					return array(
						'error' => self::getErrorMessage($field_type, $validation_type)
					);
				}
			} else {
				$fulfill = Default_View_FormHelper::checkRequirementFulfilled($validation_type, $validation_req, $field_value);
				if (!$fulfill) {
					return array(
						'error' => self::getErrorMessage($field_type, $validation_type)
					);
				}
			}
		}
	}

	public static function getErrorMessage($field_type, $validation_type) {
		switch ($field_type) {
			case self::FIELD_TYPE_EMAIL:
				switch ($validation_type) {
					case Default_View_FormHelper::VALIDATION_RULE_REQUIRED:
					case Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH:
					case Default_View_FormHelper::VALIDATION_RULE_MAX_LENGTH:
					case Default_View_FormHelper::VALIDATION_RULE_REGEX:
						return __('Please enter e valid email address');
						break;
					case Default_View_FormHelper::VALIDATION_RULE_UNIQUE:
						return __('Email is already taken');
						break;
					default:
						break;
				}
				break;
			case self::FIELD_TYPE_USERNAME:
				switch ($validation_type) {
					case Default_View_FormHelper::VALIDATION_RULE_REQUIRED:
					case Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH:
						$min_username_length = self::$required_field_validations[self::FIELD_TYPE_USERNAME][Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH];

						return __('Username should be at least %1$s characters long', $min_username_length);
						break;
					case Default_View_FormHelper::VALIDATION_RULE_MAX_LENGTH:
						$max_username_length = self::$required_field_validations[self::FIELD_TYPE_USERNAME][Default_View_FormHelper::VALIDATION_RULE_MAX_LENGTH];

						return __('Username should be maximum %1$s characters long', $max_username_length);
						break;
					case Default_View_FormHelper::VALIDATION_RULE_REGEX:
						return __('your nickname can contain only latin symbols (a-z,A-Z), numbers (0-9), symbols - and _.');
						break;
					case Default_View_FormHelper::VALIDATION_RULE_UNIQUE:
						return __('Username is already taken');
						break;
					default:
						break;
				}
				break;
			case self::FIELD_TYPE_PASSWORD:
				$min_pass_length = self::$required_field_validations[self::FIELD_TYPE_PASSWORD][Default_View_FormHelper::VALIDATION_RULE_MIN_LENGTH];

				return __('Password should be at least %1$s symbols', $min_pass_length);
				break;
			default:
				break;
		}
	}
}
