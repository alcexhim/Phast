<?php
	namespace Phast;
	
	abstract class Validator
	{
		public $Message;
		
		protected abstract function ValidateInternal($value);

		public function Validate($value)
		{
			return $this->ValidateInternal($value);
		}
	}
?>