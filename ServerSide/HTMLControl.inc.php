<?php
	namespace Phast;
	
	class HTMLControl extends WebControl
	{
		public $InnerHTML;
		
		public function __construct($tagName = null)
		{
			parent::__construct();
			$this->TagName = $tagName;
		}
		
		protected function RenderContent()
		{
			echo($this->InnerHTML);
		}
	}
?>