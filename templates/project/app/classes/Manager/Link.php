<?
namespace Manager;
use Phalcon\Di\Di;

class Link{
	/**
	 * Get asset path
	 * 
	 * @param string Asset
	 * @param null|float Version of the asset
	 * 
	 * @return string Asset path
	 */
	static function getAsset(string $path, null|float $version=null):string{
		$version = $version ?? DI::getDefault()->get('config')->version;
		return Di::getDefault()->get('url')->getStatic($path).'?v='.$version;
	}
}