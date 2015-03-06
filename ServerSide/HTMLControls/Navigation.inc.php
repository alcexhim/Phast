<?php 
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Navigation extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "nav";
		}
	}
?>