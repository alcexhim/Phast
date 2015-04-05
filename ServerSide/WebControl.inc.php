<?php
	namespace Phast;
	
    class WebControl
    {
    	/**
    	 * The unique identifier of this WebControl on a WebPage.
    	 * @var string
    	 */
		public $ID;
		/**
		 * The unique identifier of this WebControl on the client rendered WebPage.
		 * @var string
		 */
		public $ClientID;
		/**
		 * Determines how ClientIDs are generated for WebControls.
		 * @var WebControlClientIDMode
		 */
		public $ClientIDMode;
		
		public $Content;
		
		/**
		 * True if the WebControl should render its content to the output HTML; false otherwise.
		 * @var boolean
		 */
		public $EnableRender;
		
		public $ExtraData;
		
		public $Controls;
		public $HasContent;
		
		public $ParentObject;
		
		public $Top;
		public $Left;
		public $Width;
		public $Height;
		
		public $MaximumWidth;
		public $MaximumHeight;
		
		public $Visible;
		
		/**
		 * The horizontal alignment of this WebControl.
		 * @var HorizontalAlignment
		 */
		public $HorizontalAlignment;
		/**
		 * The vertical alignment of this WebControl.
		 * @var VerticalAlignment
		 */
		public $VerticalAlignment;
		
		/**
		 * The CssClass to assign to this WebControl. CssClass is assigned after all classes defined in ClassList have been assigned.
		 * @var string
		 * @see WebControl::$ClassList
		 */
		public $CssClass;
		/**
		 * Array of CssClasses to assign to this WebControl.
		 * @var string[]
		 * @see WebControl::$CssClass
		 */
		public $ClassList;
		
		public $TagName;
		public $Attributes;
		public $StyleRules;
		
		public $ToolTipTitle;
		public $ToolTipText;
		
		/**
		 * True if child tags should be parsed as control properties; false if they should be parsed as child controls.
		 * @var boolean
		 */
		public $ParseChildElements;
		
		public function GetAllControls()
		{
			return $this->Controls;
		}
		
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
			return null;
		}
		
		public function FindParentPage()
		{
			$parent = $this->ParentObject;
			while ($parent != null)
			{
				if (get_class($parent) == "Phast\\Parser\\Page" || get_class($parent) == "Phast\\Parser\\MasterPage")
				{
					return $parent;
				}
				$parent = $parent->ParentObject;
			}
			return null;
		}
		
		/**
		 * Generates a random string of the specified length using the characters specified in the string valid_chars.
		 * @param string $valid_chars Set of characters used to build the resulting random string.
		 * @param int $length The length of the resulting random string.
		 * @return string The random string of the specified length using the specified character set.
		 */
		private static function GenerateRandomString($valid_chars, $length)
		{
			// start with an empty random string
			$random_string = "";

			// count the number of chars in the valid chars string so we know how many choices we have
			$num_valid_chars = strlen($valid_chars);

			// repeat the steps until we've created a string of the right length
			for ($i = 0; $i < $length; $i++)
			{
				// pick a random number from 1 up to the number of valid chars
				$random_pick = mt_rand(1, $num_valid_chars);

				// take the random character out of the string of valid chars
				// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
				$random_char = $valid_chars[$random_pick-1];

				// add the randomly-chosen char onto the end of our string so far
				$random_string .= $random_char;
			}

			// return our finished random string
			return $random_string;
		}
		
		public function __construct()
		{
			$this->Visible = true;
			$this->HorizontalAlignment = HorizontalAlignment::Inherit;
			$this->VerticalAlignment = VerticalAlignment::Inherit;
			
			$this->Controls = array();
			$this->EnableRender = true;
			
			$this->HasContent = true;
			
			$this->TagName = null;
			$this->ClassList = array();
			$this->Attributes = array();
			$this->StyleRules = array();
			
			$this->ParseChildElements = false;
			
			$this->ClientIDMode = WebControlClientIDMode::None;
		}
		
		/**
		 * Retrieves a ClientProperty associated with this control via a browser cookie.
		 * @param string $name The name of the property to retrieve.
		 * @param string $defaultValue The value to retrieve if the ClientProperty has not been set.
		 * @return string The value of the property with the given name, or defaultValue if the property has not been set.
		 */
		public function GetClientProperty($name, $defaultValue = null)
		{
			if (!isset($_COOKIE[$this->ID . "__ClientProperty_" . $name])) return $defaultValue;
			return $_COOKIE[$this->ID . "__ClientProperty_" . $name];
		}
		/**
		 * Updates a ClientProperty associated with this control via a browser cookie.
		 * @param string $name The name of the property to update.
		 * @param string $value The value with which to update the property.
		 * @param string $expires Expiration data for the cookie associated with the ClientProperty.
		 */
		public function SetClientProperty($name, $value, $expires = null)
		{
			setcookie($this->ID . "__ClientProperty_" . $name, $value, $expires);
		}
		
		private $Initialized;
		
		/**
		 * Initializes this control, calling the OnInitialize() function and initializing any child
		 * controls.
		 */
        public function Initialize()
        {
        	$id = null;
        	$clientid = null;
        	
        	if ($this->ClientIDMode == WebControlClientIDMode::Automatic && $this->ID == null)
        	{
        		$parent = $this->ParentObject;
        		$clientid = "";
        		
        		while ($parent != null)
        		{
        			$clientid = $parent->ID . "_" . $clientid;
        			$parent = $parent->ParentObject;
        		}
        		
        		$id = "WFX" . WebControl::GenerateRandomString("ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890", 10);
        		$clientid .= $id;
        	}
        	if ($id != null) $this->ID = $id;
        	if ($clientid != null) $this->ClientID = $clientid;
        	
            $this->OnInitialize();
			if (is_array($this->Controls))
			{
				foreach ($this->Controls as $control)
				{
					$control->ParentObject = $this;
					if (method_exists($control, "Initialize"))
					{
						$control->Initialize();
					}
				}
			}
			else
			{
				trigger_error("Controls is not array in " . get_class($this) . " ; did you forget to call parent::__construct() ?");
			}
			$this->Initialized = true;
        }
		
		protected function OnInitialize()
		{
			
		}
		
        protected function BeforeContent()
        {
            
        }
        protected function RenderContent()
        {
            
        }
        protected function AfterContent()
        {
            
        }
		
        /**
         * Renders an HTML beginning tag with the specified parameters.
         * @param string $tagName The name of the tag to open.
         * @param array $namedParameters Associative array that specifies ClassNames, Attributes, and StyleRules to render with the beginning tag.  
         */
		public static function BeginTag($tagName, $namedParameters)
		{
			echo("<" . $tagName);
			
			if (is_array($namedParameters))
			{
				if (isset($namedParameters["ClassNames"])) $classNames = $namedParameters["ClassNames"];
				if (isset($namedParameters["Attributes"])) $attributes = $namedParameters["Attributes"];
				if (isset($namedParameters["StyleRules"])) $styleRules = $namedParameters["StyleRules"];
			}
			
			if (!isset($classNames) || !is_array($classNames)) $classNames = array();
			if (!isset($attributes) ||!is_array($attributes)) $attributes = array();
			if (!isset($styleRules) ||!is_array($styleRules)) $styleRules = array();
			
			$count = count($classNames);
			if ($count > 0)
			{
				echo(" class=\"");
				for ($i = 0; $i < $count; $i++)
				{
					echo($classNames[$i]);
					if ($i < $count - 1) echo(" ");
				}
				echo("\"");
			}
			
			$count = count($styleRules);
			if ($count > 0)
			{
				echo(" style=\"");
				for ($i = 0; $i < $count; $i++)
				{
					$item = $styleRules[$i];
					echo($item->Name . ": " . $item->Value);
					if ($i < $count - 1) echo("; ");
				}
				echo("\"");
			}
			
			$count = count($attributes);
			if ($count > 0)
			{
				echo(" ");
				for ($i = 0; $i < $count; $i++)
				{
					$item = $attributes[$i];
					echo($item->Name . "=\"" . $item->Value . "\"");
					if ($i < $count - 1) echo(" ");
				}
			}
			echo(">");
		}
		/**
		 * Renders an HTML ending tag with the given tag name.
		 * @param string $tagName The name of the tag to close.
		 */
		public static function EndTag($tagName)
		{
			echo("</" . $tagName . ">");
		}
		
		/**
		 * Renders the beginning tag of this WebControl, including any attribute, CSS class, or style
		 * information specified by the control author or the caller.
		 */
		protected function RenderBeginTag()
		{
			if ($this->TagName != "")
			{
				echo("<" . $this->TagName);
				
				$styleAttributeContent = "";
				$classAttributeContent = "";
				
				$count = count($this->Attributes);
				if ($count > 0)
				{
					$found = false;
					foreach ($this->Attributes as $attr)
					{
						if (!(strtolower($attr->Name) == "style" || strtolower($attr->Name) == "class"))
						{
							$found = true;
							break;
						}
					}
					if ($found) echo(" ");
					$i = 0;
					foreach ($this->Attributes as $attr)
					{
						if (strtolower($attr->Name) == "style")
						{
							if (!StringMethods::EndsWith($attr->Value, ";"))
							{
								$styleAttributeContent .= $attr->Value . "; ";
							}
							else
							{
								$styleAttributeContent .= $attr->Value;
							}
						}
						else if (strtolower($attr->Name) == "class")
						{
							$classAttributeContent .= $attr->Value;
						}
						else if (strtolower($attr->Name) == "id")
						{
							$this->ID = $attr->Value;
						}
						else
						{
							echo($attr->Name);
							echo("=\"");
							echo($attr->Value);
							echo("\"");
							if ($i < $count - 1) echo(" ");
						}
						$i++;
					}
				}
				
				$styleRules = $this->StyleRules;
				if (!$this->Visible)
				{
					$styleRules[] = new WebStyleSheetRule("display", "none");
				}
				if ($this->Width != null)
				{
					$styleRules[] = new WebStyleSheetRule("width", $this->Width);
				}
				if ($this->Height != null)
				{
					$styleRules[] = new WebStyleSheetRule("height", $this->Height);
				}
				if ($this->MaximumWidth != null)
				{
					$styleRules[] = new WebStyleSheetRule("max-width", $this->MaximumWidth);
				}
				if ($this->MaximumHeight != null)
				{
					$styleRules[] = new WebStyleSheetRule("max-height", $this->MaximumHeight);
				}
				
				if (count($styleRules) > 0 || $styleAttributeContent != "")
				{
					echo(" style=\"");
					echo($styleAttributeContent);
					$count = count($styleRules);
					$i = 0;
					foreach ($styleRules as $rule)
					{
						echo($rule->Name);
						echo(": ");
						echo($rule->Value);
						echo(";");
						if ($i < $count - 1) echo(" ");
						$i++;
					}
					echo("\"");
				}
				if ($this->CssClass != "")
				{
					if ($classAttributeContent != "") $classAttributeContent .= " ";
					$classAttributeContent .= $this->CssClass;
				}
				if (count($this->ClassList) > 0)
				{
					if ($classAttributeContent != "") $classAttributeContent .= " ";
					$count = count($this->ClassList);
					for ($i = 0; $i < $count; $i++)
					{
						$classAttributeContent .= $this->ClassList[$i];
						if ($i < $count - 1) $classAttributeContent .= " ";
					}
				}
				
				if ($classAttributeContent != "")
				{
					echo(" class=\"" . $classAttributeContent . "\"");
				}
				
				if ($this->ClientID != null)
				{
					echo(" id=\"" . $this->ClientID . "\"");
				}
				else if ($this->ID != null)
				{
					echo(" id=\"" . $this->ID . "\"");
				}
				
				if ($this->ToolTipTitle != null) echo(" data-tooltip-title=\"" . $this->ToolTipTitle . "\"");
				if ($this->ToolTipText != null) echo(" data-tooltip-content=\"" . $this->ToolTipText . "\"");
				
				if (!$this->HasContent) echo(" /");
				echo(">");
			}
		}
		/**
		 * Renders the ending tag of this WebControl.
		 */
		protected function RenderEndTag()
		{
			if ($this->TagName != "" && $this->HasContent)
			{
				echo("</" . $this->TagName . ">");
			}
		}
		
		/**
		 * Renders the beginning tag of this WebControl, followed by any leading content specified by
		 * the control author.
		 */
		public function BeginContent()
		{
        	if ($this->EnableRender !== true) return;
        	
			$this->RenderBeginTag();
            $this->BeforeContent();
		}
		/**
		 * Renders any trailing content specified by the control author before the ending tag of this
		 * WebControl, followed by the ending tag itself.
		 */
		public function EndContent()
		{
        	if ($this->EnableRender !== true) return;
        	
            $this->AfterContent();
			$this->RenderEndTag();
		}
		
		/**
		 * Creates the child controls for this WebControl.
		 */
		protected function CreateControl()
		{
		}
        
		/**
		 * Renders this WebControl and any child controls.
		 */
        public function Render()
        {
        	if ($this->EnableRender !== true) return;
        	
        	if (!$this->Initialized) $this->Initialize();
        	$this->CreateControl();
        	
            $this->BeginContent();
			if (count($this->Controls) > 0)
			{
				foreach ($this->Controls as $control)
				{
					$control->Render();
				}
			}
			else
			{
				if (is_callable($this->Content))
				{
					call_user_func($this->Content, $this);
				}
				else if (is_string($this->Content))
				{
					echo($this->Content);
				}
				else
				{
					$this->RenderContent();
				}
			}
            $this->EndContent();
        }
    }
?>