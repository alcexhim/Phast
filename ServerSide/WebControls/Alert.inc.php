<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	use Phast\Enumeration;
	
	class Alert extends WebControl
	{
		/**
		 * The title of the Alert.
		 * @var string
		 */
		public $Title;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->ClassList[] = "pwt-Alert";
			$this->TagName = "div";
		}
		
		protected function RenderBeginTag()
		{
			$ctls = $this->Controls;
			$this->Controls = array();
			
			$divTitle = new HTMLControl("div");
			$divTitle->ClassList[] = "Title";
			$divTitle->Content = $this->Title;
			$this->Controls[] = $divTitle;
			
			$divContent = new HTMLControl("div");
			$divContent->ClassList[] = "Content";
			foreach ($ctls as $ctl)
			{
				$divContent->Controls[] = $ctl;
			}
			$this->Controls[] = $divContent;
			
			parent::RenderBeginTag();
		}
	}
?>