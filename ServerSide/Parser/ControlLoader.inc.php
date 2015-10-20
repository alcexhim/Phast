<?php
	namespace Phast\Parser;

	use Phast\HTMLControl;
	use Phast\HTMLControls\Literal;

	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebPageMessage;
	use Phast\WebPageMessageSeverity;
	use Phast\System;

	use UniversalEditor\ObjectModels\Markup\MarkupElement;
	use UniversalEditor\ObjectModels\Markup\MarkupTagElement;
					
	class ControlLoader
	{
		public static $Messages;
		public static $Namespaces;
		
		/**
		 * Parses the children of the specified MarkupElement into the specified object. 
		 * @param MarkupTagElement $elem
		 * @param WebControl $obj
		 */
		public static function ParseChildren($elem, &$obj)
		{
			// our parent is a WebControl and we should parse its children as properties
			if (is_array($elem->Elements))
			{
				foreach ($elem->Elements as $elem1)
				{
					if (get_class($elem1) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
					if (!is_array($elem1->Elements))
					{
						trigger_error("\$elem1->Elements not array for tag '" . $elem1->Name . "'");
						continue;
					}
					
					foreach ($elem1->Elements as $elem2)
					{
						if (get_class($elem2) != "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement") continue;
						
						$i = stripos($elem2->Name, ":");
						if ($i === false)
						{
							$prefix = "";
							$name = $elem2->Name;
						}
						else
						{
							$prefix = substr($elem2->Name, 0, $i);
							$name = substr($elem2->Name, $i + 1);
						}
						
						if (isset(ControlLoader::$Namespaces[$prefix]) && ControlLoader::$Namespaces[$prefix] != "")
						{
							$realname = ControlLoader::$Namespaces[$prefix] . "\\" . $name;
						}
						else
						{
							$realname = $name;
						}
						
						if ($prefix == "")
						{
							// assume regular HTML control
							$obj1 = new HTMLControl($name);
							foreach ($elem2->Attributes as $att)
							{
								$obj1->Attributes[] = new WebControlAttribute($att->Name, $att->Value);
							}
							if ($elem2->GetInnerMarkup() == "")
							{
								// we have to make some compromises; AFAIK the SCRIPT tag is the only
								// tag that doesn't know how to properly handle close /> tag
								$obj1->HasContent = false;
							}
						}
						else
						{
							if (class_exists($realname))
							{
								$obj1 = new $realname();
							}
							else
							{
								ControlLoader::$Messages[] = new WebPageMessage("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")", WebPageMessageSeverity::Error);
								System::WriteErrorLog("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")");
								continue;
							}
						}
						
						ControlLoader::LoadAttributes($elem2, $obj1);
	
						if ($obj1->ParseChildElements)
						{
							ControlLoader::ParseChildren($elem2, $obj1);
						}
						else
						{
							if (is_array($elem2->Elements))
							{
								foreach ($elem2->Elements as $elem3)
								{
									ControlLoader::LoadControl($elem3, $obj1);
								}
							}
						}
	
						$obj->{$elem1->Name}[] = $obj1;
					}
				}
			}
		}
		public static function LoadAttributes($elem, &$obj)
		{
			if (is_array($elem->Attributes))
			{
				foreach ($elem->Attributes as $attr)
				{
					$obj->{$attr->Name} = $attr->Value;
				}
			}
		}
		
		public static function GetPHPXAttributes($elem)
		{
			$attrs = array();
			if (is_array($elem->Attributes))
			{
				foreach ($elem->Attributes as $attr)
				{
					$attrs[$attr->Name] = $attr->Value;
				}
			}
			if (count($elem->Elements) == 1 && get_class($elem->Elements[0]) == "UniversalEditor\\ObjectModels\\Markup\\MarkupLiteralElement")
			{
				$attrs["Content"] = $elem->Elements[0]->Value;
				$elem->Elements = array();
			}
			return $attrs;
		}
		public static function LoadPHPXControlAttributes($attrs, &$obj)
		{
			foreach ($obj->Attributes as $att)
			{
				foreach ($attrs as $name => $value)
				{
					if (is_string($att->Value) && stripos($att->Value, "\$(Control:" . $name . ")") !== false)
					{
						$att->Value = str_replace("\$(Control:" . $name . ")", $value, $att->Value);
					}
				}
			}
			
			foreach ($obj as $key => $val)
			{
				foreach ($attrs as $name => $value)
				{
					if (is_string($val) && stripos($val, "\$(Control:" . $name . ")") !== false)
					{
						$obj->{$key} = str_replace("\$(Control:" . $name . ")", $value, $val);
					}
				}
			}
			
			foreach ($obj->Controls as $ctl)
			{
				ControlLoader::LoadPHPXControlAttributes($attrs, $ctl);
			}
		}
		/**
		 * Creates a WebControl from markup in a MarkupElement.
		 * @param MarkupElement $elem The MarkupElement to parse.
		 * @param WebControl $parent The WebControl that owns this control.
		 */
		public static function LoadControl($elem, $parent)
		{
			if (get_class($elem) == "UniversalEditor\\ObjectModels\\Markup\\MarkupTagElement")
			{
				$i = stripos($elem->Name, ":");
				if ($i !== false)
				{
					$prefix = substr($elem->Name, 0, $i);
					$name = substr($elem->Name, $i + 1);
						
					if (isset(ControlLoader::$Namespaces[$prefix]) && ControlLoader::$Namespaces[$prefix] != "")
					{
						$realname = ControlLoader::$Namespaces[$prefix] . "\\" . $name;
					}
					else
					{
						$realname = $name;
					}
					
					if (class_exists($realname))
					{
						$obj = new $realname();
					}
					else
					{
						$basectl = System::$Parser->GetControlByVirtualTagPath($realname);
						if ($basectl !== null)
						{
							$ctl = clone $basectl;
							$obj = $ctl;
						}
						else
						{
							ControlLoader::$Messages[] = new WebPageMessage("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")", WebPageMessageSeverity::Error);
							return;
						}
					}

					if ($obj->VirtualTagPath == null)
					{
						ControlLoader::LoadAttributes($elem, $obj);
					}
					else
					{
						$attrs = ControlLoader::GetPHPXAttributes($elem);
					}
					
					if (is_subclass_of($obj, "Phast\\WebControl") && $obj->ParseChildElements)
					{
						ControlLoader::ParseChildren($elem, $obj);
					}
					else
					{
						if (is_array($elem->Elements))
						{
							foreach ($elem->Elements as $elem1)
							{
								ControlLoader::LoadControl($elem1, $obj);
							}
						}
					}
					
					if ($obj->VirtualTagPath != null)
					{
						ControlLoader::LoadPHPXControlAttributes($attrs, $obj);
					}
						
					$obj->ParentObject = $parent;
					$parent->Controls[] = $obj;
				}
				else
				{
					$ctl = new HTMLControl();
					$ctl->TagName = $elem->Name;
					if (is_array($elem->Attributes))
					{
						foreach ($elem->Attributes as $attr)
						{
							$ctl->Attributes[] = new WebControlAttribute($attr->Name, $attr->Value);
						}
					}
					if (is_array($elem->Elements) && count($elem->Elements) > 0)
					{
						foreach ($elem->Elements as $elem1)
						{
							ControlLoader::LoadControl($elem1, $ctl);
						}
					}
					$ctl->ParentObject = $parent;
					$parent->Controls[] = $ctl;
				}
			}
			else if (get_class($elem) == "UniversalEditor\\ObjectModels\\Markup\\MarkupLiteralElement")
			{
				$parent->Controls[] = new Literal($elem->Value);
			}
		}
	}
	ControlLoader::$Messages = array();
	
?>