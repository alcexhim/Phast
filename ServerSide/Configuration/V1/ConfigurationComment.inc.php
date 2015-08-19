<?php
	namespace Phast\Configuration\V1;
	
	class ConfigurationComment extends ConfigurationItem
	{
		public $Value;
		
		public function __construct($value)
		{
			$this->Value = $value;
		}
	}
?>