<?php

/**
 * Class Core_Cache
 */
class Core_Cache {
	/**
	 * @var
	 */
	private static $server;


	/**
	 * @param      $key
	 * @param      $var
	 * @param      $expire
	 * @param bool $version_prefix
	 * @return bool
	 */
	public static function set($key, $var, $expire, $version_prefix = true) {
		if ($version_prefix) {
			$key = self::getVersionPrefix() . $key;
		}

		return self::getServer()->set($key, $var, $expire);
	}


	/**
	 * @param      $key
	 * @param bool $version_prefix
	 * @return array|string
	 */
	public static function get($key, $version_prefix = true) {
		if ($version_prefix) {
			$key = self::getVersionPrefix() . $key;
		}

		return self::getServer()->get($key);
	}


	/**
	 * @param      $key
	 * @param bool $version_prefix
	 * @return bool
	 */
	public static function remove($key, $version_prefix = true) {
		if ($version_prefix) {
			$key = self::getVersionPrefix() . $key;
		}

		return self::getServer()->delete($key);
	}


	/**
	 * @param      $key
	 * @param bool $version_prefix
	 * @return array|bool
	 */
	public static function info($key, $version_prefix = true) {
		if ($version_prefix) {
			$key = self::getVersionPrefix() . $key;
		}

		return self::getServer()->getStats();
	}

	/**
	 * @return Memcache
	 */
	protected static function getServer() {
		if (self::$server === null) {
			self::$server = new Memcached(self::getVersionPrefix());
			$servers = self::$server->getServerList();
			if (empty($servers) || !$servers) {
				self::$server->addServer(cfg()->memcache_host, cfg()->memcache_port);
			}

		}

		return self::$server;
	}


	/**
	 * @return string
	 */
	protected static function getVersionPrefix() {
		return cfg()->getId() . '_';
	}

}