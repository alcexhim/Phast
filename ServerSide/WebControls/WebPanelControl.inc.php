<?php
	namespace Phast\WebControls;
	use System;
	use Phast\WebControl;
	
	class WebPanelControl extends WebControl
	{
		public $HorizontalAlignment;
		public $Width;
		public $Title;
		
		public function __construct($title = "")
		{
			$this->Title = $title;
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
				case "center":
				{
					echo("margin-left: auto; ");
					echo("margin-right: auto; ");
					break;
				}
			}
			echo("\">");
			echo("<div class=\"PanelTitle\">" . $this->Title . "</div>");
			echo("<div class=\"PanelContent\">");
		}
		protected function AfterContent()
		{
			echo("</div>");
			echo("</div>");
		}
	}
?>