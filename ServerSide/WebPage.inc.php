<?php
	namespace Phast;
	
	use Phast\HTMLControls\Form;
	use Phast\HTMLControls\FormMethod;
	use Phast\HTMLControls\HTMLControlInput;
	use Phast\HTMLControls\HTMLControlInputType;
	
	use Phast\Parser\ControlLoader;
	use Phast\Parser\PhastPage;
	use Phast\Parser\PhastParser;
	
	use UniversalEditor\ObjectModels\Markup\MarkupElement;
	
	/**
	 * Contains functionality common to all Phast Web pages. 
	 * @author Michael Becker
	 */
    class WebPage
    {
		public $BreadcrumbItems;
		
		/**
		 * True if this Web page should be considered when parsing a path; false otherwise.
		 * @var boolean
		 */
		public $Enabled;
		
		/**
		 * True if this Web page supports tenanted hosting; false otherwise.
		 * @var boolean
		 */
		public $EnableTenantedHosting;
		
		/**
		 * The path to this Web page, including any path variables.
		 * @var string
		 */
		public $FileName;
		
		/**
		 * The path to the physical file which defines this Web page.
		 * @var string
		 */
		public $PhysicalFileName;
		
		public function GetCanonicalFileName()
		{
			$path = System::GetVirtualPath();
			// strip path extension if there is one
			$pathLast = $path[count($path) - 1];
			$ix = strripos($pathLast, ".");
			if ($ix !== false)
			{
				$pathExt = substr($pathLast, $ix + 1);
				$path[count($path) - 1] = substr($pathLast, 0, $ix);
			}
			return System::ExpandRelativePath("~/" . implode($path, "/"));
		}
		
		/**
		 * The title of this Web page.
		 * @var string
		 */
        public $Title;
		public $ClassList;
		
		/**
		 * The class reference for this WebPage.
		 * @var PhastPage
		 */
		public $ClassReference;
		
		/**
		 * The user function that is called when this WebPage is rendered.
		 * @var callable
		 */
		public $Content;
		
		/**
		 * The controls that are rendered on this WebPage.
		 * @var WebControl[]
		 */
		public $Controls;
		
		/**
		 * Retrieves all controls, including those on the MasterPage.
		 * @var WebControl[]
		 */
		public function GetAllControls()
		{
			$controls = $this->Controls;
			if ($this->MasterPage != null)
			{
				$controls = $this->MergeMasterPageControls($this->MasterPage->Controls);
			}
			return $controls;
		}
		
		/**
		 * The WebPage from which this WebPage inherits.
		 * @var WebPage
		 */
		public $MasterPage;
		
		/**
		 * The metadata associated with this WebPage.
		 * @var WebPageMetadata[]
		 */
        public $Metadata;
        
        /**
         * The references to XML namespaces and Phast elements associated with this WebPage.
         * @var WebNamespaceReference[]
         */
        public $References;
        
        public $ResourceLinks;
        public $Scripts;
        /**
         * The cascading style sheets associated with this WebPage.
         * @var WebStyleSheet[]
         */
        public $StyleSheets;
		public $Styles;
		/**
		 * Variables that are defined on the client, manifested as HTML INPUT elements and transmitted via POST.
		 * @var WebVariable[]
		 */
		public $ClientVariables;
		/**
		 * Variables that are defined on the server, invisible to the client.
		 * @var WebVariable[]
		 */
		public $ServerVariables;
		/**
		 * Variables that are defined in the virtual path (for example /path/to/$(variable) )
		 * @var WebVariable[]
		 */
		public $PathVariables;
		public $OpenGraph;
		
		/**
		 * When true, omits the DOCTYPE HTML declaration at the beginning of the document to be compatible with
		 * older Web browsers.
		 * @var boolean
		 */
		public $UseCompatibleRenderingMode;
		
		/**
		 * Specifies whether the request for this page is for partial content only.
		 * @var boolean
		 */
		public $IsPartial;

		/**
		 * Retrieves the control with the specified ID in this control's child control collection.
		 * @param string $id The ID of the control to search for.
		 * @return WebControl|NULL The control with the specified ID, or null if no control with the specified ID was found.
		 */
		public function GetControlByID($id, $recurse = true)
		{
			$ctls = $this->GetAllControls();
			foreach ($ctls as $ctl)
			{
				if ($ctl->ID == $id) return $ctl;
				if ($recurse)
				{
					$ctl1 = $ctl->GetControlByID($id, true);
					if ($ctl1 != null) return $ctl1;
				}
			}
			
			trigger_error("Control with ID '" . $id . "' not found");
			return null;
		}
		
		/**
		 * 
		 * @param MarkupElement $element
		 * @param PhastParser $parser
		 * @return WebPage
		 */
		public static function FromMarkup($element, $parser)
		{
			$page = new WebPage();

			$attEnableTenantedHosting = $element->GetAttribute("EnableTenantedHosting");
			if ($attEnableTenantedHosting != null)
			{
				$page->EnableTenantedHosting = ($attEnableTenantedHosting->Value == "true");
			}
			$attFileName = $element->GetAttribute("FileName");
			if ($attFileName != null)
			{
				$page->FileName = $attFileName->Value;
			}
			$attrMasterPageFileName = $element->GetAttribute("MasterPageFileName");
			if ($attrMasterPageFileName != null)
			{
				$page->MasterPage = $parser->GetMasterPageByFileName($attrMasterPageFileName->Value);
			}
			$attTitle = $element->GetAttribute("Title");
			if ($attTitle != null)
			{
				$page->Title = $attTitle->Value;
			}
			$attUseCompatibleRenderingMode = $element->GetAttribute("UseCompatibleRenderingMode");
			if ($attUseCompatibleRenderingMode != null)
			{
				$page->UseCompatibleRenderingMode = ($attUseCompatibleRenderingMode->Value == "true");
			}
				
			$tagScripts = $element->GetElement("Scripts");
			if ($tagScripts != null)
			{
				foreach ($tagScripts->Elements as $elem)
				{
					if (get_class($elem) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					$attContentType = $elem->GetAttribute("ContentType");
					$contentType = "text/javascript";
					if ($attContentType != null) $contentType = $attContentType->Value;
						
					$page->Scripts[] = new WebScript($elem->GetAttribute("FileName")->Value, $contentType);
				}
			}
			$tagStyleSheets = $element->GetElement("StyleSheets");
			if ($tagStyleSheets != null)
			{
				foreach ($tagStyleSheets->Elements as $elem)
				{
					if (get_class($elem) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					$attFileName = $elem->GetAttribute("FileName");
					if ($attFileName == null) continue;
						
					$page->StyleSheets[] = new WebStyleSheet($attFileName->Value);
				}
			}
			$tagVariables = $element->GetElement("ClientVariables");
			if ($tagVariables != null)
			{
				foreach ($tagVariables->Elements as $elem)
				{
					if (get_class($elem) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					$attName = $elem->GetAttribute("Name");
					if ($attName == null) continue;
						
					$value = "";
					$attValue = $elem->GetAttribute("Value");
					if ($attValue != null) $value = $attValue->Value;
						
					$page->ClientVariables[] = new WebVariable($attName->Value, $value);
				}
			}
			$tagVariables = $element->GetElement("ServerVariables");
			if ($tagVariables != null)
			{
				foreach ($tagVariables->Elements as $elem)
				{
					if (get_class($elem) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					$attName = $elem->GetAttribute("Name");
					if ($attName == null) continue;
						
					$value = "";
					$attValue = $elem->GetAttribute("Value");
					if ($attValue != null) $value = $attValue->Value;
						
					$page->ServerVariables[] = new WebVariable($attName->Value, $value);
				}
			}
			
			$tagReferences = $element->GetElement("References");
			if ($tagReferences != null)
			{
				foreach ($tagReferences->Elements as $elem)
				{
					if (get_class($elem) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					$attTagPrefix = $elem->GetAttribute("TagPrefix");
					if ($attTagPrefix == null) continue;
						
					$attNamespacePath = $elem->GetAttribute("NamespacePath");
					if ($attNamespacePath == null) continue;
						
					$attNamespaceURL = $elem->GetAttribute("NamespaceURL");
					$namespaceURL = "";
					if ($attNamespaceURL != null) $namespaceURL = $attNamespaceURL->Value;
						
					$page->References[] = new WebNamespaceReference($attTagPrefix->Value, $attNamespacePath->Value, $namespaceURL);
				}
			}
			
			$references = $page->References;
			if ($page->MasterPage != null)
			{
				$references = $page->MasterPage->References;
				foreach ($page->References as $reference)
				{
					$references[] = $reference;
				}
			}
			foreach ($references as $reference)
			{
				ControlLoader::$Namespaces[$reference->TagPrefix] = $reference->NamespacePath;
			}
				
			$tagContent = $element->GetElement("Content");
			if ($tagContent != null)
			{
				foreach ($tagContent->Elements as $elem)
				{
					ControlLoader::LoadControl($elem, $page);
				}
			}
				
			$attrCssClass = $element->GetAttribute("CssClass");
			if ($attrCssClass != null)
			{
				$page->ClassList[] = $attrCssClass->Value;
			}
				
			$attrCodeBehindClassName = $element->GetAttribute("CodeBehindClassName");
			if ($attrCodeBehindClassName != null)
			{
				$page->CodeBehindClassName = $attrCodeBehindClassName->Value;
				
				if (class_exists($page->CodeBehindClassName))
				{
					$page->ClassReference = new $page->CodeBehindClassName();
					$page->ClassReference->Page = $page;
					$page->IsPostback = ($_SERVER["REQUEST_METHOD"] == "POST");
					
					if (method_exists($page->ClassReference, "OnClassLoaded"))
					{
						$page->ClassReference->OnClassLoaded(EventArgs::GetEmptyInstance());
					}
					else
					{
						System::WriteErrorLog("Code-behind for '" . $page->CodeBehindClassName . "' does not define an 'OnClassLoaded' entry point");
					}
				}
				else
				{
					System::WriteErrorLog("Code-behind for '" . $page->CodeBehindClassName . "' not found");
				}
			}
			return $page;
		}
		
		/**
		 * Creates the metadata tag for the given metadata.
		 * @param WebPageMetadata $metadata
		 * @return HTMLControl The HTMLControl that represents the META HTML tag referencing the given metadata.
		 */
		public static function CreateMetaTag(WebPageMetadata $metadata)
		{
			$tag = new HTMLControl();
			$tag->TagName = "meta";
			$tag->HasContent = false;
			
			switch ($metadata->Type)
			{
				case WebPageMetadataType::Name:
				{
					$tag->Attributes[] = new WebControlAttribute("name", $metadata->Name);
					break;
				}
				case WebPageMetadataType::HTTPEquivalent:
				{
					$tag->Attributes[] = new WebControlAttribute("http-equiv", $metadata->Name);
					break;
				}
				case WebPageMetadataType::Property:
				{
					$tag->Attributes[] = new WebControlAttribute("property", $metadata->Name);
					break;
				}
			}
			
			$tag->Attributes[] = new WebControlAttribute("content", $metadata->Content);
			return $tag;
		}
		/**
		 * Creates the resource link tag for the given resource link.
		 * @param WebResourceLink $link
		 * @return HTMLControl The HTMLControl that represents the LINK HTML tag referencing the given resource link.
		 */
		public static function CreateResourceLinkTag(WebResourceLink $link)
		{
			$tag = new HTMLControl();
			$tag->TagName = "link";
			$tag->HasContent = false;
			$tag->Attributes[] = new WebControlAttribute("rel", $link->Relationship);
			if ($link->ContentType != null)
			{
				$tag->Attributes[] = new WebControlAttribute("type", $link->ContentType);
			}
			$tag->Attributes[] = new WebControlAttribute("href", System::ExpandRelativePath($link->URL));
			return $tag;
		}
		public static function CreateScriptTag(WebScript $script)
		{
			$tag= new HTMLControl();
			$tag->TagName = "script";
			if ($script->ContentType != "")
			{
				$tag->Attributes[] = new WebControlAttribute("type", $script->ContentType);
			}
			if ($script->Content != "")
			{
				$tag->InnerHTML = $script->Content;
			}
			if ($script->FileName != "")
			{
				$tag->Attributes[] = new WebControlAttribute("src", System::ExpandRelativePath($script->FileName));
			}
			return $tag;
		}
		/**
		 * Creates the style sheet tag for the given style sheet.
		 * @param WebStyleSheet $stylesheet
		 * @return HTMLControl The HTMLControl that represents the LINK HTML tag referencing the given style sheet.
		 */
		public static function CreateStyleSheetTag(WebStyleSheet $stylesheet)
		{
			return WebPage::CreateResourceLinkTag(new WebResourceLink($stylesheet->FileName, "stylesheet", (($stylesheet->ContentType == "") ? "text/css" : $stylesheet->ContentType)));
		}
		
		public function __construct()
		{
			$this->BreadcrumbItems = array();
			$this->EnableTenantedHosting = true;
			$this->Metadata = array();
			$this->OpenGraph = new WebOpenGraphSettings();
			$this->ResourceLinks = array();
			$this->ClassList = array();
			$this->Enabled = true;
			$this->MasterPage = null;
			$this->References = array();
			$this->Scripts = array();
			$this->StyleSheets = array();
			$this->Styles = array();
			
			$this->ClientVariables = array();
			$this->PathVariables = array();
			$this->ServerVariables = array();
			
			$this->UseCompatibleRenderingMode = false;
			
			$this->IsPartial = isset($_GET["partial"]);
			
			$ce = new CancelEventArgs();
			$this->OnCreating($ce);
			if ($ce->Cancel) return;
			
			foreach ($this->ClientVariables as $variable)
			{
				if (isset($_POST["WebPageVariable_" . $variable->Name . "_Value"]))
				{
					$variable->Value = $_POST["WebPageVariable_" . $variable->Name . "_Value"];
				}
				if (isset($_POST["WebPageVariable_" . $variable->Name . "_IsSet"]))
				{
					$variable->IsSet = $_POST["WebPageVariable_" . $variable->Name . "_IsSet"];
				}
			}
			
			$this->OnCreated(EventArgs::GetEmptyInstance());
		}
        
        private $isInitialized;
        
        public function Initialize($renderingPage = null)
        {
        	if ($this->isInitialized) return true;
        	if ($renderingPage == null) $renderingPage = $this;
        	
        	if ($this->MasterPage != null)
        	{
        		if (!$this->MasterPage->Initialize($this)) return false;
        	}
			
        	$initializingEventArgs = new CancelEventArgs();
        	$initializingEventArgs->RenderingPage = $renderingPage;
        	
        	$initializedEventArgs = new EventArgs();
        	$initializedEventArgs->RenderingPage = $renderingPage;
        	
        	if (method_exists($this, "OnInitializing"))
        	{
            	$this->OnInitializing($initializingEventArgs);
            	if ($initializingEventArgs->Cancel) return false;
        	}
        	$initializingEventArgs->Cancel = false;
            
            if ($this->ClassReference != null)
            {
            	if (method_exists($this->ClassReference, "OnInitializing"))
            	{
	            	$this->ClassReference->OnInitializing($initializingEventArgs);
	            	if ($initializingEventArgs->Cancel) return false;
            	}
            	if (method_exists($this->ClassReference, "OnInitialized"))
            	{
            		$this->ClassReference->OnInitialized($initializedEventArgs);
            	}
            }

            if (method_exists($this, "OnInitialized"))
            {
	            $this->OnInitialized($initializedEventArgs);
            }
           	$this->isInitialized = true;
            return true;
        }
        
        protected function OnInitializing(CancelEventArgs $e)
        {
        	
        }
        protected function OnInitialized(EventArgs $e)
        {
        	
        }

        /**
         * The function called before the page constructor is called.
         * @param CancelEventArgs $e The arguments for this event handler.
         */
        protected function OnCreating(CancelEventArgs $e)
        {
        	
        }
        /**
         * The function called after the page constructor has completed.
         * @param EventArgs $e The arguments for this event handler.
         */
        protected function OnCreated(EventArgs $e)
        {
        	
        }

        /**
         * The function called before the page has started rendering.
         * @param RenderedEventArgs $e The arguments for this event handler.
         */
        protected function OnRendering(RenderingEventArgs $e)
        {
        	
        }
        /**
         * The function called after the page has completely rendered.
         * @param RenderedEventArgs $e The arguments for this event handler.
         */
        protected function OnRendered(RenderedEventArgs $e)
        {
        	
        }
        
        /**
         * Performs any necessary processing before the main content of the Web page. Designed for use by page developers.
         */
        protected function BeforeContent()
        {
            
        }
        /**
         * Renders the main content of the Web page. Designed for use by page developers.
         */
        protected function RenderContent()
        {
            
        }
        /**
         * Performs any necessary processing after the main content of the Web page. Designed for use by page developers.
         */
        protected function AfterContent()
        {
            
        }
		
        /**
         * This function is called before the content for a full page is generated. To generate a partial page, pass
         * "partial" in the query string.
         */
		protected function BeforeFullContent()
		{
			
		}
		/**
		 * This function is called after the content for a full page is generated. To generate a partial page, pass
		 * "partial" in the query string.
		 */
		protected function AfterFullContent()
		{
			
		}

		/**
		 * Determines if the WebPageVariable with the given name exists on this WebPage.
		 * @param string $name The name of the WebPageVariable to search for.
		 * @return boolean True if the WebPageVariable with the given name exists on this WebPage; false otherwise.
		 * @see WebPageVariable
		 */
		public function HasPathVariable($name)
		{
			return ($this->GetPathVariable($name) != null);
		}

		/**
		 * Retrieves the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable to return.
		 * @return WebPageVariable|NULL The WebPageVariable with the given name, or NULL if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetPathVariable($name)
		{
			foreach ($this->PathVariables as $variable)
			{
				if ($variable->Name == $name) return $variable;
			}
			return null;
		}
		/**
		 * Retrieves the string value for the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable whose value is to be returned.
		 * @return string The value of the WebPageVariable with the given name, or $defaultValue (default: null) if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetPathVariableValue($name, $defaultValue = null)
		{
			$variable = $this->GetPathVariable($name);
			if ($variable == null) return $defaultValue;
			return $variable->Value;
		}
		
		/**
		 * Retrieves the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable to return. 
		 * @return WebPageVariable|NULL The WebPageVariable with the given name, or NULL if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetClientVariable($name)
		{
			foreach ($this->ClientVariables as $variable)
			{
				if ($variable->Name == $name) return $variable;
			}
			return null;
		}
		/**
		 * Retrieves the string value for the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable whose value is to be returned. 
		 * @return string The value of the WebPageVariable with the given name, or the empty string ("") if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetClientVariableValue($name)
		{
			$variable = $this->GetClientVariable($name);
			if ($variable == null) return null;
			return $variable->Value;
		}

		/**
		 * Retrieves the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable to return.
		 * @return WebPageVariable|NULL The WebPageVariable with the given name, or NULL if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetServerVariable($name)
		{
			foreach ($this->ServerVariables as $variable)
			{
				if ($variable->Name == $name) return $variable;
			}
			return null;
		}
		/**
		 * Retrieves the string value for the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable whose value is to be returned.
		 * @return string The value of the WebPageVariable with the given name, or the empty string ("") if no WebPageVariable with the given name is defined for this WebPage.
		 */
		public function GetServerVariableValue($name)
		{
			$variable = $this->GetServerVariable($name);
			if ($variable == null) return null;
			return $variable->Value;
		}
		
		/**
		 * Updates the WebPageVariable with the given name associated with this WebPage.
		 * @param string $name The name of the WebPageVariable to update.
		 * @param string $value The value to set for the specified WebPageVariable.
		 * @param boolean $autoDeclare True if the variable should be created if it doesn't exist; false if the function should fail.
		 * @return boolean True if the variable was updated successfully; false otherwise.
		 */
		public function SetVariableValue($name, $value, $autoDeclare = false)
		{
			$variable = $this->GetVariable($name);
			if ($variable == null)
			{
				if (!$autoDeclare) return false;
				
				$variable = new WebPageVariable($name, $value, true);
				$this->Variables[] = $variable;
				return true;
			}
			$variable->Value = $value;
			return true;
		}
		/**
		 * Determines if a WebPageVariable with the given name is defined on this WebPage.
		 * @param string $name The name of the WebPageVariable to search for.
		 * @return boolean True if a WebPageVariable with the given name is defined on this WebPage; false if not.
		 */
		public function IsVariableDefined($name)
		{
			$variable = $this->GetVariable($name);
			if ($variable == null) return false;
			return true;
		}
		/**
		 * Determines if a WebPageVariable with the given name has a value (is not null) on this WebPage.
		 * @param string $name The name of the WebPageVariable to search for.
		 * @return boolean True if a WebPageVariable with the given name is defined and not null on this WebPage; false if either the variable is not defined or the variable is defined but does not have a value.
		 */
		public function IsClientVariableSet($name)
		{
			$variable = $this->GetClientVariable($name);
			if ($variable == null) return false;
			return ($variable->IsSet == "true");
		}

		/**
		 * Determines if a WebPageVariable with the given name has a value (is not null) on this WebPage.
		 * @param string $name The name of the WebPageVariable to search for.
		 * @return boolean True if a WebPageVariable with the given name is defined and not null on this WebPage; false if either the variable is not defined or the variable is defined but does not have a value.
		 */
		public function IsServerVariableSet($name)
		{
			$variable = $this->GetServerVariable($name);
			if ($variable == null) return false;
			return ($variable->IsSet == "true");
		}
		
		/**
		 * Renders the specified WebControl as a JSON control.
		 * @param WebControl $ctl
		 */
		private function RenderJSONControl($ctl)
		{
			$ary = array
			(
				"ClassName" => get_class($ctl)
			);
			
			$count = count($ctl->Attributes);
			if ($count > 0)
			{
				$attrs = array();
				for ($i = 0; $i < $count; $i++)
				{
					$attr = $ctl->Attributes[$i];
					$attrs[] = array
					(
						"Name" => $attr->Name,
						"Value" => $attr->Value
					);
				}
				$ary["Attributes"] = $attrs;
			}
			
			$reflect = new \ReflectionClass($ctl);
			$props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
			$count = count($props);
			
			if ($count > 0)
			{
				$attrs = array();
				$props2 = array();
				foreach($props as $prop)
				{
					$key = $prop->getName();
					$value = $prop->getValue($ctl);
					if ($value == null) continue;
					
					if (is_array($value))
					{
						foreach ($value as $val)
						{
							if (is_object($val))
							{
								$val->ClassName = get_class($val);
							}
						}
					}
					
					$props2[$key] = $value;
				}
				$count = count($props2);
				
				if ($count > 0)
				{
					foreach ($props2 as $key => $value)
					{
						if ($key == "ParentObject")
						{
							$key = "ParentObjectID";
							$value = $value->ID;
						}
						$attrs[] = array
						(
							"Name" => $key,
							"Value" => $value
						);
					}
					$ary["Properties"] = $attrs;
				}
			}
			return $ary;
		}
		
        public function Render()
        {
        	if (!$this->Initialize())
        	{
        		trigger_error("Could not initialize the WebPage");
        		return;
        	}
        	
        	switch (System::$WebPageFormat)
        	{
        		case WebPageFormat::JavaScript:
        		{
        			$filenames = array();
        			
        			$filename = System::$CurrentPage->PhysicalFileName . ".js";
        			if (file_exists($filename)) $filenames[] = $filename;
        			
        			$mp = System::$CurrentPage->MasterPage;
        			while ($mp != null)
        			{
        				if (file_exists($mp->PhysicalFileName . ".js"))
        				{
        					array_unshift($filenames, $mp->PhysicalFileName . ".js");
        				}
        				$mp = $mp->MasterPage;
        			}
        			
        			if (count($filenames) > 0)
        			{
        				header("HTTP/1.1 200 OK");
        				header("Content-Type: text/javascript");
        				
        				foreach ($filenames as $filen)
        				{
        					readfile($filen);
        					echo("\r\n\r\n");
        				}
        			}
        			else
        			{
        				header("HTTP/1.1 404 Not Found");
        			}
        			return;
        		}
        		case WebPageFormat::JSON:
        		{
        			header("Content-Type: application/json; charset=utf-8");
        			
        			$ary = array
        			(
        				"FileName" => $this->FileName
        			);
        			
        			$count = count($this->Controls);
        			if ($count > 0)
        			{
        				$ctls = array();
	        			for($i = 0; $i < $count; $i++)
	        			{
	        				$ctl = $this->Controls[$i];
	        				$ctls[] = $this->RenderJSONControl($ctl);
	        			}
	        			$ary["Controls"] = $ctls;
        			}
        			
        			echo(json_encode($ary));
        			return;
        		}
        		case WebPageFormat::XML:
        		{
        			header("Content-Type: application/xml; charset=utf-8");
        			echo ("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
        			
        			$tagPage = new HTMLControl("Page");
        			$tagPage->Attributes[] = new WebControlAttribute("FileName", $this->FileName);
        			
        			if (count($this->Controls) > 0)
        			{
        				$tagControls = new HTMLControl("Controls");
        				foreach ($this->Controls as $ctl)
        				{
        					$tagControl = new HTMLControl("Control");
        					$tagControl->Attributes[] = new WebControlAttribute("ID", $ctl->ID);
        					$tagControl->Attributes[] = new WebControlAttribute("ClassName", get_class($ctl));
        					
        					if (count($ctl->Attributes) > 0)
        					{
        						$tagAttributes = new HTMLControl("Attributes");
	        					foreach ($ctl->Attributes as $attr)
	        					{
	        						$tagAttribute = new HTMLControl("Attribute");
	        						$tagAttribute->Attributes[] = new WebControlAttribute("Name", $attr->Name);
	        						$tagAttribute->Attributes[] = new WebControlAttribute("Value", $attr->Value);
	        						$tagAttributes[] = $tagAttribute;
	        					}
	        					$tagControl->Controls[] = $tagAttributes;
        					}
        					
        					$tagControls->Controls[] = $tagControl;
        				}
        				$tagPage->Controls[] = $tagControls;
        			}
        			
        			$tagPage->Render();
        			return;
        		}
        	}
			
        	header("Content-Type: text/html; charset=utf-8");
			if (!$this->IsPartial)
			{
        		
				if (!$this->UseCompatibleRenderingMode)
				{
					echo("<!DOCTYPE html>\r\n");
				}
				
				$tagHTML = new HTMLControl();
				$tagHTML->TagName = "html";
				
				$tagHEAD = new HTMLControl();
				$tagHEAD->TagName = "head";
				
				if ($this->Title != "")
				{
					$tagTITLE = new HTMLControl();
					$tagTITLE->TagName = "title";
					$tagTITLE->InnerHTML = $this->Title;
					$tagHEAD->Controls[] = $tagTITLE;
				}
				
				// ========== BEGIN: Metadata ==========
				$items = array();
				$items[] = new WebPageMetadata("Content-Type", "text/html; charset=utf-8", true);
				$items[] = new WebPageMetadata("viewport", "width=device-width,minimum-scale=1.0");
				$items[] = new WebPageMetadata("X-UA-Compatible", "IE=edge", WebPageMetadataType::HTTPEquivalent);
				
				if ($this->MasterPage != null)
				{
					foreach ($this->MasterPage->Metadata as $item)
					{
						$items[] = $item;
					}
				}
				foreach ($this->Metadata as $item)
				{
					$items[] = $item;
				}
				foreach ($items as $item)
				{
					$tagHEAD->Controls[] = WebPage::CreateMetaTag($item);
				}
				// ========== END: Metadata ==========
				
				// ========== BEGIN: Resource Links ==========
				$items = array();
				$items[] = new WebResourceLink($this->GetCanonicalFileName(), "canonical");
				
				if ($this->MasterPage != null)
				{
					foreach ($this->MasterPage->ResourceLinks as $item)
					{
						$items[] = $item;
					}
				}
				foreach ($this->ResourceLinks as $item)
				{
					$items[] = $item;
				}
				foreach ($items as $item)
				{
					$tagHEAD->Controls[] = WebPage::CreateResourceLinkTag($item);
				}
				// ========== END: Resource Links ==========

				// ========== BEGIN: StyleSheets ==========
				$items = array();
				$items[] = new WebStyleSheet("$(Configuration:System.StaticPath)/StyleSheets/Main.css");
				
				if ($this->MasterPage != null)
				{
					foreach ($this->MasterPage->StyleSheets as $item)
					{
						$items[] = $item;
					}
				}
				foreach ($this->StyleSheets as $item)
				{
					$items[] = $item;
				}
				foreach ($items as $item)
				{
					$tagHEAD->Controls[] = WebPage::CreateStyleSheetTag($item);
				}
				// ========== END: StyleSheets ==========
				
				// ========== BEGIN: Scripts ==========
				$items = array();
				
				// Bring in Phast first
				$items[] = WebScript::FromFile("$(Configuration:System.StaticPath)/Scripts/System.js.php", "text/javascript");
				$items[] = WebScript::FromContent("System.IsTenantedHostingEnabled = function() { return " . (System::$EnableTenantedHosting ? "true" : "false") . "; }; System.GetTenantName = function() { return \"" . System::GetTenantName() . "\"; };", "text/javascript");
				
				$filename = System::$CurrentPage->PhysicalFileName . ".js";
				if (file_exists($filename))
				{
					$items[] = new WebScript(System::GetRequestURL() . ".js");
				}
				else
				{
					// echo("<!-- could not find script file '" . $filename . "' -->");
				}
				
				// Update the Phast application base path
				$item = new WebScript();
				$item->ContentType = "text/javascript";
				$item->Content = "System.BasePath = \"" . System::GetConfigurationValue("Application.BasePath") . "\";";
				$items[] = $item;
				
				if ($this->MasterPage != null)
				{
					foreach ($this->MasterPage->Scripts as $item)
					{
						$items[] = $item;
					}
				}
				foreach ($this->Scripts as $item)
				{
					$items[] = $item;
				}
				foreach ($items as $item)
				{
					$tagHEAD->Controls[] = WebPage::CreateScriptTag($item);
				}
				// ========== END: Scripts ==========
				
				// ========== BEGIN: OpenGraph Support ==========
				if ($this->OpenGraph->Enabled)
				{
					$og_title = $this->OpenGraph->Title;
					$og_type = "other";
					$og_url = $this->OpenGraph->URL;
					$og_site_name = $this->OpenGraph->Title;
					$og_image = $this->OpenGraph->ImageURL;
					$og_description = $this->OpenGraph->Description;
					
					if ($og_title != null) $og_title = $this->Title;
					
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:title", $og_title, WebPageMetadataType::Property));
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:type", $og_type, WebPageMetadataType::Property));
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:url", $og_url, WebPageMetadataType::Property));
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:site_name", $og_site_name, WebPageMetadataType::Property));
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:image", $og_image, WebPageMetadataType::Property));
					$tagHEAD->Controls[] = WebPage::CreateMetaTag(new WebPageMetadata("og:description", $og_description, WebPageMetadataType::Property));
				}
				// ========== END: OpenGraph Support ==========
				
				$tagHTML->Controls[] = $tagHEAD;
				
				$tagBODY = new HTMLControl();
				$tagBODY->TagName = "body";
				
				$divThrobber = new HTMLControl("div");
				$divThrobber->CssClass = "Throbber";
				$tagBODY->Controls[] = $divThrobber;
				
				$classList = array();
				if (is_array($this->ClassList))
				{
					foreach ($this->ClassList as $item)
					{
						$classList[] = $item;
					}
				}
				
				$tagBODY->ClassList = $classList;
				$tagBODY->StyleRules = $this->Styles;
				
				$this->BeforeClientVariablesInitialize();
				if (count($this->ClientVariables) > 0)
				{
					$form = new Form();
					$form->ClientID = "WebPageForm";
					$form->Method = FormMethod::Post;
					foreach ($this->ClientVariables as $variable)
					{
						$input = new HTMLControlInput();
						$input->Type = HTMLControlInputType::Hidden;
						$input->ClientID = "WebPageVariable_" . $variable->Name . "_Value";
						$input->Name = "WebPageVariable_" . $variable->Name . "_Value";
						if (isset($_POST["WebPageVariable_" . $variable->Name . "_Value"]))
						{
							$variable->Value = $_POST["WebPageVariable_" . $variable->Name . "_Value"];
						}
						$input->Value = $variable->Value;
						$form->Controls[] = $input;
						
						$input = new HTMLControlInput();
						$input->Type = HTMLControlInputType::Hidden;
						$input->ClientID = "WebPageVariable_" . $variable->Name . "_IsSet";
						$input->Name = "WebPageVariable_" . $variable->Name . "_IsSet";
						if (isset($_POST["WebPageVariable_" . $variable->Name . "_IsSet"]))
						{
							$variable->IsSet = $_POST["WebPageVariable_" . $variable->Name . "_IsSet"];
						}
						$input->Value = (($variable->IsSet == "true") ? "true" : "false");
						$form->Controls[] = $input;
					}
					$tagBODY->Controls[] = $form;
				}
				$this->AfterClientVariablesInitialize();
				
				$re = new RenderingEventArgs(RenderMode::Complete);
				$this->OnRendering($re);
				if ($re->Cancel) return;
			}
			else
			{
				$re = new RenderingEventArgs(RenderMode::Partial);
				$this->OnRendering($re);
				if ($re->Cancel) return;
			}
			$re = new RenderingEventArgs(RenderMode::Any);
			$this->OnRendering($re);
			if ($re->Cancel) return;
			
			if (is_callable($this->Content) && count($this->Controls) == 0)
			{
				$tagBODY->Content = $this->Content;
			}
			else if (!is_callable($this->Content) && count($this->Controls) > 0)
			{
				$controls = $this->Controls;
				if ($this->MasterPage != null)
				{
					$controls = $this->MergeMasterPageControls($this->MasterPage->Controls);
				}
				
				foreach ($controls as $ctl)
				{
					$tagBODY->Controls[] = $ctl;
				}
			}
			$tagHTML->Controls[] = $tagBODY;
			$tagHTML->Render();

			$this->OnRendered(new RenderedEventArgs(RenderMode::Any));
			$renderedEventArgs = null;
			if ($this->IsPartial)
			{
				$renderedEventArgs = new RenderedEventArgs(RenderMode::Partial);
			}
			else
			{
				$renderedEventArgs = new RenderedEventArgs(RenderMode::Complete);
			}
			$this->OnRendered($renderedEventArgs);
			
			if ($this->ClassReference != null)
			{
				if (method_exists($this->ClassReference, "OnRendered"))
				{
					$this->ClassReference->OnRendered($renderedEventArgs);
				}
			}
        }
        
        private function MergeMasterPageControls($controls = null)
        {
        	if ($controls == null) $controls = array();
        	$newControls = array();
        	if ($this->MasterPage != null)
        	{
        		foreach ($controls as $control)
        		{
        			$ctl = clone $control;
        			
        			if (get_class($ctl) == "Phast\\WebControls\\SectionPlaceholder")
        			{
        				$pageControls = $this->Controls;
        				foreach ($pageControls as $pageControl)
        				{
        					$pageCtl = clone $pageControl;
        					if (get_class($pageCtl) != "Phast\\WebControls\\Section") continue;
        					
        					if ($pageCtl->PlaceholderID != $ctl->ID) continue;
        					$newControls[] = $pageCtl;
        				}
        			}
        			else
        			{
        				$ctl->Controls = $this->MergeMasterPageControls($ctl->Controls);
        				$newControls[] = $ctl;
        			}
        		}
        	}
        	return $newControls;
        }
		
		protected function BeforeClientVariablesInitialize()
		{
		}
		protected function AfterClientVariablesInitialize()
		{
		}
    }
?>
