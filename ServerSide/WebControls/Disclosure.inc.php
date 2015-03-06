<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebScript;
	
	class Disclosure extends WebControl
	{
		public $Expanded;
		public $Title;
		
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "div";
			$this->ClassList[] = "Disclosure";
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(PhastStaticPath)/Scripts/Controls/Disclosure.js");
		}
		
		protected function RenderBeginTag()
		{
			if ($this->Expanded)
			{
				$this->ClassList[] = "Expanded";
				$this->Attributes[] = new WebControlAttribute("data-expanded", "true");
			}
			else
			{
				$this->Attributes[] = new WebControlAttribute("data-expanded", "false");
			}
			parent::RenderBeginTag();
		}
		
		protected function BeforeContent()
		{
			echo("<div class=\"Title\"><a href=\"#\"><span class=\"DisclosureButton\"></span> <span class=\"Title\">" . $this->Title . "</span></a></div>");
			echo("<div class=\"Content\">");
		}
		protected function AfterContent()
		{
			echo("</div>");
		}
	}
?>