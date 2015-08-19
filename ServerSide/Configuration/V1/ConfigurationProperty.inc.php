<?php
	namespace Phast\Configuration\V1;
	
	class ConfigurationProperty extends ConfigurationItem
	{
		public $ID;
		public $Value;
		
		public function __construct($id, $value = null)
		{
			$this->ID = $id;
			$this->Value = $value;
		}
	}
?>