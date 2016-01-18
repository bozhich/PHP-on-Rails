<?php
return array(
	'db_data'           => array(
		'driver'  => 'db_driver', //pgsql / mysqli etc..
		'host'    => 'db_host',
		'user'    => 'db_user',
		'pass'    => 'db_pass',
		'db'      => 'db_name',
		'charset' => 'utf8',
	),
	'dev_mode'          => false,
	'maintenance'       => false,

	'home_dir'          => '/',
	'error_path'        => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'logs' . DS,
	'root_path'         => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS,
	'cache_path'        => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'public' . DS . 'cache' . DS,
	'static_path'       => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'public' . DS . 'static' . DS,
	'share_path'        => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'public' . DS . 'share' . DS,
	'minify_script_src' => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'public' . DS,
	'migration_path'    => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'modules' . DS . 'migration' . DS . 'files' . DS,
	'tests_path'        => DS . 'home' . DS . 'user' . DS . 'www' . DS . 'mvc' . DS . 'modules' . DS . 'tests' . DS . 'files' . DS,
	'site'              => 'live.com',
	'site_address'      => 'http://live.com',
	'static_address'    => 'http://live.com/static/',
	'cache_address'     => 'http://live.com/cache/',
	'share_address'     => 'http://live.com/share/',
	'language_codes'    => array(
		'en_EN' => 'English',
		'bg_BG' => 'Български', // first language is set as default
	),
	'memcache_host'     => '127.0.0.1',
	'memcache_port'     => '11211',
	'enviroment'        => 'Tracy\Debugger::DEVELOPMENT',
);
