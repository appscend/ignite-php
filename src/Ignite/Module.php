<?php
namespace Ignite;

use Silex\Application as SilexApp;
use Silex\ControllerProviderInterface;
use Yosymfony\Toml\Toml;

abstract class Module implements ControllerProviderInterface, \ArrayAccess {

	private $moduleName = '';
	private $moduleSettings = [];

	abstract public function views(Application $app);

	public function __construct(Application $app) {
		$this->moduleName = (new \ReflectionClass($this))->getShortName();
		$app->parsedLayout = Toml::parse(MODULES_DIR.'/'.$this->moduleName.'/config.toml');
	}

    public function connect(SilexApp $app) {
		$app->setModuleName($this->moduleName);

		return $this->views($app);
    }
    
    public static function configurationDirectory() {
	    return __DIR__."/config";
    }

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->moduleSettings);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->moduleSettings[$offset];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$this->moduleSettings[$offset] = $value;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->moduleSettings[$offset]);
	}
}
?>