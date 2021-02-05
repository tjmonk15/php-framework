<?php

declare(strict_types=1);

namespace Core\Module;

final class Manager extends \Core\Obj {
	private static array $mods = [];
	private static ?\Core\Application $App = null;

	public static function Init(\Core\Application $App) {
		self::$App = $App;
		self::loadModules();
	}

	private static function loadModules(): void {
		$module_dir = self::$App->dirs->Modules;

		if( !is_dir($module_dir) )
			return;

		$d = dir($module_dir);
		while( false !== ($entry = $d->read()) ) {
			if( $entry == '.' || $entry == '..' )
				continue;

			$mod_path = \Core\IO\Path::Combine($module_dir, $entry);

			if( !is_dir($mod_path) )
				continue;

			$mod_json = \Core\IO\Path::Combine($mod_path, 'module.json');

			if( !is_file($mod_json) ) {
				// TODO: Log
				continue;
			}

			$info = file_get_contents($mod_json);

			if( !isset($info) || $info === false || ($info = json_decode($info, true)) == null ) {
				trigger_error('Module has an invalid definition file.', E_WARNING);
				continue;
			}

			self::LoadModule($info, $mod_path);
		}
	}

	private static function LoadModule(array $config, string $dir): void {
		$name = $config['name'];

		self::$mods[$name] = [
			'config' => $config,
			'dir' => $dir
		];
	}

	public static function GetModules(): array {
		return self::$mods;
	}

	public static function GetConfigs(): array {
		$ret = [];

		// TODO: Sort based on dependencies
		foreach( self::$mods as $name => $info ) {
			$ret[] = $info['config'];
		}

		return $ret;
	}

	public static function InitModules(): void {
		foreach( self::$mods as $info ) {
			if( !isset($info['config']) || !isset($info['config']['autoload']) )
				continue;

			foreach( $info['config']['autoload'] as $ns => $path ) {
				\Core\Autoload\StandardAutoloader::Register($ns, \Core\IO\Path::Combine($info['dir'], $path));
			}
		}
	}
}