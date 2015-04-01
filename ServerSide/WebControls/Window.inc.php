<?php
	namespace Phast\WebControls;
	
	use System;
	
	use Phast\HTMLControl;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebScript;
	use Phast\WebStyleSheetRule;
	
	use Phast\HorizontalAlignment;
	use Phast\VerticalAlignment;
	
	class Window extends WebControl
	{
		public $Title;
		
		private $HasButtons;
		
		public $HeaderControls;
		public $ContentControls;
		public $FooterControls;
		
		public function __construct()
		{
			parent::__construct();
			$this->HasButtons = false;
			$this->TagName = "div";
			$this->ClassList[] = "Window";
			$this->ParseChildElements = true;
		}
		
		protected function OnInitialize()
		{
			$parent = $this->FindParentPage();
			if ($parent != null) $parent->Scripts[] = new WebScript("$(PhastStaticPath)/Scripts/Controls/Window.js");
		}
		
		protected function RenderBeginTag()
		{
			switch($this->HorizontalAlignment)
			{
				case HorizontalAlignment::Left:
				{
					$this->Attributes[] = new WebControlAttribute("data-horizontal-alignment", "left");
					break;
				}
				case HorizontalAlignment::Center:
				{
					$this->Attributes[] = new WebControlAttribute("data-horizontal-alignment", "center");
					break;
				}
				case HorizontalAlignment::Right:
				{
					$this->Attributes[] = new WebControlAttribute("data-horizontal-alignment", "right");
					break;
				}
			}
			switch($this->VerticalAlignment)
			{
				case VerticalAlignment::Top:
				{
					$this->Attributes[] = new WebControlAttribute("data-vertical-alignment", "top");
					break;
				}
				case VerticalAlignment::Middle:
				{
					$this->Attributes[] = new WebControlAttribute("data-vertical-alignment", "middle");
					break;
				}
				case VerticalAlignment::Bottom:
				{
					$this->Attributes[] = new WebControlAttribute("data-vertical-alignment", "bottom");
					break;
				}
			}
			
			$divHeader = new HTMLControl("div");
			$divHeader->ClassList[] = "Header";
			$spanTitle = new HTMLControl("span");
			$spanTitle->ClassList[] = "Title";
			$spanTitle->InnerHTML = $this->Title;
			$divHeader->Controls[] = $spanTitle;
			
			$divContent = new HTMLControl("div");
			$divContent->ClassList[] = "Content";
			
			$divInnerContent = new HTMLControl("div");
			$divInnerContent->ClassList[] = "Content";
			foreach ($this->ContentControls as $ctl)
			{
				$divInnerContent->Controls[] = $ctl;
			}
			$divContent->Controls[] = $divInnerContent;

			$divLoading = new HTMLControl("div");
			$divLoading->ClassList[] = "Loading";
			
			$divLoadingStatus = new HTMLControl("div");
			$divLoadingStatus->ClassList[] = "LoadingStatus";
			$divThrobber = new HTMLControl("div");
			$divThrobber->ClassList[] = "Throbber";
			$divLoadingStatus->Controls[] = $divThrobber;
			
			$pLoadingStatus = new HTMLControl("p");
			$pLoadingStatus->ClassList[] = "LoadingStatusText";
			$divLoadingStatus->Controls[] = $pLoadingStatus;
			
			$divLoading->Controls[] = $divLoadingStatus;
			$divContent->Controls[] = $divLoading;
			
			$divFooter = new HTMLControl("div");
			$divFooter->ClassList[] = "Footer";
			foreach ($this->FooterControls as $ctl)
			{
				$divFooter->Controls[] = $ctl;
			}

			if ($this->Width != null)
			{
				if (is_numeric($this->Width))
				{
					$this->StyleRules[] = new WebStyleSheetRule("width", $this->Width . "px");
				}
				else
				{
					$this->StyleRules[] = new WebStyleSheetRule("width", $this->Width);
				}
			}
			
			$this->Controls = array($divHeader, $divContent, $divFooter);
			parent::RenderBeginTag();
		}
		
	}
?>