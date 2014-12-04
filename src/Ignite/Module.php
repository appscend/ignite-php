<?php
namespace Ignite;

use Ignite\Helpers\XmlDomConstruct;
use Silex\Application as SilexApp;
use Silex\ControllerProviderInterface;
use Yosymfony\Toml\Toml;

abstract class Module implements ControllerProviderInterface, \ArrayAccess {

	protected $moduleName = '';
	protected $moduleSettings = [];
	protected $parsedLayout = [];

	/**
	 * @var Application
	 */
	protected $app = null;

	abstract public function views(Application $app);

	public function __construct(Application $app) {
		$refl = new \ReflectionClass($this);
		$this->moduleName = $refl->getShortName();

		if (file_exists(MODULES_DIR.'/'.$this->moduleName.'/config.toml'))
			$this->parsedLayout = Toml::parse(MODULES_DIR.'/'.$this->moduleName.'/config.toml');

		if (isset($this->parsedLayout['!include'])) {
			$includes = $this->parsedLayout['!include'];

			foreach ($includes as $path)
				$this->overwritePropsFromFile(APP_ROOT_DIR.'/'.$path);
		}

		$app->setCurrentModule($this);
		$this->app = $app;
	}

	public function generateStaticViews() {
		$views = Toml::parse(CONFIG_DIR.'/static.toml');

		if (!file_exists($this->app['env']['app.static_xml_path'])) {
			mkdir(APP_ROOT_DIR . '/' . $this->app['env']['app.static_xml_path']);
		}
		if (!file_exists($this->app['env']['app.static_xml_path'].'/'.$this->moduleName))
			mkdir(APP_ROOT_DIR . '/' . $this->app['env']['app.static_xml_path'] . '/'.$this->moduleName);

		foreach ($views as $id => $v) {
			if ($this->app->getView($id) !== null) {

				if (empty($v)) {
					//this is a static view that doesn't have additional parameters (empty array in toml file)
					$renderedView = $this->app->getView($id)->getFullView()->render();
					$xmlConstruct = new XmlDomConstruct('1.0', 'UTF-8');
					$xmlConstruct->fromMixed($renderedView);

					file_put_contents(APP_ROOT_DIR . '/' . $this->app['env']['app.static_xml_path'] . $this->moduleName . "/$id.xml", $xmlConstruct->saveXML(null, LIBXML_NOEMPTYTAG));
				} else {
					//this static view CAN HAVE both additional params OR NO additional params
					foreach ($v as $target => $data) {

						if ($target == '__static') {
							$renderedView = $this->app->getView($id)->getFullView()->render();
							$xmlConstruct = new XmlDomConstruct('1.0', 'UTF-8');
							$xmlConstruct->fromMixed($renderedView);

							file_put_contents(APP_ROOT_DIR . '/' . $this->app['env']['app.static_xml_path'] . $this->moduleName . "/$id.xml", $xmlConstruct->saveXML(null, LIBXML_NOEMPTYTAG));

							continue;
						}

						$renderedView = $this->app->getView($id)->getFullView($data)->render();
						$xmlConstruct = new XmlDomConstruct('1.0', 'UTF-8');
						$xmlConstruct->fromMixed($renderedView);

						file_put_contents(APP_ROOT_DIR . '/' . $this->app['env']['app.static_xml_path'] . $this->moduleName . "/$id.$target.xml", $xmlConstruct->saveXML(null, LIBXML_NOEMPTYTAG));
					}
				}
			}
		}
	}

	public function overwritePropsFromFile($path) {
		$this->parsedLayout = self::array_merge_recursive_distinct($this->parsedLayout, Toml::parse($path));
	}

	public function getLayout() {
		return $this->parsedLayout;
	}

	public function getName() {
		return $this->moduleName;
	}

    public function connect(SilexApp $app) {
		return $this->views($app);
    }

	public static function array_merge_recursive_distinct(array &$array1, array &$array2) {
		$merged = $array1;

		foreach ($array2 as $key => &$value) {
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]) )
				$merged [$key] = self::array_merge_recursive_distinct($merged[$key], $value);

			else
				$merged [$key] = $value;
		}

		return $merged;
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