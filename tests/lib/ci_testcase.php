<?php


// Need a way to change dependencies (core libs and laoded libs)
// Need a way to set the CI class

class CI_TestCase extends PHPUnit_Framework_TestCase {
		
	public static $global_map = array(
		'benchmark'	=> 'bm',
		'config'	=> 'cfg',
		'hooks'		=> 'ext',
		'utf8'		=> 'uni',
		'router'	=> 'rtr',
		'output'	=> 'out',
		'security'	=> 'sec',
		'input'		=> 'in',
		'lang'		=> 'lang',
		
		// @todo the loader is an edge case
		'loader'	=> 'load'
	);
	
	protected $ci_config = array();
	
	protected $ci_instance;
	protected static $ci_test_instance;
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrite runBare
	 *
	 * PHPUnit instantiates the test classes before
	 * running them individually. So right before a test
	 * runs we set our instance. Normally this step would
	 * happen in setUp, but someone is bound to forget to
	 * call the parent method and debugging this is no fun.
	 */
	public function runBare()
	{
		self::$ci_test_instance = $this;
		parent::runBare();
	}
	
	// --------------------------------------------------------------------
	
	public static function instance()
	{
		return self::$ci_test_instance;
	}
	
	// --------------------------------------------------------------------
	
	function ci_instance($obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance;
		}
		
		$this->ci_instance = $obj;
	}
	
	// --------------------------------------------------------------------
	
	function ci_instance_var($name, $obj = FALSE)
	{
		if ( ! is_object($obj))
		{
			return $this->ci_instance->$name;
		}
		
		$this->ci_instance->$name =& $obj;
	}
	
	// --------------------------------------------------------------------
	
	// Set a class to a mock before it is loaded
	function ci_library($name)
	{
		
	}

	// --------------------------------------------------------------------

	/**
	 * Grab a core class
	 *
	 * Loads the correct core class without extensions
	 * and returns a reference to the class name in the
	 * globals array with the correct key. This way the
	 * test can modify the variable it assigns to and
	 * still maintain the global.
	 */
	function &ci_core_class($name)
	{
		$name = strtolower($name);
		
		if (isset(self::$global_map[$name]))
		{
			$class_name = ucfirst($name);
			$global_name = self::$global_map[$name];
		}
		elseif (in_array($name, self::$global_map))
		{
			$class_name = ucfirst(array_search($name, self::$global_map));
			$global_name = $name;
		}
		else
		{
			throw new Exception('Not a valid core class.');
		}
		
		if ( ! class_exists('CI_'.$class_name))
		{
			require_once BASEPATH.'core/'.$class_name.'.php';
		}
		
		$GLOBALS[strtoupper($global_name)] = 'CI_'.$class_name;
		return $GLOBALS[strtoupper($global_name)];
	}
	
	// --------------------------------------------------------------------
	
	// convenience function for global mocks
	function ci_set_core_class($name, $obj)
	{
		$orig =& $this->ci_core_class($name);
		$orig = $obj;
	}
	
	// --------------------------------------------------------------------
	
	function ci_set_config($key, $val = '')
	{
		if (is_array($key))
		{
			$this->ci_config = $key;
		}
		else
		{
			$this->ci_config[$key] = $val;
		}
	}
	
	// --------------------------------------------------------------------
	
	function ci_config_array()
	{
		return $this->ci_config;
	}
	
	// --------------------------------------------------------------------
	
	function ci_config_item($item)
	{
		return '';
	}
}

// EOF