<?php
if (PHP_SAPI == 'cli') {
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';
} else {
	header('Location: /');
}
