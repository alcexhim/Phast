<?php
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Layer extends HTMLControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "div";
		}
	}
?>