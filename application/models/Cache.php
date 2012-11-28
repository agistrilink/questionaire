<?php

// @todo ICache?
class Cache {
	protected static $cache;
	public static function getInstance($backend = 'File') {
		if (isset(self::$cache))
			return self::$cache;
		
		$frontend = 'Core';
		$frontendOptions = array('lifetime' => 1200, 'automatic_serialization' => true);
		$backendOptions = $backend == 'Memcached'
			?array('host' => '127.0.0.1','port' => 11211)
			:array('cache_dir' => Config::get('cacheDir'))
			;
		return self::$cache = Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions);
	}
	
	public static function clear() {
		if (!isset(self::$cache))
			return;
		
		self::$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}	
}
