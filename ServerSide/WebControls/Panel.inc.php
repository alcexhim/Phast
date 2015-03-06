<?php
	namespace Phast\WebControls;
	
	use System;
	
	use Phast\HorizontalAlignment;
	use Phast\WebControl;
		
	class Panel extends WebControl
	{
		public $FooterContent;
		public $Width;
		public $Title;
		
		public $HeaderControls;
		public $ContentControls;
		public $FooterControls;
		
		public function __construct($id, $title = "")
		{
			parent::__construct($id);
			$this->Title = $title;
			
			$this->HeaderControls = array();
			$this->ContentControls = array();
			$this->FooterControls = array();
		}
		
		protected function BeforeContent()
		{
			echo("<div class=\"Panel\" style=\"");
			if ($this->Width != null)
			{
				echo("width: " . $this->Width . "; ");
			}
			switch ($this->HorizontalAlignment)
			{
				case HorizontalAlignment::Center:
				{
					echo("margin-left: auto; ");
					echo("margin-right: auto; ");
					break;
				}
			}
			echo("\">");
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
			
			echo("</div>");
		}
	}
?>