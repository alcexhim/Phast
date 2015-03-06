<?php 
	namespace Phast\HTMLControls;
	
	use Phast\HTMLControl;
	
	class Heading extends HTMLControl
	{
		public $Level;
		
		public function __construct()
		{
			parent::__construct();
			$this->Level = 1;
		}
		
		public function RenderBeginTag()
		{
			$this->TagName = "h" . $this->Level;
			parent::RenderBeginTag();
		}
	}
?>