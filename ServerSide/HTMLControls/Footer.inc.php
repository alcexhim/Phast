<?php 
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Footer extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "footer";
		}
	}
?>