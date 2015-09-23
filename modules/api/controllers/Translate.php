<?php

class Api_TranslateController extends Api_AdminControllerHelper {
	protected $tags = array();


	public function init() {
	}

	public function listAction() {
		$rs = Default_TranslateModel::getAll(array(
			'not_found_date' => null
		));
		$this->addResponse($rs);
	}

	public function getAction() {
		$this->checkParams(array(
			'id',
		));

		$rs = Default_TranslateModel::get(array(
			'id' => $this->getRequest()->id,
		));
		$this->addResponse($rs);
	}

	public function setAction() {
		parent::init();
		$this->checkParams(array(
			'id',
			'value',
			'lang',
		));

		Default_TranslateModel::set(array(
			'value' => $this->getRequest()->value
		), array(
			'id'            => $this->getRequest()->id,
			'language_code' => $this->getRequest()->lang,
		));

		$tag_data = Default_TranslateModel::get(array(
			'id' => $this->getRequest()->id,
		));
		// update the cached translate
		Cms_Translate::cacheSet($tag_data->tag_hash, $tag_data->value, $tag_data->language_code);
	}

	public function deleteAction() {
		$this->checkParams(array(
			'id',
		));

		Default_TranslateModel::delete(array(
			'id' => $this->getRequest()->id,
		));
	}

	public function infoAction() {
		$this->getFilesTags(cfg()->root_path);
		$this->getAdditionalTags();

		print '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		print '<pre>';
		foreach ($this->tags as $i => $row) {
			$rs = Default_TranslateModel::get(array(
				'language_code' => 'en_EN',
				'tag_hash'      => Cms_Translate::hash($row['tag']),
			));
			$css = '';
			$add_info = '';

			if (!$rs['value']) {
				$css .= 'style="color: red"';
			}
			if (!$rs['value']) {
				$add_info .= ' (missing)';
			}

			print '<i ' . $css . '>' . $i . $add_info . ' - ' . $row['location'] . '</i><br />';
			print htmlspecialchars($row['tag']) . '<br />';
			print '<hr />';
		}
		print '</pre>';
	}

	public function scanAction() {
		$this->getFilesTags(cfg()->root_path);
		$this->getAdditionalTags();

		$this->tags[] = array(
			'tag'      => 'LES TEST',
			'location' => null,
		);

		$tags = array();
		$hashes = array();
		foreach ($this->tags as $row) {
			$tags[] = strtolower($row['tag']);
			$hashes[] = '"' . Cms_Translate::hash($row['tag']) . '"';

			Default_TranslateModel::addIgnore(array(
				'tag'           => strtolower($row['tag']),
				'tag_hash'      => Cms_Translate::hash($row['tag']),
				'language_code' => Cms_Translate::getLanguageCode('default')
			));
		}

		Default_TranslateModel::setNotFound($hashes);
		$tags = array_unique($tags);


		$this->addResponse(count($tags));
//		print json_encode(array(
//			'tags' => $tags,
//		));
	}

	protected function getFilesTags($dir) {
		$files = Core_Files::listFiles($dir, true);
		foreach ($files as $file) {
			$extension = Core_Files::getExtension($file);
			if (in_array($extension, array('phtml', 'php'))) {
				$content = Core_Files::getContent($file);

				if (empty($content)) {
					continue;
				}

				preg_match_all('/__(\(\'|\("|\()(.*?)(\'\)|\"\)|\)\'|\)"|",|\',)/', $content, $matches);
				if ($matches[2]) {
					foreach ($matches[2] as $tag) {
						if (substr($tag, 0, 1) == '$') {
							continue;
						}

						$this->tags[] = array(
							'tag'      => strtolower($tag),
							'location' => $file,
						);
					}
				}
			}
		}
	}

	protected function getAdditionalTags() {
		return;
		// Settings
//		foreach (Default_UserSettingsModel::getAll(array()) as $row) {
//			$this->tags[] = array(
//				'tag'      => $row['name'],
//				'location' => 'comes from where ?!',
//			);
//		}
	}
}