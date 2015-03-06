<?php 
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Header extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "header";
		}
	}
?>