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
	
	use Phast\Parser\PhastParser;
	use Phast\Pages\ErrorPage;
	
	use Phast\Configuration\ConfigurationParser;

	use Phast\Configuration\V1\ConfigurationComment;
	use Phast\Configuration\V1\ConfigurationProperty;
	use Phast\Configuration\V1\ConfigurationSpacer;
	use Phast\Configuration\V1\Phast\Configuration\V1;
		
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
		private static $RequestURL;
		public static function GetRequestURL()
		{
			return System::$RequestURL;
		}
		
		private static $ApplicationPath;
		public static function GetApplicationPath()
		{
			return System::$ApplicationPath;
		}
		public static function SetApplicationPath($value)
		{
			if (System::$ApplicationPath != null)
			{
				die("application path already set - cannot be set again!");
			}
			System::$ApplicationPath = $value;
		}
		
		private static $SystemPath;
		public static function GetSystemPath()
		{
			return System::$SystemPath;
		}
		public static function SetSystemPath($value)
		{
			if (System::$SystemPath != null)
			{
				die("system path already set - cannot be set again!");
			}
			System::$SystemPath = $value;
		}
		
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
		 * The format in which to serve WebPages.
		 * @var WebPageFormat
		 */
		public static $WebPageFormat;
		
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
		 * Gets the relative path on the Web site for the current page.
		 * @return string $_SERVER["REQUEST_URI"]
		 */
		public static function GetCurrentRelativePath()
		{
			return $_SERVER["REQUEST_URI"];
		}
		
		/**
		 * Retrieves all key-value pairs associated with the global configuration property.
		 * @return array:
		 */
		public static function GetConfigurationValues()
		{
			return System::$Configuration;
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
			
			$value = System::$ConfigurationParser->RetrieveProperty($key, $defaultValue);
			if ($value != null) return $value->Value;
			
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
		
		public static function SaveConfigurationFile()
		{
			$filename = System::GetApplicationPath() . "/Include/Configuration.inc.php";
			$file = fopen($filename, "w");
			if ($file === false) return false;
			
			$properties = array
			(
				new ConfigurationComment("Whether we should enable users to run the setup application"),
				new ConfigurationProperty("Setup.Enabled", "true"),
				new ConfigurationSpacer(),
				new ConfigurationComment("The base path of the Web site"),
				new ConfigurationProperty("Application.BasePath", ""),
				new ConfigurationSpacer(),
				// new ConfigurationComment("The default tenant for the Web site"),
				// new ConfigurationProperty("Application.DefaultTenant", "default"),
				// new ConfigurationSpacer(),
				new ConfigurationComment("The location of static Phast-related files (scripts, stylesheets, etc.)"),
				new ConfigurationProperty("System.StaticPath", "//static.alcehosting.net/dropins/Phast")
			);
			
			$hasExtraProperties = false;
			foreach (System::$Configuration as $key => $value)
			{
				if ($key == "Setup.Enabled" || $key == "Application.BasePath" || $key == "System.StaticPath")
				{
					continue;
				}
				
				if (!$hasExtraProperties)
				{
					$hasExtraProperties = true;
					$properties[] = new ConfigurationSpacer();
				}
				$properties[] = new ConfigurationProperty($key, $value);
			}
			
			fwrite($file, "<?php\r\n");
			fwrite($file, "\tuse Phast\\System;\r\n");
			fwrite($file, "\r\n");
			foreach ($properties as $item)
			{
				if (get_class($item) == "Phast\\Configuration\\V1\\ConfigurationProperty")
				{
					fwrite($file, "\tSystem::SetConfigurationValue(\"" . $item->ID . "\", \"" . $item->Value . "\");\r\n");
				}
				else if (get_class($item) == "Phast\\Configuration\\V1\\ConfigurationComment")
				{
					fwrite($file, "\t// " . $item->Value . "\r\n");
				}
				else if (get_class($item) == "Phast\\Configuration\\V1\\ConfigurationSpacer")
				{
					fwrite($file, "\t\r\n");
				}
			}
			fwrite($file, "?>\r\n");
			fclose($file);
			
			return true;
		}
		
		/**
		 * The WebPageParser
		 * @var PhastParser
		 */
		public static $Parser;
		
		/**
		 * The ConfigurationParser
		 * @var ConfigurationParser
		 */
		public static $ConfigurationParser;
		
		/**
		 * The page that is currently being processed.
		 * @var WebPage
		 */
		public static $CurrentPage;
		
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
			
			// parse the string for variables
			$retval_len = strlen($retval);
			$ret = "";
			for ($i = 0; $i < $retval_len; $i++)
			{
				$c = substr($retval, $i, 1);
				
				if ($c == "\$")
				{
					if ($i > 0 && substr($retval, $i - 1, 1) == "\\")
					{
						// literal $
						$ret .= "$";
					}
					else if ($i < $retval_len - 2)
					{
						$x = stripos($retval, ")", $i + 1);
						if ($x === false) $x = strlen($retval);
						
						$varString = substr($retval, $i + 2, $x - ($i + 2));
						
						$category = null;
						$variable = $varString;
						$defaultValue = null;
						
						$posCategoryValueSeparator = stripos($varString, ":");
						if ($posCategoryValueSeparator !== false)
						{
							$category = substr($varString, 0, $posCategoryValueSeparator);
							$variable = substr($varString, $posCategoryValueSeparator + 1);
						}
						
						$posValueDefaultSeparator = stripos($varString, "|");
						if ($posValueDefaultSeparator !== false)
						{
							$defaultValue = substr($varString, $posValueDefaultSeparator + 1);
						}
						
						$handled = false;
						switch ($category)
						{
							case "Path":
							{
								if (System::$CurrentPage != null)
								{
									$ret .= System::$CurrentPage->GetPathVariableValue($variable, $defaultValue);
									$handled = true;
								}
								break;
							}
							case "Configuration":
							{
								if (array_key_exists($variable, System::$Configuration))
								{
									$ret .= System::$Configuration[$variable];
									$handled = true;
								}
								break;
							}
							case "Variables":
							{
								if (array_key_exists($variable, System::$Variables))
								{
									foreach (System::$Variables as $varr)
									{
										if ($varr->Name == $variable)
										{
											$ret .= $varr->Value;
											$handled = true;
											break;
										}
									}
								}
								break;
							}
						}
						if (!$handled) $ret .= $defaultValue;
						$i += strlen($varString) + 2; // '(' + varString + ')'; the preceding '$' is taken care of by $i++ in the next loop iteration
						continue;
					}
					else
					{
						$ret .= $c;
					}
				}
				else
				{
					$ret .= $c;
				}
			}
			return $ret;
		}
		public static function RedirectToLoginPage()
		{
			$_SESSION["System.LastRedirectURL"] = $_SERVER["REQUEST_URI"];
			System::Redirect("~/account/login");
			return;
		}
		public static function RedirectFromLoginPage()
		{
			if ($_SESSION["System.LastRedirectURL"] != null)
			{
				System::Redirect($_SESSION["System.LastRedirectURL"]);
			}
			else
			{
				$url = System::GetConfigurationValue("Application.DefaultURL", "~/");
				System::Redirect($url);
			}
		}
		public static function GetVirtualPath($supportTenantedHosting = true)
		{
			if (isset($_GET["virtualpath"]))
			{
				if ($_GET["virtualpath"] != null)
				{
					$array = explode("/", $_GET["virtualpath"]);
					if (System::$EnableTenantedHosting && $supportTenantedHosting)
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
			$filename = str_replace("~/", System::GetApplicationPath() . "/", $filename);
			if ($isRequired)
			{
				require_once($filename);
			}
			else
			{
				include_once($filename);
			}
		}
		
		public static function Initialize()
		{
			$RootPath = System::GetApplicationPath();
				
			// require_once changed to include_once to ensure that PHP configuration is not required for Phast 2.0 (Website.xml) sites
			include_once($RootPath . "/Include/Configuration.inc.php");
				
			// load the xml files in Configuration directory
			$a = glob($RootPath . "/Include/Configuration/*.xml");
			foreach ($a as $filename)
			{
				System::LoadXMLConfigurationFile($filename);
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
				
			// Local WebControls PHPX Code-Behind loader
			$a = glob($RootPath . "/Include/WebControls/*.phpx.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
			// Local WebControls PHPX loader
			$a = glob($RootPath . "/Include/WebControls/*.phpx");
			foreach ($a as $filename)
			{
				System::$Parser->LoadFile($filename);
			}
				
			// Local MasterPages Code-Behind loader
			$a = glob($RootPath . "/Include/MasterPages/*.phpx.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
			// Local MasterPages loader
			$a = glob($RootPath . "/Include/MasterPages/*.phpx");
			foreach ($a as $filename)
			{
				System::$Parser->LoadFile($filename);
			}
				
			// Local Pages Code-Behind loader
			$a = glob($RootPath . "/Include/Pages/*.phpx.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
			// Local Pages loader
			$a = glob($RootPath . "/Include/Pages/*.phpx");
			foreach ($a as $filename)
			{
				System::$Parser->LoadFile($filename);
			}
				
			// Module Objects Code-Behind loader
			$a = glob($RootPath . "/Include/Modules/*/Objects/*.inc.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
				
			// Module WebControls PHPX Code-Behind loader
			$a = glob($RootPath . "/Include/Modules/*/WebControls/*.phpx.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
			// Module WebControls PHPX loader
			$a = glob($RootPath . "/Include/Modules/*/WebControls/*.phpx");
			foreach ($a as $filename)
			{
				System::$Parser->LoadFile($filename);
			}
				
			// Module Pages Code-Behind loader
			$a = glob($RootPath . "/Include/Modules/*/Pages/*.phpx.php");
			foreach ($a as $filename)
			{
				require_once($filename);
			}
			// Module Pages loader
			$a = glob($RootPath . "/Include/Modules/*/Pages/*.phpx");
			foreach ($a as $filename)
			{
				System::$Parser->LoadFile($filename);
			}
				
			// Application code-behind (V2 style)
			if (file_exists($RootPath . "/Include/Application.inc.php"))
			{
				require_once($RootPath . "/Include/Application.inc.php");
			}
				
			// Application PHPX and code-behind (V3 style)
			if (file_exists($RootPath . "/Include/Application.phpx"))
			{
				System::$Parser->LoadFile($RootPath . "/Include/Application.phpx");
			}
			if (file_exists($RootPath . "/Include/Application.phpx.php"))
			{
				require_once($RootPath . "/Include/Application.phpx.php");
			}
		}
		
		/**
		 * Starts the Phast application.
		 * @return boolean True if the launch succeeded; false if a failure occurred.
		 */
		public static function Launch()
		{
			System::Initialize();
			
			$path = System::GetVirtualPath();
			
			// strip path extension if there is one
			$pathLast = $path[count($path) - 1];
			$ix = strripos($pathLast, ".");
			if ($ix !== false)
			{
				$pathExt = substr($pathLast, $ix + 1);
				$path[count($path) - 1] = substr($pathLast, 0, $ix);
				
				switch ($pathExt)
				{
					case "json":
					{
						System::$WebPageFormat = WebPageFormat::JSON;
						break;
					}
					case "xml":
					{
						System::$WebPageFormat = WebPageFormat::XML;
						break;
					}
					case "html":
					{
						System::$WebPageFormat = WebPageFormat::HTML;
						break;
					}
					case "js":
					{
						System::$WebPageFormat = WebPageFormat::JavaScript;
						break;
					}
					default:
					{
						if ($path[count($path) - 1] != "")
						{
							$path[count($path) - 1] = $path[count($path) - 1];
							System::Redirect("~/" . implode("/", $path));
							return;
						}
					}
				}
			}
			
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
			
			$success = false;
			
			$pathVars = array();
			
			$actualPage = null;
			
			foreach (System::$Parser->Pages as $page)
			{
				if (!$page->Enabled) continue;

				$actualPathParts = $path;
				// try to parse the path, for example:
				// profile/$(username)/dashboard
				
				$pathParts = explode("/", $page->FileName);
				$pathPartCount = count($pathParts);
				$found = true;
				for ($i = 0; $i < $pathPartCount; $i++)
				{
					$pathPart = $pathParts[$i];
					if (stripos($pathPart, "$(") == 0 && stripos($pathPart, ")") == strlen($pathPart) - 1)
					{
						$pathVarName = substr($pathPart, 2, strlen($pathPart) - 3);
						$pathVars[$pathVarName] = $actualPathParts[$i];
					}
					else
					{
						$app = "";
						if (isset($actualPathParts[$i])) $app = $actualPathParts[$i];
						
						if ($app != $pathPart && (!($app == "" && $pathPart == "")))
						{
							// a literal path string is broken; we can't use this
							$found = false;
							break;
						}
					}
				}
				if ($found)
				{
					$actualPage = $page;
					break;
				}
			}
			
			if ($actualPage != null)
			{
				foreach ($pathVars as $key => $value)
				{
					$actualPage->PathVariables[] = new WebVariable($key, $value);
				}
				
				System::$CurrentPage = $actualPage;
				System::$RequestURL = "~/" . implode("/", $path);
				
				$actualPage->Render();
				
				System::$CurrentPage = null;
				System::$RequestURL = null;
				
				$success = true;
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
		
		public static function LoadXMLConfigurationFile($filename)
		{
			System::$ConfigurationParser->LoadFile($filename);
		}
	}
	
	require_once("Enumeration.inc.php");
	require_once("Orientation.inc.php");
	require_once("UUID.inc.php");
	
	require_once("RandomStringGenerator.inc.php");
	
	require_once("RenderMode.inc.php");
	
	require_once("EventArgs.inc.php");
	require_once("CancelEventArgs.inc.php");
	require_once("RenderEventArgs.inc.php");
	
	require_once("Enum.inc.php");
	require_once("StringMethods.inc.php");
	require_once("JH.Utilities.inc.php");

	require_once("Configuration/Property.inc.php");
	require_once("Configuration/Group.inc.php");
	require_once("Configuration/Flavor.inc.php");
	require_once("Configuration/ConfigurationParser.inc.php");

	require_once("Validator.inc.php");

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
	
	require("Conditionals/ConditionalComparison.inc.php");
	require("Conditionals/ConditionalStatement.inc.php");
	
	require_once("Compilers/StyleSheet/Internal/LessStyleSheetCompiler.inc.php");
	
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

	require("WebPageFormat.inc.php");
	
	require("WebPage.inc.php");
	require("WebPageCommand.inc.php");
	require("WebPageMessage.inc.php");
	require("WebPageMetadata.inc.php");
	require("WebPageVariable.inc.php");
	
	require("HTMLControl.inc.php");

	require("Parser/ControlLoader.inc.php");
	require("Parser/PhastParser.inc.php");

	require("Configuration/V1/ConfigurationItem.inc.php");
	
	require("Configuration/V1/ConfigurationComment.inc.php");
	require("Configuration/V1/ConfigurationProperty.inc.php");
	require("Configuration/V1/ConfigurationSpacer.inc.php");
	
	System::$Configuration = array();
	System::$EnableTenantedHosting = false;
	
	System::$IncludeFiles = array();
	System::$UnspecifiedTenantErrorHandler = function()
	{
		return call_user_func(System::$ErrorEventHandler, new ErrorEventArgs("No tenant name was specified for this tenanted hosting application."));
	};
	System::$ErrorEventHandler = function($e)
	{
		echo($e->Message);
	};
	System::$Variables = array();
	System::$Parser = new PhastParser();

	System::SetSystemPath(dirname(__FILE__));
	$PhastRootPath = System::GetSystemPath();
	
	// Initialize the ConfigurationParser
	if (System::$ConfigurationParser == null) System::$ConfigurationParser = new ConfigurationParser();
	
	require_once("Data/DataSystem.inc.php");
	
	// Global Controls loader
	$a = glob($PhastRootPath . "/WebControls/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	// Global WebControls PHPX Code-Behind loader
	$a = glob($PhastRootPath . "/WebControls/*.phpx.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	// Global WebControls PHPX loader
	$a = glob($PhastRootPath . "/WebControls/*.phpx");
	foreach ($a as $filename)
	{
		System::$Parser->LoadFile($filename);
	}
	
	// Global HTMLControls loader
	$a = glob($PhastRootPath . "/HTMLControls/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	$a = glob($PhastRootPath . "/Compilers/StyleSheet/Internal/Formatters/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	$a = glob($PhastRootPath . "/Validators/*.inc.php");
	foreach ($a as $filename)
	{
		require_once($filename);
	}
	
	session_start();
?>
