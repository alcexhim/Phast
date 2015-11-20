<?php
	namespace UniversalEditor\ObjectModels\Markup;
	
	class MarkupAttribute
	{
		public $Prefix;
		public $Name;
		public $Value;
		
		public function GetFullName()
		{
			if ($this->Prefix != "")
			{
				return $this->Prefix . ":" . $this->Name;
			}
			else
			{
				return $this->Name;
			}
		}
		public function SetFullName($name)
		{
			$names = explode(":", $name);
			if (count($names) > 1)
			{
				$this->Prefix = $names[0];
				$this->Name = $names[1];
			}
			else if (count($names) == 1)
			{
				$this->Prefix = "";
				$this->Name = $names[0];
			}
		}
		
		public function __construct($name, $value, $prefix = null)
		{
			if ($prefix == null)
			{
				$this->SetFullName($name);
			}
			else
			{
				$this->Prefix = $prefix;
				$this->Name = $name;
			}
			$this->Value = $value;
		}
	}
	abstract class MarkupElement
	{
		public abstract function GetOuterMarkup();
		public abstract function GetInnerMarkup();
		
		public static function FromArray($array)
		{
			if (!isset($array["Type"])) return null;
			
			switch ($array["Type"])
			{
				case "MarkupTagElement":
				{
					$element = new MarkupTagElement();
					$element->Name = $array["Name"];
					
					if (isset($array["Attributes"]))
					{
						$attributes = $array["Attributes"];
						foreach ($attributes as $name => $value)
						{
							$element->Attributes[] = new MarkupAttribute($name, $value);
						}
					}
					if (isset($array["Elements"]))
					{
						$elements = $array["Elements"];
						foreach ($elements as $elem)
						{
							$element->Elements[] = MarkupElement::FromArray($elem);
						}
					}
					if (isset($array["Value"]))
					{
						$element->Value = $array["Value"];
					}
					
					return $element;
				}
				case "MarkupLiteralElement":
				{
					$element = new MarkupLiteralElement();
					$element->Value = $array["Value"];
					return $element;
				}
			}
			return null;
		}
	}
	class MarkupLiteralElement extends MarkupElement
	{
		public $Value;
		
		public function GetOuterMarkup()
		{
			return $this->GetInnerMarkup();
		}
		public function GetInnerMarkup()
		{
			return $this->Value;
		}
	}
	class MarkupTagElement extends MarkupElement
	{
		/**
		 * The name of this MarkupTagElement.
		 * @var string
		 */
		public $Name;
		
		/**
		 * The MarkupAttributes associated with this MarkupTagElement.
		 * @var MarkupAttribute[]
		 */
		public $Attributes;
		/**
		 * The child MarkupElements of this MarkupTagElement.
		 * @var MarkupTagElement[]
		 */
		public $Elements;
		
		public function __construct()
		{
			$this->Attributes = array();
			$this->Elements = array();
		}
		
		public function GetOuterMarkup()
		{
			$str = "<" . $this->Name;
			if (count($this->Attributes) != 0)
			{
				foreach ($this->Attributes as $attr)
				{
					$str .= " " . $attr->Name . "=\"" . $attr->Value . "\"";
				}
			}
			if (count($this->Elements) == 0)
			{
				$str .= " />";
			}
			else
			{
				$str .= ">" . $this->GetInnerMarkup() . "</" . $this->Name . ">";
			}
			return $str;
		}
		public function GetInnerMarkup()
		{
			$str = "";
			foreach ($this->Elements as $elem)
			{
				$str .= $elem->GetOuterMarkup();
			}
			return $str;
		}
		
		public function GetAttribute($name, $index = 0)
		{
			$i = 0;
			$last = null;
			foreach ($this->Attributes as $attribute)
			{
				if ($attribute->Name == $name)
				{
					$last = $attribute;
					$i++;
					if ($i == $index) return $attribute;
				}
			}
			return $last;
		}
		public function GetAttributes()
		{
			return $this->Attributes;
		}
		
		public function HasAttribute($name)
		{
			return ($this->GetAttribute($name) != null);
		}

		/**
		 * Gets a MarkupElement with the specified name.
		 * @param string $name The name of the element to retrieve.
		 * @param number $index The index of the element to retrieve if there is more than one element with the specified name.
		 * @return MarkupTagElement|MarkupLiteralElement|MarkupElement|null
		 */
		public function GetElement($name, $index = 0)
		{
			$i = 0;
			$last = null;
			foreach ($this->Elements as $element)
			{
				if (get_class($element) == "UniversalEditor\\ObjectModels\\Markup\\MarkupLiteralElement") continue;
				
				if ($element->Name == $name)
				{
					$last = $element;
					$i++;
					if ($i == $index) return $element;
				}
			}
			return $last;
		}
		public function GetElements()
		{
			return $this->Elements;
		}
	}
	
	class MarkupObjectModel
	{
		public $Elements;
		
		/**
		 * Gets a MarkupElement with the specified name.
		 * @param string $name The name of the element to retrieve.
		 * @param number $index The index of the element to retrieve if there is more than one element with the specified name.
		 * @return MarkupTagElement|MarkupLiteralElement|MarkupElement
		 */
		public function GetElement($name, $index = 0)
		{
			$i = 0;
			$last = null;
			foreach ($this->Elements as $element)
			{
				if ($element->Name == $name)
				{
					$last = $element;
					$i++;
					if ($i == $index) return $element;
				}
			}
			return $last;
		}
		public function GetElements()
		{
			return $this->Elements;
		}
		
		public static function FromFile($filename)
		{
			$parser = new XMLParser();
			$markup = $parser->LoadFile($filename);
			return $markup;
		}
		
		public function __construct()
		{
			$this->Elements = array();
		}
		public static function FromArray($array)
		{
			$markup = new MarkupObjectModel();
			$markup->LoadArray($array);
			return $markup;
		}
		public function LoadArray($array)
		{
			$count = count($array);
			for ($i = 0; $i < $count; $i++)
			{
				$this->Elements[] = MarkupElement::FromArray($array[$i]);
			}
		}
	}
	
	class XMLParser
	{
		public $RemoveWhitespace;
		
		private $mvarOutput;
		var $resParser;
		var $strXmlData;
		
		public function __construct()
		{
			$this->RemoveWhitespace = true;
		}
		
		/**
		 * Loads an XML file and returns the MarkupObjectModel.
		 * @param string $filename
		 * @return MarkupObjectModel
		 */
		public function LoadFile($filename)
		{
			$file = fopen($filename, "r");
			$input = fread($file, filesize($filename));
			return $this->Load($input);
		}
		
		public function Load($input)
		{
			$this->mvarOutput = array();
			
			$input = html_entity_decode($input);
			
			$this->resParser = xml_parser_create ();
			xml_parser_set_option($this->resParser, XML_OPTION_CASE_FOLDING, 0);
			
			xml_set_object($this->resParser, $this);
			xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
			
			xml_set_character_data_handler($this->resParser, "tagData");
	   		
			$this->strXmlData = xml_parse($this->resParser, $input);
			if(!$this->strXmlData)
			{
				$message = xml_error_string(xml_get_error_code($this->resParser));
				$lineNumber = xml_get_current_line_number($this->resParser);
				die(sprintf("XML error: %s at line %d", $message, $lineNumber));
			}
						   
			xml_parser_free($this->resParser);
		   
			return MarkupObjectModel::FromArray($this->mvarOutput);
		}
		
		private function tagOpen($parser, $name, $attrs)
		{
			$tag = array("Type" => "MarkupTagElement", "Name" => $name, "Attributes" => $attrs);
			array_push($this->mvarOutput,$tag);
		}
	   
		private function tagData($parser, $value)
		{
			if ($this->RemoveWhitespace && trim($value) == "") return;
			$value = htmlentities($value);
			
			$tag = array("Type" => "MarkupLiteralElement", "Value" => $value);
			array_push($this->mvarOutput, $tag);
			$this->mvarOutput[count($this->mvarOutput) - 2]['Elements'][] = $this->mvarOutput[count($this->mvarOutput) - 1];
			array_pop($this->mvarOutput);
		}
	   
		function tagClosed($parser, $name)
		{
			$this->mvarOutput[count($this->mvarOutput) - 2]['Elements'][] = $this->mvarOutput[count($this->mvarOutput) - 1];
			array_pop($this->mvarOutput);
		}
	}
?>