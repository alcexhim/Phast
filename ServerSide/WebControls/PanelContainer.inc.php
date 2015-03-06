<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	
	class PanelContainer extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->TagName = "div";
			$this->ClassList[] = "PanelContainer";
		}
	}
?>