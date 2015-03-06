<?php
	namespace Phast;
	
	class WebPageVariable
	{
		public $Name;
		public $Value;
		public $IsSet;
		
		public function __construct($name, $value = null, $isSet = false)
		{
			$this->Name = $name;
			$this->Value = $value;
			$this->IsSet = $isSet;
		}
	}
?>