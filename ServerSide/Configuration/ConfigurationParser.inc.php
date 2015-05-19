<?php
	namespace Phast\Configuration;
	
	use UniversalEditor\ObjectModels\Markup\XMLParser;
	use UniversalEditor\ObjectModels\Markup\MarkupTagElement;
			
	class ConfigurationParser
	{
		/**
		 * The Flavor which stores configuration data common to all flavors.
		 * @var Flavor
		 */
		public $CommonFlavor;
		/**
		 * The flavors available in this configuration.
		 * @var Flavor[]
		 */
		public $Flavors;
		
		public function __construct()
		{
			$this->CommonFlavor = new Flavor();
			$this->Flavors = array();
		}
		
		public function RetrieveGroup($path, $pathSeparator = ".")
		{
			
		}
		public function RetrieveGroupExact($path, $pathSeparator = ".")
		{
			
		}
		
		/**
		 * Retrieves the property defined by the specified path for the currently
		 * active flavor. All path parts before the last path part are considered to be groups.
		 * @param string $name The name of the property to search for.
		 * @param mixed $defaultValue The value to return if the property cannot be found. 
		 */
		public function RetrieveProperty($name, $defaultValue = null)
		{
			$flavor = $this->GetCurrentFlavor();
			if ($flavor == null) return new Property($name, $defaultValue);
			
			$property = $flavor->RetrieveProperty($name, $defaultValue);
			if ($property == null)
			{
				// property was not found in the active flavor; try the common
				// flavor
				$flavor = $this->CommonFlavor;
				$property = $flavor->RetrieveProperty($name, $defaultValue);
			}
			return $property;
		}
		/**
		 * Retrieves the currently-active configuration flavor.
		 * @return Flavor|NULL The currently-active configuration flavor, or NULL if no configuration flavor is currently active.
		 */
		public function GetCurrentFlavor()
		{
			foreach ($this->Flavors as $flavor)
			{
				if ($flavor->HostName != null && $flavor->HostName == $_SERVER["SERVER_NAME"])
				{
					return $flavor;
				}
			}
			return null;
		}
		
		/**
		 * Loads the group from the given MarkupTagElement into the specified parent.
		 * @param MarkupTagElement $tag
		 * @param Group|ConfigurationParser $parent
		 * @return boolean True if the load was successful; false if an error occurred.
		 */
		private function LoadGroup($tag, $parent)
		{
			if (!$tag->HasAttribute("ID")) return false;
			
			$group = new Group();
			$group->Name = $tag->GetAttribute("ID")->Value;

			$elems = $tag->GetElements();
			foreach ($elems as $elem)
			{
				$this->LoadTag($elem, $group);
			}
			
			$parent->Groups[] = $group;
			return true;
		}
		private function LoadProperty($tag, $parent)
		{
			if (!$tag->HasAttribute("ID")) return false;
			
			$id = $tag->GetAttribute("ID")->Value;
			if ($tag->HasAttribute("Value"))
			{
				$parent->UpdatePropertyValue($id, $tag->GetAttribute("Value")->Value);
				return true;
			}
			else if (count($tag->Elements) == 1)
			{
				$parent->UpdatePropertyValue($id, $tag->Elements[0]->GetInnerMarkup());
				return true;
			}
			else
			{
				$parent->UpdatePropertyValue($id, $tag->GetInnerMarkup());
				return true;
			}
			return false;
		}
		/**
		 * 
		 * @param MarkupTagElement $tag
		 * @param ConfigurationParser|Group $parent
		 */
		private function LoadTag($tag, $parent)
		{
			if ($tag->Name == "Group")
			{
				$this->LoadGroup($tag, $parent);
			}
			else if ($tag->Name == "Property")
			{
				$this->LoadProperty($tag, $parent);
			}
		}
		
		public function LoadFile($filename)
		{
			$parser = new XMLParser();
			
			$markup = $parser->LoadFile($filename);
			
			$tagWebsite = $markup->GetElement("Website");
			$tagCommon = $tagWebsite->GetElement("Common");
			if ($tagCommon != null)
			{
				$tagConfiguration = $tagCommon->GetElement("Configuration");
				if ($tagConfiguration != null)
				{
					$tags = $tagConfiguration->GetElements();
					foreach ($tags as $tag)
					{
						$this->LoadTag($tag, $this->CommonFlavor);
					}
				}
			}
			
			$tagFlavors = $tagWebsite->GetElement("Flavors");
			if ($tagFlavors != null)
			{
				foreach ($tagFlavors->Elements as $elem)
				{
					$flavor = new Flavor();
					$flavor->Name = $elem->GetAttribute("ID")->Value;
					
					if ($elem->HasAttribute("HostName"))
					{
						$flavor->HostName = $elem->GetAttribute("HostName")->Value;
					}
					
					$tagConfiguration = $elem->GetElement("Configuration");
					if ($tagConfiguration != null)
					{
						$tags = $tagConfiguration->GetElements();
						foreach ($tags as $tag)
						{
							$this->LoadTag($tag, $flavor);
						}
					}
					$this->Flavors[] = $flavor;
				}
			}
			
			$this->GetCurrentFlavor();
		}
	}
?>