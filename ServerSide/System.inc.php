<?php
	namespace Phast;
	
	/*
	function wfx_exception_error_handler($errno, $errstr, $errfile, $errline)
	{
		echo("filename: \"" . $errfile . "\":" . $errline . "\n");
		echo($errstr);
		die();
		// throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	set_error_handler("Phast\\wfx_exception_error_handler");
	*/
	
	use Phast\Pages\ErrorPage;

	/**
	 * Provides event arguments during an error event.
	 * @author Michael Becker
	 */
	class ErrorEventArgs
	{
	    /**
	     * The human-readable message associated with the error.
	     * @var string
	     */
		public $Message;
		/**
		 * The error which caused this error, or null if this is the top-level error.
		 * @var NULL|ErrorEventArgs
		 */
		public $ParentError;
		
		/**
		 * Creates a new ErrorEventArgs with the given parameters.
		 * @param string $message The human-readable message associated with the error.
		 * @param NULL|ErrorEventArgs $parentError The error which caused this error, or null if this is the top-level error.
		 */
		public function __construct($message, $parentError = null)
		{
			$this->Message = $message;
			$this->ParentError = $parentError;
		}
	}
	/**
	 * A code file that is included at the immediate start of the call to System::Execute(). 
	 * @author Michael Becker
	 */
	class IncludeFile
	{
	    /**
	     * The file name of the PHP code file to include.
	     * @var string
	     */
		public $FileName;
		/**
		 * True if the file is required; false otherwise.
		 * @var boolean
		 */
		public $IsRequired;
		
		/**
		 * Creates a new IncludeFile with the given parameters.
		 * @param string $filename The file name of the PHP code file to include.
		 * @param boolean $isRequired True if the file is required; false otherwise.
		 */
		public function __construct($filename, $isRequired = false)
		{
			$this->FileName = $filename;
			$this->IsRequired = $isRequired;
		}
	}
	/**
	 * The class which contains all core functionality for the Phast system.
	 * @author Michael Becker
	 */
	class System
	{
	    /**
	     * Array of global application configuration name/value pairs. 
	     * @var array
	     */
		public static $Configuration;
		/**
		 * Array of IncludeFiles which represent PHP code files to include before executing the application.
		 * @var IncludeFile[]
		 */
		public static $IncludeFiles;
		/**
		 * True if tenanted hosting is enabled; false if this is a single-tenant application.
		 * @var boolean
		 */
		public static $EnableTenantedHosting;
		/**
		 * The name of the currently-loaded tenant. 
		 * @var string
		 */
		public static $TenantName;
		/**
		 * Error handler raised when the tenant name is unspecified in a multiple-tenant application.
		 * @var callable
		 */
		public static $UnspecifiedTenantErrorHandler;
		
		/**
		 * Global application variables
		 * @var string[]
		 */
		public static $Variables;
		
		public static $Tasks;

		public static function WriteErrorLog($message)
		{
			$caller = next(debug_backtrace());
			trigger_error($message . " (in '" . $caller['function'] . "' called from '" . $caller['file'] . "' on line " . $caller['line'] . ")");
		}
		
		/**
		 * Gets the Module with the specified name, or creates it if no Module with the specified name exists.
		 * @param string $name The name of the Module to search for.
		 */
		public static function GetModuleByName($name)
		{
			foreach (System::$Modules as $module)
			{
				if ($module->Name == $name) return $module;
			}
			
			$module = new Module($name);
			System::$Modules[] = $module;
			return $module;
		}
		
		/**
		 * Gets the relative path on the Web site for the current page.
		 * @return string $_SERVER["REQUEST_URI"]
		 */
		public static function GetCurrentRelativePath()
		{
			return $_SERVER["REQUEST_URI"];
		}
		
		/**
		 * Retrieves the value of the global configuration property with the given key if it is defined,
		 * or the default value if it has not been defined.
		 * @param string $key The key of the configuration property to search for.
		 * @param string $defaultValue The value to return if the global configuration property with the specified key has not been defined.
		 * @return string The value of the global configuration property with the given key if defined; otherwise, defaultValue.
		 */
		public static function GetConfigurationValue($key, $defaultValue = null)
		{
			if (System::HasConfigurationValue($key))
			{
				return System::$Configuration[$key];
			}
			return $defaultValue;
		}
		/**
		 * Sets the global configuration property with the given key to the specified value.
		 * @param string $key The key of the configuration property to set.
		 * @param string $value The value to which to set the property.
		 */
		public static function SetConfigurationValue($key, $value)
		{
			System::$Configuration[$key] = $value;
		}
		/**
		 * Clears the value of the global configuration property with the given key.
		 * @param string $key The key of the configuration property whose value will be cleared.
		 */
		public static function ClearConfigurationValue($key)
		{
			unset(System::$Configuration[$key]);
		}
		/**
		 * Determines whether a global configuration property with the given key is defined.
		 * @param string $key The key of the configuration property to search for.
		 * @return boolean True if the global configuration property exists; false otherwise.
		 */
		public static function HasConfigurationValue($key)
		{
			return isset(System::$Configuration[$key]);
		}
		
		/**
		 * Array of Modules which are loaded when this application executes.
		 * @var Module
		 */
		public static $Modules;
		
		/**
		 * The event handler that is called when an irrecoverable error occurs.
		 * @var callable
		 */
		public static $ErrorEventHandler;
		
		/**
		 * The event handler that is called before this application executes.
		 * @var callable
		 */
		public static $BeforeLaunchEventHandler;
		/**
		 * The event handler that is called after this application executes.
		 * @var callable
		 */
		public static $AfterLaunchEventHandler;
		
		/**
		 * Redirects the user to the specified path via a Location header.
		 * @param string $path The expandable string path to navigate to.
		 */
		public static function Redirect($path)
		{
			$realpath = System::ExpandRelativePath($path);
			header("Location: " . $realpath);
			return;
		}
		/**
		 * Expands the given path by replacing the tilde character (~) with the value of the
		 * configuration property Application.BasePath.
		 * @param string $path The path to expand.
		 * @param boolean $includeServerInfo True if server information should be included in the response; false otherwise.
		 * @return string The expanded form of the given expandable string path.
		 */
		public static function ExpandRelativePath($path, $includeServerInfo = false)
		{
			$torepl = System::GetConfigurationValue("Application.BasePath");
			if (System::$EnableTenantedHosting)
			{
				if (System::$TenantName != "")
				{
					$torepl .= "/" . System::$TenantName;
				}
				else
				{
					$torepl .= "/" . System::GetConfigurationValue("Application.DefaultTenant");
				}
			}
			
			$retval = str_replace("~", $torepl, $path);
			if ($includeServerInfo)
			{
				// from http://stackoverflow.com/questions/6768793/php-get-the-full-url
				$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
				$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
				$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
				$serverPath = $protocol . "://" . $_SERVER["SERVER_NAME"] . $port;
				$retval = $serverPath . $retval;
			}

			foreach (System::$Configuration as $name => $value)
			{
				$retval = str_replace("\$(Configuration:" . $name . ")", $value, $retval);
			}
			foreach (System::$Variables as $variable)
			{
				$retval = str_replace("\$(" . $variable->Name . ")", $variable->Value, $retval);
			}
			return $retval;
		}
		public static function RedirectToLoginPage()
		{
			System::Redirect("~/account/login");
			return;
		}
		public static function GetVirtualPath()
		{
			if (isset($_GET["virtualpath"]))
			{
				if ($_GET["virtualpath"] != null)
				{
					$array = explode("/", $_GET["virtualpath"]);
					if (System::$EnableTenantedHosting)
					{
						System::$TenantName = $array[0];
						array_shift($array);
					}
					return $array;
				}
			}
			return array();
		}
		public static function IncludeFile($filename, $isRequired)
		{
			global $RootPath;
			$filename = str_replace("~/", $RootPath . "/", $filename);
			if ($isRequired)
			{
				require_once($filename);
			}
			else
			{
				include_once($filename);
			}
		}
		
		/**
		 * Starts the Phast application.
		 * @return boolean True if the launch succeeded; false if a failure occurred.
		 */
		public static function Launch()
		{
			global $RootPath;
			$Phast_files = glob($RootPath . "/Include/Pages/*.wfx");
			
			$path = System::GetVirtualPath();
			if (System::$EnableTenantedHosting && System::$TenantName == "")
			{
				$DefaultTenant = System::GetConfigurationValue("Application.DefaultTenant");
				if ($DefaultTenant == "")
				{
					$retval = call_user_func(System::$UnspecifiedTenantErrorHandler);
					return false;
				}
				else
				{
					System::$TenantName = $DefaultTenant;
					System::Redirect("~/");
				}
			}
			
			if (is_callable(System::$BeforeLaunchEventHandler))
			{
				$retval = call_user_func(System::$BeforeLaunchEventHandler, $path);
				if (!$retval) return false;
			}
			
			if (!is_array(System::$Modules) || count(System::$Modules) == 0)
			{
				$retval = call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("There are no modules configured for this Phast application."));
				return false;
			}
			
			$success = false;
			foreach (System::$Modules as $module)
			{
				if (!$module->Enabled) continue;
				
				foreach ($module->Pages as $vpath)
				{
					$path0 = "";
					if (isset($path[0]))
					{
						$path0 = $path[0];
					}
					if ($vpath->PathName == $path0)
					{
						$xpath = $path;
						array_shift($xpath);
						if (!$vpath->Execute($xpath))
						{
							/*
							if (is_callable(System::$ErrorEventHandler))
							{
								$retval = call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("The module '" . $path[0] . "' did not execute properly"));
								if (!$retval) return false;
							}
							*/
						}
						else
						{
							$success = true;
							break;
						}
					}
				}
			}
			
			if (is_callable(System::$AfterLaunchEventHandler))
			{
				$retval = call_user_func(System::$AfterLaunchEventHandler);
				if (!$retval) return false;
			}
			
			if (!$success)
			{
				$retval = call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("The specified resource is not available on this server."));
				return false;
			}
			return true;
		}
	}
	/**
	 * Represents a module, a collection of related ModulePages.
	 * @author Michael Becker
	 */
	class Module
	{
		/**
		 * The name of this Module.
		 * @var string
		 */
		public $Name;
		/**
		 * True if this Module is enabled and will respond to ModulePage requests; false otherwise.
		 * @var boolean
		 */
		public $Enabled;
		/**
		 * Array of ModulePages that are handled by this Module.
		 * @var ModulePage[]
		 */
		public $Pages;
		
		/**
		 * Retrieves the ModulePage with the specified name on this Module, or creates one if no ModulePage with the specified name exists on this Module.
		 * @param string $name
		 * @return ModulePage
		 */
		public function GetPageByName($name)
		{
			foreach ($this->Pages as $page)
			{
				if ($page->PathName == $name) return $page;
			}
			$page = new ModulePage($name, null);
			$this->Pages[] = $page;
			return $page;
		}
		
		/**
		 * Creates a new Module with the specified parameters.
		 * @param string $name The name of this Module.
		 * @param ModulePage[] $pages Array of ModulePages that are handled by this Module.
		 */
		public function __construct($name, $pages = null)
		{
			$this->Name = $name;
			$this->Enabled = true;
			if ($pages == null) $pages = array();
			if (is_array($pages))
			{
				$this->Pages = $pages;
			}
			else
			{
				$this->Pages = array($pages);
			}
		}
	}
	/**
	 * Represents a page provided by a Module that is accessible by the specified URL.
	 * @author Michael Becker
	 */
	class ModulePage
	{
		/**
		 * The relative path of this ModulePage.
		 * @var string
		 */
		public $PathName;
		/**
		 * The user function that is executed when this ModulePage is accessed. Only valid if Pages is
		 * not defined.
		 * @var callable
		 */
		public $UserFunction;
		/**
		 * Array of ModulePages that are sub-pages of this ModulePage. Only valid if UserFunction is not
		 * defined.
		 * @var ModulePage[]
		 */
		public $Pages;
		/**
		 * The user function that is executed before this ModulePage is accessed. 
		 * @var callable
		 */
		public $BeforeExecute;
		/**
		 * The user function that is executed after this ModulePage is accessed.
		 * @var callable
		 */
		public $AfterExecute;
		/**
		 * Extra data associated with this ModulePage.
		 * @var unknown
		 */
		public $ExtraData;
		
		/**
		 * Creates a ModulePage with the specified parameters.
		 * @param string $pathName The relative path of this ModulePage.
		 * @param callable|ModulePage[] $userFunctionOrPages Either a user function to execute when this ModulePage is accessed, or an array of ModulePages that are sub-pages of this ModulePage.
		 * @param callable $beforeExecute The user function that is executed before this ModulePage is accessed.
		 * @param callable $afterExecute The user function that is executed after this ModulePage is accessed.
		 */
		public function __construct($pathName, $userFunctionOrPages, $beforeExecute = null, $afterExecute = null)
		{
			$this->PathName = $pathName;
			if (is_callable($userFunctionOrPages))
			{
				$this->UserFunction = $userFunctionOrPages;
			}
			else if (is_array($userFunctionOrPages))
			{
				$this->Pages = $userFunctionOrPages;
			}
			$this->BeforeExecute = $beforeExecute;
			$this->AfterExecute = $afterExecute;
		}
		
		/**
		 * Retrieves the ModulePage with the specified name on this ModulePage, or creates one if no ModulePage with the specified name exists on this ModulePage.
		 * @param string $name
		 * @return ModulePage
		 */
		public function GetPageByName($name)
		{
			foreach ($this->Pages as $page)
			{
				if ($page->PathName == $name) return $page;
			}
			$page = new ModulePage($name, null);
			$this->Pages[] = $page;
			return $page;
		}
		
		/**
		 * Executes this ModulePage with the specified path.
		 * @param string $path The relative path to handle via this ModulePage.
		 * @return boolean True if the specified path was handled by this ModulePage or a sub-page; false otherwise.
		 */
		public function Execute($path)
		{
			foreach (System::$IncludeFiles as $includefile)
			{
				if (get_class($includefile) != "Phast\\IncludeFile") continue;
				System::IncludeFile($includefile->FileName, $includefile->IsRequired);
			}
			
			if (is_callable($this->BeforeExecute))
			{
				$retval = call_user_func($this->BeforeExecute, $this, $path);
				if ($retval === false) return false;
			}
			if (is_array($this->Pages))
			{
				foreach ($this->Pages as $vpath)
				{
					if (((count($path) > 0) && ($vpath->PathName == $path[0])) || (count($path) == 0 && $vpath->PathName == ""))
					{
						array_shift($path);
						if (!$vpath->Execute($path))
						{
							if (is_callable(System::$ErrorEventHandler))
							{
								// $retval = call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("The module '" . $path[0] . "' did not execute properly"));
								return true;
							}
						}
						return true;
					}
				}
				return false;
			}
			
			$retval = false;
			if (is_callable($this->UserFunction))
			{
				$retval = call_user_func($this->UserFunction, $this, $path);
			}
			if (is_callable($this->AfterExecute))
			{
				$retval2 = call_user_func($this->AfterExecute, $this, $path);
				if ($retval2 === false) return false;
			}
			return $retval;
		}
	}

	require_once("Enumeration.inc.php");
	
	require_once("RenderMode.inc.php");
	
	require_once("EventArgs.inc.php");
	require_once("CancelEventArgs.inc.php");
	require_once("RenderEventArgs.inc.php");
	
	require_once("Enum.inc.php");
	require_once("StringMethods.inc.php");
	require_once("JH.Utilities.inc.php");

	/**
	 * Provides an enumeration of predefined values for horizontal alignment of content.
	 * @author Michael Becker
	 */
	abstract class HorizontalAlignment extends Enumeration
	{
		/**
		 * The horizontal alignment is not specified.
		 * @var int 0
		 */
		const Inherit = 0;
		/**
		 * The content is aligned to the left (near).
		 * @var int 1
		 */
		const Left = 1;
		/**
		 * The content is aligned in the center.
		 * @var int 2
		 */
		const Center = 2;
		/**
		 * The content is aligned to the right (far).
		 * @var int 3
		 */
		const Right = 3;
	}
	/**
	 * Provides an enumeration of predefined values for vertical alignment of content.
	 * @author Michael Becker
	 */
	abstract class VerticalAlignment extends Enumeration
	{
		/**
		 * The vertical alignment is not specified.
		 * @var int 0
		 */
		const Inherit = 0;
		/**
		 * The content is aligned to the top (near).
		 * @var int 1
		 */
		const Top = 1;
		/**
		 * The content is aligned in the middle.
		 * @var int 2
		 */
		const Middle = 2;
		/**
		 * The content is aligned to the bottom (far).
		 * @var int 3
		 */
		const Bottom = 3;
	}
	
	require("WebApplication.inc.php");
	require("WebApplicationTask.inc.php");
	
	require("WebNamespaceReference.inc.php");
	require("WebVariable.inc.php");
	
	require("WebOpenGraphSettings.inc.php");
	require("WebResourceLink.inc.php");
	require("WebScript.inc.php");
	require("WebStyleSheet.inc.php");
	
	require("WebControlAttribute.inc.php");
	require("WebControlClientIDMode.inc.php");
	require("WebControl.inc.php");
	
	require("WebPage.inc.php");
	require("WebPageCommand.inc.php");
	require("WebPageMessage.inc.php");
	require("WebPageMetadata.inc.php");
	require("WebPageVariable.inc.php");
	
	require("HTMLControl.inc.php");

	require("Parser/ControlLoader.inc.php");
	require("Parser/PhastParser.inc.php");
	
	System::$Configuration = array();
	System::$EnableTenantedHosting = false;
	
	System::$IncludeFiles = array();
	System::$Modules = array();
	System::$UnspecifiedTenantErrorHandler = function()
	{
		return call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("No tenant name was specified for this tenanted hosting application."));
	};
	System::$ErrorEventHandler = function($e)
	{
		echo($e->Message);
	};
	System::$Variables = array();
	
	global $PhastRootPath;
	$PhastRootPath = dirname(__FILE__);
	
	global $RootPath;
	require_once($RootPath . "/Include/Configuration.inc.php");
	
	require_once("Data/DataSystem.inc.php");
	
	// After loading the configuration, attempt to establish PDO connection (must be done before everything
	// else gets initialized, in case something depends on the PDO)
	include_once("PDO.inc.php");
	
	// Global Controls loader
	$a = glob($PhastRootPath . "/WebControls/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	// Global HTMLControls loader
	$a = glob($PhastRootPath . "/HTMLControls/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	// Local Objects loader
	$a = glob($RootPath . "/Include/Objects/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	// Local Controls loader
	$a = glob($RootPath . "/Include/WebControls/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	// Local MasterPages loader
	$a = glob($RootPath . "/Include/MasterPages/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	// Local Pages loader
	$a = glob($RootPath . "/Include/Pages/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	// Local Modules loader
	$a = glob($RootPath . "/Include/Modules/*/Main.inc.php");
	foreach ($a as $filename)
	{
		include_once($filename);
	}
	
	// Local Module Pages loader
	// $a = glob($RootPath . "/Include/Modules/*/Pages/*.inc.php");
	/*
	foreach ($a as $filename)
	{
		include_once($filename);
	}
	*/
	
	session_start();
?>
