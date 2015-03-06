<?php
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	
	use Phast\WebControlAttribute;
	use Phast\System;
	
	class Literal extends HTMLControl
	{
		public $Value;
		
		public function __construct($value = null)
		{
			parent::__construct();
			
			if ($value == null) $value = "";
			$this->Value = $value;
		}
		
		protected function RenderContent()
		{
			echo(System::ExpandRelativePath($this->Value));
		}
	}
?>