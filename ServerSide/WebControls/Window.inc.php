<?php
	namespace Phast\WebControls;
	use System;
	
	use Phast\WebControl;
	use Phast\WebControlAttribute;
	use Phast\WebScript;
	
	use Phast\HorizontalAlignment;
	use Phast\VerticalAlignment;
	
	class Window extends WebControl
	{
		public $Title;
		
		private $HasButtons;
		
		public function __construct()
		{
			parent::__construct();
			$this->HasButtons = false;
			$this->TagName = "div";
			$this->ClassList[] = "Window";
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
			parent::RenderBeginTag();
		}
		
		protected function BeforeContent()
		{
			echo("<div class=\"Header\"><span class=\"Title\">" . $this->Title . "</span></div>");
			echo("<div class=\"Content\"");
			if ($this->Width != null)
			{
				if (is_numeric($this->Width))
				{
					echo(" style=\"width: " . $this->Width . "px;\"");
				}
				else
				{
					echo(" style=\"width: " . $this->Width . ";\"");
				}
			}
			echo(">");
		}
		
		public function BeginButtons()
		{
			echo("</div>");
			echo("<div class=\"Buttons\">");
		}
		public function EndButtons()
		{
			echo("</div>");
			$this->HasButtons = true;
		}
		
		protected function AfterContent()
		{
			if (!$this->HasButtons)
			{
				echo("</div>");
			}
		}
	}
?>