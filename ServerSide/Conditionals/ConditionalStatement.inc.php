<?php
	namespace Phast\Conditionals;
	
	class ConditionalStatement
	{
		public $Name;
		public $Comparison;
		public $Value;
		
		public function __construct($name, $comparison, $value)
		{
			$this->Name = $name;
			$this->Comparison = $comparison;
			$this->Value = $value;
		}
	}
?>