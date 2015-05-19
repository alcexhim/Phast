<?php
	namespace Phast\Configuration;
	
	class Group
	{
		public $Name;

		public $Groups;
		public $Properties;

		public function __construct()
		{
			$this->Groups = array();
			$this->Properties = array();
		}
		
		public function RetrieveGroup($name)
		{
			foreach ($this->Groups as $group)
			{
				if ($group->Name == $name) return $group;
			}
				
			$group = new Group();
			$group->Name = $name;
			$this->Groups[] = $name;
			return $group;
		}
		public function RetrieveProperty($name, $defaultValue = null)
		{
			foreach ($this->Properties as $property)
			{
				if ($property->Name == $name) return $property;
			}
				
			$property = new Property();
			$property->Name = $name;
			$property->Value = $defaultValue;
			$this->Properties[] = $property;
			return $property;
		}
		
		public function RetrievePropertyValue($name, $defaultValue = null)
		{
			$property = $this->RetrieveProperty($name, $defaultValue);
			return $property->Value;
		}
		public function UpdatePropertyValue($name, $value)
		{
			$property = $this->RetrieveProperty($name, $value);
			$property->Value = $value;
		}
	}
?>