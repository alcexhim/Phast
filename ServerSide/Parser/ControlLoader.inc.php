<?php
	namespace Phast\Parser;

	use Phast\HTMLControl;
	use Phast\HTMLControls\Literal;
	
	use Phast\WebControlAttribute;
	use Phast\WebPageMessage;
	use Phast\System;
		
	class ControlLoader
	{
		public static $Messages;
		public static $Namespaces;
	
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
							
						$i = stripos($elem->Name, ":");
						$prefix = substr($elem2->Name, 0, $i);
						$name = substr($elem2->Name, $i + 1);
	
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
							$obj1 = new $realname();
						}
						else
						{
							ControlLoader::$Messages[] = new WebPageMessage("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")", WebPageMessageSeverity::Error);
							System::WriteErrorLog("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")");
							continue;
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
						ControlLoader::$Messages[] = new WebPageMessage("Unknown class " . $realname . " (" . $prefix . ":" . $name . ")", WebPageMessageSeverity::Error);
						return;
					}
					ControlLoader::LoadAttributes($elem, $obj);
						
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