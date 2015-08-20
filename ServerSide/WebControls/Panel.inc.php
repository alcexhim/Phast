<?php
	namespace Phast\WebControls;
	
	use System;
	
	use Phast\HorizontalAlignment;
	use Phast\WebControl;
	use Phast\WebStyleSheetRule;
		
	class Panel extends WebControl
	{
		public $FooterContent;
		public $Title;
		
		public $HeaderControls;
		public $ContentControls;
		public $FooterControls;
		
		public function GetAllControls()
		{
			$ary = array();
			foreach ($this->HeaderControls as $ctl)
			{
				$ary[] = $ctl;
			}
			foreach ($this->ContentControls as $ctl)
			{
				$ary[] = $ctl;
			}
			foreach ($this->FooterControls as $ctl)
			{
				$ary[] = $ctl;
			}
			return $ary;
		}
		
		public function __construct($id = null, $title = "")
		{
			parent::__construct($id);
			$this->Title = $title;
			$this->ParseChildElements = true;
			
			$this->HeaderControls = array();
			$this->ContentControls = array();
			$this->FooterControls = array();
			
			$this->TagName = "div";
			$this->ClassList[] = "Panel";
		}
		
		protected function RenderBeginTag()
		{
			switch ($this->HorizontalAlignment)
			{
				case "Center":
				case HorizontalAlignment::Center:
				{
					$this->StyleRules[] = new WebStyleSheetRule("margin-left", "auto");
					$this->StyleRules[] = new WebStyleSheetRule("margin-right", "auto");
					break;
				}
				case "Right":
				case HorizontalAlignment::Right:
				{
					$this->StyleRules[] = new WebStyleSheetRule("margin-left", "auto");
					break;
				}
			}
			
			parent::RenderBeginTag();
		}
		
		protected function BeforeContent()
		{
			if ($this->Title != "")
			{
				echo("<div class=\"Header\">" . $this->Title . "</div>");
			}
			echo("<div class=\"Content\">");
		}
		
		public function BeginFooter()
		{
			echo("<div class=\"Footer\">");
		}
		public function EndFooter()
		{
			echo("</div>");
		}
		
		protected function RenderContent()
		{
			if (count($this->ContentControls) > 0)
			{
				foreach ($this->ContentControls as $ctl)
				{
					$ctl->Render();
				}
			}
			else
			{
				parent::RenderContent();
			}
		}
		
		protected function AfterContent()
		{
			echo("</div>");
			
			if (is_callable($this->FooterContent))
			{
				$this->BeginFooter();
				call_user_func($this->FooterContent);
				$this->EndFooter();
			}
			else if (count($this->FooterControls) > 0)
			{
				$this->BeginFooter();
				foreach ($this->FooterControls as $ctl)
				{
					$ctl->Render();
				}
				$this->EndFooter();
			}
		}
	}
?>