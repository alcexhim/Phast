<?php
	namespace Phast\WebControls;
	
	use Phast\System;
	use Phast\WebControl;
	
	use Phast\HTMLControl;
	
	class CommandBarContainer extends WebControl
	{
		public $Items;
		
		public function __construct()
		{
			$this->TagName = "div";
			$this->ClassList[] = "CommandBar";
		}
		
		protected function RenderContent()
		{
			foreach ($this->Items as $item)
			{
				$item->Render();
			}
		}
	}
	
	class CommandBar
	{
		public $ID;
		public $Title;
	}
	
	class CommandBarItem
	{
		public $ID;
		
		public function Render()
		{
			$this->RenderContent();
		}
		protected function RenderContent()
		{
		}
	}
	class CommandBarItemButton extends CommandBarItem
	{
		public $Title;
		public $ImageURL;
		public $TargetURL;
		public $TargetScript;
		
		public $Items;
		
		public function __construct()
		{
			$this->Items = array();
		}
		
		protected function RenderContent()
		{
			echo("<div class=\"CommandBarItem\">");
			echo("<span class=\"Text\">" . $this->Title . "</span>");
			echo("<div class=\"Menu\">");
			foreach ($this->Items as $item)
			{
				$item->Render();
			}
			echo("</div>");
			echo("</div>");
		}
	}
	
	class CommandBarItemSeparator extends CommandBarItem
	{
		protected function RenderContent()
		{
			echo("<div class=\"CommandBarSeparator\"></div>");
		}
	}
?>