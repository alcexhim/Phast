<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	
	class Sidebar extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "nav";
			$this->ClassList[] = "Sidebar";
		}
	}
?>