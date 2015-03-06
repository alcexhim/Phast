<?php 
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Paragraph extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "p";
		}
	}
?>