<?php
	namespace Phast\Configuration;
	
	class Property
	{
		/**
		 * The name of this Property.
		 * @var string
		 */
		public $ID;
		/**
		 * The value of this Property.
		 * @var string
		 */
		public $Value;
		
		public function __construct($id = null, $value = null)
		{
			$this->ID = $id;
			$this->Value = $value;
		}
	}
?>