<?php

// @todo error handling
class Config {
	protected static $config;
	
	public static function init($c) {
		if (isset(self::$config))
			return;
		
		self::$config = $c->getInvokeArg('bootstrap')->getOptions();
	}
	
	public static function get($key, $section = 'appSettings') {
		return self::$config[$section][$key];
	}
	
}
